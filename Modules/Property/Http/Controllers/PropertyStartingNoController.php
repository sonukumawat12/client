<?php

namespace Modules\Property\Http\Controllers;

use App\BusinessLocation;
use App\Transaction;
use App\Utils\Util;
;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\PropertyStartingNo;
use Yajra\DataTables\Facades\DataTables;

class PropertyStartingNoController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $peroperty_taxes = PropertyStartingNo::where('business_id', $business_id)
                ->orderBy('id','desc')
                ->select([
                    '*']);

            return DataTables::of($peroperty_taxes)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Property\Http\Controllers\PropertyStartingNoController@edit\', [$id])}}" data-container=".view_modal" class="btn btn-xs btn-modal btn-primary edit_installment_cycle_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                         &nbsp;
                        <button data-href="{{action(\'\Modules\Property\Http\Controllers\PropertyStartingNoController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_installment_cycle_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->addColumn(
                    'status',
                    '@if($is_active == 1)
                        <span class="badge">Active</span>
                     @endif
                    '
                )
                ->editColumn('created_at', '@if(!empty($created_at)){{@format_date($created_at)}}@endif ')
                ->removeColumn('id')
                ->rawColumns(['action','status'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        
        return view('property::setting.starting_no.create')
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            
            $business_id = request()->session()->get('user.business_id');
            
            $input = $request->only(['prefix','starting_no','is_active']);
           
            $input['business_id'] = $business_id;
            
            if(!empty($input['is_active'])){
                PropertyStartingNo::where('business_id', $business_id)->update([
                    'is_active' => 0,
                ]);
                
                $input['is_active'] = 1;
            }else{
                $input['is_active'] = 0;
            }
            
            $tax = PropertyStartingNo::create($input);
            $output = [
                'success' => true,
                'tab' => 'starting_no',
                'msg' => __("property::lang.installment_cycle_added_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'tab' => 'starting_no',
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with($output);
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $installment_cycle = PropertyStartingNo::where('business_id', $business_id)->find($id);

            
            return view('property::setting.starting_no.edit')
                ->with(compact('installment_cycle'));
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        try {
                $business_id = request()->session()->get('user.business_id');
                $input = $request->only(['prefix','starting_no','is_active']);
                 
                if(!empty($input['is_active'])){
                    PropertyStartingNo::where('business_id', $business_id)->update([
                        'is_active' => 0,
                    ]);
                    
                    $input['is_active'] = 1;
                }else{
                    $input['is_active'] = 0;
                }
                
                PropertyStartingNo::where('id', $id)->update($input);

                $output = [
                    'success' => true,
                    'tab' => 'starting_no',
                    'msg' => __("property::lang.installment_cycle_updated_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'tab' => 'starting_no',
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return redirect()->back()->with($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                PropertyStartingNo::findOrFail($id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("property::lang.installment_cycle_deleted_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => '__("messages.something_went_wrong")'
                ];
            }

            return $output;
        }
    }
}
