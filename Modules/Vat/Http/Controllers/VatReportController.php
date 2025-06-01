<?php

namespace Modules\Vat\Http\Controllers;


use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Transaction;

use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Utils\Util;
;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

use Modules\Superadmin\Entities\Subscription;
use Modules\Vat\Entities\VatSetting;


class VatReportController extends Controller
{
    protected $commonUtil;
    protected $contactUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $businessUtil;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil,BusinessUtil $businessUtil, ContactUtil $contactUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {

        $this->commonUtil = $commonUtil;
        $this->contactUtil =  $contactUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil = $businessUtil;
        
    }
    
    public function index(){
        $business_id = request()->session()->get('user.business_id');
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        return view('vat::vat_report.index',compact('customers'));
    }
     
    public function getLedger(Request $request)
    {
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $start_date = request()->start_date;
        $end_date =  request()->end_date;
        
        $subscription = Subscription::active_subscription($business_id);
    	$pacakge_details = $subscription->package_details;
    	
    	$vat_settings = VatSetting::where('business_id', $business_id)->where('status',1)->first();
        
        $start_date = $request->get('start_date');
        
        if(!empty($vat_settings)){
            if(!empty($pacakge_details['vat_effective_date'])){
                if(strtotime($vat_settings->effective_date) > strtotime($pacakge_details['vat_effective_date'])){
                    $pacakge_details['vat_effective_date'] = $vat_settings->effective_date;
                }
                
            }else{
                $pacakge_details['vat_effective_date'] = $vat_settings->effective_date;
            }
            
        }
        
        $effective_date = !empty($pacakge_details['vat_effective_date']) ? $pacakge_details['vat_effective_date'] : $start_date;
        
        if(strtotime($start_date) < strtotime($effective_date)){
            $start_date = $effective_date;
        }
        
        $tax_type = request()->tax_type;
        
        
        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::where('business_id', $business_id)->first();
        
        $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerTaxBf($business_id,$start_date,$effective_date);
        
        $ledger_transactions = $this->contactUtil->getCustomerTaxLedger($business_id,$start_date,$end_date,$effective_date);
        
        return view('vat::vat_report.report_details')
            ->with(compact( 'ledger_details', 'ledger_transactions', 'business_details', 'location_details','start_date','end_date'));
         
    }

}
