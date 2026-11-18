# FENIX PRO: เอกสารส่งต่องานให้ Claude

วันที่จัดทำ: 3 กันยายน 2026 (Asia/Bangkok)
ฐานโค้ดตอนส่งต่อ: `91ff33bf95a8b625d9e0f0f71711ca29ca786b12`
เอกสารนี้สรุปจากไฟล์จริง ประวัติ Git และบริบทการทำงานที่มีอยู่ ไม่ใช่สำเนาบทสนทนาทุกข้อความหรือ backup เว็บไซต์ทั้งระบบ

## 1. เริ่มตรงนี้

- เว็บไซต์: https://fenixpro-th.com/
- หน้า Link Hub: https://fenixpro-th.com/go/
- โฟลเดอร์ทำงาน: `D:\EA VIDEO\fenix-pro-repo`
- Git remote: https://github.com/easpeciallab-max/fenix-pro-theme.git
- Branch production: `main`
- WordPress theme subdirectory: `fenix-pro`
- ตรวจพบ Claude Code CLI `2.1.173` ที่ `C:\Users\THANAWUT HR\.local\bin\claude.exe` ในวันที่ส่งต่อ ยังไม่ได้ตรวจการล็อกอินหรือสิทธิ์ของบัญชี Claude

อ่าน `CLAUDE.md` และ `AGENTS.md` สำหรับข้อกำหนดธีม จากนั้นอ่านส่วนที่เกี่ยวข้องในเอกสารนี้ ใช้โค้ดและข้อมูล production ที่ตรวจได้เป็นหลัก เอกสารแผนเก่าไม่ใช่หลักฐานว่างานยังค้าง

ไม่ต้องย้ายไฟล์ออกจากโฟลเดอร์เดิมเพื่อเปลี่ยนจาก Codex เป็น Claude Code และอย่าให้สองโปรแกรมแก้ไฟล์ชุดเดียวกันพร้อมกัน

## 2. เปลี่ยนผู้ช่วย ไม่ใช่ย้ายเซิร์ฟเวอร์

| ข้อมูล | อยู่ที่ไหน | สิ่งที่ต้องทำ |
|---|---|---|
| โค้ดธีม รูปที่ใช้ในธีม ไฟล์ดาวน์โหลด | `fenix-pro/` | ใช้ไฟล์เดิมต่อได้ |
| ประวัติ commit และ remote | `.git/` และ GitHub เดิม | ไม่ต้องสร้าง repository ใหม่ |
| ต้นฉบับบทความและภาพแคมเปญ | `content-drafts/` | ใช้เป็นข้อมูลอ้างอิงและสำรองงาน local |
| เนื้อหาโพสต์ที่แก้ใน admin, ตารางโพสต์, Customizer, เมนู, SEO metadata | ฐานข้อมูล WordPress | ต้องเข้าตรวจใน WordPress; repo ไม่ใช่สำเนาฐานข้อมูล |
| รูปใน Media Library, plugins, server configuration | Hosting/WordPress | ไม่ได้อยู่ครบใน repo |
| Search Console, GA4, Google Site Kit | บัญชี Google และการตั้งค่า WordPress | ใช้ property เดิม อย่าสร้างซ้ำหรือเปลี่ยนรหัสโดยไม่มีเหตุผล |
| Login, cookies, API keys, browser/MCP permissions | บัญชีและเครื่องมือแต่ละระบบ | เชื่อมใน Claude แยกตามสิทธิ์ที่จำเป็น อย่าคัดลอก credential stores |
| ประวัติแชตและเครื่องมือของ Codex | Codex | เอกสารนี้ส่งต่อสาระของงาน แต่ไม่ได้ย้าย native session/tool access |
| เครื่องมือสร้างภาพ | เครื่องมือของแต่ละผู้ช่วย | ต้องตรวจว่าฝั่ง Claude ใช้อะไรได้จริง อย่าอ้างว่ามี imagegen เดิมอัตโนมัติ |

