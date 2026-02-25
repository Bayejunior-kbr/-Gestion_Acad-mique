<?php
require_once('fpdf/fpdf.php');
require_once('traitement/db.php');
require_once('traitement/requette.php');

if (!isset($_GET['id'])) die("ID manquant.");

$id = (int)$_GET['id'];
$etudiant = getBulletinData($id);

if (!$etudiant) die("Étudiant introuvable.");

// --- LOGIQUE DE RÉCUPÉRATION (Optimisée) ---
global $pdo;
$stmt = $pdo->prepare("
    SELECT 
        m.nom_module,
        cm.coefficient,
        ev.type_evaluation,
        ev.note
    FROM classe_module cm
    JOIN module m ON m.id_module = cm.id_module
    LEFT JOIN evaluation ev 
        ON ev.id_module = cm.id_module 
        AND ev.id_etudiant = :id
        AND ev.type_evaluation <> 'TP'
    WHERE cm.id_classe = :classe
    ORDER BY m.nom_module
");
$stmt->execute([':id' => $id, ':classe' => $etudiant['id_classe']]);
$notesRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

$modules = [];
foreach($notesRaw as $row){
    $module = $row['nom_module'];
    if(!isset($modules[$module])){
        $modules[$module] = [
            'coefficient' => $row['coefficient'],
            'devoir' => null, // null pour différencier 0 de "pas de note"
            'examen' => null
        ];
    }
    if(isset($row['note'])){
        $type = strtolower($row['type_evaluation'] ?? '');
        if($type === 'devoir') $modules[$module]['devoir'] = $row['note'];
        elseif($type === 'examen') $modules[$module]['examen'] = $row['note'];
    }
}

// --- CLASSE PDF PERSONNALISÉE ---
class BulletinPDF extends FPDF {
    function Header() {
        // Logo (à décommenter si vous avez un fichier logo.png)
        // $this->Image('assets/img/logo.png', 10, 6, 30);
        
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(33, 37, 41);
        $this->Cell(0, 10, utf8_decode('ÉCOLE SUPÉRIEURE DE TECHNOLOGIE'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 5, utf8_decode('Année Académique 2025-2026'), 0, 1, 'C');
        $this->Ln(10);
        
        // Ligne de séparation
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

// --- GÉNÉRATION DU DOCUMENT ---
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
$pdf->SetFillColor(44, 62, 80); // Bleu foncé pro
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
$totalPoints = 0;
$totalCoeff = 0;

foreach($modules as $name => $data){
    $dev = $data['devoir'] ?? 0;
    $exa = $data['examen'] ?? 0;
    $coef = $data['coefficient'];
    
    // Formule : (Devoir * 0.4) + (Examen * 0.6)
    $noteMoyenneModule = ($dev * 0.4) + ($exa * 0.6);
    $notePonderee = $noteMoyenneModule * $coef;
    
    $totalPoints += $notePonderee;
    $totalCoeff += $coef;

    $pdf->Cell(70, 8, utf8_decode($name), 1, 0, 'L');
    $pdf->Cell(25, 8, $coef, 1, 0, 'C');
    $pdf->Cell(30, 8, ($data['devoir'] !== null ? number_format($dev, 2) : '-'), 1, 0, 'C');
    $pdf->Cell(30, 8, ($data['examen'] !== null ? number_format($exa, 2) : '-'), 1, 0, 'C');
    
    // Coloration si note < 10
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

// Appréciation et décision
$pdf->Ln(10);
$appreciation = "";
if($moyenneGenerale >= 16) $appreciation = "Excellent";
elseif($moyenneGenerale >= 14) $appreciation = "Tres Bien";
elseif($moyenneGenerale >= 12) $appreciation = "Bien";
elseif($moyenneGenerale >= 10) $appreciation = "Passable";
else $appreciation = "Insuffisant";

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 10, utf8_decode('Appréciation : '), 0, 0);
$pdf->SetFont('Arial', 'I', 11);
$pdf->Cell(0, 10, utf8_decode($appreciation), 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 10, utf8_decode('Décision : '), 0, 0);
$pdf->Cell(0, 10, ($moyenneGenerale >= 10 ? 'ADMIS(E)' : 'AJOURENE(E)'), 0, 1);

// Signature
$pdf->Ln(10);
$pdf->Cell(130);
$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(0, 10, 'La Direction des Etudes', 0, 1, 'C');

$pdf->Output('I', 'Bulletin_' . $etudiant['nom'] . '.pdf');
exit;