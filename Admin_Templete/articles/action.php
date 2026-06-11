<?php
/**
 * Article workflow actions: submit / publish / unpublish / delete.
 * Every action is re-checked against RBAC on the server (never trust the UI).
 */
require_once __DIR__ . '/../config/articles.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('articles/index.php');
}
verify_csrf();

$id     = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

$stmt = db()->prepare('SELECT * FROM articles WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    set_flash('danger', 'Article not found.');
    redirect('articles/index.php');
}

switch ($action) {
    case 'submit':
        if (!can('submit_review') || !can_on_article('edit', $article)
            || !in_array($article['status'], ['draft', 'unpublished'], true)) {
            http_response_code(403);
            die('Not allowed.');
        }
        db()->prepare('UPDATE articles SET status = "pending" WHERE id = ?')->execute([$id]);
        set_flash('success', 'Article submitted for review.');
        break;

    case 'publish':
        if (!can('publish')) {
            http_response_code(403);
            die('Not allowed.');
        }
        db()->prepare(
            'UPDATE articles
                SET status = "published",
                    published_at = COALESCE(published_at, NOW())
              WHERE id = ?'
        )->execute([$id]);
        set_flash('success', 'Article published.');
        break;

    case 'unpublish':
        if (!can('publish')) {
            http_response_code(403);
            die('Not allowed.');
        }
        db()->prepare('UPDATE articles SET status = "unpublished" WHERE id = ?')->execute([$id]);
        set_flash('success', 'Article unpublished.');
        break;

    case 'delete':
        if (!can_on_article('delete', $article)) {
            http_response_code(403);
            die('Not allowed.');
        }
        require_once __DIR__ . '/../config/image.php';
        delete_upload_file($article['featured_image'] ?? null);
        db()->prepare('DELETE FROM articles WHERE id = ?')->execute([$id]); // links cascade
        set_flash('success', 'Article deleted.');
        break;

    default:
        set_flash('danger', 'Unknown action.');
}

redirect('articles/index.php');
