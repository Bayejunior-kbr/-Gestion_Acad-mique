<?php
include_once "db.php";
include_once "requette.php";

/* NIVAEU */
if(isset($_POST['ajouter_niveau'])){
    $nom_niveau=$_POST['nom_niveau'];
   if (addNiveau($nom_niveau)) {
    header("Location: ../?pages=niveau&msg=ok");
} else {
    header("Location: ../?pages=niveau&msg=existe");
}
}

// supprimmer niveau
if (isset($_GET['id'])) {
    $id_niveau = $_GET['id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM classe WHERE id_niveau = ?");
    $stmt->execute([$id_niveau]);
    $nb_classes = $stmt->fetchColumn();

    if ($nb_classes > 0) {
        header("Location: ../?pages=niveau&msg=impossible");
        exit;
    } else {
       if(deleteNiveau($id_niveau)){
        header("Location: ../?pages=niveau&msg=supprime");
        exit;}
    }
}


// ajouter classe
if(isset($_POST['ajouter_classe'])){
    $nom_classe=$_POST['nom_classe'];
    $id_niveau=$_POST['id_niveau'];
   if (addclasse($nom_classe,$id_niveau)) {
    header("Location: ../?pages=classe&msg=ok");
} else {
    header("Location: ../?pages=classe&msg=existe");
}
}

// supprimer classe
if (isset($_GET['idClasse'])) {
    $id_classe = $_GET['idClasse'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM etudiant WHERE id_classe = ?");
    $stmt->execute([$id_classe]);
    $nb_etudiant = $stmt->fetchColumn();

    if ($nb_etudiant > 0) {
        header("Location: ../?pages=classe&msg=impossible");
        exit;
    } else {
        if(deleteclasse($id_classe)){
        header("Location: ../?pages=classe&msg=supprime");
        exit;}
    }
}


// ajouter etudiant
if(isset($_POST['ajouter_etudiant'])){
$matricule = genererMatricule($id_classe);
    $nom=$_POST['nom'];
    $prenom=$_POST['prenom'];
    $id_classe=$_POST['id_classe'];
   if (addetudiant($matricule,$nom, $prenom, $id_classe)) {
    header("Location: ../?pages=etudiant&msg=ok");
} else {
    header("Location: ../?pages=etudiant&msg=existe");
}
}

// suppimer etudiant
if (isset($_GET['idEtudiant'])) {
    $id_etudiant = $_GET['idEtudiant'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM evaluation WHERE id_etudiant = ?");
    $stmt->execute([$id_etudiant]);
    $nb_etudiant = $stmt->fetchColumn();

    if ($nb_etudiant > 0) {
        header("Location: ../?pages=etudiant&msg=impossible");
        exit;
    } else {
       if(deleteEtudiant($id_etudiant)){
        header("Location: ../?pages=etudiant&msg=supprime");
        exit;}
    }
}

// modifier etudiant
if(isset($_POST['idEtudiant'])){
    $id=$_POST['idEtudiant'];
    $matricule=$_POST['matricule'];
    $nom=$_POST['nom'];
    $prenom=$_POST['prenom'];
    $id_classe=$_POST['id_classe'];
    if(updateEtudiant($id,$matricule,$nom,$prenom,$id_classe)){
        header("Location: ../?pages=etudiant");
        exit;
    }
}

// ajouter module
if(isset($_POST['ajouter_module'])){
    $code_module = genererCodeModule();
    $nom_module=$_POST['nom_module'];
   if (addModule($code_module,$nom_module)) {
    header("Location: ../?pages=module&msg=ok");
} else {
    header("Location: ../?pages=module&msg=existe");
}
}
// supprimer module
if(isset($_GET['idModule'])){
    $id=$_GET['idModule'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM classe_module WHERE id_module = ?");
    $stmt->execute([$id]);
    $nb_module = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM evaluation WHERE id_module = ?");
    $stmt->execute([$id]);
    $nb_evaluation = $stmt->fetchColumn();
    if ($nb_evaluation > 0) {
        header("Location: ../?pages=module&msg=impossible");
        exit;
    }
    if ($nb_module > 0) {
        header("Location: ../?pages=module&msg=impossible");
        exit;
    } else {
    if(deleteModule($id)){
        header("Location: ../?pages=module&msg=supprime");
        exit;
    }
}
}
// modifier module
if(isset($_POST['modifier_module'])){
    $id=$_POST['id_module'];
    $code_module=$_POST['code_module'];
    $nom_module=$_POST['nom_module'];
   if (updateModule($id,$nom_module,$code_module,)){
    header("Location: ../?pages=module");
} else {
    header("Location: ../?pages=module");
}
}

// ajouter classe module
if(isset($_POST['ajouter_classe_module'])){
    $id_classe=$_POST['classe_id'];
    $id_module=$_POST['module_id'];
    $coefficient=$_POST['coefficient'];
    if(addclasse_module( $id_classe,$id_module,$coefficient)){
     header("Location: ../?pages=classe_module&msg=ok");
} else {
    header("Location: ../?pages=classe_module&msg=existe");
}
}
// supprimer claase module
if(isset($_GET['supprimer_classe_module'])){
    list($id_classe, $id_module) = explode('-', $_GET['supprimer_classe_module']);
    if(deleteclasse_module($id_classe, $id_module)){
        header("Location: ../?pages=classe_module");
        exit;
    }
}
// ===================================================================
// Ajouter une évaluation
if(isset($_POST['ajouter_evaluation'])) {
    $id_etudiant = $_POST['id_etudiant'];
    $id_module   = $_POST['id_module'];
    $type_eval   = $_POST['type_evaluation'];
    $note        = $_POST['note'];

    if(addEvaluation($id_etudiant, $id_module, $type_eval, $note)) {
        header("Location: ../?pages=evaluation&msg=ajoute");
        exit;
    } else {
        header("Location: ../?pages=evaluation&msg=existe");
        exit;
    }
}

// Supprimer une évaluation
if(isset($_GET['supprimer_evaluation'])) {
    $id = $_GET['supprimer_evaluation'];
    if(deleteEvaluation($id)) {
        header("Location: ../?pages=evaluation&msg=supprime");
        exit;
    } else {
        header("Location: ../?pages=evaluation&msg=erreur");
        exit;
    }
}

if(isset($_POST['modifier_evaluation'])) {
    $id_evaluation=$_POST['id_evaluation'];
    $id_etudiant = $_POST['id_etudiant'];
    $id_module   = $_POST['id_module'];
    $type_eval   = $_POST['type_evaluation'];
    $note        = $_POST['note'];

    if(updateEvaluation($id_evaluation, $id_etudiant, $id_module, $type_evaluation, $note)) {
        header("Location: ../?pages=evaluation");
        exit;
    } else {
        header("Location: ../?pages=evaluation");
        exit;
    }
}


?>