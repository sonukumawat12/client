<?php

namespace Modules\Shipping\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Modules\Shipping\Entities\Driver;
use Modules\Shipping\Entities\CollectionOfficer;
use Modules\Shipping\Entities\Route;
use Modules\Shipping\Entities\Type;
use Modules\Shipping\Entities\ShippingStatus;
use Illuminate\Support\Facades\DB;
use Modules\Shipping\Entities\ShippingMode;
use Modules\Shipping\Entities\ShippingDelivery;
use Modules\Shipping\Entities\ShippingDeliveryDays;
use Modules\Shipping\Entities\ShippingPrefix;
use Modules\Shipping\Entities\ShippingPackage;
use Modules\Shipping\Entities\ShippingCreditDay;
use Modules\Shipping\Entities\ShippingAccount;
use Modules\Shipping\Entities\ShippingAgent;
use Modules\Shipping\Entities\ShippingRecipient;
use Modules\Shipping\Entities\ShippingPartner;
use Modules\Shipping\Entities\ShippingPrice;
use Modules\Shipping\Entities\Shipment;
use Modules\Shipping\Entities\ShipmentPackage;

use App\AccountTransaction;
use App\Transaction;
use App\TransactionPayment;
use App\BusinessLocation; 

use App\Account;
use App\AccountType;
use App\Contact;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

