<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\EmployeeGroup;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Http\Resources\EmployeeGroupResource as EmployeeGroupResource;

class EmployeeGroupController extends Controller
{
    public function index()
    {
        return view('helpguide::dashboard.employee_groups.index');
    }

    public function show(Request $request)
    {
        $category = EmployeeGroup::findOrFail($request->input('id'));
        return new EmployeeGroupResource($category);
    }
    
    public function fetch()
    {
        $category = EmployeeGroup::paginate(500);
        return EmployeeGroupResource::collection($category);
    }

    

    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:customer_groups,name',
        ]);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "errors" => $validatedData->errors()];
        }

        $category = new EmployeeGroup();
        $category->name = $request->input('name');
        if ($category->save()) {return new EmployeeGroupResource($category);}
    }

    public function update(Request $request, EmployeeGroup $category)
    {

        $validatedData = $request->validate([
            'name' => ['required',
                        'max:255',
                        Rule::unique('customer_groups', 'name')->ignore($category->id)
                ]
        ]);
        
        $category->name = $request->input('name');

        
        if ($category->save()) {
            return ['status' => 'ok', 'message' => __('Updated'), 'data' => new EmployeeGroupResource($category)];
        }

        return ['status' => 'fail', 'message' => __('Failed to save data, please try again')];
    }

   
    public function destroy(Request $request, EmployeeGroup $category)
    {
        if ($category->delete()) {return new EmployeeGroupResource($category);}
    }
}
