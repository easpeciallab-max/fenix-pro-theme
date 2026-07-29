<?php
/**
 * Build the approved v3 article-media plan for articles 11-50.
 *
 * The plan is the source of truth for:
 * - the featured image assigned to each article;
 * - the three interior images assigned to exact H2 section IDs;
 * - WordPress attachment title, alt text, caption, and description.
 *
 * Usage:
 *   php content-drafts/tools/build-v3-media-plan.php
 */

declare(strict_types=1);

$draftRoot    = dirname(__DIR__);
$v2PlanPath   = $draftRoot . '/generated/media-v2/media-plan.json';
$jobsPath     = __DIR__ . '/process-v3-article-images.php';
$v3Root       = $draftRoot . '/generated/media-v3';
$coverDir     = $v3Root . '/covers';
$interiorDir  = $v3Root . '/article-images';
$outputPath   = $v3Root . '/media-plan.json';

foreach (array($v2PlanPath, $jobsPath) as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, "Missing required file: {$requiredFile}\n");
        exit(1);
    }
}

$v2Plan = json_decode((string) file_get_contents($v2PlanPath), true);
if (!is_array($v2Plan) || !isset($v2Plan['items']) || !is_array($v2Plan['items'])) {
    fwrite(STDERR, "Invalid v2 media plan.\n");
    exit(1);
}

/*
 * Placements follow the output order in process-v3-article-images.php.
 * Every value must match an H2 id in the corresponding article.
 */
$placements = array(
    11 => array('difference', 'fields', 'support'),
    12 => array('how', 'setting', 'check'),
    13 => array('contains', 'version', 'save'),
    14 => array('backup', 'steps', 'rollback'),
    15 => array('meaning', 'server', 'account'),
    16 => array('clocks', 'effects', 'dst'),
    17 => array('choose', 'operations', 'resources'),
    18 => array('prepare', 'run', 'report'),
    19 => array('modes', 'real', 'spread'),
    20 => array('purpose', 'split', 'leakage'),
    21 => array('windows', 'design', 'read'),
    22 => array('types', 'outputs', 'decisions'),
    23 => array('commission', 'stress', 'slippage'),
    24 => array('regimes', 'sample', 'stop'),
    25 => array('align', 'differences', 'process'),
    26 => array('balance', 'drawdown', 'shape'),
    27 => array('sequence', 'estimate', 'reduce'),
    28 => array('marginlevel', 'terms', 'plan'),
    29 => array('why', 'interaction', 'test'),
    30 => array('timezone', 'actions', 'test'),
    31 => array('currency', 'orders', 'measure'),
    32 => array('hidden', 'measure', 'stress'),
    33 => array('compare', 'calculation', 'choose'),
    34 => array('compare', 'sl', 'trail'),
    35 => array('first', 'diagnose', 'restart'),
    36 => array('workload', 'cpu', 'network'),
    37 => array('terms', 'compare', 'improve'),
    38 => array('plan', 'resources', 'restart'),
    39 => array('multiple', 'task', 'test'),
    40 => array('states', 'disconnect', 'verify'),
    41 => array('logs', 'layers', 'prevent'),
    42 => array('account', 'backup', 'incident'),
    43 => array('models', 'broker', 'choose'),
    44 => array('identity', 'evidence', 'support'),
    45 => array('models', 'migration', 'availability'),
    46 => array('deliverable', 'refund', 'evidence'),
    47 => array('visual', 'report', 'questions'),
    48 => array('server', 'risk', 'testing'),
    49 => array('tradelist', 'equity', 'stress'),
    50 => array('delivery', 'acceptance', 'backup'),
);

$jobOutputs = parseJobOutputs((string) file_get_contents($jobsPath));
$items      = array();
$errors     = array();

