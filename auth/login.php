<?php
session_start();
$erreur = $_SESSION['erreur'] ?? '';
unset($_SESSION['erreur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Connexion - Portail Académique</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            border: none;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .login-header {
            padding: 40px 40px 20px;
            text-align: center;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: #4e73df;
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 16px rgba(78, 115, 223, 0.3);
        }

        .form-floating > .form-control {
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            padding-left: 15px;
        }

        .form-floating > .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.1);
        }

        .btn-login {
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            background: #4e73df;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #224abe;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(34, 74, 190, 0.2);
        }

        .card-footer {
            background: transparent;
            border-top: 1px solid #f0f0f0;
            padding: 25px;
        }

        a {
            text-decoration: none;
            color: #4e73df;
            transition: color 0.2s;
        }

        a:hover {
            color: #224abe;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-7 col-sm-9">
          
            <div class="card login-card">
                <div class="login-header">
                    <div class="brand-logo">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Bienvenue</h3>
                    <p class="text-muted">Connectez-vous pour accéder à votre espace</p>
                </div>

                <div class="card-body px-4 px-md-5">
                    <form action="traitement_connexion.php" method="POST">
                          <?php if($erreur): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputEmail" name="email" type="email" placeholder="nom@exemple.com" required />
                            <label for="inputEmail"><i class="bi bi-envelope me-2"></i>Adresse Email</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Mot de passe" required />
                            <label for="inputPassword"><i class="bi bi-shield-lock me-2"></i>Mot de passe</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" id="rememberMe" type="checkbox" />
                                <label class="form-check-label small text-muted" for="rememberMe">Se souvenir de moi</label>
                            </div>
                            <a class="small fw-600" href="#">Oublié ?</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                            Se connecter
                        </button>
                    </form>
                </div>

                <div class="card-footer text-center">
                    <div class="small">
                        <span class="text-muted">Pas encore de compte ?</span> 
                        <a href="#" class="fw-bold">Créer un compte</a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 text-muted small">
                &copy; 2026 Votre Établissement &middot; <a href="#" class="text-muted">Mentions légales</a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>