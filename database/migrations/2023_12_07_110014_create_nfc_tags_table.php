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
        Schema::create('nfc_tags', function (Blueprint $table) {
            $table->string('NFC_ID')->primary();  // NFC_ID as the primary key
            $table->bigInteger('HOTEL_ID');  // Hotel ID (links to clients table)
            $table->string('ACTION');  // Action related to the NFC tag
            $table->string('REWARD');  // Reward associated with the NFC tag
            $table->timestamps();  // Timestamps for created_at and updated_at
        
            // Foreign key constraint for HOTEL_ID linking to the clients table
            $table->foreign('HOTEL_ID')->references('HOTEL_ID')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfc_tags');
    }
};
