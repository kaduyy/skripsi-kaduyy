<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable()->change();
            $table->foreign('booking_id')->references('id')->on('bookings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->table('reviews')->whereNull('booking_id')->exists()) {
            throw new RuntimeException('Cannot restore non-nullable review booking references while orphaned reviews exist.');
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable(false)->change();
            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
        });
    }
};
