<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatchLog extends Model
{
    /** @use HasFactory<\Database\Factories\CatchLogFactory> */
    use HasFactory;
     protected $fillable = [
        'user_id',
        'location',
        'date',
        // egyéb mezők
    ];

    // Reláció a halfogásokhoz
    public function fishCatches()
    {
        return $this->hasMany(FishCatch::class);
    }
}
