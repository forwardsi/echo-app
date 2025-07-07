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
        Schema::create('freebies', function (Blueprint $table) {
            $table->increments('id');  // Auto-incrementing primary key for the freebies table
            $table->string('freebie_code')->unique();  // Unique code for the freebie
            $table->boolean('is_scanned')->default(false);  // Indicates whether the freebie has been scanned
            $table->string('freebie_qr_code_url');  // URL for the QR code
            $table->bigInteger('HOTEL_ID');  // Foreign key reference to clients table
            $table->bigInteger('USER_ID');  // Foreign key reference to users table
            $table->timestamps();  // Timestamps for created_at and updated_at
        
            // Foreign key constraints
            $table->foreign('HOTEL_ID')->references('HOTEL_ID')->on('clients')->onDelete('cascade');
            $table->foreign('USER_ID')->references('USER_ID')->on('users')->onDelete('cascade');  // Reference USER_ID from users table
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freebies');
    }
};
