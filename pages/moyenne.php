<style>
    :root { --glass-bg: rgba(255, 255, 255, 0.9); }
    body { background-color: #f0f2f5; }
    .page-title { font-weight: 800; color: #1a202c; letter-spacing: -0.5px; }
    .search-card { border: none; border-radius: 15px; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .table-container { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .table thead { background-color: #2d3748; color: #fff; }
    .table thead th { border: none; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding: 15px; }
    .moyenne-badge { width: 60px; font-weight: 700; border-radius: 8px; padding: 8px; display: inline-block; }
    .btn-download { border-radius: 10px; font-weight: 600; transition: all 0.3s; }
    .btn-download:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3); }
    .status-pass { background-color: #d1fae5; color: #065f46; } /* Vert */
    .status-warn { background-color: #fef3c7; color: #92400e; } /* Orange */
    .status-fail { background-color: #fee2e2; color: #991b1b; } /* Rouge */
</style>

<div class="container py-5">
    
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h2 class="page-title mb-1">Tableau d'Excellence</h2>
            <p class="text-muted"><i class="bi bi-info-circle me-1"></i> Visualisation des moyennes générales et édition des bulletins.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <?php if(!empty($listeMoyennes)): ?>
                <a href="telecharger_tous_bulletins.php?classe=<?= $classe ?>&niveau=<?= $niveau ?>&nom=<?= $nom ?>" 
                   class="btn btn-success btn-download px-4 py-2">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i> Télécharger tous les bulletins
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card search-card mb-4">
        <div class="card-body p-4">
            <form method="get" class="row g-3 align-items-end">
                <input type="hidden" name="pages" value="moyenne">
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-uppercase text-muted">Classe</label>
                    <select name="classe" class="form-select border-0 bg-light" onchange="this.form.submit()">
                        <option value="">Toutes les classes</option>
                        <?php foreach($listeClasses as $c): ?>
                            <option value="<?= $c['id_classe'] ?>" <?= $classe == $c['id_classe'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom_classe']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-uppercase text-muted">Niveau</label>
                    <select name="niveau" class="form-select border-0 bg-light" onchange="this.form.submit()">
                        <option value="">Tous les niveaux</option>
                        <?php foreach($listeNiveaux as $n): ?>
                            <option value="<?= htmlspecialchars($n['nom_niveau']) ?>" <?= $niveau == $n['nom_niveau'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($n['nom_niveau']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Rechercher un élève</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="nom" class="form-control border-0 bg-light" 
                               value="<?= htmlspecialchars($nom) ?>" placeholder="Nom ou prénom..." 
                               onkeyup="if(event.key==='Enter') this.form.submit()">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <a href="?pages=moyenne" class="btn btn-light w-100 border-0 fw-bold">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    

    <div class="table-container">
        <table class="table table-hover mb-0">
            <thead class="text-center">
                <tr>
                    <th>Étudiant</th>
                    <th>Parcours</th>
                    <th>Performance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="align-middle">
                <?php if(empty($listeMoyennes)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                            Aucun étudiant trouvé pour ces critères
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($listeMoyennes as $e): 
                        // Logique de couleur de la moyenne
                        $statusClass = 'status-pass';
                        if($e['moyenne'] < 10) $statusClass = 'status-fail';
                        elseif($e['moyenne'] < 12) $statusClass = 'status-warn';
                    ?>
                    <tr class="text-center">
                        <td class="text-start ps-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: 600;">
                                    <?= strtoupper(substr($e['nom'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($e['nom']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($e['prenom']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border fw-normal"><?= htmlspecialchars($e['nom_classe']) ?></span>
                            <div class="small text-muted mt-1"><?= htmlspecialchars($e['nom_niveau']) ?></div>
                        </td>
                        <td>
                            <div class="moyenne-badge <?= $statusClass ?>">
                                <?= number_format($e['moyenne'], 2) ?>
                            </div>
                        </td>
                        <td>
                            <a href="bulletin.php?id=<?= $e['id_etudiant'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3" target="_blank">
                                <i class="bi bi-file-pdf me-1"></i> Bulletin
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($totalPages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded" href="?pages=moyenne&page_num=<?= $page-1 ?>&classe=<?= $classe ?>&niveau=<?= $niveau ?>&nom=<?= $nom ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for($i=1;$i<=$totalPages;$i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded <?= ($i == $page) ? 'bg-primary text-white' : 'text-dark' ?>" 
                       href="?pages=moyenne&page_num=<?= $i ?>&classe=<?= $classe ?>&niveau=<?= $niveau ?>&nom=<?= $nom ?>">
                       <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded" href="?pages=moyenne&page_num=<?= $page+1 ?>&classe=<?= $classe ?>&niveau=<?= $niveau ?>&nom=<?= $nom ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>