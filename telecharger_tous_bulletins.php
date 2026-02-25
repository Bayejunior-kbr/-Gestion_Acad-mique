<?php
require_once('fpdf/fpdf.php');
require_once('traitement/db.php');
require_once('traitement/requette.php');

// Récupération des filtres
$classe = $_GET['classe'] ?? '';
$niveau = $_GET['niveau'] ?? '';
$nom    = $_GET['nom'] ?? '';

// Récupération de la liste des étudiants (Limite augmentée pour l'export)
$listeEtudiants = getMoyennesPaginated(1000, 0, $classe, $niveau, $nom);

if(empty($listeEtudiants)) {
    die("Aucun étudiant trouvé pour la génération.");
}

// --- CLASSE PDF IDENTIQUE (Copie conforme de votre modèle) ---
class BulletinPDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(33, 37, 41);
        $this->Cell(0, 10, utf8_decode('ÉCOLE SUPÉRIEURE DE TECHNOLOGIE'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 5, utf8_decode('Année Académique 2025-2026'), 0, 1, 'C');
        $this->Ln(10);
        
        $this->SetDrawColor(44, 62, 80);
        $this->SetLineWidth(0.8);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 10, utf8_decode('Généré le ' . date('d/m/Y H:i')), 0, 0, 'R');
    }
}

// Création d'un dossier temporaire pour stocker les PDF avant le ZIP
$tmpDir = sys_get_temp_dir() . '/bulletins_' . time();
if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);

global $pdo;

foreach($listeEtudiants as $e) {
    $id_etudiant = (int)$e['id_etudiant'];
    $etudiant = getBulletinData($id_etudiant);
    
    if (!$etudiant) continue;

    // --- RÉCUPÉRATION DES NOTES DÉTAILLÉES ---
    $stmt = $pdo->prepare("
        SELECT m.nom_module, cm.coefficient, ev.type_evaluation, ev.note
        FROM classe_module cm
        JOIN module m ON m.id_module = cm.id_module
        LEFT JOIN evaluation ev 
            ON ev.id_module = cm.id_module 
            AND ev.id_etudiant = :id
            AND ev.type_evaluation <> 'TP'
        WHERE cm.id_classe = :classe
        ORDER BY m.nom_module
    ");
    $stmt->execute([':id' => $id_etudiant, ':classe' => $etudiant['id_classe']]);
    $notesRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $modules = [];
    foreach($notesRaw as $row){
        $module = $row['nom_module'];
        if(!isset($modules[$module])){
            $modules[$module] = ['coefficient' => $row['coefficient'], 'devoir' => null, 'examen' => null];
        }
        if(isset($row['note'])){
            $type = strtolower($row['type_evaluation'] ?? '');
            if($type === 'devoir') $modules[$module]['devoir'] = $row['note'];
            elseif($type === 'examen') $modules[$module]['examen'] = $row['note'];
        }
    }

    // --- GÉNÉRATION DU PDF INDIVIDUEL ---
    $pdf = new BulletinPDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // Section Infos Étudiant
    $pdf->SetFillColor(245, 246, 250);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('BULLETIN DE NOTES'), 0, 1, 'C', true);
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 7, 'Etudiant : ', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 7, utf8_decode($etudiant['nom'] . ' ' . $etudiant['prenom']), 0, 1);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 7, 'Classe : ', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 7, utf8_decode($etudiant['nom_classe'] . ' (' . $etudiant['nom_niveau'] . ')'), 0, 1);
    $pdf->Ln(10);

    // Entête du tableau
    $pdf->SetFillColor(44, 62, 80); 
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(70, 10, 'MODULE', 1, 0, 'L', true);
    $pdf->Cell(25, 10, 'COEFF', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'DEVOIR (40%)', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'EXAMEN (60%)', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'MOYENNE /20', 1, 1, 'C', true);

    // Corps du tableau
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial', '', 10);
    $totalPoints = 0; $totalCoeff = 0;

    foreach($modules as $name => $data){
        $dev = $data['devoir'] ?? 0;
        $exa = $data['examen'] ?? 0;
        $coef = $data['coefficient'];
        $noteMoyenneModule = ($dev * 0.4) + ($exa * 0.6);
        $totalPoints += ($noteMoyenneModule * $coef);
        $totalCoeff += $coef;

        $pdf->Cell(70, 8, utf8_decode($name), 1, 0, 'L');
        $pdf->Cell(25, 8, $coef, 1, 0, 'C');
        $pdf->Cell(30, 8, ($data['devoir'] !== null ? number_format($dev, 2) : '-'), 1, 0, 'C');
        $pdf->Cell(30, 8, ($data['examen'] !== null ? number_format($exa, 2) : '-'), 1, 0, 'C');
        
        if($noteMoyenneModule < 10) $pdf->SetTextColor(200, 0, 0);
        $pdf->Cell(35, 8, number_format($noteMoyenneModule, 2), 1, 1, 'C');
        $pdf->SetTextColor(0);
    }

    // Ligne de calcul finale
    $moyenneGenerale = ($totalCoeff > 0) ? $totalPoints / $totalCoeff : 0;
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(155, 10, utf8_decode('MOYENNE GÉNÉRALE'), 1, 0, 'R', false);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(35, 10, number_format($moyenneGenerale, 2), 1, 1, 'C', true);

    // Appréciation
    $pdf->Ln(10);
    $appr = ($moyenneGenerale >= 16) ? "Excellent" : (($moyenneGenerale >= 14) ? "Tres Bien" : (($moyenneGenerale >= 12) ? "Bien" : (($moyenneGenerale >= 10) ? "Passable" : "Insuffisant")));
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 10, utf8_decode('Appréciation : '), 0, 0);
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(0, 10, utf8_decode($appr), 0, 1);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 10, utf8_decode('Décision : '), 0, 0);
    $pdf->Cell(0, 10, ($moyenneGenerale >= 10 ? 'ADMIS(E)' : 'AJOURENE(E)'), 0, 1);

    // Signature
    $pdf->Ln(10); $pdf->Cell(130);
    $pdf->SetFont('Arial', 'U', 10);
    $pdf->Cell(0, 10, 'La Direction des Etudes', 0, 1, 'C');

    // Sauvegarde du fichier avec un nom propre
    $fileName = 'Bulletin_' . preg_replace('/[^A-Za-z0-9]/', '_', $etudiant['nom'] . '_' . $etudiant['prenom']) . '.pdf';
    $pdf->Output('F', $tmpDir . '/' . $fileName);
}

// --- CRÉATION DU ZIP ---
$zipName = 'Bulletins_Exports_' . date('Y-m-d_H-i') . '.zip';
$zipPath = sys_get_temp_dir() . '/' . $zipName;
$zip = new ZipArchive();

if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
    $files = scandir($tmpDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $zip->addFile($tmpDir . '/' . $file, $file);
        }
    }
    $zip->close();
}

// Nettoyage des PDF individuels
foreach (scandir($tmpDir) as $file) {
    if ($file !== '.' && $file !== '..') unlink($tmpDir . '/' . $file);
}
rmdir($tmpDir);

// Téléchargement du ZIP
if (file_exists($zipPath)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipName . '"');
    header('Content-Length: ' . filesize($zipPath));
    readfile($zipPath);
    unlink($zipPath); // Supprimer le ZIP après envoi
}
exit;