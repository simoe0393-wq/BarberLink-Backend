<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('salon_id')->constrained('barbershops')->onDelete('cascade');
            $table->date('booking_date');
            $table->time('booking_time');
            $table->string('service_type');
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'completed', 'cancelled', 'rescheduled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
