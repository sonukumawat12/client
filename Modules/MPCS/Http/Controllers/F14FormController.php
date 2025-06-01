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
use Modules\MPCS\Entities\Mpcs15FormDetails;
use Modules\MPCS\Entities\FormF15TransactionData; 
use Modules\MPCS\Entities\FormF15Header; 
use App\Contact;
use App\Transaction;
class F14FormController extends Controller
{ 
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $util;
 
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $util)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->util = $util;
    }

    public function index(Request $request) {
        if (!auth()->check()) {
            return redirect()->route('login');
        } 
      
        $business_id = session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);

   
        $start_date = $request->start_date ?? now()->subMonth()->format('Y-m-d');
        $end_date = $request->end_date ?? now()->format('Y-m-d');
        $location_id = $request->location_id;
        $quantity_precision = $request->quantity_precision ?? 2;
        $currency_precision = $request->currency_precision ?? 2;

        $query = Transaction::leftjoin('settlement_credit_sale_payments', 'transactions.credit_sale_id', '=', 'settlement_credit_sale_payments.id')
            ->leftjoin('products', 'settlement_credit_sale_payments.product_id', '=', 'products.id')
            ->leftjoin('variations', 'products.id', '=', 'variations.product_id')
            ->leftjoin('contacts', 'settlement_credit_sale_payments.customer_id', '=', 'contacts.id')
            ->leftjoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.is_credit_sale', 1)  
            ->whereBetween('transactions.transaction_date', [$start_date, $end_date])
            ->select(
                'transactions.transaction_date as settlement_date',
                'transactions.final_total',
                'products.name as description',
                'settlement_credit_sale_payments.qty as balance_qty',
                'settlement_credit_sale_payments.order_date as order_date',
                'settlement_credit_sale_payments.price as unit_price',
                'variations.sell_price_inc_tax as sell_price_inc_tax',
                'transactions.ref_no as our_ref',
                'transactions.invoice_no',
                'contacts.name as customer',
                'settlement_credit_sale_payments.order_number as order_no',
                'transactions.location_id',
                'business_locations.name as location',
                'business_locations.mobile as tel'
            );


        if ($location_id) {
            $query->where('transactions.location_id', $location_id);
        }


        $credit_sales = $query->orderBy('transactions.transaction_date', 'desc')->get();

        $credit_sales->transform(function ($item) use ($quantity_precision, $currency_precision) {
            dd($item);
            $is_fuel_product = $item->description == 'Fuel';
            $item->balance_qty = $this->productUtil->num_f($item->balance_qty, $is_fuel_product ? 3 : $quantity_precision, false, true);
            $item->final_total = $this->productUtil->num_f($item->final_total, $currency_precision ? 3 : $currency_precision, false, false);
            return $item;
        });


        return view('mpcs::forms.F14b.index', compact('credit_sales', 'business_locations'));
    
    }
    
 
}
