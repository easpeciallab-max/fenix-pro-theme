<?php
/**
 * Insert approved v3 article images into local HTML drafts.
 *
 * Requires wordpress-media-map.json generated after the files are uploaded.
 * The marker comments make repeated runs idempotent.
 *
 * Usage:
 *   php content-drafts/tools/apply-v3-media-to-articles.php
 */

declare(strict_types=1);

$draftRoot    = dirname(__DIR__);
$mediaPlan    = $draftRoot . '/generated/media-v3/media-plan.json';
$wordpressMap = $draftRoot . '/generated/media-v3/wordpress-media-map.json';
$outputPlan   = $draftRoot . '/generated/media-v3/wordpress-update-plan.json';

foreach (array($mediaPlan, $wordpressMap) as $requiredFile) {
    if (!is_file($requiredFile)) {
        fwrite(STDERR, "Missing required file: {$requiredFile}\n");
        exit(1);
    }
}

$plan = json_decode((string) file_get_contents($mediaPlan), true);
$map  = json_decode((string) file_get_contents($wordpressMap), true);

if (
    !is_array($plan)
    || !isset($plan['items'])
    || !is_array($plan['items'])
    || !is_array($map)
    || !isset($map['items'])
    || !is_array($map['items'])
) {
    fwrite(STDERR, "Invalid media plan or WordPress media map.\n");
    exit(1);
}

$wordpressByFilename = array();
foreach ($map['items'] as $media) {
    $wordpressByFilename[(string) $media['filename']] = $media;
}

$updates = array();
$errors  = array();

foreach ($plan['items'] as $item) {
    $id      = (int) $item['id'];
    $slug    = (string) $item['slug'];
    $path    = $draftRoot . '/articles/' . sprintf('%03d-%s.html', $id, $slug);

    if (!is_file($path)) {
        $errors[] = "Article {$id}: missing draft {$path}.";
        continue;
    }

    $raw = (string) file_get_contents($path);
    if (!preg_match('/\A(---\R.*?\R---\R)(.*)\z/su', $raw, $parts)) {
        $errors[] = "Article {$id}: invalid front matter.";
        continue;
    }

    $frontMatter = $parts[1];
    $body        = stripV3Media($parts[2]);

    foreach ($item['assets']['interiors'] as $asset) {
        $filename  = (string) $asset['filename'];
        $placement = (string) $asset['placement'];
        $media     = $wordpressByFilename[$filename] ?? null;

        if ($media === null) {
            $errors[] = "Article {$id}: {$filename} is missing from WordPress.";
            continue 2;
        }

        $figure = buildFigure($asset, $media);
        $pattern = '/(<h2\b[^>]*\bid=(["\'])'
            . preg_quote($placement, '/')
            . '\2[^>]*>.*?<\/h2>)/isu';

        $updatedBody = preg_replace($pattern, '$1' . PHP_EOL . PHP_EOL . $figure, $body, 1, $count);
        if ($updatedBody === null || $count !== 1) {
            $errors[] = "Article {$id}: unable to insert {$filename} after #{$placement}.";
            continue 2;
        }
        $body = $updatedBody;
    }

    $coverFilename = (string) $item['assets']['cover']['filename'];
    $coverMedia    = $wordpressByFilename[$coverFilename] ?? null;
    if ($coverMedia === null) {
        $errors[] = "Article {$id}: cover {$coverFilename} is missing from WordPress.";
        continue;
    }

    $body = trim($body) . PHP_EOL;
    if (file_put_contents($path, $frontMatter . $body) === false) {
        $errors[] = "Article {$id}: unable to update {$path}.";
        continue;
    }

    $updates[] = array(
        'id'                => $id,
        'post_id'           => 10000 + $id,
        'slug'              => $slug,
        'title'             => $item['title'],
        'publish_at'        => $item['publish_at'],
        'content_file'      => 'articles/' . basename($path),
        'content'           => $body,
        'featured_media_id' => (int) $coverMedia['id'],
        'featured_filename' => $coverFilename,
        'featured_url'      => (string) $coverMedia['url'],
        'interior_media'    => array_map(
            static function (array $asset) use ($wordpressByFilename): array {
                $media = $wordpressByFilename[$asset['filename']];
                return array(
                    'id'        => (int) $media['id'],
                    'filename'  => $asset['filename'],
                    'url'       => $media['url'],
                    'placement' => $asset['placement'],
                    'alt'       => $asset['alt'],
                    'title'     => $asset['title'],
                );
            },
            $item['assets']['interiors']
        ),
    );
}

if ($errors !== array()) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    exit(1);
}

$output = array(
    'version'      => 3,
    'generated_at' => gmdate(DATE_ATOM),
    'timezone'     => 'Asia/Bangkok',
    'updates'      => $updates,
);

$encoded = json_encode(
    $output,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

if ($encoded === false || file_put_contents($outputPlan, $encoded . PHP_EOL) === false) {
    fwrite(STDERR, "Unable to write {$outputPlan}.\n");
    exit(1);
}

fwrite(
    STDOUT,
    sprintf(
        "Updated %d article drafts and wrote %s.\n",
        count($updates),
        $outputPlan
    )
);

function stripV3Media(string $html): string
{
    return (string) preg_replace(
        '/\R*<!-- fenix-media-v3:[^>]+ -->.*?<!-- \/fenix-media-v3 -->\R*/isu',
        PHP_EOL . PHP_EOL,
        $html
    );
}

function buildFigure(array $asset, array $media): string
{
    $filename = htmlspecialchars((string) $asset['filename'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $url      = htmlspecialchars((string) $media['url'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $alt      = htmlspecialchars((string) $asset['alt'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $title    = htmlspecialchars((string) $asset['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $mediaId  = (int) $media['id'];

    return sprintf(
        '<!-- fenix-media-v3:%1$s -->' . PHP_EOL
        . '<figure class="wp-block-image size-full fenix-article-media">'
        . '<img src="%2$s" alt="%3$s" title="%4$s" class="wp-image-%5$d" '
        . 'width="1200" height="675" loading="lazy" decoding="async">'
        . '</figure>' . PHP_EOL
        . '<!-- /fenix-media-v3 -->',
        $filename,
        $url,
        $alt,
        $title,
        $mediaId
    );
}
