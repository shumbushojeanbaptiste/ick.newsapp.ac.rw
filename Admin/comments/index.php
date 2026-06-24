<?php
/**
 * UR KAMINUZA - Reader comment moderation.
 * Lists reader comments by status; publishers approve / reject / delete them.
 * Only approved comments are shown on the public article page.
 */
require_once __DIR__ . '/../config/auth.php';
require_can('publish');   // editors / publishers moderate comments

$pageTitle  = 'Comments';
$activeMenu = 'comments';
$basePath   = '../';

// ---- Actions (approve / reject / delete) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);

    if ($id > 0) {
        if ($action === 'approve') {
            db()->prepare("UPDATE article_comments SET status='approved' WHERE id=?")->execute([$id]);
            set_flash('success', 'Comment approved and is now visible on the site.');
        } elseif ($action === 'reject') {
            db()->prepare("UPDATE article_comments SET status='rejected' WHERE id=?")->execute([$id]);
            set_flash('success', 'Comment rejected (hidden from the site).');
        } elseif ($action === 'delete') {
            db()->prepare('DELETE FROM article_comments WHERE id=?')->execute([$id]);
            set_flash('success', 'Comment deleted.');
        }
    }
    redirect('comments/index.php' . (isset($_POST['filter']) ? '?status=' . urlencode($_POST['filter']) : ''));
}

// ---- Filter by status (default: pending = what needs attention) ----
$status = $_GET['status'] ?? 'pending';
if (!in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) {
    $status = 'pending';
}

$where  = $status === 'all' ? '' : 'WHERE c.status = ?';
$params = $status === 'all' ? [] : [$status];

$sql = "SELECT c.*, a.slug AS article_slug,
               COALESCE(a.title_en, a.title_fr, a.title_rw) AS article_title
          FROM article_comments c
          JOIN articles a ON a.id = c.article_id
          $where
         ORDER BY c.created_at DESC";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$comments = $stmt->fetchAll();

// Counts per status for the filter tabs.
$counts = db()->query(
    "SELECT
        SUM(status='pending')  AS pending,
        SUM(status='approved') AS approved,
        SUM(status='rejected') AS rejected,
        COUNT(*)               AS all_c
     FROM article_comments"
)->fetch() ?: ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'all_c' => 0];

require_once __DIR__ . '/../header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3 align-items-center">
                <div class="col">
                    <h4 class="mb-0">Reader Comments</h4>
                    <p class="text-muted mb-0">Approve comments to show them publicly. Pending comments are hidden from the site.</p>
                </div>
            </div>

            <!-- Status filter tabs -->
            <ul class="nav nav-pills mb-3">
                <?php
                $tabs = [
                    'pending'  => ['Pending',  (int) $counts['pending'],  'warning'],
                    'approved' => ['Approved', (int) $counts['approved'], 'success'],
                    'rejected' => ['Rejected', (int) $counts['rejected'], 'danger'],
                    'all'      => ['All',      (int) $counts['all_c'],    'secondary'],
                ];
                foreach ($tabs as $key => [$label, $n, $color]): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $status === $key ? 'active' : '' ?>" href="comments/index?status=<?= $key ?>">
                            <?= $label ?> <span class="badge bg-<?= $color ?>-subtle text-<?= $color ?> ms-1"><?= $n ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="card">
                <div class="card-body">
                    <?php if (!$comments): ?>
                        <p class="text-center text-muted py-4 mb-0">No <?= $status === 'all' ? '' : e($status) ?> comments.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-centered align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th>Reader</th>
                                        <th>Comment</th>
                                        <th>Article</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($comments as $c): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-medium"><?= e($c['author_name']) ?></div>
                                            <?php if ($c['author_email']): ?>
                                                <small class="text-muted"><?= e($c['author_email']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td style="max-width:380px;min-width:240px;">
                                            <?php $body = (string) $c['body']; $long = mb_strlen($body) > 160; ?>
                                            <?php if ($long): ?>
                                                <div class="kz-cmt-clip"><?= nl2br(e($body)) ?></div>
                                                <a href="#" class="kz-cmt-toggle small" data-more="Read more" data-less="Show less">Read more</a>
                                            <?php else: ?>
                                                <div><?= nl2br(e($body)) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="../public/article?slug=<?= e($c['article_slug']) ?>"
                                               target="_blank" class="text-decoration-none">
                                                <?= e(mb_strimwidth($c['article_title'] ?? '(untitled)', 0, 40, '…')) ?>
                                            </a>
                                        </td>
                                        <td><small class="text-muted"><?= e(date('M j, Y H:i', strtotime($c['created_at']))) ?></small></td>
                                        <td>
                                            <?php
                                            $sc = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][$c['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?> text-uppercase"><?= e($c['status']) ?></span>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <?php if ($c['status'] !== 'approved'): ?>
                                            <form method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="approve">
                                                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                                <input type="hidden" name="filter" value="<?= e($status) ?>">
                                                <button class="btn btn-sm btn-soft-success" title="Approve"><i class="ri-check-line"></i></button>
                                            </form>
                                            <?php endif; ?>
                                            <?php if ($c['status'] !== 'rejected'): ?>
                                            <form method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="reject">
                                                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                                <input type="hidden" name="filter" value="<?= e($status) ?>">
                                                <button class="btn btn-sm btn-soft-warning" title="Reject"><i class="ri-close-line"></i></button>
                                            </form>
                                            <?php endif; ?>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this comment permanently?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                                <input type="hidden" name="filter" value="<?= e($status) ?>">
                                                <button class="btn btn-sm btn-soft-danger" title="Delete"><i class="ri-delete-bin-line"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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

<style>
    /* Long comments: clamp to a few lines until "Read more" is clicked. */
    .kz-cmt-clip {
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
        overflow: hidden; line-height: 1.5;
    }
    .kz-cmt-clip.kz-cmt-open { -webkit-line-clamp: unset; overflow: visible; }
    .kz-cmt-toggle { cursor: pointer; }
</style>
<script>
    document.addEventListener('click', function (e) {
        var t = e.target.closest('.kz-cmt-toggle');
        if (!t) { return; }
        e.preventDefault();
        var clip = t.previousElementSibling;
        var open = clip.classList.toggle('kz-cmt-open');
        t.textContent = open ? t.getAttribute('data-less') : t.getAttribute('data-more');
    });
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>
