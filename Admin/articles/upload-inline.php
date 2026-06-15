<?php
/**
 * Inline image upload endpoint for the article rich-text editor (Quill).
 * Accepts one image field "image", stores it under uploads/articles/, and
 * returns JSON: { "url": "uploads/articles/xxx.jpg" } on success,
 * or { "error": "..." } with an appropriate status code on failure.
 */
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/image.php';

header('Content-Type: application/json');

if (!is_logged_in() || !can('create_article')) {
    http_response_code(403);
    echo json_encode(['error' => 'Not allowed.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

// CSRF check (token sent as a header or form field).
$token = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if (!is_string($token) || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token.']);
    exit;
}

if (empty($_FILES['image']['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No image uploaded.']);
    exit;
}

try {
    // Reuse the validated single-image saver (JPG/PNG/WebP/GIF, max 5 MB).
    $path = store_article_image_file($_FILES['image'], 'Inline image');
    echo json_encode(['url' => app_base_url() . $path]);
} catch (RuntimeException $ex) {
    http_response_code(422);
    echo json_encode(['error' => $ex->getMessage()]);
}
