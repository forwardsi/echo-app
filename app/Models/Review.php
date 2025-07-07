<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Specify the primary key as 'HASHTAG'
    protected $primaryKey = 'HASHTAG';

    // Indicate that the primary key is not auto-incrementing
    public $incrementing = false;

    // Specify the data type of the primary key (string for hashtags)
    protected $keyType = 'string';
}
