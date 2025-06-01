<?php

namespace App\Http\Controllers;

use App\Category;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Product;
class TaxonomyController extends Controller
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $category_type = request()->get('type');
       
        if ($category_type == 'product' && !auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
           
            $can_edit = true;
            if($category_type == 'product' && !auth()->user()->can('category.update')) {
                $can_edit = false;
            }

            $can_delete = true;
            if($category_type == 'product' && !auth()->user()->can('category.update')) {
                $can_delete = false;
            }

            $business_id = request()->session()->get('user.business_id');

            $category = Category::leftjoin('categories as parent','parent.id','categories.parent_id')
                            ->leftjoin('users as u','u.id','categories.created_by')
                            ->where('categories.business_id', $business_id)
                            ->where('categories.category_type', $category_type)
                            ->select(['categories.name', 'categories.short_code',
                                'categories.description', 'categories.id', 'categories.parent_id','u.username','parent.name as parentname','categories.nic']);

            return Datatables::of($category)
                ->addColumn(
                    'action', function ($row) use ($can_edit, $can_delete, $category_type)
                    {
                        $html = '';
                        if ($can_edit) {
							$html .= '<button data-href="'.url('taxonomies/'.$row->id.'/edit?type=').$category_type.'" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</button>';
                        }

                        if ($can_delete) {
                            $html .= '&nbsp;<button data-href="' . url('taxonomies/'.$row->id) . '" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';
                        }

                        return $html;
                    }
                )
                ->editColumn('name', function ($row) use($category_type) {
                    if ($row->parent_id != 0 && $category_type != "hrm_designation") {
                        return '--' . $row->name;
                    } else {
                        return $row->name;
                    }
                })
                ->removeColumn('id')
                ->removeColumn('parent_id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);
        

        return view('taxonomy.index')->with(compact('module_category_data', 'module_category_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        $category_type = request()->get('type');
        if ($category_type == 'product' && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

        $categories = Category::where('business_id', $business_id)
                        ->where('parent_id', 0)
                        ->where('category_type', $category_type)
                        ->select(['name', 'short_code', 'id'])
                        ->get();

        $parent_categories = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $parent_categories[$category->id] = $category->name;
            }
        }
        
        $departments = Category::where('business_id', $business_id)
                        ->where('parent_id', 0)
                        ->where('category_type', 'hrm_department')
                        ->pluck('name','id');

        return view('taxonomy.create')
                    ->with(compact('parent_categories', 'module_category_data', 'category_type','departments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category_type = request()->input('category_type');
        if ($category_type == 'product' && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
                        
            $input = $request->only(['name','nic', 'short_code', 'category_type', 'description']);
            $input['business_id'] = $request->session()->get('user.business_id');
            
            // check uniques
            if($input['category_type'] == 'hrm_department'){
                $category = Category::where('business_id',$input['business_id'])->where('category_type','hrm_department')->where('name',$input['name'])->first();
                
               
                if(!empty($category)){
                    return ['success' => false,
                        'msg' => __("messages.department_name_exists")
                    ];
                }
                
                // $category = Category::where('business_id',$input['business_id'])->where('category_type','hrm_department')->where('nic',$input['nic'])->first();
                
                // if(!empty($category)){
                //     return ['success' => false,
                //         'msg' => __("messages.department_no_exists")
                //     ];
                // }
                
                
            }
            
            if($input['category_type'] == 'hrm_designation'){
                $category = Category::where('business_id',$input['business_id'])->where('category_type','hrm_designation')->where('parent_id',$request->input('parent_id'))->where('name',$input['name'])->first();
                
                if(!empty($category)){
                    return ['success' => false,
                        'msg' => __("messages.designation_exists")
                    ];
                }
            }
            
            
            
            
            if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                $input['parent_id'] = $request->input('parent_id');
            } else {
                $input['parent_id'] = 0;
            }
            
            $input['created_by'] = $request->session()->get('user.id');

            $category = Category::create($input);
            $output = ['success' => true,
                            'data' => $category,
                            'msg' => __("category.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      
        $category_type = request()->get('type');
       
        if ($category_type == 'product' && !auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }
 
       if (request()->ajax()) {
          
            $business_id = request()->session()->get('user.business_id');
            $category = Category::where('business_id', $business_id)->find($id);
            
            $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);
 
            $parent_categories = Category::where('business_id', $business_id)
                                        ->where('parent_id', 0)
                                        ->where('category_type', $category_type)
                                        ->where('id', '!=', $id)
                                        ->pluck('name', 'id');
            $is_parent = false;
            
            if ($category->parent_id == 0) {
                $is_parent = true;
                $selected_parent = null;
            } else {
                $selected_parent = $category->parent_id ;
            }
            
             $departments = Category::where('business_id', $business_id)
                        ->where('parent_id', 0)
                        ->where('category_type', 'hrm_department')
                        ->pluck('name','id');

            return view('taxonomy.edit')
                ->with(compact('category', 'parent_categories', 'is_parent', 'selected_parent', 'module_category_data','departments'))->render();
        }
    }

 public function edit_device($id)
    {
      
        $category_type = 'auto-device';
       
        if ($category_type == 'product' && !auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }
 
            $business_id = request()->session()->get('user.business_id');
            $category = Product::where('business_id', $business_id)->find($id);
            
            $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);
 
            $parent_categories = Product::where('business_id', $business_id)                                      
                                        ->where('type', $category_type)
                                        ->where('id', '!=', $id)
                                        ->pluck('name', 'id');
            $is_parent = false;
            
            // if ($category->parent_id == 0) {
            //     $is_parent = true;
                $selected_parent = null;
            // } else {
            //     $selected_parent = $category->parent_id ;
            // }
            
             $departments = Category::where('business_id', $business_id)
                        ->where('parent_id', 0)
                        ->where('category_type', 'hrm_department')
                        ->pluck('name','id');
           
            return view('taxonomy.edit_device')
                ->with(compact('category', 'parent_categories', 'is_parent', 'selected_parent', 'module_category_data','departments'))->render();
       
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
 
             
            try {
                $input = $request->only(['name','nic', 'description']);
                $business_id = $request->session()->get('user.business_id');
                
                $category = Product::where('business_id', $business_id)->findOrFail($id);

                
                // check uniques
            // if($category->category_type == 'hrm_department'){
            //     $check = Category::where('business_id',$business_id)->where('category_type','hrm_department')->where('id', '!=', $id)->where('name',$input['name'])->first();
                
            //     if(!empty($check)){
            //         return ['success' => false,
            //             'msg' => __("messages.department_name_exists")
            //         ];
            //     }
                
            //     // $check = Category::where('business_id',$business_id)->where('category_type','hrm_department')->where('id', '!=', $id)->where('nic',$input['nic'])->first();
                
            //     // if(!empty($check)){
            //     //     return ['success' => false,
            //     //         'msg' => __("messages.department_no_exists")
            //     //     ];
            //     // }
                
                
            // }
            
            // if($category->category_type == 'hrm_designation'){
            //     $check = Category::where('business_id',$business_id)->where('category_type','hrm_designation')->where('id', '!=', $id)->where('parent_id',$request->input('parent_id'))->where('name',$input['name'])->first();
                
            //     if(!empty($check)){
            //         return ['success' => false,
            //             'msg' => __("messages.designation_exists")
            //         ];
            //     }
            // }

                
                if ($category->category_type == 'product' && !auth()->user()->can('category.update')) {
                    abort(403, 'Unauthorized action.');
                }

                $category->name = $input['name'];
                // $category->nic = $input['nic'];
                $category->product_description = $input['description'];
               // $category->short_code = $request->input('short_code');
                
                // if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                //     $category->parent_id = $request->input('parent_id');
                // } else {
                //     $category->parent_id = 0;
                // }
                $category->save();

                $output = ['success' => true,
                            'msg' => __("category.updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $category = Category::where('business_id', $business_id)->findOrFail($id);

                if ($category->category_type == 'product' && !auth()->user()->can('category.delete')) {
                    abort(403, 'Unauthorized action.');
                }

                $category->delete();

                $output = ['success' => true,
                            'msg' => __("category.deleted_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    public function getCategoriesApi()
    {
        try {
            $api_token = request()->header('API-TOKEN');

            $api_settings = $this->moduleUtil->getApiSettings($api_token);
            
            $categories = Category::catAndSubCategories($api_settings->business_id);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            return $this->respondWentWrong($e);
        }

        return $this->respond($categories);
    }

    /**
     * get taxonomy index page
     * through ajax
     * @return \Illuminate\Http\Response
     */
    public function getTaxonomyIndexPage(Request $request)
    {
         //$category_type = 'auto-device';// request()->get('type');
      
         
        // if (request()->ajax()) {
        //     $category_type = $request->get('category_type');
        //     $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);
            
        //     return view('taxonomy.ajax_index')
        //         ->with(compact('module_category_data', 'category_type'));
        // }
        
         $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $models = DeviceModel::with('Device', 'Brand')
                        ->where('business_id', $business_id)
                        ->select('*');

           $category_type = 'auto-device';//$request->get('category_type');
            $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);
 
            return Datatables::of($module_category_data)
                    ->addColumn('action', function ($row) {
                        $html = '<div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                        '.__("messages.action").'
                                        <span class="caret"></span>
                                        <span class="sr-only">
                                        '.__("messages.action").'
                                        </span>
                                    </button>
                                    ';

                        $html .= '<ul class="dropdown-menu dropdown-menu-left" role="menu">
                                <li>
                                    <a data-href="' . action('\Modules\AutoRepairServices\Http\Controllers\DeviceModelController@edit', ['device_model' => $row->id]) . '" class="cursor-pointer edit_device_model">
                                        <i class="fa fa-edit"></i>
                                        '.__("messages.edit").'
                                    </a>
                                </li>
                                <li>
                                    <a data-href="' . action('\Modules\AutoRepairServices\Http\Controllers\DeviceModelController@destroy', ['device_model' => $row->id]) . '"  id="delete_a_model" class="cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                        '.__("messages.delete").'
                                    </a>
                                </li>
                                </ul>';

                        $html .= '
                                </div>';

                        return $html;
                    })
                ->editColumn('repair_checklist', function ($row) {
                    $checklist = '';
                    if (!empty($row->repair_checklist)) {
                        $checklist = explode('|', $row->repair_checklist);
                    }

                    return $checklist;
                })
                ->editColumn('device_id', function ($row) {
                    return optional($row->Device)->name;
                })
                ->editColumn('brand_id', function ($row) {
                    return optional($row->Brand)->name;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'repair_checklist', 'device_id', 'brand_id'])
                ->make(true);
        }
    }
}
