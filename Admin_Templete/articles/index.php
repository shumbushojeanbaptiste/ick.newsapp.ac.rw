<?php
require_once __DIR__ . '/../config/articles.php';
require_can('view_articles');

$pageTitle  = 'Articles';
$activeMenu = 'articles';
$basePath   = '../';

// ---- Filters ----
$fStatus = $_GET['status']   ?? '';
$fCat    = (int) ($_GET['category'] ?? 0);
$fLang   = $_GET['lang']     ?? '';
$fSearch = trim($_GET['q']   ?? '');

$where  = [];
$params = [];

if (in_array($fStatus, ARTICLE_STATUSES, true)) {
    $where[] = 'a.status = ?';
    $params[] = $fStatus;
}
if ($fCat > 0) {
    $where[] = 'a.category_id = ?';
    $params[] = $fCat;
}
if (in_array($fLang, ['rw', 'fr', 'en'], true)) {
    // Articles that HAVE this language (title present).
    $where[] = "a.title_$fLang IS NOT NULL AND a.title_$fLang <> ''";
}
if ($fSearch !== '') {
    $where[] = '(a.title_en LIKE ? OR a.title_fr LIKE ? OR a.title_rw LIKE ?)';
    $like = '%' . $fSearch . '%';
    array_push($params, $like, $like, $like);
}

// Contributors/reporters who lack edit_any only see their own by default,
// but we still show all in the list (read view). Workflow actions are gated.
$sql = "SELECT a.*, c.name AS category_name, u.name AS author_name
          FROM articles a
          LEFT JOIN categories c ON c.id = a.category_id
          LEFT JOIN users u      ON u.id = a.author_id";
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY a.updated_at DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

$categories = db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

