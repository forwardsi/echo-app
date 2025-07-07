<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfcScanHistory extends Model
{
    use HasFactory;

    // Define the table name (optional if Laravel can auto-detect)
    protected $table = 'nfc_scan_history'; // or 'nfc_scan_histories' if using Laravel naming

    // Enable mass assignment for these fields
    protected $fillable = [
        'hotel_id',
        'nfc_id',
        'nfc_name',
        'scanned_at',
    ];

    // Disable default timestamps if you don't have created_at and updated_at
    public $timestamps = false;
}