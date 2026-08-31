<?php
/**
 * ICK News - Public website content helpers (read-only).
 * Reads published articles and the active category tree from the DB.
 */
require_once __DIR__ . '/config.php';

/* ---------------------------------------------------------------------------
 * Language (ENG / KINYA / FRENCH)
 * The visitor picks a language; it sticks in the session site-wide. Content is
 * then shown ONLY in that language (strict): articles without a title in the
 * chosen language are hidden from listings.
 * ------------------------------------------------------------------------- */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Set the active site language (rw|fr|en) and remember it in the session. */
function set_lang(string $lang): void
{
    if (isset($GLOBALS['LANGUAGES'][$lang])) {
        $_SESSION['site_lang'] = $lang;
    }
}

// Allow ?lang=xx to switch (the header switcher uses this), then persist.
if (isset($_GET['lang'])) {
    set_lang((string) $_GET['lang']);
}

/** The active site language code (defaults to English). */
function current_lang(): string
{
    $l = $_SESSION['site_lang'] ?? 'en';
    return isset($GLOBALS['LANGUAGES'][$l]) ? $l : 'en';
}

// UI string translations (t()) — needs current_lang() defined above.
require_once __DIR__ . '/i18n.php';

/* ---------------------------------------------------------------------------
 * Categories (navigation)
 * ------------------------------------------------------------------------- */

/** All active categories, ordered. Cached per request. */
function nav_categories(): array
{
    static $rows = null;
    if ($rows === null) {
        $rows = db()->query(
            'SELECT id, parent_id, name, name_rw, name_fr, slug, sort_order
               FROM categories
              WHERE is_active = 1
              ORDER BY sort_order, name'
        )->fetchAll();
    }
    return $rows;
}

/**
 * Category name in the current language. Accepts a category row (with
 * name/name_rw/name_fr) OR an article row (with category_name) — falls back to
 * the English name when a translation is missing.
 */
