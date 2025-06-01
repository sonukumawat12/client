<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denominations extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'denomination',
        'count',
        'total',
        'type',
        'denominations_belongs_id',
        'module'
    ];
}
