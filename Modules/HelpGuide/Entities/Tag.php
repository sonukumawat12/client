<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name','id'];
    
    public function articles()
    {
        return $this->belongsToMany('Modules\HelpGuide\Entities\Article');
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->name);
    }

    public function getUrlAttribute(): string
    {
        return action('\Modules\HelpGuide\Http\Controllers\Frontend\TagController@index', [$this->id, $this->slug]);
    }
}
