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
        Schema::create('reviews', function (Blueprint $table) {
            $table->string('HASHTAG')->unique(); // Unique identifier (UUID/Hashtag)
            $table->bigInteger('HOTEL_ID'); // Foreign key linking to hotels
            $table->bigInteger('NFC_ID'); // Foreign key linking to nfcs
            $table->bigInteger('USER_ID'); // Foreign key linking to users
            $table->string('USER_EMAIL'); // User's email
            $table->text('REVIEW'); // The review text
            $table->boolean('IS_POSTED')->default(false); // Whether the review is confirmed
            $table->timestamp('confirmed_at')->nullable(); // Timestamp when bartender confirms review
            $table->timestamps(); // created_at and updated_at timestamps
        
            // Foreign key constraint linking HOTEL_ID to clients table
            $table->foreign('HOTEL_ID')->references('HOTEL_ID')->on('clients')->onDelete('cascade');
        
            // Foreign key constraint linking USER_ID to users table
            $table->foreign('USER_ID')->references('USER_ID')->on('users')->onDelete('cascade');

            // Foreign key constraint linking USER_ID to users table
            $table->foreign('NFC_ID')->references('NFC_ID')->on('nfc_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
