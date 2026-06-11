<?php
/**
 * Avatar image handling for UR KAMINUZA.
 *
 * Cropper.js produces the final square client-side and posts it as a base64
 * data URL ("data:image/png;base64,...."). We decode, validate, and store it.
 *
 * If the GD extension is enabled we re-encode the image to a normalised
 * 400x400 PNG (defence-in-depth: strips anything non-image). If GD is not
 * available we save the validated bytes directly.
 */
 
const AVATAR_SIZE      = 400;                 // final square size in px
const AVATAR_MAX_BYTES = 2 * 1024 * 1024;     // 2 MB

/**
 * Save a base64 data-URL avatar for a user. Returns the stored relative path
 * (e.g. "uploads/avatars/u5_abc.png") or throws RuntimeException on failure.
 */
function save_avatar_from_data_url(int $userId, string $dataUrl): string
{
    if (!preg_match('#^data:image/(png|jpeg|jpg|webp);base64,#i', $dataUrl, $m)) {
        throw new RuntimeException('Unsupported image format. Use PNG, JPG or WebP.');
    }

    $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
    $binary = base64_decode($base64, true);
    if ($binary === false) {
        throw new RuntimeException('Could not decode the image data.');
    }
    if (strlen($binary) > AVATAR_MAX_BYTES) {
        throw new RuntimeException('Image is larger than 2 MB after cropping.');
    }

    // Validate it really is an image.
    $info = @getimagesizefromstring($binary);
    if ($info === false) {
        throw new RuntimeException('The uploaded file is not a valid image.');
    }

    $dir = BASE_PATH . '/uploads/avatars';
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Upload folder is not writable.');
    }

    $filename = 'u' . $userId . '_' . bin2hex(random_bytes(6)) . '.png';
    $absPath  = $dir . '/' . $filename;
    $relPath  = 'uploads/avatars/' . $filename;

    if (function_exists('imagecreatefromstring')) {
        // GD path: normalise to a clean AVATAR_SIZE square PNG.
        $src = @imagecreatefromstring($binary);
        if (!$src) {
            throw new RuntimeException('Could not process the image.');
        }
        $dst = imagecreatetruecolor(AVATAR_SIZE, AVATAR_SIZE);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled(
            $dst, $src,
            0, 0, 0, 0,
            AVATAR_SIZE, AVATAR_SIZE,
            imagesx($src), imagesy($src)
        );
        imagepng($dst, $absPath);
        imagedestroy($src);
        imagedestroy($dst);
    } else {
        // No GD: save the validated bytes directly (Cropper already squared it).
        if (file_put_contents($absPath, $binary) === false) {
            throw new RuntimeException('Could not save the image.');
        }
    }

    return $relPath;
}

/** Delete a previously stored avatar file (best effort). */
function delete_avatar_file(?string $relPath): void
{
    delete_upload_file($relPath);
}

/** Delete any uploaded file under /uploads (best effort, path-guarded). */
function delete_upload_file(?string $relPath): void
{
    if (!$relPath) {
        return;
    }
    $abs = BASE_PATH . '/' . ltrim($relPath, '/');
    $uploadsRoot = realpath(BASE_PATH . '/uploads');
    if (is_file($abs) && $uploadsRoot && strpos(realpath($abs) ?: '', $uploadsRoot) === 0) {
        @unlink($abs);
    }
}

const ARTICLE_IMG_MAX_BYTES = 5 * 1024 * 1024; // 5 MB

/**
 * Validate and store ONE uploaded image file under uploads/articles.
 * Internal worker shared by the featured image and the gallery.
 * Returns the stored relative path. Throws RuntimeException on error.
 */
