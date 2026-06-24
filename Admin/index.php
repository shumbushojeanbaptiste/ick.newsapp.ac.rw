<?php
require_once __DIR__ . '/config/articles.php';   // pulls in auth.php + visibility helper
require_login();

$pageTitle  = 'Dashboard';
$activeMenu = 'dashboard';

// ---- Dashboard stats (scoped to what THIS user is allowed to see) ----
[$visCount, $visParams] = article_visibility_clause('articles');   // no table alias
$countStmt = db()->prepare(
    "SELECT
        COUNT(*) AS total,
        SUM(status = 'published')   AS published,
        SUM(status = 'pending')     AS pending,
        SUM(status = 'draft')       AS draft,
        SUM(status = 'unpublished') AS unpublished
     FROM articles
     WHERE $visCount"
);
$countStmt->execute($visParams);
$counts = $countStmt->fetch();

$cards = [
    ['label' => 'Total Articles', 'value' => (int) $counts['total'],       'icon' => 'bx-news',          'tone' => 'primary'],
    ['label' => 'Published',      'value' => (int) $counts['published'],   'icon' => 'bx-check-circle',  'tone' => 'success'],
    ['label' => 'Pending Review', 'value' => (int) $counts['pending'],     'icon' => 'bx-time',          'tone' => 'warning'],
    ['label' => 'Drafts',         'value' => (int) $counts['draft'],       'icon' => 'bx-edit',          'tone' => 'info'],
];

// Recent articles (same visibility scope as the counters)
[$visRecent, $visRecentParams] = article_visibility_clause('a');   // alias 'a'
$recentStmt = db()->prepare(
    "SELECT a.id, a.slug, a.status, a.created_at,
            COALESCE(a.title_en, a.title_fr, a.title_rw, '(untitled)') AS title,
            c.name AS category, u.name AS author
       FROM articles a
       LEFT JOIN categories c ON c.id = a.category_id
       LEFT JOIN users u      ON u.id = a.author_id
      WHERE $visRecent
      ORDER BY a.created_at DESC
      LIMIT 8"
);
$recentStmt->execute($visRecentParams);
$recent = $recentStmt->fetchAll();

$statusBadge = [
    'draft'       => 'bg-info-subtle text-info',
    'pending'     => 'bg-warning-subtle text-warning',
    'published'   => 'bg-success-subtle text-success',
    'unpublished' => 'bg-danger-subtle text-danger',
];

require_once __DIR__ . '/header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col-12">
                    <h4 class="fs-16 mb-1">Good day, <?= e($me['name']) ?>!</h4>
                    <p class="text-muted mb-0">Here's what's happening across <?= e(APP_NAME) ?> today.</p>
                </div>
            </div>

            <!-- Stat cards -->
            <div class="row">
                <?php foreach ($cards as $card): ?>
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"><?= e($card['label']) ?></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-0"><?= (int) $card['value'] ?></h4>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-<?= e($card['tone']) ?>-subtle rounded fs-3">
                                            <i class="bx <?= e($card['icon']) ?> text-<?= e($card['tone']) ?>"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Recent articles -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Recent Articles</h4>
                            <?php if (can('view_articles')): ?>
                                <a href="articles/index" class="btn btn-soft-primary btn-sm">View all</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-centered align-middle table-nowrap mb-0">
                                    <thead class="text-muted table-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Author</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!$recent): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No articles yet.</td></tr>
                                        <?php else: foreach ($recent as $a): ?>
                                            <tr>
                                                <td>
                                                    <?php if (can('view_articles')): ?>
                                                        <a href="articles/edit?id=<?= (int) $a['id'] ?>" class="fw-medium link-primary"><?= e($a['title']) ?></a>
                                                    <?php else: ?>
                                                        <?= e($a['title']) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= e($a['category'] ?? '—') ?></td>
                                                <td><?= e($a['author'] ?? '—') ?></td>
                                                <td><span class="badge <?= $statusBadge[$a['status']] ?? '' ?> text-uppercase"><?= e($a['status']) ?></span></td>
                                                <td><?= e(date('d M Y', strtotime($a['created_at']))) ?></td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <script>document.write(new Date().getFullYear())</script> © <?= e(APP_NAME) ?>.
                </div>
            </div>
        </div>
    </footer>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
