<?php
require_once __DIR__ . '/../config/auth.php';
require_can('manage_users');

$pageTitle  = 'Users';
$activeMenu = 'users';
$basePath   = '../';

$roles = db()->query('SELECT id, name, label FROM roles ORDER BY id')->fetchAll();
$roleById = [];
foreach ($roles as $r) {
    $roleById[(int) $r['id']] = $r;
}

/** Count active admins (to protect the last one). */
function active_admin_count(): int
{
    return (int) db()->query(
        "SELECT COUNT(*) FROM users u JOIN roles r ON r.id = u.role_id
          WHERE r.name = 'admin' AND u.is_active = 1"
    )->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $myId   = (int) current_user()['id'];

    if ($action === 'save') {
        $id      = (int) ($_POST['id'] ?? 0);
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $roleId  = (int) ($_POST['role_id'] ?? 0);
        $active  = !empty($_POST['is_active']) ? 1 : 0;
        $pass    = $_POST['password'] ?? '';

        if ($name === '' || $email === '' || !isset($roleById[$roleId])) {
            set_flash('danger', 'Name, email and a valid role are required.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_flash('danger', 'Please enter a valid email address.');
        } else {
            // Email uniqueness
            $chk = db()->prepare('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
            $chk->execute([$email, $id]);
            if ($chk->fetch()) {
                set_flash('danger', 'That email is already used by another account.');
                redirect('users/index.php');
            }

            if ($id > 0) {
                // Guard: don't let the last active admin be demoted/deactivated.
                $target = db()->prepare("SELECT u.is_active, r.name AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?");
                $target->execute([$id]);
                $cur = $target->fetch();
                $wasActiveAdmin = $cur && $cur['role'] === 'admin' && $cur['is_active'];
                $becomesAdmin   = $roleById[$roleId]['name'] === 'admin' && $active;
                if ($wasActiveAdmin && !$becomesAdmin && active_admin_count() <= 1) {
                    set_flash('danger', 'You cannot demote or deactivate the last active admin.');
                    redirect('users/index.php');
                }

                if ($pass !== '') {
                    if (strlen($pass) < 6) {
                        set_flash('danger', 'Password must be at least 6 characters.');
                        redirect('users/index.php');
                    }
                    db()->prepare('UPDATE users SET name=?, email=?, role_id=?, is_active=?, password=? WHERE id=?')
                        ->execute([$name, $email, $roleId, $active, password_hash($pass, PASSWORD_DEFAULT), $id]);
                } else {
                    db()->prepare('UPDATE users SET name=?, email=?, role_id=?, is_active=? WHERE id=?')
                        ->execute([$name, $email, $roleId, $active, $id]);
                }
                if ($id === $myId) {
                    refresh_current_user();
                }
                set_flash('success', 'User updated.');
            } else {
                if (strlen($pass) < 6) {
                    set_flash('danger', 'New users need a password of at least 6 characters.');
                    redirect('users/index.php');
                }
                db()->prepare('INSERT INTO users (name, email, role_id, is_active, password) VALUES (?,?,?,?,?)')
                    ->execute([$name, $email, $roleId, $active, password_hash($pass, PASSWORD_DEFAULT)]);
                set_flash('success', 'User created.');
            }
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === $myId) {
            set_flash('danger', 'You cannot delete your own account.');
        } else {
            $target = db()->prepare("SELECT r.name AS role, u.is_active FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?");
            $target->execute([$id]);
            $cur = $target->fetch();
            if ($cur && $cur['role'] === 'admin' && $cur['is_active'] && active_admin_count() <= 1) {
                set_flash('danger', 'You cannot delete the last active admin.');
            } else {
                // Reassign this user's articles to the current admin to satisfy the FK.
                db()->prepare('UPDATE articles SET author_id = ? WHERE author_id = ?')->execute([$myId, $id]);
                db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
                set_flash('success', 'User deleted (their articles reassigned to you).');
            }
        }
    }
    redirect('users/index.php');
}

$users = db()->query(
    'SELECT u.*, r.label AS role_label, r.name AS role_name,
            (SELECT COUNT(*) FROM articles a WHERE a.author_id = u.id) AS article_count
       FROM users u JOIN roles r ON r.id = u.role_id
      ORDER BY u.created_at DESC'
)->fetchAll();

require_once __DIR__ . '/../header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3 align-items-center">
                <div class="col">
                    <h4 class="mb-0">Users</h4>
                    <p class="text-muted mb-0">Manage staff accounts and their roles.</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetUserForm()">
                        <i class="ri-user-add-line align-bottom me-1"></i> Add User
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
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Articles</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td class="fw-medium">
                                            <?= e($u['name']) ?>
                                            <?php if ((int) $u['id'] === (int) current_user()['id']): ?>
                                                <span class="badge bg-primary-subtle text-primary ms-1">You</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= e($u['email']) ?></td>
                                        <td><span class="badge bg-info-subtle text-info"><?= e($u['role_label']) ?></span></td>
                                        <td>
                                            <?php if ($u['is_active']): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-light text-body"><?= (int) $u['article_count'] ?></span></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-soft-secondary"
                                                onclick='editUser(<?= htmlspecialchars(json_encode([
                                                    "id" => (int) $u["id"], "name" => $u["name"], "email" => $u["email"],
                                                    "role_id" => (int) $u["role_id"], "is_active" => (int) $u["is_active"],
                                                ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>)'>
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <?php if ((int) $u['id'] !== (int) current_user()['id']): ?>
                                                <button class="btn btn-sm btn-soft-danger"
                                                    onclick="confirmDeleteUser(<?= (int) $u['id'] ?>, <?= htmlspecialchars(json_encode($u['name']), ENT_QUOTES) ?>)">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="user-id" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="user-name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="user-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="user-email">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="user-email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="user-role">Role <span class="text-danger">*</span></label>
                        <select name="role_id" id="user-role" class="form-select" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= (int) $r['id'] ?>"><?= e($r['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="user-pass">Password <span id="pass-hint" class="text-danger">*</span></label>
                        <input type="password" name="password" id="user-pass" class="form-control" minlength="6">
                        <small class="text-muted" id="pass-edit-hint" style="display:none">Leave blank to keep the current password.</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="user-active" value="1" checked>
                        <label class="form-check-label" for="user-active">Active</label>
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

<form method="post" id="deleteUserForm" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="del-user-id">
</form>

<?php
$pageScripts = <<<'HTML'
<script>
    function resetUserForm() {
        document.getElementById('user-id').value = 0;
        document.getElementById('user-name').value = '';
        document.getElementById('user-email').value = '';
        document.getElementById('user-role').selectedIndex = 0;
        document.getElementById('user-pass').value = '';
        document.getElementById('user-active').checked = true;
        document.getElementById('userModalTitle').textContent = 'Add User';
        document.getElementById('pass-hint').style.display = '';
        document.getElementById('pass-edit-hint').style.display = 'none';
        document.getElementById('user-pass').required = true;
    }
    function editUser(u) {
        document.getElementById('user-id').value = u.id;
        document.getElementById('user-name').value = u.name || '';
        document.getElementById('user-email').value = u.email || '';
        document.getElementById('user-role').value = u.role_id;
        document.getElementById('user-pass').value = '';
        document.getElementById('user-active').checked = u.is_active == 1;
        document.getElementById('userModalTitle').textContent = 'Edit User';
        document.getElementById('pass-hint').style.display = 'none';
        document.getElementById('pass-edit-hint').style.display = '';
        document.getElementById('user-pass').required = false;
        new bootstrap.Modal(document.getElementById('userModal')).show();
    }
    function confirmDeleteUser(id, name) {
        Swal.fire({
            title: 'Delete user?',
            html: '<b>' + name + '</b> will be removed. Their articles are reassigned to you.',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#f06548', cancelButtonColor: '#74788d',
            confirmButtonText: 'Yes, delete user'
        }).then(function (r) {
            if (r.isConfirmed) {
                document.getElementById('del-user-id').value = id;
                document.getElementById('deleteUserForm').submit();
            }
        });
    }
</script>
HTML;

require_once __DIR__ . '/../footer.php';
?>
