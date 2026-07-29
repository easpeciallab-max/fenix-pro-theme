<?php
/**
 * Compose generated cover backgrounds with deterministic FENIX PRO typography.
 *
 * Usage:
 *   php content-drafts/tools/compose-ai-article-covers.php --start=11 --limit=10
 */

declare(strict_types=1);

$options   = getopt('', array('start::', 'limit::'));
$startId   = max(1, (int) ($options['start'] ?? 11));
$limit     = max(1, (int) ($options['limit'] ?? 10));
$root      = dirname(__DIR__);
$planFile  = $root . '/generated/media-v2/media-plan.json';
$sourceDir = $root . '/generated/media-v3/cover-backgrounds';
$outputDir = $root . '/generated/media-v3/covers';
$logoFile  = dirname($root) . '/fenix-pro/assets/img/logo.png';
$fontBold  = 'C:/Windows/Fonts/tahomabd.ttf';
$fontBody  = 'C:/Windows/Fonts/tahoma.ttf';

if (!extension_loaded('gd') || !function_exists('imagewebp')) {
    fwrite(STDERR, "PHP GD with WebP support is required.\n");
    exit(1);
}

foreach (array($planFile, $logoFile, $fontBold, $fontBody) as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, "Missing required file: {$requiredFile}\n");
        exit(1);
    }
}

if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
    fwrite(STDERR, "Unable to create output directory: {$outputDir}\n");
    exit(1);
}

/**
 * Return the rendered width of one UTF-8 string.
 */
function fenix_text_width(string $text, string $font, int $size): int
{
    $box = imagettfbbox($size, 0, $font, $text);

    if ($box === false) {
        return 0;
    }

    return (int) abs($box[2] - $box[0]);
}

/**
 * Wrap UTF-8 text to a pixel width without splitting Latin words.
 *
 * @return string[]
 */