class AddShipmentSWController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        // $shipment = Shipment::with('transection','lineitem','location','agent','driver','sender','receiver','partner','package','mode')->find(38);
        // return $this->addShipmentSWPrint($shipment);
   
        $business_id = request()
            ->session()
            ->get('business.id');

        

        $types = Type::leftjoin('users', 'types.created_by', 'users.id')
            ->where('types.business_id', $business_id)
            ->select('users.username as added_by', 'types.*')
            ->get();
        $shipping_types = $types->pluck('shipping_types', 'id');
        
        $status = ShippingStatus::where('shipping_status.business_id', $business_id)
            ->select('shipping_status.*')
            ->get();
        $shipping_status = $status->pluck('shipping_status', 'id');
        
        $shipping_mode = ShippingMode::where('shipping_mode.business_id', $business_id)
            ->select('shipping_mode','id')->get()->pluck('shipping_mode','id');
            
        $shipping_agents = ShippingAgent::where('shipping_agents.business_id', $business_id)
            ->select('name','id')->get()->pluck('name','id');
            
        
        $shipping_delivery = ShippingDelivery::where('shipping_delivery.business_id', $business_id)
            ->select('shipping_delivery','id')
            ->get()->pluck('shipping_delivery','id');
            
        $shipping_delivery_days = ShippingDeliveryDays::where('shipping_delivery_days.business_id', $business_id)
            ->select('days')
            ->get()->pluck('days','days');
        
        $prefix = ShippingPrefix::where('shipping_prefix.business_id', $business_id)->latest()->first();
        $latest_shipment = Shipment::where('business_id', $business_id)->latest()->first();
        $current = 0;
        if(!empty($latest_shipment)){
            $current_arr = explode('-',$latest_shipment->tracking_no);
            if(!empty($current_arr)){
                $current = $current_arr[sizeof($current_arr) - 1];
            }
        }
    
        $pre = "";
        if(!empty($prefix)){
            $current = $prefix->starting_no > $current ? $prefix->starting_no : $current;
            $pre = $prefix->prefix;
        }
        
        $tracking_no = $pre."-".($current + 1);
            
        $package = ShippingPackage::where('shipping_packages.business_id', $business_id)
            ->select('package_name','id')
            ->get()->pluck('package_name','id');
            
        $credit_days = ShippingCreditDay::where('shipping_credit_days.business_id', $business_id)
            ->select('credit_days')
            ->get()->pluck('credit_days','credit_days');
        
        $shipping_accounts = ShippingAccount::where('business_id', $business_id)->first();
        
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $expense_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);

        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');
        
        $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_type_id)->pluck('name', 'id');

            
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $recipients = ShippingRecipient::where('business_id',$business_id)->select('name','id')->pluck('name','id');
        $shipping_partner = ShippingPartner::where('business_id',$business_id)->select('name','id')->pluck('name','id');
        $drivers = Driver::where('shipping_drivers.business_id', $business_id)
            ->select('shipping_drivers.*')
            ->get()->pluck('driver_name','id');
            
        $prices = ShippingPrice::leftJoin('shipping_mode', 'shipping_prices.shipping_mode', 'shipping_mode.id')
                ->where('shipping_prices.business_id', $business_id)
                ->select(['shipping_prices.id', DB::raw('CONCAT(shipping_prices.shipping_partner, " | ", ROUND(shipping_prices.per_kg, 2), " | ", shipping_mode.shipping_mode) AS name')])->pluck('name','id');
        $payment_types =  $this->productUtil->payment_types(null, true, true, false ,false, true);
        unset($payment_types['credit_purchase']);
        $businessLocations = BusinessLocation::whereBusinessId($business_id)->pluck('name', 'id');
        $currency  = BusinessLocation::with('currency')->whereBusinessId($business_id)->first()->currency;
        return view('shipping::add_shipment_sw.index')->with(
            compact(
                'payment_types',
                'shipping_types',
                'shipping_status',
                'shipping_delivery',
                'shipping_delivery_days',
                'shipping_mode',
                'prefix',
                'package',
                'credit_days',
                'shipping_accounts',
                'income_accounts',
                'expense_accounts',
                
                'tracking_no',
                'shipping_agents',
                'customers',
                'recipients',
                'shipping_partner',
                'drivers',
                'prices',
                'businessLocations',
                'currency'
            )
        );
    }
    
    public function customer_details(){
        $id = request()->id;
        $customer = Contact::findOrFail($id);
        return response()->json(['customer' => $customer]);
    }
    
    public function getRatePerKg(){
        $shipping_mode = request()->shipping_mode;
        $shipping_partner = request()->shipping_partner;
        $shipping_package = request()->shipping_package;
        $shipping_price = ShippingPrice::where('shipping_partner',$shipping_partner)->where('shipping_mode',$shipping_mode)->where('shipping_package',$shipping_package)->first();
        
        if(!empty($shipping_price)){
            $cost = $shipping_price->per_kg;
            $constant = $shipping_price->constant_value;
            $fixed_price = $shipping_price->fixed_price;
        }else{
            $cost = 0;
            $constant = 0;
            $fixed_price=0;
        }
        return response()->json(['cost' => $cost,'constant' => $constant,'fixed_price' => $fixed_price]);
    }
    
    public function recipient_details(){
        $id = request()->id;
        $recipient = ShippingRecipient::findOrFail($id);
        return response()->json(['recipient' => $recipient]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('shipping::create');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function createIncentives()
    {
        $applicable_to = [
            'driver' => 'Driver',
            'helper' => 'Helper',
            'both' => 'Both',
        ];
        $incentive_type = [
            'fixed' => 'Fixed',
            'percentage' => 'Percentage',
        ];
        $based_on = [
            'trip_amount' => 'Trip Amount',
            'company_decision' => 'Company Decision',
        ];

        return view('shipping::settings.routes.create-incentives')->with(compact('applicable_to', 'incentive_type', 'based_on'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        //try {
            $shipment_data = $request->only('tracking_no','agent_no','customer_id','recipient_id','shipping_mode','delivery_time','shipping_partner','location_id');
            $shipment_data['business_id'] = $business_id;
            $shipment_data['operation_date'] = $this->commonUtil->uf_date($request->date);
            $shipment_data['package_type_id'] = $request->shipping_package;
            $shipment_data['schedule_id'] = $request->shipping_delivery;
            $shipment_data['delivery_status'] = $request->shipping_status;
            $shipment_data['driver_id'] = $request->drivers;
            $shipment_data['total'] = $request->total_payable;
            $shipment_data['agent_id'] = $request->agent_no;
            $shipment_data['created_by'] = auth()->user()->id;
            
            $shipment = Shipment::create($shipment_data);
            
            
            $transaction_data = [];
            $transaction_data['invoice_no'] = $shipment_data['tracking_no'];
            $transaction_data['ref_no'] = $shipment->id;
            $transaction_data['status'] = 'final';
            $transaction_data['contact_id'] = $shipment_data['customer_id'];
            $transaction_data['total_before_tax'] = $request->total_payable;
            $transaction_data['final_total'] = $request->total_payable;
            
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = auth()->user()->id;
            $transaction_data['type'] = 'shipment';
            $transaction_data['payment_status'] = 'due';
            
            $transaction_data['transaction_date'] = $shipment_data['operation_date'];
            $transaction = Transaction::create($transaction_data);

            $shipment->transaction_id = $transaction->id;
            $shipment->save();
            
            $payments = $request->payment;
            
            //add payment for transaction
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments);
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
            
            
            $cheque_nos = "";
            if(!empty($request->select_cheques)){
                foreach ($request->select_cheques as $select_cheque) {
                    if (!empty($select_cheque)) {
                        $account_transaction = AccountTransaction::find($select_cheque);
                        
                        $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);
                        
                        if (!empty($transaction_payment)) {
                            $amount = $this->transactionUtil->num_uf($account_transaction->amount);
                            if (!empty($amount)) {
                                $credit_data = [
                                    'amount' => $amount,
                                    'account_id' => $account_transaction->account_id,
                                    'transaction_id' => $transaction->id,
                                    'type' => 'credit',
                                    'sub_type' => null,
                                    'operation_date' => $transaction_data['transaction_date'],
                                    'created_by' => session()->get('user.id'),
                                    'transaction_payment_id' => $transaction_payment->id,
                                    'note' => null,
                                    'attachment' => null
                                ];
                                $credit = AccountTransaction::createAccountTransaction($credit_data);
                                
                                $cheque_nos .= !empty($transaction_payment->cheque_number) ? $transaction_payment->cheque_number."," : "";
                                
                                $transaction_payment->is_deposited = 1;
                                $transaction_payment->save();
                            }
                        }
                    }
                }
            }
            

            foreach($request->new_package_name as $key => $pkg_name){
                // dd($shipment->id);
                $package_data = array('business_id'=> $business_id,'shipment_id' => $shipment->id,'package_name' => $pkg_name,'package_description' => $request->new_package_description[$key],
                    'length'=> $request->new_length_cm[$key],'width'=> $request->new_width_cm[$key],'height'=> $request->new_height_cm[$key],'weight'=> $request->new_weight_cm[$key],
                    'rate_per_kg' => $request->new_per_kg[$key],'volumetric_weight' => $request->new_volumetric_weight[$key],'price_type' => $request->new_price_type[$key],'shipping_charge' => $request->new_shipping_charge[$key],
                    'declared_value' => $request->new_declared_value[$key],	'service_fee' => $request->new_service_fee[$key],	'total' => $request->new_total[$key],    'fixed_price' => $request->fixed_price_value[$key],
                    'created_by' =>auth()->user()->id,
                );
                ShipmentPackage::create($package_data);
            }
            if($request->make_print == 1){
                $shipment = Shipment::with('transection','lineitem','location','location.currency','agent','driver','sender','receiver','partner','package','mode')->find($shipment->id);
                return $this->addShipmentSWPrint($shipment);
            }
        
            $output = [
                'success' => true,
                'tab' => 'agent_details',
                'msg' => __('lang_v1.success')
            ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     DB::rollBack();
        //     $output = [
        //         'success' => false,
        //         'tab' => 'agent_details',
        //         'msg' => __('messages.something_went_wrong')
        //     ];
        // }
        
        return redirect()->back()->with('status', $output);
    }


    /**
     * print out slip 
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function addShipmentSWPrint($shipment)
    {
        $shipment_barcode = $this->getShippingBarcode($shipment);
        return view('shipping::add_shipment_sw.print_shipment',compact('shipment_barcode','shipment'));
    }

    /**
     * Barcode for Shipping
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getShippingBarcode($shipment)
    {
        $tracking_no = $shipment->tracking_no;
        $track_crack = explode('-',$tracking_no);
        $barcode_string = $track_crack[1].'0'.$shipment->business_id.'0'.$shipment->location_id.'0'.$shipment->id;
        $qr = new DNS1D();
        $qr = $qr->getBarcodePNG($barcode_string, 'PHARMA');
        $barcode =  '<img style="max-width: 97%;" src="data:image/png;base64,' . $qr . '" alt="barcode"   />';

        return $barcode;
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    
    public function updatePackages($id){
        $packages = ShipmentPackage::where('shipment_id',$id)->get();
        
        return view('shipping::add_shipment.editpackages')->with(
                compact('packages','id')
            );
    }
    
    public function postupdatePackages(Request $request,$id)
    {
        $business_id = request()->session()->get('business.id');
        try {
            
            $data = $request->package;
            $packages = ShipmentPackage::where('shipment_id',$id)->get();
            
            foreach($packages as $pac){
                $pac->height = $data[$pac->id]['height'];
                $pac->weight = $data[$pac->id]['weight'];
                $pac->length = $data[$pac->id]['length'];
                $pac->width = $data[$pac->id]['width'];
                
                $pac->save();
            }
            
            
            $output = [
                'success' => true,
                'tab' => 'agent_details',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            DB::rollBack();
            $output = [
                'success' => false,
                'tab' => 'agent_details',
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect()->back()->with('status', $output);
    }
    
    public function show($id)
    {
       $business_id = request()
            ->session()
            ->get('business.id');

        $data = Shipment::leftjoin('users', 'shipments.created_by', 'users.id')
                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('shipping_agents', 'shipments.agent_id', 'shipping_agents.id')
                ->leftjoin('shipping_recipients', 'shipments.recipient_id', 'shipping_recipients.id')
                ->leftjoin('shipping_mode', 'shipments.shipping_mode', 'shipping_mode.id')
                ->leftjoin('shipment_packages', 'shipment_packages.shipment_id', 'shipments.id')
                ->leftjoin('shipping_packages', 'shipments.package_type_id', 'shipping_packages.id')
                ->leftjoin('shipping_delivery', 'shipments.schedule_id', 'shipping_delivery.id')
                ->leftjoin('shipping_partners', 'shipments.shipping_partner', 'shipping_partners.id')
                ->leftjoin('shipping_status', 'shipments.delivery_status', 'shipping_status.id')
                ->leftjoin('transactions','transactions.id','shipments.transaction_id')
                ->leftjoin('transaction_payments','transactions.id','transaction_payments.transaction_id')
                ->where('shipments.id', $id)
                ->select([
                    'shipments.*',
                    'users.username as created_by',
                    'contacts.name as sender',
                    'contacts.address as address',
                    'contacts.mobile as mobile',
                    'shipping_recipients.address as rec_address',
                    'shipping_recipients.mobile_1 as rec_mobile_1',
                    'shipping_recipients.mobile_2 as rec_mobile_2',
                    'shipping_recipients.land_no as rec_land_no',
                    'shipping_recipients.postal_code as rec_postal_code',
                    'shipping_recipients.landmarks as rec_landmarks',
                    'shipping_agents.name as agent',
                    'shipping_recipients.name as recipient',
                    'shipping_mode.shipping_mode as mode',
                    'shipping_packages.package_name as package',
                    'shipment_packages.fixed_price as fixed_price_value',
                    'shipment_packages.package_description as package_description',
                    'shipment_packages.length as length',
                    'shipment_packages.width as width',
                    'shipment_packages.height as height',
                    'shipment_packages.weight as weight',
                    'shipment_packages.rate_per_kg as rate_per_kg',
                    'shipment_packages.volumetric_weight as volumetric_weight',
                    'shipment_packages.price_type as price_type',
                    'shipment_packages.shipping_charge as shipping_charge',
                    'shipment_packages.declared_value as declared_value',
                    'shipment_packages.service_fee as service_fee',
                    'shipping_delivery.shipping_delivery as delivery',
                    'shipping_partners.name as partner',
                    'shipping_status.shipping_status as status',
                    'transaction_payments.method as t_method',
                    'transaction_payments.card_transaction_number as t_card_transaction_number',
                    'transaction_payments.note as t_note',
                    'transaction_payments.account_id as t_account_id',
                    'transaction_payments.post_dated_cheque as t_post_dated_cheque',
                    'transaction_payments.cheque_number as t_cheque_number',
                    'transaction_payments.cheque_date as t_cheque_date',
                    'transaction_payments.bank_name as t_bank_name',
                    'transaction_payments.id as t_id',
                    DB::raw('(SELECT SUM(transaction_payments.amount) FROM transaction_payments WHERE
                        transaction_payments.transaction_id=transactions.id) as total_paid')
                ])->get();   
        //$data = Shipment::find($id);    

        

        $types = Type::leftjoin('users', 'types.created_by', 'users.id')
            ->where('types.business_id', $business_id)
            ->select('users.username as added_by', 'types.*')
            ->get();
        $shipping_types = $types->pluck('shipping_types', 'id');
        
        $status = ShippingStatus::where('shipping_status.business_id', $business_id)
            ->select('shipping_status.*')
            ->get();
        $shipping_status = $status->pluck('shipping_status', 'id');
        
        $shipping_mode = ShippingMode::where('shipping_mode.business_id', $business_id)
            ->select('shipping_mode','id')->get()->pluck('shipping_mode','id');
            
        $shipping_agents = ShippingAgent::where('shipping_agents.business_id', $business_id)
            ->select('name','id')->get()->pluck('name','id');
            
        
        $shipping_delivery = ShippingDelivery::where('shipping_delivery.business_id', $business_id)
            ->select('shipping_delivery','id')
            ->get()->pluck('shipping_delivery','id');
            
        $shipping_delivery_days = ShippingDeliveryDays::where('shipping_delivery_days.business_id', $business_id)
            ->select('days')
            ->get()->pluck('days','days');
        
        $prefix = ShippingPrefix::where('shipping_prefix.business_id', $business_id)->latest()->first();
        $latest_shipment = Shipment::where('business_id', $business_id)->latest()->first();
        $current = 0;
        if(!empty($latest_shipment)){
            $current_arr = explode('-',$latest_shipment->tracking_no);
            if(!empty($current_arr)){
                $current = $current_arr[sizeof($current_arr) - 1];
            }
        }
    
        $pre = "";
        if(!empty($prefix)){
            $current = $prefix->starting_no > $current ? $prefix->starting_no : $current;
            $pre = $prefix->prefix;
        }
        
        $tracking_no = $pre."-".($current + 1);
            
        $package = ShippingPackage::where('shipping_packages.business_id', $business_id)
            ->select('package_name','id')
            ->get()->pluck('package_name','id');
            
        $credit_days = ShippingCreditDay::where('shipping_credit_days.business_id', $business_id)
            ->select('credit_days')
            ->get()->pluck('credit_days','credit_days');
        
        $shipping_accounts = ShippingAccount::where('business_id', $business_id)->first();
        
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $expense_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);

        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');
        
        $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_type_id)->pluck('name', 'id');

            
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $recipients = ShippingRecipient::where('business_id',$business_id)->select('name','id')->pluck('name','id');
        $shipping_partner = ShippingPartner::where('business_id',$business_id)->select('name','id')->pluck('name','id');
        $drivers = Driver::where('shipping_drivers.business_id', $business_id)
            ->select('shipping_drivers.*')
            ->get()->pluck('driver_name','id');
            
        $prices = ShippingPrice::leftJoin('shipping_mode', 'shipping_prices.shipping_mode', 'shipping_mode.id')
                ->where('shipping_prices.business_id', $business_id)
                ->select(['shipping_prices.id', DB::raw('CONCAT(shipping_prices.shipping_partner, " | ", ROUND(shipping_prices.per_kg, 2), " | ", shipping_mode.shipping_mode) AS name')])->pluck('name','id');
        $payment_types =  $this->productUtil->payment_types(null, true, true, false ,false, true);
        unset($payment_types['credit_purchase']);
        $businessLocations = BusinessLocation::whereBusinessId($business_id)->pluck('name', 'id');
        $view_page=1;
        return view('shipping::add_shipment.show')->with(
            compact(
                'payment_types',
                'shipping_types',
                'shipping_status',
                'shipping_delivery',
                'shipping_delivery_days',
                'shipping_mode',
                'prefix',
                'package',
                'credit_days',
                'shipping_accounts',
                'income_accounts',
                'expense_accounts',
                'data',
                'tracking_no',
                'shipping_agents',
                'customers',
                'recipients',
                'shipping_partner',
                'drivers',
                'prices',
                'businessLocations',
                'view_page'
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
       $business_id = request()
            ->session()
            ->get('business.id');

        $data = Shipment::leftjoin('users', 'shipments.created_by', 'users.id')
                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('shipping_agents', 'shipments.agent_id', 'shipping_agents.id')
                ->leftjoin('shipping_recipients', 'shipments.recipient_id', 'shipping_recipients.id')
                ->leftjoin('shipping_mode', 'shipments.shipping_mode', 'shipping_mode.id')
                ->leftjoin('shipment_packages', 'shipment_packages.shipment_id', 'shipments.id')
                ->leftjoin('shipping_packages', 'shipments.package_type_id', 'shipping_packages.id')
                ->leftjoin('shipping_delivery', 'shipments.schedule_id', 'shipping_delivery.id')
                ->leftjoin('shipping_partners', 'shipments.shipping_partner', 'shipping_partners.id')
                ->leftjoin('shipping_status', 'shipments.delivery_status', 'shipping_status.id')
                ->leftjoin('transactions','transactions.id','shipments.transaction_id')
                ->leftjoin('transaction_payments','transactions.id','transaction_payments.transaction_id')
                ->where('shipments.id', $id)
                ->select([
                    'shipments.*',
                    'users.username as created_by',
                    'contacts.name as sender',
                    'contacts.address as address',
                    'contacts.mobile as mobile',
                    'shipping_recipients.address as rec_address',
                    'shipping_recipients.mobile_1 as rec_mobile_1',
                    'shipping_recipients.mobile_2 as rec_mobile_2',
                    'shipping_recipients.land_no as rec_land_no',
                    'shipping_recipients.postal_code as rec_postal_code',
                    'shipping_recipients.landmarks as rec_landmarks',
                    'shipping_agents.name as agent',
                    'shipping_recipients.name as recipient',
                    'shipping_mode.shipping_mode as mode',
                    'shipping_packages.package_name as package',
                    'shipment_packages.fixed_price as fixed_price_value',
                    'shipment_packages.package_description as package_description',
                    'shipment_packages.length as length',
                    'shipment_packages.width as width',
                    'shipment_packages.height as height',
                    'shipment_packages.weight as weight',
                    'shipment_packages.rate_per_kg as rate_per_kg',
                    'shipment_packages.volumetric_weight as volumetric_weight',
                    'shipment_packages.price_type as price_type',
                    'shipment_packages.shipping_charge as shipping_charge',
                    'shipment_packages.declared_value as declared_value',
                    'shipment_packages.service_fee as service_fee',
                    'shipping_delivery.shipping_delivery as delivery',
                    'shipping_partners.name as partner',
                    'shipping_status.shipping_status as status',
                    'transaction_payments.method as t_method',
                    'transaction_payments.card_transaction_number as t_card_transaction_number',
                    'transaction_payments.note as t_note',
                    'transaction_payments.account_id as t_account_id',
                    'transaction_payments.post_dated_cheque as t_post_dated_cheque',
                    'transaction_payments.cheque_number as t_cheque_number',
                    'transaction_payments.cheque_date as t_cheque_date',
                    'transaction_payments.bank_name as t_bank_name',
                    'transaction_payments.id as t_id',
                    DB::raw('(SELECT SUM(transaction_payments.amount) FROM transaction_payments WHERE
                        transaction_payments.transaction_id=transactions.id) as total_paid')
                ])->get();   
        //$data = Shipment::find($id);    

        

        $types = Type::leftjoin('users', 'types.created_by', 'users.id')
            ->where('types.business_id', $business_id)
            ->select('users.username as added_by', 'types.*')
            ->get();
        $shipping_types = $types->pluck('shipping_types', 'id');
        
        $status = ShippingStatus::where('shipping_status.business_id', $business_id)
            ->select('shipping_status.*')
            ->get();
        $shipping_status = $status->pluck('shipping_status', 'id');
        
        $shipping_mode = ShippingMode::where('shipping_mode.business_id', $business_id)
            ->select('shipping_mode','id')->get()->pluck('shipping_mode','id');
            
        $shipping_agents = ShippingAgent::where('shipping_agents.business_id', $business_id)
            ->select('name','id')->get()->pluck('name','id');
            
        
        $shipping_delivery = ShippingDelivery::where('shipping_delivery.business_id', $business_id)
            ->select('shipping_delivery','id')
            ->get()->pluck('shipping_delivery','id');
            
        $shipping_delivery_days = ShippingDeliveryDays::where('shipping_delivery_days.business_id', $business_id)
            ->select('days')
            ->get()->pluck('days','days');
        
        $prefix = ShippingPrefix::where('shipping_prefix.business_id', $business_id)->latest()->first();
        $latest_shipment = Shipment::where('business_id', $business_id)->latest()->first();
        $current = 0;
        if(!empty($latest_shipment)){
            $current_arr = explode('-',$latest_shipment->tracking_no);
            if(!empty($current_arr)){
                $current = $current_arr[sizeof($current_arr) - 1];
            }
        }
    
        $pre = "";
        if(!empty($prefix)){
            $current = $prefix->starting_no > $current ? $prefix->starting_no : $current;
            $pre = $prefix->prefix;
        }
        
        $tracking_no = $pre."-".($current + 1);
            
        $package = ShippingPackage::where('shipping_packages.business_id', $business_id)
            ->select('package_name','id')
            ->get()->pluck('package_name','id');
            
        $credit_days = ShippingCreditDay::where('shipping_credit_days.business_id', $business_id)
            ->select('credit_days')
            ->get()->pluck('credit_days','credit_days');
        
        $shipping_accounts = ShippingAccount::where('business_id', $business_id)->first();
        
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $expense_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);

        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');
        
        $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_type_id)->pluck('name', 'id');

            
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $recipients = ShippingRecipient::where('business_id',$business_id)->select('name','id')->pluck('name','id');
        $shipping_partner = ShippingPartner::where('business_id',$business_id)->select('name','id')->pluck('name','id');
        $drivers = Driver::where('shipping_drivers.business_id', $business_id)
            ->select('shipping_drivers.*')
            ->get()->pluck('driver_name','id');
            
        $prices = ShippingPrice::leftJoin('shipping_mode', 'shipping_prices.shipping_mode', 'shipping_mode.id')
                ->where('shipping_prices.business_id', $business_id)
                ->select(['shipping_prices.id', DB::raw('CONCAT(shipping_prices.shipping_partner, " | ", ROUND(shipping_prices.per_kg, 2), " | ", shipping_mode.shipping_mode) AS name')])->pluck('name','id');
        $payment_types =  $this->productUtil->payment_types(null, true, true, false ,false, true);
        unset($payment_types['credit_purchase']);
        $businessLocations = BusinessLocation::whereBusinessId($business_id)->pluck('name', 'id');
        $edit_page=1;
        return view('shipping::add_shipment.edit')->with(
            compact(
                'payment_types',
                'shipping_types',
                'shipping_status',
                'shipping_delivery',
                'shipping_delivery_days',
                'shipping_mode',
                'prefix',
                'package',
                'credit_days',
                'shipping_accounts',
                'income_accounts',
                'expense_accounts',
                'data',
                'tracking_no',
                'shipping_agents',
                'customers',
                'recipients',
                'shipping_partner',
                'drivers',
                'prices',
                'businessLocations',
                'edit_page'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $shipment_data = $request->only('tracking_no','agent_no','customer_id','recipient_id','shipping_mode','delivery_time','shipping_partner','location_id');
            $shipment_data['business_id'] = $business_id;
            $shipment_data['operation_date'] = $this->commonUtil->uf_date($request->date);
            $shipment_data['package_type_id'] = $request->shipping_package;
            $shipment_data['schedule_id'] = $request->shipping_delivery;
            $shipment_data['delivery_status'] = $request->shipping_status;
            $shipment_data['driver_id'] = $request->drivers;
            $shipment_data['total'] = $request->total;
            $shipment_data['agent_id'] = $request->agent_no;
            $shipment_data['created_by'] = auth()->user()->id;
            
            $shipmentDetails = Shipment::find($id)->first();
           

            $shipmentDetails->update($shipment_data);
            
            
            $transaction_data = [];
            $transaction_data['invoice_no'] = $shipment_data['tracking_no'];
            $transaction_data['ref_no'] = $id;
            $transaction_data['status'] = 'final';
            $transaction_data['contact_id'] = $shipment_data['customer_id'];
            $transaction_data['total_before_tax'] = $request->total_payable;
            $transaction_data['final_total'] = $request->total_payable;
            
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = auth()->user()->id;
            $transaction_data['type'] = 'shipment';
            $transaction_data['payment_status'] = 'due';
            
            $transaction_data['transaction_date'] = $shipment_data['operation_date'];                 
            
            $payments = $request->payment;

            $transaction = Transaction::find($shipmentDetails->transaction_id)->first();
            $transaction->update($transaction_data);

            //add payment for transaction
            foreach ($payments as $payment) {            

                //Check if transaction_sell_lines_id is set.

                $payment_mehod = $payment['method'];

                if (!empty($payment['payment_id'])) {

                    $payment['card_type'] = $payment['account_id'];

                    $this->transactionUtil->editPaymentLine($payment, $transaction,true);

                } 
            }
            $this->transactionUtil->updatePaymentStatus($shipmentDetails->transaction_id, $request->total_payable);
            
            
            $cheque_nos = "";
            if(!empty($request->select_cheques)){
                foreach ($request->select_cheques as $select_cheque) {
                    if (!empty($select_cheque)) {
                        $account_transaction = AccountTransaction::find($select_cheque);
                        
                        $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);
                        
                        if (!empty($transaction_payment)) {
                            $amount = $this->transactionUtil->num_uf($account_transaction->amount);
                            if (!empty($amount)) {
                                $credit_data = [
                                    'amount' => $amount,
                                    'account_id' => $account_transaction->account_id,
                                    'transaction_id' => $transaction->id,
                                    'type' => 'credit',
                                    'sub_type' => null,
                                    'operation_date' => $transaction_data['transaction_date'],
                                    'created_by' => session()->get('user.id'),
                                    'transaction_payment_id' => $transaction_payment->id,
                                    'note' => null,
                                    'attachment' => null
                                ];
                                $credit = AccountTransaction::where('transaction_id' , $shipmentDetails->transaction_id)->update($credit_data);
                                
                                $cheque_nos .= !empty($transaction_payment->cheque_number) ? $transaction_payment->cheque_number."," : "";
                                
                                $transaction_payment->is_deposited = 1;
                                $transaction_payment->save();
                            }
                        }
                    }
                }
            }
            

            $package_data = array('shipment_id' => $id,'package_name' => $request->package_name,'package_description' => $request->package_description,
                    'length'=> $request->length_cm,'width'=> $request->width_cm,'height'=> $request->height_cm,'weight'=> $request->weight_cm,
                    'rate_per_kg' => $request->per_kg,'volumetric_weight' => $request->volumetric_weight,'price_type' => $request->price_type,'shipping_charge' => $request->shipping_charge,
                    'declared_value' => $request->declared_value, 'service_fee' => $request->service_fee,   'total' => $request->total,    'fixed_price' => $request->fixed_price_value
                );
                
            ShipmentPackage::where('shipment_id' , $id)->update($package_data);

            $output = [
                'success' => true,
                'tab' => 'agent_details',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            DB::rollBack();
            $output = [
                'success' => false,
                'tab' => 'agent_details',
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function createShipmentBarCode(Request $request)
    {
        // Your shipment creation logic

        // Generate barcode for the shipment
        $trackingNo = $request->input('tracking_no');
        $shipmentBarcode = new DNS1D();
        $shipmentBarcode->setStorPath(__DIR__ . "/cache/");
        //$barcodePNG = $shipmentBarcode->getBarcodePNG($trackingNo, 'C39+');
         $barCode = $shipmentBarcode->getBarcodeSVG($trackingNo, 'C39+');
        //echo '<img src="data:image/png;base64,' . $barcodePNG . '" alt="barcode"   />';

        // Save the barcode image or do whatever you want with it
        // For example, save it in public directory
        //file_put_contents(public_path('barcodes/shipment_barcode.png'), $barcodePNG);

        // Generate QR code for the shipment
        $shipmentQR = new DNS2D();
        $shipmentQR->setStorPath(__DIR__ . "/cache/");
        $qrCode = $shipmentQR->getBarcodePNG($trackingNo, 'QRCODE');
        //echo $qrPNG = $shipmentQR->getBarcodeSVG('shm3--34', 'QRCODE');


        return view('shipping::shipping.shipping_scan_code')->with(
            compact(
                'qrCode',
                'barCode'
            )
        );


        // Save the QR code image or handle it accordingly
        //file_put_contents(public_path('qrcodes/shipment_qr.png'), $qrPNG);

        // Rest of your shipment creation logic
    }

    function printTermCondition(){
        return view('shipping::add_shipment_sw.terms_condition');
    }
}
