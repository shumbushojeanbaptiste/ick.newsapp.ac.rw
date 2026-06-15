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
