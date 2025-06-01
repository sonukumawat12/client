<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class ArticleTranslation extends Model
{
    protected $fillable = ['article_id','language'];

    public function article()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\Article');
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title);
    }

    public function getUrlAttribute(): string
    {
        return action('\Modules\HelpGuide\Http\Controllers\Frontend\ArticlesController@index', [$this->article_id, $this->slug]);
    }

}
