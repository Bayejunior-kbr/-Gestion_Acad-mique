<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Dashboard</div>
                    <!-- <a class="nav-link" href="?page=acceuil">
                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                        Accueil
                    </a> -->
                    <a class="nav-link" href="?page=dashboard">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Tableau de bord
                    </a>



                     <div class="sb-sidenav-menu-heading">Gestion académique</div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAcademique" aria-expanded="false">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Gestion académique
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseAcademique" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="?pages=niveau">Niveaux</a>
                            <a class="nav-link" href="?pages=classe">Classes</a>
                            <a class="nav-link" href="?pages=etudiant">Étudiants</a>
                        </nav>
                    </div>
                      <!-- Modules & Évaluations déroulant -->
                    <div class="sb-sidenav-menu-heading">Modules & Évaluations</div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseModules" aria-expanded="false">
                        <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                        Modules & Évaluations
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseModules" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="?pages=module">Modules</a>
                            <a class="nav-link" href="?pages=classe_module">Classe - Modules</a>
                            <a class="nav-link" href="?pages=evaluation">Évaluations</a>
                            <a class="nav-link" href="?pages=moyenne">Moyenne</a>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                Start Bootstrap
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 mt-4"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>