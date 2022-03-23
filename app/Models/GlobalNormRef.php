<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalNormRef extends Model
{
    protected $fillable = [
        'year', 'p1', 'p2', 'p3',
        'p4', 'p5', 'p6', 'freq',
        'tech_interact', 'peda_app', 'feedback_avg'
    ];
}
