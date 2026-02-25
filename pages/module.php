<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root { --primary-color: #4e73df; --dark-blue: #224abe; }
    body { background-color: #f8f9fc; }
    .card { border: none; border-radius: 12px; transition: transform 0.2s; }
    .module-card:hover { transform: translateY(-5px); }
    .btn-rounded { border-radius: 50px; }
    .form-control, .form-select { border-radius: 8px; border: 1px solid #e3e6f0; padding: 0.6rem 1rem; }
    .badge-code { font-family: 'Monaco', 'Consolas', monospace; font-size: 0.85rem; padding: 0.5em 0.8em; }
</style>

<div class="container py-4">

    <?php if (isset($_GET['msg'])) : ?>
        <div class="mb-4">
            <?php if ($_GET['msg'] == 'existe') : ?>
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Ce module ou code existe déjà.
                </div>
            <?php elseif ($_GET['msg'] == 'impossible') : ?>
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-shield-lock-fill me-2"></i> Impossible de supprimer ce module : il est assigné à des classes.
                </div>
            <?php elseif ($_GET['msg'] == 'supprime') : ?>
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i> Le module a été retiré avec succès.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="mb-5">
        <h2 class="fw-bold text-primary mb-0">Gestion des Modules</h2>
        <p class="text-muted small"><i class="bi bi-book me-1"></i> Configuration des unités d'enseignement</p>
    </div>

    <div class="card shadow-sm mb-5 border-top border-4 <?= $editModule ? 'border-warning' : 'border-primary' ?>">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4 text-dark">
                <i class="bi <?= $editModule ? 'bi-pencil-square text-warning' : 'bi-plus-circle text-primary' ?> me-2"></i>
                <?= $editModule ? 'Modifier le module' : 'Ajouter un nouveau module' ?>
            </h5>
            
            <form method="post" action="traitement/actions.php" class="row g-3 align-items-end">
                <?php if ($editModule) : ?>
                    <input type="hidden" name="id_module" value="<?= $editModule['id_module'] ?>">
                <?php endif; ?>

                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">NOM DU MODULE</label>
                    <select name="nom_module" class="form-select bg-light" required>
                        <option value="">-- Sélectionner un module --</option>
                        <?php
                        $modules = [
                            'Anglais','Mathématiques','PHP / MySQL','SGBD','CMS','OE',
                            'C++','Java','Python','Systèmes Linux',
                            'Réseaux informatiques','Algorithmique',
                            'Développement Web','Sécurité informatique'
                        ];
                        foreach ($modules as $m) : ?>
                            <option value="<?= $m ?>" <?= ($editModule && $editModule['nom_module'] == $m) ? 'selected' : '' ?>>
                                <?= $m ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">CODE IDENTIFIANT</label>
                    <input type="text" name="code_module" class="form-control bg-light fw-bold" 
                           value="<?= $editModule['code_module'] ?? '' ?>" readonly placeholder="Généré automatiquement">
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button name="<?= $editModule ? 'modifier_module' : 'ajouter_module' ?>" class="btn btn-<?= $editModule ? 'warning' : 'success' ?> btn-rounded flex-grow-1 fw-bold shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> <?= $editModule ? 'Mettre à jour' : 'Enregistrer' ?>
                        </button>

                        <?php if ($editModule) : ?>
                            <a href="?pages=module" class="btn btn-light btn-rounded"><i class="bi bi-x"></i></a>
                        <?php else : ?>
                            <button type="reset" class="btn btn-light btn-rounded"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php if (!empty($listeModule)) : ?>
            <?php foreach ($listeModule as $module) : ?>
                <div class="col-xl-3 col-md-4 col-sm-6">
                    <div class="card h-100 shadow-sm text-center module-card border-bottom border-3 border-primary-subtle">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                    <i class="bi bi-journal-code fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($module['nom_module']) ?></h6>
                                <span class="badge bg-dark-subtle text-dark border badge-code"><?= htmlspecialchars($module['code_module']) ?></span>
                            </div>

                            <hr class="my-3 opacity-10">

                            <div class="d-flex justify-content-center gap-2">
                                <a title="Modifier" href="?pages=module&idModule=<?= $module['id_module'] ?>" 
                                   class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a title="Supprimer" onclick="return confirm('Voulez-vous supprimer ce module ?')" 
                                   href="traitement/actions.php?idModule=<?= $module['id_module'] ?>" 
                                   class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12 text-center py-5">
                <div class="card border-dashed p-5 bg-transparent">
                    <i class="bi bi-collection fs-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-2">Aucun module n'a été configuré pour le moment.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>