require_once __DIR__ . '/../header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3 align-items-center">
                <div class="col">
                    <h4 class="mb-0">Articles</h4>
                    <p class="text-muted mb-0">Manage news content across all languages.</p>
                </div>
                <?php if (can('create_article')): ?>
                <div class="col-auto">
                    <a href="articles/edit.php" class="btn btn-primary">
                        <i class="ri-add-line align-bottom me-1"></i> New Article
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Filters -->
            <div class="card">
                <div class="card-body">
                    <form method="get" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label mb-1">Search</label>
                            <input type="text" name="q" class="form-control" placeholder="Title..." value="<?= e($fSearch) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                <?php foreach (ARTICLE_STATUSES as $s): ?>
                                    <option value="<?= $s ?>" <?= $fStatus === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1">Category</label>
                            <select name="category" class="form-select">
                                <option value="0">All categories</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= (int) $c['id'] ?>" <?= $fCat === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Language</label>
                            <select name="lang" class="form-select">
                                <option value="">Any</option>
                                <option value="en" <?= $fLang === 'en' ? 'selected' : '' ?>>English</option>
                                <option value="fr" <?= $fLang === 'fr' ? 'selected' : '' ?>>French</option>
                                <option value="rw" <?= $fLang === 'rw' ? 'selected' : '' ?>>Kinyarwanda</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn-primary"><i class="ri-filter-2-line"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-centered align-middle table-nowrap mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Languages</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Updated</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$articles): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">No articles found.</td></tr>
                                <?php else: foreach ($articles as $a):
                                    $actions = article_actions($a); ?>
                                    <tr>
                                        <td class="fw-medium" style="white-space:normal;max-width:320px;">
                                            <?php if (($a['media_type'] ?? 'none') === 'video'): ?>
                                                <i class="ri-film-line text-danger me-1" title="Video"></i>
                                            <?php elseif (($a['media_type'] ?? 'none') === 'audio'): ?>
                                                <i class="ri-mic-line text-info me-1" title="Audio"></i>
                                            <?php endif; ?>
                                            <a href="articles/view.php?id=<?= (int) $a['id'] ?>" class="link-primary text-reset"><?= e(article_title($a)) ?></a>
                                            <span class="badge bg-light text-muted ms-1"><?= e(strtoupper($a['primary_lang'])) ?></span>
                                        </td>
                                        <td><?= e($a['category_name'] ?? '—') ?></td>
                                        <td>
                                            <?php foreach (article_langs($a) as $l): ?>
                                                <span class="badge bg-info-subtle text-info text-uppercase"><?= e($l) ?></span>
                                            <?php endforeach; ?>
                                        </td>
                                        <td><?= e($a['author_name'] ?? '—') ?></td>
                                        <td><span class="badge <?= status_badge_class($a['status']) ?> text-uppercase"><?= e($a['status']) ?></span></td>
                                        <td><?= e(date('d M Y', strtotime($a['updated_at']))) ?></td>
                                        <td class="text-end">
                                            <a href="articles/view.php?id=<?= (int) $a['id'] ?>" class="btn btn-sm btn-soft-info" title="Read"><i class="ri-eye-line"></i></a>
                                            <?php if (in_array('edit', $actions, true)): ?>
                                                <a href="articles/edit.php?id=<?= (int) $a['id'] ?>" class="btn btn-sm btn-soft-secondary" title="Edit"><i class="ri-pencil-line"></i></a>
                                            <?php endif; ?>
                                            <?php if (in_array('submit', $actions, true)): ?>
                                                <button class="btn btn-sm btn-soft-warning" title="Submit for review"
                                                        onclick="workflow(<?= (int) $a['id'] ?>,'submit','Submit this article for review?')"><i class="ri-send-plane-line"></i></button>
                                            <?php endif; ?>
                                            <?php if (in_array('publish', $actions, true)): ?>
                                                <button class="btn btn-sm btn-soft-success" title="Publish"
                                                        onclick="workflow(<?= (int) $a['id'] ?>,'publish','Publish this article now?')"><i class="ri-check-line"></i></button>
                                            <?php endif; ?>
                                            <?php if (in_array('unpublish', $actions, true)): ?>
                                                <button class="btn btn-sm btn-soft-dark" title="Unpublish"
                                                        onclick="workflow(<?= (int) $a['id'] ?>,'unpublish','Take this article offline?')"><i class="ri-eye-off-line"></i></button>
                                            <?php endif; ?>
                                            <?php if (in_array('delete', $actions, true)): ?>
                                                <button class="btn btn-sm btn-soft-danger" title="Delete"
                                                        onclick="confirmDelete(<?= (int) $a['id'] ?>, <?= htmlspecialchars(json_encode(article_title($a)), ENT_QUOTES) ?>)"><i class="ri-delete-bin-line"></i></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row"><div class="col-sm-6"><script>document.write(new Date().getFullYear())</script> © <?= e(APP_NAME) ?>.</div></div>
        </div>
    </footer>
</div>

<!-- Hidden workflow form -->
<form method="post" id="workflowForm" action="articles/action.php" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="action" id="wf-action">
    <input type="hidden" name="id" id="wf-id">
</form>

<?php
$pageScripts = <<<'HTML'
<script>
    function submitWorkflow(id, action) {
        document.getElementById('wf-id').value = id;
        document.getElementById('wf-action').value = action;
        document.getElementById('workflowForm').submit();
    }
    function workflow(id, action, message) {
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#405189',
            cancelButtonColor: '#74788d',
            confirmButtonText: 'Yes, continue'
        }).then(function (r) { if (r.isConfirmed) submitWorkflow(id, action); });
    }
    function confirmDelete(id, title) {
        Swal.fire({
            title: 'Delete article?',
            html: '<b>' + title + '</b> will be permanently removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f06548',
            cancelButtonColor: '#74788d',
            confirmButtonText: 'Yes, delete it'
        }).then(function (r) { if (r.isConfirmed) submitWorkflow(id, 'delete'); });
    }
</script>
HTML;

require_once __DIR__ . '/../footer.php';
?>
