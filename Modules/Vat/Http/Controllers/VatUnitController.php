<?php

namespace Modules\Vat\Http\Controllers;

use Modules\Vat\Entities\VatUnit;
use Modules\Vat\Entities\VatProduct;
use App\Utils\ModuleUtil;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Utils\Util;
use Modules\Superadmin\Entities\HelpExplanation;
use Illuminate\Routing\Controller;

class VatUnitController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $is_property = request()->is_property;
         
            $unit = VatUnit::where('business_id', $business_id)
                        ->select(['actual_name',  'allow_decimal', 'id']);
            
            return Datatables::of($unit)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Vat\Http\Controllers\VatUnitController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_unit_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    <button data-href="{{action(\'\Modules\Vat\Http\Controllers\VatUnitController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_unit_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->editColumn('allow_decimal', function ($row) {
                    if ($row->allow_decimal) {
                        return __('messages.yes');
                    } else {
                        return __('messages.no');
                    }
                })
                
                
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        
        return view('vat::unit.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        $business_id = request()->session()->get('user.business_id');
        
        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }
        
        return view('vat::unit.create')
                ->with(compact('quick_add'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        try {
            $input = $request->only(['actual_name', 'allow_decimal']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            
            
            $unit = VatUnit::create($input);
            $output = ['success' => true,
                        'data' => $unit,
                        'msg' => __("unit.added_success")
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
        

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $unit = VatUnit::where('business_id', $business_id)->find($id);
            return view('vat::unit.edit')
                ->with(compact('unit'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        if (request()->ajax()) {
            try {
                $input = $request->only(['actual_name', 'allow_decimal']);
                $business_id = $request->session()->get('user.business_id');

                $unit = VatUnit::where('business_id', $business_id)->findOrFail($id);
                $unit->actual_name = $input['actual_name'];
                $unit->allow_decimal = $input['allow_decimal'];

                $unit->save();

                $output = ['success' => true,
                            'msg' => __("unit.updated_success")
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
                $business_id = request()->user()->business_id;

                $unit = VatUnit::where('business_id', $business_id)->findOrFail($id);

                //check if any product associated with the unit
                $exists = VatProduct::where('unit_id', $unit->id)
                                ->exists();
                if (!$exists) {
                    $unit->delete();
                    $output = ['success' => true,
                            'msg' => __("unit.deleted_success")
                            ];
                } else {
                    $output = ['success' => false,
                            'msg' => __("lang_v1.unit_cannot_be_deleted")
                            ];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => '__("messages.something_went_wrong")'
                        ];
            }

            return $output;
        }
    }

    
}
