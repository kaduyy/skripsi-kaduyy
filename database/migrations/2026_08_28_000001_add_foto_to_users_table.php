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
        if (!Schema::hasColumn('users', 'foto')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('foto')->nullable()->after('no_whatsapp');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'foto')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('foto');
            });
        }
    }
};
