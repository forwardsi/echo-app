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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigInteger('HOTEL_ID')->primary();  // HOTEL_ID is the primary key
            $table->string('HOTEL_NAME');
            $table->string('LOGO_IMG');
            $table->string('BANNER_IMG');
            $table->string('HEX_COLOR');
            $table->text('GMB_LINK');
            $table->string('RANKING_FEATURE');
            $table->integer('NUMBER_OF_NFC_TAGS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
