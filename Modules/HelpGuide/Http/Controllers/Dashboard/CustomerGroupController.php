<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\CustomerGroup;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Http\Resources\CustomerGroupResource as CustomerGroupResource;

class CustomerGroupController extends Controller
{
    public function index()
    {
        return view('helpguide::dashboard.customer_groups.index');
    }

    public function show(Request $request)
    {
        $category = CustomerGroup::findOrFail($request->input('id'));
        return new CustomerGroupResource($category);
    }
    
    public function fetch()
    {
        $category = CustomerGroup::paginate(500);
        return CustomerGroupResource::collection($category);
    }

    

    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:customer_groups,name',
        ]);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "errors" => $validatedData->errors()];
        }

        $category = new CustomerGroup();
        $category->name = $request->input('name');
        if ($category->save()) {return new CustomerGroupResource($category);}
    }

    public function update(Request $request, CustomerGroup $category)
    {

        $validatedData = $request->validate([
            'name' => ['required',
                        'max:255',
                        Rule::unique('customer_groups', 'name')->ignore($category->id)
                ]
        ]);
        
        $category->name = $request->input('name');

        
        if ($category->save()) {
            return ['status' => 'ok', 'message' => __('Updated'), 'data' => new CustomerGroupResource($category)];
        }

        return ['status' => 'fail', 'message' => __('Failed to save data, please try again')];
    }

   
    public function destroy(Request $request, CustomerGroup $category)
    {
        if ($category->delete()) {return new CustomerGroupResource($category);}
    }
}
