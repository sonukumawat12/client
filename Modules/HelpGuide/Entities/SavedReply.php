<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SavedReply extends Model
{
    protected $table = "saved_replies";
    public function user()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\User');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('own_saved_reply', function($query) {
            $query->where('user_id',  Auth::id());
        });
    }
}
