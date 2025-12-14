<?php
require_once '../config.php';
require_once '../includes/db_helper.php';

header('Content-Type: application/json');

try {
    $events = getAllEvents();
    
    // Filter for active events only
    $activeEvents = array_filter($events, function($e) {
        return ($e['status'] ?? '') === 'active';
    });

    // Sort by date (nearest upcoming first)
    usort($activeEvents, function($a, $b) {
        $tA = strtotime($a['date'] . ' ' . $a['time']);
        $tB = strtotime($b['date'] . ' ' . $b['time']);
        return $tA - $tB;
    });

    // Return only necessary fields
    $output = array_map(function($e) {
        return [
            'title' => $e['title'],
            'date' => $e['date'],
            'time' => $e['time'],
            'location' => $e['location'],
            'description' => $e['description']
        ];
    }, array_values($activeEvents));

    echo json_encode(['success' => true, 'data' => $output]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to load events']);
}