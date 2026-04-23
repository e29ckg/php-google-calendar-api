# Google Calendar API Service (PHP)

โปรเจ็กต์ RESTful API ที่พัฒนาด้วย PHP สำหรับเชื่อมต่อและจัดการข้อมูลใน Google Calendar รองรับการทำงานแบบ CRUD (Create, Read, Update, Delete) พร้อมระบบรักษาความปลอดภัยด้วย API Key และการบันทึกประวัติการใช้งาน (Access Log)

## 📋 สิ่งที่ต้องเตรียม (Prerequisites)
* **PHP:** เวอร์ชัน 7.4 หรือสูงกว่า (ต้องเปิดใช้งาน `extension=zip` ใน `php.ini`)
* **Composer:** สำหรับติดตั้ง Library
* **Google Cloud Console:**
  * เปิดใช้งาน **Google Calendar API**
  * สร้าง **Service Account** และดาวน์โหลดไฟล์คีย์แบบ JSON (`credentials.json`)
* **Google Calendar:** ต้องแชร์ปฏิทินให้กับอีเมลของ Service Account และให้สิทธิ์เป็น *"Make changes to events"*

## 🛠️ โครงสร้างไฟล์ (Project Structure)
```text
google-calendar-service/
├── api_auth.php           # ศูนย์กลางตรวจสอบ API Key และบันทึก Log (Middleware)
├── api_get_events.php     # API สำหรับดึงข้อมูลนัดหมาย (Read)
├── api_create_event.php   # API สำหรับสร้างนัดหมาย (Create)
├── api_update_event.php   # API สำหรับแก้ไขนัดหมาย (Update)
├── api_delete_event.php   # API สำหรับลบนัดหมาย (Delete)
├── composer.json          # ไฟล์กำหนดเวอร์ชัน Google API Client
├── credentials.json       # ไฟล์คีย์ Service Account (⚠️ ห้ามเผยแพร่)
├── test_call_api.php      # ตัวอย่างโค้ด cURL สำหรับเรียกใช้ API
└── vendor/                # โฟลเดอร์ Library (สร้างอัตโนมัติเมื่อรัน Composer)
```

## 🚀 การติดตั้งและตั้งค่า (Installation & Setup)

1. คัดลอกโปรเจ็กต์ไปวางในโฟลเดอร์เว็บเซิร์ฟเวอร์ (เช่น `htdocs`)
2. นำไฟล์คีย์จาก Google Cloud มาเปลี่ยนชื่อเป็น `credentials.json` และวางในโฟลเดอร์หลัก
3. เปิด Terminal แล้วรันคำสั่งติดตั้ง Library:
   ```bash
   composer install
   ```
4. **ตั้งค่า API Key:** เปิดไฟล์ `api_auth.php` และเปลี่ยนค่าของตัวแปร `$SECRET_API_KEY` ให้เป็นรหัสผ่านลับของคุณ

## 🔐 การตรวจสอบสิทธิ์ (Authentication)
ทุกครั้งที่เรียกใช้งาน API จะต้องส่ง HTTP Header ที่ชื่อว่า `X-API-KEY` มาด้วยเสมอ ตัวอย่าง:
`X-API-KEY: my_super_secret_key_2026_xoxo`

## 📡 API Endpoints

รูปแบบ Method ที่ใช้: **POST**
รูปแบบ Header ข้อมูล: `Content-Type: application/json`

### 1. ดึงนัดหมายที่กำลังจะมาถึง (Get Events)
* **Endpoint:** `/api_get_events.php`
* **Request Body:**
  ```json
  {
    "calendar_id": "your-email@example.com",
    "max_results": 10
  }
  ```

### 2. สร้างนัดหมาย (Create Event)
* **Endpoint:** `/api_create_event.php`
* **Request Body:**
  ```json
  {
    "calendar_id": "your-email@example.com",
    "summary": "ชื่อกิจกรรม",
    "description": "รายละเอียดกิจกรรม",
    "location": "สถานที่",
    "start_time": "2026-04-24T10:00:00+07:00",
    "end_time": "2026-04-24T11:00:00+07:00"
  }
  ```

### 3. แก้ไขนัดหมาย (Update Event)
* **Endpoint:** `/api_update_event.php`
* **Request Body:**
  ```json
  {
    "calendar_id": "your-email@example.com",
    "event_id": "รหัสกิจกรรม (Event ID)",
    "summary": "ชื่อกิจกรรม (ใหม่)",
    "description": "รายละเอียดกิจกรรม (ใหม่)"
  }
  ```

### 4. ลบนัดหมาย (Delete Event)
* **Endpoint:** `/api_delete_event.php`
* **Request Body:**
  ```json
  {
    "calendar_id": "your-email@example.com",
    "event_id": "รหัสกิจกรรมที่ต้องการลบ"
  }
  ```

## ⚠️ ข้อควรระวังและการนำขึ้น Git
* **ห้าม** อัปโหลดไฟล์ `credentials.json` และโฟลเดอร์ `vendor/` ขึ้น Git (ควรเพิ่มลงในไฟล์ `.gitignore` เสมอ)
* ไฟล์ `api_access.log` จะถูกสร้างอัตโนมัติเพื่อเก็บประวัติการเข้าใช้งาน ควรตรวจสอบขนาดไฟล์เป็นระยะหากมีปริมาณการใช้งานสูง