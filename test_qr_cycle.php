<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\User;

// 1. Create Admin, Owner, Customer
$admin = clone User::firstOrCreate(['phone' => 'admin_qr'], ['name' => 'Super Admin', 'password' => bcrypt('password'), 'role' => 'admin']);
$adminToken = $admin->createToken('test')->plainTextToken;

$owner = User::firstOrCreate(['phone' => 'owner_qr'], ['name' => 'Owner', 'password' => bcrypt('password'), 'role' => 'owner']);
$ownerToken = $owner->createToken('test')->plainTextToken;

$customer = User::firstOrCreate(['phone' => 'customer_qr'], ['name' => 'Customer', 'password' => bcrypt('password'), 'role' => 'customer']);
$customerToken = $customer->createToken('test')->plainTextToken;

App\Models\Customer::firstOrCreate(['user_id' => $customer->id]);

// Create Barbershop
$salon = App\Models\Barbershop::firstOrCreate(
    ['owner_id' => $owner->id],
    ['salon_name' => 'QR Salon', 'address' => 'QR City', 'status' => 'pending']
);

// Create Payment
$payment = App\Models\Payment::firstOrCreate(
    ['salon_id' => $salon->id],
    ['amount' => 100, 'proof_image' => 'dummy.png', 'payment_status' => 'pending']
);

// 1. Admin Activates Payment (Triggers QR Gen)
$res1 = Http::withToken($adminToken)->post("http://127.0.0.1:8000/api/admin/payments/activate/{$payment->id}")->json();
echo "1. Admin Activation: " . ($res1['salon_status'] ?? 'failed') . "\n";

// 2. Owner Gets QR
$res2 = Http::withToken($ownerToken)->get('http://127.0.0.1:8000/api/owner/qr')->json();
$qrToken = $res2['data']['qr_token'] ?? null;
echo "2. Generated QR Token: " . $qrToken . "\n";

// 3. Customer Links
if ($qrToken) {
    $res3 = Http::withToken($customerToken)->post('http://127.0.0.1:8000/api/customer/link-salon', ['qr_token' => $qrToken])->json();
    echo "3. Link Salon Result: " . ($res3['message'] ?? json_encode($res3)) . "\n";
} else {
    echo "3. Link Salon Result: Failed (No Token)\n";
}

// 4. Customer Gets Linked Salon
$res4 = Http::withToken($customerToken)->get('http://127.0.0.1:8000/api/customer/linked-salon')->json();
echo "4. Linked Salon Name: " . ($res4['data']['salon_name'] ?? 'None') . "\n";
