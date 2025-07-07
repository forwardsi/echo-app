<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    use HasFactory;
    // Set the table associated with the model
    protected $table = 'users';

    // Set the primary key for the table
    protected $primaryKey = 'USER_ID';  // Primary key is USER_ID

    // Define the attributes that can be mass-assigned
    protected $fillable = [
        'FULL_NAME',               // Full name of the user
        'EMAIL',                   // User's email address
        'COUNTRY_OF_RESIDENCE',    // User's country of residence
        'LENGTH_OF_STAY',          // Length of stay at the hotel
        'PROMOTIONAL_MESSAGE',     // Whether the user has agreed to receive promotional messages
        'PROGRESS',                // Registration progress (could be 'registration', 'completed', etc.)
        'PHONE_NUMBER',            // User's phone number
    ];

    // Relationship example: Users have a relationship with a Client (HOTEL_ID)
    public function client()
    {
        return $this->belongsTo(Client::class, 'HOTEL_ID', 'HOTEL_ID');
    }
}
