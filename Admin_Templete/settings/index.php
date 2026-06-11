<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/image.php';
require_can('manage_users'); // admin-only, same gate as user management

$pageTitle  = 'Settings';
$activeMenu = 'settings';
$basePath   = '../';

// Text fields we accept from the form.
$textKeys = [
    'company_name', 'tagline', 'about',
    'contact_email', 'contact_phone', 'address',
    'facebook', 'twitter', 'instagram', 'youtube', 'linkedin',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $errors = [];

    // Optional logo upload (stored under uploads/articles for simplicity).
    $logoPath = setting('logo', '');
    try {
        if (!empty($_FILES['logo']['name'])) {
            $newLogo = save_article_image($_FILES['logo']); // validates JPG/PNG/WebP/GIF, 5MB
            if ($newLogo) {
                delete_upload_file($logoPath ?: null);
                $logoPath = $newLogo;
            }
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if (!empty($_POST['contact_email']) && !filter_var($_POST['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Contact email is not valid.';
    }

    if ($errors) {
        foreach ($errors as $err) {
            set_flash('danger', $err);
        }
        redirect('settings/index.php');
    }

    // Upsert each setting.
    $up = db()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    foreach ($textKeys as $k) {
        $up->execute([$k, trim($_POST[$k] ?? '')]);
    }
    $up->execute(['logo', $logoPath]);

    set_flash('success', 'Settings saved.');
    redirect('settings/index.php');
}

$s = all_settings();
$val = static fn(string $k) => $s[$k] ?? '';

require_once __DIR__ . '/../header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col">
                    <h4 class="mb-0">Site Settings</h4>
                    <p class="text-muted mb-0">Company information, contact details, social media and logo.</p>
                </div>
            </div>

            <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-xl-8">
                        <!-- Company -->
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Company Information</h5></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="company_name">Company name</label>
                                        <input type="text" name="company_name" id="company_name" class="form-control" value="<?= e($val('company_name')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="tagline">Tagline</label>
                                        <input type="text" name="tagline" id="tagline" class="form-control" value="<?= e($val('tagline')) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="about">About</label>
                                        <textarea name="about" id="about" class="form-control" rows="3"><?= e($val('about')) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Contact</h5></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact_email">Email</label>
                                        <input type="email" name="contact_email" id="contact_email" class="form-control" value="<?= e($val('contact_email')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact_phone">Phone</label>
                                        <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="<?= e($val('contact_phone')) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="address">Address</label>
                                        <input type="text" name="address" id="address" class="form-control" value="<?= e($val('address')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social media -->
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Social Media</h5></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="facebook"><i class="ri-facebook-circle-fill text-primary"></i> Facebook</label>
                                        <input type="url" name="facebook" id="facebook" class="form-control" value="<?= e($val('facebook')) ?>" placeholder="https://facebook.com/...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="twitter"><i class="ri-twitter-x-fill"></i> Twitter / X</label>
                                        <input type="url" name="twitter" id="twitter" class="form-control" value="<?= e($val('twitter')) ?>" placeholder="https://x.com/...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="instagram"><i class="ri-instagram-fill text-danger"></i> Instagram</label>
                                        <input type="url" name="instagram" id="instagram" class="form-control" value="<?= e($val('instagram')) ?>" placeholder="https://instagram.com/...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="youtube"><i class="ri-youtube-fill text-danger"></i> YouTube</label>
                                        <input type="url" name="youtube" id="youtube" class="form-control" value="<?= e($val('youtube')) ?>" placeholder="https://youtube.com/...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="linkedin"><i class="ri-linkedin-box-fill text-primary"></i> LinkedIn</label>
                                        <input type="url" name="linkedin" id="linkedin" class="form-control" value="<?= e($val('linkedin')) ?>" placeholder="https://linkedin.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logo -->
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Logo</h5></div>
                            <div class="card-body text-center">
                                <img src="<?= $val('logo') ? e($val('logo')) : 'assets/images/logo-dark.png' ?>"
                                     alt="logo" id="logo-preview" class="img-fluid mb-3 p-2 border rounded" style="max-height:120px">
                                <input type="file" name="logo" class="form-control" accept="image/*" onchange="previewLogo(this)">
                                <small class="text-muted">PNG/JPG/WebP, max 5 MB. Used in the public site header.</small>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body d-grid">
                                <button type="submit" class="btn btn-primary"><i class="ri-save-line align-bottom me-1"></i> Save Settings</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row"><div class="col-sm-6"><script>document.write(new Date().getFullYear())</script> © <?= e(APP_NAME) ?>.</div></div>
        </div>
    </footer>
</div>

<?php
$pageScripts = <<<'HTML'
<script>
    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) { document.getElementById('logo-preview').src = e.target.result; };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
HTML;

require_once __DIR__ . '/../footer.php';
?>
