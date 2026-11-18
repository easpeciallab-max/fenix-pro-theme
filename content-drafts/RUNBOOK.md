# วิธีเดินงานบทความทีละชุด (106 บท · 15 พ.ย. 2026 – 28 ก.พ. 2027)

เจ้าของอนุมัติแผนและสั่งให้ทำจนครบเมื่อ 18 พ.ย. 2026 ไม่ต้องถามรายบท
คิวทั้งหมดอยู่ใน `plan-queue.json` (ลำดับไฟล์ วันที่ลง หมวดหมู่ สถานะ)

## รอบหนึ่ง = 6 บท

**1. ดูคิวถัดไป**
```
php content-drafts/tools/publish-batch.php --next=6
```

**2. สั่งเขียน** — เอเจนต์ 1 ตัวต่อ 1 บท ชี้ให้อ่าน `writing-brief.md` + `published-slugs.md`
และระบุในคำสั่งทุกครั้ง: ลำดับไฟล์ · slug · คีย์เวิร์ด · ขอบเขตเนื้อหา · ห้ามใส่ตัวเลขผลงาน FENIX ·
ให้รัน `check-draft.php` เอง · วันที่ "ตรวจทานล่าสุด" = วันที่ทำงานจริง

**3. ตรวจร่าง**
```
php content-drafts/tools/check-draft.php content-drafts/articles/06*.html
```
แก้ที่ยังไม่ผ่านเอง (ส่วนใหญ่เป็นความยาว meta_description)

**4. ตรวจลิงก์แหล่งข้อมูลว่าเปิดได้จริง**
```
for u in $(grep -ho 'href="https\?://[^"]*"' content-drafts/articles/06*.html | grep -v fenixpro | sed 's/href="//;s/"$//' | sort -u); do
  printf '%s %s\n' "$(curl -s -o /dev/null -w '%{http_code}' -L --max-time 20 -A 'Mozilla/5.0' "$u")" "$u"
done
```
บางเว็บบล็อก curl (FRED, The Block, Investopedia) ให้เปิดดูในเบราว์เซอร์แทนก่อนตัดสิน

**5. หารูป** — เปิดแท็บ pexels.com ไว้ แล้วยิง fetch ในหน้าเดียวจบทั้งชุด
```js
const qs={k1:"query หนึ่ง", k2:"query สอง"};const out={};
for(const k in qs){const r=await fetch('/search/'+encodeURIComponent(qs[k])+'/',{headers:{accept:'text/html'}});
const t=await r.text();const m=[...t.matchAll(/https:\/\/images\.pexels\.com\/photos\/(\d+)\/pexels-photo-\1\.(jpeg|png)/g)];
out[k]=[...new Set(m.map(x=>x[1]+'.'+x[2]))].slice(0,8);}JSON.stringify(out)
```
**ต้องดูรูปจริงก่อนใช้** ทำ contact sheet ด้วย ffmpeg (`xstack`) แล้วเปิดดู
รูปซ้ำกับบทความก่อนหน้าไม่ได้ · เลือกเสร็จแล้วโหลด `?w=1600` → `ffmpeg -vf scale=1200:-2` เป็น WebP
ภาพแนวตั้งให้ครอบเป็น 3:2 ก่อน

**6. แก้ alt ให้ตรงกับรูปจริง** (คำค้นมักได้รูปคนละอย่างกับที่คนเขียนคิดไว้)
แล้วรัน check-draft ซ้ำ

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

**9. บันทึกสถานะ + commit**
```
php content-drafts/tools/publish-batch.php --done=slug1:id1,slug2:id2
git add content-drafts && git commit && git push
```

## กฎที่พลาดแล้วเจ็บ (เจอมาแล้ว)

- **ห้ามเชื่อรายงานของเอเจนต์เรื่องตัวเลข** ทุกตัวเลขในบทความต้องเปิดต้นทางดูเอง
- **เอเจนต์อาจเกลาไฟล์ต่อหลังเราลงเว็บไปแล้ว** ถ้ามีรายงานเข้ามาหลังลง ให้ rebuild payload แล้วอัปเดตโพสต์เดิม
- **อย่าสั่ง `loading=eager` ตอนตรวจหน้า** ไม่งั้นจะไม่เห็นบั๊กที่เกิดตอนรูปยังไม่โหลด
- รูปที่อัปแล้วต้องตั้ง alt เสมอ (อยู่ในสคริปต์อัปโหลดแล้ว)
