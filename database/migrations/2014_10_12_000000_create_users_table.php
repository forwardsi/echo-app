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
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('USER_ID')->primary(); // Primary key, USER_ID
            $table->string('FULL_NAME');             // User's full name
            $table->string('EMAIL');                 // User's email
            $table->string('PHONE_NUMBER')->nullable();
            $table->string('COUNTRY_OF_RESIDENCE');  // User's country of residence
            $table->integer('LENGTH_OF_STAY');       // User's length of stay
            $table->boolean('PROMOTIONAL_MESSAGE')->default(false);
            $table->string('PROGRESS');
            $table->bigInteger('HOTEL_ID');          // Must match the signed BIGINT in clients
            $table->timestamps();
        
            // Foreign key constraint linking HOTEL_ID to clients.HOTEL_ID
            $table->foreign('HOTEL_ID')
                  ->references('HOTEL_ID')
                  ->on('clients')
                  ->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
