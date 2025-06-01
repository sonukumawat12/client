<?php

namespace Modules\HelpGuide\Http\Controllers\Frontend;

use Modules\HelpGuide\Entities\Category;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Modules\HelpGuide\Http\Resources\ArticleResource;

class CategoryController extends Controller
{
    public function index($id)
    {
        $category = Category::select('id', 'name', 'parent_id')->findOrFail($id);
        return view('helpguide::frontend.category', ['category' => $category, 'articles' => $category->categoryArticles()]);
    }
}