function fenix_wrap_text(string $text, string $font, int $size, int $maxWidth): array
{
    $tokens = preg_split('/(\s+)/u', trim($text), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $lines  = array();
    $line   = '';

    foreach ($tokens ?: array() as $token) {
        $candidate = $line . $token;

        if ($line !== '' && fenix_text_width($candidate, $font, $size) > $maxWidth) {
            $lines[] = trim($line);
            $line    = ltrim($token);
            continue;
        }

        $line = $candidate;
    }

    if (trim($line) !== '') {
        $lines[] = trim($line);
    }

    return $lines;
}

/**
 * Find a font size that keeps the title inside the requested line count.
 *
 * @return array{size: int, lines: string[]}
 */
function fenix_fit_text(
    string $text,
    string $font,
    int $maxWidth,
    int $maxLines,
    int $startSize,
    int $minimumSize
): array {
    for ($size = $startSize; $size >= $minimumSize; $size -= 2) {
        $lines = fenix_wrap_text($text, $font, $size, $maxWidth);
        if (count($lines) <= $maxLines) {
            return array('size' => $size, 'lines' => $lines);
        }
    }

    return array(
        'size'  => $minimumSize,
        'lines' => array_slice(fenix_wrap_text($text, $font, $minimumSize, $maxWidth), 0, $maxLines),
    );
}

/**
 * Draw text with a subtle black shadow for small-screen readability.
 */
function fenix_draw_text(
    GdImage $image,
    int $size,
    int $x,
    int $y,
    int $color,
    string $font,
    string $text
): void {
    $shadow = imagecolorallocatealpha($image, 0, 0, 0, 32);
    imagettftext($image, $size, 0, $x + 2, $y + 3, $shadow, $font, $text);
    imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
}

/**
 * Center-crop an image to a destination canvas.
 */
function fenix_cover_crop(GdImage $source, int $width, int $height): GdImage
{
    $sourceWidth  = imagesx($source);
    $sourceHeight = imagesy($source);
    $sourceRatio  = $sourceWidth / $sourceHeight;
    $targetRatio  = $width / $height;

    if ($sourceRatio > $targetRatio) {
        $cropHeight = $sourceHeight;
        $cropWidth  = (int) round($sourceHeight * $targetRatio);
        $sourceX    = (int) floor(($sourceWidth - $cropWidth) / 2);
        $sourceY    = 0;
    } else {
        $cropWidth  = $sourceWidth;
        $cropHeight = (int) round($sourceWidth / $targetRatio);
        $sourceX    = 0;
        $sourceY    = (int) floor(($sourceHeight - $cropHeight) / 2);
    }

    $canvas = imagecreatetruecolor($width, $height);
    imagecopyresampled(
        $canvas,
        $source,
        0,
        0,
        $sourceX,
        $sourceY,
        $width,
        $height,
        $cropWidth,
        $cropHeight
    );

    return $canvas;
}

$plan  = json_decode((string) file_get_contents($planFile), true, 512, JSON_THROW_ON_ERROR);
$items = array_values(
    array_filter(
        $plan['items'],
        static fn(array $item): bool => $item['id'] >= $startId && $item['id'] < ($startId + $limit)
    )
);
$logo = imagecreatefrompng($logoFile);

if (!$logo instanceof GdImage) {
    fwrite(STDERR, "Unable to open logo: {$logoFile}\n");
    exit(1);
}

foreach ($items as $item) {
    $slug       = (string) $item['slug'];
    $sourceFile = "{$sourceDir}/{$slug}-cover-bg.png";
    $targetFile = "{$outputDir}/{$slug}-cover.webp";

    if (!is_file($sourceFile)) {
        fwrite(STDERR, "Missing cover background: {$sourceFile}\n");
        exit(1);
    }

    $source = imagecreatefrompng($sourceFile);

    if (!$source instanceof GdImage) {
        fwrite(STDERR, "Unable to open cover background: {$sourceFile}\n");
        exit(1);
    }

    $cover = fenix_cover_crop($source, 1200, 630);
    imagedestroy($source);

    imagealphablending($cover, true);
    imagesavealpha($cover, true);

    $white      = imagecolorallocate($cover, 248, 248, 250);
    $muted      = imagecolorallocate($cover, 186, 188, 197);
    $orange     = imagecolorallocate($cover, 255, 151, 42);
    $deepOrange = imagecolorallocate($cover, 255, 111, 20);
    $blackWash  = imagecolorallocatealpha($cover, 0, 0, 0, 38);

    imagefilledrectangle($cover, 0, 0, 675, 630, $blackWash);

    $logoSize = 104;
    imagecopyresampled(
        $cover,
        $logo,
        1062,
        28,
        0,
        0,
        $logoSize,
        $logoSize,
        imagesx($logo),
        imagesy($logo)
    );

    fenix_draw_text($cover, 17, 58, 58, $orange, $fontBold, 'FENIX PRO EA');
    fenix_draw_text($cover, 12, 58, 82, $muted, $fontBody, 'MT5 KNOWLEDGE SERIES');

    $title = strtoupper(trim((string) $item['cover_title']));
    $fit   = fenix_fit_text($title, $fontBold, 575, 3, 58, 38);
    $y     = 180;

    foreach ($fit['lines'] as $index => $line) {
        $color = $index === 0 ? $white : $orange;
        fenix_draw_text($cover, $fit['size'], 58, $y, $color, $fontBold, $line);
        $y += $fit['size'] + 20;
    }

    $subtitle = strtoupper(trim((string) $item['cover_subtitle']));
    if ($subtitle !== '') {
        $subFit = fenix_fit_text($subtitle, $fontBold, 575, 2, 34, 25);
        $y     += 14;

        foreach ($subFit['lines'] as $line) {
            fenix_draw_text($cover, $subFit['size'], 58, $y, $white, $fontBold, $line);
            $y += $subFit['size'] + 15;
        }
    }

    imagefilledrectangle($cover, 58, 566, 292, 570, $deepOrange);
    fenix_draw_text($cover, 13, 58, 603, $muted, $fontBody, 'FENIXPRO-TH.COM');

    if (!imagewebp($cover, $targetFile, 88)) {
        imagedestroy($cover);
        fwrite(STDERR, "Unable to write WebP: {$targetFile}\n");
        exit(1);
    }

    imagedestroy($cover);
    fwrite(STDOUT, sprintf("Composed %02d %s\n", $item['id'], $targetFile));
}

imagedestroy($logo);
fwrite(STDOUT, sprintf("Composed %d cover(s)\n", count($items)));
