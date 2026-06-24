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
    'contact_email', 'contact_phone', 'address', 'contact_website',
    // Contact page: Google Map embed + the address replies are sent FROM.
    'contact_map_embed', 'contact_reply_email',
    // About page: four cards + editorial board text (intro reuses 'about').
    'about_who', 'about_how', 'about_what', 'about_management', 'about_editorial',
    'facebook', 'twitter', 'instagram', 'youtube', 'linkedin',
    // Advert links (theme colour is static, based on the logo — not editable)
    'header_ad_link', 'sidebar_ad_link', 'sidebar_tall_ad_link', 'midpage_ad_link',
    // Header quick links (label fixed in the theme; URL editable here)
    'quick_radio', 'quick_latest', 'quick_multimedia', 'quick_about',
];

// Advert image upload slots: form file field => settings key.
$adSlots = [
    'header_ad_image'       => 'Header banner (728×90)',
    'sidebar_ad_image'      => 'Sidebar banner (400×270)',
    'sidebar_tall_ad_image' => 'Tall sidebar banner (300×600)',
    'midpage_ad_image'      => 'Mid-page banner (820×100)',
];

// About-page card images (same upload pipeline, shown in the About card).
$aboutImages = [
    'about_who_image'        => 'Who We Are image',
    'about_how_image'        => 'How We Work image',
    'about_what_image'       => 'What We Do image',
    'about_management_image' => 'Management image',
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
    if (!empty($_POST['contact_reply_email']) && !filter_var($_POST['contact_reply_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Reply-from email is not valid.';
    }

    // Image uploads (adverts + About cards). Each optional; keep existing
    // value unless a new file is uploaded.
    $adPaths = [];
    foreach ($adSlots + $aboutImages as $key => $label) {
        $adPaths[$key] = setting($key, '');
        try {
            if (!empty($_FILES[$key]['name'])) {
                $newAd = save_article_image($_FILES[$key]);
                if ($newAd) {
                    delete_upload_file($adPaths[$key] ?: null);
                    $adPaths[$key] = $newAd;
                }
            }
        } catch (RuntimeException $ex) {
            $errors[] = $label . ': ' . $ex->getMessage();
        }
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
    foreach ($adPaths as $key => $path) {
        $up->execute([$key, $path]);
    }

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
                                    <div class="col-md-6">
                                        <label class="form-label" for="address">Address</label>
                                        <input type="text" name="address" id="address" class="form-control" value="<?= e($val('address')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact_website">Website</label>
                                        <input type="text" name="contact_website" id="contact_website" class="form-control" value="<?= e($val('contact_website')) ?>" placeholder="https://...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="contact_reply_email">Reply-from email <span class="text-muted fw-normal">(replies to contact messages are sent from this address)</span></label>
                                        <input type="email" name="contact_reply_email" id="contact_reply_email" class="form-control" value="<?= e($val('contact_reply_email')) ?>" placeholder="e.g. info@kaminuza.rw">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="contact_map_embed">Google Map embed link</label>
                                        <input type="text" name="contact_map_embed" id="contact_map_embed" class="form-control" value="<?= e($val('contact_map_embed')) ?>" placeholder="Paste the src URL from Google Maps → Share → Embed a map">
                                        <small class="text-muted">In Google Maps: find your location → <b>Share</b> → <b>Embed a map</b> → copy the link inside <code>src="…"</code> (or paste the whole iframe — we’ll extract it).</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- About page -->
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">About Us Page</h5></div>
                            <div class="card-body">
                                <p class="text-muted small">Content for the public <b>About Us</b> page. The intro uses the “About” text above. Each card has a short text and an optional image.</p>
                                <div class="row g-3">
                                    <?php
                                    $aboutCards = [
                                        'about_who'        => ['Who We Are',  'about_who_image'],
                                        'about_how'        => ['How We Work', 'about_how_image'],
                                        'about_what'       => ['What We Do',  'about_what_image'],
                                        'about_management' => ['Management',  'about_management_image'],
                                    ];
                                    foreach ($aboutCards as $key => [$label, $imgKey]): ?>
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <label class="form-label fw-semibold"><?= e($label) ?></label>
                                            <textarea name="<?= $key ?>" class="form-control mb-2" rows="3" placeholder="Short description for ‘<?= e($label) ?>’"><?= e($val($key)) ?></textarea>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if ($val($imgKey)): ?>
                                                    <img src="<?= e($val($imgKey)) ?>" alt="" class="border rounded" style="height:42px;width:62px;object-fit:cover;">
                                                <?php endif; ?>
                                                <input type="file" name="<?= $imgKey ?>" class="form-control form-control-sm" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <div class="col-12">
                                        <label class="form-label" for="about_editorial">Editorial Board text</label>
                                        <textarea name="about_editorial" id="about_editorial" class="form-control" rows="3" placeholder="A paragraph about the editorial board / team"><?= e($val('about_editorial')) ?></textarea>
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

                        <!-- Header quick links -->
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Header Quick Links</h5></div>
                            <div class="card-body">
                                <p class="text-muted small">The small links shown under the main menu. Set where each one points. Leave empty to use a sensible default.</p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="quick_radio">Salus Radio URL</label>
                                        <input type="url" name="quick_radio" id="quick_radio" class="form-control" value="<?= e($val('quick_radio')) ?>" placeholder="https://...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="quick_latest">Latest News URL</label>
                                        <input type="text" name="quick_latest" id="quick_latest" class="form-control" value="<?= e($val('quick_latest')) ?>" placeholder="section.php?cat=news">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="quick_multimedia">Multimedia URL</label>
                                        <input type="text" name="quick_multimedia" id="quick_multimedia" class="form-control" value="<?= e($val('quick_multimedia')) ?>" placeholder="section.php?cat=multimedia">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="quick_about">About Us URL</label>
                                        <input type="text" name="quick_about" id="quick_about" class="form-control" value="<?= e($val('quick_about')) ?>" placeholder="about">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advertisements -->
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Advertisements / Banners</h5></div>
                            <div class="card-body">
                                <p class="text-muted small">Upload a banner image and set the link it opens. Leave empty to hide a slot (a placeholder shows until then).</p>
                                <?php
                                $adLinkKey = ['header_ad_image' => 'header_ad_link', 'sidebar_ad_image' => 'sidebar_ad_link', 'sidebar_tall_ad_image' => 'sidebar_tall_ad_link', 'midpage_ad_image' => 'midpage_ad_link'];
                                foreach ($adSlots as $imgKey => $label): $linkKey = $adLinkKey[$imgKey]; ?>
                                <div class="row g-3 align-items-end mb-3 pb-3 border-bottom">
                                    <div class="col-md-3">
                                        <?php if ($val($imgKey)): ?>
                                            <img src="<?= e($val($imgKey)) ?>" alt="<?= e($label) ?>" class="img-fluid border rounded" style="max-height:60px">
                                        <?php else: ?>
                                            <span class="text-muted small">No banner</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label"><?= e($label) ?></label>
                                        <input type="file" name="<?= $imgKey ?>" class="form-control" accept="image/*">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Link URL</label>
                                        <input type="url" name="<?= $linkKey ?>" class="form-control" value="<?= e($val($linkKey)) ?>" placeholder="https://...">
                                    </div>
                                </div>
                                <?php endforeach; ?>
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
