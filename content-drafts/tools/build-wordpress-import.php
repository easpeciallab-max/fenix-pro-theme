<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$draftRoot = $root . DIRECTORY_SEPARATOR . 'content-drafts';
$manifestPath = $draftRoot . DIRECTORY_SEPARATOR . 'generated' . DIRECTORY_SEPARATOR . 'content-manifest.json';
$coverSourceDir = $draftRoot . DIRECTORY_SEPARATOR . 'generated' . DIRECTORY_SEPARATOR . 'covers';
$themeCoverDir = $root . DIRECTORY_SEPARATOR . 'fenix-pro' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'covers';
$importDir = $draftRoot . DIRECTORY_SEPARATOR . 'generated' . DIRECTORY_SEPARATOR . 'imports';
$siteUrl = 'https://fenixpro-th.com';
$themeCoverUrl = $siteUrl . '/wp-content/themes/fenix-pro/assets/content/covers';
$author = 'adminwp';
$minimumContentCharacters = 4500;

if (!is_file($manifestPath)) {
    fwrite(STDERR, "Missing manifest. Run build-content-assets.php first.\n");
    exit(1);
}

foreach ([$themeCoverDir, $importDir] as $directory) {
    if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
        fwrite(STDERR, "Unable to create directory: {$directory}\n");
        exit(1);
    }
}

$manifest = json_decode((string) file_get_contents($manifestPath), true);
if (!is_array($manifest) || !isset($manifest['items']) || !is_array($manifest['items'])) {
    fwrite(STDERR, "Invalid content manifest.\n");
    exit(1);
}

$scheduledBySlug = [];
foreach ($manifest['items'] as $manifestItem) {
    $scheduledBySlug[$manifestItem['slug']] = $manifestItem['publish_at'];
}

$articles = [];
$errors = [];
foreach ($manifest['items'] as $item) {
    $articlePath = $draftRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $item['content_file']);
    if (!is_file($articlePath)) {
        continue;
    }

    $parsed = parseArticle($articlePath);
    foreach (['seo_title', 'meta_description', 'excerpt', 'cover_alt'] as $required) {
        if (!isset($parsed['meta'][$required]) || trim($parsed['meta'][$required]) === '') {
            $errors[] = basename($articlePath) . ": missing {$required}";
        }
    }

    if (preg_match('/\[(?:ระบุ|ใส่|แก้)[^\]]*\]|\b(?:placeholder|todo)\b/iu', $parsed['body'])) {
        $errors[] = basename($articlePath) . ': contains placeholder text';
    }

    if (preg_match('/<h1\b/iu', $parsed['body'])) {
        $errors[] = basename($articlePath) . ': body must not contain H1';
    }

    if (mb_strlen(strip_tags($parsed['body'])) < $minimumContentCharacters) {
        $errors[] = basename($articlePath) . ': content is shorter than the minimum quality threshold';
    }

    if (preg_match_all(
        '~href=["\']https://fenixpro-th\.com/([^/"\'#?]+)/?["\']~iu',
        $parsed['body'],
        $internalLinks
    )) {
        foreach ($internalLinks[1] as $linkedSlug) {
            if (
                isset($scheduledBySlug[$linkedSlug])
                && $scheduledBySlug[$linkedSlug] > $item['publish_at']
            ) {
                $errors[] = basename($articlePath)
                    . ': links to unpublished scheduled article /'
                    . $linkedSlug
                    . '/';
            }
        }
    }

    $coverSource = $coverSourceDir . DIRECTORY_SEPARATOR . $item['cover_filename'];
    if (!is_file($coverSource)) {
        $errors[] = basename($articlePath) . ': missing cover ' . $item['cover_filename'];
    } else {
        $coverTarget = $themeCoverDir . DIRECTORY_SEPARATOR . $item['cover_filename'];
        if (!copy($coverSource, $coverTarget)) {
            $errors[] = basename($articlePath) . ': unable to copy cover into theme assets';
        }
    }

    $articles[] = array_merge($item, $parsed);
}

if ($articles === []) {
    fwrite(STDERR, "No completed article files were found.\n");
    exit(1);
}

usort($articles, static fn(array $a, array $b): int => $a['id'] <=> $b['id']);

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    exit(1);
}

$importPath = $importDir . DIRECTORY_SEPARATOR . sprintf(
    'fenix-content-%03d-%03d.xml',
    $articles[0]['id'],
    $articles[array_key_last($articles)]['id']
);
$seedPath = $importDir . DIRECTORY_SEPARATOR . 'fenix-content-import-seed.json';

