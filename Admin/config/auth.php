<?php
/**
 * UR KAMINUZA - Authentication & Role-Based Access Control (RBAC).
 *
 * Include AFTER config.php. Call require_login() at the top of any protected page,
 * and can($permission) / require_can($permission) to gate actions.
 */

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Try to restore a session from a "remember me" cookie before anything else.
if (empty($_SESSION['user'])) {
    attempt_remember_login();
}

/**
 * Permission matrix - "Standard newsroom".
 * Keys are permission names; values list the roles that hold them.
 *
 * Workflow note:
 *   - submit_review : move own draft -> pending
 *   - publish       : move article -> published (and unpublish)
 *   - delete_any    : delete any article
 *   - delete_own    : delete own article
 */
const PERMISSIONS = [
    // user / system management
    'manage_users'      => ['admin'],
    'manage_categories' => ['admin', 'chief_editor'],
    'manage_radio'      => ['admin', 'chief_editor'],

    // article authoring
    'create_article'    => ['admin', 'chief_editor', 'reporter', 'contributor'],
    'edit_own'          => ['admin', 'chief_editor', 'reporter', 'contributor'],
    'edit_any'          => ['admin', 'chief_editor'],
    'submit_review'     => ['admin', 'chief_editor', 'reporter', 'contributor'],

    // workflow / publishing
    'publish'           => ['admin', 'chief_editor'],
    'delete_any'        => ['admin', 'chief_editor'],
    'delete_own'        => ['admin', 'chief_editor', 'reporter'],

    // everyone who can log in can view the admin lists
    'view_articles'     => ['admin', 'chief_editor', 'reporter', 'contributor', 'viewer'],
];

/** Currently logged-in user (or null). */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/** Is anyone logged in? */
function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

/** Machine role name of the current user, e.g. "chief_editor". */
function current_role(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}

/** Attempt login. Returns true on success. $remember issues a 30-day cookie. */
function attempt_login(string $email, string $password, bool $remember = false): bool
{
    $stmt = db()->prepare(
        'SELECT u.id, u.name, u.email, u.password, u.avatar, u.is_active,
                r.name AS role, r.label AS role_label
           FROM users u
           JOIN roles r ON r.id = u.role_id
          WHERE u.email = :email
          LIMIT 1'
    );
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !$user['is_active'] || !password_verify($password, $user['password'])) {
        return false;
    }

    // Don't keep the hash in the session.
    unset($user['password']);
    session_regenerate_id(true);
    $_SESSION['user'] = $user;

    if ($remember) {
        remember_user((int) $user['id']);
    }
    return true;
}

