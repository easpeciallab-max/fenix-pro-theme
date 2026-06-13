# AGENTS.md — FENIX PRO EA Theme

ไฟล์นี้เป็นบริบทสำหรับ Codex อ่านก่อนเริ่มงานในโปรเจกต์นี้

## โปรเจกต์คืออะไร
ธีม WordPress แบบ **custom** สำหรับเว็บ Landing ขาย **FENIX PRO EA** — ระบบช่วยเทรดอัตโนมัติ (Expert Advisor) บน MetaTrader 5 เนื้อหาภาษาไทย โทนดำ–ส้มไฟ–ทอง
- **ไม่มี build step** — PHP + CSS + vanilla JS ตรง ๆ แก้ไฟล์แล้วใช้ได้เลย ไม่มี npm/compile
- ธีมอยู่ในโฟลเดอร์ `fenix-pro/` (root ของ repo เก็บเอกสาร dev) WP Pusher ตั้ง subdirectory = `fenix-pro`
- ต้องการ WordPress 6.0+ / PHP 7.4+

## หลักการสำคัญ — ห้ามทำผิด
1. **Customizer-driven**: ทุกข้อความ/รูป/ราคา/ลิงก์ แก้ได้ผ่าน WordPress Customizer โดยไม่ต้องแตะโค้ด เวลาเพิ่มเนื้อหาใหม่ ให้เพิ่มเป็น **setting** เสมอ **ห้าม hardcode** ข้อความลงใน template
2. **รีวิว**: `show_reviews` ปิดเป็น default จนกว่าจะมีรีวิวจริง — **ห้ามแต่งรีวิวปลอม**
3. **ตัวเลขผลทดสอบ**: ค่า Backtest/Forward เป็น placeholder ("ระบุ...") — **ห้ามใส่ตัวเลขสมมติ** ให้เจ้าของกรอกเอง
4. **ห้ามลบหรือลดทอน** ข้อความ disclaimer และ risk warning (สำคัญต่อความถูกต้อง/ความน่าเชื่อถือ)
5. **อังกฤษพิมพ์ใหญ่**: ทุกคำภาษาอังกฤษแสดงเป็นตัวพิมพ์ใหญ่อัตโนมัติด้วย CSS (`text-transform: uppercase` บน `body`) ยกเว้น `.keep-case` (อีเมล/URL) → เขียน HTML เป็นพิมพ์เล็กปกติได้ ปล่อยให้ CSS จัดการ
6. **Escape เสมอ**: output ทุกจุดผ่าน `esc_html()` / `esc_url()` / `esc_attr()` (inline SVG จาก `fenix_icon()` มี phpcs:ignore เพราะ trusted)

## โครงสร้างไฟล์ (ใน fenix-pro/)
- `functions.php` — theme setup, enqueue (Noto Sans Thai), **`fenix_defaults()`** (ค่า default ของทุก setting — แหล่งความจริงของเนื้อหาเริ่มต้น), helpers:
  - `fenix_mod($key)` — อ่านค่า setting (theme_mod) พร้อม fallback เป็น default
  - `fenix_lines($text)` — แตก textarea เป็น array (บรรทัดละ 1 รายการ)
  - `fenix_logo_url()` — URL โลโก้ (custom_logo หรือโลโก้ที่ฝังในธีม)
  - `fenix_fallback_menu()` — เมนูสำรองชี้ไป slug หน้าย่อย
  - `fenix_page_hero($kicker,$title,$subtitle)` — หัวหน้าเพจ ใช้ร่วมทุก template
  - `fenix_line_cta($title,$sub)` — แถบ CTA ทัก LINE ปิดท้ายหน้าย่อย
  - `fenix_icon($name,$class)` — inline SVG icon (~20 ไอคอน: flame, pulse, clock, gauge, flag, cpu, candles, layout, shield, moon, headset, check, x, warn, chat, arrow, quote, mail, facebook, download)
- `inc/customizer.php` — สร้าง Customizer panel `fenix_panel` + sections จาก array `$sections` ด้วย **loop เดียว** (auto-register setting/control/sanitizer) FAQ 10 ข้อ generate ด้วย for-loop
- `header.php` / `footer.php` — โครง + เมนู (wp_nav_menu + fallback) + ปุ่ม LINE ลอย
- `front-page.php` — **หน้าแรก (hub)** รวม: hero, highlight bar, pain, about (EA คืออะไร), features, gallery, fit, การ์ดนำทาง 5 ใบ, FAQ ย่อ, risk strip, CTA
- `template-backtest.php` / `template-forward.php` / `template-pricing.php` / `template-install.php` / `template-risk.php` — เทมเพลตเพจ (มี `Template Name:` header ให้เลือกใน Page Attributes)
- `index.php` / `single.php` / `page.php` — บล็อก/เพจทั่วไป (เผื่อบทความ SEO)
- `style.css` — design system ทั้งหมด (theme header comment อยู่บนสุด ห้ามย้าย)
- `assets/js/main.js` — sticky header, mobile nav (aria-expanded), IntersectionObserver `.reveal`→`.in`
- `assets/img/` — โลโก้ (logo.png วงกลมโปร่งใส, logo-128.png)
- `screenshot.png` — ภาพ preview ธีม
- `readme.txt` — คู่มือผู้ใช้ (ภาษาไทย) สำหรับเจ้าของเว็บ

