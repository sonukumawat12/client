<?php

namespace Modules\MPCS\Http\Controllers;

use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\Store;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\MPCS\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\MPCS\Entities\FormF16Detail;
use Modules\MPCS\Entities\FormF17Detail;
use Modules\MPCS\Entities\FormF17Header;
use Modules\MPCS\Entities\FormF17HeaderController;
use Modules\MPCS\Entities\FormF22Header;
use App\Contact;
use App\Transaction;
class Form9CCashController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $util;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $util)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->util = $util;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
   
    
    
    public function index(Request $request)
{
       
        if (!auth()->check()) {
            return redirect()->route('login');
        } 
    $business_id = request()->session()->get('business.id');
    $settings = MpcsFormSetting::where('business_id', $business_id)->first();
   $dateRange = $request->input('form_16a_date_range');
    if ($dateRange) {
        $dates = explode(' - ', $dateRange);
        $start_date = $dates[0];
        $end_date = $dates[1];
    } else {
        // Set default date range if the request parameter is empty
        $start_date = Carbon::now()->subDays(7)->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
    }

    if (!empty($settings)) {
        $F16a_from_no = $settings->F16A_form_sn;
    } else {
        $F16a_from_no = 1;
    }

    $suppliers = Contact::suppliersDropdown($business_id, false);
    $business_locations = BusinessLocation::forDropdown($business_id);
    $invoiceno = FormF16Detail::pluck('invoice_no')->toArray();
    $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();
     
    $setting = MpcsFormSetting::where('business_id', $business_id)->first();
    $max_form_no = FormF16Detail::max('form_no');
    $all_form_no = FormF16Detail::pluck('form_no')->toArray();
    $max_form_nos = $max_form_no + 1;
         
    if ($max_form_nos >= $F16a_from_no) {
        $F16a_from_no = $max_form_nos;
    }

    $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
        ->join(
            'business_locations AS BS',
            'transactions.location_id',
            '=',
            'BS.id'
        )
        ->leftJoin(
            'transaction_payments AS TP',
            'transactions.id',
            '=',
            'TP.transaction_id'
        )
        ->leftJoin(
            'transactions AS PR',
            'transactions.id',
            '=',
            'PR.return_parent_id'
        )
        ->leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
        ->leftjoin('products', 'purchase_lines.product_id', 'products.id')
        ->leftjoin('variations', 'products.id', 'variations.product_id')
        ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
        ->where('transactions.business_id', $business_id)
        ->where('transactions.status', 'received')
        ->select(
            'transactions.id',
            'transactions.invoice_no as invoice',
            'transactions.ref_no as reference_no',
            'purchase_lines.quantity as received_qty',
            'purchase_lines.purchase_price as unit_purchase_price',
            'transactions.final_total as total_purchase_price',
            'BS.name as location',
            'contacts.name as name',
            'transactions.updated_at as date',
            'products.name as product',
            'products.id as product_id',
            'variations.default_sell_price',
            'transactions.pay_term_number',
            'transactions.pay_term_type',
            'PR.id as return_transaction_id',
            DB::raw('SUM(TP.amount) as amount_paid'),
            DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                    TP2.transaction_id=PR.id ) as return_paid'),
            DB::raw('COUNT(PR.id) as return_exists'),
            DB::raw('COALESCE(PR.final_total, 0) as amount_return'),
            DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
        )
        ->groupBy('transactions.id')
            ->get()
            ->toArray();
           $lastRecord = end($purchases);
    
 $name = $lastRecord['name'];
  $invoice = $lastRecord['invoice']; 
 
  //$form_f16a = FormF16Detail::where('transaction_id', $lastRecord['id'])->first();
    return view('mpcs::forms.f9c_cash')->with(compact(
        'business_locations',
        'F16a_from_no',
        'sub_categories',
        'setting',
        'suppliers',
        'invoiceno',
        'all_form_no',
        'lastRecord',
        'name',
        'invoice'
    ));
}

 public function store(Request $request){
     
      $request->validate([
        'form_starting_number' => 'required|string',
        'previous_note_amount' => 'required|numeric',
    ]);

    Form9CSetting::create([
        'form_starting_number' => $request->input('starting_number'),
        'previous_note_amount' => $request->input('previous_note_amount'),
        'user_id' => auth()->id(),
        'created_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Settings saved successfully!');
 }
  

 
}
