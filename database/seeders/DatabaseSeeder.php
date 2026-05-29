<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Demo
        User::updateOrCreate(['phone' => 'admin'], [
            'name' => 'Admin Manager',
            'phone' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // 2. Owner Demo
        $owner = User::updateOrCreate(['phone' => '0511111111'], [
            'name' => 'Owner Demo',
            'phone' => '0511111111',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        $salon = \App\Models\Barbershop::updateOrCreate(['owner_id' => $owner->id], [
            'owner_id' => $owner->id,
            'salon_name' => 'BarberLink Elite Salon',
            'address' => 'Dubai Marina, UAE',
            'status' => 'active',
            'qr_token' => 'BL-DEMO-QR123',
            'qr_image_path' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=BL-DEMO-QR123'
        ]);

        // 3. Customer Demo
        $customerUser = User::updateOrCreate(['phone' => '0522222222'], [
            'name' => 'Customer Demo',
            'phone' => '0522222222',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $customer = \App\Models\Customer::updateOrCreate(['user_id' => $customerUser->id], [
            'user_id' => $customerUser->id,
            'linked_salon_id' => $salon->id,
        ]);

        // 4. Booking Demo
        \App\Models\Booking::updateOrCreate(['customer_id' => $customer->id, 'salon_id' => $salon->id], [
            'customer_id' => $customer->id,
            'salon_id' => $salon->id,
            'service_type' => 'حلاقة VIP',
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '10:00:00',
            'status' => 'pending',
        ]);
    }
}
