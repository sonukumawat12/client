<?php

namespace Modules\HelpGuide\Http\Controllers\Frontend;

use Modules\HelpGuide\Entities\Tag;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Modules\HelpGuide\Http\Resources\ArticleResource;

class TagController extends Controller
{
    public function index($id)
    {
        // Load articles by given tag id
        $tag = Tag::select('id', 'name')->findOrFail($id);
        $tag->setRelation('articles', $tag->articles()->orderBy('id', 'desc')->paginate(30));
     
        return view('helpguide::frontend.tag', ['tag' => $tag->name, 'articles' => ArticleResource::collection($tag->articles)]);
    }
}