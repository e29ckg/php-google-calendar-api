# Google Calendar API Service (PHP)

โปรเจ็กต์นี้คือ RESTful API ที่พัฒนาด้วย PHP สำหรับเชื่อมต่อกับ Google Calendar API โดยรองรับการทำงานพื้นฐานครบถ้วน ได้แก่ การสร้าง (Create), การแก้ไข (Update) และการลบ (Delete) นัดหมายในปฏิทิน เหมาะสำหรับนำไปเชื่อมต่อกับแอปพลิเคชันอื่นๆ (เช่น Mobile App, หน้าเว็บ Frontend หรือระบบหลังบ้าน)

## 📋 สิ่งที่ต้องเตรียม (Prerequisites)
* **PHP:** เวอร์ชัน 7.4 หรือสูงกว่า
* **Composer:** สำหรับติดตั้ง Library
* **Google Cloud Console:**
  * เปิดใช้งาน **Google Calendar API**
  * สร้าง **Service Account** และดาวน์โหลดไฟล์คีย์แบบ JSON (`credentials.json`)
* **Google Calendar:** ต้องแชร์ปฏิทินให้กับอีเมลของ Service Account และให้สิทธิ์เป็น *"Make changes to events"*

## 🛠️ โครงสร้างไฟล์ (Project Structure)
```text
google-calendar-service/
├── api_create_event.php   # API สำหรับสร้างนัดหมาย
├── api_update_event.php   # API สำหรับแก้ไขนัดหมาย
├── api_delete_event.php   # API สำหรับลบนัดหมาย
├── composer.json          # ไฟล์กำหนดเวอร์ชัน Google API Client
├── credentials.json       # ไฟล์คีย์ Service Account (คุณต้องนำมาใส่เอง)
└── vendor/                # โฟลเดอร์ Library (สร้างอัตโนมัติเมื่อรัน Composer)
```

## 🚀 การติดตั้ง (Installation)

1. คัดลอกโค้ดทั้งหมดไปวางไว้ในโฟลเดอร์ของเว็บเซิร์ฟเวอร์ (เช่น `htdocs` หรือ `/var/www/html`)
2. นำไฟล์คีย์ที่ได้จาก Google Cloud มาเปลี่ยนชื่อเป็น `credentials.json` และวางไว้ในโฟลเดอร์หลักของโปรเจ็กต์
3. เปิด Terminal ชี้มาที่โฟลเดอร์โปรเจ็กต์ แล้วรันคำสั่ง:
   ```bash
   composer install
   ```

## 📡 การใช้งาน API (API Endpoints)

API ทั้งหมดรับข้อมูลผ่าน **HTTP POST** ในรูปแบบ **JSON**

### 1. สร้างนัดหมาย (Create Event)
* **Endpoint:** `/api_create_event.php`
* **Request Body (JSON):**
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

### 2. แก้ไขนัดหมาย (Update Event)
* **Endpoint:** `/api_update_event.php`
* **Request Body (JSON):**
  ```json
  {
    "calendar_id": "your-email@example.com",
    "event_id": "รหัสกิจกรรมที่ได้จากการสร้าง",
    "summary": "ชื่อกิจกรรม (ใหม่)",
    "description": "รายละเอียดกิจกรรม (ใหม่)"
  }
  ```

### 3. ลบนัดหมาย (Delete Event)
* **Endpoint:** `/api_delete_event.php`
* **Request Body (JSON):**
  ```json
  {
    "calendar_id": "your-email@example.com",
    "event_id": "รหัสกิจกรรมที่ต้องการลบ"
  }
  ```

## ⚠️ ข้อควรระวังด้านความปลอดภัย
* **ห้าม** นำไฟล์ `credentials.json` อัปโหลดขึ้น Public Repository (เช่น GitHub แบบสาธารณะ) เด็ดขาด
* ในการใช้งานบนเซิร์ฟเวอร์จริง (Production) ควรเพิ่มระบบ Authentication (เช่น API Key หรือ Bearer Token) ในไฟล์ PHP เพื่อป้องกันไม่ให้บุคคลภายนอกที่ไม่ได้รับอนุญาตยิง API เข้ามาแก้ไขปฏิทินของคุณ
