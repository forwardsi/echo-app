<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfcScan extends Model
{
    use HasFactory;

    protected $table = 'nfc_scans'; // Define the table name

    protected $fillable = [
        'nfc_id',
        'scan_count',
    ];

    public $timestamps = true; // Enable created_at and updated_at timestamps

    // Define relationship (assuming NFC_ID is primary in nfc_tags)
    public function nfcTag()
    {
        return $this->belongsTo(NfcTag::class, 'nfc_id', 'NFC_ID');
    }
}
