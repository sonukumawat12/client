<?php

namespace Modules\MPCS\Http\Controllers;

use App\Account;
use App\AccountType;
use App\Business;
use App\TaxRate;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\Transaction;
use App\Variation;
use App\AccountTransaction;
use Modules\MPCS\Entities\Mpcs16aFormSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\MPCS\Entities\FormF22Detail;
use Modules\MPCS\Entities\FormF22Header;
use Modules\MPCS\Entities\FormF22LossGain;
use Modules\MPCS\Entities\MpcsFormSetting;
use Modules\Petro\Entities\Pump;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log; // Import Log class
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\VariationLocationDetails;


class F22FormController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

    private $barcode_types;

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


    public function F22StockTaking()
    {
        
        if (!auth()->check()) {
            return redirect()->route('login');
        } 
        $business_id = request()->session()->get('business.id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $f22_counts  = FormF22Header::where('business_id', $business_id)->count();
        if (!empty($settings)) {
            $F22_from_no = $settings->F22_form_sn +  $f22_counts;
        } else {
            $F22_from_no = 1 +  $f22_counts;
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');

        $settings = MpcsFormSetting::where('business_id',  $business_id)->select('F22_no_of_product_per_page')->first();
 
        $last_form = FormF22Header::where('business_id', $business_id)->orderBy('id', 'desc')->first();
        $last_form_no = !empty($last_form) ? $last_form->form_no : '';
        
         $accountType = AccountType::where('name', 'expenses')->first();
         $accountType_gains = AccountType::whereIn('name', ['Current Assets', 'Current Liabilities'])->first();

         if ($accountType_gains) {
            // Fetch all accounts under "expenses"
            $accountType_gain = Account::where('account_type_id', $accountType_gains->id)->pluck('name', 'id');
        }
        if ($accountType) {
            // Fetch all accounts under "expenses"
            $accounts = Account::where('account_type_id', $accountType->id)->pluck('name', 'id');
        }
        return view('mpcs::forms.F22.F22_stock_taking')->with(compact('F22_from_no', 'business_locations', 'products', 'settings', 'last_form_no','accounts','accountType_gain'));
    }

    public function getF22FormList(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $header = FormF22Header::leftjoin('business_locations', 'form_f22_headers.location_id', 'business_locations.id')
                ->leftjoin('users', 'form_f22_headers.created_by', 'users.id')
                ->where('form_f22_headers.business_id', $business_id)
                ->select('form_f22_headers.*', 'business_locations.name as locations_name', 'users.username');


            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $header->whereIn('form_f22_headers.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $header->where('form_f22_headers.location_id', request()->location_id);
            }


            return Datatables::of($header)
                ->addIndexColumn()

                ->removeColumn('id')
                ->addColumn('total_stock_lose_purchase', function ($row) {
                     $total_stock_lose_purchase = FormF22Detail::where('form_no', $row->form_no)
                    ->groupBy('form_no')
                    ->selectRaw('SUM(difference_qty * unit_purchase_price) as total')
                    ->value('total'); // Fetch single value
                        return $total_stock_lose_purchase;
                    })
                    ->addColumn('total_stock_lose_sale', function ($row) {
                         $total_stock_lose_sale = FormF22Detail::where('form_no', $row->form_no)
                    ->groupBy('form_no')
                    ->selectRaw('SUM(difference_qty * unit_sale_price) as total')
                    ->value('total'); // Fetch single value
                        return $total_stock_lose_sale;
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

                    if (auth()->user()->can("superadmin")) {
                        $html .= '<li><a href="' . action('\Modules\MPCS\Http\Controllers\F22FormController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';
                    }

                    $html .= '<li><a href="#" class="reprint_form" data-href="' . action('\Modules\MPCS\Http\Controllers\F22FormController@printF22FormById', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';

                    $html .= '</ul>';
                    return $html;
                })

                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $form = FormF22Header::leftjoin('form_f22_details', 'form_f22_headers.id', 'form_f22_details.header_id')
                ->where('form_f22_headers.id', $id)
                ->select('form_f22_headers.*', 'form_f22_details.*', 'form_f22_details.id as detial_id');
            return DataTables::of($form)
                ->addIndexColumn()

                ->removeColumn('id')
                ->editColumn('current_stock', function ($row) {
                    return '<input type="hidden" value="' . $row->current_stock . '" name="f22[' . $row->detial_id . '][current_stock]" id="f22[' . $row->detial_id . '][current_stock]" ><span class="display_currency current_stock" data-orig-value="' . $row->current_stock . '" data-currency_symbol = "false">' . $row->current_stock . '</span>';
                })
                ->editColumn('unit_purchase_price', function ($row) {
                    return '<span class="display_currency unit_purchase_price" data-orig-value="' . $row->unit_purchase_price . '" data-currency_symbol = "false">' . $row->unit_purchase_price . '</span><input type="hidden" value="' . $row->unit_purchase_price . '"  class="unit_purchase_price" name="f22[' . $row->detial_id . '][unit_purchase_price]" >';
                })
                ->editColumn('total_purchase_price', function ($row) {
                    return '<span class="display_currency total_purchase_price" data-orig-value="' . $row->purchase_price_total . '" data-currency_symbol = "false"></span><input value="' . $row->purchase_price_total . '" type="hidden" class="total_purhcase_value" name="f22[' . $row->detial_id . '][total_purhcase_value]" >';
                })
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="display_currency unit_sale_price" data-orig-value="' . $row->unit_sale_price . '" data-currency_symbol = "false">' . $row->unit_sale_price . '</span><input type="hidden" value="' . $row->unit_sale_price . '"   class="unit_sale_price" name="f22[' . $row->detial_id . '][unit_sale_price]" >';
                })
                ->editColumn('total_sale_price', function ($row) {
                    return '<span class="display_currency total_sale_price" data-orig-value="' . $row->sales_price_total . '" data-currency_symbol = "false"></span><input value="' . $row->sales_price_total . '" type="hidden" class="total_sale_value" name="f22[' . $row->detial_id . '][total_sale_value]" >';
                })

                ->addColumn('book_no', function ($row) {
                    return '<input class="form-control input_number book_no" name="f22[' . $row->detial_id . '][book_no]" id="f22[' . $row->detial_id . '][book_no]" style="width: 80px;" name="book_no" value="' . $row->book_no . '" >';
                })
                ->addColumn('stock_count', function ($row) {
                    return '<input class="form-control input_number stock_count"  name="f22[' . $row->detial_id . '][stock_count]" id="f22[' . $row->detial_id . '][stock_count]"  style="width: 80px;" name="stock_count" value="' . $row->stock_count . '" >';
                })
                ->addColumn('qty_difference', function ($row) {
                    return '<input class="form-control input_number qty_difference" name="f22[' . $row->detial_id . '][qty_difference]" id="f22[' . $row->detial_id . '][qty_difference]" style="width: 80px;" name="qty_difference" value="' . $row->difference_qty . '" readonly >';
                })
                ->editColumn('sku', function ($row) {
                    return '<input type="hidden" value="' . $row->product_code . '" name="f22[' . $row->detial_id . '][sku]" id="f22[' . $row->detial_id . '][sku]" > ' . $row->sku;
                })
                ->editColumn('product', function ($row) {
                    return '<input type="hidden" value="' . $row->product . '" name="f22[' . $row->detial_id . '][product]" id="f22[' . $row->detial_id . '][product]" > ' . $row->product;
                })
                ->rawColumns(['total_purchase_price', 'total_sale_price', 'book_no', 'stock_count', 'qty_difference', 'unit_purchase_price', 'unit_sale_price', 'current_stock', 'sku', 'product'])
                ->make(true);
        }

        return view('mpcs::forms.F22.edit')->with(compact('id'));
    }

    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        try {
            $data = array();
            parse_str($request->data, $data); // converting serielize string to array

            DB::beginTransaction();

            foreach ($data['f22'] as $key => $item) {
                $data_details = array(
                    'product_code' => $item['sku'],
                    'product' => $item['product'],
                    'book_no' => $item['book_no'],
                    'current_stock' => $item['current_stock'],
                    'stock_count' => $item['stock_count'],
                    'unit_purchase_price' => $item['unit_purchase_price'],
                    'unit_sale_price' => $item['unit_sale_price'],
                    'purchase_price_total' => $item['total_purhcase_value'],
                    'sales_price_total' => $item['total_sale_value'],
                    'difference_qty' => $item['qty_difference']
                );

                $details = FormF22Detail::where('id', $key)->update($data_details);
            }
            DB::commit();

            return $this->printF22FormById($id);
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }

    public function getF22Form(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
       
        $qty_precision = (int) $business_details->quantity_precision;
        if (request()->ajax()) {
             $currency_precision = Business::where('id', $business_id)->value('currency_precision');
             $tax_rate = TaxRate::where('business_id', $business_id)
                   ->where('name', 'VAT') // Adding condition for name = "VAT"
                   ->select('amount')
                   ->first();
	                $tax_amount = ($tax_rate->amount)/100;

            $purchases = Product::leftjoin('purchase_lines', 'products.id', 'purchase_lines.product_id')
                ->leftjoin('transactions', 'purchase_lines.transaction_id', 'transactions.id')
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
                ->leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->where('products.business_id', $business_id)
                ->select(
                    'transactions.id',
                    'transactions.ref_no as reference_no',
                    'variations.dpp_inc_tax as unit_purchase_price',
                    'transactions.final_total as total_purchase_price',
                    'BS.name as location',
                    'products.category_id',
                    'products.name as product',
                    'products.id as product_id',
                    'variations.id as variation_id',
                    'products.sku',
                    'products.tax_type as tax_type',
                    'products.alert_quantity',
                    'variation_location_details.qty_available as current_stock',
                    'variations.default_sell_price',
                    'variations.sell_price_inc_tax as sell_price_inc_tax',
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
                ->groupBy('products.sku');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }

            if (!empty(request()->product_id)) {
                $purchases->where('products.id', request()->product_id);
            }
            

\Log::info("Stock taking{$purchases->get()}");
            return DataTables::of($purchases)
                ->addIndexColumn()

                ->removeColumn('id')
                ->editColumn('current_stock', function ($row) {
                    return '<input type="hidden" value="' . $row->current_stock . '" name="f22[' . $row->product_id . '][current_stock]" id="f22[' . $row->product_id . '][current_stock]" ><span class="display_currency current_stock" data-orig-value="' . $row->current_stock . '" data-currency_symbol = "false">' . $row->current_stock . '</span>';
                })
                ->editColumn('unit_purchase_price', function ($row) use ($tax_amount,$currency_precision) {
                // Check if tax_type is "inclusive"
                    if ($row->tax_type === 'inclusive') {
                        $calculated_price = $row->unit_purchase_price;// + ($row->unit_purchase_price * $tax_amount);
                    } else {
                        $calculated_price = $row->unit_purchase_price;
                    }
                
                    $formatted_price = number_format($calculated_price, $currency_precision, '.', '');
                

                    return '<span class="display_currency unit_purchase_price" data-orig-value="' . $formatted_price . '" data-currency_symbol="false">' . $formatted_price . '</span>
                    <input type="hidden" class="unit_purchase_price" name="f22[' . $row->product_id . '][unit_purchase_price]" value="' . $formatted_price . '">';
                       
                })

                ->editColumn('total_purchase_price', function ($row) {
                    $formatted_total_price = number_format($row->unit_purchase_price, 2, '.', '')*0.00;//$row->current_stock;
                
                    return '<span class="display_currency total_purchase_price" data-orig-value="' . $formatted_total_price . '" data-currency_symbol="false">' . $formatted_total_price . '</span>
                            <input type="hidden" class="total_purchase_price_input" name="f22[' . $row->product_id . '][total_purchase_price]" value="' . $formatted_total_price . '">';
                })
                ->editColumn('unit_sale_price', function ($row) use ($tax_amount, $currency_precision) {
                    if ($row->tax_type === 'inclusive') {
                        $calculated_sale_price = $row->sell_price_inc_tax;
                    } else {
                        $calculated_sale_price = $row->sell_price_inc_tax;
                    }
                
                    $formatted_price = number_format($calculated_sale_price, $currency_precision, '.', '');
                
                    return '<span class="display_currency unit_sale_price" data-orig-value="' . $formatted_price . '" data-currency_symbol="false">' . $formatted_price . '</span>
                            <input type="hidden" class="unit_sale_price_input" name="f22[' . $row->product_id . '][unit_sale_price]" value="' . $formatted_price . '">';
                })
                

                ->editColumn('total_sale_price', function ($row) {
                    $formatted_total_price = number_format($row->sell_price_inc_tax, 2, '.', '')*0.00;
                
                    return '<span class="display_currency total_sale_price" data-orig-value="' . $formatted_total_price . '" data-currency_symbol="false">' . $formatted_total_price . '</span>
                            <input type="hidden" class="total_sale_price_input" name="f22[' . $row->product_id . '][total_sale_price]" value="' . $formatted_total_price . '">';
                })

                ->addColumn('book_no', function ($row) {
                    return '<input class="form-control book_no" name="f22[' . $row->product_id . '][book_no]" id="f22[' . $row->product_id . '][book_no]" name="book_no" value="" style="width: 80px;">';
                })
                ->addColumn('stock_count', function ($row) use($qty_precision, $business_id) {
                    $category = Category::where('business_id', $business_id)
                        ->where('parent_id', 0)
                        ->where('id', $row->category_id)
                        ->where('name', 'Fuel')
                        ->first();
                
                    $precision = ($category) ? 3 : $qty_precision;
                    $stockCount = number_format($row->current_stock, $precision); 
                
                    return '<input class="form-control stock_count" name="f22[' . $row->product_id . '][stock_count]" id="f22[' . $row->product_id . '][stock_count]" style="width: 80px;" value="0.00" >';
                })
                
                ->addColumn('qty_difference', function ($row) use($qty_precision, $business_id) {
                    $category = Category::where('business_id', $business_id)
                    ->where('parent_id', 0)
                    ->where('id', $row->category_id)
                    ->where('name', 'Fuel')
                    ->first();

                  
                  $stockCount = number_format($row->alert_quantity);
                    if (empty($row->alert_quantity)) {
                        $difference =$row->current_stock ;
                    } else {
                          $difference =$row->current_stock;
                    }
                     $difference = number_format($difference, $qty_precision, '.', '');
                    return '<input class="form-control qty_difference" name="f22[' . $row->product_id . '][qty_difference]" id="f22[' . $row->product_id . '][qty_difference]" style="width: 65px;" name="qty_difference" value="'.$difference.'" >';
                })
                ->editColumn('sku', function ($row) {
                    return '<input type="hidden" value="' . $row->sku . '" name="f22[' . $row->product_id . '][sku]" id="f22[' . $row->product_id . '][sku]" > ' . $row->sku;
                })
                ->editColumn('product', function ($row) {
                    return '<input type="hidden" value="' . $row->product . '" name="f22[' . $row->product_id . '][product]" id="f22[' . $row->product_id . '][product]" > ' . $row->product . ' <input type="hidden" value="' . $row->variation_id . '" name="f22[' . $row->product_id . '][variation_id]" id="f22[' . $row->product_id . '][variation_id]" >';
                })
                ->rawColumns(['total_purchase_price', 'total_sale_price', 'book_no', 'stock_count', 'qty_difference', 'unit_purchase_price', 'unit_sale_price', 'current_stock', 'sku', 'product'])
                ->make(true);
        }
    }

    public function getLastVerifiedF22Form(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $last_form = FormF22Header::where('business_id', $business_id)->orderBy('id', 'desc')->first();
            if (!empty($last_form)) {
                $last_form_header_id =  $last_form->id;
            } else {
                $last_form_header_id =  0;
            }
            $verified_form = FormF22Detail::where('header_id', $last_form_header_id);

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $verified_form->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $verified_form->where('transactions.location_id', request()->location_id);
            }

            if (!empty(request()->product_id)) {
                $verified_form->where('products.id', request()->product_id);
            }
            $index = 0;

            return Datatables::of($verified_form)
                ->addIndexColumn()

                ->removeColumn('id')
                ->editColumn('current_stock', function ($row) {
                    return '<input type="hidden" value="' . $row->current_stock . '" name="f22[' . $row->id . '][current_stock]" id="f22[' . $row->id . '][current_stock]" ><span class="display_currency current_stock" data-orig-value="' . $row->current_stock . '" data-currency_symbol = "false">' . $row->current_stock . '</span>';
                })
                ->editColumn('unit_purchase_price', function ($row) {
                    return '<span class="display_currency unit_purchase_price" data-orig-value="' . $row->unit_purchase_price . '" data-currency_symbol = "false">' . $row->unit_purchase_price . '</span><input type="hidden" value="' . $row->unit_purchase_price . '"  class="unit_purchase_price" name="f22[' . $row->id . '][unit_purchase_price]" >';
                })
                ->editColumn('total_purchase_price', function ($row) {
                    return '<span class="display_currency lf_total_purchase_price" data-orig-value="' . $row->purchase_price_total . '" data-currency_symbol = "false"></span><input type="hidden"  class="total_purhcase_value" name="f22[' . $row->id . '][total_purhcase_value]" value="' . $row->purchase_price_total . '" >' . $row->purchase_price_total;
                })
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="display_currency unit_sale_price" data-orig-value="' . $row->unit_purchase_price . '" data-currency_symbol = "false">' . $row->unit_purchase_price . '</span><input type="hidden" value="' . $row->unit_purchase_price . '"   class="unit_sale_price" name="f22[' . $row->id . '][unit_sale_price]" >';
                })
                ->editColumn('total_sale_price', function ($row) {
                    return '<span class="display_currency lf_total_sale_price" data-orig-value="' . $row->sales_price_total . '" data-currency_symbol = "false"></span><input type="hidden" class="total_sale_value" name="f22[' . $row->id . '][total_sale_value]" value="' . $row->sales_price_total . '">' . $row->sales_price_total;
                })
                ->addColumn('book_no', function ($row) {
                    return '<span>' . $row->book_no . '</span><input type="hidden" class="form-control book_no" name="f22[' . $row->id . '][book_no]" id="f22[' . $row->id . '][book_no]"  value="' . $row->book_no . '" >';
                })
                ->addColumn('stock_count', function ($row) {
                    return '<span>' . $row->stock_count . '</span><input type="hidden" class="form-control stock_count"  name="f22[' . $row->id . '][stock_count]" id="f22[' . $row->id . '][stock_count]"  value="' . $row->stock_count . '" >';
                })
                ->addColumn('qty_difference', function ($row) {
                    return '<span>' . $row->difference_qty . '</span><input type="hidden"  class="form-control difference_qty" name="f22[' . $row->id . '][difference_qty]" id="f22[' . $row->id . '][difference_qty]" value="' . $row->difference_qty . '">';
                })
                ->editColumn('sku', function ($row) {
                    return '<input type="hidden" value="' . $row->product_code . '" name="f22[' . $row->id . '][sku]" id="f22[' . $row->id . '][sku]" > ' . $row->product_code;
                })
                ->editColumn('product', function ($row) {
                    return '<input type="hidden" value="' . $row->product . '" name="f22[' . $row->id . '][product]" id="f22[' . $row->id . '][product]" > ' . $row->product;
                })
                ->rawColumns(['total_purchase_price', 'total_sale_price', 'book_no', 'stock_count', 'qty_difference', 'unit_purchase_price', 'unit_sale_price', 'current_stock', 'sku', 'product'])
                ->make(true);
        }
    }

    public function printF22Form(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
 
        $data = array();
        parse_str($request->data, $data); // converting serielize string to array
        $settings = MpcsFormSetting::where('business_id',  $business_id)->select('F22_no_of_product_per_page')->first();
        $details = $data;

        $data = $data['f22'];
        $date=$request->date_value;
      
        return view('mpcs::forms.F22.partials.print_f22_form')->with(compact('data', 'settings', 'details','date'));
    }

    public function printF22FormById($header_id)
    {
        $business_id = request()->session()->get('user.business_id');

        $header = FormF22Header::leftjoin('business_locations', 'form_f22_headers.location_id', 'business_locations.id')
            ->where('form_f22_headers.id', $header_id)->select('business_locations.name as location_name', 'form_f22_headers.*')->first();
        $details = FormF22Detail::where('header_id', $header_id)->get();
        $settings = MpcsFormSetting::where('business_id',  $business_id)->select('F22_no_of_product_per_page')->first();

        return view('mpcs::forms.F22.partials.print_byID_f22_form')->with(compact('header', 'details', 'settings'));
    }

    public function saveF22Form(Request $request)
{
    $business_id = $request->session()->get('user.business_id');

    try {
        $data = [];
        parse_str($request->data, $data);       
        $date = $request->date_value;

        $data_header = [
            'form_no' => $data['F22_from_no'],
            'business_id' => $business_id,
            'location_id' => $data['f22_location_id'],
            'manager_name' => $data['manager_name'],
            'form_date' => $date,
            'purchase_price1' => $data['purchase_price1'] ?? 0.00,
            'purchase_price2' => $data['purchase_price2'] ?? 0.00,
            'purchase_price3' => $data['purchase_price3'] ?? 0.00,
            'sales_price1' => $data['sales_price1'] ?? 0.00,
            'sales_price2' => $data['sales_price2'] ?? 0.00,
            'sales_price3' => $data['sales_price3'] ?? 0.00,
            'status' => 1,
            'created_by' => Auth::user()->id
        ];

        DB::beginTransaction();

        $header = FormF22Header::create($data_header);
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $link_account = FormF22LossGain::where('business_id', $business_id)->first();

        $total_loss = 0;
        $total_gain = 0;
 
        foreach ($data['f22'] as $key => $item) {
            $difference = (!empty($item['stock_count'])) ? (float)$item['stock_count'] - (float)$item['current_stock'] : 0;

            $data_details = [
                'header_id' => $header->id,
                'business_id' => $business_id,
                'form_no' => $data['F22_from_no'],
                'location_id' => $data['f22_location_id'],
                'product_code' => $item['sku'],
                'product' => $item['product'],
                'book_no' => $item['book_no'],
                'current_stock' => $item['current_stock'],
                'stock_count' => $item['stock_count'],
                'unit_purchase_price' => $item['unit_purchase_price'],
                'unit_sale_price' => $item['unit_sale_price'],
                'purchase_price_total' => $item['total_purchase_price'],
                'sales_price_total' => $item['total_sale_price'],
                'difference_qty' => $difference,
                'status' => 1
            ];

            FormF22Detail::create($data_details);

            if (!empty($settings) && $settings->current_stock_aa_onstocktaking == 1) {
                VariationLocationDetails::where('variation_id', $item['variation_id'])
                    ->where('product_id', $key)
                    ->increment('qty_available', $difference);
            }

            // Get variation purchase price for this product
            $product = Product::where('name', $item['product'])
                ->where('business_id', $business_id)
                ->first();
            $variation = Variation::where('product_id', $product->id)->first();
            $unit_price = $variation->default_purchase_price;
            
\Log::info("difference{$difference }");
\Log::info("product name:{$item['product'] }");
            if ($difference > 0) {
                $total_loss += abs($difference) * $unit_price;
            } elseif ($difference <= 0) {
                $total_gain += $difference * $unit_price;
            }
        }
\Log::info("total loss{$total_loss }");
\Log::info("total gain{$total_gain }");
        // Handle loss transaction
        if ($total_loss > 0) {
            $transaction = Transaction::create([
                'business_id' => $business_id,
                'location_id' => $data['f22_location_id'],
                'type' => 'stock_taking',
                'sub_type' => 'shortage',
                'status' => 'final',
                'ref_no' => 'F22 Form No ' . $data['F22_from_no'] . ' Stock Loss',
                'final_total' => $total_loss
            ]);

            AccountTransaction::createAccountTransaction([
                'amount' => $total_loss,
                'account_id' => $link_account->stock_loss_account,
                'type' => 'debit',
                'sub_type' => 'ledger',
                'transaction_id' => $transaction->id,
                'created_by' => Auth::user()->id
            ]);

            AccountTransaction::createAccountTransaction([
                'amount' => $total_loss,
                'account_id' => $link_account->stock_gain_account,
                'type' => 'credit',
                'sub_type' => 'ledger',
                'transaction_id' => $transaction->id,
                'created_by' => Auth::user()->id
            ]);
        }

        // Handle gain transaction
        if ($total_gain > 0) {
            $transaction = Transaction::create([
                'business_id' => $business_id,
                'location_id' => $data['f22_location_id'],
                'type' => 'stock_taking',
                'sub_type' => 'overage',
                'status' => 'final',
                'ref_no' => 'F22 Form No ' . $data['F22_from_no'] . ' Stock Gain',
                'final_total' => $total_gain
            ]);

            AccountTransaction::createAccountTransaction([
                'amount' => $total_gain,
                'account_id' => $link_account->stock_gain_account,
                'type' => 'credit',
                'sub_type' => 'ledger',
                'transaction_id' => $transaction->id,
                'created_by' => Auth::user()->id
            ]);

            AccountTransaction::createAccountTransaction([
                'amount' => $total_gain,
                'account_id' =>$link_account->stock_loss_account,
                'type' => 'debit',
                'sub_type' => 'ledger',
                'transaction_id' => $transaction->id,
                'created_by' => Auth::user()->id
            ]);
        }

        DB::commit();

        return $this->printF22FormById($header->id);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
        return [
            'success' => 0,
            'msg' => __('messages.something_went_wrong')
        ];
    }
}


    
     public function store_stock_taking(Request $request)
        {
             
          
        
            try {
                  $business_id = request()->session()->get('user.business_id');
                $data = [];
                parse_str($request->data, $data); // Converting serialized string to array
        
                $data_header = [
                    'business_id' => $business_id,
                    'stock_loss_account' =>$request->stock_loss_account,
                    'stock_gain_account' => $request->stock_gain_account,
                    'status' => "Enabled",
                    'added_user' => Auth::user()->username
                ];
        
                DB::beginTransaction();
               $form_gain_loss= FormF22LossGain::create($data_header);
                 // Update the current row
                    DB::table('form_f22_loss_gains')->update(['status' => 'Disable']);
                    DB::table('form_f22_loss_gains')
                    ->where('id', $form_gain_loss->id)
                    ->update(['status' => 'Enabled']);

                DB::commit();
        
                return redirect()->back()->with('success');
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback on error
                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
        
                return redirect()->back()->with('error', __('messages.something_went_wrong'));
            }
        }
     public function getF22FormListGainLoss(Request $request)
{
    $business_id = request()->session()->get('user.business_id');

    if ($request->ajax()) {
        $header = FormF22LossGain::where('business_id', $business_id)
                ->orderBy('updated_at', 'desc') // Sort by descending order
                ->select('form_f22_loss_gains.*');
                    return Datatables::of($header)
            ->addIndexColumn()
            ->removeColumn('id')
            ->addColumn('stock_loss_account', function ($row) {
                $accountTypeGain = Account::where('id', $row->stock_loss_account)->first();
                return $accountTypeGain ? $accountTypeGain->name : 'N/A'; // Avoid null errors
            })
            ->addColumn('stock_gain_account', function ($row) {
                $accountTypeLoss = Account::where('id', $row->stock_gain_account)->first();
                return $accountTypeLoss ? $accountTypeLoss->name : 'N/A'; // Fetch gain account name
            })
            ->addColumn('status', function ($row) {
                if ($row->status === 'Enabled') {
                    return 'Enabled';
                } else {
                    return 'Disable';
                }
            })
         ->editColumn('action', function ($row) {
                $html = '';
            
                if (auth()->user()->can("edit_f22_stock_Taking_form")) {
                    if ($row->status === 'Enabled') {
                        $html = '<button class="toggle-action-btn" 
                        data-id="' . $row->id . '" 
                        data-enabled="' . ($row->status ? 'Disable' : 'Enabled') . '" 
                        aria-pressed="' . ($row->status ? 'Disable' : 'Enabled') . '">
                        ' . ($row->status ? 'Disable' : 'Enabled') . '
                    </button>';
                    } else {
                       
                    $html = '<button class="toggle-action-btn" 
                    data-id="' . $row->id . '" 
                    data-enabled="' . ($row->status ? 'Enabled' : 'Disable') . '" 
                    aria-pressed="' . ($row->status ? 'Enabled' : 'Disable') . '">
                    ' . ($row->status ? 'Enabled' : 'Disable') . '
                </button>'; 
                }
                   
                }
            
                return $html;
            })

            ->rawColumns(['status', 'action']) // Ensure HTML is not escaped
            ->make(true);
    }
}
public function checkUserExistence()
{
    $business_id = request()->session()->get('user.business_id');
    $user_id = auth()->user()->username;

    // Check if the user already exists in the form_f22_loss_gains table
    $userExists = FormF22LossGain::where('business_id', $business_id)
        ->where('added_user', $user_id)
        ->exists();

    return response()->json(['exists' => $userExists]);
}