$xml = buildWxr($articles, $siteUrl, $themeCoverUrl, $author);
if (file_put_contents($importPath, $xml) === false) {
    fwrite(STDERR, "Unable to write WordPress import file.\n");
    exit(1);
}

$seed = buildImportSeed($articles);
if (
    file_put_contents(
        $seedPath,
        json_encode($seed, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) === false
) {
    fwrite(STDERR, "Unable to write temporary admin import seed.\n");
    exit(1);
}

fwrite(
    STDOUT,
    sprintf(
        "Validated %d articles, copied %d covers, and wrote %s plus %s.\n",
        count($articles),
        count($articles),
        $importPath,
        $seedPath
    )
);

function parseArticle(string $path): array
{
    $raw = (string) file_get_contents($path);
    if (!preg_match('/\A---\R(.*?)\R---\R(.*)\z/su', $raw, $matches)) {
        throw new RuntimeException('Invalid front matter: ' . $path);
    }

    $meta = [];
    foreach (preg_split('/\R/u', trim($matches[1])) as $line) {
        $parts = explode(':', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        $meta[trim($parts[0])] = trim($parts[1]);
    }

    return [
        'meta' => $meta,
        'body' => trim($matches[2]),
    ];
}

function buildImportSeed(array $articles): array
{
    $items = [];
    foreach ($articles as $article) {
        $items[] = [
            'id'               => (int) $article['id'],
            'title'            => $article['title'],
            'slug'             => $article['slug'],
            'publish_at'       => $article['publish_at'],
            'category_slug'    => $article['category_slug'],
            'category_name'    => $article['category_name'],
            'primary_keyword'  => $article['primary_keyword'],
            'cover_filename'   => $article['cover_filename'],
            'seo_title'        => $article['meta']['seo_title'],
            'meta_description' => $article['meta']['meta_description'],
            'excerpt'          => $article['meta']['excerpt'],
            'cover_alt'        => $article['meta']['cover_alt'],
            'content'          => $article['body'],
        ];
    }

    return [
        'version'      => 1,
        'generated_at' => gmdate(DATE_ATOM),
        'timezone'     => 'Asia/Bangkok',
        'articles'     => $items,
    ];
}

function buildWxr(array $articles, string $siteUrl, string $themeCoverUrl, string $author): string
{
    $now = gmdate(DATE_RSS);
    $categories = [];
    foreach ($articles as $article) {
        $categories[$article['category_slug']] = $article['category_name'];
    }

    $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
    $xml .= '<rss version="2.0"' . PHP_EOL;
    $xml .= ' xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"' . PHP_EOL;
    $xml .= ' xmlns:content="http://purl.org/rss/1.0/modules/content/"' . PHP_EOL;
    $xml .= ' xmlns:wfw="http://wellformedweb.org/CommentAPI/"' . PHP_EOL;
    $xml .= ' xmlns:dc="http://purl.org/dc/elements/1.1/"' . PHP_EOL;
    $xml .= ' xmlns:wp="http://wordpress.org/export/1.2/"' . PHP_EOL;
    $xml .= '>' . PHP_EOL;
    $xml .= '<channel>' . PHP_EOL;
    $xml .= '<title>FENIX PRO EA</title>' . PHP_EOL;
    $xml .= '<link>' . xml($siteUrl) . '</link>' . PHP_EOL;
    $xml .= '<description>FENIX PRO EA scheduled SEO content</description>' . PHP_EOL;
    $xml .= '<pubDate>' . xml($now) . '</pubDate>' . PHP_EOL;
    $xml .= '<language>th</language>' . PHP_EOL;
    $xml .= '<wp:wxr_version>1.2</wp:wxr_version>' . PHP_EOL;
    $xml .= '<wp:base_site_url>' . xml($siteUrl) . '</wp:base_site_url>' . PHP_EOL;
    $xml .= '<wp:base_blog_url>' . xml($siteUrl) . '</wp:base_blog_url>' . PHP_EOL;

    $xml .= '<wp:author>' . PHP_EOL;
    $xml .= '<wp:author_id>1</wp:author_id>' . PHP_EOL;
    $xml .= '<wp:author_login>' . cdata($author) . '</wp:author_login>' . PHP_EOL;
    $xml .= '<wp:author_email>' . cdata('') . '</wp:author_email>' . PHP_EOL;
    $xml .= '<wp:author_display_name>' . cdata($author) . '</wp:author_display_name>' . PHP_EOL;
    $xml .= '<wp:author_first_name>' . cdata('') . '</wp:author_first_name>' . PHP_EOL;
    $xml .= '<wp:author_last_name>' . cdata('') . '</wp:author_last_name>' . PHP_EOL;
    $xml .= '</wp:author>' . PHP_EOL;

    foreach ($categories as $slug => $name) {
        $xml .= '<wp:category>' . PHP_EOL;
        $xml .= '<wp:term_id>0</wp:term_id>' . PHP_EOL;
        $xml .= '<wp:category_nicename>' . cdata($slug) . '</wp:category_nicename>' . PHP_EOL;
        $xml .= '<wp:category_parent>' . cdata('') . '</wp:category_parent>' . PHP_EOL;
        $xml .= '<wp:cat_name>' . cdata($name) . '</wp:cat_name>' . PHP_EOL;
        $xml .= '</wp:category>' . PHP_EOL;
    }

    foreach ($articles as $article) {
        $xml .= attachmentItem($article, $siteUrl, $themeCoverUrl, $author);
    }

    foreach ($articles as $article) {
        $xml .= articleItem($article, $siteUrl, $author);
    }

    $xml .= '</channel>' . PHP_EOL;
    $xml .= '</rss>' . PHP_EOL;
    return $xml;
}

function attachmentItem(array $article, string $siteUrl, string $themeCoverUrl, string $author): string
{
    $attachmentId = 20000 + (int) $article['id'];
    $filename = $article['cover_filename'];
    $slug = pathinfo($filename, PATHINFO_FILENAME);
    $url = $themeCoverUrl . '/' . rawurlencode($filename);
    $dateLocal = $article['publish_at'];
    $dateGmt = localToGmt($dateLocal);

    $xml = '<item>' . PHP_EOL;
    $xml .= '<title>' . cdata($article['meta']['cover_alt']) . '</title>' . PHP_EOL;
    $xml .= '<link>' . xml($siteUrl . '/?attachment_id=' . $attachmentId) . '</link>' . PHP_EOL;
    $xml .= '<pubDate>' . xml(gmdate(DATE_RSS, strtotime($dateGmt . ' UTC'))) . '</pubDate>' . PHP_EOL;
    $xml .= '<dc:creator>' . cdata($author) . '</dc:creator>' . PHP_EOL;
    $xml .= '<guid isPermaLink="false">' . xml($siteUrl . '/?attachment_id=' . $attachmentId) . '</guid>' . PHP_EOL;
    $xml .= '<description></description>' . PHP_EOL;
    $xml .= '<content:encoded>' . cdata('') . '</content:encoded>' . PHP_EOL;
    $xml .= '<excerpt:encoded>' . cdata('') . '</excerpt:encoded>' . PHP_EOL;
    $xml .= '<wp:post_id>' . $attachmentId . '</wp:post_id>' . PHP_EOL;
    $xml .= '<wp:post_date>' . cdata($dateLocal) . '</wp:post_date>' . PHP_EOL;
    $xml .= '<wp:post_date_gmt>' . cdata($dateGmt) . '</wp:post_date_gmt>' . PHP_EOL;
    $xml .= '<wp:post_modified>' . cdata($dateLocal) . '</wp:post_modified>' . PHP_EOL;
    $xml .= '<wp:post_modified_gmt>' . cdata($dateGmt) . '</wp:post_modified_gmt>' . PHP_EOL;
    $xml .= '<wp:comment_status>' . cdata('closed') . '</wp:comment_status>' . PHP_EOL;
    $xml .= '<wp:ping_status>' . cdata('closed') . '</wp:ping_status>' . PHP_EOL;
    $xml .= '<wp:post_name>' . cdata($slug) . '</wp:post_name>' . PHP_EOL;
    $xml .= '<wp:status>' . cdata('inherit') . '</wp:status>' . PHP_EOL;
    $xml .= '<wp:post_parent>0</wp:post_parent>' . PHP_EOL;
    $xml .= '<wp:menu_order>0</wp:menu_order>' . PHP_EOL;
    $xml .= '<wp:post_type>' . cdata('attachment') . '</wp:post_type>' . PHP_EOL;
    $xml .= '<wp:post_mime_type>' . cdata('image/webp') . '</wp:post_mime_type>' . PHP_EOL;
    $xml .= '<wp:post_password>' . cdata('') . '</wp:post_password>' . PHP_EOL;
    $xml .= '<wp:is_sticky>0</wp:is_sticky>' . PHP_EOL;
    $xml .= '<wp:attachment_url>' . cdata($url) . '</wp:attachment_url>' . PHP_EOL;
    $xml .= postMeta('_wp_attachment_image_alt', $article['meta']['cover_alt']);
    $xml .= '</item>' . PHP_EOL;
    return $xml;
}

function articleItem(array $article, string $siteUrl, string $author): string
{
    $postId = 10000 + (int) $article['id'];
    $attachmentId = 20000 + (int) $article['id'];
    $dateLocal = $article['publish_at'];
    $dateGmt = localToGmt($dateLocal);
    $link = $siteUrl . '/' . $article['slug'] . '/';

    $xml = '<item>' . PHP_EOL;
    $xml .= '<title>' . cdata($article['title']) . '</title>' . PHP_EOL;
    $xml .= '<link>' . xml($link) . '</link>' . PHP_EOL;
    $xml .= '<pubDate>' . xml(gmdate(DATE_RSS, strtotime($dateGmt . ' UTC'))) . '</pubDate>' . PHP_EOL;
    $xml .= '<dc:creator>' . cdata($author) . '</dc:creator>' . PHP_EOL;
    $xml .= '<guid isPermaLink="false">' . xml($siteUrl . '/?p=' . $postId) . '</guid>' . PHP_EOL;
    $xml .= '<description></description>' . PHP_EOL;
    $xml .= '<content:encoded>' . cdata($article['body']) . '</content:encoded>' . PHP_EOL;
    $xml .= '<excerpt:encoded>' . cdata($article['meta']['excerpt']) . '</excerpt:encoded>' . PHP_EOL;
    $xml .= '<wp:post_id>' . $postId . '</wp:post_id>' . PHP_EOL;
    $xml .= '<wp:post_date>' . cdata($dateLocal) . '</wp:post_date>' . PHP_EOL;
    $xml .= '<wp:post_date_gmt>' . cdata($dateGmt) . '</wp:post_date_gmt>' . PHP_EOL;
    $xml .= '<wp:post_modified>' . cdata($dateLocal) . '</wp:post_modified>' . PHP_EOL;
    $xml .= '<wp:post_modified_gmt>' . cdata($dateGmt) . '</wp:post_modified_gmt>' . PHP_EOL;
    $xml .= '<wp:comment_status>' . cdata('closed') . '</wp:comment_status>' . PHP_EOL;
    $xml .= '<wp:ping_status>' . cdata('closed') . '</wp:ping_status>' . PHP_EOL;
    $xml .= '<wp:post_name>' . cdata($article['slug']) . '</wp:post_name>' . PHP_EOL;
    $xml .= '<wp:status>' . cdata('future') . '</wp:status>' . PHP_EOL;
    $xml .= '<wp:post_parent>0</wp:post_parent>' . PHP_EOL;
    $xml .= '<wp:menu_order>0</wp:menu_order>' . PHP_EOL;
    $xml .= '<wp:post_type>' . cdata('post') . '</wp:post_type>' . PHP_EOL;
    $xml .= '<wp:post_password>' . cdata('') . '</wp:post_password>' . PHP_EOL;
    $xml .= '<wp:is_sticky>0</wp:is_sticky>' . PHP_EOL;
    $xml .= '<category domain="category" nicename="' . xml($article['category_slug']) . '">' . cdata($article['category_name']) . '</category>' . PHP_EOL;
    $xml .= postMeta('_thumbnail_id', (string) $attachmentId);
    $xml .= postMeta('_yoast_wpseo_title', $article['meta']['seo_title']);
    $xml .= postMeta('_yoast_wpseo_metadesc', $article['meta']['meta_description']);
    $xml .= postMeta('_yoast_wpseo_focuskw', $article['primary_keyword']);
    $xml .= postMeta('_yoast_wpseo_opengraph-title', $article['meta']['seo_title']);
    $xml .= postMeta('_yoast_wpseo_opengraph-description', $article['meta']['meta_description']);
    $xml .= '</item>' . PHP_EOL;
    return $xml;
}

function postMeta(string $key, string $value): string
{
    return '<wp:postmeta>' . PHP_EOL
        . '<wp:meta_key>' . cdata($key) . '</wp:meta_key>' . PHP_EOL
        . '<wp:meta_value>' . cdata($value) . '</wp:meta_value>' . PHP_EOL
        . '</wp:postmeta>' . PHP_EOL;
}

function localToGmt(string $local): string
{
    $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $local, new DateTimeZone('Asia/Bangkok'));
    if (!$date) {
        throw new RuntimeException('Invalid publish date: ' . $local);
    }
    return $date->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
}

function cdata(string $value): string
{
    return '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $value) . ']]>';
}

function xml(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}
