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
class NewF14FormController extends Controller
{ 
    const FUEL_QTY_DECIMALS = 3;
    public function index()
        {if (!auth()->check()) {
            return redirect()->route('login');
        } 
        $business_id = request()->session()->get('business.id');
        $business = Business::where('id', $business_id)->first();
        $setting = MpcsFormSetting::where('business_id', $business_id)->first();
        $business_locations = BusinessLocation::forDropdown($business_id)->toArray();
        $businesslocationkeys = array_keys($business_locations);
        $default_business_location = isset($businesslocationkeys[0])?$businesslocationkeys[0]:'';
        $fuel_qty_decimals = self::FUEL_QTY_DECIMALS;
        
        $startdate = date('Y-m-01');
        $enddate = date('Y-m-t');

        return view('mpcs::forms.F14')->with(compact(
            'business_locations',
            'setting',
            'business',
            'business_id',
            'fuel_qty_decimals',
            'startdate',
            'enddate',
            'default_business_location'
        ));
    }
    
    public function getForm14(){
        
        $business_id = request()->session()->get('user.business_id');
        
        $startdate = date('Y-m-d',strtotime('-1 day'));
        $enddate = date('Y-m-d');
        
        if (request()->has('date_range')) {
            $range = explode(' to ',request()->query('date_range'));
            if(count($range) == 2){
                $startdate = $range[0];
                $enddate = $range[1];
            }
        }
        
        $query = Transaction::leftjoin('settlement_credit_sale_payments', 'transactions.credit_sale_id', 'settlement_credit_sale_payments.id')
            ->leftjoin('products', 'settlement_credit_sale_payments.product_id', 'products.id')
            ->leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('contacts', 'settlement_credit_sale_payments.customer_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('settlements', 'settlement_credit_sale_payments.settlement_no', 'settlements.id')
            ->where('transactions.is_credit_sale', true)
            ->where('settlement_credit_sale_payments.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '>=', $startdate)
            ->whereDate('transactions.transaction_date', '<=', $enddate)
            ->select(
                'transactions.transaction_date as settlement_date',
                'transactions.final_total',
                'transactions.is_credit_sale',
                'products.name as description',
                'products.category_id as category',
                'settlement_credit_sale_payments.qty as balance_qty',
                'settlement_credit_sale_payments.order_date as order_date',
                'settlement_credit_sale_payments.price as unit_price',
                'variations.sell_price_inc_tax as sell_price_inc_tax',
                'transactions.ref_no as our_ref',
                'transactions.invoice_no',
                'contacts.name as customer',
                'settlement_credit_sale_payments.order_number as order_no',
                'settlements.settlement_no as sattlement_no',
                'settlement_credit_sale_payments.customer_reference as customer_reference',
                'business.name as comapany',
                'business.quantity_precision as quantity_precision',
                'business.currency_precision as currency_precision',
                'business_locations.mobile as tel'
            );
        if (request()->has('business_location_id')) {
            $query->where('transactions.location_id', request()->query('business_location_id'));
            
        }
        
        $fuelSubCategory = Category::subCategoryOnlyFuel($business_id)->pluck('id')->toArray();
        $fuelSubCategory[] = Category::where('categories.name', 'Fuel')->first()->id;
        
        $credit_sales = $query->orderBy('settlements.id', 'desc')->get();
        
        foreach($credit_sales as &$sale){
            $sale->is_fuel = in_array($sale->category,$fuelSubCategory);
            $sale->date = date('m/d/Y',strtotime($sale->settlement_date));
        }
        
        return $credit_sales->groupBy('date');
    }
}
?>