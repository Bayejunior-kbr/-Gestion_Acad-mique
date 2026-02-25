<style>
    :root { --eval-bg: #f8f9fc; --note-high: #1cc88a; --note-mid: #f6c23e; --note-low: #e74a3b; }
    body { background-color: var(--eval-bg); }
    .card { border: none; border-radius: 12px; }
    .btn-rounded { border-radius: 50px; }
    .table thead th { background-color: #4e73df; color: white; border: none; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
    .note-badge { font-weight: 800; font-size: 1rem; padding: 0.5rem 1rem; border-radius: 8px; min-width: 65px; display: inline-block; }
    .badge-devoir { background-color: #4e73df; }
    .badge-examen { background-color: #6610f2; }
    .badge-tp { background-color: #36b9cc; }
    .pagination .page-link { border: none; margin: 0 3px; border-radius: 8px; color: #4e73df; font-weight: bold; }
    .pagination .page-item.active .page-link { background-color: #4e73df; }
    .filter-bar { background: #fff; border-radius: 50px; padding: 15px 25px; border: 1px solid #e3e6f0; }
</style>

<div class="container py-4">

    <?php if (isset($_GET['msg'])): ?>
        <div class="mb-4">
            <?php 
                $alerts = [
                    'modifie' => ['success', 'bi-check-all', 'Évaluation mise à jour !'],
                    'ajoute' => ['success', 'bi-plus-circle', 'Nouvelle note enregistrée.'],
                    'supprime' => ['success', 'bi-trash', 'Évaluation supprimée.'],
                    'erreur' => ['danger', 'bi-exclamation-octagon', 'Une erreur est survenue.'],
                    'existe' => ['warning', 'bi-info-circle', 'Cette évaluation existe déjà.']
                ];
                if(isset($alerts[$_GET['msg']])): $a = $alerts[$_GET['msg']];
            ?>
                <div class="alert alert-<?= $a[0] ?> border-0 shadow-sm d-flex align-items-center mb-0">
                    <i class="bi <?= $a[1] ?> me-2"></i> <?= $a[2] ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark">Saisie des Notes</h2>
            <p class="text-muted mb-0">Gestion du carnet de notes numérique</p>
        </div>
    </div>

    <div class="card shadow-sm mb-5 border-top border-4 border-primary">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="bi <?= $editEvaluation ? 'bi-pencil-square text-warning' : 'bi-plus-circle text-primary' ?> me-2"></i>
                <?= $editEvaluation ? "Modifier la note" : "Nouvelle évaluation" ?>
            </h5>
            
            <form method="post" action="traitement/actions.php">
                <?php if($editEvaluation): ?>
                    <input type="hidden" name="id_evaluation" value="<?= $editEvaluation['id_evaluation'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">ÉTUDIANT</label>
                        <select name="id_etudiant" class="form-select bg-light" required>
                            <option value="">-- Sélectionner l'étudiant --</option>
                            <?php foreach($listeEtudiants as $et): ?>
                                <option value="<?= $et['id_etudiant'] ?>" <?= $editEvaluation && $editEvaluation['id_etudiant']==$et['id_etudiant'] ? 'selected' : '' ?>>
                                    [<?= htmlspecialchars($et['nom_classe']) ?>] <?= htmlspecialchars($et['nom'].' '.$et['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">MODULE</label>
                        <select name="id_module" class="form-select bg-light" required>
                            <option value="">-- Module --</option>
                            <?php foreach($listeModules as $m): ?>
                                <option value="<?= $m['id_module'] ?>" <?= $editEvaluation && $editEvaluation['id_module']==$m['id_module'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nom_module']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">TYPE</label>
                        <select name="type_evaluation" class="form-select bg-light" required>
                            <option value="">-- Type --</option>
                            <?php foreach(['devoir','examen','TP'] as $type): ?>
                                <option value="<?= $type ?>" <?= $editEvaluation && $editEvaluation['type_evaluation']==$type ? 'selected' : '' ?>>
                                    <?= ucfirst($type) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label small fw-bold text-muted">NOTE</label>
                        <input type="number" name="note" class="form-control bg-light fw-bold" min="0" max="20" step="0.01" value="<?= $editEvaluation ? $editEvaluation['note'] : '' ?>" required>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <?php if($editEvaluation): ?>
                            <a href="?pages=evaluation" class="btn btn-light btn-rounded px-4 me-2 border">Annuler</a>
                        <?php endif; ?>
                        <button type="submit" name="<?= $editEvaluation ? 'modifier_evaluation' : 'ajouter_evaluation' ?>" class="btn btn-primary btn-rounded px-5 shadow">
                            <?= $editEvaluation ? 'Mettre à jour' : 'Enregistrer la note' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="filter-bar shadow-sm mb-4">
        <form method="get" class="row align-items-center g-3">
            <input type="hidden" name="pages" value="evaluation">
            <div class="col-md-auto">
                <span class="text-muted fw-bold small"><i class="bi bi-funnel me-1"></i> FILTRES :</span>
            </div>
            <div class="col-md-4">
                <select class="form-select form-select-sm border-0 bg-transparent fw-bold" onchange="location = this.value;">
                    <option value="?pages=evaluation">Tous les modules</option>
                    <?php foreach($listeModules as $module): ?>
                        <option value="?pages=evaluation&module=<?= $module['id_module'] ?>&type=<?= $type_filter ?>" <?= $module_filter == $module['id_module'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($module['nom_module']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="type" class="form-select form-select-sm border-0 bg-transparent fw-bold" onchange="this.form.submit()">
                    <option value="">Tous les types d'éval</option>
                    <?php foreach(['devoir','examen','TP'] as $t): ?>
                        <option value="<?= $t ?>" <?= $type_filter==$t?'selected':'' ?>><?= ucfirst($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php if (!empty($listeEvaluations)) : ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Étudiant</th>
                                <th>Module / Type</th>
                                <th>Note sur 20</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listeEvaluations as $i => $eval) : 
                                // Détermination de la couleur de la note
                                $noteColor = ($eval['note'] >= 15) ? 'note-high' : (($eval['note'] >= 10) ? 'note-mid' : 'note-low');
                                $bgNote = ($eval['note'] >= 15) ? '#e6fcf5' : (($eval['note'] >= 10) ? '#fff9db' : '#fff5f5');
                            ?>
                                <tr>
                                    <td class="ps-4 text-muted small"><?= $offset + $i + 1 ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($eval['nom_etudiant'].' '.$eval['prenom_etudiant']) ?></div>
                                        <div class="text-muted small">ID: #<?= $eval['id_evaluation'] ?></div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border me-1"><?= htmlspecialchars($eval['nom_module']) ?></span>
                                        <span class="badge rounded-pill badge-<?= strtolower($eval['type_evaluation']) ?> shadow-sm"><?= ucfirst($eval['type_evaluation']) ?></span>
                                    </td>
                                    <td>
                                        <div class="note-badge text-center" style="color: var(--<?= $noteColor ?>); background: <?= $bgNote ?>;">
                                            <?= number_format($eval['note'], 2) ?>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="?pages=evaluation&idEvaluation=<?= $eval['id_evaluation'] ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="bi bi-pencil">modif</i>
                                            </a>
                                            <a href="traitement/actions.php?supprimer_evaluation=<?= $eval['id_evaluation'] ?>" 
                                               onclick="return confirm('Supprimer cette note définitivement ?')" 
                                               class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash">sup</i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="py-5 text-center">
                    <i class="bi bi-clipboard-x fs-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">Aucun résultat trouvé pour cette sélection.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center shadow-sm d-inline-flex p-1 bg-white rounded-pill mx-auto">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
               <a class="page-link rounded-pill" href="?pages=evaluation&page_num=<?= $page-1 ?>&module=<?= $module_filter ?>&type=<?= $type_filter ?>">
                   <i class="bi bi-chevron-left"></i>
               </a>
            </li>

            <?php for($i=1;$i<=$totalPages;$i++): ?>
                <li class="page-item <?= $i==$page ? 'active' : '' ?>">
                    <a class="page-link rounded-pill" href="?pages=evaluation&page_num=<?= $i ?>&module=<?= $module_filter ?>&type=<?= $type_filter ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link rounded-pill" href="?pages=evaluation&page_num=<?= $page+1 ?>&module=<?= $module_filter ?>&type=<?= $type_filter ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

</div>