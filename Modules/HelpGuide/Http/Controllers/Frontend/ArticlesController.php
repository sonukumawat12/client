<?php

namespace Modules\HelpGuide\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;

use Modules\HelpGuide\Entities\Article;
use Modules\HelpGuide\Entities\ArticleRate;

use Illuminate\Support\Facades\Auth;

use Modules\HelpGuide\Http\Resources\ArticleResource;

class ArticlesController extends Controller
{
    public function index($id)
    {
        $article = Article::select('id', 'category_id', 'title', 'content', 'updated_at', 'created_at', 'rate_total', 'rate_helpful')
            ->with(['category' => function($q){
                $q->select('id','name')->first();
            }]);

        if(!Auth::guest() &&Auth::User()->can('manage_articles')){
            $article->withoutGlobalScope('published');
        }
            
        $article = $article->findOrFail($id);
        
        $relatedArticles = $article->relatedPostsByTag();
        return view("helpguide::frontend.article", ['article' => $article, 'related_articles' => $relatedArticles])->withCanonical($article->url);;
    }
    
    public function updateRate(Request $request)
    {
        $article = Article::select('id','rate_helpful','rate_total')->where("published","=",1)->findOrFail($request->article_id);

        $newRate = false;
        $helpful = false;

        $articleParams = array();
        
        $articleParams['article_id'] = $request->article_id;

        if (Auth::check()) {
            $articleParams['user_id'] = Auth::id();
        }else{
            $articleParams['user_ip'] = $request->ip();
        }

        $articleRate = ArticleRate::firstOrNew($articleParams);

        if(!$articleRate->exists){
            $article->rate_total = $article->rate_total+1;
            if($request->input('rate') == "yes"){
                $article->rate_helpful = $article->rate_helpful+1;
                $articleRate->last_rate = "yes";
            }else if($request->input('rate') == "no"){
                $article->rate_helpful = abs($article->rate_helpful-1);
                $articleRate->last_rate = "no";
            }
        }else{
            if($request->input('rate') == "yes" && $articleRate->last_rate != "yes"){
                $article->rate_helpful = $article->rate_helpful+1;
                $articleRate->last_rate = "yes";
            }else if($request->input('rate') == "no" && $articleRate->last_rate != "no"){
                $article->rate_helpful = $article->rate_helpful-1;
                $articleRate->last_rate = "no";
            }
        }


        $articleRate->save();
        $article->save();
            
        return new ArticleResource($article);

    }

}
