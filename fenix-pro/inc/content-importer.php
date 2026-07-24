<?php
/**
 * Temporary, administrator-only importer for the approved SEO content batch.
 *
 * Remove this file and its functions.php include after the import is verified.
 *
 * @package FenixPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const FENIX_CONTENT_IMPORT_PAGE  = 'fenix-content-import';
const FENIX_CONTENT_IMPORT_BATCH = 5;
const FENIX_CONTENT_IMPORT_KEY   = 'seo-50-2026';

/**
 * Register the temporary Tools page.
 */
function fenix_content_import_admin_menu() {
	add_management_page(
		'FENIX Content Import',
		'FENIX Content Import',
		'manage_options',
		FENIX_CONTENT_IMPORT_PAGE,
		'fenix_content_import_render_page'
	);
}
add_action( 'admin_menu', 'fenix_content_import_admin_menu' );

/**
 * Read and validate the generated import seed.
 *
 * @return array|WP_Error
 */
function fenix_content_import_load_seed() {
	$path = get_template_directory() . '/inc/content-import-seed.json';
	if ( ! is_readable( $path ) ) {
		return new WP_Error( 'missing_seed', 'The content import seed is missing.' );
	}

	$seed = json_decode( (string) file_get_contents( $path ), true );
	if (
		! is_array( $seed )
		|| empty( $seed['articles'] )
		|| ! is_array( $seed['articles'] )
		|| 50 !== count( $seed['articles'] )
	) {
		return new WP_Error( 'invalid_seed', 'The content import seed is invalid or incomplete.' );
	}

	return $seed;
}

/**
 * Find a post in any status by its slug.
 *
 * @param string $slug Post slug.
 * @return WP_Post|null
 */
function fenix_content_import_find_post( $slug ) {
	$posts = get_posts(
		array(
			'name'           => sanitize_title( $slug ),
			'post_type'      => 'post',
			'post_status'    => array( 'publish', 'future', 'draft', 'pending', 'private' ),
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		)
	);

	return $posts ? $posts[0] : null;
}

/**
 * Create or reuse the article category.
 *
 * @param array $article Article data.
 * @return int|WP_Error
 */
function fenix_content_import_category( $article ) {
	$term = get_term_by( 'slug', $article['category_slug'], 'category' );
	if ( $term instanceof WP_Term ) {
		return (int) $term->term_id;
	}

	$created = wp_insert_term(
		$article['category_name'],
		'category',
		array( 'slug' => $article['category_slug'] )
	);

	if ( is_wp_error( $created ) ) {
		return $created;
	}

	return (int) $created['term_id'];
}

/**
 * Create or reuse a featured image copied from the theme asset.
 *
 * @param array $article Article data.
 * @return int|WP_Error
 */
function fenix_content_import_cover( $article ) {
	$filename = sanitize_file_name( basename( $article['cover_filename'] ) );
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
			'meta_key'       => '_fenix_content_cover_filename',
			'meta_value'     => $filename,
		)
	);

	if ( $existing ) {
		$attachment_id = (int) $existing[0]->ID;
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $article['cover_alt'] );
		return $attachment_id;
	}

	$source = get_template_directory() . '/assets/content/covers/' . $filename;
	if ( ! is_readable( $source ) ) {
		return new WP_Error( 'missing_cover', 'Missing cover file: ' . $filename );
	}

	$contents = file_get_contents( $source );
	if ( false === $contents ) {
		return new WP_Error( 'cover_read_failed', 'Unable to read cover file: ' . $filename );
	}

	$upload = wp_upload_bits( $filename, null, $contents );
	if ( ! empty( $upload['error'] ) ) {
		return new WP_Error( 'cover_upload_failed', $upload['error'] );
	}

	$filetype      = wp_check_filetype( $upload['file'], null );
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => sanitize_text_field( $article['cover_alt'] ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) ) {
		return $attachment_id;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $article['cover_alt'] );
	update_post_meta( $attachment_id, '_fenix_content_cover_filename', $filename );

	return (int) $attachment_id;
}

/**
 * Import one article.
 *
 * @param array $article Article data.
 * @return array|WP_Error
 */
