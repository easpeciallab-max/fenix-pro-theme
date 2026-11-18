# วิธีเดินงานบทความทีละชุด (แผนคีย์เวิร์ดจริง 2027)

แผนและเหตุผลอยู่ใน `seo-plan-2027.md` · คิวอยู่ใน `plan-queue.json` · โครงกลุ่มอยู่ใน `clusters.json`
คำค้นจริงที่เก็บมาอยู่ใน `keyword-research-2026-11.md`

## รอบหนึ่ง = 3-4 บทต่อเอเจนต์ · รันพร้อมกันได้ 4-6 เอเจนต์

**1. ดูคิวถัดไป**
```
php content-drafts/tools/publish-batch.php --next=12
```
คิวมีคอลัมน์ `pillar` (เสาหลักของกลุ่ม) และ `tier` (A = ดันให้ติด, B = บทลูกเสริมความลึก)

**2. สั่งเขียน** — ในคำสั่งต้องมีครบ: ชื่อไฟล์ · **ชื่อเรื่องที่กำหนดไว้ (ห้ามเอเจนต์เปลี่ยน)** · คำค้นเป้า ·
ขอบเขตเนื้อหา · URL เสาหลักที่ต้องลิงก์ขึ้น · ห้ามเอ่ยชื่อรุ่นสินค้า · ให้รัน `check-draft.php` เอง

**3. ตรวจร่าง**
```
php content-drafts/tools/check-draft.php content-drafts/articles/1??-*.html
```
ตัวตรวจคุมกฎใหม่ด้วย: ย่อหน้าแรกเป็นคำตอบ (120-420 ตัวอักษร) · ต้องมีส่วน `<h2 id="faq">` 3-6 คำถาม ·
ลิงก์ภายในไม่เกิน 8 · ห้ามมีชื่อรุ่นสินค้า

**4. ตรวจลิงก์แหล่งข้อมูลว่าเปิดได้จริง**
```
for u in $(grep -ho 'href="https\?://[^"]*"' content-drafts/articles/1??-*.html | grep -v fenixpro | sed 's/href="//;s/"$//' | sort -u); do
  printf '%s %s\n' "$(curl -s -o /dev/null -w '%{http_code}' -L --max-time 20 -A 'Mozilla/5.0' "$u")" "$u"
done
```
bls.gov และบางเว็บบล็อก curl ให้เปิดในเบราว์เซอร์ยืนยันแทน

**5. ตรวจตัวเลขที่อ้างด้วยตัวเอง** เปิดหน้าต้นทางอ่านจริงทุกตัวเลข ห้ามเชื่อรายงานของเอเจนต์

**6. หารูป** — เปิดแท็บ pexels.com แล้วยิง fetch ในหน้าเดียวจบทั้งชุด
```js
const qs={k1:"query หนึ่ง", k2:"query สอง"};const out={};
for(const k in qs){const r=await fetch('/search/'+encodeURIComponent(qs[k])+'/',{headers:{accept:'text/html'}});
const t=await r.text();const m=[...t.matchAll(/https:\/\/images\.pexels\.com\/photos\/(\d+)\/pexels-photo-\1\.(jpeg|png)/g)];
out[k]=[...new Set(m.map(x=>x[1]+'.'+x[2]))].slice(0,8);}JSON.stringify(out)
```
**ต้องดูรูปจริงก่อนใช้** ทำ contact sheet ด้วย ffmpeg (`xstack`) แล้วเปิดดู
โหลด `?w=1600` → `ffmpeg -vf scale=1200:-2` เป็น WebP · ภาพแนวตั้งครอบเป็น 3:2 ก่อน
**แก้ alt ให้ตรงกับรูปจริง** แล้วรัน check-draft ซ้ำ

**7. อัปรูป**
```
bash content-drafts/tools/upload-images.sh <โฟลเดอร์รูป> <โฟลเดอร์ทำงาน> <alts.json>
```

**8. สร้าง payload แล้วส่งขึ้นเว็บ**
```
php content-drafts/tools/publish-batch.php --work=<โฟลเดอร์ทำงาน> --slugs=a,b,c
curl -X POST ".../wp-json/wp/v2/posts" -H "X-Authorization: Basic $AUTH" --data-binary @post-<slug>.json
```
สถานะ `future` + วันที่ตามแผน · ถ้าวันที่ผ่านไปแล้วให้ใช้ `publish`

**9. เชื่อมเข้ากลุ่ม** — เติม slug ใหม่ลง `clusters.json` ใต้เสาหลักของมัน แล้วรันสคริปต์วางลิงก์ขึ้น-ลงใหม่
(สคริปต์อยู่ใน scratchpad: `link-clusters.php` · ถ้าหายให้เขียนใหม่จากวิธีในไฟล์นี้)

**10. บันทึกสถานะ + commit**
```
php content-drafts/tools/publish-batch.php --done=slug1:id1,slug2:id2
git add content-drafts && git commit && git push
```

## กฎที่พลาดแล้วเจ็บ (เจอมาแล้วจริง)

- **ห้ามเชื่อรายงานของเอเจนต์เรื่องตัวเลข** ทุกตัวเลขต้องเปิดต้นทางดูเอง
- **เอเจนต์อาจเกลาไฟล์ต่อหลังเราลงเว็บไปแล้ว** ถ้ามีรายงานเข้ามาหลังลง ให้ rebuild payload แล้วอัปเดตโพสต์เดิม
- **อย่าใช้ `git add -A` ตอนที่เอเจนต์ยังเขียนอยู่** จะกวาดไฟล์ครึ่ง ๆ กลาง ๆ หรือการลบไฟล์เข้า commit
  ถ้าพลาดไปแล้ว เรียกคืนด้วย `git show <commit>:<path>`
- **อย่าสั่ง `loading=eager` ตอนตรวจหน้า** ไม่งั้นจะไม่เห็นบั๊กที่เกิดตอนรูปยังไม่โหลด
- **เลขหน้าไฟล์ต้องตรงกับคิว** ถ้าเปลี่ยนแผนแล้วเลขชนกัน ให้เปลี่ยนชื่อไฟล์ก่อน ไม่งั้น `publish-batch.php` หยิบไฟล์ผิด
- **คำค้นห้ามซ้ำกันระหว่างบท** บทเก่ากับบทใหม่แย่งคำเดียวกันคือเสียทั้งคู่ ตรวจก่อนตั้งชื่อเรื่องเสมอ
- รูปที่อัปแล้วต้องตั้ง alt เสมอ (อยู่ในสคริปต์อัปโหลดแล้ว)
