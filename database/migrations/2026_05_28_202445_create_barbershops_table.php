<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barbershops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('salon_name');
            $table->text('address')->nullable();
            $table->string('qr_token')->unique()->nullable();
            $table->string('qr_image_path')->nullable();
            $table->enum('status', ['pending', 'pending_review', 'active', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barbershops');
    }
};
