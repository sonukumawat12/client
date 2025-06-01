<?php

namespace Modules\HelpGuide\Http\Controllers\Frontend;

use Modules\HelpGuide\Entities\Article;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Modules\HelpGuide\Http\Resources\ArticleResource;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Articles search
        $q = $request->get('q');

        $articles = Article::select('id', 'title', 'content', 'created_at', 'rate_helpful', 'rate_total')
            ->where('id', (int)$q)
            ->orWhere('title','LIKE','%'.$q.'%')
            ->orWhere('content','LIKE','%'.$q.'%');


        $articles = ArticleResource::collection($articles->paginate(30));


        if (count ( $articles ) > 0)
            return view ( 'frontend.search' )->withArticles ( $articles )->withQuery ( $q );
 
        return view ( 'frontend.search' )->withQuery ( $q );
    }
}
