<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #4e73df;
        --accent-info: #36b9cc;
    }
    body { background-color: #f8f9fc; color: #333; }
    .card { border: none; border-radius: 12px; transition: all 0.2s ease; }
    .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .btn-rounded { border-radius: 50px; }
    .form-control, .form-select { border-radius: 8px; border: 1px solid #e3e6f0; padding: 0.75rem; }
    .badge-soft { font-weight: 600; padding: 0.5em 0.8em; border-radius: 6px; }
    .modal-content { border: none; border-radius: 15px; }
    .table thead th { background-color: #f8f9fc; text-transform: uppercase; font-size: 0.75rem; color: #858796; letter-spacing: 1px; }
</style>

<div class="container py-4">

    <?php if (isset($_GET['msg'])) : ?>
        <div class="mb-4">
            <?php if ($_GET['msg'] == 'existe') : ?>
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> Cette classe existe déjà.
                </div>
            <?php elseif ($_GET['msg'] == 'impossible') : ?>
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-slash-circle-fill me-2"></i> Impossible de supprimer : cette classe contient des étudiants.
                </div>
            <?php elseif ($_GET['msg'] == 'supprime') : ?>
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-check-lg me-2"></i> La classe a été supprimée avec succès.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="row align-items-center mb-5">
        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
            <h2 class="fw-bold text-primary mb-0">Gestion des Classes</h2>
            <p class="text-muted small mb-0"><i class="bi bi-diagram-3 me-1"></i> Organisation du cursus académique</p>
        </div>
        
        <div class="col-md-6">
            <form method="get" class="d-flex justify-content-md-end align-items-center">
                <input type="hidden" name="pages" value="classe">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0 text-muted small"><i class="bi bi-filter"></i></span>
                    <select name="niveau_filter" class="form-select border-start-0 small shadow-sm" onchange="this.form.submit()">
                        <option value="">Tous les niveaux</option>
                        <?php foreach ($listeniveaux as $niv) : ?>
                            <option value="<?= $niv['id_niveau'] ?>" 
                                <?= (isset($_GET['niveau_filter']) && $_GET['niveau_filter'] == $niv['id_niveau']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($niv['nom_niveau']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-5 border-top border-primary border-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-plus-square-dotted me-2 text-primary"></i>Nouvelle Section</h5>
            <form method="post" action="traitement/actions.php" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-uppercase">Nom de la classe</label>
                    <input type="text" name="nom_classe" class="form-control bg-light" placeholder="Ex : GL, IAGE, CYBER" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-uppercase">Niveau de rattachement</label>
                    <select name="id_niveau" class="form-select bg-light" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($listeniveaux as $niv) : ?>
                            <option value="<?= $niv['id_niveau'] ?>"><?= htmlspecialchars($niv['nom_niveau']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button name="ajouter_classe" class="btn btn-primary w-100 fw-bold btn-rounded shadow-sm py-2">
                        Créer la classe
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php if (!empty($listeClasse)) : ?>
        <?php foreach ($listeClasse as $classe) : ?>
            <div class="col">
                <div class="card h-100 shadow-sm text-center card-hover">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="mb-3">
                            <span class="badge bg-primary-subtle text-primary badge-soft mb-2"><?= htmlspecialchars($classe['nom_niveau']) ?></span>
                            <h4 class="fw-bold text-dark mb-0"><?= htmlspecialchars($classe['nom_classe']) ?></h4>
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-accent-info d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-people-fill"></i>
                                <span class="fw-bold"><?= $classe['nb_etudiants'] ?></span>
                                <span class="small text-muted">Étudiants</span>
                            </div>
                        </div>

                        <div class="mt-auto d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" 
                                    data-bs-toggle="modal" data-bs-target="#modalEtudiants<?= $classe['id_classe'] ?>">
                                <i class="bi bi-person-list me-1"></i> Étudiants
                            </button>

                            <button type="button" class="btn btn-outline-info btn-sm rounded-pill" 
                                    data-bs-toggle="modal" data-bs-target="#modalModules<?= $classe['id_classe'] ?>">
                                <i class="bi bi-journal-bookmark me-1"></i> Modules
                            </button>

                            <hr class="my-2 opacity-10">

                            <a onclick="return confirm('Supprimer définitivement la classe <?= htmlspecialchars($classe['nom_classe']) ?> ?')" 
                               href="traitement/actions.php?idClasse=<?= $classe['id_classe'] ?>" 
                               class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                               <i class="bi bi-trash3 me-1"></i> Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalEtudiants<?= $classe['id_classe'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content shadow-lg">
                        <div class="modal-header border-0 bg-primary text-white p-4">
                            <h5 class="modal-title fw-bold"><i class="bi bi-people me-2"></i>Liste des Étudiants - <?= htmlspecialchars($classe['nom_classe']) ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <?php $etudiants = getEtudiantByClasse($classe['id_classe']); ?>
                            <?php if(!empty($etudiants)) : ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Matricule</th>
                                                <th>Nom Complet</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($etudiants as $etudiant): ?>
                                                <tr>
                                                    <td class="fw-bold text-primary"><?= htmlspecialchars($etudiant['matricule']) ?></td>
                                                    <td><?= htmlspecialchars($etudiant['prenom']) ?> <span class="text-uppercase fw-semibold"><?= htmlspecialchars($etudiant['nom']) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-person-exclamation fs-1 text-muted opacity-25"></i>
                                    <p class="text-muted mt-2">Aucun étudiant inscrit dans cette classe.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalModules<?= $classe['id_classe'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content shadow-lg">
                        <div class="modal-header border-0 bg-info text-white p-4">
                            <h5 class="modal-title fw-bold"><i class="bi bi-journal-code me-2"></i>Programme - <?= htmlspecialchars($classe['nom_classe']) ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <?php $modules = getModuleByClasse($classe['id_classe']); ?>
                            <?php if(!empty($modules)) : ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Module</th>
                                                <th>Code</th>
                                                <th class="text-center">Coefficient</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($modules as $module): ?>
                                                <tr>
                                                    <td class="fw-semibold"><?= htmlspecialchars($module['nom_module']) ?></td>
                                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($module['code_module']) ?></span></td>
                                                    <td class="text-center"><span class="fw-bold text-info"><?= htmlspecialchars($module['coefficient']) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-journal-x fs-1 text-muted opacity-25"></i>
                                    <p class="text-muted mt-2">Aucun module n'a été affecté.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    <?php else : ?>
        <div class="col-12">
            <div class="card p-5 text-center border-dashed">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-3">Aucune classe ne correspond à vos critères.</p>
            </div>
        </div>
    <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>