<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    protected $fillable = [
        'num1',
        'operator',
        'num2',
        'result',
    ];

    protected $casts = [
        'num1' => 'float',
        'num2' => 'float',
        'result' => 'float',
    ];
}
