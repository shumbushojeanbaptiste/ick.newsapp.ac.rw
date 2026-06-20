<?php
/**
 * UR KAMINUZA - Public website content helpers (read-only).
 * Reads published articles and the active category tree from the DB.
 */
require_once __DIR__ . '/config.php';

/* ---------------------------------------------------------------------------
 * Categories (navigation)
 * ------------------------------------------------------------------------- */

/** All active categories, ordered. Cached per request. */
function nav_categories(): array
{
    static $rows = null;
    if ($rows === null) {
        $rows = db()->query(
            'SELECT id, parent_id, name, slug, sort_order
               FROM categories
              WHERE is_active = 1
              ORDER BY sort_order, name'
        )->fetchAll();
    }
    return $rows;
}

/** Top-level active sections (parent_id IS NULL). */
function nav_top_sections(): array
{
    return array_values(array_filter(nav_categories(), static fn($c) => $c['parent_id'] === null));
}

/** Active sub-sections of a given parent id. */
function nav_children(int $parentId): array
{
    return array_values(array_filter(nav_categories(), static fn($c) => (int) $c['parent_id'] === $parentId));
}

/* ---------------------------------------------------------------------------
 * Articles (published only, on the public site)
 * ------------------------------------------------------------------------- */

/**
 * Best title for an article in the requested language, falling back to the
 * article's primary language, then any other filled language.
 */
function a_title(array $a, string $lang = 'en'): string
{
    $order = array_unique([$lang, $a['primary_lang'] ?? 'en', 'en', 'fr', 'rw']);
    foreach ($order as $l) {
        if (!empty($a["title_$l"])) {
            return $a["title_$l"];
        }
    }
    return '(untitled)';
}

/** Best excerpt for an article, with the same language fallback as a_title(). */
function a_excerpt(array $a, string $lang = 'en'): string
{
    $order = array_unique([$lang, $a['primary_lang'] ?? 'en', 'en', 'fr', 'rw']);
    foreach ($order as $l) {
        if (!empty($a["excerpt_$l"])) {
            return $a["excerpt_$l"];
        }
    }
    // Fall back to a trimmed body if no excerpt exists.
    foreach ($order as $l) {
        if (!empty($a["body_$l"])) {
            return mb_substr(trim(strip_tags($a["body_$l"])), 0, 160);
        }
    }
    return '';
}

/** Featured image URL for an article (or '' if none). */
function a_image(array $a): string
{
    return upload_url($a['featured_image'] ?? '');
}

/**
 * Recent published articles, optionally filtered by a flag or category.
 * $flag: 'featured' | 'breaking' | 'editor_pick' | null
 */
function published_articles(int $limit = 10, ?string $flag = null, ?int $categoryId = null): array
{
    $where  = ["a.status = 'published'"];
    $params = [];

    if ($flag === 'featured')      { $where[] = 'a.is_featured = 1'; }
    elseif ($flag === 'breaking')  { $where[] = 'a.is_breaking = 1'; }
    elseif ($flag === 'editor_pick'){ $where[] = 'a.is_editor_pick = 1'; }

    if ($categoryId !== null) {
        $where[] = 'a.category_id = ?';
        $params[] = $categoryId;
    }

    $sql = 'SELECT a.*, c.name AS category_name, c.slug AS category_slug
              FROM articles a
              LEFT JOIN categories c ON c.id = a.category_id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY COALESCE(a.published_at, a.created_at) DESC
             LIMIT ' . (int) $limit;

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Full published article by slug, with author name + category. Returns null if
 * not found or not published (so unpublished/draft articles aren't public).
 */
function article_by_slug(string $slug): ?array
{
    $stmt = db()->prepare(
        "SELECT a.*, c.name AS category_name, c.slug AS category_slug, u.name AS author_name
           FROM articles a
           LEFT JOIN categories c ON c.id = a.category_id
           LEFT JOIN users u      ON u.id = a.author_id
          WHERE a.slug = ? AND a.status = 'published'
          LIMIT 1"
    );
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

/** Best body for an article in the requested language, with fallback. */
function a_body(array $a, string $lang = 'en'): string
{
    $order = array_unique([$lang, $a['primary_lang'] ?? 'en', 'en', 'fr', 'rw']);
    foreach ($order as $l) {
        if (!empty($a["body_$l"])) {
            return $a["body_$l"];
        }
    }
    return '';
}

/** Which languages this article actually has (title present). */
function a_languages(array $a): array
{
    $langs = [];
    foreach (['rw', 'fr', 'en'] as $l) {
        if (!empty($a["title_$l"])) {
            $langs[$l] = $GLOBALS['LANGUAGES'][$l] ?? strtoupper($l);
        }
    }
    return $langs;
}

/** External reference links for an article. */
function article_links(int $articleId): array
{
    $stmt = db()->prepare('SELECT label, url FROM article_links WHERE article_id = ? ORDER BY id');
    $stmt->execute([$articleId]);
    return $stmt->fetchAll();
}

/** Gallery images for an article. */
function article_images(int $articleId): array
{
    $stmt = db()->prepare('SELECT path, caption FROM article_images WHERE article_id = ? ORDER BY sort_order, id');
    $stmt->execute([$articleId]);
    return $stmt->fetchAll();
}

/**
 * Render the media player for an article (YouTube/Vimeo embed, or uploaded
 * video/audio). Returns '' when the article has no media.
 */
function render_media(array $a): string
{
    $type = $a['media_type'] ?? 'none';
    if ($type === 'none') {
        return '';
    }
    $file = $a['media_file'] ?? null;
    $url  = $a['media_url'] ?? null;

    // Uploaded file (served from the Admin uploads).
    if ($file) {
        $src = e(upload_url($file));
        return $type === 'audio'
            ? '<audio controls class="img-responsive" style="width:100%"><source src="' . $src . '"></audio>'
            : '<video controls class="img-responsive" style="width:100%"><source src="' . $src . '"></video>';
    }
    if ($url) {
        // YouTube / Vimeo -> responsive iframe.
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $url, $m)) {
            return '<div class="ratio ratio-16x9"><iframe width="100%" height="420" src="https://www.youtube.com/embed/' . e($m[1]) . '" frameborder="0" allowfullscreen></iframe></div>';
        }
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $url, $m)) {
            return '<iframe width="100%" height="420" src="https://player.vimeo.com/video/' . e($m[1]) . '" frameborder="0" allowfullscreen></iframe>';
        }
        // Fallback: treat as a direct media URL.
        $src = e($url);
        return $type === 'audio'
            ? '<audio controls style="width:100%"><source src="' . $src . '"></audio>'
            : '<video controls style="width:100%"><source src="' . $src . '"></video>';
    }
    return '';
}

