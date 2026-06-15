<?php
require_once __DIR__ . '/../config/auth.php';
require_can('manage_radio');

$pageTitle  = 'Radio Streams';
$activeMenu = 'radio';
$basePath   = '../';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id     = (int) ($_POST['id'] ?? 0);
        $name   = trim($_POST['name'] ?? '');
        $url    = trim($_POST['stream_url'] ?? '');
        $active = !empty($_POST['is_active']) ? 1 : 0;

        if ($name === '' || $url === '') {
            set_flash('danger', 'Name and stream URL are required.');
        } else {
            if (!preg_match('~^https?://~i', $url)) {
                $url = 'https://' . $url;
            }
            if ($id > 0) {
                db()->prepare('UPDATE radio_streams SET name = ?, stream_url = ?, is_active = ? WHERE id = ?')
                    ->execute([$name, $url, $active, $id]);
                set_flash('success', 'Radio stream updated.');
            } else {
                db()->prepare('INSERT INTO radio_streams (name, stream_url, is_active) VALUES (?, ?, ?)')
                    ->execute([$name, $url, $active]);
                set_flash('success', 'Radio stream added.');
            }
        }
    } elseif ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        db()->prepare('UPDATE radio_streams SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
        set_flash('success', 'Status updated.');
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        db()->prepare('DELETE FROM radio_streams WHERE id = ?')->execute([$id]);
        set_flash('success', 'Radio stream deleted.');
    }
    redirect('radio/index.php');
}

$streams = db()->query(
    'SELECT rs.*, (SELECT COUNT(*) FROM articles a WHERE a.radio_stream_id = rs.id) AS used_count
       FROM radio_streams rs ORDER BY rs.name'
)->fetchAll();

require_once __DIR__ . '/../header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3 align-items-center">
                <div class="col">
                    <h4 class="mb-0">Radio Streams</h4>
                    <p class="text-muted mb-0">Embeddable live radio streams (e.g. Salus Radio).</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#radioModal" onclick="resetRadioForm()">
                        <i class="ri-add-line align-bottom me-1"></i> Add Stream
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-centered align-middle table-nowrap mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Name</th>
                                    <th>Stream URL</th>
                                    <th>Status</th>
                                    <th>Used by</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$streams): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No radio streams yet.</td></tr>
                                <?php else: foreach ($streams as $s): ?>
                                    <tr>
                                        <td class="fw-medium"><?= e($s['name']) ?></td>
                                        <td><a href="<?= e($s['stream_url']) ?>" target="_blank" rel="noopener" class="text-truncate d-inline-block" style="max-width:320px"><?= e($s['stream_url']) ?></a></td>
                                        <td>
                                            <?php if ($s['is_active']): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-light text-body"><?= (int) $s['used_count'] ?> articles</span></td>
                                        <td class="text-end">
                                            <form method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                                                <button class="btn btn-sm btn-soft-warning" title="Toggle active"><i class="ri-toggle-line"></i></button>
                                            </form>
                                            <button class="btn btn-sm btn-soft-secondary"
                                                onclick='editRadio(<?= htmlspecialchars(json_encode([
                                                    "id" => (int) $s["id"], "name" => $s["name"],
                                                    "stream_url" => $s["stream_url"], "is_active" => (int) $s["is_active"],
                                                ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>)'>
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-soft-danger"
                                                onclick="confirmDeleteRadio(<?= (int) $s['id'] ?>, <?= htmlspecialchars(json_encode($s['name']), ENT_QUOTES) ?>)">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
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

<!-- Add / Edit modal -->
<div class="modal fade" id="radioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="radio-id" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="radioModalTitle">Add Stream</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="radio-name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="radio-name" class="form-control" placeholder="e.g. Salus Radio" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="radio-url">Stream URL <span class="text-danger">*</span></label>
                        <input type="text" name="stream_url" id="radio-url" class="form-control" placeholder="https://..." required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="radio-active" value="1" checked>
                        <label class="form-check-label" for="radio-active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form method="post" id="deleteRadioForm" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="del-radio-id">
</form>

<?php
$pageScripts = <<<'HTML'
<script>
    function resetRadioForm() {
        document.getElementById('radio-id').value = 0;
        document.getElementById('radio-name').value = '';
        document.getElementById('radio-url').value = '';
        document.getElementById('radio-active').checked = true;
        document.getElementById('radioModalTitle').textContent = 'Add Stream';
    }
    function editRadio(s) {
        document.getElementById('radio-id').value = s.id;
        document.getElementById('radio-name').value = s.name || '';
        document.getElementById('radio-url').value = s.stream_url || '';
        document.getElementById('radio-active').checked = s.is_active == 1;
        document.getElementById('radioModalTitle').textContent = 'Edit Stream';
        new bootstrap.Modal(document.getElementById('radioModal')).show();
    }
    function confirmDeleteRadio(id, name) {
        Swal.fire({
            title: 'Delete stream?',
            html: '<b>' + name + '</b> will be removed. Articles using it keep no stream.',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#f06548', cancelButtonColor: '#74788d',
            confirmButtonText: 'Yes, delete it'
        }).then(function (r) {
            if (r.isConfirmed) {
                document.getElementById('del-radio-id').value = id;
                document.getElementById('deleteRadioForm').submit();
            }
        });
    }
</script>
HTML;

require_once __DIR__ . '/../footer.php';
?>
