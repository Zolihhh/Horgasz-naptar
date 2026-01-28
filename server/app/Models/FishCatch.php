<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishCatch extends Model
{
    /** @use HasFactory<\Database\Factories\FishCatchFactory> */
    use HasFactory;
 protected $fillable = [
        'catchLogId',
        'species_id',
        'weight',
        'length',
        'lure_id',
        'catch_time',
    ];

    public function fishCatches()
    {
        return $this->hasMany(FishCatch::class, 'catchLogId'); 
        // 'catchLogId' → a fish_catches tábla idegen kulcsa
    }
}
