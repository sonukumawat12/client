<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Database\Eloquent\Model;

class PushNotificationToken extends Model
{
    public function user()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\User');
    }
}