## วิธีเพิ่ม setting ใหม่ (pattern ที่ต้องทำตาม)
1. เพิ่ม default ใน `fenix_defaults()` (functions.php): `'my_key' => 'ค่าเริ่มต้น',`
2. เพิ่ม field เข้า section ที่เหมาะสมใน `$sections` (customizer.php): `'my_key' => array( 'Label ภาษาไทย', 'text' ),`
   - type รองรับ: `text`, `textarea`, `url`, `checkbox`, `image`, `radio` (radio ต้องส่ง choices เป็น arg ที่ 3 + มี sanitizer)
   - loop จะ register setting + control + sanitize ให้อัตโนมัติ
3. เรียกในเทมเพลต: `fenix_mod('my_key')` แล้ว escape
   - checkbox → `fenix_sanitize_checkbox`, ราคา radio → `fenix_sanitize_pricing_mode` (price|contact)
   - รายการ (บรรทัดละ 1) → `fenix_lines()`
- เพิ่ม **section** ใหม่: `$sections['fenix_xxx'] = array('title'=>..., 'description'=>..., 'fields'=>array(...));` ก่อนบรรทัด `$priority = 10;`

## Design tokens (style.css :root)
- สี: `--void #0A0A0E`, `--coal`, `--ember #FF7A1A`, `--flare #FFB347`, `--gold #F2C14E`, `--line-green #06C755` (ใช้เฉพาะปุ่ม LINE), `--warn #FFC53D`
- ฟอนต์: **Noto Sans Thai** (ทั้ง display และ body) โหลดจาก Google Fonts
- การ์ด: `--card-bg` ตั้งใจให้สว่างกว่าพื้น section เพื่อไม่ให้กล่องกลืนพื้นหลัง
- section: สลับ `.section` (พื้นปกติ) กับ `.section-alt` (เข้มขึ้น) + เส้นแบ่งบาง ๆ (`border-soft`)
- ระวัง CSS specificity: class แบบ `.section` กับ element อาจ override กันที่ padding/margin — มีบล็อก override v1.1/v1.2 ต่อท้ายไฟล์ (ลำดับหลังชนะ)

## เพจ & slug (สำคัญต่อการลิงก์)
หน้าแรก = `front-page.php` (อัตโนมัติเมื่อ front page เป็น static/รายการล่าสุด) เพจที่ต้องสร้างใน WP แล้วเลือก Template + ตั้ง slug ให้ตรง:
| slug | Template |
|------|----------|
| `backtest` | FENIX — หน้า Backtest |
| `forward-test` | FENIX — หน้า Forward Test |
| `pricing` | FENIX — หน้า Pricing |
| `how-to-install` | FENIX — หน้า How to Install |
| `risk-disclosure` | FENIX — หน้า Risk Disclosure |

การ์ด hub บนหน้าแรกลิงก์ไป slug เหล่านี้ (แก้ URL ได้ที่ Customizer หมวด 16) เนื้อหา "รู้จัก FENIX PRO" รวมอยู่ในหน้าแรกแล้ว (ไม่มีหน้า Product แยก)
**อย่าเปิดเพจเหล่านี้ด้วย Elementor** — จะทับการแสดงผลของ template

## ทดสอบก่อน commit
- lint ทุกไฟล์ที่แก้: `php -l <file>` (ต้อง "No syntax errors")
- เช็ค render (ถ้าต้องการ): เขียน stub ของฟังก์ชัน WP (`get_header`, `the_post`, `esc_*`, `get_theme_mod` คืน default ฯลฯ) แล้ว `require` front-page.php / template-*.php ดูว่าไม่มี Fatal/Warning/Notice และ markup สำคัญครบ

## Deploy flow
แก้โค้ด → `git commit` → `git push` → WP Pusher ดึงลงเว็บ (manual กด Update หรือเปิด Push-to-Deploy ให้ดึงอัตโนมัติทุก push)
- repo สาธารณะใช้ WP Pusher ฟรีได้ — ปลอดภัยเพราะธีมไม่มี secret (config อยู่ใน WP DB ผ่าน Customizer)
- แนะนำ: ถ้าเปิด auto-deploy ควรมี staging หรือ lint ให้ผ่านก่อน push ขึ้น production
