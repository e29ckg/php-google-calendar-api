<?php
// ==========================================
// 1. ตั้งค่า API Key ลับ
// ==========================================
$SECRET_API_KEY = "my_super_secret_key_2026_xoxo";

// ==========================================
// 2. ฟังก์ชันสำหรับบันทึกประวัติ (Log)
// ==========================================
function writeApiLog($status, $message) {
    $log_file = __DIR__ . '/api_access.log'; // ไฟล์ Log จะถูกสร้างอัตโนมัติในโฟลเดอร์นี้
    $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown IP';
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'Unknown URI';
    
    // กำหนดเวลาปัจจุบัน (ปี-เดือน-วัน ชั่วโมง:นาที:วินาที)
    date_default_timezone_set('Asia/Bangkok');
    $date_time = date('Y-m-d H:i:s');
    
    // รูปแบบข้อความที่จะบันทึก
    $log_entry = "[$date_time] IP: $ip_address | API: $request_uri | Status: $status | Message: $message" . PHP_EOL;
    
    // บันทึกลงไฟล์ (FILE_APPEND คือให้เขียนต่อท้ายไฟล์เดิม)
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// ==========================================
// 3. ตั้งค่า Header พื้นฐานสำหรับทุก API
// ==========================================
header("Access-Control-Allow-Origin: *"); // เปลี่ยน * เป็นโดเมนเว็บของคุณเพื่อความปลอดภัยสูงสุด
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-API-KEY");

// จัดการ Preflight Request ของเบราว์เซอร์
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ==========================================
// 4. ระบบตรวจสอบ API Key
// ==========================================
// รองรับการอ่าน Header ทั้งบนเซิร์ฟเวอร์ Apache และ Nginx
$provided_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '';

if (empty($provided_key)) {
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $provided_key = isset($headers['X-API-KEY']) ? $headers['X-API-KEY'] : '';
    }
}

// เช็คว่า Key ถูกต้องหรือไม่
if ($provided_key !== $SECRET_API_KEY) {
    // กรณีรหัสผิด: บันทึก Log และเตะออก
    writeApiLog("FAILED", "Invalid or Missing API Key");
    http_response_code(401);
    echo json_encode([
        "status" => "error", 
        "message" => "ไม่อนุญาตให้เข้าถึง API (Invalid API Key)"
    ]);
    exit; // สำคัญมาก! หยุดการทำงานตรงนี้ทันที โค้ด API หลักจะไม่ได้รัน
}

// กรณีรหัสถูกต้อง: บันทึก Log เล็กน้อยว่าเข้าใช้งานได้ (คุณสามารถปิดบรรทัดนี้ได้ถ้าไม่อยากให้ Log เยอะเกินไป)
writeApiLog("SUCCESS", "Authorized Access");
?>