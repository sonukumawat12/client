<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\Tag;
use Modules\HelpGuide\Entities\Article;

use Modules\HelpGuide\Entities\Category;
use Modules\HelpGuide\Entities\ArticleTranslation;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\HelpGuide\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ManageArticlesController extends Controller
{

    public function togglePublished(Request $request)
    {
        $article = Article::withoutGlobalScope('published')->findOrFail($request->article_id);
        // $this->authorize('update', $article);
        if(!$article){
            return response()->json([
            'status' => 'fail',
            'message' => 'Article not found'
            ]);
        }
        
        $article->published = $article->published == 1 ? 0 : 1;
        
        if ($article->save()) {return new ArticleResource($article);}
    }

    public function save(Request $request)
    {
        // $this->authorize('create', Article::class);
        $article = new Article;

        $validatedData = Validator::make($request->all(), [
            'title' => ['required'],
            'content' => ['required'],
            'language' => ['required', Rule::in(array_keys(availableLanguages()))],
            'category' => ['required', 'integer', 'exists:helpguide_categories,id'],
        ]);

        if ($validatedData->fails()) {
            return Response::json(['messages' => $validatedData->errors()], 422);
        }
        
        $tags = (array)$request->input('tags');
        $tagsId = array();

        // Store tags 
        foreach($tags as $tg){  
            if(isset($tg['id'])){
                $tagsId[] = (int)$tg['id']; continue;
            }
            if(isset($tg['label'])){
                $tag = Tag::firstOrCreate(['name' => $tg['label']]);
            } else if(is_string($tg)){
                $tag = Tag::firstOrCreate(['name' => $tg]);
            } else {
                continue;
            }
            
            $tagsId[] = $tag->id;
        }

        $article->user_id = Auth::id();
        $article->category_id = $request->input('category');
        $article->published = (int)$request->input('published');
        $article->featured = (int)$request->input('featured');
        $article->updated_by = Auth::id();

        if($article->save()) {

            $article->tags()->sync($tagsId);

            $content = str_replace("helpguide/public/uploads/articles/images", "public/uploads/articles/images", $request->input('content'));
            $content = str_replace("helpguide/dashboard/public/uploads/articles/images", "public/uploads/articles/images", $content);
            $translation = new ArticleTranslation();
            $translation->title = $request->input('title');
            $translation->content = $content;
            $translation->language = $request->input('language');
            $article->articleTranslations()->save($translation);

            return new ArticleResource($article);
        }
        
        return Response::json([
            'message' => __('Failed to save data')
        ], 422);
    }

    public function update(Request $request)
    {
        $article = Article::withoutGlobalScope('published')->findOrFail($request->article_id);
        
        // $this->authorize('update', $article);

        $validatedData = Validator::make($request->all(), [
            'title' => ['required'],
            'content' => ['required'],
            'language' => ['required', Rule::in(array_keys(availableLanguages()))],
            'category' => ['required', 'integer', 'exists:helpguide_categories,id'],
        ]);

        if ($validatedData->fails()) {
            return Response::json(['messages' => $validatedData->errors()], 422);
        }
        
        $tags = (array)$request->input('tags');
        $tagsId = array();

        // Store tags 
        foreach($tags as $tg){  
            if(isset($tg['id'])){
                $tagsId[] = (int)$tg['id']; continue;
            }
            if(isset($tg['label'])){
                $tag = Tag::firstOrCreate(['name' => $tg['label']]);
            } else if(is_string($tg)){
                $tag = Tag::firstOrCreate(['name' => $tg]);
            } else {
                continue;
            }
            
            $tagsId[] = $tag->id;
        }

        $article->user_id = Auth::id();
        $article->title = $request->input('title');
        $article->published = (int)$request->input('published');
        $article->featured = (int)$request->input('featured');
        $article->updated_by = Auth::id();
        $article->category_id = $request->input('category');

        if($article->save()) {

            $article->tags()->sync($tagsId);

            $content = str_replace("helpguide/public/uploads/articles/images", "public/uploads/articles/images", $request->input('content'));
            $content = str_replace("helpguide/dashboard/public/uploads/articles/images", "public/uploads/articles/images", $content);
            $translation = ArticleTranslation::firstOrCreate(['article_id' => $article->id, 'language' => $request->input('language')]);
            $translation->title = $request->input('title');
            $translation->content = $content;
            $translation->language = $request->input('language');
            $article->articleTranslations()->save($translation);

            return new ArticleResource($article);
        }
        
        return Response::json([
            'message' => __('Failed to save data')
        ], 422);
    }

    public function fetch(Request $request)
    {
        // $this->authorize('manage', Article::class);
        $articles = Article::whereNull('deleted_at')
        ->orderBy('id', 'desc')
        ->withoutGlobalScope('published');

        if ( $request->input('category') ){
            $articles->whereHas('category', function($query) use ($request) {
                $query->select('id')->where('category_id', $request->input('category'));
            });
        }
        
        $articles = $articles->paginate(30);

        return ArticleResource::collection($articles);
    }

    public function show(Request $request, $id)
    {
        $article = Article::withoutGlobalScope('published')->findOrFail($id);
        // $this->authorize('update', $article);
        $ArticleResource = new ArticleResource($article);

        if( $request->input('language') ){
            $article->currentLanguage = $request->input('language');
            $ArticleResource->noFallbackTitle = true;
            $ArticleResource->noFallbackِContent = true;
        }
        
        $ArticleResource->showContent = true;

        return $ArticleResource;
    }

    public function destroy(Request $request, $id)
    {
        $article = Article::withoutGlobalScope('published')->findOrFail($id);
        // $this->authorize('delete', $article);
        $article->delete();
        return response()->json([
            'status' => 'ok',
            'message' => __('Record has been deleted')
        ]);
    }
}
