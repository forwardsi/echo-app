<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    // Set the table associated with the model
    protected $table = 'clients';

    // Set the primary key for the table
    protected $primaryKey = 'HOTEL_ID';  // Set HOTEL_ID as the primary key

    // Indicate that the primary key is not auto-incrementing
    public $incrementing = false;

    // Define which attributes are mass assignable
    protected $fillable = [
        'HOTEL_ID',
        'HOTEL_NAME',
        'LOGO_IMG',
        'BANNER_IMG',
        'HEX_COLOR',
        'GMB_LINK',
        'RANKING_FEATURE',
        'NUMBER_OF_NFC_TAGS',
    ];
}
