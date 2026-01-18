<?php

use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Carbon\Carbon;

$controller = new BookingController();
$request = new Request([
    'check_in_date' => date('Y-m-d'),
    'check_out_date' => date('Y-m-d', strtotime('+1 day')),
    'room_type_id' => 1 // Assuming ID 1 exists
]);

try {
    $response = $controller->getAvailableRooms($request);
    echo "Status: " . $response->status() . "\n";
    echo "Content: " . $response->content() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
