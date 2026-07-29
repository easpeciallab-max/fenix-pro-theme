<?php
/**
 * Build the article-image plan used by the local visual renderer and WordPress updater.
 *
 * Usage:
 *   php content-drafts/tools/build-article-media-plan.php
 */

declare(strict_types=1);

$root         = dirname(__DIR__);
$manifestFile = $root . '/generated/content-manifest.json';
$articlesDir  = $root . '/articles';
$outputDir    = $root . '/generated/media-v2';
$planFile     = $outputDir . '/media-plan.json';
$dataFile     = $outputDir . '/campaign-media-data.js';

if (!is_file($manifestFile)) {
    fwrite(STDERR, "Missing content manifest: {$manifestFile}\n");
    exit(1);
}

$manifest = json_decode((string) file_get_contents($manifestFile), true, 512, JSON_THROW_ON_ERROR);

if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
    fwrite(STDERR, "Unable to create output directory: {$outputDir}\n");
    exit(1);
}

/**
 * Return a UTF-8-safe excerpt without cutting a word when possible.
 */
function fenix_media_excerpt(string $text, int $limit = 96): string
{
    $text = trim((string) preg_replace('/\s+/u', ' ', strip_tags($text)));

    if (mb_strlen($text, 'UTF-8') <= $limit) {
        return $text;
    }

    $excerpt = mb_substr($text, 0, $limit, 'UTF-8');
    $space   = mb_strrpos($excerpt, ' ', 0, 'UTF-8');

    if ($space !== false && $space > (int) ($limit * 0.65)) {
        $excerpt = mb_substr($excerpt, 0, $space, 'UTF-8');
    }

    return rtrim($excerpt, " \t\n\r\0\x0B,.;:!?") . '...';
}

/**
 * Strip the lightweight YAML-like front matter used in article drafts.
 *
 * @return array{meta: array<string, string>, html: string}
 */
function fenix_media_parse_article(string $raw): array
{
    $meta = array();
    $html = $raw;

    if (preg_match('/\A---\R(.*?)\R---\R/su', $raw, $match)) {
        $html = substr($raw, strlen($match[0]));

        foreach (preg_split('/\R/u', $match[1]) as $line) {
            if (!str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode(':', $line, 2));
            if ($key !== '') {
                $meta[$key] = $value;
            }
        }
    }

    return array(
        'meta' => $meta,
        'html' => $html,
    );
}

/**
 * Extract H2 sections and list-based takeaways from one article.
 *
 * @return array{sections: array<int, array<string, string>>, checklist: array<int, string>}
 */