function cat_name(array $c): string
{
    $lang = current_lang();
    if ($lang !== 'en') {
        // Category rows use name_rw/name_fr; article rows use category_name_rw/_fr.
        if (!empty($c["name_$lang"]))          { return $c["name_$lang"]; }
        if (!empty($c["category_name_$lang"])) { return $c["category_name_$lang"]; }
    }
    return $c['name'] ?? $c['category_name'] ?? '';
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

/**
 * Extra external links shown at the end of the category menu:
 *   - the ICK News YouTube channel  ("Videos" / "Amashusho" / "Vidéos")
 *   - the main ICK website          ("ICK")
 * URLs come from Settings ("youtube" and "contact_website"), so the admin can
 * change them without touching code. A link is skipped when its URL is unset.
 */
function nav_external_links(): array
{
    $links = [];

    $youtube = trim((string) (setting('nav_youtube') ?: setting('youtube')));
    if ($youtube !== '') {
        $links[] = ['label' => t('videos'), 'url' => $youtube, 'icon' => 'fa-youtube-play'];
    }

    $ickSite = trim((string) (setting('nav_ick_site') ?: setting('contact_website')));
    if ($ickSite !== '') {
        $links[] = ['label' => t('ick_site'), 'url' => $ickSite, 'icon' => 'fa-external-link'];
    }

    return $links;
}

/* ---------------------------------------------------------------------------
 * Articles (published only, on the public site)
 * ------------------------------------------------------------------------- */

/**
 * Best title for an article in the requested language, falling back to the
 * article's primary language, then any other filled language.
 */
function a_title(array $a, string $lang = ''): string
{
    $lang = $lang ?: current_lang();
    $order = array_unique([$lang, $a['primary_lang'] ?? 'en', 'en', 'fr', 'rw']);
    foreach ($order as $l) {
        if (!empty($a["title_$l"])) {
            return $a["title_$l"];
        }
    }
    return '(untitled)';
}

/** Best excerpt for an article, with the same language fallback as a_title(). */
/**
 * Article excerpt in the current language, capped so cards stay uniform.
 * $max = 0 keeps the full text; otherwise trims on a word boundary + adds "…".
 */
function a_excerpt(array $a, string $lang = '', int $max = 160): string
{
    $lang = $lang ?: current_lang();
    $order = array_unique([$lang, $a['primary_lang'] ?? 'en', 'en', 'fr', 'rw']);
    $text = '';
    foreach ($order as $l) {
        if (!empty($a["excerpt_$l"])) { $text = $a["excerpt_$l"]; break; }
    }
    if ($text === '') {
        // Fall back to a trimmed body if no excerpt exists.
        foreach ($order as $l) {
            if (!empty($a["body_$l"])) { $text = trim(strip_tags($a["body_$l"])); break; }
        }
    }
    $text = trim(preg_replace('~\s+~', ' ', $text));
    if ($max > 0 && mb_strlen($text) > $max) {
        $text = mb_substr($text, 0, $max);
        // Cut back to the last space so we don't split a word.
        $sp = mb_strrpos($text, ' ');
        if ($sp !== false && $sp > $max * 0.6) { $text = mb_substr($text, 0, $sp); }
        $text = rtrim($text, " .,;:") . '…';
    }
    return $text;
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
/**
 * SQL condition (no params) requiring the article to have a non-empty title in
 * the current site language — used to STRICTLY show only that language's content.
 * Column name is validated against the known languages, so it's safe to inline.
 */
function lang_filter_sql(string $alias = 'a'): string
{
    $lang = current_lang();
    return "{$alias}.title_{$lang} IS NOT NULL AND {$alias}.title_{$lang} <> ''";
}

function published_articles(int $limit = 10, ?string $flag = null, ?int $categoryId = null): array
{
    $where  = ["a.status = 'published'", lang_filter_sql('a')];
    $params = [];

    if ($flag === 'featured')      { $where[] = 'a.is_featured = 1'; }
    elseif ($flag === 'breaking')  { $where[] = 'a.is_breaking = 1'; }
    elseif ($flag === 'editor_pick'){ $where[] = 'a.is_editor_pick = 1'; }

    if ($categoryId !== null) {
        $where[] = 'a.category_id = ?';
        $params[] = $categoryId;
    }

    $sql = 'SELECT a.*, c.name AS category_name, c.name_rw AS category_name_rw, c.name_fr AS category_name_fr, c.slug AS category_slug
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
        "SELECT a.*, c.name AS category_name, c.name_rw AS category_name_rw, c.name_fr AS category_name_fr, c.slug AS category_slug, u.name AS author_name
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
function a_body(array $a, string $lang = ''): string
{
    $lang = $lang ?: current_lang();
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

    $langCond = lang_filter_sql('articles');   // strict: only current-language content
    $countStmt = db()->prepare(
        "SELECT COUNT(*) FROM articles WHERE status='published' AND $langCond AND category_id IN ($in)"
    );
    $countStmt->execute($ids);
    $total = (int) $countStmt->fetchColumn();

    $sql = "SELECT a.*, c.name AS category_name, c.name_rw AS category_name_rw, c.name_fr AS category_name_fr, c.slug AS category_slug
              FROM articles a
              LEFT JOIN categories c ON c.id = a.category_id
             WHERE a.status='published' AND " . lang_filter_sql('a') . " AND a.category_id IN ($in)
             ORDER BY COALESCE(a.published_at, a.created_at) DESC
             LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
    $stmt = db()->prepare($sql);
    $stmt->execute($ids);
    return [$stmt->fetchAll(), $total];
}

/** Active radio/TV streams (e.g. ICK Radio) for the On-Air ticker. */
function active_streams(): array
{
    try {
        $stmt = db()->query('SELECT name, stream_url FROM radio_streams WHERE is_active = 1 ORDER BY id');
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * The station name shown in the header / quick-links / player.
 * Order: the Settings override, then the first active stream, then "ICK Radio".
 * ICK plans to launch its own online radio, so ICK Radio is the default name.
 */
function radio_name(): string
{
    $name = trim((string) setting('radio_name'));
    if ($name !== '') {
        return $name;
    }
    $streams = active_streams();
    $name = trim((string) ($streams[0]['name'] ?? ''));
    return $name !== '' ? $name : 'ICK Radio';
}

/** The live stream URL, or '' when the station isn't broadcasting yet. */
function radio_url(): string
{
    $streams = active_streams();
    return trim((string) (setting('quick_radio') ?: ($streams[0]['stream_url'] ?? '')));
}

/* ---------------------------------------------------------------------------
 * Byline, comments & reactions (reader engagement)
 * ------------------------------------------------------------------------- */

/** The credit line to show as "By …": custom byline, else the author account. */
function a_byline(array $a): string
{
    $b = trim((string) ($a['byline'] ?? ''));
    if ($b !== '') {
        return $b;
    }
    return trim((string) ($a['author_name'] ?? '')) ?: site_name();
}

/** A stable per-browser token used to limit one reaction per visitor. */
function visitor_token(): string
{
    if (empty($_SESSION['visitor_token'])) {
        $_SESSION['visitor_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['visitor_token'];
}

/** Approved comments for an article, newest first. */
function article_comments(int $articleId): array
{
    $stmt = db()->prepare(
        "SELECT author_name, body, created_at
           FROM article_comments
          WHERE article_id = ? AND status = 'approved'
          ORDER BY created_at DESC"
    );
    $stmt->execute([$articleId]);
    return $stmt->fetchAll();
}

/** [likes, dislikes] counts for an article. */
function reaction_counts(int $articleId): array
{
    $stmt = db()->prepare(
        "SELECT
            SUM(reaction='like')    AS likes,
            SUM(reaction='dislike') AS dislikes
         FROM article_reactions WHERE article_id = ?"
    );
    $stmt->execute([$articleId]);
    $row = $stmt->fetch() ?: [];
    return [(int) ($row['likes'] ?? 0), (int) ($row['dislikes'] ?? 0)];
}

/** This visitor's existing reaction on an article ('like'|'dislike'|null). */
function my_reaction(int $articleId): ?string
{
    $stmt = db()->prepare('SELECT reaction FROM article_reactions WHERE article_id = ? AND visitor = ? LIMIT 1');
    $stmt->execute([$articleId, visitor_token()]);
    return $stmt->fetchColumn() ?: null;
}

/**
 * Normalise a Google Map embed setting into a plain src URL.
 * Accepts either the raw embed URL or a full <iframe ... src="URL" ...> that
 * the admin pasted; returns '' if it doesn't look like a Google Maps embed.
 */
function map_embed_src(?string $raw): string
{
    $raw = trim((string) $raw);
    if ($raw === '') {
        return '';
    }
    // If they pasted a whole iframe, pull out the src attribute.
    if (stripos($raw, '<iframe') !== false && preg_match('~src=["\']([^"\']+)["\']~i', $raw, $m)) {
        $raw = $m[1];
    }
    // Only allow Google Maps embed URLs (avoid arbitrary embedded content).
    if (preg_match('~^https://(www\.)?google\.[a-z.]+/maps/embed~i', $raw)) {
        return $raw;
    }
    return '';
}

/* ---------------------------------------------------------------------------
 * Search
 * ------------------------------------------------------------------------- */

/**
 * Full-text-ish search of published articles in the CURRENT language: matches
 * the title, excerpt or body for the active language. Returns [rows, total].
 */
function search_articles(string $q, int $limit = 12, int $offset = 0): array
{
    $q = trim($q);
    if ($q === '') {
        return [[], 0];
    }
    $lang = current_lang();
    $like = '%' . $q . '%';
    // Only search the active-language columns so results stay in that language.
    $match = "(a.title_$lang LIKE ? OR a.excerpt_$lang LIKE ? OR a.body_$lang LIKE ?)";
    $base  = "FROM articles a
              LEFT JOIN categories c ON c.id = a.category_id
             WHERE a.status='published' AND " . lang_filter_sql('a') . " AND $match";

    $countStmt = db()->prepare("SELECT COUNT(*) $base");
    $countStmt->execute([$like, $like, $like]);
    $total = (int) $countStmt->fetchColumn();

    $sql = "SELECT a.*, c.name AS category_name, c.name_rw AS category_name_rw,
                   c.name_fr AS category_name_fr, c.slug AS category_slug
            $base
            ORDER BY COALESCE(a.published_at, a.created_at) DESC
            LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
    $stmt = db()->prepare($sql);
    $stmt->execute([$like, $like, $like]);
    return [$stmt->fetchAll(), $total];
}
