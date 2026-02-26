<?php
include_once "db.php";

/*listes les niveaux */
function getNiveaux(){
    global $pdo;
    $sql = "  SELECT n.id_niveau, n.nom_niveau, COUNT(c.id_classe) AS nb_classes
                FROM niveau n
                LEFT JOIN classe c ON c.id_niveau = n.id_niveau
                GROUP BY n.id_niveau
                ORDER BY n.nom_niveau
    ";
    $stmt = $pdo->query($sql);
    $niveaux=$stmt->fetchAll();

    foreach ($niveaux as &$niveau) {
        $stmt2 = $pdo->prepare("SELECT id_classe, nom_classe FROM classe WHERE id_niveau = ?");
        $stmt2->execute([$niveau['id_niveau']]);
        $niveau['classes'] = $stmt2->fetchAll();
    }
    return $niveaux;
}

/*ajouter les niveaux*/
function addNiveau($nom_niveau){
    global $pdo;
    $check = $pdo->prepare("SELECT * FROM niveau WHERE nom_niveau = ?");
    $check->execute([$nom_niveau]);
    if ($check->rowCount() > 0) {
        return false;
    }
    $sql = "INSERT INTO niveau (nom_niveau) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nom_niveau]);
}
/*supprimer un niveau*/
function deleteNiveau($id){
    global $pdo;
    $sql="DELETE from niveau where id_niveau=$id";
    $stmt=$pdo->prepare($sql);
    return $stmt->execute();
}

/*================Classe=================== */

