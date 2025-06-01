<?php

namespace Modules\Vat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Vat\Entities\VatSetting;
use Modules\Superadmin\Entities\Subscription;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SettingsController extends Controller
{
    protected $transactionUtil;
    
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }
    
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
       $business_id = request()->session()->get('user.business_id');
        
        if (request()->ajax()) {
            
           
            $expenses = VatSetting::leftjoin('users', 'users.id', '=', 'vat_settings.created_by')
                ->where('vat_settings.business_id', $business_id)

                ->select(
                    
                    'vat_settings.*',

                    'users.username'

                )->get();



            return Datatables::of($expenses)

                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->editColumn('effective_date', '{{@format_date($effective_date)}}')
                ->editColumn('vat_period', function($row){
                    if($row->is_custom_date == 0){
                        return ucfirst(str_replace("-"," ",$row->vat_period));
                    }else{
                        $dates = explode('_',$row->vat_period);
                        return $this->transactionUtil->format_date($dates[0])." - ".$this->transactionUtil->format_date($dates[1]);
                    }
                })
                ->editColumn('status', function ($row) {
                    if($row->status == 1){
                        $html = "<span class='badge bg-success'>".__('vat::lang.active')."</span>";
                    }else{
                        $html = "<span class='badge bg-danger'>".__('vat::lang.inactive')."</span>";
                    }
                    
                    return $html;
                })
                
                ->editColumn('tax_report_name', function ($row) {
                    return __('vat::lang.'.$row->tax_report_name);
                })
                

                ->rawColumns(['status'])

                ->make(true);

        }
        
         return view('vat::vat_settings.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('vat::vat_settings.settings_add');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            
            $subscription = Subscription::active_subscription($business_id);
            $pacakge_details = $subscription->package_details;
            
            if(!empty($pacakge_details['vat_effective_date'])){
                if(strtotime(request()->effective_date) < strtotime($pacakge_details['vat_effective_date'])){
                    $output = [

                        'success' => false,
        
                        'msg' => __('vat::lang.minimum_effective_date').$this->transactionUtil->format_date($pacakge_details['vat_effective_date'])
        
                    ];
                    return redirect()->back()->with('status', $output);
                }
            }

            
            DB::beginTransaction();
            VatSetting::where('business_id',$business_id)->update(['status' => 0]);
            
            $new_setting = array('business_id' => $business_id,'vat_period' => request()->vat_period,'effective_date' => request()->effective_date,'status' => 1,'created_by' => auth()->user()->id,'tax_report_name' => request()->tax_report_name);
            if(request()->vat_period == 'custom'){
                $new_setting['is_custom_date'] = 1;
                $new_setting['vat_period'] = request()->report_cycle_starting_date."_".request()->report_cycle_ending_date;
            }
            
            VatSetting::create($new_setting);
            
            DB::commit();
        
                    $output = [
        
                        'success' => true,
        
                        'msg' => __('messages.success')
        
                    ];
        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
       
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
