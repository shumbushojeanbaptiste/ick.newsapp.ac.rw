<?php
/**
 * UR KAMINUZA - Like / dislike endpoint (AJAX, JSON).
 * One reaction per visitor per article; clicking the same one again removes it,
 * clicking the other switches it. Reactions are instant (not moderated).
 *   POST react.php { article_id, reaction: like|dislike }
 */
require_once __DIR__ . '/include/content.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method']);
    exit;
}

$articleId = (int) ($_POST['article_id'] ?? 0);
$reaction  = $_POST['reaction'] ?? '';
if ($articleId <= 0 || !in_array($reaction, ['like', 'dislike'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'bad_request']);
    exit;
}

// Only allow reactions on published articles.
$chk = db()->prepare("SELECT id FROM articles WHERE id = ? AND status = 'published' LIMIT 1");
$chk->execute([$articleId]);
if (!$chk->fetchColumn()) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'not_found']);
    exit;
}

$visitor  = visitor_token();
$existing = my_reaction($articleId);

if ($existing === $reaction) {
    // Same button clicked again → remove the vote (toggle off).
    $del = db()->prepare('DELETE FROM article_reactions WHERE article_id = ? AND visitor = ?');
    $del->execute([$articleId, $visitor]);
    $mine = null;
} else {
    // New or switched vote → upsert.
    $up = db()->prepare(
        'INSERT INTO article_reactions (article_id, visitor, reaction)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE reaction = VALUES(reaction)'
    );
    $up->execute([$articleId, $visitor, $reaction]);
    $mine = $reaction;
}

[$likes, $dislikes] = reaction_counts($articleId);
echo json_encode(['ok' => true, 'likes' => $likes, 'dislikes' => $dislikes, 'mine' => $mine]);
