<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/image.php';
require_login();

$pageTitle  = 'My Profile';
$activeMenu = '';

$me = current_user();

// ---- Handle POST actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    // 1) Update name / email
    if ($action === 'update_info') {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            set_flash('danger', 'Name and email are required.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_flash('danger', 'Please enter a valid email address.');
        } else {
            // Ensure the email isn't taken by someone else.
            $chk = db()->prepare('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
            $chk->execute([$email, $me['id']]);
            if ($chk->fetch()) {
                set_flash('danger', 'That email is already used by another account.');
            } else {
                db()->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?')
                    ->execute([$name, $email, $me['id']]);
                refresh_current_user();
                set_flash('success', 'Profile updated successfully.');
            }
        }
        redirect('profile.php');
    }

    // 2) Change password
    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $row = db()->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
        $row->execute([$me['id']]);
        $hash = $row->fetchColumn();

        if (!password_verify($current, $hash ?: '')) {
            set_flash('danger', 'Your current password is incorrect.');
        } elseif (strlen($new) < 6) {
            set_flash('danger', 'New password must be at least 6 characters.');
        } elseif ($new !== $confirm) {
            set_flash('danger', 'New password and confirmation do not match.');
        } else {
            db()->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([password_hash($new, PASSWORD_DEFAULT), $me['id']]);
            set_flash('success', 'Password changed successfully.');
        }
        redirect('profile.php');
    }

    // 3) Update avatar (base64 data URL from Cropper.js)
    if ($action === 'update_avatar') {
        $dataUrl = $_POST['cropped_image'] ?? '';
        try {
            $newPath = save_avatar_from_data_url((int) $me['id'], $dataUrl);
            // Remove the old file, then store the new path.
            delete_avatar_file($me['avatar'] ?? null);
            db()->prepare('UPDATE users SET avatar = ? WHERE id = ?')
                ->execute([$newPath, $me['id']]);
            refresh_current_user();
            set_flash('success', 'Profile photo updated.');
        } catch (RuntimeException $ex) {
            set_flash('danger', $ex->getMessage());
        }
        redirect('profile.php');
    }
}

$pageStyles = '<link href="assets/libs/cropperjs/cropper.min.css" rel="stylesheet" type="text/css">';

require_once __DIR__ . '/header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col">
                    <h4 class="mb-0">My Profile</h4>
                    <p class="text-muted mb-0">Manage your account information, photo and password.</p>
                </div>
            </div>

            <div class="row">
                <!-- Avatar card -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-3 text-start">Profile Photo</h5>
                            <div class="profile-user position-relative d-inline-block mx-auto mb-3">
                                <img src="<?= e(avatar_url($me)) ?>" id="avatar-preview"
                                     class="rounded-circle avatar-xl img-thumbnail object-fit-cover"
                                     alt="avatar" width="160" height="160">
                            </div>
                            <div>
                                <label for="avatar-input" class="btn btn-soft-primary btn-sm">
                                    <i class="ri-image-edit-line align-bottom me-1"></i> Change Photo
                                </label>
                                <input type="file" id="avatar-input" class="d-none" accept="image/png,image/jpeg,image/webp">
                            </div>
                            <p class="text-muted fs-13 mt-3 mb-0">
                                Recommended: a square image, at least <b>400×400px</b>.<br>
                                JPG, PNG or WebP — max <b>2&nbsp;MB</b>. You can crop it before saving.
                            </p>
                            <div class="mt-2">
                                <span class="badge bg-info-subtle text-info text-uppercase"><?= e($me['role_label']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info + password -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Account Information</h5>
                            <form method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update_info">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="name">Full Name</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                               value="<?= e($me['name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                               value="<?= e($me['email']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control" value="<?= e($me['role_label']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Change Password</h5>
                            <form method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="change_password">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label" for="current_password">Current Password</label>
                                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="new_password">New Password</label>
                                        <input type="password" name="new_password" id="new_password" class="form-control" minlength="6" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="confirm_password">Confirm New Password</label>
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" minlength="6" required>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                </div>
                            </form>
                        </div>
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

<!-- Crop modal -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop your photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-muted fs-13 mb-2">Drag to reposition, scroll or pinch to zoom. The square area becomes your 400×400 avatar.</div>
                <div style="max-height:360px;">
                    <img id="crop-image" alt="Image to crop" style="max-width:100%; display:block;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop-save">Save Photo</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form that posts the cropped image -->
<form method="post" id="avatarForm" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="update_avatar">
    <input type="hidden" name="cropped_image" id="cropped_image">
</form>

<?php
$pageScripts = <<<'HTML'
<script src="assets/libs/cropperjs/cropper.min.js"></script>
<script>
(function () {
    const MAX_BYTES = 2 * 1024 * 1024; // 2 MB
    const OUT_SIZE  = 400;
    const input     = document.getElementById('avatar-input');
    const image     = document.getElementById('crop-image');
    const modalEl   = document.getElementById('cropModal');
    const modal     = new bootstrap.Modal(modalEl);
    let cropper = null;

    input.addEventListener('change', function (e) {
        const file = e.target.files && e.target.files[0];
        if (!file) return;

        if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
            Swal.fire({ icon: 'error', title: 'Unsupported file', text: 'Please choose a JPG, PNG or WebP image.' });
            input.value = '';
            return;
        }
        if (file.size > MAX_BYTES) {
            Swal.fire({ icon: 'error', title: 'Too large', text: 'The image must be 2 MB or smaller.' });
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (ev) {
            image.src = ev.target.result;
            modal.show();
        };
        reader.readAsDataURL(file);
    });

    // Initialise Cropper once the modal is fully shown (so sizing is correct).
    modalEl.addEventListener('shown.bs.modal', function () {
        if (cropper) cropper.destroy();
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
            background: false,
            responsive: true
        });
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        if (cropper) { cropper.destroy(); cropper = null; }
        input.value = '';
    });

    document.getElementById('crop-save').addEventListener('click', function () {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({
            width: OUT_SIZE, height: OUT_SIZE,
            imageSmoothingQuality: 'high'
        });
        // Preview immediately, then submit as PNG data URL.
        const dataUrl = canvas.toDataURL('image/png');
        document.getElementById('avatar-preview').src = dataUrl;
        document.getElementById('cropped_image').value = dataUrl;
        modal.hide();
        document.getElementById('avatarForm').submit();
    });
})();
</script>
HTML;

require_once __DIR__ . '/footer.php';
?>
