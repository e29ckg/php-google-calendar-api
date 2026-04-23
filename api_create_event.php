<?php
// ตั้งค่า Header สำหรับให้แอปอื่นเรียกใช้
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// เรียกใช้ Library
require __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

// รับข้อมูลจากแอปที่ส่งมา
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// ตรวจสอบความครบถ้วนของข้อมูล
if (empty($data['summary']) || empty($data['start_time']) || empty($data['end_time'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error", 
        "message" => "ข้อมูลไม่ครบถ้วน (ต้องการ: summary, start_time, end_time)"
    ]);
    exit;
}

try {
    // เชื่อมต่อ Google API
    $client = new Client();
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->addScope(Calendar::CALENDAR);
    $service = new Calendar($client);

    // กำหนดข้อมูล Event
    $event = new Event([
        'summary' => $data['summary'],
        'description' => isset($data['description']) ? $data['description'] : '',
        'location' => isset($data['location']) ? $data['location'] : '',
        'start' => [
            'dateTime' => $data['start_time'], 
            'timeZone' => 'Asia/Bangkok',
        ],
        'end' => [
            'dateTime' => $data['end_time'],
            'timeZone' => 'Asia/Bangkok',
        ],
    ]);

    // นำข้อมูลลงปฏิทิน (ใช้ 'primary' หรืออีเมลของปฏิทินที่ต้องการ)
    $calendarId = !empty($data['calendar_id']) ? $data['calendar_id'] : 'primary';
    $createdEvent = $service->events->insert($calendarId, $event);

    // ส่งข้อความตอบกลับแอปว่าสำเร็จ
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "สร้างนัดหมายสำเร็จแล้ว",
        "event_id" => $createdEvent->getId(),
        "event_link" => $createdEvent->htmlLink
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ]);
}
?>