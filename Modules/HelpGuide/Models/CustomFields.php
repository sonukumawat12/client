<?php

namespace Modules\HelpGuide\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFields extends Model
{
    public function getRulesAttribute(  $value )
    {
        return isset($value) ? unserialize($value) : $value;
    }

    public function getRulesMessagesAttribute(  $value )
    {
        return isset($value) ? unserialize($value) : $value;
    }

    public function getValueAttribute(  $value )
    {
        return isset($value) ? unserialize($value) : $value;
    }
}
