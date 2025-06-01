<?php
namespace Modules\Petro\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\CustomerReference;
use App\Product;
use App\Utils\ProductUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\DailyVoucher;
use Modules\Petro\Entities\DailyVoucherItem;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumpOperator;
use Yajra\DataTables\Facades\DataTables;

use Modules\Petro\Entities\SettlementCreditSalePayment;
use App\Business;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\NotificationTemplate;
use Milon\Barcode\DNS2D;
use Modules\Petro\Entities\PetroShift;

class DailyVoucherController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $contactUtil;
    protected $businessUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, BusinessUtil   $businessUtil,ContactUtil $contactUtil)
    {
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->contactUtil = $contactUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {
            $daily_vouchers = DailyVoucher::leftjoin('pumps', 'daily_vouchers.pump_id', 'pumps.id')
                ->leftjoin('pump_operators', 'daily_vouchers.operator_id', 'pump_operators.id')
                ->leftjoin('settlements','settlements.id','daily_vouchers.settlement_no')
                ->leftjoin('contacts', 'daily_vouchers.customer_id', 'contacts.id')
                ->leftjoin('business_locations', 'daily_vouchers.location_id', 'business_locations.id')
                ->leftjoin('customer_references', 'daily_vouchers.vehicle_no', 'customer_references.id')
                ->leftjoin('users', 'daily_vouchers.created_by', 'users.id')
                ->where('daily_vouchers.business_id', $business_id)
                ->select(
                    'daily_vouchers.*',
                    'pumps.pump_name',
                    'business_locations.name as location_name',
                    'pump_operators.name as operator_name',
                    'customer_references.reference',
                    'contacts.name as customer_name',
                    'contacts.credit_limit',
                    'users.username as username',
                    'settlements.settlement_no as settlement_nos',
                    'settlements.status as settlement_status'
                );
            if(!empty(request()->location_id)){
                $daily_vouchers->where('daily_vouchers.location_id', request()->location_id);
            }
            
            if (!empty(request()->settlement_id)) {
                $daily_vouchers->where('settlements.id', request()->settlement_id);
            }
            
            if (!empty(request()->status)) {
                    if(request()->status == 'completed'){
                        $daily_vouchers->whereNotNull('settlements.settlement_no')->where('settlements.status',0);
                    }
                    
                    if(request()->status == 'pending'){
                        $daily_vouchers->where(function($q) {
                            $q->whereNull('settlements.settlement_no')
                              ->orWhere('settlements.status', 1);
                        });

                    }
                }
            
            if (!empty(request()->customer_id)) {
                $query->where('daily_vouchers.customer_id', request()->customer_id);
            }
            
            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $daily_vouchers->whereDate('daily_vouchers.transaction_date', '>=',request()->start_date);
                $daily_vouchers->whereDate('daily_vouchers.transaction_date', '<=',request()->end_date);
            }

            return DataTables::of($daily_vouchers->orderBy('transaction_date','desc'))

                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (auth()->user()->can('daily_voucher.view')) {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Petro\Http\Controllers\DailyVoucherController@print', $row->id) . '" class="print_bill" ><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->editColumn('credit_limit',function($row){
                    if(empty($row->credit_limit)){
                        return "No Limit";
                    }else{
                        return $this->productUtil->num_f($row->credit_limit);
                    }
                })
                ->addColumn('balance_available',function($row){
                    if(empty($row->credit_limit)){
                        $bal =  $this->productUtil->num_f(($row->current_outstanding - $row->total_amount));
                    }else{
                        $bal = $this->productUtil->num_f(($row->credit_limit-$row->current_outstanding - $row->total_amount));
                    }
                    
                    if($this->productUtil->num_uf($bal < 0)){
                        return "<span style='color: red;'>$bal</span>";
                    }else{
                        return "<span>$bal</span>";
                    }
                    
                })
                
                ->addColumn('status',function($row){
                    if(empty($row->settlement_nos) || $row->settlement_status == 1){
                        return 'Pending';
                    }else{
                        return 'Completed';
                    }
                })
                
                ->addColumn('total_collection', function ($id) {
                    $total = DB::table('daily_vouchers')
                            ->where('operator_id', $id->operator_id)
                            ->where('id', '<=', $id->id)
                            ->whereNull('settlement_no')
                            ->sum('total_amount') ?? 0;
                        
                        return $this->productUtil->num_f($total);
                })
                    
                ->editColumn('voucher_order_date','{{ @format_date($voucher_order_date) }}')
                ->editColumn('current_outstanding','{{ @num_format($current_outstanding) }}')
                ->editColumn('total_amount','{{ @num_format($total_amount) }}')
                ->rawColumns(['action','balance_available'])
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
       

        $business_id = request()->session()->get('business.id');

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $pumps = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');
        $open_shifts = PetroShift::where('business_id',$business_id)->where('status','0')->pluck('pump_operator_id')->toArray();
        $pump_operators = PumpOperator::where('business_id', $business_id)->whereNotIn('id',$open_shifts)->pluck('name', 'id');
        $daily_vouchers_no = (DailyVoucher::where('business_id', $business_id)->count()) + 1;
        $busness_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $default_location = current(array_keys($busness_locations->toArray()));
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');

        return view('petro::daily_collection.partials.create_daily_voucher')->with(compact(
            'customers',
            'pumps',
            'pump_operators',
            'daily_vouchers_no',
            'busness_locations',
            'products',
            'default_location'
        ));
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
            $data = $request->credit_data;
            $pump_operator_id = $request->pump_operator_id;
            $pump_operator = PumpOperator::findOrFail($pump_operator_id);
            $business_id = $request->session()->get('business.id');
            
            
            foreach($data as $one){
                $price = $this->productUtil->num_uf($one['price']);
                $unit_discount = $this->productUtil->num_uf($one['unit_discount']);
                $qty = $this->productUtil->num_uf($one['qty']);
                $amount = $this->productUtil->num_uf($one['amount']);
                $sub_total = $this->productUtil->num_uf($one['sub_total']);
                $total_discount = $this->productUtil->num_uf($one['total_discount']);
                
                
                $dt = array(
                    'business_id' => $business_id,
                    'pump_operator_id' => $pump_operator_id,
                    'customer_id' => $one['customer_id'],
                    'product_id' => $one['product_id'],
                    'order_number' => $one['order_number'],
                    'order_date' => \Carbon::parse($one['order_date'])->format('Y-m-d'),
                    'price' => $price,
                    'discount' => $unit_discount,
                    'qty' => $qty,
                    'amount' => $amount,
                    'sub_total' => $sub_total,
                    'total_discount' => $total_discount,
                    'outstanding' => $this->productUtil->num_uf($one['outstanding']),
                    'credit_limit' => $one['credit_limit'],
                    'customer_reference' => $one['customer_reference'],
                    'note' => $one['note'],
                    'is_from_pumper' => 1
                );
                $credit_sale_payment = SettlementCreditSalePayment::create($dt);
                
                // store the customer reference
                if(!empty($credit_sale_payment->customer_reference)){
                    $customer = Contact::findOrFail($credit_sale_payment->customer_id);
                    $name = $customer->name;
                    $barcode_string = $name . '.' . $credit_sale_payment->customer_reference;
                    $qr = new DNS2D();
                    $qr = $qr->getBarcodePNG($barcode_string, 'QRCODE');
                    $src = 'data:image/png;base64,' . $qr;
        
                    
                    $ref_data = array(
                        'business_id' => $credit_sale_payment->business_id,
                        'date' => date('Y-m-d', strtotime($credit_sale_payment->order_date)),
                        'contact_id' => $credit_sale_payment->customer_id,
                        'reference' =>$credit_sale_payment->customer_reference,
                        'barcode_src' => $src
                    );
                    CustomerReference::updateOrCreate(['business_id' => $credit_sale_payment->business_id,'contact_id' => $credit_sale_payment->customer_id,'reference' =>$credit_sale_payment->customer_reference],$ref_data);
                    
                }
                
                $customer_reference =  CustomerReference::where('reference' ,$credit_sale_payment->customer_reference)->first()->id ?? 0;
                
                $daily_vouchers_no = (DailyVoucher::where('business_id', $business_id)->count()) + 1;
                
                $data = array(
                    'business_id' => $business_id,
                    'transaction_date' => date('Y-m-d', strtotime($credit_sale_payment->order_date)),
                    'daily_vouchers_no' => $daily_vouchers_no,
                    'location_id' => $pump_operator->location_id,
                    
                    'pump_id' => null,
                    
                    'operator_id' => $pump_operator->id,
                    'customer_id' => $credit_sale_payment->customer_id,
                    'current_outstanding' => $this->productUtil->num_uf($one['outstanding']),
                    'outstanding_pending' => $this->productUtil->num_uf($one['outstanding']),
                    
                    'voucher_order_number' => $one['order_number'],
                    'voucher_order_date' => \Carbon::parse($credit_sale_payment->order_date)->format('Y-m-d'),
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    
                    'vehicle_no' => $customer_reference,
                    'total_amount' => $sub_total
                );
                
                $daily_voucher = DailyVoucher::create($data);
                
                $details = array(
                    'business_id' => $business_id,
                    'daily_voucher_id' => $daily_voucher->id,
                    'product_id' => $this->productUtil->num_uf($one['product_id']),
                    'unit_price' => $this->productUtil->num_uf($price),
                    'qty' => $qty,
                    'sub_total' => $this->productUtil->num_uf($sub_total),

                );
                DailyVoucherItem::create($details);
                
                
                // fetch customer's uncreditted credit sales
                $uncreditted = SettlementCreditSalePayment::where('customer_id',$one['customer_id'])->whereNull('is_committed')->where('is_from_pumper',1)->sum('sub_total') ?? 0;
                
                $total_paid = 0;
                
                $business_id = request()->session()->get('user.business_id');
                $business = Business::where('id', $business_id)->first();
                $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
                
                $contact = Contact::where('id',$credit_sale_payment->customer_id)->first();
                
                $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for','credit_sale')->first();


                $final_total = $credit_sale_payment->amount - $credit_sale_payment->total_discount;
                
                if(!empty($msg_template) && $contact->credit_notification == 'pumper_dashboard'){
                    
                    $msg = $msg_template->sms_body;
                    $msg = str_replace('{business_name}',$business->name,$msg);
                    $msg = str_replace('{total_amount}',$this->productUtil->num_f($final_total),$msg);
                    $msg = str_replace('{contact_name}',$contact->name,$msg);
                    $msg = str_replace('{invoice_number}',"",$msg);
                    $msg = str_replace('{transaction_date}',date('Y-m-d', strtotime($credit_sale_payment->order_date)),$msg);
                    $msg = str_replace('{paid_amount}',$this->productUtil->num_f($total_paid),$msg);
                    $msg = str_replace('{due_amount}',$this->productUtil->num_f($final_total - $total_paid),$msg);
                    $msg = str_replace('{cumulative_due_amount}', $this->productUtil->num_f($this->contactUtil->getCustomerBalance($credit_sale_payment->customer_id,$business_id,true) +  $uncreditted),$msg);
                    
                    
                    $phones = array($contact->mobile,$contact->alternate_number);
                    
                    if(!empty($phones)){
                        $data = [
                            'sms_settings' => $sms_settings,
                            'mobile_number' => implode(',',$phones),
                            'sms_body' => $msg
                        ];
                        
                        $response = $this->businessUtil->sendSms($data,'credit_sale',$contact); 
                    }
                }
                
            }

            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getCustomerReference($id)
    {
        $refs = CustomerReference::where('contact_id', $id)->select('reference', 'id')->get();

        $html = '<option>Please Select</option>';

        foreach ($refs as $ref) {
            $html .= '<option value="' . $ref->id . '">' . $ref->reference . '</option>';
        }

        return $html;
    }

    public function getProductPrice($id)
    {
        $price = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->where('variations.product_id', $id)
            ->select('default_sell_price')
            ->first();

        if (!empty($price)) {
            return $this->productUtil->num_f($price->default_sell_price);
        } else {
            return '0';
        }
    }

    public function getProductRow()
    {
        $index = request()->index;
        $business_id = request()->session()->get('business.id');
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');
        return view('petro::daily_collection.partials.product_row')->with(compact('products', 'index'));
    }

    public function print($id)
    {
        $daily_voucher = DailyVoucher::leftjoin('pumps', 'daily_vouchers.pump_id', 'pumps.id')
            ->leftjoin('pump_operators', 'daily_vouchers.operator_id', 'pump_operators.id')
            ->leftjoin('contacts', 'daily_vouchers.customer_id', 'contacts.id')
            ->leftjoin('customer_references', 'daily_vouchers.vehicle_no', 'customer_references.id')
            ->leftjoin('users', 'daily_vouchers.created_by', 'users.id')
            ->where('daily_vouchers.id', $id)
            ->select(
                'daily_vouchers.*',
                'pumps.pump_name',
                'pump_operators.name as operator_name',
                'customer_references.reference',
                'contacts.name as customer_name',
                'users.username as username'
            )->first();

        $daily_voucher_items = DailyVoucherItem::leftjoin('products', 'daily_voucher_items.product_id', 'products.id')
            ->where('daily_voucher_items.daily_voucher_id', $id)
            ->select('daily_voucher_items.*', 'products.name as product_name')
            ->get();

        return view('petro::daily_collection.partials.print_daily_voucher')->with(compact('daily_voucher', 'daily_voucher_items'));
    }
}
