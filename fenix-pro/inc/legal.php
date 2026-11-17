<?php
/**
 * หน้ากฎหมาย ชุด 3 · ตัวช่วยจัดวางเอกสาร
 *
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026
 * /privacy-policy/ และ /terms/ ยังเก็บเนื้อหาไว้ในตัวแก้ไขของ WordPress
 * ไฟล์นี้อ่านเนื้อหานั้นแล้วจัดวางใหม่ตอนแสดงผล ไม่ได้แก้ข้อความเลยสักตัว
 * (เป็นเอกสารกฎหมาย จึงต้องตรงกับของเดิมทุกตัวอักษร)
 *
 * @package fenix-pro
 */

/**
 * แยกเนื้อหาเอกสารออกเป็นส่วน ๆ
 *
 * @param string $html เนื้อหาของหน้า
 * @return array|null array( 'lead' => HTML, 'info' => [ HTML ], 'sections' => [ ['title'=>, 'id'=>, 'body'=>] ], 'updated' => ข้อความ )
 */
function fenix_legal_parse( $html ) {
	$dom = fenix_pages_dom( $html );

	if ( ! $dom ) {
		return null;
	}

	list( $doc, $root ) = $dom;

	$out = array(
		'lead'     => '',
		'info'     => array(),
		'sections' => array(),
		'updated'  => '',
	);

	$current = null;
	$index   = 0;

	foreach ( iterator_to_array( $root->childNodes ) as $node ) {
		if ( XML_ELEMENT_NODE !== $node->nodeType ) {
			continue;
		}

		if ( 'h2' === $node->nodeName ) {
			$index++;
			$title = fenix_pages_text( $node );

			$out['sections'][] = array(
				'title' => $title,
				'id'    => 'sec-' . $index,
				'body'  => '',
			);

			$current = count( $out['sections'] ) - 1;
			continue;
		}

		/* ก่อนหัวข้อแรก: ย่อหน้าแรกคือคำนำ ย่อหน้าที่มี "ป้าย:" คือข้อมูลเอกสาร */
		if ( null === $current ) {
			if ( 'p' !== $node->nodeName ) {
				continue;
			}

			$inner = fenix_pages_inner( $node );

			if ( '' === $out['lead'] && false === strpos( wp_strip_all_tags( $inner ), ':' ) ) {
				$out['lead'] = $inner;
				continue;
			}

			if ( '' === $out['lead'] ) {
				$out['lead'] = $inner;
				continue;
			}

			foreach ( fenix_legal_rows( $inner ) as $row ) {
				$out['info'][] = $row;
			}
			continue;
		}

		/* ย่อหน้าปิดท้าย "ปรับปรุงล่าสุด" แยกไปเป็นป้าย */
		$plain = fenix_pages_text( $node );

		if ( 'p' === $node->nodeName && 0 === strpos( $plain, 'ปรับปรุงล่าสุด' ) ) {
			$out['updated'] = $plain;
			continue;
		}

		$out['sections'][ $current ]['body'] .= $doc->saveHTML( $node );
	}

	return $out;
}

/**
 * ย่อหน้าข้อมูลเอกสารคั่นแต่ละแถวด้วย <br> แยกออกมาเป็นแถวละรายการ
 *
 * @param string $html HTML ข้างในย่อหน้า
 * @return array
 */
function fenix_legal_rows( $html ) {
	$parts = preg_split( '~<br\s*/?>~i', (string) $html );
	$rows  = array();

	foreach ( $parts as $part ) {
		$part = trim( $part );

		if ( '' === wp_strip_all_tags( $part ) ) {
			continue;
		}

		/* ป้ายหน้าเครื่องหมาย ":" เป็นตัวหนาสีขาว */
		$cut = strpos( $part, ':' );

		if ( false !== $cut ) {
			$part = '<b>' . substr( $part, 0, $cut + 1 ) . '</b>' . substr( $part, $cut + 1 );
		}

		$rows[] = $part;
	}

	return $rows;
}

/**
 * เลขหัวข้อ "1." ของหน้าเงื่อนไขฯ ให้เป็นสีส้ม · ข้อความไม่เปลี่ยน
 *
 * @param string $title ชื่อหัวข้อเดิม
 * @return string HTML
 */
function fenix_legal_heading( $title ) {
	/* ช่องว่างหลังเลขเก็บไว้เหมือนเดิม ข้อความต้องตรงกับของเดิมทุกตัวอักษร */
	if ( preg_match( '/^(\d+\.)(\s*)(.*)$/u', $title, $found ) ) {
		return '<span class="n">' . esc_html( $found[1] ) . '</span>' . esc_html( $found[2] . $found[3] );
	}

	return esc_html( $title );
}

/**
 * ชื่อหัวข้อสำหรับสารบัญ · ตัดเลขนำหน้าออก เพราะมีเลขในกล่องอยู่แล้ว
 *
 * @param string $title ชื่อหัวข้อเดิม
 * @return string
 */
function fenix_legal_toc_label( $title ) {
	return trim( (string) preg_replace( '/^\d+\.\s*/u', '', $title ) );
}

/**
 * กล่องสารบัญ · บนมือถือพับไว้ตอนเปิดหน้า บนคอมกางตลอด
 *
 * @param array $sections ช่วงของเอกสาร
 */
function fenix_legal_toc( $sections ) {
	if ( ! $sections ) {
		return;
	}
	?>
	<aside class="dside">
		<details class="toc" open data-legal-toc>
			<summary>
				<?php echo fenix_icon( 'list', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				ในหน้านี้มีอะไรบ้าง
				<span class="chev"><?php echo fenix_icon( 'chev-r', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			</summary>
			<ol>
				<?php foreach ( $sections as $i => $section ) : ?>
					<li>
						<a href="#<?php echo esc_attr( $section['id'] ); ?>">
							<b><?php echo esc_html( (string) ( $i + 1 ) ); ?></b>
							<?php echo esc_html( fenix_legal_toc_label( $section['title'] ) ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ol>
		</details>
	</aside>
	<?php
}

/**
 * การ์ดข้อมูลเอกสาร · ไอคอนประจำแถว โล่ / ซองจดหมาย / ปฏิทิน
 *
 * @param array $rows แถวข้อมูล
 */
function fenix_legal_info( $rows ) {
	if ( ! $rows ) {
		return;
	}

	$icons = array( 'shield', 'mail', 'cal' );
	?>
	<div class="dinfo">
		<?php foreach ( $rows as $i => $row ) : ?>
			<div>
				<?php echo fenix_icon( isset( $icons[ $i ] ) ? $icons[ $i ] : 'cal', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo $row; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}
