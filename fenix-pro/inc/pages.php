<?php
/**
 * FENIX PRO EA · ตัวช่วยของหน้าภายในชุด 1 (อนุมัติ 17 พ.ย. 2026)
 * /backtest/ · /forward-test/ · /pricing/ · /about/ · /articles/
 *
 * หน้า BACKTEST กับ ABOUT เก็บเนื้อหาไว้ในตัวแก้ไขของ WordPress เหมือนเดิม
 * ไฟล์นี้จึงแปลงโครง HTML ของเนื้อหานั้นให้เข้ารูปแบบใหม่ตอนแสดงผล
 * โดยไม่แตะข้อความต้นฉบับสักตัวอักษร ถ้าเจอรูปแบบที่ไม่รู้จักก็ปล่อยผ่านตามเดิม
 *
 * @package fenix-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * หัวหน้าเพจแบบใหม่ (ลำดับหน้า · ป้าย · หัวข้อ · คำโปรย · ของแถมท้าย)
 */
function fenix_phero( $title, $sub = '', $eyebrow = '', $extra = '' ) {
	?>
	<section class="fx-phero">
		<div class="slash" aria-hidden="true"><i></i><i></i><i></i></div>
		<div class="wrap">
			<nav class="crumb" aria-label="ลำดับหน้า">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
				<?php echo fenix_icon( 'chev-r', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( $title ); ?></span>
			</nav>

			<?php if ( '' !== trim( (string) $eyebrow ) ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<h1><?php echo esc_html( $title ); ?></h1>

			<?php if ( '' !== trim( (string) $sub ) ) : ?>
				<p class="sub"><?php echo esc_html( $sub ); ?></p>
			<?php endif; ?>

			<?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>
	<?php
}

/**
 * กล่อง CTA สีส้มปิดท้ายหน้า
 */
function fenix_pages_cta( $title, $text, $button, $url, $icon = 'line' ) {
	if ( '' === trim( (string) $title ) ) {
		return;
	}
	?>
	<section class="sec">
		<div class="wrap">
			<div class="fx-cta">
				<div class="wm" aria-hidden="true"><?php echo esc_html( fenix_mod( 'cta_watermark' ) ); ?></div>
				<div>
					<h2><?php echo esc_html( $title ); ?></h2>
					<?php if ( '' !== trim( (string) $text ) ) : ?>
						<p><?php echo esc_html( $text ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( '' !== trim( (string) $button ) && '' !== trim( (string) $url ) ) : ?>
					<a class="fx-btn fx-btn-black" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">
						<?php echo fenix_icon( $icon, 'line' === $icon ? 'i fill lg' : 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( $button ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * โหลดเนื้อหาของหน้าเป็น DOM เพื่อจัดรูปแบบใหม่
 * คืนค่า array( DOMDocument, DOMElement ราก ) หรือ null ถ้าเนื้อหาว่าง
 */
function fenix_pages_dom( $html ) {
	$html = trim( (string) $html );

	if ( '' === $html ) {
		return null;
	}

	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="UTF-8"><div id="fenix-root">' . $html . '</div>' );
	libxml_clear_errors();

	$root = $dom->getElementById( 'fenix-root' );

	if ( $root ) {
		/* ลิงก์ที่เขียนเป็น URL หรืออีเมล ให้คงตัวพิมพ์เล็กตามกติกาของธีม */
		foreach ( $root->getElementsByTagName( 'a' ) as $link ) {
			$text = fenix_pages_text( $link );

			if ( ! preg_match( '#^(https?://|www\.|mailto:|[^\s@]+@[^\s@]+\.[a-z]{2,})#i', $text ) ) {
				continue;
			}

			$class = trim( $link->getAttribute( 'class' ) . ' keep-case' );
			$link->setAttribute( 'class', $class );
		}
	}

	return $root ? array( $dom, $root ) : null;
}

/** คืน HTML ข้างในของโหนด */
function fenix_pages_inner( $node ) {
	$out = '';

	foreach ( $node->childNodes as $child ) {
		$out .= $node->ownerDocument->saveHTML( $child );
	}

	return $out;
}

/** ข้อความล้วนของโหนด แบบตัดช่องว่างหัวท้าย */
function fenix_pages_text( $node ) {
	return trim( preg_replace( '/\s+/u', ' ', $node->textContent ) );
}

/**
 * แยกเนื้อหาออกเป็นช่วงตามหัวข้อ h2
 * คืน array( 'intro' => HTML ก่อนหัวข้อแรก, 'sections' => [ ['title'=>, 'nodes'=>[]] ] )
 */
function fenix_pages_split( $root ) {
	$intro    = array();
	$sections = array();
	$current  = null;

	foreach ( iterator_to_array( $root->childNodes ) as $node ) {
		if ( XML_ELEMENT_NODE !== $node->nodeType ) {
			continue;
		}

		if ( 'hr' === $node->nodeName ) {
			continue;
		}

		if ( 'h2' === $node->nodeName ) {
			if ( null !== $current ) {
				$sections[] = $current;
			}
			$current = array(
				'title' => fenix_pages_text( $node ),
				'nodes' => array(),
			);
			continue;
		}

		if ( null === $current ) {
			$intro[] = $node;
			continue;
		}

		$current['nodes'][] = $node;
	}

	if ( null !== $current ) {
		$sections[] = $current;
	}

	return array(
		'intro'    => $intro,
		'sections' => $sections,
	);
}

/**
 * ใส่คลาสสีให้ช่องในตารางตามชื่อรายการในคอลัมน์แรก
 * ไม่แตะตัวเลขหรือข้อความใด ๆ
 */
function fenix_pages_table( $table ) {
	$rows = $table->getElementsByTagName( 'tr' );

	foreach ( $rows as $row ) {
		$cells = array();
		foreach ( $row->childNodes as $cell ) {
			if ( XML_ELEMENT_NODE === $cell->nodeType && in_array( $cell->nodeName, array( 'td', 'th' ), true ) ) {
				$cells[] = $cell;
			}
		}

		if ( count( $cells ) < 2 || 'th' === $cells[0]->nodeName ) {
			continue;
		}

		$label = fenix_pages_text( $cells[0] );
		$class = '';

		$has = function ( $needles ) use ( $label ) {
			foreach ( $needles as $needle ) {
				if ( false !== strpos( $label, $needle ) ) {
					return true;
				}
			}
			return false;
		};

		if ( $has( array( 'กำไรสุทธิ' ) ) ) {
			$class = 'g';
		} elseif ( $has( array( 'เฉลี่ยต่อเดือน', 'ยอดเงินสุดท้าย' ) ) ) {
			$class = 'b';
		} elseif ( $has( array( 'DD', 'แย่สุด', 'ติดลบลอยตัว', 'ทรุดหนัก' ) ) ) {
			$class = 'r';
		}

		foreach ( array_slice( $cells, 1 ) as $cell ) {
			$text = fenix_pages_text( $cell );
			$use  = $class;

			/* ช่องที่ไม่มีค่า หรือบอกให้ไปคิดเอง ใช้สีจาง */
			if ( '' === $text || '—' === $text || '-' === $text || false !== strpos( $text, 'คิดตามค่าคูณ' ) ) {
				$use = 'm';
			}

			if ( '' !== $use ) {
				$cell->setAttribute( 'class', trim( $cell->getAttribute( 'class' ) . ' ' . $use ) );
			}
		}
	}

	$table->setAttribute( 'class', trim( $table->getAttribute( 'class' ) . ' dtable num' ) );
}

/**
 * แปลงย่อหน้าเป็นกล่องข้อความตามคำขึ้นต้น
 * คืน HTML ของกล่อง หรือ '' ถ้าไม่เข้าเงื่อนไข
 */
function fenix_pages_note( $node ) {
	$html = fenix_pages_inner( $node );
	$text = fenix_pages_text( $node );

	if ( 0 === strpos( $text, 'เงื่อนไขที่ใช้ทดสอบ' ) ) {
		$html = preg_replace( '/^(\s*เงื่อนไขที่ใช้ทดสอบ\s*:)/u', '<b>$1</b>', $html, 1 );

		return '<div class="note cond">' . fenix_icon( 'flask', 'i' ) . '<span>' . $html . '</span></div>';
	}

	if ( 0 === strpos( $text, '⚠' ) || 0 === strpos( $text, 'คำเตือน' ) ) {
		/* เอาสัญลักษณ์ ⚠ ออกแล้วใช้ไอคอนแทน ตามสเปกข้อ 1.4
		   (สัญลักษณ์อาจอยู่ในแท็กอย่าง <strong> จึงข้ามแท็กเปิดไปก่อน) */
		$html = preg_replace( '/^((?:\s|<[^>]+>)*)⚠\s*/u', '$1', $html, 1 );
		$html = preg_replace( '/^((?:\s|<[^>]+>)*)(คำเตือน\s*:)/u', '$1<b>$2</b>', $html, 1 );

		return '<div class="note warn">' . fenix_icon( 'warn', 'i' ) . '<span>' . $html . '</span></div>';
	}

	return '';
}

/**
 * รายการลิงก์ไฟล์ PDF -> แถวปุ่มรอง
 * คืน HTML หรือ '' ถ้าไม่ใช่รายการไฟล์
 */
function fenix_pages_reports( $list ) {
	$links = $list->getElementsByTagName( 'a' );

	if ( ! $links->length ) {
		return '';
	}

	$buttons = '';

	foreach ( $links as $link ) {
		$href = $link->getAttribute( 'href' );

		if ( ! preg_match( '/\.pdf($|\?)/i', $href ) ) {
			return '';
		}

		$buttons .= '<a class="fx-btn fx-btn-out" href="' . esc_url( $href ) . '" target="_blank" rel="noopener">'
			. fenix_icon( 'file', 'i' ) . esc_html( fenix_pages_text( $link ) ) . '</a>';
	}

	return '<div class="reports">' . $buttons . '</div>';
}

/**
 * แยกชนิดของบล็อกในเนื้อหา เพื่อนำไปจัดวางตามแบบ
 */
function fenix_pages_kind( $node ) {
	$name  = $node->nodeName;
	$class = $node->getAttribute( 'class' );

	if ( 'figure' === $name && false !== strpos( $class, 'wp-block-image' ) ) {
		return 'img';
	}

	if ( 'figure' === $name && false !== strpos( $class, 'wp-block-table' ) ) {
		return 'table';
	}

	if ( 'table' === $name ) {
		return 'table';
	}

	if ( 'ul' === $name || 'ol' === $name ) {
		return '' !== fenix_pages_reports( $node ) ? 'reports' : 'list';
	}

	if ( 'p' === $name ) {
		return '' !== fenix_pages_note( $node ) ? 'alert' : 'p';
	}

	return 'other';
}

/** รูปหนึ่งใบในกรอบรายงาน */
function fenix_pages_figure( $node ) {
	$node->setAttribute( 'class', trim( $node->getAttribute( 'class' ) . ' fx-shot' ) );

	return $node->ownerDocument->saveHTML( $node );
}

/** ตารางหนึ่งตาราง พร้อมใส่สีช่องตามรายการ */
function fenix_pages_table_html( $node ) {
	$tables = 'table' === $node->nodeName ? array( $node ) : iterator_to_array( $node->getElementsByTagName( 'table' ) );

	foreach ( $tables as $table ) {
		fenix_pages_table( $table );
	}

	return $node->ownerDocument->saveHTML( $node );
}

/**
 * จัดวางเนื้อหาของ EA หนึ่งตัวในหน้า BACKTEST
 * ส่วนบน = รูปสรุป + ตาราง (หรือ ตาราง + รูปกราฟคู่กัน)
 * ส่วนท้าย = กล่องเงื่อนไข กล่องคำเตือน ปุ่มรายงาน และกล่องชวนเลือกคูณ LOT
 */
function fenix_backtest_section( $nodes ) {
	$head_done = false;
	$top       = array();
	$tail      = array();

	foreach ( $nodes as $node ) {
		$kind = fenix_pages_kind( $node );

		/* ย่อหน้าแรกใช้เป็นคำอธิบายในหัวช่วงไปแล้ว */
		if ( ! $head_done && 'p' === $kind ) {
			$head_done = true;
			continue;
		}

		$item = array( $kind, $node );

		if ( in_array( $kind, array( 'alert', 'reports' ), true ) || $tail ) {
			$tail[] = $item;
			continue;
		}

		$top[] = $item;
	}

	/* ---- ส่วนบน ---- */
	$out   = '';
	$count = count( $top );

	if ( $count >= 2 && 'img' === $top[0][0] && 'table' === $top[1][0] ) {
		$right = '';
		foreach ( array_slice( $top, 1 ) as $item ) {
			$right .= 'table' === $item[0] ? fenix_pages_table_html( $item[1] ) : (
				'img' === $item[0] ? fenix_pages_figure( $item[1] ) : fenix_pages_block( $item )
			);
		}
		$out .= '<div class="g-sum">' . fenix_pages_figure( $top[0][1] ) . '<div class="stack">' . $right . '</div></div>';
	} else {
		$figs = array();

		$flush = function () use ( &$figs, &$out ) {
			if ( ! $figs ) {
				return;
			}
			$out .= count( $figs ) > 1 ? '<div class="g2">' . implode( '', $figs ) . '</div>' : $figs[0];
			$figs = array();
		};

		foreach ( $top as $item ) {
			if ( 'img' === $item[0] ) {
				$figs[] = fenix_pages_figure( $item[1] );
				continue;
			}
			$flush();
			$out .= 'table' === $item[0] ? fenix_pages_table_html( $item[1] ) : fenix_pages_block( $item );
		}

		$flush();
	}

	/* ---- ส่วนท้าย ---- */
	if ( $tail ) {
		$stack = '';
		foreach ( $tail as $item ) {
			$stack .= fenix_pages_block( $item );
		}
		$out .= '<div class="stack">' . $stack . '</div>';
	}

	return $out;
}

/** แปลงบล็อกหนึ่งอันตามชนิดของมัน */
function fenix_pages_block( $item ) {
	list( $kind, $node ) = $item;

	if ( 'img' === $kind ) {
		return fenix_pages_figure( $node );
	}

	if ( 'table' === $kind ) {
		return fenix_pages_table_html( $node );
	}

	if ( 'reports' === $kind ) {
		return fenix_pages_reports( $node );
	}

	if ( 'alert' === $kind ) {
		return fenix_pages_note( $node );
	}

	if ( 'p' === $kind ) {
		$lot = fenix_backtest_lotcta( $node );

		return '' !== $lot ? $lot : '<div class="note">' . fenix_icon( 'info', 'i' ) . '<span>' . fenix_pages_inner( $node ) . '</span></div>';
	}

	return $node->ownerDocument->saveHTML( $node );
}

/**
 * ย่อหน้าชวนเลือกคูณ LOT -> กล่องพร้อมปุ่ม (รูป 05 ข้อ 4-6)
 * ลิงก์หน้าเลือกคูณ LOT อ่านจากลิงก์ในย่อหน้าเดิม ไม่ได้ตั้งค่าซ้ำ
 */
function fenix_backtest_lotcta( $node ) {
	$links = $node->getElementsByTagName( 'a' );
	$url   = '';

	foreach ( $links as $link ) {
		$href = $link->getAttribute( 'href' );
		if ( false !== strpos( $href, 'preset' ) ) {
			$url = $href;
			break;
		}
	}

	if ( '' === $url ) {
		return '';
	}

	$line  = trim( (string) fenix_mod( 'line_url' ) );
	$label = trim( (string) fenix_mod( 'bt_lot_btn' ) );
	$chat  = trim( (string) fenix_mod( 'bt_line_btn' ) );

	$acts = '';

	if ( '' !== $label ) {
		$acts .= '<a class="fx-btn fx-btn-or" href="' . esc_url( $url ) . '">' . esc_html( $label ) . fenix_icon( 'arrow', 'i' ) . '</a>';
	}

	if ( '' !== $chat && '' !== $line ) {
		$acts .= '<a class="fx-btn fx-btn-line" href="' . esc_url( $line ) . '" target="_blank" rel="noopener">' . fenix_icon( 'line', 'i fill' ) . esc_html( $chat ) . '</a>';
	}

	if ( '' === $acts ) {
		return '';
	}

	return '<div class="lotcta"><p>' . fenix_pages_inner( $node ) . '</p><div class="acts">' . $acts . '</div></div>';
}

/** ย่อหน้าแรกใต้หัวข้อ (ใช้เป็นคำอธิบายในหัวช่วง) */
function fenix_pages_first_p( $nodes ) {
	foreach ( $nodes as $node ) {
		if ( 'p' === $node->nodeName ) {
			return fenix_pages_inner( $node );
		}
	}

	return '';
}

/**
 * การ์ดบทความแบบใหม่ (ใช้ในหน้า /articles/ และหน้าหมวดหมู่)
 */
function fenix_article_card() {
	$cats = get_the_category();
	$cat  = ! empty( $cats ) ? $cats[0] : null;
	?>
	<article class="art">
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<img src="<?php echo esc_url( get_the_post_thumbnail_url( null, 'medium_large' ) ); ?>" alt="<?php echo esc_attr( fenix_featured_image_alt() ); ?>" width="768" height="403" loading="lazy" decoding="async">
			</a>
		<?php endif; ?>
		<div class="b">
			<div class="meta">
				<?php if ( $cat ) : ?>
					<span class="fx-badge dark"><?php echo esc_html( $cat->name ); ?></span>
				<?php endif; ?>
				<span><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></span>
			</div>
			<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
			<a class="fx-link" href="<?php the_permalink(); ?>">
				<?php echo esc_html( fenix_mod( 'blog_read_more' ) ); ?>
				<?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</div>
	</article>
	<?php
}

/**
 * การ์ดบทความเด่น (เรื่องล่าสุด) รูปซ้าย ข้อความขวา
 */
function fenix_article_feature() {
	$cats = get_the_category();
	$cat  = ! empty( $cats ) ? $cats[0] : null;
	?>
	<article class="feat">
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<img src="<?php echo esc_url( get_the_post_thumbnail_url( null, 'large' ) ); ?>" alt="<?php echo esc_attr( fenix_featured_image_alt() ); ?>" width="768" height="403" loading="eager" fetchpriority="high" decoding="async">
			</a>
		<?php endif; ?>
		<div class="b">
			<div class="meta">
				<?php if ( $cat ) : ?>
					<span class="fx-badge or"><?php echo esc_html( $cat->name ); ?></span>
				<?php endif; ?>
				<span><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></span>
			</div>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34, '…' ) ); ?></p>
			<a class="fx-link" href="<?php the_permalink(); ?>">
				<?php echo esc_html( fenix_mod( 'blog_read_more' ) ); ?>
				<?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</div>
	</article>
	<?php
}

/**
 * เนื้อหาของหน้ารวมบทความและหน้าหมวดหมู่ (โครงเดียวกัน)
 */
function fenix_articles_body( $query, $load_query = array() ) {
	if ( ! $query->have_posts() ) {
		echo '<section class="sec"><div class="wrap"><p class="fx-lead">ยังไม่มีบทความในขณะนี้</p></div></section>';
		return;
	}
	?>
	<section class="sec">
		<div class="wrap">
			<?php
			$first = true;
			ob_start();

			while ( $query->have_posts() ) :
				$query->the_post();

				if ( $first ) {
					$first = false;
					fenix_article_feature();
					echo '<div class="fx-grid" data-posts-grid>';
					continue;
				}

				fenix_article_card();
			endwhile;

			if ( ! $first ) {
				echo '</div>';
			}

			$list = ob_get_clean();
			wp_reset_postdata();

			echo $list; // phpcs:ignore WordPress.Security.EscapeOutput
			?>

			<?php if ( $query->max_num_pages > 1 ) : ?>
				<div class="more">
					<button type="button" class="fx-btn fx-btn-out load-more-btn"
						data-page="1"
						data-card="art"
						data-max="<?php echo esc_attr( (string) (int) $query->max_num_pages ); ?>"
						data-query="<?php echo esc_attr( wp_json_encode( $load_query ) ); ?>">
						<?php echo esc_html( fenix_mod( 'blog_more_label' ) ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
