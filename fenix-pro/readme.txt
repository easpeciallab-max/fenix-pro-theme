=== FENIX PRO EA - WordPress Theme ===

ธีม WordPress แบบ custom สำหรับเว็บ Landing ขาย FENIX PRO EA
ระบบช่วยเทรดอัตโนมัติบน MetaTrader 5 โทนดำ-ส้มไฟ-ทอง

ตัวธีมอยู่ในโฟลเดอร์ fenix-pro/ และถูก deploy ผ่าน WP Pusher โดยตั้ง
Repository subdirectory = fenix-pro

ไม่มี build step: แก้ PHP, CSS, JS แล้วใช้งานได้ทันที

----------------------------------------------------------------
1) ภาพรวมการแก้ไข
----------------------------------------------------------------

เนื้อหาเกือบทั้งหมดของเว็บถูกออกแบบให้แก้ผ่าน WordPress Customizer
ไม่ต้องแก้โค้ดโดยตรง

เข้าไปที่:
รูปแบบ > ปรับแต่ง > FENIX PRO - ตั้งค่าหน้าเว็บ

สิ่งที่แก้ได้จาก Customizer:
- ลิงก์ LINE OA และช่องทางติดต่อ
- เปิด/ปิด section หน้าแรก
- ข้อความ Hero, About, Features, FAQ, Footer
- รูปภาพ Hero, Gallery, Install guide, Risk page
- ราคาและแพ็กเกจ
- ผล Backtest / Forward Test
- เมนูลัดมือถือ
- Language switcher
- Cookie consent และ tracking
- หน้า Link Hub สำหรับยิงแอด

สำคัญ:
- ห้ามแต่งรีวิวปลอม
- ห้ามใส่ตัวเลขผลทดสอบสมมติ
- ห้ามลบหรือลดทอนคำเตือนความเสี่ยง

----------------------------------------------------------------
2) หน้าเว็บหลักและ Template ที่ต้องใช้
----------------------------------------------------------------

สร้างหน้าใน WordPress แล้วเลือก Template ให้ตรงกับ slug ต่อไปนี้

หน้าแรก:
- slug: หน้าแรกของเว็บ
- template: ไม่ต้องเลือก หรือใช้ค่าเริ่มต้นของธีม
- render โดย front-page.php
- ใช้เป็นหน้า production หลัก เว้นแต่ตัดสินใจย้ายไป Elementor จริง

ผล Backtest:
- slug: backtest
- template: FENIX · หน้า Backtest
- แก้เนื้อหาใน Customizer หมวดหน้า Backtest

ผล Forward Test:
- slug: forward-test
- template: FENIX · หน้า Forward Test
- แก้เนื้อหาใน Customizer หมวดหน้า Forward Test

แพ็กเกจ:
- slug: pricing
- template: FENIX · หน้า Pricing
- แก้แพ็กเกจและราคาใน Customizer

วิธีติดตั้ง:
- slug: how-to-install
- template: FENIX · หน้า How to Install
- ใช้รูปคู่มือใน assets/img/install/ เป็นค่าเริ่มต้น

คำเตือนความเสี่ยง:
- slug: risk-disclosure
- template: FENIX · หน้า Risk Disclosure
- ควรคงหน้านี้ไว้เสมอ และมีลิงก์จาก footer / บทความ

บทความทั้งหมด:
- slug: articles
- template: FENIX · หน้ารวมบทความ
- ใช้สำหรับ SEO content / blog

หน้า Link Hub สำหรับยิงแอด:
- slug ที่ใช้อยู่บนเว็บจริง: go
- template: FENIX · หน้า Link Hub (ยิงแอด)
- เป็นหน้า standalone ไม่มี header/footer เพื่อโฟกัส CTA
- แก้ข้อความ ปุ่ม รูปโชว์ และลิงก์ได้ใน Customizer หมวด 28

----------------------------------------------------------------
3) กติกา Theme vs Elementor
----------------------------------------------------------------

ธีมนี้รองรับ Elementor แล้ว แต่ต้องแยกบทบาทให้ชัด

แนะนำให้ใช้ Theme template สำหรับ:
- หน้าแรก production หลัก
- Backtest
- Forward Test
- Pricing
- How to Install
- Risk Disclosure
- Articles / Blog
- Link Hub /go/

เหตุผล:
- เนื้อหาถูกผูกกับ Customizer แล้ว
- SEO, schema, disclaimer, risk warning, footer, mobile nav ถูกคุมโดยธีม
- ลดโอกาส Elementor ทับ template แล้วเนื้อหาหาย

