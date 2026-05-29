<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Customer;

$owner = User::where('role', 'owner')->first();
$ownerToken = $owner->createToken('test')->plainTextToken;

$customer = User::where('role', 'customer')->first();
$customerToken = $customer->createToken('test')->plainTextToken;
$c = Customer::where('user_id', $customer->id)->first();
$c->linked_salon_id = App\Models\Barbershop::first()->id;
$c->save();

// 1. Customer creates booking
$res1 = Http::withToken($customerToken)->post('http://127.0.0.1:8000/api/bookings/create', [
    'booking_date' => date('Y-m-d', strtotime('+1 day')),
    'booking_time' => '14:00',
    'service_type' => 'Haircut'
])->json();
$bookingId = $res1['data']['id'] ?? null;
echo "1. Create Booking: " . ($res1['message'] ?? json_encode($res1)) . " [Status: " . ($res1['data']['status'] ?? 'failed') . "]\n";

// 2. Owner confirms
if ($bookingId) {
    $res2 = Http::withToken($ownerToken)->post("http://127.0.0.1:8000/api/owner/bookings/confirm/{$bookingId}")->json();
    echo "2. Owner Confirm: " . ($res2['message'] ?? json_encode($res2)) . "\n";
}

// 3. Customer checks history
$res3 = Http::withToken($customerToken)->get('http://127.0.0.1:8000/api/bookings/history')->json();
echo "3. History latest status: " . ($res3['data'][0]['status'] ?? 'none') . "\n";

// 4. Check Notifications
$notifs = App\Models\Notification::where('user_id', $customer->id)->get();
echo "4. Notifications count: " . $notifs->count() . "\n";
if ($notifs->count() > 0) {
    echo "   Latest Notif: " . $notifs->last()->message . "\n";
}

// 5. Owner completes
if ($bookingId) {
    $res5 = Http::withToken($ownerToken)->post("http://127.0.0.1:8000/api/owner/bookings/complete/{$bookingId}")->json();
    echo "5. Owner Complete: " . ($res5['message'] ?? json_encode($res5)) . "\n";
}