การย้ายผู้ช่วยไม่จำเป็นต้องลบ Codex, ย้ายโดเมน, เปลี่ยน WordPress, สร้าง Google property ใหม่ หรือเปลี่ยนการตั้งเวลาโพสต์เดิม

หากต้องย้ายเครื่องหรือ hosting จริงในภายหลัง ต้องสำรองฐานข้อมูลและ `wp-content` ด้วย โดยไม่เก็บ backup ที่มีข้อมูลส่วนตัวหรือรหัสผ่านใน public repository

## 3. สถานะงานล่าสุด

### เฟส 1

เจ้าของแจ้งปิดเฟส 1 แล้ว งานเก่าเกี่ยวกับบทความ 11-50 ไม่ใช่คำสั่งให้ทำซ้ำ การจะรายงานสถานะบทความปัจจุบันต้องอ่าน WordPress จริง โดยแยก published, scheduled และ draft

### หน้า /go/: งานล่าสุดที่ตรวจบนเว็บแล้ว

Commit `91ff33b` เปลี่ยนสองการ์ดดาวน์โหลดเดิมเป็นการ์ด FENIX FAST ใบเดียว:

- รูปที่อนุมัติ: `fenix-pro/assets/img/link-download-fenix-fast.webp`
- ขนาดภาพ: 1200 x 631 px, 83,400 bytes
- ข้อความใหญ่และชื่อบนกล่องเป็น FENIX FAST
- คำว่า FAST คงส้ม/ทองแบบภาพที่อนุมัติ
- ปุ่ม DOWNLOAD ทรงแคปซูล สีเขียวสว่างแบบ flat ไม่มีลูกศร ไม่มี gradient/bevel
- ไม่แสดง V4.0 ในภาพ แต่ชื่อ ZIP ยังมีเวอร์ชันตามไฟล์ต้นฉบับ
- ZIP: `fenix-pro/assets/downloads/FENIX_Fast_V4.0.zip`
- ZIP มีไฟล์ `FENIX_Fast_V4.0.ex5` ไม่ได้แก้ไขหรือรันไบนารี
- URL ดาวน์โหลด: https://fenixpro-th.com/wp-content/themes/fenix-pro/assets/downloads/FENIX_Fast_V4.0.zip
- SHA256: `2F7D9C1DAD91CD9E4A9AB06CB14D4A70A247E87E472488B467AEAB164FDEE4FD`
- วันที่ 3 กันยายน 2026 ตรวจหน้า `/go/` ได้ HTTP 200 แสดงการ์ดใหม่หนึ่งใบ รูปโหลดได้จริง และ ZIP จากเว็บมี hash ตรงกับไฟล์ต้นฉบับ
- ผลตรวจนี้ยืนยันการแสดงผลและความตรงกันของไฟล์ ไม่ได้ยืนยันความปลอดภัย ประสิทธิภาพ หรือผลการเทรดของ EA

ตั้งค่าที่ Customizer หมวด `28) หน้า Link Hub (สำหรับยิงแอด)`:

| Setting | หน้าที่ |
|---|---|
| `links_fast_enabled` | เปิด FENIX FAST และซ่อนสองการ์ดเก่า; default เป็น true |
| `links_fast_img` | URL รูปใหม่ |
| `links_fast_url` | URL ดาวน์โหลด |
| `links_fast_alt` | Alt ของรูป |

สองการ์ดเก่าและค่าที่เจ้าของเคยตั้งไม่ได้ถูกลบ ปิด `links_fast_enabled` จึงกลับไปใช้ค่าการ์ดเก่าได้ ไม่ควรลบ legacy settings/assets โดยพลการ

### ลิงก์ที่เจ้าของเปลี่ยนล่าสุด

