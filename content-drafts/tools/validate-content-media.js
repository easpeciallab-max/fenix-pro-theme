/**
 * Validate the 50-article campaign and its final WebP assets.
 *
 * Usage:
 *   node content-drafts/tools/validate-content-media.js
 */

const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const errors = [];

function readJson(filename) {
    return JSON.parse(fs.readFileSync(filename, 'utf8'));
}

function expect(condition, message) {
    if (!condition) {
        errors.push(message);
    }
}

function htmlAttribute(tag, name) {
    const pattern = new RegExp(`\\b${name}\\s*=\\s*(["'])(.*?)\\1`, 'is');
    const match = tag.match(pattern);

    return match ? match[2].trim() : '';
}

function readUint24LE(buffer, offset) {
    return buffer[offset] | (buffer[offset + 1] << 8) | (buffer[offset + 2] << 16);
}

function readWebpSize(filename) {
    const buffer = fs.readFileSync(filename);

    if (
        buffer.length < 30 ||
        buffer.toString('ascii', 0, 4) !== 'RIFF' ||
        buffer.toString('ascii', 8, 12) !== 'WEBP'
    ) {
        throw new Error(`Invalid WebP: ${filename}`);
    }

    let offset = 12;

    while (offset + 8 <= buffer.length) {
        const type = buffer.toString('ascii', offset, offset + 4);
        const chunkLength = buffer.readUInt32LE(offset + 4);
        const data = offset + 8;

        if (type === 'VP8X' && data + 10 <= buffer.length) {
            return {
                width: readUint24LE(buffer, data + 4) + 1,
                height: readUint24LE(buffer, data + 7) + 1,
            };
        }

        if (type === 'VP8 ' && data + 10 <= buffer.length) {
            return {
                width: buffer.readUInt16LE(data + 6) & 0x3fff,
                height: buffer.readUInt16LE(data + 8) & 0x3fff,
            };
        }

        if (type === 'VP8L' && data + 5 <= buffer.length && buffer[data] === 0x2f) {
            return {
                width: 1 + (((buffer[data + 2] & 0x3f) << 8) | buffer[data + 1]),
                height:
                    1 +
                    (((buffer[data + 4] & 0x0f) << 10) |
                        (buffer[data + 3] << 2) |
                        ((buffer[data + 2] & 0xc0) >> 6)),
            };
        }

        offset = data + chunkLength + (chunkLength % 2);
    }

    throw new Error(`WebP dimensions not found: ${filename}`);
}

function validateImage(filename, expectedWidth, expectedHeight) {
    if (!fs.existsSync(filename)) {
        errors.push(`Missing image: ${filename}`);
        return;
    }

    try {
        const size = readWebpSize(filename);
        expect(
            size.width === expectedWidth && size.height === expectedHeight,
            `Unexpected dimensions ${size.width}x${size.height}: ${filename}`
        );
    } catch (error) {
        errors.push(error.message);
    }
}

const manifest = readJson(path.join(root, 'generated/content-manifest.json'));
const v3Map = readJson(path.join(root, 'generated/media-v3/wordpress-media-map.json'));
const v3Plan = readJson(path.join(root, 'generated/media-v3/wordpress-update-plan.json'));
const items = manifest.items || [];

expect(items.length === 50, 'Content manifest must contain 50 articles.');
expect(manifest.article_count === 50, 'article_count must be 50.');

const seenSlugs = new Set();
const seenImageUrls = new Set();
let imageCount = 0;

items.forEach((item, index) => {
    const id = Number(item.id);
    const slug = String(item.slug || '');
    const contentFile = path.join(root, String(item.content_file || ''));
    const mediaRoot = path.join(root, 'generated', id <= 10 ? 'media-v2' : 'media-v3');

    expect(id === index + 1, `Article IDs are not sequential at ${id}.`);
    expect(slug !== '', `Article ${id}: missing slug.`);
    expect(!seenSlugs.has(slug), `Article ${id}: duplicate slug ${slug}.`);
    seenSlugs.add(slug);

    const publishMatch = String(item.publish_at || '').match(
        /^(\d{4})-(\d{2})-(\d{2}) 08:00:00$/
    );
    expect(Boolean(publishMatch), `Article ${id}: publish time is not 08:00.`);
    if (publishMatch) {
        expect(Number(publishMatch[3]) % 2 === 0, `Article ${id}: publish day is not even.`);
    }

    expect(fs.existsSync(contentFile), `Article ${id}: missing content file.`);
    if (!fs.existsSync(contentFile)) {
        return;
    }

    const html = fs.readFileSync(contentFile, 'utf8');
    const figureCount = (html.match(/<figure\b/gi) || []).length;
    const imgTags = html.match(/<img\b[^>]*>/gi) || [];

    expect(figureCount === 3, `Article ${id}: expected 3 figures, found ${figureCount}.`);
    expect(imgTags.length === 3, `Article ${id}: expected 3 images, found ${imgTags.length}.`);
    expect(!html.toLowerCase().includes('chatgpt.com'), `Article ${id}: contains a ChatGPT URL.`);

    imgTags.forEach((tag) => {
        const src = htmlAttribute(tag, 'src');
        const alt = htmlAttribute(tag, 'alt');
        const filename = path.posix.basename(new URL(src).pathname);

        imageCount += 1;
        expect(alt !== '', `Article ${id}: image ${filename} is missing alt.`);
        expect(filename.toLowerCase().endsWith('.webp'), `Article ${id}: image is not WebP.`);
        expect(!seenImageUrls.has(src), `Article ${id}: duplicate image URL ${src}.`);
        seenImageUrls.add(src);

        validateImage(
            path.join(mediaRoot, 'article-images', filename),
            1200,
            675
        );
    });

    validateImage(
        path.join(mediaRoot, 'covers', `${slug}-cover.webp`),
        1200,
        630
    );
});

const v3MediaItems = v3Map.items || [];
const v3Updates = v3Plan.updates || [];

expect(v3MediaItems.length === 160, 'WordPress media map must contain 160 items.');
expect(v3Updates.length === 40, 'WordPress update plan must contain 40 posts.');

v3MediaItems.forEach((media) => {
    ['alt', 'title', 'caption', 'description'].forEach((field) => {
        expect(
            String(media[field] || '').trim() !== '',
            `Media ${media.filename || '(unknown)'}: missing ${field}.`
        );
    });
});

v3Updates.forEach((update) => {
    expect(
        (update.interior_media || []).length === 3,
        `Article ${update.id}: WordPress update plan must contain 3 interior images.`
    );
    expect(
        Number(update.featured_media_id) > 0,
        `Article ${update.id}: WordPress update plan is missing featured media.`
    );
});

if (errors.length > 0) {
    console.error('Content media validation failed:');
    errors.forEach((error) => console.error(`- ${error}`));
    process.exit(1);
}

console.log(
    `Validated ${items.length} articles, ${imageCount} interior images, ` +
        `50 covers, and ${v3MediaItems.length} WordPress media records.`
);
