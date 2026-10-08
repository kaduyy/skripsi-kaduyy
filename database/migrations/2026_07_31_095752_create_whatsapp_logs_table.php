<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('phone', 20);
            $table->text('message');
            $table->string('type', 50)->default('konfirmasi');
            $table->enum('status', ['pending', 'terkirim', 'gagal'])->default('pending');
            $table->text('response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->tinyInteger('retry_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
