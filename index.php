<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: auth/login.php");
    exit;
}
$dossierPublic="http://localhost/mini_projet/application/public/";
include_once "includes/header.php";
include_once "includes/navbar.php";
include_once "includes/sidebar.php";
include_once "traitement/requette.php";

$listeniveau=getNiveaux();
$listeClasse=getClasse();
$listeEtudiant=getEtudiant();
$listeModule=getModule();
$listeClasseModule=getClasse_module();
$listeEvaluations=getEvaluations();


$totalEtudiant = countEtudiant();
$totalClasse = countClasse();
$totalModule = countModule();
$totalEvaluation = countEvaluation();

$stats = getStatistiquesReussite();

// dans pages classe module
$classe_filter = $_GET['classe'] ?? '';
$module_filter = $_GET['module'] ?? '';
$listeClasseModule = getClasseModuleFiltered($classe_filter, $module_filter);
$listeClasse = getClasse();
$listeModule = getModule(); 


// dans pages evaluationn
$module_filter = isset($_GET['module']) && $_GET['module'] !== '' ? (int)$_GET['module'] : '';
$type_filter   = $_GET['type'] ?? '';
$page          = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$limit         = 10;
$offset        = ($page - 1) * $limit;
$listeEvaluations = getEvaluationsPaginated($limit, $offset, $module_filter, $type_filter);
$totalEvaluations = countEvaluations($module_filter, $type_filter);
$totalPages       = ceil($totalEvaluations / $limit);
$listeModules = getModule(); 
$listeEtudiants = getEtudiant(); 


// dans classe

$listeniveaux = getNiveaux();
$niveau_filter = $_GET['niveau_filter'] ?? null;
$listeClasse = getClasses($niveau_filter);



//dans etudiant.php
$limit = 10;
$page = isset($_GET['page_num']) && is_numeric($_GET['page_num']) 
        ? (int) $_GET['page_num'] 
        : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$niveau_filter = $_GET['niveau_filter'] ?? null;
$classe_filter = $_GET['classe_filter'] ?? null;
$listeClasses = getClasse(); 
$totalEtudiants = countAllEtudiants($niveau_filter, $classe_filter);
$totalPages = ceil($totalEtudiants / $limit);
$listeEtudiant = getEtudiantsPaginated($limit, $offset, $niveau_filter, $classe_filter);



// dans la pages moyenne 
$classe = isset($_GET['classe']) ? $_GET['classe'] : '';
$niveau = isset($_GET['niveau']) ? $_GET['niveau'] : '';
$nom    = isset($_GET['nom']) ? $_GET['nom'] : '';
$page   = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$limit  = 15;
$offset = ($page - 1) * $limit;

$listeMoyennes   = getMoyennesPaginated($limit, $offset, $classe, $niveau, $nom);
$totalEtudiants  = countFilteredMoyennes($classe, $niveau, $nom);
$totalPages       = ceil($totalEtudiants / $limit);

$listeClasses = getClasse();
$listeNiveaux = getNiveaux();



// modif evaluation
$editEvaluation = false;
if(isset($_GET['idEvaluation'])) {
    $editEvaluation = getEvaluationById($_GET['idEvaluation']);
}

// modif module
$editModule=false;
if(isset($_GET['idModule'])){
    $id=$_GET['idModule'];
    $editModule=findmodulebyid($id);
}

// modif etudiant
$editEtudiant=false;
if(isset($_GET['idEtudiant'])){
    $id=$_GET['idEtudiant'];
    $editEtudiant=findetudiantbyid($id);
}


//dashboard
$admis = $stats['admis'];
$redouble = $stats['redouble'];
$renvoye = $stats['renvoye'];
$totalGeneral = $admis + $redouble + $renvoye;

// Helper pour le calcul de pourcentage
function getPercent($val, $total) {
    return ($total > 0) ? round(($val / $total) * 100, 1) : 0;
} 



$view= isset($_GET['pages'])?$_GET['pages']:'dashboard';
if(file_exists("pages/$view.php")){
    include_once "pages/$view.php";
}else{
    include_once "pages/erreur404.php";
}
// include_once "includes/footer.php";

?>