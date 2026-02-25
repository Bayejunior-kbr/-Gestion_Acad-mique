<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #858796;
    }
    body { background-color: #f8f9fc; color: #333; }
    .card { border: none; border-radius: 12px; transition: transform 0.2s; }
    .card-hover:hover { transform: translateY(-5px); }
    .btn-rounded { border-radius: 50px; padding-left: 20px; padding-right: 20px; }
    .alert { border: none; border-radius: 10px; }
    .modal-content { border: none; border-radius: 15px; overflow: hidden; }
    .status-badge { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
</style>

<div class="container py-5">
    
    <?php if (isset($_GET['msg'])) : ?>
        <div class="mb-4">
            <?php if ($_GET['msg'] == 'existe') : ?>
                <div class="alert alert-warning d-flex align-items-center shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Ce niveau existe déjà.
                </div>
            <?php elseif ($_GET['msg'] == 'impossible') : ?>
                <div class="alert alert-danger d-flex align-items-center shadow-sm">
                    <i class="bi bi-x-circle-fill me-2"></i> Impossible de supprimer ce niveau, il contient des classes.
                </div>
            <?php elseif ($_GET['msg'] == 'supprime') : ?>
                <div class="alert alert-success d-flex align-items-center shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i> Le niveau a été supprimé avec succès.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-primary mb-0">Gestion des Niveaux</h2>
            <p class="text-muted small">Organisation des cycles universitaires</p>
        </div>
        <div class="bg-white p-2 px-3 rounded-pill shadow-sm border">
            <i class="bi bi-calendar3 text-primary me-2"></i>
            <span class="small fw-semibold">Session 2025-2026</span>
        </div>
    </div>

    <div class="card shadow-sm mb-5 border-start border-primary border-4">
        <div class="card-body p-4">
            <h5 class="card-title fw-bold mb-4"><i class="bi bi-plus-lg me-2"></i>Nouveau Cycle</h5>
            <form method="post" action="traitement/actions.php" class="row g-3">
                <div class="col-md-8">
                    <div class="form-floating">
                        <select name="nom_niveau" class="form-select border-0 bg-light" id="floatingSelect" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="L1">Licence 1 (L1)</option>
                            <option value="L2">Licence 2 (L2)</option>
                            <option value="L3">Licence 3 (L3)</option>
                            <option value="M1">Master 1 (M1)</option>
                            <option value="M2">Master 2 (M2)</option>
                        </select>
                        <label for="floatingSelect">Choisir le niveau académique</label>
                    </div>
                </div>
                <div class="col-md-4 d-grid">
                    <button name="ajouter_niveau" class="btn btn-primary btn-rounded fw-bold shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter le niveau
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($listeniveau as $niveau) : ?>
        <div class="col">
            <div class="card h-100 shadow-sm text-center card-hover border-bottom border-3 <?= $niveau['nb_classes'] == 0 ? 'border-danger' : 'border-success' ?>">
                <div class="card-body p-4">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-mortarboard fs-3 text-primary"></i>
                    </div>
                    <h4 class="fw-extrabold mb-1"><?= htmlspecialchars($niveau['nom_niveau']) ?></h4>
                    <div class="mb-4">
                        <span class="badge status-badge rounded-pill <?= $niveau['nb_classes'] == 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?>">
                            <?= $niveau['nb_classes'] ?> classe<?= $niveau['nb_classes'] > 1 ? 's' : '' ?>
                        </span>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-light btn-sm fw-semibold rounded-3" 
                                data-bs-toggle="modal" data-bs-target="#modal<?= $niveau['id_niveau'] ?>">
                            <i class="bi bi-eye me-1"></i> Classes
                        </button>
                        <a onclick="return confirm('Voulez-vous vraiment supprimer ce niveau ?')" 
                           href="traitement/actions.php?id=<?= $niveau['id_niveau']; ?>" 
                           class="btn btn-link btn-sm text-danger text-decoration-none">
                           <i class="bi bi-trash3 me-1"></i> Supprimer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal<?= $niveau['id_niveau'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header border-0 bg-primary text-white p-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-layers me-2"></i>Classes en <?= htmlspecialchars($niveau['nom_niveau']) ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body p-4">
                        <?php if (!empty($niveau['classes'])) : ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($niveau['classes'] as $classe) : ?>
                                    <div class="list-group-item d-flex align-items-center border-0 px-0">
                                        <i class="bi bi-arrow-right-short text-primary fs-4 me-2"></i>
                                        <span class="fw-medium text-secondary"><?= htmlspecialchars($classe['nom_classe']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="text-center py-4">
                                <i class="bi bi-folder-x fs-1 text-muted opacity-25"></i>
                                <p class="text-muted mt-2">Aucune classe enregistrée</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer border-0 p-3">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>