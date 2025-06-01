<?php

namespace Modules\Dsr\Http\Controllers;

use App\BusinessLocation;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Dsr\Entities\DesignatedDsrOfficer;
use Modules\Dsr\Entities\DsrSettings;
use Modules\Dsr\Entities\Province;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\MeterSale;
;
use \App\Product;

class DsrController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('dsr::index');
    }

    public function settings()
    {

        $fuel_products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('categories.name', 'Fuel')
            ->pluck('products.name','products.id')
            ->toArray();
        return view('dsr::settings.index',compact('fuel_products'));
    }
    
    public function getDealers(){
        $area_ids = request()->area_id;
        $dealers = DsrSettings::where(function ($query) use ($area_ids) {
            foreach ($area_ids as $area) {
                $query->orWhereJsonContains('areas', $area);
            }
        })->pluck('dealer_number', 'business_id');
        
        $html = "";
        
        foreach($dealers as $key => $dealer){
            $html .= '<option value="'.$key.'">'.$dealer."</option>";
        }
        
        
        return $html;
    }

    public function report()
    {
        $dsr = DesignatedDsrOfficer::where('officer_username',auth()->user()->username)->first();
        
        if(auth()->user()->hasRole('dsr_officer')){
            $products = [];
        }else{
            $products = DB::table('products')
            ->where('products.business_id',request()->session()->get('user.business_id'))
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('categories.name', 'Fuel')
            ->pluck('products.name','products.id')
            ->toArray();
        }
        
        $country_id = $dsr->country_id ?? 0;
        
        $countries = DB::table('countries')->where('id',$country_id)->pluck('country','id');
        return view('dsr::report.index',
            compact('countries','dsr','products'));
    }
    
    public function fetchReport(){
        if(auth()->user()->hasRole('dsr_officer')){
            $business_ids = request()->dealer_id;
        }else{
            $business_ids = [request()->session()->get('user.business_id')];
        }
        
        $product_id = request()->product_id;
        $product = Product::findOrFail($product_id);
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $dsr_ob = DsrSettings::where('product_id',$product_id)->first();
        
        $acc_sale = 0;
        $acc_purchase = 0;
        $acc_date = date('Y-m-d');
        if(!empty($dsr_ob)){
            $acc_sale = $dsr_ob->accumulative_sale;
            $acc_purchase = $dsr_ob->accumulative_purchase;
            $acc_date = $dsr_ob->date_time;
        }
        
        
        $startDate = \Carbon::parse($start_date);
        $endDate = \Carbon::parse($end_date);
        
        $dates = [];
        
        while ($startDate->lte($endDate)) {
            $dates[] = $startDate->toDateString();
            $startDate->addDay(); // Increment by one day
        }
        
        $pumps = Pump::where('product_id',$product_id)->whereIn('business_id',$business_ids)->get();
        $tanks = FuelTank::where('product_id',$product_id)->whereIn('business_id',$business_ids)->get();
        
        $tdata = array();
        foreach($dates as $date){
            $td['date'] =  $date;
            foreach($pumps as $key => $pump){
                $reading = $this->_getPumpReadings($pump->id,$date);
                $td['pump_'.($key+1)] = !empty($reading) ? number_format($reading,3,".","") : number_format($pump->last_meter_reading,3,".","");
            }
            
            $td['today_sales'] = $this->_getSales($product_id,$date,$business_ids);
            $td['accumulative_sales'] = $this->_getAccumulativeSales($product_id,$date,$business_ids,$acc_date) + $acc_sale;
            $td['today_purchases'] = $this->_getPurchases($product_id,$date,$business_ids);
            $td['accumulative_purchases'] = $this->_getAccumulativePurchases($product_id,$date,$business_ids,$acc_date) + $acc_purchase;
            
            foreach($tanks as $key => $tank){
                $level = $this->_getTankLevel($tank->id,$date);
                $td['tank_'.($key+1)] = !empty($level) ? number_format($level,3,".","") : number_format($tank->current_balance,3,".","")."<br><small class='text-danger'><b>".__('dsr::lang.qty_not_entered')."</b></small>";
            }
            
            $td['testing_qty'] = $this->_getTestingQty($product_id,$date,$business_ids);
            
            $tdata[] = $td;
        }
        
        return view('dsr::report.report',compact('pumps','tdata','product','tanks','dsr_ob'));
    }
    
    public function fetchProducts(){
        if(auth()->user()->hasRole('dsr_officer')){
            $business_ids = request()->dealer_id;
        }else{
            $business_ids = [request()->session()->get('user.business_id')];
        }
        
        $fuel_products = DB::table('products')
            ->whereIn('products.business_id',$business_ids)
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('categories.name', 'Fuel')
            ->pluck('products.name','products.id')
            ->toArray();
        $html = '<option value="">'.__('lang_v1.all')."</option>";
        foreach($fuel_products as $key => $product){
            $html .= '<option value="'.$key.'">'.$product."</option>";
        }
        
        return $html;
    }
    
    function _getPumpReadings($pump_id,$date){
        $query = MeterSale::leftjoin('settlements','meter_sales.settlement_no','settlements.id')
                ->where('meter_sales.pump_id',$pump_id)
                ->whereDate('settlements.transaction_date',$date)
                ->orderBy('meter_sales.id','DESC')->first();
        if(!empty($query)){
            $reading = $query->closing_meter;
        }else{
            $reading = null;
        }
        return $reading;
    }
    
    
    function _getTankLevel($tank_id,$date){
        $query = DB::table('dip_readings')->where('tank_id',$tank_id)->whereRaw("STR_TO_DATE(date_and_time, '%m/%d/%Y') BETWEEN ? AND ?", [$date, $date])->first();
        if(!empty($query)){
            return $query->fuel_balance_dip_reading;
        }else{
            return null;
        }
    }
    
    function _getSales($product_id,$date,$business_ids){

        $query = MeterSale::leftjoin('settlements', 'meter_sales.settlement_no', 'settlements.id')
            ->where('meter_sales.product_id', $product_id)
            ->whereDate('settlements.transaction_date', $date)
            ->whereIn('meter_sales.business_id', $business_ids)
            ->select(DB::raw('SUM(meter_sales.closing_meter - meter_sales.starting_meter) as total_meter_difference'))
            ->first();
        
        $totalMeterDifference = $query->total_meter_difference ?? 0;
        
        return $totalMeterDifference;

    }
    
    function _getAccumulativeSales($product_id,$date,$business_ids,$acc_date){

        $query = MeterSale::leftjoin('settlements', 'meter_sales.settlement_no', 'settlements.id')
            ->where('meter_sales.product_id', $product_id)
            ->whereDate('settlements.transaction_date','<=', $date)
            ->whereDate('settlements.transaction_date','>', $acc_date)
            ->whereIn('meter_sales.business_id', $business_ids)
            ->select(DB::raw('SUM(meter_sales.closing_meter - meter_sales.starting_meter) as total_meter_difference'))
            ->first();
        
        $totalMeterDifference = $query->total_meter_difference ?? 0;
        
        return $totalMeterDifference;

    }
    
    function _getTestingQty($product_id,$date,$business_ids){

        $query = MeterSale::leftjoin('settlements', 'meter_sales.settlement_no', 'settlements.id')
            ->where('meter_sales.product_id', $product_id)
            ->whereDate('settlements.transaction_date', $date)
            ->whereIn('meter_sales.business_id', $business_ids)
            ->sum('meter_sales.testing_qty');
        
        
        return $query;

    }
    
    function _getPurchases($product_id,$date,$business_ids){

        $query = DB::table('tank_purchase_lines')->leftjoin('transactions', 'transactions.id', 'tank_purchase_lines.transaction_id')
            ->where('tank_purchase_lines.product_id', $product_id)
            ->whereDate('transactions.transaction_date', $date)
            ->whereIn('tank_purchase_lines.business_id', $business_ids)->sum('quantity');
        
        return $query;

    }
    
    function _getAccumulativePurchases($product_id,$date,$business_ids,$acc_date){

        $query = DB::table('tank_purchase_lines')->leftjoin('transactions', 'transactions.id', 'tank_purchase_lines.transaction_id')
            ->where('tank_purchase_lines.product_id', $product_id)
            ->whereDate('transactions.transaction_date','<=' ,$date)
            ->whereDate('transactions.transaction_date','>' ,$acc_date)
            ->whereIn('tank_purchase_lines.business_id', $business_ids)->sum('quantity');
        
        return $query;

    }

}
