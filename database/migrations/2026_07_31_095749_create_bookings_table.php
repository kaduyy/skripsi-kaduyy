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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('slot_id')->constrained('slots')->onDelete('cascade');
            $table->string('nama_pelanggan', 100);
            $table->string('no_whatsapp', 20);
            $table->date('tanggal_kunjungan');
            $table->time('jam_kunjungan');
            $table->decimal('total_harga', 12, 2);
            $table->decimal('dp_amount', 12, 2)->default(50000);
            $table->string('bukti_transfer', 255)->nullable();
            $table->string('status', 50)->default('menunggu_pembayaran');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
