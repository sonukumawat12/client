<?php

namespace Modules\Leads\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class LeadsSettings extends Model
{
    use LogsActivity;

    protected $table = 'leads_settings';
    public $timestamps = false;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Leads';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}