function fenix_media_extract_structure(string $html): array
{
    $dom = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $dom->loadHTML(
        '<?xml encoding="UTF-8"><div id="fenix-article-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $xpath    = new DOMXPath($dom);
    $sections = array();
    $h2Nodes  = $xpath->query('//*[@id="fenix-article-root"]//h2');

    if ($h2Nodes !== false) {
        foreach ($h2Nodes as $heading) {
            $headingText = trim((string) preg_replace('/\s+/u', ' ', $heading->textContent));

            if ($headingText === '' || str_contains($headingText, 'แหล่งข้อมูลอ้างอิง')) {
                continue;
            }

            $description = '';
            for ($node = $heading->nextSibling; $node !== null; $node = $node->nextSibling) {
                if ($node instanceof DOMElement && $node->tagName === 'h2') {
                    break;
                }

                if ($node instanceof DOMElement && $node->tagName === 'p') {
                    $candidate = trim((string) preg_replace('/\s+/u', ' ', $node->textContent));
                    if ($candidate !== '') {
                        $description = fenix_media_excerpt($candidate, 118);
                        break;
                    }
                }
            }

            $sections[] = array(
                'id'          => $heading instanceof DOMElement ? $heading->getAttribute('id') : '',
                'title'       => preg_replace('/^\d+\.\s*/u', '', $headingText),
                'description' => $description,
            );
        }
    }

    $checklist = array();
    $lists     = $xpath->query('//*[@id="fenix-article-root"]//ol | //*[@id="fenix-article-root"]//ul');

    if ($lists !== false) {
        $candidates = array();

        foreach ($lists as $list) {
            $sectionHeading = '';
            for ($node = $list->previousSibling; $node !== null; $node = $node->previousSibling) {
                if ($node instanceof DOMElement && $node->tagName === 'h2') {
                    $sectionHeading = trim((string) preg_replace('/\s+/u', ' ', $node->textContent));
                    break;
                }
            }

            if (str_contains($sectionHeading, 'แหล่งข้อมูลอ้างอิง')) {
                continue;
            }

            $items = array();
            foreach ($list->childNodes as $child) {
                if (!$child instanceof DOMElement || $child->tagName !== 'li') {
                    continue;
                }

                $item = fenix_media_excerpt($child->textContent, 82);
                if ($item !== '') {
                    $items[] = $item;
                }
            }

            if (count($items) >= 3) {
                $score = 0;
                if (preg_match('/เช็กลิสต์|ขั้นตอน|วิธี|ตรวจ|ก่อน|ควร|ต้อง/u', $sectionHeading)) {
                    $score += 10;
                }
                if ($list instanceof DOMElement && $list->tagName === 'ol') {
                    $score += 2;
                }

                $candidates[] = array(
                    'score' => $score,
                    'items' => array_slice($items, 0, 6),
                );
            }
        }

        if ($candidates !== array()) {
            usort(
                $candidates,
                static fn(array $a, array $b): int => $b['score'] <=> $a['score']
            );
            $checklist = $candidates[0]['items'];
        }
    }

    if (count($checklist) < 6) {
        foreach (array_reverse($sections) as $section) {
            if (count($checklist) >= 6) {
                break;
            }

            if ($section['title'] !== '' && !in_array($section['title'], $checklist, true)) {
                $checklist[] = $section['title'];
            }
        }
    }

    return array(
        'sections'  => array_slice($sections, 0, 7),
        'checklist' => array_slice($checklist, 0, 6),
    );
}

/**
 * Build accurate, human-readable media metadata for the contextual image.
 *
 * @return array{alt: string, title: string, caption: string, description: string}
 */
function fenix_media_context_metadata(string $category, string $keyword, string $seoTitle): array
{
    $patterns = array(
        'ea-basics'       => "หน้าจออธิบาย {$keyword} และหลักการทำงานของ EA",
        'metatrader5'     => "หน้าจอสาธิต {$keyword} บน MetaTrader 5",
        'backtest-forward'=> "หน้าจอสรุป {$keyword} สำหรับการทดสอบ EA",
        'risk-management' => "หน้าจอสรุป {$keyword} สำหรับบริหารความเสี่ยง EA",
        'vps-mt5'         => "หน้าจออธิบาย {$keyword} สำหรับใช้งาน MT5 บน VPS",
        'ea-trust'        => "หน้าจอสรุปวิธีตรวจสอบ {$keyword} ก่อนเลือกใช้ EA",
    );

    $alt = $patterns[$category] ?? "หน้าจออธิบาย {$keyword} สำหรับผู้ใช้ EA";

    return array(
        'alt'         => fenix_media_excerpt($alt, 125),
        'title'       => fenix_media_excerpt("ภาพรวม {$seoTitle}", 100),
        'caption'     => fenix_media_excerpt("ภาพประกอบสรุป {$seoTitle}", 150),
        'description' => fenix_media_excerpt(
            "ภาพประกอบบทความเรื่อง {$seoTitle} แสดงประเด็นหลักบนหน้าจอในรูปแบบที่อ่านง่ายและไม่ใช้ตัวเลขผลลัพธ์สมมติ",
            240
        ),
    );
}

$items = array();

foreach ($manifest['items'] as $manifestItem) {
    $articleFile = $root . '/' . $manifestItem['content_file'];

    if (!is_file($articleFile)) {
        fwrite(STDERR, "Missing article: {$articleFile}\n");
        exit(1);
    }

    $article   = fenix_media_parse_article((string) file_get_contents($articleFile));
    $structure = fenix_media_extract_structure($article['html']);
    $meta      = $article['meta'];
    $slug      = (string) $manifestItem['slug'];
    $keyword   = (string) $manifestItem['primary_keyword'];
    $seoTitle  = (string) ($meta['seo_title'] ?? $manifestItem['title']);
    $sections  = $structure['sections'];

    if (count($sections) < 3) {
        fwrite(STDERR, "Article {$slug} has fewer than three usable H2 sections.\n");
        exit(1);
    }

    $contextPlacement = $sections[0]['id'];
    $summaryPlacement = $sections[min(2, count($sections) - 1)]['id'];
    $checkPlacement   = $sections[count($sections) - 1]['id'];
    $contextMeta      = fenix_media_context_metadata(
        (string) $manifestItem['category_slug'],
        $keyword,
        $seoTitle
    );

    $summaryTitle = "ประเด็นสำคัญเรื่อง {$keyword}";
    $checkTitle   = "เช็กลิสต์ {$keyword}";

    $items[] = array(
        'id'               => (int) $manifestItem['id'],
        'slug'             => $slug,
        'title'            => (string) $manifestItem['title'],
        'seo_title'        => $seoTitle,
        'primary_keyword'  => $keyword,
        'category_slug'    => (string) $manifestItem['category_slug'],
        'category_name'    => (string) $manifestItem['category_name'],
        'publish_at'       => (string) $manifestItem['publish_at'],
        'cover_title'      => (string) $manifestItem['cover_title'],
        'cover_subtitle'   => (string) $manifestItem['cover_subtitle'],
        'sections'         => $sections,
        'checklist'        => $structure['checklist'],
        'assets'           => array(
            'cover' => array(
                'filename'    => "{$slug}-cover.webp",
                'alt'         => (string) ($meta['cover_alt'] ?? $seoTitle),
                'title'       => $seoTitle,
                'caption'     => fenix_media_excerpt("ภาพปกบทความ {$seoTitle}", 150),
                'description' => "ภาพปกบทความ {$seoTitle}",
                'placement'   => 'featured',
                'width'       => 1200,
                'height'      => 630,
            ),
            'context' => array_merge(
                array(
                    'filename'  => "{$slug}-overview.webp",
                    'placement' => $contextPlacement,
                    'width'     => 1200,
                    'height'    => 675,
                ),
                $contextMeta
            ),
            'summary' => array(
                'filename'    => "{$slug}-key-points.webp",
                'alt'         => fenix_media_excerpt("อินโฟกราฟิกสรุปประเด็นสำคัญเรื่อง {$keyword}", 125),
                'title'       => fenix_media_excerpt($summaryTitle, 100),
                'caption'     => fenix_media_excerpt("สรุปประเด็นที่ควรเข้าใจเกี่ยวกับ {$keyword}", 150),
                'description' => fenix_media_excerpt(
                    "อินโฟกราฟิกประกอบบทความ {$seoTitle} สรุปหัวข้อสำคัญจากเนื้อหาเพื่อช่วยทบทวนก่อนนำไปใช้จริง",
                    240
                ),
                'placement'   => $summaryPlacement,
                'width'       => 1200,
                'height'      => 675,
            ),
            'checklist' => array(
                'filename'    => "{$slug}-checklist.webp",
                'alt'         => fenix_media_excerpt("เช็กลิสต์ตรวจสอบ {$keyword} ก่อนนำไปใช้จริง", 125),
                'title'       => fenix_media_excerpt($checkTitle, 100),
                'caption'     => fenix_media_excerpt("รายการตรวจสอบ {$keyword} ก่อนตัดสินใจใช้งาน", 150),
                'description' => fenix_media_excerpt(
                    "ภาพเช็กลิสต์ประกอบบทความ {$seoTitle} รวบรวมรายการตรวจสอบจากเนื้อหาโดยไม่เพิ่มคำกล่าวอ้างด้านผลตอบแทน",
                    240
                ),
                'placement'   => $checkPlacement,
                'width'       => 1200,
                'height'      => 675,
            ),
        ),
    );
}

$plan = array(
    'generated_at' => gmdate('c'),
    'article_count'=> count($items),
    'asset_count'  => count($items) * 4,
    'items'        => $items,
);

$json = json_encode(
    $plan,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
);

file_put_contents($planFile, $json . PHP_EOL);
file_put_contents($dataFile, 'window.FENIX_MEDIA_PLAN = ' . $json . ';' . PHP_EOL);

fwrite(
    STDOUT,
    sprintf(
        "Built media plan for %d articles / %d assets\n%s\n%s\n",
        $plan['article_count'],
        $plan['asset_count'],
        $planFile,
        $dataFile
    )
);
