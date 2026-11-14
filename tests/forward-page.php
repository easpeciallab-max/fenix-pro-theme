<?php
/** Run with: php tests/forward-page.php */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();

// Only WordPress infrastructure is stubbed; the template is the real one.
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function remove_action( ...$args ) {}
function get_template_directory() { return dirname( __DIR__ ) . '/fenix-pro'; }
function get_template_directory_uri() { return 'https://example.test/wp-content/themes/fenix-pro'; }
function get_theme_mod( $key, $default = false ) {
	global $theme_mods;
	return array_key_exists( $key, $theme_mods ) ? $theme_mods[ $key ] : $default;
}
function home_url( $path = '' ) { return 'https://example.test' . $path; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return esc_attr( $value ); }
function esc_url( $value ) { return esc_attr( $value ); }
function get_header() {}
function get_footer() {}
function have_posts() { return false; }
function the_post() {}
function get_the_title() { return 'FORWARD TEST'; }

require get_template_directory() . '/functions.php';

function check( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: $message\n" );
		exit( 1 );
	}
	echo "PASS: $message\n";
}

function render_forward( $mods ) {
	global $theme_mods;
	$theme_mods = $mods;
	ob_start();
	require get_template_directory() . '/template-forward.php';
	return ob_get_clean();
}

// ---- หน้าที่ยังไม่มีตัวเลขจริง ----
$html = render_forward( array() );
check( false === strpos( $html, 'ระบุ' ), 'ไม่มีคำว่า "ระบุ..." ค้างอยู่บนหน้า' );
check( false === strpos( $html, 'ปรับแต่ง →' ), 'คำสั่งสำหรับแอดมินไม่หลุดออกหน้าเว็บ' );
check( false === strpos( $html, 'shot-placeholder' ), 'กรอบรูปเปล่าถูกถอดออกแล้ว' );
check( false === strpos( $html, 'stats-grid' ), 'กรอบตัวเลขไม่ขึ้นเมื่อยังไม่มีข้อมูล' );
check( false === strpos( $html, 'เช่น XAUUSD' ), 'ตัวอย่างค่าที่ไม่ใช่ข้อมูลจริงถูกเอาออก' );

// ---- สิ่งที่ต้องมีแทน ----
check( false !== strpos( $html, 'MYFXBOOK' ), 'มีปุ่มไปดูบัญชีจริงบน Myfxbook' );
check( false !== strpos( $html, 'myfxbook.com/portfolio/eafn/12227282' ), 'ปุ่มชี้ไปบัญชีจริงของเรา' );
check( false !== strpos( $html, 'Backtest' ), 'อธิบายว่า Forward Test ต่างจาก Backtest อย่างไร' );
check( false !== strpos( $html, 'Drawdown' ), 'บอกให้ดู Drawdown ไม่ใช่ดูแต่กำไร' );
check( false !== strpos( $html, 'ไม่มีการตัดขาดทุนอัตโนมัติ' ), 'คำเตือนบอกตรง ๆ ว่าไม่มีการตัดขาดทุน' );
check( 1 === substr_count( $html, 'line.me' ), 'ยังมีทางติดต่อทีมงานท้ายหน้า' );

// ---- พอมีตัวเลขจริงแล้วต้องขึ้นเอง ----
$html = render_forward( array( 'fw_stat1_value' => '1 ม.ค. – 31 ธ.ค. 2569', 'fw_stat6_value' => '58.37%' ) );
check( false !== strpos( $html, 'stats-grid' ), 'กรอบตัวเลขกลับมาเมื่อกรอกค่าจริง' );
check( 2 === substr_count( $html, 'class="stat"' ), 'ขึ้นเฉพาะช่องที่กรอกค่าแล้ว ไม่ขึ้นช่องว่าง' );
check( false !== strpos( $html, '58.37%' ) && false !== strpos( $html, 'Max Drawdown' ), 'ช่องที่กรอกแล้วแสดงทั้งป้ายและค่า' );

// ---- ค่าที่กรอกต้องถูก escape ----
$html = render_forward( array( 'fw_stat2_value' => 'Real <b>' ) );
check( false !== strpos( $html, 'Real &lt;b&gt;' ), 'ค่าที่กรอกถูก escape' );

// ---- รูปประกอบ ----
$html = render_forward( array( 'forward_img' => 'https://example.test/fw.png', 'forward_img_caption' => 'กราฟยอดเงินบัญชีจริง' ) );
check( false !== strpos( $html, 'fw.png' ) && false !== strpos( $html, 'กราฟยอดเงินบัญชีจริง' ), 'ใส่รูปได้เมื่อมีรูปจริงให้ลง' );
