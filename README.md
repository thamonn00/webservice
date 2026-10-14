# 🐾 Lost Pet — เว็บเซอร์วิสแจ้งสัตว์หาย / พบสัตว์
Laravel 9, API ใช้ Sanctum Bearer Token และมีหน้าเว็บสำหรับดูข้อมูลสัตว์

## เริ่มต้นแบบพื้นฐาน (Laragon)

1. สร้างฐานข้อมูล `lostpet` ใน phpMyAdmin โดยใช้ `utf8mb4`
2. ถ้ายังไม่มีไฟล์ `.env` ให้คัดลอกจาก `.env.example` แล้วตั้งค่า `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` และ `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`
3. เปิด terminal ที่โฟลเดอร์โปรเจกต์ แล้วรัน:

   ```bash
   composer install
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   ```

   ใช้ `key:generate` เฉพาะครั้งแรกที่ `APP_KEY` ยังว่าง `migrate` สร้างตารางตาม migration ที่มีอยู่ และ `db:seed` เติมข้อมูลตั้งต้น ห้ามใช้ `migrate:fresh` กับฐานข้อมูลที่ต้องการเก็บข้อมูล
   Migration ชุดนี้ใช้กับฐานข้อมูลใหม่ หากเป็นฐานข้อมูลจาก migration ชุดเดิม ให้วางแผนย้ายข้อมูลก่อน ห้ามรัน migrate โดยไม่ตรวจสอบ
4. ถ้าอัปโหลดรูป ให้รัน `php artisan storage:link` เพียงครั้งเดียว

บัญชีตัวอย่าง `somchai@example.com` และ `somying@example.com` ใช้ได้เฉพาะ local/testing รหัสผ่าน `password123` ห้ามนำบัญชีตัวอย่างไปใช้บน production

Web: `/login`, `/register`, `/pets`, `/api-docs`.

## ออกแบบฐานข้อมูล (9 ตารางของระบบ + ตาราง token)
```
users ─┬─< pets >─┬─ species ─< breeds
       │          │
       └─< reports ┘(pet_id เว้นว่างได้)      provinces ─< districts ─< subdistricts
               │                                                         ▲
               └── subdistrict_id ───────────────────────────────────────┘
photos (photoable_type + photoable_id)  ← ใช้ร่วมกันทั้ง pets และ reports
personal_access_tokens (Sanctum)
```
| ตาราง | หน้าที่ / จุดที่ออกแบบ |
|---|---|
| `users` | name, email, phone, password, `role` (user/admin) |
| `species`, `breeds` | ชนิดและสายพันธุ์ (สายพันธุ์ผูกกับชนิด ตรวจตอน insert ว่าสายพันธุ์ตรงกับชนิด) |
| `pets` | สัตว์เลี้ยงของสมาชิก (user_id, species_id, breed_id, name, color, gender, age, description) |
| `reports` | ประกาศ **หาย (lost) / พบ (found)** — `pet_id` เป็น null ได้ เพราะประกาศ "พบ" มักไม่รู้ว่าเป็นสัตว์ของใคร จึงมี species_id/breed_id/color ในตัวเอง; ที่อยู่เก็บเป็น `subdistrict_id` เพียงคอลัมน์เดียว (อำเภอ/จังหวัดดึงต่อจากความสัมพันธ์ ไม่เก็บซ้ำ); `status` open/closed |
| `provinces`, `districts`, `subdistricts` | 77 จังหวัด / 928 อำเภอ / 7,436 ตำบล จาก `database/data/*.json` |
| `photos` | **ตารางรูปตารางเดียว** (polymorphic) |

**รูปภาพรวมตารางเดียวดีกว่า:** ของเดิมแยก `pet_photos`, `report_photos` (และ sighting_photos) ซึ่งคอลัมน์เหมือนกันทุกประการ
รวมเป็น `photos` ตารางเดียวด้วย `photoable_type/photoable_id` ทำให้โค้ด อัปโหลด/ลบ/รูปหลัก เขียนครั้งเดียวใช้ได้ทุกตาราง
และเพิ่มตารางใหม่ที่มีรูปได้ทันที (ลบสัตว์/ประกาศ → ลบรูปและไฟล์ให้อัตโนมัติ)

