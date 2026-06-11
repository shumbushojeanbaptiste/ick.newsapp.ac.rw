<?php
/**
 * Article workflow helpers for UR KAMINUZA.
 * Statuses: draft -> pending -> published <-> unpublished
 */
require_once __DIR__ . '/auth.php';

const ARTICLE_STATUSES = ['draft', 'pending', 'published', 'unpublished'];

/** Bootstrap badge classes per status. */
function status_badge_class(string $status): string
{
    return [
        'draft'       => 'bg-secondary-subtle text-secondary',
        'pending'     => 'bg-warning-subtle text-warning',
        'published'   => 'bg-success-subtle text-success',
        'unpublished' => 'bg-danger-subtle text-danger',
    ][$status] ?? 'bg-light text-body';
}

/**
 * Which workflow actions can the current user perform on this article?
 * Returns a list like ['edit','submit','publish','unpublish','delete'].
 */
function article_actions(array $article): array
{
    $actions = [];
    $status  = $article['status'];

    if (can_on_article('edit', $article)) {
        $actions[] = 'edit';
    }

    // Author (or editor) can submit a draft / unpublished item for review.
    if (in_array($status, ['draft', 'unpublished'], true)
        && can('submit_review')
        && can_on_article('edit', $article)) {
        $actions[] = 'submit';
    }

    // Publishers move pending/draft/unpublished -> published.
    if (can('publish') && $status !== 'published') {
        $actions[] = 'publish';
    }
    // Publishers can take a published article down.
    if (can('publish') && $status === 'published') {
        $actions[] = 'unpublish';
    }

    if (can_on_article('delete', $article)) {
        $actions[] = 'delete';
    }

    return $actions;
}

/** Map a language code to its label. */
function lang_label(string $code): string
{
    return $GLOBALS['LANGUAGES'][$code] ?? strtoupper($code);
}

/**
 * Best available title for an article, preferring its primary language,
 * then any other filled language, then a placeholder.
 */
function article_title(array $a): string
{
    $order = array_unique([$a['primary_lang'] ?? 'en', 'en', 'fr', 'rw']);
    foreach ($order as $l) {
        if (!empty($a["title_$l"])) {
            return $a["title_$l"];
        }
    }
    return '(untitled)';
}

/** Which languages does this article actually have a title for? */
function article_langs(array $a): array
{
    $langs = [];
    foreach (['rw', 'fr', 'en'] as $l) {
        if (!empty($a["title_$l"])) {
            $langs[] = $l;
        }
    }
    return $langs;
}

/** May the current user reassign an article's author? (editors only) */
function can_reassign_author(): bool
{
    return can('edit_any'); // admin + chief_editor
}

/** Users who can be set as an article author (active authoring roles). */
function authorable_users(): array
{
    return db()->query(
        "SELECT u.id, u.name, r.label AS role_label
           FROM users u JOIN roles r ON r.id = u.role_id
          WHERE u.is_active = 1
            AND r.name IN ('admin','chief_editor','reporter','contributor')
          ORDER BY u.name"
    )->fetchAll();
}

/**
 * Turn an embed URL into a YouTube/Vimeo embeddable iframe src, or null if it
 * is not a recognised provider (caller can then treat it as a direct file).
 */
function embed_iframe_src(string $url): ?string
{
    // YouTube: watch?v=, youtu.be/, /embed/
    if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    // Vimeo
    if (preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }
    return null;
}

/**
 * Render the media player HTML for an article (embed iframe, SoundCloud,
 * or a direct <video>/<audio> for uploaded/linked files). Returns '' if none.
 */
function render_article_media(array $a): string
{
    $type = $a['media_type'] ?? 'none';
    if ($type === 'none') {
        return '';
    }

    // Prefer an uploaded file; otherwise use the embed URL.
    $file = $a['media_file'] ?? null;
    $url  = $a['media_url'] ?? null;

    if ($file) {
        $src = e($file);
        if ($type === 'video') {
            return '<video controls class="w-100 rounded" style="max-height:480px"><source src="' . $src . '"></video>';
        }
        return '<audio controls class="w-100"><source src="' . $src . '"></audio>';
    }

    if ($url) {
        $iframe = embed_iframe_src($url);
        if ($iframe) {
            return '<div class="ratio ratio-16x9"><iframe src="' . e($iframe)
                . '" title="media" allowfullscreen frameborder="0"></iframe></div>';
        }
        // SoundCloud or other audio embed.
        if (stripos($url, 'soundcloud.com') !== false) {
            return '<iframe width="100%" height="120" scrolling="no" frameborder="no" '
                . 'src="https://w.soundcloud.com/player/?url=' . urlencode($url) . '"></iframe>';
        }
        // Fallback: treat the URL as a direct media file.
        $src = e($url);
        if ($type === 'video') {
            return '<video controls class="w-100 rounded" style="max-height:480px"><source src="' . $src . '"></video>';
        }
        return '<audio controls class="w-100"><source src="' . $src . '"></audio>';
    }

    return '';
}
