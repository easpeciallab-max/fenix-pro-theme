# FENIX PRO EA — WordPress Theme

ธีม WordPress แบบ custom สำหรับเว็บ Landing ขาย **FENIX PRO EA** (ระบบช่วยเทรดอัตโนมัติบน MetaTrader 5) — โทนดำ–ส้มไฟ–ทอง, ฟอนต์ Noto Sans Thai, รองรับมือถือเต็มรูปแบบ, ทุก CTA ลิงก์ไป LINE

ตัวธีมอยู่ในโฟลเดอร์ [`fenix-pro/`](./fenix-pro) — root ของ repo เก็บเฉพาะเอกสารสำหรับพัฒนา

## โครงสร้าง
```
.
├── CLAUDE.md          # บริบทสำหรับ Claude Code (อ่านก่อนแก้โค้ด)
├── README.md
├── .gitignore
└── fenix-pro/         # ← ตัวธีม WordPress (WP Pusher subdirectory)
    ├── style.css      # theme header + design system
    ├── functions.php  # setup + defaults + helpers
    ├── inc/customizer.php
    ├── front-page.php # หน้าแรก (hub)
    ├── template-*.php # เทมเพลตเพจ (Backtest, Forward, Pricing, Install, Risk)
    ├── header.php / footer.php / index.php / single.php / page.php
    ├── assets/        # js + รูป
    └── readme.txt     # คู่มือผู้ใช้ (เจ้าของเว็บ)
```

## พัฒนา
ไม่มี build step — แก้ไฟล์แล้วใช้ได้เลย

```bash
# lint ก่อน commit
php -l fenix-pro/functions.php
find fenix-pro -name "*.php" -exec php -l {} \;
```

สถาปัตยกรรมหลัก: **ทุกเนื้อหาแก้ผ่าน WordPress Customizer** ไม่ hardcode — เพิ่ม setting ใหม่โดยเพิ่ม default ใน `fenix_defaults()` (functions.php) + field ใน `$sections` (inc/customizer.php) loop จะ register ให้เอง รายละเอียดทั้งหมดดู [CLAUDE.md](./CLAUDE.md)

## Deploy ด้วย WP Pusher (GitHub → WordPress)

1. ติดตั้งปลั๊กอิน **WP Pusher** ในเว็บ → เชื่อมบัญชี GitHub (กดปุ่มเดียวจาก WP admin)
2. **WP Pusher → Install Theme**
   - **Theme repository**: `your-username/your-repo`
   - **Repository branch**: `main`
   - **Repository subdirectory**: `fenix-pro`  ← สำคัญ (ธีมอยู่ใน subdir)
   - **Push-to-Deploy**: ติ๊กถ้าต้องการให้อัปเดตอัตโนมัติทุกครั้งที่ push (WP Pusher ตั้ง webhook ให้เอง)
3. ติดตั้งเสร็จ → ไป รูปแบบ → ธีม → เปิดใช้งาน
4. อัปเดตภายหลัง: `git push` แล้ว WP Pusher ดึงให้เอง (หรือกด **Update** ใน WP Pusher ถ้าไม่เปิด auto)

**หมายเหตุ**
- WP Pusher เวอร์ชันฟรีรองรับ **repo สาธารณะ** เท่านั้น (private ต้องมี license) — repo นี้เป็นสาธารณะได้อย่างปลอดภัย เพราะธีม**ไม่มีข้อมูลลับ** (ลิงก์ LINE, ราคา ฯลฯ เก็บใน WordPress DB ผ่าน Customizer ไม่ใช่ในโค้ด)
- ไม่ต้องติดตั้ง Git บนเซิร์ฟเวอร์
- ถ้าเปิด Push-to-Deploy: ทุก push ขึ้น production ทันที แนะนำ lint ให้ผ่านก่อน หรือใช้ branch แยกสำหรับ staging

## อัปโหลดแบบ manual (ทางเลือกสำรอง)
ถ้าไม่ใช้ WP Pusher: zip เฉพาะโฟลเดอร์ `fenix-pro/` แล้วอัปที่ รูปแบบ → ธีม → อัปโหลดธีม

## License
GPL v2 or later
