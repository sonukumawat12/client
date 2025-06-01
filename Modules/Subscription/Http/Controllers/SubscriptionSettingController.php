<?php

namespace Modules\Subscription\Http\Controllers;


use App\Utils\ModuleUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Modules\Subscription\Entities\SubscriptionUserActivity;
use Modules\Subscription\Entities\SubscriptionSetting;
use Modules\Subscription\Entities\SubscriptionPrice;
use Yajra\DataTables\DataTables;

class SubscriptionSettingController extends Controller
{
    protected $moduleUtil;
    protected $subscription_cycles;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->subscription_cycles = array(
                'daily' => 'Daily',
                'weekly' => 'Weekly',
                'monthly' => 'Monthly',
                'quarterly' => 'Quarterly',
                'bi_annually' => 'By Annually',
                'annually' => 'Annually'
            );
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        
        if (request()->ajax()) {
            $settings = SubscriptionSetting::leftjoin('users','users.id','subscription_settings.created_by')->where('subscription_settings.business_id', $business_id)->orderBy('subscription_settings.id','DESC')->select('users.username as user','subscription_settings.*')->get();

            return DataTables::of($settings)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Subscription\Http\Controllers\SubscriptionSettingController@edit', [$row->id]) . '" class="btn-modal" data-container=".subscription_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Subscription\Http\Controllers\SubscriptionSettingController@destroy', [$row->id]) . '" class="delete-button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '<li class="divider"></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Subscription\Http\Controllers\SubscriptionSettingController@add_subscription', [$row->id]) . '" class="btn-modal" data-container=".subscription_modal"><i class="fa fa-plus"></i> ' . __("subscription::lang.add_subscription") . '</a></li>';


                        return $html;
                    }
                )
                ->editColumn('base_amount', '{{@num_format($base_amount)}}')
                
                ->addColumn('subscription_amount',function($row){
                    $price = SubscriptionPrice::where('settings_id',$row->id)->latest()->first();
                    if(!empty($price)){
                        return $this->moduleUtil->num_f($price->new_amount);
                    }
                })
                
                ->addColumn('subscription_cycle',function($row){
                    return __('subscription::lang.'.$row->subscription_cycle);
                })
                
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('subscription::settings.index')->with(compact('business_id'));
    }
    
    public function add_subscription($id)
    {
        $business_id = request()->session()->get('business.id');
        
        $current_amount = SubscriptionPrice::where('settings_id',$id)->latest()->first()->new_amount ?? 0;
        
        return view('subscription::settings.add_subscription')->with(compact('id','current_amount'));
    }
    
    
    public function fetch_subscription($id)
    {
        $business_id = request()->session()->get('business.id');
        
        if (request()->ajax()) {
            $settings = SubscriptionPrice::leftjoin('users','users.id','subscription_prices.created_by')->where('subscription_prices.settings_id', $id)->orderBy('subscription_prices.id','DESC')->select('users.username as user','subscription_prices.*')->get();

            return DataTables::of($settings)
                ->editColumn('new_amount', '{{@num_format($new_amount)}}')
                ->editColumn('current_amount', '{{@num_format($current_amount)}}')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $subscription_cycles = $this->subscription_cycles;
        return view('subscription::settings.create')->with(compact('subscription_cycles'));
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
            
            $data = $request->only(['transaction_date','product','base_amount','subscription_cycle']);
            $data['business_id'] = $business_id;
            
            $data['created_by'] = auth()->user()->id;
            
            $exists = SubscriptionSetting::where('business_id',$business_id)->where('product',$data['product'])->where('subscription_cycle',$data['subscription_cycle'])->count();
            if($exists > 0){
                $output =  [
                    'success' => false,
                    'msg' => __('subscription::lang.combination_already_exists')
                ];
                
                return redirect()->back()->with('status', $output);
            }
            
            $setting = SubscriptionSetting::create($data);
            
            $price_data = array('transaction_date' => $data['transaction_date'],'settings_id' => $setting->id,'current_amount' => $request->subscription_amount, 'new_amount' => $request->subscription_amount, 'business_id' => $business_id,'created_by' => auth()->user()->id);
            
            SubscriptionPrice::create($price_data);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function save_subscription(Request $request,$id)
    {
        try {
            $business_id = request()->session()->get('business.id');
            
            $data = $request->all();
            
            
            $price_data = array('transaction_date' => $data['transaction_date'],'settings_id' => $id,'current_amount' => $request->current_amount, 'new_amount' => $request->new_amount, 'business_id' => $business_id,'created_by' => auth()->user()->id);
            
            SubscriptionPrice::create($price_data);
            
            $description = "Updated subscription amount from ".$this->moduleUtil->num_f($request->current_amount)." to ".$this->moduleUtil->num_f($request->new_amount);
            
            SubscriptionUserActivity::create(array('business_id' => $business_id, 'model' => 'SubscriptionSetting','description' => 'update', 'subject_id' => $id, 'properties' => $description, 'created_by' => auth()->user()->id));

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            logger($e);
            
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('subscription::settings.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $subscription_cycles = $this->subscription_cycles;
        $subscription = SubscriptionSetting::findOrFail($id);
        return view('subscription::settings.edit')->with(compact('subscription_cycles','subscription'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
        try {
            $business_id = request()->session()->get('business.id');
            
            $data = $request->only(['transaction_date','product','base_amount','subscription_cycle']);
            
            $subscription = SubscriptionSetting::findOrFail($id);
            
            $exists = SubscriptionSetting::where('id',$id)->update($data);
            
            $is_editted = 0;
            $description = "";
            
            if($subscription->transaction_date != $data['transaction_date']){
                $is_editted = 1;
                $description .= "Date changed from ".$this->moduleUtil->format_date($subscription->transaction_date)." to ".$this->moduleUtil->format_date($data['transaction_date']).PHP_EOL;
            }
            
            if($subscription->base_amount != $data['base_amount']){
                $is_editted = 1;
                $description .= "Base amount changed from ".$this->moduleUtil->num_f($subscription->base_amount)." to ".$this->moduleUtil->num_f($data['base_amount']).PHP_EOL;
            }
            
            if($subscription->product != $data['product']){
                $is_editted = 1;
                $description .= "Product changed from ".($subscription->product)." to ".($data['product']).PHP_EOL;
            }
            
            if($subscription->subscription_cycle != $data['subscription_cycle']){
                $is_editted = 1;
                $description .= "Subscription cycle changed from ".__('subscription::lang.'.$subscription->subscription_cycle)." to ".__('subscription::lang.'.$data['subscription_cycle']).PHP_EOL;
            }
            
            if($is_editted == 1){
                SubscriptionUserActivity::create(array('business_id' => $business_id, 'model' => 'SubscriptionSetting','description' => 'update', 'subject_id' => $subscription->id, 'properties' => $description, 'created_by' => auth()->user()->id));
            }
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
         try {
            $business_id = request()->session()->get('business.id');
            
            SubscriptionUserActivity::create(array('business_id' => $business_id, 'model' => 'SubscriptionSetting','description' => 'delete', 'subject_id' => $id, 'properties' => "Deleted setting ID $id", 'created_by' => auth()->user()->id));
            
            SubscriptionSetting::where('id', $id)->delete();
            SubscriptionPrice::where('settings_id', $id)->delete();
            
            $output = [
                'success' => true,
                'msg' => __('fleet::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
}