function logout(): void
{
    // Invalidate the remember-me token (DB + cookie) if present.
    if (!empty($_COOKIE['remember'])) {
        [$selector] = array_pad(explode(':', $_COOKIE['remember'], 2), 2, '');
        if ($selector !== '') {
            $stmt = db()->prepare('DELETE FROM remember_tokens WHERE selector = ?');
            $stmt->execute([$selector]);
        }
        setcookie('remember', '', time() - 3600, '/');
    }
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Issue a remember-me cookie (30 days) backed by a hashed DB token.
 * Uses the selector/validator split so the stored value can't be replayed.
 */
function remember_user(int $userId): void
{
    $selector  = bin2hex(random_bytes(16));   // 32 chars
    $validator = bin2hex(random_bytes(32));    // 64 chars
    $expires   = new DateTime('+30 days');

    $stmt = db()->prepare(
        'INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $selector, hash('sha256', $validator), $expires->format('Y-m-d H:i:s')]);

    setcookie('remember', $selector . ':' . $validator, [
        'expires'  => $expires->getTimestamp(),
        'path'     => '/',
        'httponly' => true,
        // Send only over HTTPS in production; stays off on http://localhost (XAMPP).
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite' => 'Lax',
    ]);
}

/** Restore a login from the remember-me cookie, if valid. */
function attempt_remember_login(): void
{
    if (empty($_COOKIE['remember'])) {
        return;
    }
    [$selector, $validator] = array_pad(explode(':', $_COOKIE['remember'], 2), 2, '');
    if ($selector === '' || $validator === '') {
        return;
    }

    $stmt = db()->prepare('SELECT * FROM remember_tokens WHERE selector = ? LIMIT 1');
    $stmt->execute([$selector]);
    $row = $stmt->fetch();

    if (!$row || strtotime($row['expires_at']) < time()
        || !hash_equals($row['token_hash'], hash('sha256', $validator))) {
        return;
    }

    // Load the user and start a session.
    $us = db()->prepare(
        'SELECT u.id, u.name, u.email, u.avatar, u.is_active, r.name AS role, r.label AS role_label
           FROM users u JOIN roles r ON r.id = u.role_id
          WHERE u.id = ? LIMIT 1'
    );
    $us->execute([$row['user_id']]);
    $user = $us->fetch();
    if ($user && $user['is_active']) {
        $_SESSION['user'] = $user;
    }
}

/** Where a freshly-logged-in user should land, based on role. */
function landing_url(): string
{
    return current_role() === 'viewer' ? 'articles/index.php' : 'index.php';
}

/** Reload the current user's row into the session (after a profile edit). */
function refresh_current_user(): void
{
    $id = current_user()['id'] ?? null;
    if (!$id) {
        return;
    }
    $stmt = db()->prepare(
        'SELECT u.id, u.name, u.email, u.avatar, u.is_active,
                r.name AS role, r.label AS role_label
           FROM users u JOIN roles r ON r.id = u.role_id
          WHERE u.id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    if ($row = $stmt->fetch()) {
        $_SESSION['user'] = $row;
    }
}

/**
 * Avatar URL for a user array, relative to the app root. Falls back to the
 * template's bundled default avatar when none is set.
 */
function avatar_url(?array $user = null): string
{
    $user = $user ?? current_user();
    $avatar = $user['avatar'] ?? null;
    return $avatar ? $avatar : 'assets/images/users/avatar-1.jpg';
}

/** Does the current user hold a permission? */
function can(string $permission): bool
{
    $role = current_role();
    if ($role === null) {
        return false;
    }
    $allowed = PERMISSIONS[$permission] ?? [];
    return in_array($role, $allowed, true);
}

/**
 * Can the current user act on a specific article, honouring "own" vs "any"?
 * $action is one of: edit, delete.
 */
function can_on_article(string $action, array $article): bool
{
    $userId = current_user()['id'] ?? 0;
    $isOwner = (int) $article['author_id'] === (int) $userId;

    switch ($action) {
        case 'edit':
            return can('edit_any') || ($isOwner && can('edit_own'));
        case 'delete':
            return can('delete_any') || ($isOwner && can('delete_own'));
        default:
            return false;
    }
}

/** Redirect to login if not authenticated. */
function require_login(): void
{
    if (!is_logged_in()) {
        // redirect() resolves this to an absolute URL path from the app root.
        redirect('login.php');
    }
}

/** Abort with 403 if the permission is missing. */
function require_can(string $permission): void
{
    require_login();
    if (!can($permission)) {
        http_response_code(403);
        die('403 - You do not have permission to perform this action.');
    }
}

/**
 * Create a password reset token for an email. Returns the raw token (to put in
 * the link) or null if no active user matches that email.
 */
function create_password_reset(string $email): ?string
{
    $stmt = db()->prepare('SELECT id FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        return null;
    }

    $token   = bin2hex(random_bytes(32));
    $expires = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

    // Invalidate previous unused tokens for this user.
    db()->prepare('UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0')
        ->execute([$user['id']]);

    $ins = db()->prepare(
        'INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
    );
    $ins->execute([$user['id'], hash('sha256', $token), $expires]);

    return $token;
}

/** Look up a valid (unused, unexpired) reset token. Returns the row or null. */
function find_valid_reset(string $token): ?array
{
    $stmt = db()->prepare(
        'SELECT * FROM password_resets
          WHERE token_hash = ? AND used = 0 AND expires_at > NOW()
          LIMIT 1'
    );
    $stmt->execute([hash('sha256', $token)]);
    return $stmt->fetch() ?: null;
}

/** Set a new password for the user behind a valid token, then consume it. */
function consume_password_reset(string $token, string $newPassword): bool
{
    $row = find_valid_reset($token);
    if (!$row) {
        return false;
    }
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    db()->prepare('UPDATE users SET password = ? WHERE id = ?')
        ->execute([$hash, $row['user_id']]);
    db()->prepare('UPDATE password_resets SET used = 1 WHERE id = ?')
        ->execute([$row['id']]);
    return true;
}

/**
 * Build a URL relative to the Admin root, regardless of how deep
 * the current script is. Relies on $GLOBALS['BASE_URL'] set by the page
 * (defaults to './'). Pages set it via base_url_prefix().
 */
function base_url(string $path = ''): string
{
    $prefix = $GLOBALS['BASE_URL'] ?? '';
    return $prefix . ltrim($path, '/');
}
