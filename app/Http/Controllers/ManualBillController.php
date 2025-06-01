<?php


namespace App\Http\Controllers;



use App\AccountTransaction;
use App\Account;
use App\AccountType;
use Carbon\Carbon;
use App\Contact;
use App\Product;
use App\TransactionSellLine;
use App\ProductVariation;
use App\Variation;

use App\Utils\Util;

use App\Transaction;

use App\ContactLedger;

use App\BusinessLocation;

use App\Utils\ModuleUtil;

use App\CustomerReference;

use App\CustomerStatement;

use App\Utils\ProductUtil;

use App\TransactionPayment;

use App\Utils\BusinessUtil;

use App\Utils\ContactUtil;

use Illuminate\Http\Request;

use App\Utils\TransactionUtil;

use App\CustomerStatementDetail;

use App\CustomerStatementLogo;

use App\CustomerStatementSetting;

use Mpdf\Mpdf;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Modules\Superadmin\Entities\HelpExplanation;
use Illuminate\Support\Str;

use Modules\Fleet\Entities\RouteProduct;
use Modules\Petro\Entities\CustomerPayment;

use Maatwebsite\Excel\Facades\Excel as MatExcel;
use App\Exports\CustomerStatement as CustomerStatementExport;

use App\Exports\CustomerStatementPmt as CustomerStatementPmtExport;
use Spatie\Activitylog\Models\Activity;
use App\User;
use Modules\Superadmin\Entities\Subscription;
use Modules\Vat\Entities\VatCustomerStatementDetail;
use Illuminate\Support\Facades\Log;


class ManualBillController extends Controller

{

    /**
     * All Utils instance.
     *

     */

    protected $transactionUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $commonUtil;

    protected $businessUtil;
    
    protected $contactUtil;


    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(BusinessUtil $businessUtil, Util $commonUtil, TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, ContactUtil $contactUtil)

    {

        $this->transactionUtil = $transactionUtil;

        $this->productUtil = $productUtil;

        $this->moduleUtil = $moduleUtil;

        $this->commonUtil = $commonUtil;

        $this->businessUtil = $businessUtil;
        
        $this->contactUtil = $contactUtil;

    }

    public function index(Request $request)

    {
        try {

        $business_id = request()->session()->get('business.id');
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        return view('manual_bill.index')->with(compact('customers'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong"),
                'error' => $e->getMessage()
            ];
        }
        return $output;

    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $business_id = auth()->user()->business_id;
            $user_id = auth()->id();

            // Step 1: Basic Validation
            $request->validate([
                'customer' => 'required|exists:contacts,id',
                'qty' => 'required|numeric|min:1',
                'unit_price' => 'required|numeric|min:0',
                'settlement_amount' => 'required|numeric|min:0',
            ]);

            // Step 2: Create Transaction
            $transaction = Transaction::create([
                'business_id' => $business_id,
                'location_id' => 1, // or get from UI or user session
                'contact_id' => $request->customer,
                'type' => 'sell',
                'transaction_date' => now(), // or use $request->transaction_date if provided
                'payment_status' => 'due',
                'final_total' => $request->total ?? ($request->qty * $request->unit_price),
                'invoice_no' => 'INV-' . time(),
                'customer_ref' => 'MANUAL-' . time(),
                'order_no' => $request->order_no,
                'order_date' => $request->order_date,
                'created_by' => $user_id,
            ]);

            // Step 3: Create Product (or use existing one from dropdown)
            $product_id = $request->input('products');
            $variation_id = null;

            // if (!$product_id) {
            //     // Auto-create a dummy product if not selected
            //     $product = Product::create([
            //         'business_id' => $business_id,
            //         'name' => 'Manual Product - ' . now()->format('YmdHis'),
            //         'type' => 'single',
            //         'unit_id' => 1,
            //         'enable_stock' => 0
            //     ]);

            //     $product_variation = ProductVariation::create([
            //         'product_id' => $product->id,
            //         'name' => 'Standard'
            //     ]);

            //     $variation = Variation::create([
            //         'product_id' => $product->id,
            //         'product_variation_id' => $product_variation->id,
            //         'name' => 'Default',
            //         'sell_price_inc_tax' => $request->unit_price
            //     ]);

            //     $product_id = $product->id;
            //     $variation_id = $variation->id;
            // }

            // Fetch product with variations
            $product = Product::with('variations')->find($product_id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Invalid product selected.'
                ], 422);
            }

            // Get first available variation (or handle multiple, if needed)
            $variation = $product->variations->first();

            if (!$variation) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Selected product has no variation.'
                ], 422);
            }

            // Now you have both:
            $product_id = $product->id;
            $variation_id = $variation->id;

            // Step 4: Create Sell Line
            TransactionSellLine::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product_id,
                'variation_id' => $variation_id,
                'quantity' => $request->qty,
                'unit_price' => $request->unit_price,
                'unit_price_inc_tax' => $request->unit_price,
                'sub_total' => $request->total ?? ($request->qty * $request->unit_price)
            ]);

            // Step 5: Optional - create payment or settlement_credit_sale_payments if needed

            // Step 6: Create customer_reference if not already exists
            CustomerReference::firstOrCreate([
                'business_id' => $business_id,
                'reference' => $transaction->customer_ref
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Manual bill saved successfully.',
                'transaction_id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Manual bill save error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }


    public function getTableData(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $start_date = Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d');
        $end_date = Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d');

        // Fetch only credit sale payments
        $payment_data = DB::table('settlement_credit_sale_payments')
            ->leftJoin('settlements', 'settlement_credit_sale_payments.settlement_no', '=', 'settlements.id')
            ->leftJoin('transactions', 'transactions.invoice_no', '=', 'settlements.settlement_no')
            ->leftJoin('products', 'products.id', '=', 'settlement_credit_sale_payments.product_id')
            ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
            ->where('settlement_credit_sale_payments.business_id', $business_id)
            ->where('settlement_credit_sale_payments.customer_id', $request->customer_id)
            ->whereDate('settlements.transaction_date', '>=', $start_date)
            ->whereDate('settlements.transaction_date', '<=', $end_date)
            ->select([
                'settlements.transaction_date',
                'settlements.settlement_no',
                'settlement_credit_sale_payments.order_number',
                'settlement_credit_sale_payments.created_at',
                'transactions.id',
                'settlement_credit_sale_payments.customer_reference',
                'settlement_credit_sale_payments.amount as settlement_amount',
                'products.id',
                'products.name',
                'variations.sell_price_inc_tax as unit_price',
                'transactions.location_id',
            ])
            ->orderBy('settlements.transaction_date', 'desc')
            ->get();

            // Extract settlement numbers with optional metadata (like date)
        $settlement_numbers = $payment_data->pluck('settlement_no')->unique()->values();
        $order_numbers = $payment_data->pluck('order_number')->unique()->values();
        $vehicle_numbers = $payment_data->pluck('customer_reference')
            ->filter() // removes null (and falsey) values
            ->unique()
            ->values();
        $products = $payment_data
            ->pluck('name', 'id')   // [id => name]
            ->filter()              // remove null keys or values
            ->unique()              // remove duplicates by value
            ->toArray();   

        // Get customer info
        $customer_name = DB::table('contacts')
            ->where('id', $request->customer_id)
            ->value('name');


        return [
            'payment_data' => $payment_data,
            'customer_name' => $customer_name,
            'settlement_numbers' => $settlement_numbers,
            'customer_references' => $vehicle_numbers,
            'order_numbers' => $order_numbers,
            'products_name' => $products,
        ];
    }

    
}
