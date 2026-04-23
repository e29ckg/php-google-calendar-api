<?php
// 1. เรียกใช้ Middleware ตรวจสอบความปลอดภัย
require __DIR__ . '/api_auth.php';
require __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;

// รับข้อมูล JSON 
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// 2. กำหนดปฏิทินและจำนวนข้อมูลที่ต้องการดึง (ค่าเริ่มต้นคือดึง 10 รายการ)
$calendarId = !empty($data['calendar_id']) ? $data['calendar_id'] : 'primary';
$maxResults = !empty($data['max_results']) ? (int)$data['max_results'] : 10;

try {
    // 3. เชื่อมต่อ Google API
    $client = new Client();
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->addScope(Calendar::CALENDAR_READONLY); // ใช้แค่สิทธิ์อ่านก็พอ
    $service = new Calendar($client);

    // 4. ตั้งค่าเงื่อนไขการค้นหา
    $optParams = [
        'maxResults' => $maxResults,
        'orderBy' => 'startTime', // เรียงตามเวลาเริ่ม
        'singleEvents' => true,   // แยกกิจกรรมที่เกิดซ้ำออกเป็นรายการเดี่ยวๆ
        'timeMin' => date('c'),   // ดึงเฉพาะกิจกรรมที่ยังไม่จบ (เริ่มนับจากเวลาปัจจุบัน)
    ];

    // 5. สั่งดึงข้อมูลจาก Calendar
    $results = $service->events->listEvents($calendarId, $optParams);
    $events = $results->getItems();

    // 6. จัดรูปแบบข้อมูลให้สวยงามก่อนส่งกลับไปให้แอป
    $eventList = [];
    if (!empty($events)) {
        foreach ($events as $event) {
            // เช็คว่าเป็นกิจกรรมแบบทั้งวัน (All-day) หรือแบบระบุเวลา
            $start = $event->start->dateTime;
            $end = $event->end->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
                $end = $event->end->date;
            }

            $eventList[] = [
                'event_id' => $event->getId(),
                'summary' => $event->getSummary(),
                'description' => $event->getDescription(),
                'location' => $event->getLocation(),
                'start' => $start,
                'end' => $end,
                'link' => $event->getHtmlLink()
            ];
        }
    }

    // 7. ตอบกลับเป็น JSON
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "ดึงข้อมูลสำเร็จ",
        "total_events" => count($eventList),
        "data" => $eventList // ส่ง Array ของเหตุการณ์ทั้งหมดกลับไป
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage()
    ]);
}
?>