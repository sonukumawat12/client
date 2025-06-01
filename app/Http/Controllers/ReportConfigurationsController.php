<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\ReportConfiguration;

class ReportConfigurationsController extends Controller
{
    public function index(){
        $business_id = request()->session()->get('user.business_id');
        
        $customer_statement = ReportConfiguration::where('business_id',$business_id)->where('name','customer_statement_report')->first();
        $customer_statement_report = !empty($customer_statement) ? json_decode($customer_statement->configurations,true) : [];
        
        return view('reports_configurations.settings',compact('customer_statement_report'));
    }
    
    public function store(Request $request){
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = request()->session()->get('user.business_id');
            $cs_report = $request->customer_statement_report;
            $customer_statement_report = [
                    'date' => !empty($cs_report['date']) ? $cs_report['date'] : '0',
                    'location' => !empty($cs_report['location']) ? $cs_report['location'] : '0',
                    'invoice_no' => !empty($cs_report['invoice_no']) ? $cs_report['invoice_no'] : '0',
                    'route' => !empty($cs_report['route']) ? $cs_report['route'] : '0',
                    'vehicle' => !empty($cs_report['vehicle']) ? $cs_report['vehicle'] : '0',
                    'customer_reference' => !empty($cs_report['customer_reference']) ? $cs_report['customer_reference'] : '0',
                    'customer_po' => !empty($cs_report['customer_po']) ? $cs_report['customer_po'] : '0',
                    'voucher_date' => !empty($cs_report['voucher_date']) ? $cs_report['voucher_date'] : '0',
                    'product' => !empty($cs_report['product']) ? $cs_report['product'] : '0',
                    'qty' => !empty($cs_report['qty']) ? $cs_report['qty'] : '0',
                    'unit_price' => !empty($cs_report['unit_price']) ? $cs_report['unit_price'] : '0',
                    'invoice_amount' => !empty($cs_report['invoice_amount']) ? $cs_report['invoice_amount'] : '0',
                    'due_amount' => !empty($cs_report['due_amount']) ? $cs_report['due_amount'] : '0',
            ];
            
            ReportConfiguration::updateOrCreate(['business_id' => $business_id,'name' => 'customer_statement_report'],['configurations' => json_encode($customer_statement_report)]);
            
            $output = [
                'success' => 1,
                'msg' => __('business.settings_updated_success')
            ];
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        
        return redirect()->back()->with('status', $output);
        
        
    }

}
