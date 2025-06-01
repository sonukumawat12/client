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


class ShippingReceiptController extends Controller
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

    public function printreceipt()
    {
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $contact_id = request()->input('contact_id');

        $start_date = request()->start_date;
        $end_date =  request()->end_date;
        
        $contact = Contact::find($contact_id);
        
        $business_details = $this->businessUtil->getDetails($contact->business_id);
        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        
        $ledger_details['opening_balance'] = 0;
        
        if($contact->type == 'customer'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id,$business_id,$start_date);
            $ledger_transactions = $this->contactUtil->getCustomerLedger($contact_id,$business_id,$start_date,$end_date);
            
            if (request()->input('action') == 'pdf') {
                $for_pdf = true;
                $html = view('contact.ledger_new')
                    ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details','start_date','end_date'))->render();
                $mpdf = $this->getMpdf();
                $mpdf->WriteHTML($html);
                $mpdf->Output();
            }
            if (request()->input('action') == 'print') {
                $for_pdf = true;
                return view('contact.ledger_new')
                    ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details','start_date','end_date'))->render();
            }
            return view('contact.ledger_new')
                ->with(compact('ledger_details', 'contact',  'ledger_transactions', 'business_details', 'location_details','start_date','end_date'));
        }
        
        if($contact->type == 'supplier'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id,$business_id,$start_date);
            $ledger_transactions = $this->contactUtil->getSupplierLedger($contact_id,$business_id,$start_date,$end_date);
            
            if (request()->input('action') == 'pdf') {
                $for_pdf = true;
                $html = view('contact.ledger_new_supplier')
                    ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details','start_date','end_date'))->render();
                $mpdf = $this->getMpdf();
                $mpdf->WriteHTML($html);
                $mpdf->Output();
            }
            if (request()->input('action') == 'print') {
                $for_pdf = true;
                return view('contact.ledger_new_supplier')
                    ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details','start_date','end_date'))->render();
            }
            return view('contact.ledger_new_supplier')
                ->with(compact('ledger_details', 'contact',  'ledger_transactions', 'business_details', 'location_details','start_date','end_date'));
        }
            
    }

}