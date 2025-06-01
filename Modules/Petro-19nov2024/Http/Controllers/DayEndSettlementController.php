<?php

namespace Modules\Petro\Http\Controllers;

;
use App\Store;
use Illuminate\Http\Request;
use Response;
use Illuminate\Routing\Controller;
use DB;
use Modules\Petro\Entities\DayEnd;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\NotificationUtil;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\MeterSale;
use Session;
use Spatie\Activitylog\Models\Activity;
use Modules\Petro\Entities\Settlement;

use Modules\Petro\Entities\PumpOperator;

class DayEndSettlementController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;
    protected $notificationUtil;
    private $barcode_types;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, NotificationUtil $notificationUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->notificationUtil = $notificationUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $startDate = request()->start_date;
            $endDate = request()->end_date;

            $query = DayEnd::leftjoin('users as created', 'created.id', 'day_ends.created_by')
                ->leftjoin('users as editted', 'editted.id', 'day_ends.updated_by')
                ->where('day_ends.business_id', $business_id)
                ->select([
                    'day_ends.*',
                    'created.username as user_added',
                    'editted.username as user_editted'
                ]);
                
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query = $query->whereDate('day_end_date','>=', $startDate)->whereDate('day_end_date','<=',$endDate);
            }
            
            $query->orderBy('day_ends.id','DESC')->get();
            
            $dip_report = Datatables::of($query)
                ->addColumn(
                        'action',
                        function ($row) {
                            $html = '';
                             $html .=  '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                    data-toggle="dropdown" aria-expanded="false">' .
                                    __("messages.actions") .
                                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                                
                                if(auth()->user()->can('edit_day_end_settlement')){
                                    $html .= '<li><a href="#" data-href="' . action("\Modules\Petro\Http\Controllers\DayEndSettlementController@edit", [$row->id]) . '" class="edit_dip"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';
                                }

                                
                                $html .= '</ul></div>';
                            return $html;
                        })
                ->editColumn('user_editted', function ($row) {
                    if(!empty($row->user_editted)){
                        $html = $row->user_editted."<br>".$this->productUtil->format_date($row->updated_at,true);
                        return $html;
                    }else{
                        return '--';
                    }
                })
                ->editColumn('pumps', function ($row) {
                    if(!empty($row->pumps)){
                        $html = "";
                        $pump_ids = json_decode($row->pumps,true)?? [];
                        
                        foreach($pump_ids as $key => $pump){
                            $pump = Pump::find($pump);
                            if(!empty($pump)){
                                if($key != 0){
                                    $html .= "<br>".$pump->pump_name;
                                }else{
                                    $html .= $pump->pump_name;
                                }
                            }
                        }
                        
                        return $html;
                    }
                })
                ->editColumn('sold_pumps', function ($row) {
                    if(!empty($row->sold_pumps)){
                        $html = "";
                        $pump_ids = json_decode($row->sold_pumps,true);
                        foreach($pump_ids as $key => $pump){
                            $pump = Pump::find($pump);
                            $settlements = MeterSale::where('pump_id',$pump->id)->leftjoin('settlements','settlements.id','meter_sales.settlement_no')->whereDate('settlements.transaction_date',$row->day_end_date)->pluck('settlements.settlement_no')->toArray() ?? [];
                            
                            
                            if(!empty($pump)){
                                if($key != 0){
                                    $html .= "<br>".$pump->pump_name."(<b>".implode(', ',$settlements)."</b>)";
                                }else{
                                    $html .= $pump->pump_name."(<b>".implode(', ',$settlements)."</b>)";
                                }
                            }
                        }
                        
                        return $html;
                    }
                })
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                
                ->editColumn('day_end_date', '{{@format_date($day_end_date)}}')
                
                ->removeColumn('id');
                
            return $dip_report->rawColumns(['action','user_editted','pumps','sold_pumps'])
                ->make(true);
        }
        
        return view('petro::petro_settings.index');
    }
    
    public function create(Request $request){
        $business_id = $request->session()->get('user.business_id');
        
        $date = date('Y-m-d');
        $isolate_pumps = MeterSale::leftjoin('settlements','settlements.id','meter_sales.settlement_no')->where('settlements.business_id',$business_id)->whereDate('settlements.transaction_date',$date)->select('meter_sales.pump_id')->pluck('pump_id')->toArray() ?? [];
        
        $pumps = Pump::where('business_id', $business_id)->whereNotIn('id',$isolate_pumps)->pluck('pump_name', 'id');
        
        
        $html = (string) view('petro::petro_settings.partials.pending_pumps')->with(compact(
                'pumps'
            ));
            
        $pumps = Pump::where('business_id', $business_id)->whereIn('id',$isolate_pumps)->pluck('pump_name', 'id');
            
        $html_sold = (string) view('petro::petro_settings.partials.pumps_in_settlement')->with(compact(
                'pumps','date'
            ));
        
        return view('petro::petro_settings.partials.add_day_end')->with(compact(
            'html','html_sold'
        ));
    }
    
    public function pendingPumps(Request $request){
        if(empty($request->date)){
            $date = date('Y-m-d');
        }else{
            $date = $this->transactionUtil->uf_date($request->date);
        }
        
        $business_id = $request->session()->get('user.business_id');
        $isolate_pumps = MeterSale::leftjoin('settlements','settlements.id','meter_sales.settlement_no')->where('settlements.business_id',$business_id)->whereDate('settlements.transaction_date',$date)->select('meter_sales.pump_id')->pluck('pump_id')->toArray() ?? [];
        
        // dd($isolate_pumps);
        
        $pumps = Pump::where('business_id', $business_id)->whereNotIn('id',$isolate_pumps)->pluck('pump_name', 'id');
        
        $html = (string) view('petro::petro_settings.partials.pending_pumps')->with(compact(
                'pumps'
            ));
            
        $pumps = Pump::where('business_id', $business_id)->whereIn('id',$isolate_pumps)->pluck('pump_name', 'id');
        
        $html_sold = (string) view('petro::petro_settings.partials.pumps_in_settlement')->with(compact(
                'pumps','date'
            ));
            
            
        return array('pending' => $html, 'sold' => $html_sold);
    }
    

        public function store(Request $request)
    {
        try {

            $business_id = $request->session()->get('user.business_id');

            $data = array('business_id' => $business_id,
                'day_end_date' => $this->transactionUtil->uf_date($request->day_end_date),
                'pumps' => json_encode($request->pumps),
                'sold_pumps' => $request->sold_pumps,
                'created_by' =>auth()->user()->id ,
            );

            $latest_date = DayEnd::where('business_id',$business_id)->get()->last()->day_end_date ?? null;
            if(!empty($latest_date) && strtotime($latest_date) >= strtotime($data['day_end_date'])){
                return redirect()->back()->with('status', $output = [
                    'success' => false,
                    'msg' => __('petro::lang.date_greater_than_day_end')
                ]);
            }

            DayEnd::create($data);


            $settlements = Settlement::where('settlements.transaction_date', $data['day_end_date'])->where('settlements.business_id', $business_id)
                ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')
                ->with([
                    'meter_sales',
                    'other_sales',
                    'other_incomes',
                    'customer_payments',
                    'cash_payments',
                    'cash_deposits',
                    'card_payments',
                    'cheque_payments',
                    'credit_sale_payments',
                    'expense_payments',
                    'excess_payments',
                    'shortage_payments',
                    'cash_deposits',
                    'loan_payments',
                    'drawings_payments',
                    'customer_loans'
                ])
                ->select('settlements.*', 'pump_operators.id as pump_operator_id')
                ->get();

            $total_cash = 0;
            $total_cards = 0;
            $total_credit_sales = 0;
            $total_short = 0;
            $total_loans = 0;
            $total_cheques = 0;

            $cash_deposit = 0;
            $total_expenses = 0;
            $total_excess = 0;
            $loan_payments = 0;
            $owners_drawings = 0;

            $pump_ids = [];
            $total_sale = 0;
            $pumpers_id = [];
            $total_sale = 0;
            $meter_sales_id = [];

            foreach($settlements as $settlement){
              $pump_ids = array_unique(array_merge($pump_ids, $settlement->meter_sales->pluck('pump_id')->toArray()));
              $meter_sales_id = array_merge($meter_sales_id, $settlement->meter_sales->pluck('id')->toArray());
              $pumpers_id[] = $settlement->pump_operator_id; // This will keep adding IDs in a single array
              $pumpers_id = array_unique($pumpers_id); // Use array_unique to remove any duplicate IDs

                $total_sale += $settlement->meter_sales->sum('sub_total') + $settlement->other_sales->sum('sub_total');

                $total_cash += $settlement->cash_payments->sum('amount');
                $total_cards += $settlement->card_payments->sum('amount');
                $total_credit_sales += $settlement->credit_sale_payments->sum('amount');
                $total_short += $settlement->shortage_payments->sum('amount');
                $total_loans += $settlement->customer_loans->sum('amount');
                $total_cheques += $settlement->cheque_payments->sum('amount');

                $cash_deposit += $settlement->cash_deposits->sum('amount');
                $total_expenses += $settlement->expense_payments->sum('amount');
                $total_excess += $settlement->excess_payments->sum('amount');
                $loan_payments += $settlement->loan_payments->sum('amount');
                $owners_drawings += $settlement->drawings_payments->sum('amount');
            }

            $pumps = Pump::whereIn('id',$pump_ids)->select('pump_name')->pluck('pump_name')->toArray() ?? [];
            $pump_operator = PumpOperator::whereIn('id', $pumpers_id)->select('name')->pluck('name')->toArray() ?? [];

            $dip_readings = DB::table('dip_readings')
                ->leftJoin('fuel_tanks', 'dip_readings.tank_id', '=', 'fuel_tanks.id')
                ->leftJoin('products', 'fuel_tanks.product_id', '=', 'products.id')
                ->where('dip_readings.business_id', $business_id)
                ->whereRaw("STR_TO_DATE(date_and_time, '%m/%d/%Y') = ?", [$data['day_end_date']])
                ->select([
                    'dip_readings.*',
                    'fuel_tanks.fuel_tank_number as tank_name',
                    'products.name as product_name',
                    'products.id as productID'
                ])
                ->get();

            $tank_qty_diff = '';
            foreach($dip_readings as $one){
                $diff = $this->productUtil->num_f($one->fuel_balance_dip_reading - $one->current_qty);
                $tank_qty_diff .= PHP_EOL.$one->tank_name." (".$one->product_name.") ---> ".$diff;
            }

            $tank_summary = DB::table('fuel_tanks')
                ->select('fuel_tanks.*',
                    'products.name as product_name','products.id as product_id')
                ->leftJoin('products', 'fuel_tanks.product_id', '=', 'products.id')
                ->leftjoin('business_locations','business_locations.id','fuel_tanks.location_id')
                ->where('fuel_tanks.business_id', $business_id)->select('fuel_tanks.*','business_locations.name as location_name','products.name as product_name')->get();

            $prod_summary = '';
            foreach($tank_summary as $tank){
                $date_obj = \Carbon::parse($data['day_end_date']);
                $tank->start_date = $date_obj;
                $tank->end_date = $date_obj->endOfDay();

                $tank->starting_qty = $this->transactionUtil->num_f($this->transactionUtil->getTankBalanceByDate($tank->id,$tank->start_date));
                $tank->sold_qty = $this->transactionUtil->num_f($this->transactionUtil->__totalSellAndTransferOut($business_id,$tank->start_date,$tank->end_date,$tank->id));
                $tank->purchase_qty = $this->transactionUtil->num_f($this->transactionUtil->__totalPurchaseAndTransferIn($business_id,$tank->start_date,$tank->end_date,$tank->id));
                $tank->balance_qty = $this->transactionUtil->num_f($this->transactionUtil->getTankBalanceByDateInclude($tank->id,$tank->end_date));

                $prod_summary .= PHP_EOL.$tank->fuel_tank_number." (".$tank->product_name.") [Starting Qty = ".$tank->starting_qty.", Received Qty = ".$tank->purchase_qty.", Sold Qty = ".$tank->sold_qty.", Balance Qty = ".$tank->balance_qty."]";
            }

            $bulk_sales = MeterSale::leftJoin('pumps', 'pumps.id', '=', 'meter_sales.pump_id')
                ->leftJoin('products', 'products.id', '=', 'meter_sales.product_id')
                ->whereIn('meter_sales.id', $meter_sales_id)
                ->where('pumps.bulk_sale_meter', '>', 0)
                ->select(
                    'products.name',
                    DB::raw('SUM(meter_sales.closing_meter - meter_sales.starting_meter - meter_sales.testing_qty) as total_sales')
                )
                ->groupBy('meter_sales.product_id')
                ->get();

            $product_sales = MeterSale::leftJoin('pumps', 'pumps.id', '=', 'meter_sales.pump_id')
                ->leftJoin('products', 'products.id', '=', 'meter_sales.product_id')
                ->whereIn('meter_sales.id', $meter_sales_id)
                ->where('pumps.bulk_sale_meter', 0)
                ->select(
                    'products.name',
                    DB::raw('SUM(meter_sales.closing_meter - meter_sales.starting_meter - meter_sales.testing_qty) as total_sales')
                )
                ->groupBy('meter_sales.product_id')
                ->get();

            $psale_html = "";
            foreach($product_sales as $psale){
                $psale_html .= $psale->name." Sold Qty ".$this->transactionUtil->num_f($psale->total_sales)."lts".PHP_EOL;
            }

            $pbulk_html = "";
            foreach($bulk_sales as $pbulk){
                $pbulk_html .= $pbulk->name." Sold Qty ".$this->transactionUtil->num_f($pbulk->total_sales)."lts".PHP_EOL;
            }



            $sms_data = array(
                'date' => $request->day_end_date,
                'total_sale' => $this->transactionUtil->num_f($total_sale),
                'pumpers_worked' => implode(',',$pump_operator),
                'pumps' => implode(',',$pumps),
                'total_cash' => $this->transactionUtil->num_f($total_cash),
                'total_cards' => $this->transactionUtil->num_f($total_cards),
                'total_credit_sales' => $this->transactionUtil->num_f($total_credit_sales),
                'total_short' => $this->transactionUtil->num_f($total_short),
                'total_loans' => $this->transactionUtil->num_f($total_loans),
                'total_cheques' => $this->transactionUtil->num_f($total_cheques),

                'cash_deposit' => $this->transactionUtil->num_f($cash_deposit),
                'total_expenses' => $this->transactionUtil->num_f($total_expenses),
                'total_excess' => $this->transactionUtil->num_f($total_excess),
                'loan_payments' => $this->transactionUtil->num_f($loan_payments),
                'owners_drawings' => $this->transactionUtil->num_f($owners_drawings),

                'tank_product_qty_difference' => $tank_qty_diff,
                'fuel_category_products' => $prod_summary,
                'product_sold_qty' => $psale_html,
                'bulk_sale_qty' => $pbulk_html
            );

            $this->notificationUtil->sendPetroNotification('day_end_settlement',$sms_data);


            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status',$output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    
    public function show()
    {
        return view('petro::show');
    }
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $day_end = DayEnd::findOrFail($id);
        $pumps_ = Pump::whereIn('id',json_decode($day_end->pumps,true))->pluck('pump_name','id');
        
        $pumps = Pump::where('business_id', $business_id)->whereIn('id',json_decode($day_end->sold_pumps,true))->pluck('pump_name', 'id');
        $date = $day_end->day_end_date;
            
        $html_sold = (string) view('petro::petro_settings.partials.pumps_in_settlement')->with(compact(
                'pumps','date'
            ));
        
        return view('petro::petro_settings.partials.edit_day_end')->with(compact(
            'pumps_','day_end','html_sold'
        ));

    }
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    
    public function update(Request $request, $id)
    {
        try {
            
            $business_id = $request->session()->get('user.business_id');
            $day_end = DayEnd::findOrFail($id);
            
            $changed = "";
            if(date('Y-m-d',strtotime($day_end->day_end_date)) != $this->transactionUtil->uf_date($request->day_end_date)){
                $changed .= "Day end date changed from ".$this->productUtil->format_date($day_end->day_end_date)." to ".$request->day_end_date;
            }
            
           
            
            if(!empty($changed)){
                $changed .= PHP_EOL."By ".auth()->user()->username;
                $changed .= PHP_EOL."At ".$this->productUtil->format_date(date('Y-m-d H:i'),true);
                
                $activity = new Activity();
                $activity->log_name = "Day End Settlement";
                $activity->description = "update";
                $activity->subject_id = $id;
                $activity->subject_type = "Modules\Petro\Entities\DayEnd";
                $activity->causer_id = auth()->user()->id;
                $activity->causer_type = 'App\User';
                $activity->properties = $changed;
                $activity->created_at = date('Y-m-d H:i');
                $activity->updated_at = date('Y-m-d H:i');
                $activity->save();
            }
            
            $data = array('business_id' => $business_id,
                'day_end_date' => $this->transactionUtil->uf_date($request->day_end_date),
                'pumps' => json_encode($request->pumps),
                'updated_by' =>auth()->user()->id ,
            );
            
            DayEnd::where('id',$id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            logger($e);
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status',$output);
    }
    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    
    public function destroy()
    {
    }
}
