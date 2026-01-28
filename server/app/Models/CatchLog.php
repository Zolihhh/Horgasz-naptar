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
        'comment',  
        'fishing_start',
        'fishing_end',
    ];

  public function catchLog()
    {
        return $this->belongsTo(CatchLog::class, 'catchLogId');
    }
}