เอา **การแจ้งเบาะแส (sightings)** ออกแล้วตามที่ต้องการ

## API (ส่ง `Accept: application/json` ทุกครั้ง)
| งาน | Method + URL | Token |
|---|---|---|
| สมัคร | `POST /api/register` | - |
| **1.2 Login** (ได้ token) | `POST /api/login` {email,password} | - |
| **1.2 Logout** | `POST /api/logout` | ✔ `Authorization: Bearer <token>` |
| ข้อมูลตัวเอง | `GET /api/me` | ✔ |
| **1.3 getAll** | `GET /api/pet` (alias `GET /api/pets`; กรอง: q, species_id, breed_id, gender) | ✔ |
| **1.3 getByID** | `GET /api/pet/{id}` | ✔ |
| **1.4 insert** | `POST /api/pet` | ✔ |
| **1.4 update** | `PUT/PATCH /api/pet/{id}` | ✔ |
| **1.4 delete** | `DELETE /api/pet/{id}` | ✔ |
| ประกาศหาย/พบ getAll/getByID | `GET /api/reports` (alias `GET /api/report`; กรอง: q, type=lost/found, status, species_id) | ✔ |
| ประกาศหาย/พบ insert/update/delete | `POST /api/reports` · `PUT/PATCH /api/reports/{id}` · `DELETE /api/reports/{id}` | ✔ |
| ข้อมูลอ้างอิง | `GET /api/species` `/breeds` `/provinces` `/districts?province_id=` `/subdistricts?district_id=` (และ `/{id}`; `districts`/`subdistricts` ส่ง `?per_page=` ได้เพราะข้อมูลเยอะ) | ✔ |

รายละเอียดพารามิเตอร์/ตัวอย่าง curl ดูที่หน้า `/api-docs`. อัปโหลดรูปส่ง `multipart/form-data` ฟิลด์ `photos[]` (หรือ `photo_urls[]`);
แก้ไขพร้อมไฟล์ให้ `POST` แล้วใส่ `_method=PUT`; ลบรูปด้วย `delete_photo_ids[]`.

**1.5 เทคนิคเพิ่มเติม:** ค้นหา/กรอง (`q`, `species_id`, `breed_id`, `gender`) + แบ่งหน้า (`per_page`) + รวมรูป/ชนิด/เจ้าของใน response เดียว + หน้าเว็บมีปุ่มคัดลอก URL/curl/JSON ทุกเส้น

## เว็บ (login ก่อนถึงเข้าได้ ยกเว้น `/api-docs`)
- `/login` เข้าสู่ระบบ · `/register` สมัครสมาชิก · `/pets` แสดงข้อมูลสัตว์ + ค้นหา · `/pets/{id}` ดูรายตัว + JSON + ปุ่มก๊อป · `/reports` ประกาศหาย/พบ + กรอง type/status · `/reports/{id}` ดูรายประกาศ + JSON + ปุ่มก๊อป · `/api-docs` ตาราง API + ปุ่มก๊อปทุกเส้น · ปุ่มออกจากระบบอยู่บน nav (โชว์ชื่อ+อีเมล user ที่ล็อกอิน)

## 1.1 เก็บโปรเจกต์บน GitHub (ต้อง push เอง)
```bash
cd c:\laragon\www\minipro_webservice\pet\webservice1
git add .
git commit -m "เว็บมินิมอล + API pet ครบ 1.2-1.5"
git push origin main
```

## Seeder / Factory
`php artisan db:seed` เรียก seeder ข้อมูลตั้งต้น และเพิ่มสัตว์ 100 + ประกาศ 100 ตัวละ 1 รูปเฉพาะ local/testing (ข้ามถ้ามีข้อมูลแล้ว ใช้ `migrate:fresh --seed` เพื่อเริ่มใหม่) บัญชี admin ใช้ค่าจาก `.env`
(ใช้ `PetFactory`, `ReportFactory`, `PhotoFactory`, `UserFactory`) — `User/Species/Geography` รันซ้ำได้ไม่สร้างข้อมูลซ้ำ