public function fetchPumps()
{
    try {
        $business_id = request()->session()->get('user.business_id');

        $pumps = Pump::leftJoin('products', 'pumps.product_id', '=', 'products.id')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                     ->leftjoin('business_locations', 'pumps.location_id', 'business_locations.id')
                      ->leftjoin('fuel_tanks', 'pumps.fuel_tank_id', 'fuel_tanks.id')
                    ->where('categories.name', 'Fuel')
                    ->where('products.business_id', $business_id)
                    ->select(
                        'pumps.*',
                        'products.name as product_name',
                        'pumps.pump_name as pump_name',

                    )
                    ->get();

        Log::info('Pumps fetched successfully', ['pumps' => $pumps ]);

        return response()->json(['pumps' => $pumps], 200);
    } catch (\Exception $e) {
        Log::error('Error fetching pumps: ' . $e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json(['error' => 'Failed to fetch pumps'], 500);
    }
}

public function getLinkaccountstate(Request $request)
{
     
    $rowId = $request->input('row_id');
    $isEnabled = $request->input('is_enabled');
    Log::info('Link Account', ['Link' => $isEnabled]);
    if ($isEnabled) {
        // Disable all other rows
        DB::table('form_f22_loss_gains')->update(['status' => 'Disable']);
    }

    // Update the current row
    DB::table('form_f22_loss_gains')
        ->where('id', $rowId)
        ->update(['status' => 'Enabled']);

    return response()->json(['success' => true]);
}


}
