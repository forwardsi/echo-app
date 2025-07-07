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
        Schema::table('nfc_tags', function (Blueprint $table) {
            $table->string('LOCATION')->after('REWARD');  // Adding the location column after the REWARD column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nfc_tags', function (Blueprint $table) {
            $table->dropColumn('LOCATION');  // Rollback location column if necessary
        });
    }
};
