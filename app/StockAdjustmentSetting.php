<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentSetting extends Model
{
    //
    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Account';

    protected $guarded = ['id'];
    
}