ใช้ Elementor สำหรับ:
- หน้า draft หรือ landing เฉพาะแคมเปญ
- หน้า sales page ที่ต้องลากวางเองจริง ๆ
- ทดลองดีไซน์ก่อนตัดสินใจย้ายเข้าธีม

ถ้าจะใช้ Elementor:
- เลือก Template: FENIX - Elementor Full Width หรือ FENIX - Elementor Canvas
- ห้ามเอา Elementor ไปทับหน้า Backtest / Forward / Pricing / Install / Risk
  ถ้ายังต้องการให้ template ธีมแสดงข้อมูลจาก Customizer
- Elementor starter อยู่ใน elementor-templates/fenix-pro-ea-home-starter.json
- หลัง import ต้องเปลี่ยนลิงก์ LINE จาก # เป็นลิงก์จริง

สรุปง่าย ๆ:
- เว็บหลัก = Theme custom
- หน้าแก้ไวหรือแคมเปญเฉพาะกิจ = Elementor
- หน้า /go/ ยิงแอด = Link Hub template

----------------------------------------------------------------
4) สิ่งที่ต้องตั้งค่าก่อนใช้จริง
----------------------------------------------------------------

1. ใส่ LINE OA
   Customizer > 1) ช่องทางติดต่อ
   ค่า line_url จะถูกใช้กับ CTA หลายจุดทั่วเว็บ

2. ตรวจเมนูหลัก
   Appearance > Menus
   เมนูแนะนำ:
   - หน้าแรก
   - ผลทดสอบ
     - Backtest
     - Forward Test
   - แพ็กเกจ
   - วิธีติดตั้ง
   ไม่จำเป็นต้องใส่ "ความเสี่ยง" ในเมนูหลัก แต่ควรมีใน footer

3. กรอกผลทดสอบจริง
   Backtest / Forward Test ยังมีค่า placeholder เช่น "ระบุ..."
   ให้กรอกเฉพาะข้อมูลจริงเท่านั้น

4. ตั้งราคาและแพ็กเกจ
   ถ้ายังไม่อยากแสดงราคา ให้ใช้โหมดสอบถามทาง LINE

5. ตรวจรูปภาพ
   - Hero image
   - Gallery
   - Install guide
   - OG default image สำหรับแชร์ LINE/Facebook

6. ตรวจคำเตือนความเสี่ยง
   ควรคงข้อความไว้ครบ โดยเฉพาะหน้า Risk Disclosure และ disclaimer ใต้ผลทดสอบ

----------------------------------------------------------------
5) ปุ่ม LINE และมือถือ
----------------------------------------------------------------

ค่าเริ่มต้น:
- ปุ่ม LINE ลอยแบบยาว (float-line) ปิดอยู่
- ถ้ามี line_url และปิด float-line ธีมจะแสดงปุ่ม LINE วงกลมเฉพาะ desktop
- มือถือใช้เมนูลัดด้านล่างแทน และซ่อนปุ่มวงกลม

เมนูลัดมือถือแก้ได้ที่:
Customizer > Mobile bottom nav

ค่าที่แนะนำ:
- หน้าแรก
- ผลทดสอบ
- แพ็กเกจ
- วิธีติดตั้ง
- ทัก LINE

----------------------------------------------------------------
6) ภาษาและ GTranslate
----------------------------------------------------------------

ธีมมีตำแหน่ง language switcher ใน header

ถ้าติดตั้ง GTranslate:
- ธีมจะใช้ shortcode [gtranslate] อัตโนมัติ
- ดีสำหรับแปลหน้าเว็บแบบฟรีและเร็ว
- SEO หลักยังควรเน้นภาษาไทย

ถ้าไม่มีปลั๊กอินภาษา:
- ธีมจะแสดง fallback language switcher จากค่าใน Customizer
- fallback เป็น UI เริ่มต้นเท่านั้น ไม่ได้แปลทั้งเว็บจริง

หน้า Link Hub จะซ่อน widget แปลภาษา เพื่อไม่ให้ทับดีไซน์หน้าแอด

----------------------------------------------------------------
7) บทความและ SEO
----------------------------------------------------------------

ธีมรองรับบทความ SEO:
- single.php สำหรับบทความเดี่ยว
- index.php / search.php สำหรับ archive และ search
- template-blog.php สำหรับหน้า articles

