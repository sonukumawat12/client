<?php

namespace Modules\Airline\Http\Controllers;

use App\Customer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Transaction;
use App\TransactionPayment;
use Illuminate\Support\Facades\Auth;

use App\ContactGroup;
use App\Contact;
use App\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; // Import the File class
use Illuminate\Support\Str;

use Modules\Airline\Entities\AirlinePrefixStarting;
use Modules\Airline\Entities\AdditionalService;
use Symfony\Component\HttpFoundation\Response;

use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use Yajra\DataTables\Facades\DataTables;

class AirlineServiceController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $businessUtil;
    
    
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil =  $businessUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'account_id' => ''
        ];
    }

    public function store_service(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'data' => 'required|array|min:1',
            'data.*.serviceName' => 'required|string', 
            'data.*.description' => 'required|string', 
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try{
            
            
            $payment_data = array();
            $payment = array();
            foreach($request->data as $data){
                $service = AdditionalService::create([
                    'name' => $data['serviceName'],
                    'description' => $data['description'],
                    'date_added' => date('Y-m-d'),
                    'user_id' => Auth::user()->id
                ]);
            }

            $output = [
                'statusText' => 'Success',
                'message' => __('lang_v1.success'),
                'data' => $service,
            ];
        } catch (QueryException $e) {
            $output = [
                'statusText' => 'Success',
                'success' => false,
                'message' => __('messages.something_went_wrong')
            ];
        }
        
        return $output;
        
    }
    
    /**
     * update a new prefix & starting no.
     */
    public function edit_service()
    {
        request()->validate([
            'id' => 'required',
            'serviceName' => 'required|string:min:3',
            'description' => 'required',
        ]);

        try {

            $prefix = AdditionalService::where('user_id', auth()->user()->id)
                ->findOrFail(request('id'));

            $prefix->update([
                'name' => request('serviceName'),
                'description' => request('description'),
            ]);

            $response = [
                'statusText' => 'Success',
                'data' => $prefix
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'errors' => 'Failed ' . end($e->errorInfo)
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * delete a new prefix & starting no.
     */
    public function delete_service()
    {
        try {

            $prefix = AdditionalService::where('user_id', auth()->user()->id)
                ->findOrFail(request('id'));

            $prefix->delete();

            $response = [
                'statusText' => 'Success',
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'errors' => 'Failed ' . end($e->errorInfo)
            ], Response::HTTP_BAD_REQUEST);
        }
    }

}