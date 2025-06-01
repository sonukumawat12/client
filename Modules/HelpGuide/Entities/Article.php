<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    public $currentLanguage;
    
    public function __construct()
    {
        $this->currentLanguage = app()->getLocale();
    }

    public function category()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\Category');
    }

    public function articleTranslations()
    {
        return $this->hasMany('Modules\HelpGuide\Entities\ArticleTranslation');
    }

    public function tags(Type $var = null)
    {
        return $this->belongsToMany('Modules\HelpGuide\Entities\Tag');
    }

    public function relatedPostsByTag()
    {
        return Article::whereHas('tags', function ($query) {
            $tagIds = $this->tags()->pluck('tags.id')->all();
            $query->whereIn('tags.id', $tagIds);
        })->where('id', '<>', $this->id)->get();
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title);
    }

    public function getUrlAttribute(): string
    {
        return action('\Modules\HelpGuide\Http\Controllers\Frontend\ArticlesController@index', [$this->id, $this->slug]);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('published', function($query) {
            $query->where('published', 1);
        });
    }

    public function articleByLanguage()
    {
        return ArticleTranslation::select('title','content')
            ->where('article_id', $this->id)
            ->where('language', $this->currentLanguage)
            ->first();
    }

    public function transTitle()
    {
        $articleContent = $this->articleByLanguage();
        
        if( $articleContent ){ 
            return $articleContent->title;
        }

        return $this->title;
    }

    public function transContent()
    {
        $articleContent = $this->articleByLanguage();
        
        if( $articleContent ){ 
            return $articleContent->content;
        }

        return $this->content;
    }

}