- Instagram: https://www.instagram.com/fenixproea_th/
- ปุ่มเปิดบัญชี MT5 หน้า `/go/`: https://portal.zaurix.com?ref=202859
- ปุ่มผลเทรดจริงหน้า `/go/`: https://www.myfxbook.com/members/speccub/fenix-smart-core/12163940/dTX1kOz5MZwCABISlzuH
- คู่มือติดตั้ง MT5 หน้า `/go/` ใช้ข้อความสั้นว่า `คู่มือติดตั้ง MT5`; ปุ่มคู่มือสองปุ่มวางในแถวเดียวกัน
- FENIX FAST เป็นสินค้าดาวน์โหลดใหม่ ไม่ใช่คำสั่งเปลี่ยนชื่อแบรนด์ทั้งเว็บไซต์จาก FENIX PRO

## 4. ไฟล์งานและแหล่งข้อมูล

| ตำแหน่ง | ใช้ทำอะไร |
|---|---|
| `fenix-pro/functions.php` | Theme setup, defaults, helper, SEO fallback, security hardening |
| `fenix-pro/inc/customizer.php` | ลงทะเบียน settings/controls ผ่าน array loop |
| `fenix-pro/template-links.php` | หน้า `/go/` |
| `fenix-pro/front-page.php` | Homepage ตามธีม |
| `fenix-pro/template-*.php` | เทมเพลตเพจและคู่มือ |
| `fenix-pro/style.css` | Design system และ overrides; ไม่มี build step |
| `fenix-pro/assets/` | รูป โลโก้ JS และ ZIP ที่ธีมใช้ |
| `content-drafts/content-strategy-50.md` | แผนแคมเปญ 50 บท หมวด Pillar และตารางเผยแพร่ |
| `content-drafts/articles/` | ต้นฉบับ HTML 50 บท |
| `content-drafts/generated/content-manifest.json` | ลำดับบท slug เวลา และเส้นทางไฟล์ |
| `content-drafts/generated/media-v2/` | รูป final ที่ตัวตรวจใช้สำหรับบท 1-10 |
| `content-drafts/generated/media-v3/` | รูป final และ mapping สำหรับบท 11-50 |
| `content-drafts/generated/media-v3/wordpress-media-map.json` | 160 media records ของบท 11-50 พร้อม WordPress media IDs และ SEO fields |
| `content-drafts/generated/media-v3/wordpress-update-plan.json` | 40 post updates ที่บันทึกไว้ พร้อม post/media IDs; ตรวจให้ตรงกับเว็บก่อนใช้ |
| `content-drafts/generated/imports/fenix-content-001-050.xml` | WXR handoff/import เดิม ไม่ใช่ backup ล่าสุดของ WordPress และไม่ควร import ซ้ำ |
| `content-drafts/tools/` | ตัวสร้าง/แปลง/ตรวจไฟล์; อ่านสคริปต์ก่อนรันเพราะบางตัวเขียนทับ output |
| `content-drafts/seo-plan.md` | แผน SEO เก่าสำหรับฐานบทความเดิม ไม่ใช่สถานะล่าสุดของ 50 บท |
| `elementor-templates/*-widget.html` | สำเนา widget คู่มือที่เก็บไว้ ไม่ใช่หลักฐานว่า sync กับ admin ล่าสุด |
| `elementor-templates/fenix-pro-ea-home-starter.json` | Starter เก่า อย่า import ทับหน้า production |
| `launch-content/`, `content-drafts/about.md`, `privacy-policy.md`, `terms.md` | สำเนาเนื้อหาประกอบ ต้องตรวจหน้า live ก่อนอัปเดต |

ไฟล์ PNG/intermediate บางชุดถูก gitignore ไว้แต่ยังอยู่ในเครื่อง การ clone GitHub จึงไม่ใช่การคัดลอกทุกไฟล์ local ส่วนภาพ WebP final ที่ตัวตรวจอ้างอิงอยู่ใน repo

หากต้องนำไฟล์ไปเครื่องอื่น ให้ใช้ Git repository เดิมสำหรับโค้ด/ประวัติ และสำรอง ignored source assets ที่ต้องการเก็บแยกต่างหาก ไม่คัดลอก `.agents`, `.codex`, browser profiles หรือ credentials โดยอัตโนมัติ

