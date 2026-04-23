<?php
require __DIR__ . '/api_auth.php';
require __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// ต้องระบุ Event ID ที่ต้องการแก้มาด้วย
if (empty($data['event_id']) || empty($data['summary'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ต้องการ event_id และ summary"]);
    exit;
}

try {
    $client = new Client();
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->addScope(Calendar::CALENDAR);
    $service = new Calendar($client);

    $calendarId = !empty($data['calendar_id']) ? $data['calendar_id'] : 'primary';
    $eventId = $data['event_id']; // รับ Event ID ที่แอปส่งมาให้

    // 1. ดึง Event เดิมออกมาก่อน
    $event = $service->events->get($calendarId, $eventId);

    // 2. แก้ไขข้อมูลที่ต้องการ (ตัวอย่างนี้แก้แค่หัวข้อ)
    $event->setSummary($data['summary']);
    
    // ถ้ามีการส่งรายละเอียดใหม่มา ก็อัปเดตด้วย
    if (!empty($data['description'])) {
        $event->setDescription($data['description']);
    }

    // 3. บันทึกการอัปเดตกลับไปที่ Google Calendar
    $updatedEvent = $service->events->update($calendarId, $eventId, $event);

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "อัปเดตนัดหมายสำเร็จแล้ว",
        "updated_link" => $updatedEvent->htmlLink
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>