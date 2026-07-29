<?php
/**
 * Convert rendered article-media PNG files to optimized WebP assets.
 *
 * Usage:
 *   php content-drafts/tools/convert-article-media.php --start=1 --limit=50
 */

declare(strict_types=1);

$options   = getopt('', array('start::', 'limit::'));
$startId   = max(1, (int) ($options['start'] ?? 1));
$limit     = max(1, (int) ($options['limit'] ?? 50));
$root      = dirname(__DIR__);
$outputDir = $root . '/generated/media-v2';
$planFile  = $outputDir . '/media-plan.json';
$pngDir    = $outputDir . '/png-preview';
$coverDir  = $outputDir . '/covers';
$imageDir  = $outputDir . '/article-images';

if (!function_exists('imagewebp')) {
    fwrite(STDERR, "PHP GD with WebP support is required.\n");
    exit(1);
}

foreach (array($coverDir, $imageDir) as $directory) {
    if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
        fwrite(STDERR, "Unable to create directory: {$directory}\n");
        exit(1);
    }
}

$plan  = json_decode((string) file_get_contents($planFile), true, 512, JSON_THROW_ON_ERROR);
$items = array_values(
    array_filter(
        $plan['items'],
        static fn(array $item): bool => $item['id'] >= $startId && $item['id'] < ($startId + $limit)
    )
);

foreach ($items as $item) {
    foreach (array('cover', 'context', 'summary', 'checklist') as $type) {
        $source = sprintf('%s/%s-%s.png', $pngDir, $item['slug'], $type);
        $asset  = $item['assets'][$type];
        $target = ($type === 'cover' ? $coverDir : $imageDir) . '/' . $asset['filename'];

        if (!is_file($source)) {
            fwrite(STDERR, "Missing rendered PNG: {$source}\n");
            exit(1);
        }

        $image = imagecreatefrompng($source);

        if ($image === false) {
            fwrite(STDERR, "Unable to read PNG: {$source}\n");
            exit(1);
        }

        $width  = (int) $asset['width'];
        $height = (int) $asset['height'];

        if (imagesx($image) !== $width || imagesy($image) !== $height) {
            $resized = imagecreatetruecolor($width, $height);
            imagecopyresampled(
                $resized,
                $image,
                0,
                0,
                0,
                0,
                $width,
                $height,
                imagesx($image),
                imagesy($image)
            );
            imagedestroy($image);
            $image = $resized;
        }

        if (!imagewebp($image, $target, 84)) {
            imagedestroy($image);
            fwrite(STDERR, "Unable to write WebP: {$target}\n");
            exit(1);
        }

        imagedestroy($image);
        fwrite(STDOUT, sprintf("Converted %02d %s -> %s\n", $item['id'], $type, $target));
    }
}

fwrite(STDOUT, sprintf("Converted %d articles / %d assets\n", count($items), count($items) * 4));