## 5. ข้อกำหนดบทความและภาพที่เจ้าของยืนยัน

- เน้นคุณภาพและทราฟฟิกที่เกี่ยวข้อง ไม่ใช่เพิ่มจำนวนบทความเพื่อเลี้ยงเว็บ
- แคมเปญนี้มี 50 บทใหม่ แผนเดิมอ้างถึงบทความเก่า 36 บทเป็นฐาน แต่จำนวน live ปัจจุบันต้องตรวจใหม่
- จัดหมวดและทำ Topic Cluster: Pillar เชื่อมบทสนับสนุน บทสนับสนุนลิงก์กลับ Pillar และบทที่เกี่ยวข้องอย่างเป็นธรรมชาติ
- ตารางใน manifest คือวันเลขคู่ เวลา 08:00:00 เขตเวลา Asia/Bangkok ระหว่าง 26 กรกฎาคมถึง 4 พฤศจิกายน 2026
- ยึดวันเลขคู่ ไม่ตีความเป็นทุก 48 ชั่วโมงข้ามเดือน และอย่าเปลี่ยนวันเก่าเป็นวันปัจจุบันอัตโนมัติ
- เจ้าของต้องการรูปประกอบ 3-5 รูปต่อบท; ชุด final ที่ตรวจในเครื่องขณะส่งต่อมี 3 รูปต่อบท รวม 150 รูป และปก 50 รูป
- ปกใช้เอกลักษณ์แบรนด์ได้ แต่ภาพประกอบต้องหลากหลาย เป็นธรรมชาติ เรียบง่าย สอดคล้องกับเนื้อหา ไม่ทำดำ-ส้มเหมือนกันทั้งหมด
- ไม่ทำทุกภาพเป็นอินโฟกราฟิกหรือแผ่นข้อความ ไม่ใช้ layout/scene เดิมซ้ำทุกบท
- รูปต้องจับคู่กับบทความให้ถูกด้วย slug/article ID และ media mapping ไม่จับคู่จากลำดับอัปโหลดอย่างเดียว
- ชื่อไฟล์สื่อความหมาย ใช้ขีดกลางและ WebP; Alt อธิบายภาพจริง ไม่ยัดคีย์เวิร์ด และตรวจทั้งค่า Media Library กับ HTML ที่ render
- อย่าเปลี่ยนดีไซน์ที่อนุมัติแล้วนอกเหนือจากจุดที่เจ้าของสั่ง
- Title และ meta ต้องกระชับ เป็นภาษาไทยธรรมชาติ ตรวจความยาว/พื้นที่แสดงผลจริง ไม่ยืดจนล้น และไม่รับประกันอันดับ SEO
- ห้ามแต่งรีวิว สถิติผลตอบแทน หลักฐานการเทรด หรือคุณสมบัติ EA ที่ไม่ได้ตรวจยืนยัน
- อย่าลิงก์ไป draft/future posts ที่ผู้ชมเปิดไม่ได้; ตรวจผล render จริงเมื่อปลายทางเผยแพร่ และไม่รับรองว่าลิงก์อนาคตจะเปิดเองโดยยังไม่ตรวจกลไกที่ใช้อยู่
- อย่า import 50 บทหรือ media ซ้ำเพื่อแก้ลิงก์ ให้แก้เฉพาะรายการที่ยืนยัน ID/slug และสำรองก่อน

## 6. WordPress, Git และการเผยแพร่

