<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->integer('durasi_menit')->default(0);
            $table->decimal('harga', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['booking_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_service');
    }
};
