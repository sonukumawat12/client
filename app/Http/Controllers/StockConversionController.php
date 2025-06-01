<?php
namespace App\Http\Controllers;
use App\Account;
use App\AccountType;
use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\ContactLedger;
use App\Customer;
use App\Media;
use App\PurchaseLine;
use App\ContactGroup;
use App\CustomerReference;
use App\Product;
use App\System;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\UserContactAccess;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Utils\ContactUtil;
use App\NotificationTemplate;
use App\Unit;
use App\Store;
;
use Illuminate\Support\Facades\DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\DNS1D;
use Milon\Barcode\Facades\DNS1DFacade;
use Yajra\DataTables\Facades\DataTables;
use App\new_vehicle;
use App\ContactLinkedAccount;
use Maatwebsite\Excel\Facades\Excel as MatExcel;
use App\Exports\ContactOpeningBalanceExport;
use App\StockConversion;
 
class StockConversionController extends Controller
{
   
    //protected $balance_duen;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        
        //balance_duen $GLOBALS
    ) {

         
        //$this->balance_duen =& $GLOBALS;
    }
   

   
  
 


    public function index()
    {
 

       
          $name='';
            $contact_fields='';
            $is_property='';
            $contact_id='';
        $type = 'supplier';//request()->get('type');
         $payment_status = ['partial' => 'Partial', 'due' => 'Due', 'paid' => 'Paid'];
        $business_id = request()->session()->get('user.business_id');
        $types = ['supplier', 'customer'];
        $business_id = request()->session()->get('user.business_id');
        $units = Unit::forDropdown($business_id, true); //
         $unitsBase = Unit::getPropertyUnitDropdownBase($business_id, true); //
        $stock_list = StockConversion::pluck('conversion_form_no', 'id');
        $stock_Conversion=$stock_list;
        $product = Product::where('business_id', $business_id)->pluck('name', 'id');
       
        $locations =Store::where('business_id', $business_id)->pluck('name', 'id'); 
       //   
        if (request()->ajax()) {
    $route_operations = StockConversion::leftJoin('products', 'products.id', 'stock_conversions.product_convert_from')
        ->select('products.name AS productname','stock_conversions.product_convert_from', 'stock_conversions.id as conversion_id', 'stock_conversions.location', 'stock_conversions.created_at', 'stock_conversions.conversion_form_no', 'stock_conversions.unit_convert_from', 'stock_conversions.unit_convert_to', 'stock_conversions.total_qty_convert_from', 'stock_conversions.product_convert_to', 'stock_conversions.qty_convert_to', 'stock_conversions.updated_at', 'stock_conversions.user');
  

   if (!empty(request()->location) || !empty(request()->conversion_from_no )|| !empty(request()->product_convert_from)) {
    $route_operations->Orwhere('stock_conversions.location', request()->location)
                     ->Orwhere('stock_conversions.conversion_form_no', request()->conversion_from_no)
                     ->Orwhere('stock_conversions.product_convert_from', request()->product_convert_from)
                     ->Orwhere('stock_conversions.product_convert_to', request()->product_convert_to)
                      ->Orwhere('stock_conversions.product_convert_to', request()->product_convert_to);
}  
    $route_operations = $route_operations->get();

    return DataTables::of($route_operations)
        ->addColumn('action', function ($row) {
            $html = '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                    data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-left" role="menu">';
            $html .= '<li><a href="#" class="view_payment_modal"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
            $html .= '<li><a href="#" class="view_payment_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("Edit") . '</a></li>';
            $html .= '<li><a href="#" class="view_payment_modal"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';

            return $html;
        })
        ->rawColumns(['action', 'payment_status', 'method'])
        ->make(true);
}
     // $html .= '<li><a href="' . route('stockconversion.view', ['id' => $row->conversion_id]) . '" class="view_payment_modal"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
            //$html .= '<li><a href="' . route('stockconversion.edit', ['id' => $row->conversion_id]) . '" class="view_payment_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("Edit") . '</a></li>';
          //  $html .= '<li><a href="' . route('stockconversion.delete', ['id' => $row->conversion_id]) . '" class="view_payment_modal"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';

       
        return view('stock_conversion.index', compact('type','name','locations','stock_Conversion','product','units','unitsBase','stock_list'));
    
        
        
    }
    
      public function store(Request $request)
    {
      
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {     
                    $business_id = $request->session()->get('user.business_id');
                    $username = User::where('id', $business_id)->pluck('username')->first();
                   
                    
                    $newQuantityFrom=0.0;
                    $newQuantityTo=0.0;
                   
                    $unitsfrom = Unit::where('id', $request->unit_convert_from)->pluck('actual_name')->first();
                    $unitsto =Unit::where('id', $request->unit_convert_to)->pluck('actual_name')->first();  
                    
                    $location =Store::where('id', $request->location)->pluck('name')->first();   
                DB::beginTransaction();
                    $conversion_form_no = $request->conversion_from_no;
                    $product_convert_from = $request->product_conversion_from;
                    $unit_convert_from = $unitsfrom;
                    $total_qty_convert_from = $request->qty_convert_from;
                    
                    $unit_convert_to = $unitsto;
                    $qty_convert_to = $request->qty_convert_to;
                    $location =$location;
                    $user=$username;
        $product_convert_to=Product::where('id', $request->product_convert_to)->pluck('name')->first();
            if ($product_convert_from!=$product_convert_to)
            {
                  $data = [
           
            
                'conversion_form_no' => $conversion_form_no,
                'product_convert_from' => $product_convert_from,
                'unit_convert_from' => $unit_convert_from,
                'total_qty_convert_from' => $total_qty_convert_from,
                'product_convert_to' => $product_convert_to,
                'unit_convert_to' => $unit_convert_to,
                'qty_convert_to'=> $qty_convert_to,
                'location'=> $location,
                'user'=> $user
        ];
            $datas= StockConversion::create($data);
            
           $purchaseLine = PurchaseLine::where('product_id', $product_convert_from)->first();
            
            if ($purchaseLine) {
               
            $newQuantityFrom = $purchaseLine->quantity - $request->qty_convert_from;
            $purchaseLine->quantity = $newQuantityFrom;
            $purchaseLine->save();
            }
          $purchaseLineto = PurchaseLine::where('product_id', $product_convert_to)->first();
        
        if ($purchaseLineto) {
           
        $newQuantity = $purchaseLineto->quantity + $qty_convert_to;
        $purchaseLineto->quantity = $newQuantity;
        $purchaseLineto->save();
        }
        $output = [
            'success' => true,
            'data' => $datas,
            'msg' => __("Stock Conversion Successfully")
        ];
        
    
          
              DB::commit();
        
            }
      else
      {
           $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong"),
                'error' => $e->getMessage()
            ];
      }

      
           
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong"),
                'error' => $e->getMessage()
            ];
        }
        return $output;
    }
     public function create()
    {
       
       
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        $mode = request()->mode;
        $type = request()->type;
        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not
      $units = Unit::forDropdown($business_id, true);
        $types = [];
            $unitsBase = Unit::getPropertyUnitDropdownBase($business_id, true); //
        $customer_groups = '';//ContactGroup::forDropdown($business_id);
        $supplier_groups ='';// ContactGroup::forDropdown($business_id, true, false, 'supplier');
        $contact_id ='';// $this->businessUtil->check_customer_code($business_id);
        $user_groups ='';// User::forDropdown($business_id);
        $product_convert_to = Product::where('business_id', $business_id)->pluck('name', 'id');
         $locations = Store::where('business_id', $business_id)->pluck('name', 'id');
        if($type == 'customer'){
            $notifications ='';// NotificationTemplate::customerNotifications();
        }else{
            $notifications = '';//NotificationTemplate::supplierNotifications();
        }
     
        return view('stock_conversion.create')
            ->with(compact('notifications','types', 'customer_groups', 'units','locations','unitsBase','product_convert_to','supplier_groups', 'contact_id', 'type','user_groups', 'mode'));
        
            
    }
       public function getStockUnit(Request $request,$id)
    {
            $qty_from=$request->qty_from;
            $qty_to=0.0;
            $purchaseLine = PurchaseLine::select('quantity')
            ->where('product_id', $id)
            ->first();
            
            $product = Product::select('id', 'unit_id')
            ->where('id', $id)
            ->first();
            
            $unit = Unit::select('id','base_unit_id','base_unit_multiplier')
            ->where('id', $product->unit_id)
            ->first();
            
            $qty = $purchaseLine->quantity;
            $unitid= $unit->id;
            $base_unit_id=$unit->base_unit_id;
            $multiplier=$unit->base_unit_multiplier;
            if($unitid!=$base_unit_id && !empty($qty) && !empty($multiplier))
            {
                $qty_to=$qty_from*$multiplier;
                
            }
            
            
            return compact('qty_to', 'unitid','base_unit_id');
       
    }
    
      public function edit($id)
    {
        
    }
    
     public function delete($id)
    {
        
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {     
                     
        
             
        $datas=   StockConversion::where('id', $id)->delete();
        $output = [
            'success' => true,
            'data' => $datas,
            'msg' => __("Deleted Successfully")
        ];
        
    
          
           
        
            
       

      
           
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong"),
                'error' => $e->getMessage()
            ];
        }
        return $output;
    }
      public function view($id)
    {
        
    } 
     
      public function getStockQuantity($id)
    {
        $quantity = PurchaseLine::where('products.id', $id)->first();
             
        if (!empty($quantity)) {
            return $quantity->quantity;
        } else {
            return '0';
        }
    }
  
    
}
