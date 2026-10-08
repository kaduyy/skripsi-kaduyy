<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('durasi_total_menit')->default(0);
        });

        Schema::create('booking_slot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('slot_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['booking_id', 'slot_id']);
            $table->index('slot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slot');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('durasi_total_menit');
        });
    }
};