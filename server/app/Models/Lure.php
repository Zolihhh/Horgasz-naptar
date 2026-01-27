<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lure extends Model
{
    /** @use HasFactory<\Database\Factories\LureFactory> */
    use HasFactory;
    protected $fillable = [
        'lure',
    ];
}
