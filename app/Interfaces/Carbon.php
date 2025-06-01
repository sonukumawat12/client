<?php

namespace App\Interfaces;

use Carbon\Carbon as BaseCarbon;
use App\Utils\Util;

class Carbon extends BaseCarbon
{
    public static function parse($time = null, $tz = null)
    {
        if ($time instanceof \Carbon\Carbon || $time instanceof \DateTime) {
            return $time;
        }
        
        if (strpos($time, ':') === false) {
            $hasTime = false;
        } else {
            $hasTime = true;
        }
    
        $util = new Util();
        $new_time = $util->uf_date($time, $hasTime);

        return parent::parse($new_time, $tz);
    }
}