foreach ($v2Plan['items'] as $sourceItem) {
    $id = (int) ($sourceItem['id'] ?? 0);
    if ($id < 11 || $id > 50) {
        continue;
    }

    if (!isset($placements[$id], $jobOutputs[$id])) {
        $errors[] = "Article {$id}: missing placement or image jobs.";
        continue;
    }

    $outputs = $jobOutputs[$id];
    if (count($outputs) !== 3 || count($placements[$id]) !== 3) {
        $errors[] = "Article {$id}: expected exactly three interior images.";
        continue;
    }

    $sectionsById = array();
    foreach (($sourceItem['sections'] ?? array()) as $section) {
        $sectionId = (string) ($section['id'] ?? '');
        if ($sectionId !== '') {
            $sectionsById[$sectionId] = $section;
        }
    }

    $coverFilename = (string) ($sourceItem['assets']['cover']['filename'] ?? '');
    $coverPath     = $coverDir . '/' . $coverFilename;
    if ($coverFilename === '' || !is_file($coverPath)) {
        $errors[] = "Article {$id}: missing cover {$coverFilename}.";
        continue;
    }

    $coverSize = getimagesize($coverPath);
    if ($coverSize === false || $coverSize[0] !== 1200 || $coverSize[1] !== 630) {
        $errors[] = "Article {$id}: cover must be 1200x630.";
    }

    $cover = $sourceItem['assets']['cover'];
    $cover['width']       = 1200;
    $cover['height']      = 630;
    $cover['placement']   = 'featured';
    $cover['source_path'] = relativePath($draftRoot, $coverPath);

    $interiors = array();
    foreach ($outputs as $index => $filename) {
        $placement = $placements[$id][$index];
        if (!isset($sectionsById[$placement])) {
            $errors[] = "Article {$id}: section #{$placement} does not exist.";
            continue;
        }

        $imagePath = $interiorDir . '/' . $filename;
        if (!is_file($imagePath)) {
            $errors[] = "Article {$id}: missing interior image {$filename}.";
            continue;
        }

        $size = getimagesize($imagePath);
        if ($size === false || $size[0] !== 1200 || $size[1] !== 675) {
            $errors[] = "Article {$id}: {$filename} must be 1200x675.";
        }

        $sectionTitle = cleanText((string) $sectionsById[$placement]['title']);
        $articleTitle = cleanText((string) $sourceItem['title']);
        $altText      = 'ภาพประกอบหัวข้อ ' . $sectionTitle;

        $interiors[] = array(
            'filename'    => $filename,
            'placement'   => $placement,
            'width'       => 1200,
            'height'      => 675,
            'alt'         => $altText,
            'title'       => $sectionTitle,
            'caption'     => $altText,
            'description' => 'ภาพประกอบบทความ ' . $articleTitle . ' สำหรับอธิบายหัวข้อ ' . $sectionTitle,
            'source_path' => relativePath($draftRoot, $imagePath),
        );
    }

    $item                  = $sourceItem;
    $item['assets']        = array(
        'cover'     => $cover,
        'interiors' => $interiors,
    );
    $items[]               = $item;
}

if ($errors !== array()) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    exit(1);
}

usort(
    $items,
    static fn(array $left, array $right): int => ((int) $left['id']) <=> ((int) $right['id'])
);

if (count($items) !== 40) {
    fwrite(STDERR, 'Expected 40 article entries, found ' . count($items) . ".\n");
    exit(1);
}

$output = array(
    'version'      => 3,
    'generated_at' => gmdate(DATE_ATOM),
    'timezone'     => 'Asia/Bangkok',
    'items'        => $items,
);

$encoded = json_encode(
    $output,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

if ($encoded === false || file_put_contents($outputPath, $encoded . PHP_EOL) === false) {
    fwrite(STDERR, "Unable to write {$outputPath}.\n");
    exit(1);
}

fwrite(
    STDOUT,
    sprintf(
        "Wrote %s with %d covers and %d interior images.\n",
        $outputPath,
        count($items),
        array_sum(array_map(static fn(array $item): int => count($item['assets']['interiors']), $items))
    )
);

/**
 * Parse image output filenames without executing the processing script.
 *
 * @return array<int, array<int, string>>
 */
function parseJobOutputs(string $source): array
{
    $outputs = array();
    if (!preg_match_all('/^\s{4}(\d+)\s*=>\s*array\((.*?)(?=^\s{4}\d+\s*=>\s*array\(|^\);)/ms', $source, $blocks, PREG_SET_ORDER)) {
        throw new RuntimeException('Unable to parse v3 image jobs.');
    }

    foreach ($blocks as $block) {
        $id = (int) $block[1];
        if ($id < 11 || $id > 50) {
            continue;
        }

        preg_match_all("/'output'\s*=>\s*'([^']+)'/", $block[2], $matches);
        foreach ($matches[1] as $filename) {
            if (str_ends_with($filename, '-cover-bg.png')) {
                continue;
            }
            $outputs[$id][] = $filename;
        }
    }

    return $outputs;
}

function cleanText(string $value): string
{
    $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim((string) preg_replace('/\s+/u', ' ', $value));
}

function relativePath(string $base, string $path): string
{
    $base = str_replace('\\', '/', rtrim($base, '\\/'));
    $path = str_replace('\\', '/', $path);
    return ltrim((string) preg_replace('~^' . preg_quote($base, '~') . '/?~', '', $path), '/');
}