function store_article_image_file(array $file, string $label = 'Image'): string
{
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException("$label is too large (server limit reached).");
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("$label upload failed (error code {$file['error']}).");
    }
    if ($file['size'] > ARTICLE_IMG_MAX_BYTES) {
        throw new RuntimeException("$label must be 5 MB or smaller.");
    }

    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        throw new RuntimeException("$label is not a valid image file.");
    }

    $ext = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_WEBP => 'webp',
        IMAGETYPE_GIF  => 'gif',
    ][$info[2]] ?? null;
    if ($ext === null) {
        throw new RuntimeException("$label must be a JPG, PNG, WebP or GIF image.");
    }

    $dir = BASE_PATH . '/uploads/articles';
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Upload folder is not writable.');
    }

    $filename = date('Ymd') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $abs      = $dir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $abs)
        // Fallback for non-HTTP contexts (e.g. CLI tests).
        && !rename($file['tmp_name'], $abs)) {
        throw new RuntimeException('Could not save the uploaded image.');
    }

    return 'uploads/articles/' . $filename;
}

/**
 * Save an uploaded article featured image. Returns the stored relative path,
 * or null if no file was uploaded. Throws RuntimeException on a real error.
 */
function save_article_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null; // optional field, nothing uploaded
    }
    return store_article_image_file($file, 'Featured image');
}

/**
 * Save MULTIPLE gallery images from a normalised list of file entries
 * (each like a single $_FILES item). Returns the list of stored paths.
 * Throws RuntimeException on the first invalid file.
 */
function save_gallery_images(array $files): array
{
    $paths = [];
    foreach ($files as $file) {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $paths[] = store_article_image_file($file, 'Gallery image');
    }
    return $paths;
}

/**
 * Re-shape PHP's awkward multi-file $_FILES['x'] (arrays per key) into a
 * simple list of single-file arrays.
 */
function normalize_files_array(array $field): array
{
    if (!isset($field['name'])) {
        return [];
    }
    if (!is_array($field['name'])) {
        return [$field]; // single file
    }
    $out = [];
    foreach ($field['name'] as $i => $name) {
        $out[] = [
            'name'     => $name,
            'type'     => $field['type'][$i] ?? '',
            'tmp_name' => $field['tmp_name'][$i] ?? '',
            'error'    => $field['error'][$i] ?? UPLOAD_ERR_NO_FILE,
            'size'     => $field['size'][$i] ?? 0,
        ];
    }
    return $out;
}

// Allowed media uploads. The real cap is also bounded by php.ini
// (upload_max_filesize / post_max_size — currently 40M on this XAMPP).
const MEDIA_MAX_BYTES = 40 * 1024 * 1024; // 40 MB
const MEDIA_EXTENSIONS = [
    // video
    'video/mp4'        => 'mp4',
    'video/webm'       => 'webm',
    'video/ogg'        => 'ogv',
    'video/quicktime'  => 'mov',
    // audio
    'audio/mpeg'       => 'mp3',
    'audio/mp4'        => 'm4a',
    'audio/ogg'        => 'ogg',
    'audio/wav'        => 'wav',
    'audio/x-wav'      => 'wav',
    'audio/webm'       => 'weba',
];

/**
 * Save an uploaded article media file (video or audio).
 * Returns the stored relative path or null if no file was uploaded.
 * Throws RuntimeException on a real error.
 */
function save_article_media(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('The media file is too large (server limit is 40 MB).');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Media upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > MEDIA_MAX_BYTES) {
        throw new RuntimeException('Media file must be 40 MB or smaller.');
    }

    // Detect the real MIME type from content, not the client-supplied name.
    $mime = function_exists('mime_content_type')
        ? (mime_content_type($file['tmp_name']) ?: '')
        : ($file['type'] ?? '');

    $ext = MEDIA_EXTENSIONS[$mime] ?? null;
    if ($ext === null) {
        throw new RuntimeException('Unsupported media type. Use MP4, WebM, MOV (video) or MP3, M4A, OGG, WAV (audio).');
    }

    $dir = BASE_PATH . '/uploads/media';
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Media folder is not writable.');
    }

    $filename = date('Ymd') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $abs      = $dir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $abs) && !rename($file['tmp_name'], $abs)) {
        throw new RuntimeException('Could not save the media file.');
    }

    return 'uploads/media/' . $filename;
}

/** Is this MIME (by extension) a video? Used to classify uploaded media. */
function media_kind_from_path(string $path): string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return in_array($ext, ['mp4', 'webm', 'ogv', 'mov'], true) ? 'video' : 'audio';
}
