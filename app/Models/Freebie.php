<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freebie extends Model
{
    use HasFactory;

    // Define the table associated with the model (if it's different from the plural form of the model name)
    protected $table = 'freebies';

    // Define the primary key if it's not 'id' (in this case, it is 'id', so we don't need to set it explicitly)
    // protected $primaryKey = 'id';

    // Define which attributes are mass assignable (for security, only include fields you want to allow mass assignment)
    protected $fillable = [
        'freebie_code',
        'is_scanned',
        'freebie_qr_code_url',
        'HOTEL_ID',
        'USER_ID',
    ];

    // Define relationships (optional, depending on how you plan to use them)

    // One Freebie belongs to one Client (Hotel)
    public function client()
    {
        return $this->belongsTo(Client::class, 'HOTEL_ID', 'HOTEL_ID');
    }

    // One Freebie belongs to one User
    public function user()
    {
        return $this->belongsTo(User::class, 'USER_ID', 'USER_ID');
    }
}
