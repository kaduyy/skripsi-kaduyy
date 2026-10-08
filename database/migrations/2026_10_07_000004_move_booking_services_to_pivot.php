<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('bookings')->orderBy('id')->get([
            'id',
            'service_id',
            'slot_id',
            'durasi_total_menit',
            'created_at',
            'updated_at',
        ])->each(function ($booking) {
            $service = DB::table('services')->where('id', $booking->service_id)->first();
            $pivotExists = DB::table('booking_service')
                ->where('booking_id', $booking->id)
                ->where('service_id', $booking->service_id)
                ->exists();

            if ($service && ! $pivotExists) {
                DB::table('booking_service')->insert([
                    'booking_id' => $booking->id,
                    'service_id' => $service->id,
                    'harga' => $service->harga,
                    'durasi_menit' => $service->durasi_menit,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                ]);
            }

            $totalDuration = (int) DB::table('booking_service')
                ->where('booking_id', $booking->id)
                ->sum('durasi_menit');

            if ($totalDuration === 0 && $service) {
                $totalDuration = (int) $service->durasi_menit;
            }

            DB::table('bookings')->where('id', $booking->id)->update([
                'durasi_total_menit' => max($totalDuration, (int) $booking->durasi_total_menit),
            ]);

            if ($booking->slot_id && ! DB::table('booking_slot')
                ->where('booking_id', $booking->id)
                ->where('slot_id', $booking->slot_id)
                ->exists()) {
                DB::table('booking_slot')->insert([
                    'booking_id' => $booking->id,
                    'slot_id' => $booking->slot_id,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                ]);
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
        });

        DB::table('bookings')->orderBy('id')->get(['id'])->each(function ($booking) {
            $serviceId = DB::table('booking_service')
                ->where('booking_id', $booking->id)
                ->orderBy('id')
                ->value('service_id');

            if ($serviceId) {
                DB::table('bookings')->where('id', $booking->id)->update(['service_id' => $serviceId]);
            }
        });
    }
};