<?php

namespace Modules\Discount\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\User;
use Modules\Discount\Entities\Discountlevel;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class NewdiscountController extends Controller
{
     /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
       $business_id = request()->session()->get('user.business_id');
       $enable_petro_module = true;

      $categories = Category::subCategory($business_id, $enable_petro_module);
      
     $discounts = Discountlevel::all();
        $users=User::all();
        return view('discount::discounts.index')
            ->with(compact('categories', 'users', 'discounts'));
    }
    public function listdiscounts(){
        return view('discount::discounts.list');
    }
    public function getdiscounts()
    {
        
        $discounts = Discountlevel::select('*')->orderBy('created_at', 'desc');

        return Datatables()
            ->of($discounts)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <a class="discountModal"><i class="glyphicon discountModal"></i> @lang("messages.edit")</a>
                    
                        <a data-href="{{action(\'\Modules\HR\Http\Controllers\EmployeeController@destroy\', [$id])}}" class="delete_employee"><i class="glyphicon glyphicon-trash" style="color:brown;"></i> @lang("messages.delete")</a>
                    </div>'
                )
                ->removeColumn('id')
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_time' => 'required',
            'sub_category' => 'required',
            'users.*' =>'required',
            'max_discount' =>'required',
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->with('status', $output);
        }
        $users = $request->input('users');
        foreach ($users as $user) {
    Discountlevel::create([
        'data_time' => $request->data_time, 
        'sub_category' => $request->sub_category, 
        'user' => $user, 
        'max_discount' => $request->max_discount,
         'user_id' => '1',
    ]);
     $output = [
            'success' => 1,
            'msg' => 'Discount Level Created Successfully.'
        ];

        return redirect()->to('list-discounts')->with('status', $output);
}
        
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('tasksmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('tasksmanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
