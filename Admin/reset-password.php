<?php
require_once __DIR__ . '/config/auth.php';

if (is_logged_in()) {
    redirect(landing_url());
}

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$error = null;
$valid = $token !== '' && find_valid_reset($token) !== null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $pass  = $_POST['password'] ?? '';
    $conf  = $_POST['password_confirm'] ?? '';

    if (!$valid) {
        $error = 'This reset link is invalid or has expired.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($pass !== $conf) {
        $error = 'Passwords do not match.';
    } elseif (consume_password_reset($token, $pass)) {
        set_flash('success', 'Password reset successfully. Please sign in.');
        redirect('login.php');
    } else {
        $error = 'Could not reset password. The link may have expired.';
    }
}
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-preloader="disable">
<head>
    <meta charset="utf-8">
    <title>Reset Password | <?= e(APP_NAME) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= e(logo_url('dark')) ?>">
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">
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
                            <a href="login" class="d-inline-block auth-logo">
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
                                    <h5 class="text-primary">Create new password</h5>
                                    <p class="text-muted">Your new password must be different from previous ones.</p>
                                </div>

                                <?php if (!$valid): ?>
                                    <div class="alert alert-danger text-center">
                                        This reset link is invalid or has expired.
                                        <a href="forgot-password">Request a new one</a>.
                                    </div>
                                <?php else: ?>
                                    <div class="p-2">
                                        <form method="post" action="reset-password.php">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="token" value="<?= e($token) ?>">
                                            <div class="mb-3">
                                                <label class="form-label">New Password</label>
                                                <input type="password" name="password" class="form-control" placeholder="Enter password" required minlength="6">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Confirm Password</label>
                                                <input type="password" name="password_confirm" class="form-control" placeholder="Confirm password" required minlength="6">
                                            </div>
                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">Reset Password</button>
                                            </div>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="mb-0">Back to <a href="login" class="fw-semibold text-primary text-decoration-underline">Sign In</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/js/app.js"></script>
    <?php if ($error): ?>
    <script>Swal.fire({ icon: 'error', title: 'Error', text: <?= json_encode($error) ?>, confirmButtonColor: '#405189' });</script>
    <?php endif; ?>
</body>
</html>
