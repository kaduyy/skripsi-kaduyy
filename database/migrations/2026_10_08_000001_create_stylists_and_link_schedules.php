<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stylists', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->string('status', 20)->default('aktif');
            $table->timestamps();
        });

        Schema::table('slots', function (Blueprint $table) {
            $table->foreignId('stylist_id')->nullable()->after('kapster')->constrained('stylists')->nullOnDelete();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('stylist_id')->nullable()->after('user_id')->constrained('stylists')->nullOnDelete();
        });

        DB::table('slots')->select('kapster')->whereNotNull('kapster')->distinct()->orderBy('kapster')->get()
            ->each(function ($row) {
                $stylistId = DB::table('stylists')->where('nama', $row->kapster)->value('id');

                if (! $stylistId) {
                    $stylistId = DB::table('stylists')->insertGetId([
                        'nama' => $row->kapster,
                        'status' => 'aktif',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('slots')->where('kapster', $row->kapster)->update(['stylist_id' => $stylistId]);
            });

        DB::table('bookings')->orderBy('id')->get(['id', 'slot_id'])->each(function ($booking) {
            $stylistId = DB::table('slots')->where('id', $booking->slot_id)->value('stylist_id');

            if ($stylistId) {
                DB::table('bookings')->where('id', $booking->id)->update(['stylist_id' => $stylistId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('stylist_id');
        });

        Schema::table('slots', function (Blueprint $table) {
            $table->dropConstrainedForeignId('stylist_id');
        });

        Schema::dropIfExists('stylists');
    }
};