<?php
namespace App\Http\Controllers;
use App\Contact;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Utils\ContactUtil;


;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactSummaryController extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $businessUtil;
    protected $productUtil;
    protected $contactUtil;
    //protected $balance_duen;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil,
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        BusinessUtil $businessUtil,
        ProductUtil $productUtil,
        ContactUtil $contactUtil
        //balance_duen $GLOBALS
    ) {

        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->contactUtil = $contactUtil;
        //$this->balance_duen =& $GLOBALS;
    }

    public function generateDateArray($startDate, $endDate)
    {
        $dates = [];
    
        $start = \Carbon::parse($startDate);
        $end = \Carbon::parse($endDate);
        
        // Loop through each day and add it to the array
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dates[] = $date->toDateString();
        }
    
        return $dates;
    }
    
    public function index(){
        $contact_types = array(
            'customer' => __('lang_v1.customer'),
            'supplier' => __('lang_v1.supplier')
        );
        return view('contact_summary.index')->with(compact(
            'contact_types'
        ));
    }
    
    public function getLedger(){
        $business_id = request()->session()->get('user.business_id');
        
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $contact_type = request()->contact_type;
        $contact_id = request()->contact_id;
        
        $date_array = $this->generateDateArray($start_date, $end_date);
        
        $contacts = Contact::where('business_id',$business_id);
        if(!empty($contact_type)){
            $contacts->where('type',$contact_type);
        }
        
        if(!empty($contact_id)){
            $contacts->where('id',$contact_id);
        }
        
        
        
        $ledger_details = [];
        foreach($date_array as $date){
            foreach($contacts->get() as $contact){
                
                $bf = $this->contactUtil->getContactSummaryBf($contact->id,$business_id,$date);
                $total = $this->contactUtil->getContactSummaryLedger($contact->id,$business_id,$date);
                $balance = $bf + $total['total_in'] - $total['total_out'];
                
                $ledger_details[] = array('date' => $date, 'contact_type' => $contact->type,'contact_name' => $contact->name, 'bf_balance' => $bf, 'total_in' => $total['total_in'], 'total_out' => $total['total_out'],'balance' => $balance);
            }
        }
        
        $for_pdf = false;
        
        return view('contact_summary.ledger_new')->with(compact(
            'for_pdf','start_date','end_date','ledger_details','contact_type'
        ));
    }
    
    public function getContact($type = null){
        $business_id = request()->session()->get('user.business_id');
        
        if($type == 'customer'){
            $data = Contact::customersDropdown($business_id, false);
        }else if($type == 'supplier'){
            $data = Contact::suppliersDropdown($business_id, false);
        }else{
            $data = Contact::contactDropdown($business_id.false);
        }
        
        return $data;
    }
}