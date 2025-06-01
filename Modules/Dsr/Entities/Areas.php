<?php

namespace Modules\Dsr\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Areas extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'district_id'];
}
