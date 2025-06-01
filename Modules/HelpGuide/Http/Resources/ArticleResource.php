<?php

namespace Modules\HelpGuide\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

use Modules\HelpGuide\Http\Resources\Category as CategoryResource;
use Modules\HelpGuide\Http\Resources\TagResource;

class ArticleResource extends JsonResource
{
    public $showContent = false;
 
    public function toArray($request)
    {
        $articleContent = $this->articleByLanguage();
        
        if( $articleContent ) $title = $articleContent->title;
        else if ( isset($this->noFallbackTitle) ) {
            $title = "";
        } else {
            $title = $this->title;
        }

        if( $articleContent ) $content = $articleContent->content;
        else if ( isset($this->noFallbackTitle) ) {
            $content = "";
        } else {
            $content = $this->content;
        }
       
        return [
            'id' => $this->id,
            'url' => $this->url,
            'published' => $this->published,
            'title' => $title,
            'tags' => tagResource::collection($this->tags),
            $this->mergeWhen($this->showContent, [
                'content' => $content,
            ]),
            'category' => new CategoryResource($this->category),
            'rate_helpful' => $this->rate_helpful,
            'rate_total' => $this->rate_total,
            'featured' => $this->featured,
            'language' => $this->currentLanguage,
            'created_at' => Carbon::parse($this->created_at)->format(setting('date_format')) . " - " . Carbon::parse($this->created_at)->diffForHumans(),
            'updated_at' => $this->updated_at
        ];
    }
}