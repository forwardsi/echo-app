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
        Schema::create('nfc_scans', function (Blueprint $table) {
            $table->id();
            $table->string('nfc_id'); // Foreign key to NFC Tags
            $table->integer('scan_count')->default(0); // How many times scanned
            $table->timestamps();
    
            // Define foreign key constraint
            $table->foreign('nfc_id')->references('NFC_ID')->on('nfc_tags')->onDelete('cascade');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfc_scans');
    }
};
