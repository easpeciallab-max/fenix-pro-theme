<?php
declare(strict_types=1);

if (!extension_loaded('gd')) {
    fwrite(STDERR, "The GD extension is required.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$planPath = $root . DIRECTORY_SEPARATOR . 'content-drafts' . DIRECTORY_SEPARATOR . 'content-strategy-50.md';
$outputDir = $root . DIRECTORY_SEPARATOR . 'content-drafts' . DIRECTORY_SEPARATOR . 'generated';
$coverDir = $outputDir . DIRECTORY_SEPARATOR . 'covers';
$manifestPath = $outputDir . DIRECTORY_SEPARATOR . 'content-manifest.json';
$logoPath = $root . DIRECTORY_SEPARATOR . 'fenix-pro' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png';
$fontRegular = 'C:\Windows\Fonts\tahoma.ttf';
$fontBold = 'C:\Windows\Fonts\tahomabd.ttf';

foreach ([$planPath, $logoPath, $fontRegular, $fontBold] as $requiredPath) {
    if (!is_file($requiredPath)) {
        fwrite(STDERR, "Missing required file: {$requiredPath}\n");
        exit(1);
    }
}

if (!is_dir($coverDir) && !mkdir($coverDir, 0777, true) && !is_dir($coverDir)) {
    fwrite(STDERR, "Unable to create output directory: {$coverDir}\n");
    exit(1);
}

$clusters = [
    [
        'from' => 1,
        'to' => 8,
        'slug' => 'ea-basics',
        'name' => 'พื้นฐาน EA',
        'accent' => '#36D399',
    ],
    [
        'from' => 9,
        'to' => 17,
        'slug' => 'metatrader5',
        'name' => 'คู่มือ MT5',
        'accent' => '#4DA3FF',
    ],
    [
        'from' => 18,
        'to' => 26,
        'slug' => 'backtest-forward',
        'name' => 'BACKTEST & FORWARD TEST',
        'accent' => '#A78BFA',
    ],
    [
        'from' => 27,
        'to' => 35,
        'slug' => 'risk-management',
        'name' => 'บริหารความเสี่ยง',
        'accent' => '#FF6464',
    ],
    [
        'from' => 36,
        'to' => 43,
        'slug' => 'vps-mt5',
        'name' => 'VPS สำหรับ MT5',
        'accent' => '#34D3D3',
    ],
    [
        'from' => 44,
        'to' => 50,
        'slug' => 'ea-trust',
        'name' => 'เลือกและตรวจสอบ EA',
        'accent' => '#F2C14E',
    ],
];

$plan = file_get_contents($planPath);
if ($plan === false) {
    fwrite(STDERR, "Unable to read content plan.\n");
    exit(1);
}

$items = [];
foreach (preg_split('/\R/u', $plan) as $line) {
    if (!preg_match(
        '/^\|\s*(\d+)\s*\|\s*([0-9]{4}-[0-9]{2}-[0-9]{2}\s+[0-9]{2}:[0-9]{2})\s*\|\s*([^|]+?)\s*\|\s*([^|]+?)\s*\|\s*([^|]+?)\s*\|\s*`([^`]+)`\s*\|\s*([^|]+?)\s*\|\s*`([^`]+)`\s*\|$/u',
        $line,
        $matches
    )) {
        continue;
    }

    $id = (int) $matches[1];
    $cluster = null;
    foreach ($clusters as $candidate) {
        if ($id >= $candidate['from'] && $id <= $candidate['to']) {
            $cluster = $candidate;
            break;
        }
    }

    if ($cluster === null) {
        fwrite(STDERR, "No cluster mapping for article {$id}.\n");
        exit(1);
    }

    $title = trim($matches[4]);
    [$coverTitle, $coverSubtitle] = splitCoverTitle($title);

    $items[] = [
        'id' => $id,
        'publish_at' => trim($matches[2]) . ':00',
        'role' => trim($matches[3]),
        'title' => $title,
        'primary_keyword' => trim($matches[5]),
        'slug' => trim($matches[6]),
        'planned_links' => trim($matches[7]),
        'cover_filename' => trim($matches[8]),
        'cover_title' => $coverTitle,
        'cover_subtitle' => $coverSubtitle,
        'category_slug' => $cluster['slug'],
        'category_name' => $cluster['name'],
        'accent' => $cluster['accent'],
        'content_file' => sprintf('articles/%03d-%s.html', $id, trim($matches[6])),
    ];
}

usort($items, static fn(array $a, array $b): int => $a['id'] <=> $b['id']);

if (count($items) !== 50) {
    fwrite(STDERR, 'Expected 50 content items, found ' . count($items) . ".\n");
    exit(1);
}

foreach ($items as $item) {
    createCover($item, $coverDir, $logoPath, $fontRegular, $fontBold);
}

$manifest = [
    'generated_at' => date(DATE_ATOM),
    'timezone' => 'Asia/Bangkok',
    'article_count' => count($items),
    'cover_size' => ['width' => 1200, 'height' => 630],
    'items' => $items,
];

$encoded = json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
if ($encoded === false || file_put_contents($manifestPath, $encoded . PHP_EOL) === false) {
    fwrite(STDERR, "Unable to write manifest.\n");
    exit(1);
}

fwrite(STDOUT, "Generated " . count($items) . " covers and content manifest.\n");

function splitCoverTitle(string $title): array
{
    foreach (['? ', ': '] as $separator) {
        $position = mb_strpos($title, $separator);
        if ($position !== false) {
            $head = trim(mb_substr($title, 0, $position + 1));
            $tail = trim(mb_substr($title, $position + mb_strlen($separator)));
            return [$head, $tail];
        }
    }

    $words = preg_split('/\s+/u', trim($title));
    if (!is_array($words) || count($words) < 5) {
        return [$title, 'คู่มือจาก FENIX PRO EA'];
    }

    $cut = (int) ceil(count($words) / 2);
    return [
        implode(' ', array_slice($words, 0, $cut)),
        implode(' ', array_slice($words, $cut)),
    ];
}

function createCover(
    array $item,
    string $coverDir,
    string $logoPath,
    string $fontRegular,
    string $fontBold
): void {
    $width = 1200;
    $height = 630;
    $image = imagecreatetruecolor($width, $height);
    imagealphablending($image, true);
    imagesavealpha($image, true);

    $background = color($image, '#09090B');
    $panel = color($image, '#111216');
    $panelLine = color($image, '#2A2C33');
    $text = color($image, '#F7F7F8');
    $muted = color($image, '#A7A9B3');
    $orange = color($image, '#FF7A1A');
    $accent = color($image, $item['accent']);
    $grid = color($image, '#17191E');

    imagefilledrectangle($image, 0, 0, $width, $height, $background);

    for ($x = 0; $x <= $width; $x += 60) {
        imageline($image, $x, 0, $x, $height, $grid);
    }
    for ($y = 0; $y <= $height; $y += 60) {
        imageline($image, 0, $y, $width, $y, $grid);
    }

    imagefilledrectangle($image, 0, 0, 18, $height, $orange);
    imagefilledrectangle($image, 18, 0, 26, $height, $accent);

    imagefilledrectangle($image, 64, 42, 1136, 588, $panel);
    imagerectangle($image, 64, 42, 1136, 588, $panelLine);

    $logo = imagecreatefrompng($logoPath);
    if ($logo === false) {
        fwrite(STDERR, "Unable to open logo.\n");
        exit(1);
    }
    imagecopyresampled($image, $logo, 92, 76, 0, 0, 78, 78, imagesx($logo), imagesy($logo));
    imagedestroy($logo);

    drawText($image, 186, 102, 23, $text, $fontBold, 'FENIX PRO EA');
    drawText($image, 186, 137, 14, $muted, $fontRegular, 'MT5 AUTOMATED TRADING SYSTEM');

    $chipWidth = max(190, textWidth($item['category_name'], $fontBold, 16) + 42);
    roundedRect($image, 92, 183, 92 + $chipWidth, 225, 21, $accent);
    drawText($image, 113, 211, 16, $background, $fontBold, $item['category_name']);

    $titleLines = wrapText($item['cover_title'], $fontBold, 48, 620);
    $titleY = 302;
    foreach (array_slice($titleLines, 0, 2) as $line) {
        drawText($image, 92, $titleY, 48, $text, $fontBold, $line);
        $titleY += 66;
    }

    $subtitleLines = wrapText($item['cover_subtitle'], $fontRegular, 23, 630);
    $subtitleY = max(440, $titleY + 8);
    foreach (array_slice($subtitleLines, 0, 2) as $line) {
        drawText($image, 94, $subtitleY, 23, $muted, $fontRegular, $line);
        $subtitleY += 36;
    }

    drawClusterVisual($image, $item['category_slug'], $accent, $orange, $panelLine);

    drawText(
        $image,
        965,
        555,
        15,
        $muted,
        $fontBold,
        sprintf('%02d / 50', $item['id'])
    );

    $target = $coverDir . DIRECTORY_SEPARATOR . $item['cover_filename'];
    if (!imagewebp($image, $target, 84)) {
        fwrite(STDERR, "Unable to write cover: {$target}\n");
        exit(1);
    }

    imagedestroy($image);
}

function drawClusterVisual($image, string $cluster, int $accent, int $orange, int $line): void
{
    $left = 820;
    $top = 190;
    $right = 1090;
    $bottom = 475;

    roundedRect($image, $left, $top, $right, $bottom, 26, color($image, '#0D0E11'));
    roundedOutline($image, $left, $top, $right, $bottom, 26, $line);

    if ($cluster === 'ea-basics') {
        $points = [[875, 255], [1018, 238], [930, 355], [1034, 398]];
        imageline($image, 875, 255, 1018, 238, $line);
        imageline($image, 875, 255, 930, 355, $line);
        imageline($image, 1018, 238, 1034, 398, $line);
        imageline($image, 930, 355, 1034, 398, $line);
        foreach ($points as $index => [$x, $y]) {
            imagefilledellipse($image, $x, $y, 54, 54, $index === 0 ? $orange : $accent);
            imageellipse($image, $x, $y, 76, 76, $line);
        }
        return;
    }

    if ($cluster === 'metatrader5') {
        roundedOutline($image, 852, 226, 1058, 420, 14, $line);
        imageline($image, 852, 272, 1058, 272, $line);
        imagefilledellipse($image, 875, 250, 10, 10, $orange);
        imagefilledellipse($image, 895, 250, 10, 10, $accent);
        imagefilledellipse($image, 915, 250, 10, 10, $line);
        $candles = [[884, 340, 304], [920, 370, 322], [956, 337, 286], [992, 388, 330], [1028, 320, 278]];
        foreach ($candles as $i => [$x, $low, $high]) {
            imageline($image, $x, $low + 25, $x, $high - 25, $i % 2 === 0 ? $accent : $orange);
            imagefilledrectangle($image, $x - 8, min($low, $high), $x + 8, max($low, $high), $i % 2 === 0 ? $accent : $orange);
        }
        return;
    }

    if ($cluster === 'backtest-forward') {
        imageline($image, 858, 414, 1058, 414, $line);
        imageline($image, 858, 232, 858, 414, $line);
        $plot = [[870, 388], [900, 364], [928, 376], [959, 321], [992, 338], [1020, 282], [1047, 248]];
        for ($i = 1; $i < count($plot); $i++) {
            imageline($image, $plot[$i - 1][0], $plot[$i - 1][1], $plot[$i][0], $plot[$i][1], $accent);
        }
        foreach ($plot as [$x, $y]) {
            imagefilledellipse($image, $x, $y, 13, 13, $orange);
        }
        return;
    }

    if ($cluster === 'risk-management') {
        $shield = [954, 224, 1030, 250, 1020, 354, 954, 420, 888, 354, 878, 250];
        imagefilledpolygon($image, $shield, color($image, '#15171C'));
        imagepolygon($image, $shield, $accent);
        imagefilledrectangle($image, 934, 293, 974, 360, $orange);
        imagefilledellipse($image, 954, 379, 12, 12, $orange);
        return;
    }

    if ($cluster === 'vps-mt5') {
        foreach ([228, 305, 382] as $y) {
            roundedOutline($image, 865, $y, 1054, $y + 54, 10, $line);
            imagefilledellipse($image, 890, $y + 27, 13, 13, $accent);
            imagefilledellipse($image, 916, $y + 27, 13, 13, $orange);
            imagefilledrectangle($image, 967, $y + 20, 1028, $y + 34, $line);
        }
        return;
    }

    roundedOutline($image, 866, 224, 996, 398, 14, $line);
    imageline($image, 892, 268, 970, 268, $accent);
    imageline($image, 892, 306, 970, 306, $line);
    imageline($image, 892, 344, 948, 344, $line);
    imageellipse($image, 1010, 350, 88, 88, $orange);
    imageline($image, 1043, 383, 1072, 414, $orange);
    imageline($image, 988, 350, 1006, 369, $accent);
    imageline($image, 1006, 369, 1034, 330, $accent);
}

function color($image, string $hex): int
{
    $hex = ltrim($hex, '#');
    return imagecolorallocate(
        $image,
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2))
    );
}

