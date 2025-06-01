<?php

namespace Modules\HelpGuide\Http\Controllers\Frontend;

use Modules\HelpGuide\Entities\User;
use Modules\HelpGuide\Entities\Article;

use Modules\HelpGuide\Entities\Category;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Modules\HelpGuide\Http\Resources\ArticleResource;

class IndexController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return view('helpguide::frontend.error');
        }
        $articlesByCategory = Category::select('id','name')
            ->where('is_featured', 1)
            ->get()->map(function( $articles ){
                $articles['articles_count'] = $articles->categoryArticleCount();
                $articles['articles'] = $articles->recentArticles();
                return $articles;
            });
        
        // Load Featured articles
        $featuredArticles = Article::where('featured','=',1)->where('published','=','1')->select(['id','title']);
        $featuredArticles = $featuredArticles->take(9)->get();

        $tplData = [
            'articles_by_category' => $articlesByCategory,
            'featured_articles' => $featuredArticles
        ];
        return view('helpguide::frontend.index', $tplData);
    }
}
