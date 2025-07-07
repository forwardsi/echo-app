<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfcTag extends Model
{
    use HasFactory;

    // Set the table associated with the model
    protected $table = 'nfc_tags';

    // Set the primary key for the table
    protected $primaryKey = 'NFC_ID';  // NFC_ID is the primary key

    // Indicate that the primary key is not auto-incrementing
    public $incrementing = false; // NFC_ID is not auto-incrementing

    // Define the type of primary key (string in this case)
    protected $keyType = 'string';  // Primary key is a string (e.g., UUID)

    // Define the attributes that can be mass-assigned
    protected $fillable = [
        'HOTEL_ID',     // Hotel ID (reference to the hotel)
        'NFC_ID',       // Unique NFC tag ID
        'ACTION',       // Action related to the NFC tag
        'REWARD',       // Reward associated with the NFC tag
        'created_at',   // Created at timestamp (automatically handled by Laravel)
        'updated_at',   // Updated at timestamp (automatically handled by Laravel)
    ];

    // Optional: If you don't want to use timestamps in this model, you can disable it
    // public $timestamps = false; // Disable automatic handling of timestamps if not needed

    // Relationships: You can define relationships (if any) to other models.
    // For example, if you want to get the associated hotel for this NFC tag:
    public function client()
    {
        return $this->belongsTo(Client::class, 'HOTEL_ID', 'HOTEL_ID');
    }
}
