<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specie extends Model
{
    /** @use HasFactory<\Database\Factories\SpecieFactory> */
    use HasFactory;

    protected $fillable = [
        'fish_name',
        'photo'
    ];
    
}

