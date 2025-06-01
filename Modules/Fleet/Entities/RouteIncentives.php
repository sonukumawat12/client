<?php

namespace Modules\Fleet\Entities;

use Illuminate\Database\Eloquent\Model;

class RouteIncentives extends Model
{
    protected $fillable = [];

    protected $table = 'route_incentives';
    protected $guarded = ['id'];
}
