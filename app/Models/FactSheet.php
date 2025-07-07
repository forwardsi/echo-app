<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactSheet extends Model
{
    protected $table = 'fact_sheet';

    protected $fillable = [
        'hotel_id',
        'fact_sheet_json',
    ];

    protected $casts = [
        'fact_sheet_json' => 'array',
    ];

    public function hotel()
    {
        return $this->belongsTo(Client::class, 'hotel_id', 'HOTEL_ID');
    }
}