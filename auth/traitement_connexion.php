<?php
session_start();

// Tableau utilisateur "admin"
$utilisateurs = [
    [
        'email' => 'admin@ecole.com',
        'password' => 'admin123', // mot de passe en clair pour l'exemple
        'nom' => 'Administrateur'
    ]
];

// Récupération des infos du formulaire
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$connecte = false;

// Vérification
foreach($utilisateurs as $user){
    if($user['email'] === $email && $user['password'] === $password){
        $_SESSION['user'] = [
            'email' => $user['email'],
            'nom'   => $user['nom']
        ];
        $connecte = true;
        break;
    }
}

if($connecte){
    // Redirection vers la page principale du projet
    header("Location: ../index.php");
    exit;
}else{
    // Retour vers login avec erreur
    $_SESSION['erreur'] = "Email ou mot de passe incorrect";
    header("Location: login.php");
    exit;
}