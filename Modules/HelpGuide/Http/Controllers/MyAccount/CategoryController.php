<?php

namespace Modules\HelpGuide\Http\Controllers\MyAccount;

use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;

use Modules\HelpGuide\Category;
use Modules\HelpGuide\Http\Resources\Category as CategoryResource;

class CategoryController extends Controller
{
    public function fetch()
    {
        $category = Category::select('id','name','thumbnail')->where('active',1)->orderBy('id', 'desc')->paginate(500);
        return collect($category);
    }
}