function fenix_content_import_article( $article ) {
	$existing = fenix_content_import_find_post( $article['slug'] );
	if ( $existing instanceof WP_Post ) {
		return array(
			'action'  => 'skipped',
			'post_id' => (int) $existing->ID,
		);
	}

	$category_id = fenix_content_import_category( $article );
	if ( is_wp_error( $category_id ) ) {
		return $category_id;
	}

	$attachment_id = fenix_content_import_cover( $article );
	if ( is_wp_error( $attachment_id ) ) {
		return $attachment_id;
	}

	$post_id = wp_insert_post(
		wp_slash(
			array(
				'post_author'       => get_current_user_id(),
				'post_title'        => $article['title'],
				'post_name'         => $article['slug'],
				'post_content'      => $article['content'],
				'post_excerpt'      => $article['excerpt'],
				'post_status'       => 'future',
				'post_type'         => 'post',
				'post_date'         => $article['publish_at'],
				'post_date_gmt'     => get_gmt_from_date( $article['publish_at'] ),
				'comment_status'    => 'closed',
				'ping_status'       => 'closed',
				'post_category'     => array( $category_id ),
				'meta_input'        => array(
					'_thumbnail_id'                    => $attachment_id,
					'_yoast_wpseo_title'               => $article['seo_title'],
					'_yoast_wpseo_metadesc'            => $article['meta_description'],
					'_yoast_wpseo_focuskw'             => $article['primary_keyword'],
					'_yoast_wpseo_opengraph-title'     => $article['seo_title'],
					'_yoast_wpseo_opengraph-description' => $article['meta_description'],
					'_yoast_wpseo_opengraph-image-id'  => $attachment_id,
					'_fenix_content_batch'             => FENIX_CONTENT_IMPORT_KEY,
				),
			)
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	return array(
		'action'        => 'created',
		'post_id'       => (int) $post_id,
		'attachment_id' => (int) $attachment_id,
	);
}

/**
 * Get the progress state for the current administrator.
 *
 * @return array
 */
function fenix_content_import_state() {
	$key   = 'fenix_content_import_' . get_current_user_id();
	$state = get_transient( $key );
	if ( ! is_array( $state ) ) {
		$state = array(
			'created' => 0,
			'skipped' => 0,
			'errors'  => array(),
		);
	}

	return $state;
}

/**
 * Store the progress state for the current administrator.
 *
 * @param array $state Progress state.
 */
function fenix_content_import_save_state( $state ) {
	set_transient(
		'fenix_content_import_' . get_current_user_id(),
		$state,
		HOUR_IN_SECONDS
	);
}

/**
 * Count imported articles and collect any missing slugs.
 *
 * @param array $articles Seed articles.
 * @return array
 */
function fenix_content_import_audit( $articles ) {
	$found   = 0;
	$future  = 0;
	$missing = array();

	foreach ( $articles as $article ) {
		$post = fenix_content_import_find_post( $article['slug'] );
		if ( ! $post ) {
			$missing[] = $article['slug'];
			continue;
		}

		++$found;
		if ( 'future' === $post->post_status ) {
			++$future;
		}
	}

	return array(
		'found'   => $found,
		'future'  => $future,
		'missing' => $missing,
	);
}

/**
 * Render and run the temporary importer.
 */
function fenix_content_import_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'fenix-pro' ) );
	}

	$seed = fenix_content_import_load_seed();
	?>
	<div class="wrap">
		<h1>FENIX Content Import</h1>
		<?php if ( is_wp_error( $seed ) ) : ?>
			<div class="notice notice-error"><p><?php echo esc_html( $seed->get_error_message() ); ?></p></div>
			<?php return; ?>
		<?php endif; ?>

		<?php
		$articles = $seed['articles'];
		$total    = count( $articles );
		$offset   = 0;
		$running  = false;
		$state    = fenix_content_import_state();

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['fenix_content_import'] ) ) {
			check_admin_referer( 'fenix_content_import' );
			$offset  = isset( $_POST['offset'] ) ? max( 0, absint( $_POST['offset'] ) ) : 0;
			$running = true;

			if ( 0 === $offset ) {
				$state = array(
					'created' => 0,
					'skipped' => 0,
					'errors'  => array(),
				);
			}

			@set_time_limit( 120 );
			$batch = array_slice( $articles, $offset, FENIX_CONTENT_IMPORT_BATCH );
			foreach ( $batch as $article ) {
				$result = fenix_content_import_article( $article );
				if ( is_wp_error( $result ) ) {
					$state['errors'][] = $article['slug'] . ': ' . $result->get_error_message();
					continue;
				}

				if ( 'created' === $result['action'] ) {
					++$state['created'];
				} else {
					++$state['skipped'];
				}
			}

			$offset += count( $batch );
			fenix_content_import_save_state( $state );
		}

		$audit = fenix_content_import_audit( $articles );
		?>

		<p>
			Seed: <strong><?php echo esc_html( (string) $total ); ?></strong> articles.
			Found: <strong><?php echo esc_html( (string) $audit['found'] ); ?></strong>.
			Scheduled: <strong><?php echo esc_html( (string) $audit['future'] ); ?></strong>.
		</p>

		<?php if ( $running && $offset < $total ) : ?>
			<div class="notice notice-info">
				<p>Imported through item <?php echo esc_html( (string) $offset ); ?> of <?php echo esc_html( (string) $total ); ?>. Continuing automatically.</p>
			</div>
			<form id="fenix-content-import-next" method="post">
				<?php wp_nonce_field( 'fenix_content_import' ); ?>
				<input type="hidden" name="fenix_content_import" value="1">
				<input type="hidden" name="offset" value="<?php echo esc_attr( (string) $offset ); ?>">
				<?php submit_button( 'Continue import', 'primary' ); ?>
			</form>
			<script>
				window.setTimeout(function () {
					document.getElementById('fenix-content-import-next').submit();
				}, 400);
			</script>
		<?php elseif ( $running ) : ?>
			<?php $audit = fenix_content_import_audit( $articles ); ?>
			<?php if ( empty( $audit['missing'] ) && empty( $state['errors'] ) ) : ?>
				<div class="notice notice-success">
					<p>Import complete. All 50 articles exist and all 50 are scheduled.</p>
				</div>
			<?php else : ?>
				<div class="notice notice-error">
					<p>Import finished with errors. Review the details below before cleanup.</p>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<p>This administrator-only tool creates missing categories, uploads SEO covers, and schedules the approved 50-article batch. Existing slugs are skipped.</p>
			<form method="post">
				<?php wp_nonce_field( 'fenix_content_import' ); ?>
				<input type="hidden" name="fenix_content_import" value="1">
				<input type="hidden" name="offset" value="0">
				<?php submit_button( 'Start verified import', 'primary' ); ?>
			</form>
		<?php endif; ?>

		<?php if ( ! empty( $state['errors'] ) ) : ?>
			<h2>Errors</h2>
			<ul>
				<?php foreach ( $state['errors'] as $error ) : ?>
					<li><?php echo esc_html( $error ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( ! empty( $audit['missing'] ) && $running && $offset >= $total ) : ?>
			<h2>Missing slugs</h2>
			<p><?php echo esc_html( implode( ', ', $audit['missing'] ) ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}
