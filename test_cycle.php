<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\User;

// Create Admin
$admin = clone User::firstOrCreate(
    ['phone' => 'admin_123'],
    ['name' => 'Super Admin', 'password' => bcrypt('password'), 'role' => 'admin']
);
$adminToken = $admin->createToken('test')->plainTextToken;

// 1. Register Owner
$res1 = Http::post('http://127.0.0.1:8000/api/register/owner', [
    'owner_name' => 'Cycle Owner',
    'salon_name' => 'Cycle Salon',
    'phone' => '05' . rand(1000, 9999),
    'address' => 'Test',
    'password' => 'password'
])->json();
$ownerToken = $res1['token'];
echo "1. Register Owner Status: " . $res1['status'] . "\n";

// 2. Upload Proof (Simulate file upload)
$data = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
file_put_contents('dummy.png', base64_decode($data));
$res2 = Http::withToken($ownerToken)->attach('proof_image', file_get_contents('dummy.png'), 'dummy.png')
    ->post('http://127.0.0.1:8000/api/payments/upload-proof')->json();
echo "2. Upload Proof Status: " . ($res2['status'] ?? json_encode($res2)) . "\n";

// 3. Admin Gets Pending
$res3 = Http::withToken($adminToken)->get('http://127.0.0.1:8000/api/admin/payments/pending')->json();
$paymentId = $res3['data'][0]['payment_id'] ?? null;
echo "3. Pending Payments found: " . count($res3['data'] ?? []) . "\n";

// 4. Admin Approves
if ($paymentId) {
    $res4 = Http::withToken($adminToken)->post("http://127.0.0.1:8000/api/admin/payments/activate/{$paymentId}")->json();
    echo "4. Admin Activation: " . $res4['salon_status'] . "\n";
}

// 5. Owner Checks Status
$res5 = Http::withToken($ownerToken)->get('http://127.0.0.1:8000/api/check-status')->json();
echo "5. Final Owner Status: " . $res5['status'] . "\n";
