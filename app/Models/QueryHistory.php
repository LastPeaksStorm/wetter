<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryHistory extends Model
{
    protected $fillable = [
        'plz',
    ];

    use HasFactory;
}
