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
    'specieId',
    'weight',
    'length',
    'lureId',
    'catchTime',
];

    public function fishCatches()
    {
        return $this->hasMany(FishCatch::class, 'catchLogId'); 
        // 'catchLogId' → a fish_catches tábla idegen kulcsa
    }
}
