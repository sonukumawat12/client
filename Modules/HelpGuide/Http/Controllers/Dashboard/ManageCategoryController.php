<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\Category;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Http\Resources\Category as CategoryResource;

class ManageCategoryController extends Controller
{
    public function index()
    {
        // $this->authorize('manage_categories');
        return view('helpguide::dashboard.manage_category.index');
    }

    public function show(Request $request)
    {
        $category = Category::findOrFail($request->input('id'));
        // $this->authorize('view_category', $category);
        return new CategoryResource($category);
    }
    
    public function fetch()
    {
        // $this->authorize('viewAny', Category::class);
        $category = Category::withCount(['tickets' => function($q){ 
            $q->withoutGlobalScope('own_ticket');
        }])->withCount(['articles' => function($q){ 
            $q->withoutGlobalScope('published');
        }])->orderBy('id', 'desc')->paginate(500);
        return CategoryResource::collection($category);
    }

    public function categoriesWithtickets()
    {
        // $this->authorize('viewAny', Category::class);
        $category = Category::orderBy('id', 'desc')->get();
        return CategoryResource::collection($category);
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Category::class);

        $validatedData = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:helpguide_categories,name',
            'parent_id' => 'sometimes|exists:helpguide_categories,id',
            'is_featured' => 'sometimes|numeric',
            'has_ticket' => 'sometimes|numeric',
            'active' => 'sometimes|numeric',
        ]);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "errors" => $validatedData->errors()];
        }

        $category = new Category;
        $category->name = $request->input('name');
        $category->active = (int)$request->input('active');
        $category->has_ticket = (int)$request->input('has_ticket');
        $category->parent_id = $request->input('parent_id');
        $category->is_featured = (int)$request->input('is_featured');
        $category->thumbnail = $request->input('thumbnail');
        $category->category_order = (int)$request->input('category_order');

        if ($category->save()) {return new CategoryResource($category);}
    }

    public function update(Request $request, Category $category)
    {

        // $this->authorize('update', $category);

        $validatedData = $request->validate([
            'name' => ['required',
                        'max:255',
                        Rule::unique('helpguide_categories', 'name')->ignore($category->id)
                ],
                'parent_id' => ['sometimes', 'exists:helpguide_categories,id'],
                'is_featured' => ['sometimes', 'numeric'],
                'has_ticket' => ['sometimes','numeric']
        ]);
        
        $category->name = $request->input('name');
        $category->is_featured = (int)$request->input('is_featured');
        $category->has_ticket = (int)$request->input('has_ticket');
        $category->thumbnail = $request->input('thumbnail');
        $category->category_order = (int)$request->input('category_order');

        // is the category a parent category
        if($request->input('parent_id') && $category->children->count() != 0){
            $category->removeChildren();
        }

        $category->parent_id = $request->input('parent_id');

        if ($category->save()) {
            return ['status' => 'ok', 'message' => __('Updated'), 'data' => new CategoryResource($category)];
        }

        return ['status' => 'fail', 'message' => __('Failed to save data, please try again')];
    }

    public function toggleActive(Request $request, Category $category)
    {

        // $this->authorize('update', $category);

        $category->active =  $category->active ? 0 : 1;

        if ($category->save()) {
            return ['status' => 'ok', 'message' => __('Records has been saved!'), 'data' => new CategoryResource($category)];
        }

        return ['status' => 'fail', 'message' => __('Failed to save data, please try again')];
    }

    public function destroy(Request $request, Category $category)
    {
        // $this->authorize('delete', $category);
        if ($category->delete()) {return new CategoryResource($category);}
    }
}