function drawText($image, int $x, int $baseline, int $size, int $color, string $font, string $text): void
{
    imagettftext($image, $size, 0, $x, $baseline, $color, $font, $text);
}

function textWidth(string $text, string $font, int $size): int
{
    $box = imagettfbbox($size, 0, $font, $text);
    return abs($box[2] - $box[0]);
}

function wrapText(string $text, string $font, int $size, int $maxWidth): array
{
    $chars = preg_split('//u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
    if (!is_array($chars)) {
        return [$text];
    }

    $lines = [];
    $line = '';
    $lastSpace = -1;

    foreach ($chars as $char) {
        $candidate = $line . $char;
        if (preg_match('/\s/u', $char)) {
            $lastSpace = mb_strlen($candidate) - 1;
        }

        if ($line !== '' && textWidth($candidate, $font, $size) > $maxWidth) {
            if ($lastSpace >= 0) {
                $lines[] = trim(mb_substr($candidate, 0, $lastSpace));
                $line = ltrim(mb_substr($candidate, $lastSpace + 1));
            } else {
                $lines[] = trim($line);
                $line = $char;
            }
            $foundSpace = mb_strrpos($line, ' ');
            $lastSpace = $foundSpace === false ? -1 : $foundSpace;
            continue;
        }

        $line = $candidate;
    }

    if (trim($line) !== '') {
        $lines[] = trim($line);
    }

    return $lines;
}

function roundedRect($image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
{
    imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}

function roundedOutline($image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
{
    imageline($image, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
    imageline($image, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);
    imageline($image, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
    imageline($image, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagearc($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
    imagearc($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color);
    imagearc($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
    imagearc($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color);
}
