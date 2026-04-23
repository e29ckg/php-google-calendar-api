<?php
// ตั้งค่า Header ให้เป็นรูปแบบ API (JSON)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;

// รับข้อมูล JSON ที่ส่งเข้ามา
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// ตรวจสอบว่าได้ส่ง Event ID มาหรือไม่
if (empty($data['event_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "status" => "error", 
        "message" => "กรุณาระบุ event_id ที่ต้องการลบ"
    ]);
    exit;
}

try {
    // เชื่อมต่อ Google API
    $client = new Client();
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->addScope(Calendar::CALENDAR);
    $service = new Calendar($client);
    
    $calendarId = !empty($data['calendar_id']) ? $data['calendar_id'] : 'primary';
    $eventId = $data['event_id']; // รับค่า Event ID

    // สั่งลบกิจกรรมจาก Google Calendar
    $service->events->delete($calendarId, $eventId);

    // ตอบกลับว่าลบสำเร็จ
    http_response_code(200); // OK
    echo json_encode([
        "status" => "success",
        "message" => "ลบนัดหมายสำเร็จแล้ว"
    ]);

} catch (Exception $e) {
    // กรณีที่ลบไม่สำเร็จ (เช่น ไม่มี Event ID นี้อยู่จริง หรือถูกลบไปแล้ว)
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "เกิดข้อผิดพลาดในการลบ: " . $e->getMessage()
    ]);
}
?>