- ธีม custom PHP + CSS + vanilla JS ไม่มี npm build; ต้องการ WordPress 6.0+ / PHP 7.4+ ตามข้อกำหนด repo
- ข้อความ/รูป/ลิงก์ใหม่เพิ่มใน `fenix_defaults()` และ Customizer แล้วอ่านด้วย `fenix_mod()` พร้อม escape
- ค่าที่บันทึกใน WordPress Customizer override default ใน PHP การแก้ default อย่างเดียวจึงอาจไม่เปลี่ยนค่าบนเว็บที่เคยตั้งไว้
- อย่าเปิดเพจที่ใช้ custom PHP template ด้วย Elementor เพราะอาจทับการแสดงผล; widget Elementor มีบริบทการแก้แยกต่างหาก
- Flow ที่ใช้: แก้โค้ด -> ตรวจ diff/lint/tests -> commit -> push -> WP Pusher ดึง subdirectory `fenix-pro`
- งานล่าสุด push แล้วตรวจ live พบการเปลี่ยนแปลง ใช้ `main` เป็น production; ให้ถือว่าการ push อาจอัปเดตเว็บจริงทันที
- การรับช่วงงานหรือเปิดโฟลเดอร์ไม่ใช่เหตุให้ push/deploy อัตโนมัติ
- โค้ดปลายทางก่อนเริ่มเตรียมเอกสารส่งต่อนี้อยู่ในสถานะ clean และตรงกับ `origin/main`
- เอกสารส่งต่อที่เพิ่มครั้งนี้เป็นการแก้ local เท่านั้น ไม่ได้ push/deploy หรือเปลี่ยนข้อมูล WordPress

แนวทางงานแต่ละครั้ง:

1. ตรวจ `git status --short --branch` และอ่านไฟล์ที่เกี่ยวข้อง
2. ไม่ revert งานเจ้าของหรืออีกโปรแกรม ไม่ใช้ reset แบบทำลายข้อมูล
3. ตรวจ syntax/tests ก่อน commit และตรวจ staged files ว่าไม่มี secrets/backup
4. Push เฉพาะเมื่ออยู่ในขอบเขตที่เจ้าของอนุญาตให้เผยแพร่
5. หลัง deploy ตรวจ URL จริงแบบไม่ล็อกอิน รวม cache, รูป, href, alt และมือถือในจุดที่แก้
6. หากเป็นไฟล์ดาวน์โหลด ตรวจ hash จากเว็บเทียบต้นฉบับโดยไม่รัน EA

## 7. ความปลอดภัยและสิทธิ์

- โค้ดปิด comments/pingbacks, ซ่อนเมนูความคิดเห็น, ปิด XML-RPC และจำกัด public REST user directory อยู่ใน `functions.php` อย่าเปิดกลับเพียงเพื่อให้เครื่องมือเชื่อมง่าย
- เจ้าของเคยขอข้ามงานตั้ง 2FA จึงไม่ใช่งานค้างที่อนุมัติให้เริ่มใหม่
- ไม่ได้มีรายงาน audit ความปลอดภัยครบระบบฉบับใหม่จากการเตรียมเอกสารนี้
- WordPress, GitHub, Google และเครื่องมือภาพต้องตรวจการเชื่อมต่อจริงใน Claude แยกกัน การมีไฟล์หรือ URL ไม่เท่ากับมีสิทธิ์เขียน
- อย่าฝังรหัสผ่าน token หรือ application password ใน `CLAUDE.md`, source code, บทสนทนาส่งต่อ หรือ public repository
- อย่าเปิด auto-approve แบบไม่จำกัดหรือปิด security protections เพื่อย้ายผู้ช่วย

## 8. การตรวจที่ทำซ้ำได้

จาก root ของ repo:

```powershell
git status --short --branch
node content-drafts/tools/validate-content-media.js
php -l fenix-pro/functions.php
php -l fenix-pro/inc/customizer.php
php -l fenix-pro/template-links.php
php tests/link-hub-downloads.php
git diff --check
```

เครื่องนี้ไม่มี PHP บน PATH ในงานก่อนหน้า จึงใช้ portable PHP 8.4.25 สำหรับการตรวจ โดยไม่แก้ system PATH:

```powershell
$php = Join-Path $env:TEMP 'fenix-php-8.4.25\php.exe'
if (Test-Path -LiteralPath $php) {
    & $php -n tests/link-hub-downloads.php
}
```

TEMP อาจถูกล้างในภายหลัง; ถ้าไม่พบ runtime ให้แจ้งข้อจำกัดหรือใช้ PHP ที่ติดตั้งอย่างถูกต้อง ไม่รายงานว่าทดสอบผ่านโดยไม่ได้รัน