/*listes les classes */
function getClasse(){
    global $pdo;

    $sql = "  SELECT 
            c.id_classe,
            c.nom_classe,
            n.nom_niveau,
            COUNT(e.id_etudiant) nb_etudiants
        FROM 
            classe c
        LEFT JOIN 
            niveau n ON c.id_niveau = n.id_niveau
        LEFT JOIN 
            etudiant e ON c.id_classe = e.id_classe
        GROUP BY 
            c.id_classe, c.nom_classe, n.nom_niveau
        ORDER BY 
            c.nom_classe
    ";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/*ajouter une classes */
function addclasse($nom_classe,$id_niveau){
    global $pdo;
    $check=$pdo->prepare("SELECT * from classe where nom_classe=? and id_niveau=?");
    $check->execute([$nom_classe,$id_niveau]);
    if ($check->rowCount() > 0) {
        return false;
    }

    $sql = "INSERT INTO classe (nom_classe, id_niveau) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nom_classe, $id_niveau]);

}

/*supprimer un classe */
function deleteclasse($id){
    global $pdo;
    $sql="DELETE from classe where id_classe=$id";
    $stmt=$pdo->prepare($sql);
    return $stmt->execute();
}

/*recuperes et listes les infos des classes */
function getClasses($niveau_id = null) {
    global $pdo;
    $sql = "SELECT c.*, n.nom_niveau,
                   (SELECT COUNT(*) FROM etudiant e WHERE e.id_classe = c.id_classe) AS nb_etudiants
            FROM classe c
            JOIN niveau n ON n.id_niveau = c.id_niveau";
    if ($niveau_id) {
        $sql .= " WHERE c.id_niveau = :niveau_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['niveau_id' => $niveau_id]);
    } else {
        $stmt = $pdo->query($sql);
    }
    return $stmt->fetchAll();
}


/*================Etudiants=================== */
/*listes les Etudiant */
function getEtudiant(){
    global $pdo;
    
    $sql = " SELECT 
            E.id_etudiant,
            E.matricule,
            E.nom,
            E.prenom,
            c.nom_classe,
            n.nom_niveau
            FROM 
                etudiant E
            LEFT JOIN 
                classe c ON E.id_classe = c.id_classe
            LEFT JOIN 
                niveau n ON c.id_niveau = n.id_niveau
            ORDER BY 
                E.nom
        ";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}
/*ajouter un etudiant */
function addetudiant($matricule,$nom,$prenom,$id_classe){
    global $pdo;
    $check=$pdo->prepare("SELECT * from etudiant where matricule=? and id_classe=?");
    $check->execute([$matricule,$id_classe]);
    if ($check->rowCount() > 0) {
        return false;
    }
    $sql = "INSERT INTO etudiant (matricule,nom,prenom,id_classe) VALUES (?, ?,?,?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$matricule,$nom,$prenom, $id_classe]);

}
/*listes les etudiant en fonctions de sa classes */
function getEtudiantByClasse($id_classe){
    global $pdo;
    $sql = "SELECT matricule, nom, prenom FROM etudiant WHERE id_classe = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_classe]);
    return $stmt->fetchAll();
}

/* supprimer un etudiant */
function deleteEtudiant($id){
    global $pdo;
    $sql="DELETE from etudiant where id_etudiant=$id";
    $stmt=$pdo->prepare($sql);
    return $stmt->execute();
}
/* modifier un etudiant*/
function updateEtudiant($id,$matricule,$nom,$prenom,$id_classe){
    global $pdo;
    $sql="UPDATE etudiant set matricule=? ,nom=?,prenom=?,id_classe=? where id_etudiant=?";
    $stmt=$pdo->prepare($sql);
    return $stmt->execute([$matricule,$nom,$prenom,$id_classe,$id]);
}

function findetudiantbyid($id){
    global $pdo;
    $sql="SELECT * from etudiant where id_etudiant=?";
    $stmt=$pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}
/* pour modifer verifier l'id de l'etudiant*/
function getEvaluationsByEtudiant($id_etudiant) {
    global $pdo;
    $sql = "SELECT e.id_evaluation, e.type_evaluation, e.note, 
                   m.nom_module, m.code_module
            FROM evaluation e
            INNER JOIN module m ON e.id_module = m.id_module
            WHERE e.id_etudiant = ?
            ORDER BY m.nom_module, e.type_evaluation";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_etudiant]);
    return $stmt->fetchAll();
}
/* pour le filter et  compter pour pagination le nombre d'etudiant */
function countAllEtudiants($niveau = null, $classe = null){
    global $pdo;
    $sql = "SELECT COUNT(*) 
            FROM etudiant e
            JOIN classe c ON e.id_classe = c.id_classe
            WHERE 1=1";
    if($niveau){
        $sql .= " AND c.id_niveau = :niveau";
    }

    if($classe){
        $sql .= " AND e.id_classe = :classe";
    }

    $stmt = $pdo->prepare($sql);

    if($niveau){
        $stmt->bindValue(':niveau', $niveau);
    }

    if($classe){
        $stmt->bindValue(':classe', $classe);
    }

    $stmt->execute();
    return $stmt->fetchColumn();
}
/* fonction permettant de filtre et aussi la pagination si sa affiche 10 etudiant on passe a la suivante*/
function getEtudiantsPaginated($limit, $offset, $niveau = null, $classe = null){
    global $pdo;

    $sql = "SELECT e.*, c.nom_classe, n.nom_niveau
            FROM etudiant e
            JOIN classe c ON e.id_classe = c.id_classe
            JOIN niveau n ON c.id_niveau = n.id_niveau
            WHERE 1=1";

    if($niveau){
        $sql .= " AND c.id_niveau = :niveau";
    }

    if($classe){
        $sql .= " AND e.id_classe = :classe";
    }

    $sql .= " ORDER BY e.id_etudiant DESC
              LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);

    if($niveau){
        $stmt->bindValue(':niveau', $niveau);
    }
    
    if($classe){
        $stmt->bindValue(':classe', $classe);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
/* pour genere les matricules des eleves utiliser dans action.php*/
function genererMatricule($id_classe) {
    global $pdo;
    $annee = date("Y");
    $stmt = $pdo->prepare("SELECT matricule 
        FROM etudiant 
        WHERE matricule LIKE ?
        ORDER BY id_etudiant DESC 
        LIMIT 1
    ");
    $stmt->execute([$annee . "-%"]);
    $dernier = $stmt->fetchColumn();

    if($dernier) {
        $parts = explode('-', $dernier);
        $numero = (int) end($parts);
        $numero++;
    } else {
        $numero = 1;
    }
    $numero = str_pad($numero, 4, "0", STR_PAD_LEFT);
    return $annee . "-ETU-" . $numero;
}

/*====================Module======================= */

/* listes les modules pour l'affiches*/
function getModule(){
    global $pdo;
    $sql="SELECT * from module ORDER BY nom_module";
    $stmt=$pdo->query($sql);
    return $stmt->fetchAll();
}

/* ajouter module */
function addModule($code_module,$nom_module){
    global $pdo;
    $check=$pdo->prepare("SELECT * from module where code_module=? or nom_module=?");
    $check->execute([$code_module,$nom_module]);
    if ($check->rowCount() > 0) {
        return false;
    }
    $sql="INSERT into module (code_module,nom_module) values (?,?)";
    $stmt=$pdo->prepare($sql);
    return $stmt->execute([$code_module,$nom_module]);
}

/*supprimer module  */
function deleteModule($id){
    global $pdo;
    $sql="DELETE from module where id_module=$id";
    $stmt=$pdo->prepare($sql);
    return $stmt->execute();
}

/*modifier un module */
function updateModule($id,$nom_module,$code_module){
    global $pdo;    
    $sql="UPDATE module set nom_module=? ,code_module=? where id_module=?";
    $stmt=$pdo->prepare($sql);
    return $stmt->execute([$nom_module,$code_module,$id]);
}

/* pour verifier l'id a modifier utiliser dans index.php */
function findmodulebyid($id){
    global $pdo;
    $sql="SELECT * from module where id_module=?";
    $stmt=$pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/*function pour genere le code module */
function genererCodeModule(){
    global $pdo;
    $stmt = $pdo->query("SELECT code_module FROM module ORDER BY id_module DESC LIMIT 1");
    $dernier = $stmt->fetchColumn();
    if($dernier){
        $numero = (int) filter_var($dernier, FILTER_SANITIZE_NUMBER_INT);
        $numero++;
    } else {
        $numero = 1;
    }
    return "MOD-" . str_pad($numero, 3, "0", STR_PAD_LEFT);
}

/*==================classemodule============================ */
/*pour lites et affiches les module lier par les classes*/
function getClasse_module() {
    global $pdo;
    $sql = "SELECT 
                cm.id_classe, 
                cm.id_module, 
                cm.coefficient, 
                c.nom_classe, 
                m.nom_module,
                n.nom_niveau          
            FROM classe_module cm
            INNER JOIN classe c ON cm.id_classe = c.id_classe
            INNER JOIN niveau n ON c.id_niveau = n.id_niveau   
            INNER JOIN module m ON cm.id_module = m.id_module
            ORDER BY c.nom_classe, cm.coefficient";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/*ajouter classe module en verifier si c'est deja exister ou pas*/
function addclasse_module($id_classe,$id_module,$coefficient){
    global $pdo;
    $check=$pdo->prepare("SELECT * from classe_module where id_classe=? and id_module=?");
    $check->execute([$id_classe,$id_module]);
    if ($check->rowCount() > 0) {
        return false;
    }
    $sql="INSERT into classe_module (id_classe,id_module,coefficient) values (?,?,?)";
    $stmt=$pdo->prepare($sql);
    return $stmt->execute([$id_classe,$id_module,$coefficient]);
}

/*supermer un moule lier avec une classe*/
function deleteclasse_module($id_classe, $id_module){
    global $pdo;
    $sql = "DELETE FROM classe_module WHERE id_classe = ? AND id_module = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id_classe, $id_module]);
}

function getModuleByClasse($id_classe) {
    global $pdo;
    $sql = "SELECT m.nom_module, m.code_module, cm.coefficient
        FROM classe_module cm
        JOIN module m ON cm.id_module = m.id_module
        WHERE cm.id_classe = ?
        ORDER BY m.nom_module
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_classe]);
    return $stmt->fetchAll();
}

/* permet de listes les modules de chaque  classes demander*/
function getClasseModuleFiltered($classe_filter = '', $module_filter = '') {
    global $pdo;

    $sql = "SELECT 
                cm.id_classe, 
                cm.id_module, 
                cm.coefficient, 
                c.nom_classe, 
                m.nom_module,
                n.nom_niveau
            FROM classe_module cm
            INNER JOIN classe c ON cm.id_classe = c.id_classe
            INNER JOIN niveau n ON c.id_niveau = n.id_niveau
            INNER JOIN module m ON cm.id_module = m.id_module
            WHERE 1=1 ";

    $params = [];
    if($classe_filter) {
        $sql .= " AND cm.id_classe = :classe ";
        $params[':classe'] = $classe_filter;
    }
    if($module_filter) {
        $sql .= " AND cm.id_module = :module ";
        $params[':module'] = $module_filter;
    }
    $sql .= " ORDER BY c.nom_classe, cm.coefficient";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/*=======================evaluation======================== */

// Récupérer toutes les évaluations avec info étudiant et module
function getEvaluations() {
    global $pdo;
    $sql ="SELECT e.id_evaluation, e.note, e.type_evaluation,
                   et.nom AS nom_etudiant, et.prenom AS prenom_etudiant,
                   m.nom_module, m.code_module
            FROM evaluation e
            INNER JOIN etudiant et ON e.id_etudiant = et.id_etudiant
            INNER JOIN module m ON e.id_module = m.id_module
            ORDER BY et.nom, m.nom_module";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// Ajouter une évaluation
function addEvaluation($id_etudiant, $id_module, $type_evaluation, $note) {
    global $pdo;
    $check=$pdo->prepare("SELECT * from evaluation where id_etudiant=? and id_module=? and type_evaluation=? ");
    $check->execute([$id_etudiant, $id_module, $type_evaluation]);
    if ($check->rowCount() > 0) {
        return false;
    }

    $sql = "INSERT INTO evaluation (id_etudiant, id_module, type_evaluation, note)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id_etudiant, $id_module, $type_evaluation, $note]);
}

// Supprimer une évaluation
function deleteEvaluation($id_evaluation) {
    global $pdo;
    $sql = "DELETE FROM evaluation WHERE id_evaluation = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id_evaluation]);
}

// Modifier une évaluation
function updateEvaluation($id_evaluation, $id_etudiant, $id_module, $type_evaluation, $note) {
    global $pdo;
    $sql = "UPDATE evaluation 
            SET id_etudiant = ?, id_module = ?, type_evaluation = ?, note = ? 
            WHERE id_evaluation = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id_etudiant, $id_module, $type_evaluation, $note, $id_evaluation]);
}

// Récupérer une évaluation par son ID
function getEvaluationById($id_evaluation) {
    global $pdo;
    $sql = "SELECT * FROM evaluation WHERE id_evaluation = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_evaluation]);
    return $stmt->fetch();
}
//  pour les filter et pagination des evalution
function getEvaluationsPaginated($limit, $offset, $module_filter='', $type_filter='') {
    global $pdo;

    $sql = "SELECT e.id_evaluation, e.note, e.type_evaluation,
                   et.nom AS nom_etudiant, et.prenom AS prenom_etudiant,
                   c.nom_classe, n.nom_niveau, m.nom_module, m.id_module
            FROM evaluation e
            INNER JOIN etudiant et ON e.id_etudiant = et.id_etudiant
            INNER JOIN classe c ON et.id_classe = c.id_classe
            INNER JOIN niveau n ON c.id_niveau = n.id_niveau
            INNER JOIN module m ON e.id_module = m.id_module
            WHERE 1=1";

    $params = [];

    if($module_filter){
        $sql .= " AND m.id_module = ? ";
        $params[] = $module_filter;
    }

    if($type_filter){
        $sql .= " AND e.type_evaluation = ? ";
        $params[] = $type_filter;
    }

    $sql .= " ORDER BY et.nom, m.nom_module
              LIMIT ".(int)$limit." OFFSET ".(int)$offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

// Pour compter le total avec filtres
function countEvaluations($module_filter='', $type_filter='') {
    global $pdo;

    $sql = "SELECT COUNT(*) FROM evaluation e
            INNER JOIN module m ON e.id_module = m.id_module
            WHERE 1=1";
    $params = [];
    if($module_filter){
        $sql .= " AND m.id_module = ? ";
        $params[] = $module_filter;
    }
    if($type_filter){
        $sql .= " AND e.type_evaluation = ? ";
        $params[] = $type_filter;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

/* ================================calcule moyenne=========================== */
function getMoyennesPaginated($limit, $offset, $classe = '', $niveau = '', $nom = '')
{
    global $pdo;

    // Récupérer les étudiants
    $sql = " SELECT 
            e.id_etudiant,
            e.nom,
            e.prenom,
            n.nom_niveau,
            c.nom_classe,
            c.id_classe
        FROM etudiant e
        INNER JOIN classe c ON e.id_classe = c.id_classe
        INNER JOIN niveau n ON c.id_niveau = n.id_niveau
        WHERE 1=1
    ";
       
    $params = [];
    if(!empty($classe)) { $sql .= " AND c.id_classe = :classe"; $params[':classe']=$classe; }
    if(!empty($niveau)) { $sql .= " AND n.nom_niveau = :niveau"; $params[':niveau']=$niveau; }
    if(!empty($nom)) { $sql .= " AND e.nom LIKE :nom"; $params[':nom']="%$nom%"; }

    $sql .= " ORDER BY n.nom_niveau ASC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach($params as $k=>$v) $stmt->bindValue($k,$v);
    $stmt->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
    $stmt->bindValue(':offset',(int)$offset,PDO::PARAM_INT);
    $stmt->execute();
    $etudiants = $stmt->fetchAll();

    foreach($etudiants as &$e){
        // Récupérer les notes par module
        $modStmt = $pdo->prepare(" SELECT 
                m.id_module,
                m.nom_module,
                cm.coefficient,
                ev.type_evaluation,
                ev.note
            FROM classe_module cm
            JOIN module m ON m.id_module = cm.id_module
            LEFT JOIN evaluation ev 
                ON ev.id_module = cm.id_module AND ev.id_etudiant = :id AND ev.type_evaluation<>'TP'
            WHERE cm.id_classe = :classe
        ");
           
        $modStmt->execute([':id'=>$e['id_etudiant'], ':classe'=>$e['id_classe']]);
        $modules = $modStmt->fetchAll();

        $totalNotes = 0;
        $totalCoeff = 0;

        $modulesNotes = []; // pour stocker devoir et examen par module

        foreach($modules as $m){
            $modId = $m['id_module'];
            if(!isset($modulesNotes[$modId])){
                $modulesNotes[$modId] = ['devoir'=>0,'examen'=>0,'coef'=>$m['coefficient']];
            }
            if($m['type_evaluation']==='devoir') $modulesNotes[$modId]['devoir'] = $m['note'] ?? 0;
            if($m['type_evaluation']==='examen') $modulesNotes[$modId]['examen'] = $m['note'] ?? 0;
        }

        foreach($modulesNotes as $mod){
            $moyModule = $mod['devoir']*0.4 + $mod['examen']*0.6;
            $totalNotes += $moyModule * $mod['coef'];
            $totalCoeff += $mod['coef'];
        }
        $e['moyenne'] = $totalCoeff>0 ? round($totalNotes/$totalCoeff,2) : 0;
    }
    return $etudiants;
}
function getBulletinData($id)
{
    global $pdo;
    // Récupérer infos étudiant + moyenne calculée sur devoir/examen
    $sql = "SELECT 
            e.id_etudiant,
            e.nom,
            e.prenom,
            e.id_classe,
            c.nom_classe,
            n.nom_niveau
        FROM etudiant e
        JOIN classe c ON e.id_classe = c.id_classe
        JOIN niveau n ON c.id_niveau = n.id_niveau
        WHERE e.id_etudiant = :id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id'=>$id]);
    $etudiant = $stmt->fetch();

    if(!$etudiant) return false;

    // Calcul moyenne détaillée par module pour le bulletin
    $stmt2 = $pdo->prepare("
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
            AND ev.type_evaluation<>'TP'
        WHERE cm.id_classe = :classe
        ORDER BY m.nom_module
    ");
    $stmt2->execute([':id'=>$id, ':classe'=>$etudiant['id_classe']]);
    $etudiant['modules'] = $stmt2->fetchAll();

    return $etudiant;
}

function countFilteredMoyennes($classe='', $niveau='', $nom=''){
    global $pdo;

    $sql = "SELECT COUNT(*) FROM etudiant e
            INNER JOIN classe c ON e.id_classe = c.id_classe
            INNER JOIN niveau n ON c.id_niveau = n.id_niveau
            WHERE 1=1";
    $params = [];
    if(!empty($classe)) { $sql .= " AND c.id_classe = :classe"; $params[':classe']=$classe; }
    if(!empty($niveau)) { $sql .= " AND n.nom_niveau = :niveau"; $params[':niveau']=$niveau; }
    if(!empty($nom)) { $sql .= " AND e.nom LIKE :nom"; $params[':nom']="%$nom%"; }

    $stmt = $pdo->prepare($sql);
    foreach($params as $k=>$v) $stmt->bindValue($k,$v);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// fonction pour dasboarde
function countEtudiant() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM etudiant")->fetchColumn();
}

function countClasse() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM classe")->fetchColumn();
}

function countModule() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM module")->fetchColumn();
}

function countEvaluation() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM evaluation")->fetchColumn();
}

// foncyion statistique

function getNombreEtudiantParNiveau() {
    global $pdo;

    $sql = "
        SELECT n.nom_niveau, COUNT(e.id_etudiant) as total
        FROM niveau n
        LEFT JOIN classe c ON n.id_niveau = c.id_niveau
        LEFT JOIN etudiant e ON c.id_classe = e.id_classe
        GROUP BY n.id_niveau
        ORDER BY n.nom_niveau
    ";

    return $pdo->query($sql)->fetchAll();
}

function getNombreClasseParNiveau() {
    global $pdo;

    $sql = "  SELECT n.nom_niveau, COUNT(c.id_classe) as total
        FROM niveau n
        LEFT JOIN classe c ON n.id_niveau = c.id_niveau
        GROUP BY n.id_niveau
        ORDER BY n.nom_niveau
    ";
    return $pdo->query($sql)->fetchAll();
}


function getMoyennesGenerales() {
    global $pdo;
    $sql = "SELECT 
            e.id_etudiant,
            e.nom,
            e.prenom,
            c.id_classe,
            c.nom_classe,
            n.nom_niveau,
            ROUND(
                SUM(
                    (
                        COALESCE(dev.note,0) * 0.4 +
                        COALESCE(ex.note,0) * 0.6
                    ) * cm.coefficient
                ) / SUM(cm.coefficient)
            ,2) AS moyenne
        FROM etudiant e
        JOIN classe c ON e.id_classe = c.id_classe
        JOIN niveau n ON c.id_niveau = n.id_niveau
        JOIN classe_module cm ON cm.id_classe = c.id_classe

        LEFT JOIN evaluation dev 
            ON dev.id_etudiant = e.id_etudiant 
            AND dev.id_module = cm.id_module
            AND dev.type_evaluation = 'devoir'

        LEFT JOIN evaluation ex 
            ON ex.id_etudiant = e.id_etudiant 
            AND ex.id_module = cm.id_module
            AND ex.type_evaluation = 'examen'

        GROUP BY e.id_etudiant
    ";
    return $pdo->query($sql)->fetchAll();
}

function getBestStudentByClasse() {
    $etudiants = getMoyennesGenerales();

    usort($etudiants, function($a,$b){
        if($a['nom_classe'] == $b['nom_classe']){
            return $b['moyenne'] <=> $a['moyenne'];
        }
        return strcmp($a['nom_classe'],$b['nom_classe']);
    });

    $best = [];
    foreach($etudiants as $e){
        if(!isset($best[$e['nom_classe']])){
            $best[$e['nom_classe']] = $e;
        }
    }

    return $best;
}

function getBestStudentByNiveau() {
    $etudiants = getMoyennesGenerales();

    usort($etudiants, function($a,$b){
        if($a['nom_niveau'] == $b['nom_niveau']){
            return $b['moyenne'] <=> $a['moyenne'];
        }
        return strcmp($a['nom_niveau'],$b['nom_niveau']);
    });

    $best = [];
    foreach($etudiants as $e){
        if(!isset($best[$e['nom_niveau']])){
            $best[$e['nom_niveau']] = $e;
        }
    }

    return $best;
}

function getStatistiquesReussite() {
    $etudiants = getMoyennesGenerales();

    $admis = 0;
    $redouble = 0;
    $renvoye = 0;

    foreach($etudiants as $e){
        if($e['moyenne'] >= 10){
            $admis++;
        } elseif($e['moyenne'] < 5){
            $renvoye++;
        } else {
            $redouble++;
        }
    }

    return [
        'admis'=>$admis,
        'redouble'=>$redouble,
        'renvoye'=>$renvoye
    ];
}


