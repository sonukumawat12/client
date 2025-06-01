<?php

namespace Modules\SalesDiscounts\Http\Controllers;
use App\Business;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SalesDiscounts\Entities\SalesDiscount;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Contact;
use App\BusinessLocation;
use Carbon\Carbon;
use App\Transaction;
use App\TransactionSellLine;

class SalesDiscountsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
     protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;
     protected $productUtil;
    

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }


    public function index()
    {
         $business_id = request()->session()->get('business.id');
         $business_locations = BusinessLocation::forDropdown($business_id);
         $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        return view('salesdiscounts::index')->with(compact('customers', 'business_locations'));
         
    }
     public function getSalesDiscountList(Request $request)
    {
            $business_id = request()->session()->get('user.business_id');
            $business_details = Business::find($business_id);
            $currency_precision = (int) $business_details->currency_precision;
            $qty_precision = (int) $business_details->quantity_precision;
        if (request()->ajax()) {
           
            $start_date = !empty($request->start_date) ? Carbon::parse($request->start_date)->format('Y-m-d') : date('Y-m-d');
            $end_date = !empty($request->end_date) ? Carbon::parse($request->end_date)->format('Y-m-d') : date('Y-m-d');
            $query = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
                ->leftjoin('business', 'transactions.business_id', 'business.id')
                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->where('transactions.business_id', $business_id)
                 ->whereIn('transactions.discount_type', ['fixed', 'percentage'])
               ->select('transactions.*', 'contacts.name as customer_name','business_locations.name as location_name','business.name as created_by');
         
         
         if (!empty($start_date) && !empty($end_date)) {
                 $query->whereDate('transactions.transaction_date', '>=', $start_date);
                 $query->whereDate('transactions.transaction_date', '<=', $end_date);
                 
            }
          if (!empty(request()->location_id   && request()->location_id !='All')) {
                    $query->where('transactions.location_id', request()->location_id);
                }
          if (!empty(request()->customer_id   && request()->customer_id !='All')) {
                $query->where('transactions.contact_id', request()->customer_id);
            }
          if (!empty(request()->discount_type && request()->discount_type !='All')) {
                $query->where('transactions.discount_type', request()->discount_type);
            }
            return Datatables::of($query)
                ->addIndexColumn()
                ->removeColumn('id')
                 ->editColumn('discount_amount', function ($row) use ($currency_precision) {
                        return number_format($row->discount_amount, $currency_precision);
                    })
               
                ->editColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
   
                       $html .= '<li><a href="' . action('\Modules\SalesDiscounts\Http\Controllers\SalesDiscountsController@show', ['id' => $row->id]) . '"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                    if (auth()->user()->can("edit_f22_stock_Taking_form")) {
                         $html .= '<li><a href="' . action('\Modules\SalesDiscounts\Http\Controllers\SalesDiscountsController@edit', ['id' => $row->id]) . '"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';
                    }
                    $html .= '<li><a href="' . action('\Modules\SalesDiscounts\Http\Controllers\SalesDiscountsController@destroy', [$row->id]) . '" ><i class="fa fa-trash" aria-hidden="true"></i>' . __("messages.delete") . '</a></li>';
                   
                    $html .= '</ul>';
                    return $html;
                })

                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
          $business_id = request()->session()->get('business.id');
         $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
          $business_locations = BusinessLocation::forDropdown($business_id);
        return view('salesdiscounts::create')->with(compact('customers','business_locations'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
         try {
            
            $business_id = request()->session()->get('business.id');
         
              $data = [
                    'business_id' => $business_id,
                    'transaction_date' => $request->transaction_date,
                    'location' => $request->location,
                    'invoice_no' => $request->subscription_amounts,
                    'customer' =>$request->customer,
                    'discount_type' => $request->discount_type,
                    'discount_amount' =>$request->subscription_amount,
                    'added_by' =>$request->created_by
                    ];
             
            $outputs = SalesDiscount::create($data);
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with($output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        
        $list = Transaction::findOrFail($id);
        $business_id = request()->session()->get('business.id');
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $location= BusinessLocation::where('business_id', $list->location_id)->first(['name']);
        $customer=Contact::where('id', $list->contact_id)->first('name');
        $business_details = Business::find($business_id);
        $currency_precision = (int) $business_details->currency_precision;
       
      
        if (!$list) {
            abort(404); // Return a 404 error if the record is not found
        }
    
        return view('salesdiscounts::show', compact('list', 'customers', 'customer','location', 'business_locations','currency_precision'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    
    public function edit($id)
    {
        $list = Transaction::findOrFail($id);
        $business_id = request()->session()->get('business.id');
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id);
         $location= BusinessLocation::where('business_id', $list->location_id)->first(['name']);
        $customer=Contact::where('id', $list->contact_id)->first('name');
        $business_details = Business::find($business_id);
        $currency_precision = (int) $business_details->currency_precision;
        if (!$list) {
            abort(404); // Return a 404 error if the record is not found
        }
    
        return view('salesdiscounts::edit', compact('list', 'customers', 'customer','location', 'business_locations','currency_precision'));
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
   public function update(Request $request, $id)
{
    try {
        $list = Transaction::findOrFail($id);

        $list->transaction_date = $request->input('transaction_date');
        $list->contact_id = $request->input('customer');
        $list->business_id = $request->input('location');
        $list->invoice_no = $request->input('invoice_no');
        $list->discount_amount = $request->input('discount_amount');
        $list->discount_type = $request->input('discount_type');

        $list->save();

        $output = ['success' => true,
                   'msg' => __('success')
                ];
    } catch (\Exception $e) {
        $output = ['success' => false,
                   'msg' => __('updated error')
                ];
    }

    return redirect()->back()->with($output);
}

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
         try {
            Transaction::where('id', $id)->delete();
            
            $output = [
                'success' => true,
                'msg' => __('fleet::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

       return redirect()->back()->with($output);
    }
}