ผลตรวจ local ที่รันซ้ำระหว่างส่งต่อวันที่ 3 กันยายน 2026:

- ตัวตรวจ media ผ่าน: 50 บทความ, 150 ภาพประกอบ, 50 ปก, 160 WordPress media records ใน v3 mapping
- Link Hub render tests ผ่าน 10 assertions รวมการซ่อน/คืนสองการ์ดเก่า ลิงก์ ZIP รูป และการ escape Customizer values
- ผลตรวจ local ไม่ยืนยันว่าโพสต์ทั้ง 50 เผยแพร่แล้ว ไม่ยืนยัน cron และไม่ยืนยันว่า Media Library ปัจจุบันไม่ถูกเปลี่ยนภายหลัง

## 9. สิ่งที่ทำต่อเมื่อเปิดใน Claude

1. อ่านเอกสารนี้กับ `CLAUDE.md` และตรวจ Git แบบ read-only
2. บอกเจ้าของว่าเข้าใจ scope FENIX PRO และงานล่าสุดแล้ว ไม่เริ่มสร้างเว็บหรือบทความใหม่
3. ตรวจเครื่องมือที่ใช้งานได้จริง โดยไม่อ่าน secret stores; ขอให้เจ้าของล็อกอินเฉพาะระบบที่จำเป็นต่อคำสั่งถัดไป
4. เมื่อต้องแก้บทความ ตรวจ ID/slug/status กับ WordPress ก่อน และสำรองเนื้อหา/metadata เดิม
5. รอคำสั่งงานใหม่ ไม่มีคำสั่งให้รื้อเฟส 1, เปลี่ยนโลโก้, เปิด comments หรือเปลี่ยนการตั้งเวลาเดิมจากการส่งต่องานครั้งนี้

EA Special (`easpecial.com`) เป็นเว็บแม่รวม EA อีกโปรเจกต์หนึ่ง ไม่รวมใน scope ของ repo นี้ อย่านำโค้ดหรือ branding ไปทับกัน

## 10. วิธีเปิดโปรเจกต์

Claude Desktop: แท็บ Code -> Local -> Select folder -> เลือก `D:\EA VIDEO\fenix-pro-repo` แล้วใช้ข้อความเริ่มงานด้านล่าง หากบัญชีไม่เปิดใช้ Code ต้องจัดการสิทธิ์/แพ็กเกจในบัญชี Claude เอง

หรือใช้ CLI ที่ตรวจพบในเครื่อง:

```powershell
Set-Location -LiteralPath 'D:\EA VIDEO\fenix-pro-repo'
claude
```

ข้อความเริ่มงาน:

> รับช่วงโปรเจกต์ FENIX PRO จาก Codex ในโฟลเดอร์นี้ อ่าน CLAUDE.md และ CLAUDE-HANDOFF.md ให้ครบก่อน ตรวจสถานะ Git และสรุปว่าเข้าใจงานล่าสุดแล้ว โดยยังไม่แก้ไฟล์ ไม่ import บทความ ไม่ push/deploy และไม่เปลี่ยนข้อมูล WordPress เฟส 1 จบแล้ว งานล่าสุดคือการ์ด FENIX FAST หน้า /go/ ให้รักษางานเดิมทั้งหมด ตรวจว่าเครื่องมือใดเชื่อมได้จริง แล้วรอคำสั่งใหม่จากฉัน

การเปิดโฟลเดอร์ทำให้ Claude Code ใช้ไฟล์โปรเจกต์ได้ แต่ไม่ใช่การนำเข้าประวัติแชต Codex หรือย้ายสิทธิ์เครื่องมือทั้งหมด เอกสารทางการอธิบาย [การเลือกโฟลเดอร์ใน Claude Code Desktop](https://code.claude.com/docs/en/desktop-quickstart) และ [การอ่าน CLAUDE.md](https://code.claude.com/docs/en/memory)
