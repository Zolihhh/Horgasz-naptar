<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishCatch extends Model
{
    /** @use HasFactory<\Database\Factories\FishCatchFactory> */
    use HasFactory;
    protected $fillable = [
        'catch_log_id',
        'species',
        'weight',
        'length',
        // egyéb mezők
    ];

    // Visszafelé kapcsolat a naplóhoz
    public function catchLog()
    {
        return $this->belongsTo(CatchLog::class);
    }
}
