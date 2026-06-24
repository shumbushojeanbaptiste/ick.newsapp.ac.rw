<?php
require_once __DIR__ . '/config/auth.php';

if (is_logged_in()) {
    redirect(landing_url());
}

$resetLink = null;
$notice    = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $token = $email !== '' ? create_password_reset($email) : null;

    // Always show the same neutral message (don't reveal which emails exist).
    $notice = 'If that email is registered, a reset link has been generated below.';

    if ($token) {
        // DEV MODE: show the link on screen (no mail server on XAMPP).
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $dir    = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        $resetLink = $scheme . '://' . $_SERVER['HTTP_HOST'] . $dir . '/reset-password.php?token=' . $token;
    }
}
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-preloader="disable">
<head>
    <meta charset="utf-8">
    <title>Forgot Password | <?= e(APP_NAME) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= e(logo_url('dark')) ?>">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/icons.min.css" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet">
    <link href="assets/css/custom.min.css" rel="stylesheet">
</head>
<body>
    <div class="auth-page-wrapper pt-5">
        <?php $loginBg = setting('login_bg'); ?>
        <div class="auth-one-bg-position auth-one-bg"<?= $loginBg ? ' style="background-image:url(' . e($loginBg) . ');"' : '' ?>><div class="bg-overlay"></div></div>
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <a href="login.php" class="d-inline-block auth-logo">
                                <img src="<?= e(logo_url('light')) ?>" alt="" height="48">
                            </a>
                            <p class="mt-3 fs-15 fw-medium"><?= e(APP_NAME) ?> Admin</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Forgot Password?</h5>
                                    <p class="text-muted">Reset password with <?= e(APP_NAME) ?></p>
                                </div>

                                <?php if ($notice): ?>
                                    <div class="alert alert-info text-center"><?= e($notice) ?></div>
                                <?php endif; ?>

                                <?php if ($resetLink): ?>
                                    <div class="alert alert-success">
                                        <strong>Dev mode reset link:</strong><br>
                                        <a href="<?= e($resetLink) ?>" class="word-break-all"><?= e($resetLink) ?></a>
                                        <div class="small text-muted mt-1">Valid for 1 hour.</div>
                                    </div>
                                <?php endif; ?>

                                <div class="p-2">
                                    <form method="post" action="forgot-password.php">
                                        <?= csrf_field() ?>
                                        <div class="mb-4">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                                        </div>
                                        <div class="text-center mt-4">
                                            <button class="btn btn-success w-100" type="submit">Send Reset Link</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="mb-0">Wait, I remember my password... <a href="login.php" class="fw-semibold text-primary text-decoration-underline">Click here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
