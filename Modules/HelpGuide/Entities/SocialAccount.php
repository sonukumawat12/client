<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = ['access_token'];
    
    public function user()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\User');
    }
}