ฟีเจอร์บทความ:
- Table of contents จาก H2/H3
- Reading progress
- Share to LINE / Facebook / copy link
- Related posts
- Load more posts ด้วย AJAX
- Risk disclaimer ใต้บทความ

SEO ที่ธีมช่วยให้:
- Open Graph / Twitter card
- Organization / WebSite / FAQ schema
- BlogPosting schema
- Breadcrumb schema
- archive canonical
- robots noindex สำหรับ search/author/date

ถ้าติดตั้ง Yoast, Rank Math หรือ SEOPress:
- ธีมจะปิด OG/schema บางส่วนเองเพื่อลด tag ซ้ำ

----------------------------------------------------------------
8) รีวิวและผลทดสอบ
----------------------------------------------------------------

รีวิว:
- show_reviews ปิดเป็น default
- เปิดเมื่อมีรีวิวจริงเท่านั้น
- ก่อนเปิด ต้องแก้ข้อความตัวอย่างทั้งหมดใน Customizer

ผลทดสอบ:
- ค่า placeholder เช่น "ระบุ..." หมายถึงยังไม่กรอกข้อมูลจริง
- ห้ามใส่ตัวเลขเดาเอง
- ถ้ามีผลจริง ควรแนบภาพหรือแหล่งตรวจสอบได้ เช่น Myfxbook / FXBlue
- ต้องคง disclaimer ใต้ section ผลทดสอบไว้

----------------------------------------------------------------
9) Deploy flow
----------------------------------------------------------------

Flow ปัจจุบัน:

แก้โค้ดในเครื่อง
> lint PHP
> git commit
> git push origin main
> WP Pusher deploy theme จาก subdirectory fenix-pro

ถ้าเปิด Push-to-Deploy:
- push แล้ว WP Pusher ควรอัปเดตอัตโนมัติ

ถ้าไม่อัปเดต:
- เข้า WP Pusher > Themes > Update theme
- เคลียร์ cache ถ้าใช้ cache plugin/CDN

ก่อน commit:
- รัน php -l กับไฟล์ PHP ที่แก้
- อย่า commit ไฟล์ลับหรือ local config
- .claude/ ถูก ignore แล้ว เป็น config local ของ Claude Code เท่านั้น

----------------------------------------------------------------
10) ไฟล์สำคัญ
----------------------------------------------------------------

functions.php:
- theme setup
- defaults ทั้งหมดใน fenix_defaults()
- helper functions
- SEO/schema
- AJAX load more

inc/customizer.php:
- register Customizer sections/settings/controls
- เพิ่ม setting ใหม่ที่นี่คู่กับ defaults ใน functions.php

front-page.php:
- หน้าแรก custom theme

header.php / footer.php:
- header, menu, language switcher, footer, mobile nav, LINE buttons, cookie bar

style.css:
- design system และ responsive layout ทั้งหมด
- theme header ต้องอยู่บนสุด

assets/js/main.js:
- mobile nav
- submenu
- reveal animation
- load more
- cookie consent / tracking loader

template-links.php:
- หน้า Link Hub สำหรับ /go/

template-elementor-*.php:
- หน้า Elementor เฉพาะกรณีที่ต้องการลากวางเอง

----------------------------------------------------------------
11) เวอร์ชันและ cache
----------------------------------------------------------------

Theme header ยังเป็น Version 1.0.0 ได้
ธีมใช้ filemtime() เป็น version ของ style.css และ main.js
ดังนั้นแก้ CSS/JS แล้ว browser จะได้ query string ใหม่อัตโนมัติ

ไม่จำเป็นต้องเพิ่มเลขเวอร์ชันทุกครั้งที่แก้เล็ก ๆ
ถ้าจะเพิ่มเวอร์ชัน ให้ทำเป็นรอบ release จริง เช่น 1.1.0, 1.2.0

----------------------------------------------------------------
12) สถานะที่ควรรู้ตอนนี้
----------------------------------------------------------------

- หน้าแรก production ใช้ Theme custom
- /go/ ใช้ Link Hub template
- /articles/ ใช้ template รวมบทความ
- Elementor starter เป็นจุดเริ่มต้น ไม่ใช่ production final
- Product template เก่าถูกนำออกแล้ว ไม่ต้องสร้างหน้า product
- Risk Disclosure ยังต้องมีอยู่และไม่ควรถูกซ่อนจาก footer/legal links

จบคู่มือปัจจุบัน
