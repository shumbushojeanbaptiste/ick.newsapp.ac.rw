<?php
require_once __DIR__ . '/config/auth.php';

if (is_logged_in()) {
    redirect(landing_url());
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } elseif (attempt_login($email, $password, $remember)) {
        set_flash('success', 'Welcome back, ' . (current_user()['name'] ?? '') . '!');
        redirect(landing_url());
    } else {
        $error = 'Invalid credentials or inactive account.';
    }
}
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable">

<head>
    <meta charset="utf-8">
    <title>Sign In | <?= e(APP_NAME) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="auth-page-wrapper pt-5">
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

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
                                    <h5 class="text-primary">Welcome Back!</h5>
                                    <p class="text-muted">Sign in to continue to <?= e(APP_NAME) ?>.</p>
                                </div>

                                <div class="p-2 mt-4">
                                    <form method="post" action="login.php">
                                        <?= csrf_field() ?>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" id="email"
                                                   placeholder="Enter email" required
                                                   value="<?= e($_POST['email'] ?? '') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <div class="float-end">
                                                <a href="forgot-password.php" class="text-muted">Forgot password?</a>
                                            </div>
                                            <label class="form-label" for="password">Password</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" name="password"
                                                       class="form-control pe-5 password-input"
                                                       placeholder="Enter password" id="password" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                        type="button" id="password-addon">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                                            <label class="form-check-label" for="remember">Remember me</label>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="mb-0 text-muted">&copy; <script>document.write(new Date().getFullYear())</script> <?= e(APP_NAME) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        // Show / hide password toggle.
        document.getElementById('password-addon').addEventListener('click', function () {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    </script>
    <?php if ($error): ?>
    <script>
        Swal.fire({ icon: 'error', title: 'Login failed', text: <?= json_encode($error) ?>, confirmButtonColor: '#405189' });
    </script>
    <?php endif; ?>
    <?php require_once __DIR__ . '/config/flash.php'; // shows logout / reset success toasts ?>
</body>
</html>
