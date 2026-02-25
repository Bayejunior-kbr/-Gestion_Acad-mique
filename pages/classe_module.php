<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root { --indigo: #6610f2; --cool-gray: #f8f9fc; }
    body { background-color: var(--cool-gray); }
    .card { border: none; border-radius: 12px; }
    .form-select, .form-control { border-radius: 8px; border: 1px solid #d1d3e2; padding: 0.6rem; }
    .btn-rounded { border-radius: 50px; }
    .filter-section { background: white; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); }
    .module-badge-card { 
        transition: all 0.2s ease-in-out; 
        border-left: 4px solid var(--indigo);
    }
    .module-badge-card:hover { transform: scale(1.02); box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important; }
    .coeff-tag { background: #eef0f8; color: var(--indigo); font-weight: 800; border-radius: 6px; padding: 2px 8px; }
</style>

<div class="container py-4">

    <?php if (isset($_GET['msg'])): ?>
        <div class="mb-4">
            <?php if ($_GET['msg'] == 'existe'): ?>
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Cette liaison existe déjà.
                </div>
            <?php elseif ($_GET['msg'] == 'impossible'): ?>
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-x-octagon-fill me-2"></i> Impossible de supprimer : des données liées sont présentes.
                </div>
            <?php elseif ($_GET['msg'] == 'supprime'): ?>
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i> Affectation supprimée avec succès.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Programmes d'Études</h2>
            <p class="text-muted small mb-0">Gérez les coefficients et l'attribution des modules par classe</p>
        </div>
        <div class="text-end">
            <span class="badge bg-white text-dark shadow-sm p-2 border">
                <i class="bi bi-collection-play text-indigo me-1"></i> <?= count($listeClasseModule) ?> Affectations
            </span>
        </div>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-dark text-white p-3">
            <i class="bi bi-plus-circle me-2"></i> Nouvelle Affectation
        </div>
        <div class="card-body p-4">
            <form method="post" action="traitement/actions.php">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">CLASSE</label>
                        <select name="classe_id" class="form-select bg-light" required>
                            <option value="">-- Choisir une classe --</option>
                            <?php foreach ($listeClasse as $classe): ?>
                                <option value="<?= $classe['id_classe'] ?>">
                                    <?= htmlspecialchars($classe['nom_classe']) ?> (<?= htmlspecialchars($classe['nom_niveau']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">MODULE</label>
                        <select name="module_id" class="form-select bg-light" required>
                            <option value="">-- Choisir un module --</option>
                            <?php foreach ($listeModule as $module): ?>
                                <option value="<?= $module['id_module'] ?>"><?= htmlspecialchars($module['nom_module']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">COEFFICIENT</label>
                        <input type="number" name="coefficient" class="form-control bg-light" min="1" placeholder="Ex: 2" required>
                    </div>
                    <div class="col-12 text-end pt-2">
                        <button type="reset" class="btn btn-light btn-rounded px-4 me-2 border">Annuler</button>
                        <button type="submit" name="ajouter_classe_module" class="btn btn-success btn-rounded px-4 fw-bold">
                            <i class="bi bi-link-45deg"></i> Créer le lien
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="filter-section p-3 mb-4 shadow-sm">
        <form method="get" class="row g-3 align-items-center">
            <input type="hidden" name="pages" value="classe_module">
            <div class="col-md-auto text-muted small fw-bold"><i class="bi bi-funnel-fill me-1"></i> FILTRER :</div>
            <div class="col-md-4">
                <select name="classe" class="form-select form-select-sm shadow-none" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    <?php foreach($listeClasse as $c): ?>
                        <option value="<?= $c['id_classe'] ?>" <?= ($classe_filter == $c['id_classe']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom_classe']) ?> (<?= htmlspecialchars($c['nom_niveau']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="module" class="form-select form-select-sm shadow-none" onchange="this.form.submit()">
                    <option value="">Tous les modules</option>
                    <?php foreach($listeModule as $m): ?>
                        <option value="<?= $m['id_module'] ?>" <?= ($module_filter == $m['id_module']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nom_module']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-auto ms-auto">
                <a href="?pages=classe_module" class="text-decoration-none small text-danger"><i class="bi bi-x-circle"></i> Réinitialiser</a>
            </div>
        </form>
    </div>

    <div class="row g-3">
        <?php if (!empty($listeClasseModule)): ?>
            <?php foreach ($listeClasseModule as $cm): ?>
                <div class="col-xl-3 col-md-4 col-sm-6">
                    <div class="card h-100 shadow-sm module-badge-card border-0">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-auto">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-dark-subtle text-dark border-0 small font-monospace">
                                        <?= htmlspecialchars($cm['nom_niveau']) ?>
                                    </span>
                                    <span class="coeff-tag small">x<?= $cm['coefficient'] ?></span>
                                </div>
                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($cm['nom_classe']) ?></h6>
                                <p class="text-primary fw-medium small mb-0"><?= htmlspecialchars($cm['nom_module']) ?></p>
                            </div>
                            
                            <div class="mt-3 pt-3 border-top text-end">
                                <a onclick="return confirm('Supprimer cette affectation ?')" 
                                   href="traitement/actions.php?supprimer_classe_module=<?= $cm['id_classe'] ?>-<?= $cm['id_module'] ?>" 
                                   class="btn btn-outline-danger btn-sm border-0 rounded-circle py-1 px-2"
                                   title="Supprimer">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 py-5 text-center">
                <div class="opacity-50">
                    <i class="bi bi-search fs-1"></i>
                    <p class="mt-2">Aucun résultat ne correspond à vos filtres.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>