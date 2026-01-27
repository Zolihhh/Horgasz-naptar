<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatchLog extends Model
{
    /** @use HasFactory<\Database\Factories\CatchLogFactory> */
    use HasFactory;
     protected $fillable = [
         'locationid',
        'userid',
        'fishing_start',
        'fishing_end',
    ];

    // Reláció a halfogásokhoz
    public function fishCatches()
    {
        return $this->hasMany(FishCatch::class);
    }
}
