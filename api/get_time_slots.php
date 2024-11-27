<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$date = $_GET['date'] ?? date('Y-m-d');

// Jam operasional (2 jam per slot)
$timeSlots = [
    '08:00-10:00',
    '10:00-12:00',
    '13:00-15:00',
    '15:00-17:00',
    '20:00-22:00'
];

try {
    // Ambil booking yang sudah ada
    $stmt = $db->prepare("
        SELECT time_slot 
        FROM bookings 
        WHERE booking_date = ? 
        AND status = 'confirmed'
    ");
    $stmt->execute([$date]);
    $bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $slots = [];
    foreach ($timeSlots as $time) {
        $slots[] = [
            'time' => $time,
            'status' => in_array($time, $bookedSlots) ? 'booked' : 'available'
        ];
    }
    
    echo json_encode($slots);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 