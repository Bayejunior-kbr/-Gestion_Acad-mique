<style>
    .stat-card { border: none; border-radius: 20px; transition: transform 0.3s ease; overflow: hidden; }
    .stat-card:hover { transform: translateY(-5px); }
    .icon-shape { width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .ranking-card { border-left: 5px solid; border-radius: 15px; background: #fff; }
    .progress { height: 8px; border-radius: 10px; }
    .avatar-sm { width: 40px; height: 40px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #495057; }
</style>

<div class="container-fluid py-4">
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm bg-gradient-primary text-white" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 small opacity-75">Total Étudiants</h6>
                            <h2 class="mb-0 fw-bold"><?= $totalEtudiant ?></h2>
                        </div>
                        <div class="icon-shape"><i class="bi bi-people"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm bg-gradient-success text-white" style="background: linear-gradient(45deg, #1cc88a, #13855c);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 small opacity-75">Classes Actives</h6>
                            <h2 class="mb-0 fw-bold"><?= $totalClasse ?></h2>
                        </div>
                        <div class="icon-shape"><i class="bi bi-door-open"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm bg-gradient-warning text-white" style="background: linear-gradient(45deg, #f6c23e, #dda20a);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 small opacity-75">Modules</h6>
                            <h2 class="mb-0 fw-bold"><?= $totalModule ?></h2>
                        </div>
                        <div class="icon-shape"><i class="bi bi-journal-bookmark"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm bg-gradient-danger text-white" style="background: linear-gradient(45deg, #e74a3b, #be2617);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 small opacity-75">Évaluations</h6>
                            <h2 class="mb-0 fw-bold"><?= $totalEvaluation ?></h2>
                        </div>
                        <div class="icon-shape"><i class="bi bi-pencil-square"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <h5 class="fw-bold mb-4"><i class="bi bi-trophy text-warning me-2"></i>Tableau d'Honneur</h5>
            
            <div class="mb-4">
                <p class="text-muted small fw-bold text-uppercase">Majors par Niveau</p>
                <?php foreach(getBestStudentByNiveau() as $s): ?>
                <div class="card ranking-card shadow-sm mb-3 border-primary p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3 bg-primary text-white">🥇</div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold"><?= $s['nom'] ?> <?= $s['prenom'] ?></h6>
                            <small class="text-muted"><?= $s['nom_niveau'] ?></small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary rounded-pill"><?= round($s['moyenne'],2) ?>/20</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div>
                <p class="text-muted small fw-bold text-uppercase">Majors par Classe</p>
                <div class="row g-3">
                    <?php foreach(getBestStudentByClasse() as $s): ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-3 h-100" style="border-right: 4px solid #1cc88a !important;">
                            <div class="d-flex justify-content-between">
                                <small class="fw-bold text-success"><?= $s['nom_classe'] ?></small>
                                <small class="text-muted fw-bold"><?= round($s['moyenne'],2) ?>/20</small>
                            </div>
                            <div class="mt-2 fw-bold text-dark"><?= $s['nom'] ?> <?= $s['prenom'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <h5 class="fw-bold mb-4"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Analyses & Cohortes</h5>
            
            <div class="card shadow-sm border-0 mb-4 p-4">
                <h6 class="fw-bold mb-4">Statut de la Promotion</h6>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">Admis</span>
                        <span class="small fw-bold text-success"><?= getPercent($admis, $totalGeneral) ?>%</span>
                    </div>
                    <div class="progress"><div class="progress-bar bg-success" style="width: <?= getPercent($admis, $totalGeneral) ?>%"></div></div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">Redoublants</span>
                        <span class="small fw-bold text-warning"><?= getPercent($redouble, $totalGeneral) ?>%</span>
                    </div>
                    <div class="progress"><div class="progress-bar bg-warning" style="width: <?= getPercent($redouble, $totalGeneral) ?>%"></div></div>
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">Renvoyés</span>
                        <span class="small fw-bold text-danger"><?= getPercent($renvoye, $totalGeneral) ?>%</span>
                    </div>
                    <div class="progress"><div class="progress-bar bg-danger" style="width: <?= getPercent($renvoye, $totalGeneral) ?>%"></div></div>
                </div>
            </div>

            <div class="card shadow-sm border-0 p-4">
                <h6 class="fw-bold mb-3">Effectifs & Structures</h6>
                <ul class="list-group list-group-flush">
                    <?php 
                    $etudiantsParNiveau = getNombreEtudiantParNiveau();
                    $classesParNiveau = getNombreClasseParNiveau();
                    foreach($etudiantsParNiveau as $index => $n): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <div>
                            <span class="fw-bold d-block"><?= $n['nom_niveau'] ?></span>
                            <small class="text-muted"><?= $classesParNiveau[$index]['total'] ?> Classes</small>
                        </div>
                        <span class="badge bg-soft-primary text-primary fw-bold" style="background: #eef2ff;"><?= $n['total'] ?> élèves</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>