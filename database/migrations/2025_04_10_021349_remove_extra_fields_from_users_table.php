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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'PHONE_NUMBER',
                'COUNTRY_OF_RESIDENCE',
                'LENGTH_OF_STAY',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('PHONE_NUMBER')->nullable();
            $table->string('COUNTRY_OF_RESIDENCE');
            $table->integer('LENGTH_OF_STAY');
        });
    }
};
