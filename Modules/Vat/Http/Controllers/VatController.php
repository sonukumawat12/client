<?php

namespace Modules\Vat\Http\Controllers;


use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Transaction;

use App\Utils\ModuleUtil;
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

use Modules\Vat\Entities\VatInvoiceDetail;
use Modules\Vat\Entities\VatInvoiceDetail2;
use Modules\Vat\Entities\FleetVatInvoice2;
use Modules\Vat\Entities\FleetVatInvoiceDetail2;
use Modules\Vat\Entities\VatInvoice;
use Modules\Vat\Entities\VatInvoice2;
use Modules\Vat\Entities\VatCustomerStatement;
use Modules\Vat\Entities\VatCustomerStatementDetail;
use App\Product;
use App\Category;
use Modules\Fleet\Entities\RouteOperation;


class VatController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $businessUtil;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil,BusinessUtil $businessUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {

        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil = $businessUtil;
        
    }
    
    
    public function updateVats(Request $request){
        $business_id = $request->session()->get('user.business_id');
        $start_date = $request->start_date;
        $end_date_date = $request->end_date_date;
        $transaction_types = $request->transaction_types;
        
        try {
            
            $effective_date = $this->transactionUtil->__getVatEffectiveDate($business_id);
            
            if(strtotime($start_date) < strtotime($effective_date)){
                $output = [
                    'success' => false,
                    'msg' => __('superadmin::lang.you_can_only_generate_invoices_from').$this->transactionUtil->format_date($effective_date)
                ];
                
                return $output;
            }
            
            $expenses = Transaction::whereIn('transactions.type',$transaction_types)->whereDate('transaction_date','>=',$effective_date)->get();
        
            foreach($expenses as $transaction){
                $this->transactionUtil->calculateAndUpdateVAT($transaction);
            }
            

            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
        
    }
    
    public function updateSingleVats(Request $request){
        $business_id = $request->session()->get('user.business_id');
        $transaction_id = $request->transaction_id;
        
        try {
            $transaction = Transaction::findOrFail($transaction_id);
        
        $this->transactionUtil->calculateAndUpdateVAT($transaction);

            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
        
    }
    
    public function getCustomerVatSchedule(Request $request){
        $business_id = $request->session()->get('user.business_id');
        
        if (request()->ajax()) {
            $vat_invoice_schedule = VatInvoice::leftjoin('contacts','contacts.id','vat_invoices.customer_id')
                                                    ->where('vat_invoices.business_id',$business_id)
                                                    ->select('vat_invoices.id','vat_invoices.customer_id as contact_id','vat_invoices.date as date','vat_invoices.customer_bill_no as invoice_no','contacts.vat_number','contacts.name as contact_name','vat_invoices.total_amount as tax_base','vat_invoices.tax_amount as tax_amount',DB::raw('"vat_invoice" as type'))
                                                    ->where('vat_invoices.tax_amount','>',0)
                                                    ->orderBy('vat_invoices.date','DESC');
            
            $vat_invoice_statement = VatCustomerStatement::leftjoin('contacts','contacts.id','vat_customer_statements.customer_id')
                                                    ->where('vat_customer_statements.business_id', $business_id)
                                                    ->select('vat_customer_statements.id','vat_customer_statements.customer_id as contact_id','vat_customer_statements.print_date as date','vat_customer_statements.statement_no as invoice_no','contacts.vat_number','contacts.name as contact_name',DB::raw('"0" as tax_base'),DB::raw('"0" as tax_amount'),DB::raw('"vat_customer_statement" as type'))
                                                    ->orderBy('vat_customer_statements.print_date','DESC');
                                                    
                                                    
            $vat_invoice2_schedule = VatInvoice2::leftjoin('contacts','contacts.id','vat_invoices_2.customer_id')
                                                    ->where('vat_invoices_2.business_id',$business_id)
                                                    ->select('vat_invoices_2.id','vat_invoices_2.customer_id as contact_id','vat_invoices_2.date as date','vat_invoices_2.customer_bill_no as invoice_no','contacts.vat_number','contacts.name as contact_name','vat_invoices_2.total_amount as tax_base','vat_invoices_2.tax_amount as tax_amount',DB::raw('"vat_invoice2" as type'))
                                                    ->where('vat_invoices_2.tax_amount','>',0)
                                                    ->orderBy('vat_invoices_2.date','DESC');
                                                    
            $fleet_vat_invoice2_schedule = FleetVatInvoice2::leftjoin('contacts','contacts.id','fleet_vat_invoices_2.customer_id')
                                                    ->where('fleet_vat_invoices_2.business_id',$business_id)
                                                    ->select('fleet_vat_invoices_2.id','fleet_vat_invoices_2.customer_id as contact_id','fleet_vat_invoices_2.date as date  ','fleet_vat_invoices_2.customer_bill_no as invoice_no','contacts.vat_number','contacts.name as contact_name','fleet_vat_invoices_2.total_amount as tax_base','fleet_vat_invoices_2.tax_amount as tax_amount',DB::raw('"fleet_vat_invoice2" as type'))
                                                    ->where('fleet_vat_invoices_2.tax_amount','>',0)
                                                    ->orderBy('fleet_vat_invoices_2.date','DESC');
                                                    
            
                                
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                                
                $start_date = request()->start_date ?? date('Y-m-01');
        
                $end =  request()->end_date ?? date('Y-m-t');
                
                // vat effective date
                $effective_date = $this->transactionUtil->__getVatEffectiveDate($business_id);
                
                
                if(strtotime($start_date) < strtotime($effective_date)){
                    $start_date = $effective_date;
                }
                
                $vat_invoice_schedule->whereDate('vat_invoices.date', '>=', $start_date)->whereDate('vat_invoices.date','<=',$end);
                $vat_invoice2_schedule->whereDate('vat_invoices_2.date', '>=', $start_date)->whereDate('vat_invoices_2.date','<=',$end);
                $fleet_vat_invoice2_schedule->whereDate('fleet_vat_invoices_2.date', '>=', $start_date)->whereDate('fleet_vat_invoices_2.date','<=',$end);
                $vat_invoice_statement->whereDate('vat_customer_statements.print_date', '>=', $start_date)->whereDate('vat_customer_statements.print_date','<=',$end);
            }
                
            if (!empty(request()->get('contact_id'))) {
                $vat_invoice_schedule->where('customer_id', request()->get('contact_id'));
                $vat_invoice2_schedule->where('customer_id', request()->get('contact_id'));
                $fleet_vat_invoice2_schedule->where('customer_id', request()->get('contact_id'));
                $vat_invoice_statement->where('customer_id', request()->get('contact_id'));
            }
            
                                
                                                   
            $merged_data = $vat_invoice_schedule->union($vat_invoice2_schedule)->union($fleet_vat_invoice2_schedule)->union($vat_invoice_statement)->orderBy('date','DESC');
            
            
            return Datatables::of($merged_data->get())
    
                    ->removeColumn('id')
                    
                    ->editColumn('vat_number',function($row){
                        return explode('-',$row->vat_number)[0];
                    })
                    
                    ->addColumn('product_name',function($row){
                        if($row->type == 'vat_invoice'){
                            $products = VatInvoiceDetail::where('issue_bill_id',$row->id)->pluck('product_id')->toArray() ?? [];
                        }elseif($row->type == 'fleet_vat_invoice2'){
                            $products = FleetVatInvoiceDetail2::where('issue_bill_id',$row->id)->pluck('product_id')->toArray() ?? [];
                            
                            $cats = RouteOperation::whereIn('id',$products)->pluck('invoice_no')->toArray() ?? [];
                            
                            return implode('<br>',$cats);
                            
                        }elseif($row->type == 'vat_customer_statement'){
                            $products = VatCustomerStatementDetail::where('statement_id',$row->id)->pluck('product_id')->toArray() ?? [];
                        }else{
                            $products = VatInvoiceDetail2::where('issue_bill_id',$row->id)->pluck('product_id')->toArray() ?? [];
                        }
                        
                        $categories = Product::whereIn('id',$products)->pluck('category_id')->toArray() ?? [];
                        $cats = Category::whereIn('id',$categories)->pluck('name')->toArray() ?? [];
                        
                        return implode('<br>',$cats);
                    })
    
                    ->editColumn(
    
                        'tax_base',
                        function($row){
                            if($row->type == 'vat_customer_statement'){
                                $row->tax_base = VatCustomerStatementDetail::where('statement_id',$row->id)->sum('invoice_amount') ?? 0;
                                
                                $total =  $row->tax_base;
                                $tax_rate = \App\TaxRate::where('business_id',request()->session()->get('business.id'))->first()->amount ?? 0;
                                $pre_tax = $total / (1+ ($tax_rate/100));
                                $row->tax_amount = ($tax_rate/100) * $pre_tax;
                            }
                            
                            $row->tax_base -= $row->tax_amount;
                                
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="'.$row->tax_base.'">'.$this->transactionUtil->num_uf($this->transactionUtil->num_f($row->tax_base)).'</span>';
                        }
                    )
    
                    ->editColumn(
    
                        'tax_amount',
                        function($row){
                            if($row->type == 'vat_customer_statement'){
                                
                                $total =  VatCustomerStatementDetail::where('statement_id',$row->id)->sum('invoice_amount') ?? 0;
                                $tax_rate = \App\TaxRate::where('business_id',request()->session()->get('business.id'))->first()->amount ?? 0;
                                $pre_tax = $total / (1+ ($tax_rate/100));
                                $row->tax_amount = ($tax_rate/100) * $pre_tax;
                            }
                            
                            return '<span class="display_currency tax-amount" data-currency_symbol="true" data-orig-value="'.$row->tax_amount.'">'.$this->transactionUtil->num_uf($this->transactionUtil->num_f($row->tax_amount)).'</span>';
                        }
                    )
    
                    ->editColumn('date', '{{@format_date($date)}}')
    
                    ->rawColumns(['final_total', 'tax_amount','tax_base','product_name'])
    
                    ->make(true);
        }
        
        $business_locations = BusinessLocation::forDropdown($business_id,true);

        $contacts = Contact::contactDropdown($business_id,false,false);
        

        return view('vat::vat_schedule.reports')->with(compact(

            'business_locations',

            'contacts'

        ));
        
    }
    
    public function getSupplierVatSchedule(Request $request){
        $business_id = $request->session()->get('user.business_id');
        
        if (request()->ajax()) {
            
            $sales_invoices = Transaction::leftjoin('purchase_lines','transactions.id','purchase_lines.transaction_id')
                                ->leftjoin('contacts', 'contacts.id', '=', 'transactions.contact_id')
                                ->leftjoin('products','products.id','purchase_lines.product_id')
                                ->leftjoin('categories','products.category_id','categories.id')
                                ->leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')
                                ->where('transactions.business_id', $business_id)
                                ->where('transactions.tax_amount' ,'>',0)
                                ->where('transactions.tax_id' ,'>',0)
                                ->whereIn('transactions.type',['purchase'])
                                ->select(
                                    'transactions.contact_id',
                                    'transactions.transaction_date as date',
                                    'transactions.invoice_no',
                                    'contacts.vat_number',
                                    'contacts.name as contact_name',
                                    'categories.name as product_name',
                                    \DB::raw('(purchase_lines.purchase_price * purchase_lines.quantity) as tax_base'),
                                    \DB::raw('(purchase_lines.purchase_price * purchase_lines.quantity)*tr.amount/100 as tax_amount')
                                );
                                
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                                
                $start_date = request()->start_date ?? date('Y-m-01');
        
                $end =  request()->end_date ?? date('Y-m-t');
                
                $effective_date = $this->transactionUtil->__getVatEffectiveDate($business_id);
                
                
                if(strtotime($start_date) < strtotime($effective_date)){
                    $start_date = $effective_date;
                }
                
                $sales_invoices->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date','<=',$end);
            }
                
            if (!empty(request()->get('contact_id'))) {
                $sales_invoices->where('transactions.contact_id',request()->get('contact_id'));
            }
            
                                
                                                   
            $merged_data = $sales_invoices->orderBy('date','DESC');
            
            
            return Datatables::of($merged_data->get())
    
                    ->removeColumn('id')
                    
                    ->editColumn('vat_number',function($row){
                        return explode('-',$row->vat_number)[0];
                    })
    
                     ->editColumn(
    
                        'tax_base',
                        function($row){
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="'.$row->tax_base.'">'.$this->transactionUtil->num_uf($this->transactionUtil->num_f($row->tax_base)).'</span>';
                        }
                    )
    
                    ->editColumn(
    
                        'tax_amount',
                        function($row){
                            return '<span class="display_currency tax-amount" data-currency_symbol="true" data-orig-value="'.$row->tax_amount.'">'.$this->transactionUtil->num_uf($this->transactionUtil->num_f($row->tax_amount)).'</span>';
                        }
                    )
    
                    ->editColumn('date', '{{@format_date($date)}}')
    
                    ->rawColumns(['final_total', 'tax_amount','tax_base'])
    
                    ->make(true);
        }
        
    }
    
     
    public function getVatReport(Request $request)
    {

        $business_id = $request->session()->get('user.business_id');
        
        if (request()->ajax()) {
            
           
            $expenses = Transaction::leftjoin('contacts', 'contacts.id', '=', 'transactions.contact_id')

                ->leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')

                ->where('transactions.business_id', $business_id)

                ->where('transactions.tax_amount' ,'>',0)
                
                ->where('transactions.tax_id' ,'>',0)
                
                // ->where(function ($query) {
                //     $query->whereIn('transactions.type', ['sell']);
                //         // ->orWhere('transactions.is_vat' ,1);
                // })
                
                ->whereIn('transactions.type',['sell','purchase','expense'])
                
                ->whereIn('transactions.status',array('received','final'))
                
                ->select(
                    
                    'transactions.*',

                    'contacts.name as contact_name'

                );



            
            
            if (!empty(request()->get('contact_id'))) {
                $expenses->where('transactions.contact_id', request()->get('contact_id'));
            }
            
            if (!empty(request()->get('reference_type'))) {
                $expenses->where('transactions.type', request()->get('reference_type'));
            }
            
            if (request()->has('location_id') && !empty(request()->get('location_id'))) {

                $location_id = request()->get('location_id');

                if (!empty($location_id)) {

                    $expenses->where('transactions.location_id', $location_id);

                }

            }


            //Add condition for start and end date filter, uses in sales representative expense report & list of expense

            if (!empty(request()->start_date) && !empty(request()->end_date)) {

                $start_date = request()->start_date;

                $end =  request()->end_date;
                
                $effective_date = $this->transactionUtil->__getVatEffectiveDate($business_id);
                
                
                if(strtotime($start_date) < strtotime($effective_date)){
                    $start_date = $effective_date;
                }
                
                $expenses->whereDate('transaction_date', '>=', $start_date)

                    ->whereDate('transaction_date', '<=', $end);

            }



            return Datatables::of($expenses)

                ->addColumn(

                    'action',

                    function($row){
                        $html = '<div class="btn-group">

                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
    
                                data-toggle="dropdown" aria-expanded="false"> '. __("messages.actions").'<span class="caret"></span><span class="sr-only">Toggle Dropdown
    
                                    </span>
    
                            </button>
    
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                            if($row->type == 'purchase'){
                                $html .= '<li><a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                                $html .= '<li><a href="#" class="print-invoice" data-href="' . action('PurchaseController@printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                            }
                            
                            if($row->type == 'expense'){
                                $html .= '<li><a href="{{action(\'ExpenseController@edit\', [$id])}}"><i class="fa fa-eye"></i> '.__("messages.view").'</a></li>';
                            }
                            
                            if($row->type == 'sell'){
                                if($row->is_settlement == 1){
                                    
                                    $settlement = DB::table('settlements')->where('settlement_no',$row->invoice_no)->first();
                                    
                                    if(!empty($settlement)){
                                         $html .= '<li><a data-href="' . action("\Modules\Petro\Http\Controllers\SettlementController@show", [$settlement->id]) . '" class="btn-modal" data-container=".settlement_modal"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                                         $html .= '<li><a data-href="' . action("\Modules\Petro\Http\Controllers\SettlementController@print", [$settlement->id]) . '" class="print_settlement_button"><i class="fa fa-print"></i> ' . __("petro::lang.print") . '</a></li>';
                                    }
                                    
                                   
                                }else{
                                    $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                                    $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i> ' . __("messages.print") . '</a></li>';
                                }
                                
                                
                            }
                                
                            
                        $html .= '</ul>
                        </div>';
                        
                        return $html;
                    }

                )

                ->removeColumn('id')

                ->editColumn(

                    'final_total',

                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{empty($deletedBy) ? $final_total : 0}}">{{@num_format($final_total)}}</span>'

                )

                ->editColumn(

                    'tax_amount',

                    '<span class="display_currency tax-amount" data-currency_symbol="true" data-orig-value="{{ $tax_amount }}">{{@num_format($tax_amount)}}</span>'

                )

                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                ->editColumn('ref_no', function ($row) {

                    $ref = $row->invoice_no;
                    
                    if(empty($ref)){
                        $ref = $row->ref_no;
                    }

                    return $ref;

                })
                

                ->rawColumns(['final_total', 'action', 'tax_amount', 'ref_no'])

                ->make(true);

        }
        
        $business_locations = BusinessLocation::forDropdown($business_id,true);

        $contacts = Contact::contactDropdown($business_id,false,false);
        
        $reports = array();
        if($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'range_sale')){
            $reports['sell'] = __('superadmin::lang.sales');
        }
        
        if($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'range_expense')){
            $reports['expense'] = __('superadmin::lang.expenses');
        }
        
        if($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'range_purchase')){
            $reports['purchase'] = __('superadmin::lang.purchase');
        }
        

        return view('vat::vat.reports')->with(compact(

            'business_locations',

            'contacts',
            
            'reports'

        ));
    }
    
    public function printVatReport(Request $request){
        $business_id = $request->session()->get('user.business_id');
        
        $location_details  = BusinessLocation::findOrFail($business_id);
        
        $expenses = Transaction::leftjoin('contacts', 'contacts.id', '=', 'transactions.contact_id')

                ->leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')

                ->where('transactions.business_id', $business_id)

                ->where('transactions.tax_amount' ,'>',0)
                
                // ->where(function ($query) {
                //         $query->whereIn('transactions.type', ['sell'])
                //             ->orWhere('transactions.is_vat' ,1);
                //     })
                
                ->whereIn('transactions.type',['sell','purchase','expense'])
                
                 ->whereIn('transactions.status',array('received','final'))
                
                ->select(
                    
                    'transactions.*',

                    'contacts.name as contact_name'

                );



            
            
            if (!empty(request()->get('contact_id'))) {
                $expenses->where('transactions.contact_id', request()->get('contact_id'));
            }
            
            if (!empty(request()->get('reference_type'))) {
                $expenses->where('transactions.type', request()->get('reference_type'));
            }
            
            if (request()->has('location_id') && !empty(request()->get('location_id'))) {

                $location_id = request()->get('location_id');

                if (!empty($location_id)) {

                    $expenses->where('transactions.location_id', $location_id);

                }

            }
            

            //Add condition for start and end date filter, uses in sales representative expense report & list of expense

            if (!empty(request()->start_date) && !empty(request()->end_date)) {

                $start_date = request()->start_date;

                $end =  request()->end_date;
                
                $effective_date = $this->transactionUtil->__getVatEffectiveDate($business_id);
                
                if(strtotime($start_date) < strtotime($effective_date)){
                    $start_date = $effective_date;
                }
                
                $expenses->whereDate('transaction_date', '>=', $start_date)

                    ->whereDate('transaction_date', '<=', $end);

            }
            
            $start_date = request()->start_date;

            $end_date =  request()->end_date;
            
            $location_id = $request->get('location_id');
            $input_tax = $this->transactionUtil->getInputTax($business_id, $start_date, $end_date, $location_id)['total_tax'];
            $output_tax = $this->transactionUtil->getOutputTax($business_id, $start_date, $end_date, $location_id)['total_tax'];
            $expense_tax = $this->transactionUtil->getExpenseTax($business_id, $start_date, $end_date, $location_id)['total_tax'];
            
            $expenses = $expenses->get();
            
            return view('vat::vat.print')->with(compact(
                'location_details',
                'expenses',
                'start_date',
                'end_date',
                'input_tax',
                'output_tax',
                'expense_tax'
            ));
    }

}
