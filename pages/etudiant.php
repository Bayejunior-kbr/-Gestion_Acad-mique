<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root { --primary-color: #4e73df; --warning-color: #f6c23e; }
    body { background-color: #f8f9fc; color: #333; }
    .card { border: none; border-radius: 12px; }
    .btn-rounded { border-radius: 50px; }
    .form-control, .form-select { border-radius: 8px; border: 1px solid #e3e6f0; }
    .table thead th { background-color: #f8f9fc; text-transform: uppercase; font-size: 0.75rem; color: #858796; letter-spacing: 0.5px; border: none; }
    .table tbody td { border-bottom: 1px solid #f1f1f1; vertical-align: middle; }
    .badge-id { font-family: 'Courier New', Courier, monospace; letter-spacing: 1px; }
    .pagination .page-link { border: none; margin: 0 3px; border-radius: 8px; color: var(--primary-color); shadow: none; }
    .pagination .page-item.active .page-link { background-color: var(--primary-color); border: none; }
</style>

<div class="container py-4">

    <?php if (isset($_GET['msg'])) : ?>
        <div class="mb-4">
            <?php if ($_GET['msg'] == 'existe') : ?>
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i> Ce Matricule existe déjà.</div>
            <?php elseif ($_GET['msg'] == 'impossible') : ?>
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center"><i class="bi bi-x-octagon-fill me-2"></i> Impossible de supprimer : l'étudiant possède des évaluations.</div>
            <?php elseif ($_GET['msg'] == 'supprime') : ?>
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i> L'étudiant a été supprimé avec succès.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-primary mb-0">Registre des Étudiants</h2>
            <p class="text-muted small">Total : <span class="fw-bold text-dark"><?= $totalEtudiants ?></span> inscrits</p>
        </div>
        <div class="bg-white p-2 px-3 rounded-pill shadow-sm border small fw-medium">
            <i class="bi bi-mortarboard text-primary me-1"></i> Portail Académique
        </div>
    </div>

    <div class="card shadow-sm mb-5 border-top border-4 <?= $editEtudiant ? 'border-warning' : 'border-success' ?>">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="bi <?= $editEtudiant ? 'bi-pencil-square text-warning' : 'bi-person-plus text-success' ?> me-2"></i>
                <?= $editEtudiant ? 'Mettre à jour le dossier' : 'Inscrire un nouvel étudiant' ?>
            </h5>
            <form method="post" action="traitement/actions.php" class="row g-3">
                <?php if ($editEtudiant): ?>
                    <input type="hidden" name="idEtudiant" value="<?= $editEtudiant['id_etudiant'] ?>">
                <?php endif; ?>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">MATRICULE</label>
                    <input type="text" name="matricule" class="form-control bg-light fw-bold" value="<?= $editEtudiant['matricule'] ?? '' ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">NOM</label>
                    <input type="text" id="nom" name="nom" class="form-control" required value="<?= $editEtudiant['nom'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">PRÉNOM</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" required value="<?= $editEtudiant['prenom'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">AFFECTATION</label>
                    <select name="id_classe" class="form-select" required>
                        <option value="">-- Classe --</option>
                        <?php foreach ($listeClasses as $classe): ?>
                            <option value="<?= $classe['id_classe'] ?>" <?= ($editEtudiant && $editEtudiant['id_classe'] == $classe['id_classe']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($classe['nom_classe']) ?> (<?= htmlspecialchars($classe['nom_niveau']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 text-end mt-4">
                    <?php if ($editEtudiant): ?>
                        <a href="?pages=etudiant" class="btn btn-light btn-rounded px-4 me-2">Annuler</a>
                        <button type="submit" name="modifier_etudiant" class="btn btn-warning btn-rounded px-4 fw-bold">Sauvegarder les modifications</button>
                    <?php else: ?>
                        <button type="submit" name="ajouter_etudiant" class="btn btn-success btn-rounded px-5 fw-bold shadow-sm">Valider l'inscription</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white p-3 rounded-4 shadow-sm mb-4 border border-light">
        <form method="GET" class="row g-3 align-items-center">
            <input type="hidden" name="pages" value="etudiant">
            <div class="col-md-auto text-muted small fw-bold me-2"><i class="bi bi-funnel me-1"></i> FILTRER PAR :</div>
            <div class="col-md-4">
                <select name="niveau_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tous les niveaux</option>
                    <?php foreach(getNiveaux() as $niveau): ?>
                        <option value="<?= $niveau['id_niveau'] ?>" <?= ($niveau_filter == $niveau['id_niveau']) ? 'selected' : '' ?>><?= $niveau['nom_niveau'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="classe_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    <?php foreach(getClasse() as $classe): ?>
                        <option value="<?= $classe['id_classe'] ?>" <?= ($classe_filter == $classe['id_classe']) ? 'selected' : '' ?>><?= $classe['nom_classe'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <div class="card shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <?php if (!empty($listeEtudiant)) : ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Matricule</th>
                                <th>Identité de l'Étudiant</th>
                                <th>Classe / Niveau</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listeEtudiant as $i => $etudiant) : ?>
                                <tr>
                                    <td class="ps-4 text-muted small"><?= $i + 1 ?></td>
                                    <td><span class="badge bg-light text-primary border border-primary-subtle badge-id"><?= htmlspecialchars($etudiant['matricule']) ?></span></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($etudiant['nom']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($etudiant['prenom']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark"><?= htmlspecialchars($etudiant['nom_classe']) ?></div>
                                        <div class="badge bg-info-subtle text-info small" style="font-size: 0.7rem;"><?= htmlspecialchars($etudiant['nom_niveau']) ?></div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-sm rounded-3">
                                            <a title="Modifier" href="?pages=etudiant&idEtudiant=<?= $etudiant['id_etudiant'] ?>" class="btn btn-white btn-sm border-end"><i class="bi bi-pencil text-primary"></i></a>
                                            <button title="Notes" type="button" class="btn btn-white btn-sm border-end" data-bs-toggle="modal" data-bs-target="#modalNotes<?= $etudiant['id_etudiant'] ?>"><i class="bi bi-journal-text text-success"></i></button>
                                            <a title="Supprimer" onclick="return confirm('Supprimer cet étudiant ?')" href="traitement/actions.php?idEtudiant=<?= $etudiant['id_etudiant'] ?>&action=supprimer_etudiant" class="btn btn-white btn-sm"><i class="bi bi-trash text-danger"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <nav class="p-4 border-top">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link shadow-none" href="?pages=etudiant&page_num=<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
                        </li>
                        <?php for($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link shadow-none" href="?pages=etudiant&page_num=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link shadow-none" href="?pages=etudiant&page_num=<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-2">Aucun étudiant trouvé dans cette sélection.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php foreach ($listeEtudiant as $etudiant) : ?>
        <div class="modal fade" id="modalNotes<?= $etudiant['id_etudiant'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-dark text-white p-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-file-earmark-bar-graph me-2 text-success"></i>
                            Relevé de <?= htmlspecialchars($etudiant['prenom']) ?> <?= htmlspecialchars($etudiant['nom']) ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <?php $evaluations = getEvaluationsByEtudiant($etudiant['id_etudiant']); ?>
                        <?php if(!empty($evaluations)) : ?>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Module / Code</th>
                                            <th>Type</th>
                                            <th class="text-center">Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($evaluations as $eval): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($eval['nom_module']) ?></div>
                                                    <code class="text-muted small"><?= htmlspecialchars($eval['code_module']) ?></code>
                                                </td>
                                                <td><span class="text-muted small text-uppercase"><?= htmlspecialchars($eval['type_evaluation']) ?></span></td>
                                                <td class="text-center fw-bold text-primary fs-5"><?= htmlspecialchars($eval['note']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info border-0 rounded-4 text-center">Aucune note enregistrée pour cet étudiant.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const nomInput = document.getElementById("nom");
    const prenomInput = document.getElementById("prenom");

    function validateName(input) {
        const value = input.value.trim();
        const regex = /^[A-Za-zÀ-ÿ\s]+$/;

        if (value.length < 2) {
            input.setCustomValidity("Minimum 2 lettres requises.");
        } 
        else if (!regex.test(value)) {
            input.setCustomValidity("Les chiffres ne sont pas autorisés.");
        } 
        else {
            input.setCustomValidity("");
        }
    }

    nomInput.addEventListener("input", function() {
        validateName(nomInput);
    });

    prenomInput.addEventListener("input", function() {
        validateName(prenomInput);
    });

});
</script>