/** Look up a single active category by its slug (or null). */
function category_by_slug(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM categories WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

/** Slug of the top-level ancestor of a category (for menu highlighting). */
function category_parent_slug(int $categoryId): ?string
{
    $cursor = $categoryId;
    $guard = 0;
    $slug = null;
    while ($cursor && $guard++ < 100) {
        $stmt = db()->prepare('SELECT slug, parent_id FROM categories WHERE id = ?');
        $stmt->execute([$cursor]);
        $row = $stmt->fetch();
        if (!$row) {
            break;
        }
        $slug = $row['slug'];
        if ($row['parent_id'] === null) {
            break;
        }
        $cursor = (int) $row['parent_id'];
    }
    return $slug;
}

/** Direct child category ids of a category (one level). */
function child_category_ids(int $parentId): array
{
    $stmt = db()->prepare('SELECT id FROM categories WHERE parent_id = ?');
    $stmt->execute([$parentId]);
    return array_map('intval', array_column($stmt->fetchAll(), 'id'));
}

/**
 * Published articles in a category INCLUDING its sub-categories, so a parent
 * section page (e.g. "News") shows stories filed under Local/Regional/etc. too.
 * Supports LIMIT + OFFSET for pagination. Returns [rows, totalCount].
 */
function published_in_category(int $categoryId, int $limit = 12, int $offset = 0): array
{
    // The category itself + its direct children (covers the 2-3 level skeleton).
    $ids = array_merge([$categoryId], child_category_ids($categoryId));
    // Also include grandchildren (e.g. News > Local > UR section).
    foreach (child_category_ids($categoryId) as $childId) {
        $ids = array_merge($ids, child_category_ids($childId));
    }
    $ids = array_values(array_unique($ids));
    $in  = implode(',', array_fill(0, count($ids), '?'));

    $countStmt = db()->prepare(
        "SELECT COUNT(*) FROM articles WHERE status='published' AND category_id IN ($in)"
    );
    $countStmt->execute($ids);
    $total = (int) $countStmt->fetchColumn();

    $sql = "SELECT a.*, c.name AS category_name, c.slug AS category_slug
              FROM articles a
              LEFT JOIN categories c ON c.id = a.category_id
             WHERE a.status='published' AND a.category_id IN ($in)
             ORDER BY COALESCE(a.published_at, a.created_at) DESC
             LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
    $stmt = db()->prepare($sql);
    $stmt->execute($ids);
    return [$stmt->fetchAll(), $total];
}

/** Active radio/TV streams (e.g. Salus Radio) for the On-Air ticker. */
function active_streams(): array
{
    try {
        $stmt = db()->query('SELECT name, stream_url FROM radio_streams WHERE is_active = 1 ORDER BY id');
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}
