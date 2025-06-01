<?php



namespace Modules\Airline\Http\Controllers;



use App\Account;

use App\AccountTransaction;

use App\AccountType;

use App\ContactLinkedAccount;

use App\Country;

use Modules\Airline\Entities\Airline;

use Modules\Airline\Entities\AirlineAgent;

use Modules\Airline\Entities\AirlineAirports;

use Modules\Airline\Entities\BusinessLocation;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\QueryException;

use Modules\Airline\Entities\AirlinePrefixStarting;

use Modules\Airline\Entities\AirlinePrefixStartingMode;

use Symfony\Component\HttpFoundation\Response;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use Yajra\DataTables\Facades\DataTables;

use Modules\Airline\Entities\AdditionalService;

use Illuminate\Support\Facades\DB;



class AirlineSettingController extends Controller

{

    /**

     * Display a listing of the resource.

     */

    public function index()

    {

        // $business_id = request()->session()->get('business.id');

        // if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'airline_module')) {

        //     abort(403, 'Unauthorized action.');

        // }



        if (!auth()->user()->can('airline.view_setting')) {

            abort(403, 'Unauthorized action.');

        }



        $airline_prefix_starting_mode = $this->get_airline_prefix_starting_mode();

        $airline_prefix_starting_no = $this->get_airline_prefix_starting_no();



        $airlines = $this->get_airlines();



        $airline_agents = $this->get_airline_agents();



        $accounts_type = $this->get_accounts_income_expense_type();



        $selected_types = $accounts_type->pluck('id')->toArray();



        $accounts = $this->get_accounts($selected_types);



        $airports = $this->get_airports();



        $airline_additional_service  = $this->get_airline_additional_service();


        $contact_settings  = $this->get_contact_settings();

        return view('airline::airline_settings.index')->with(compact(

            'airline_prefix_starting_mode',

            'airline_prefix_starting_no',

            'airlines',

            'airline_agents',

            'accounts_type',

            'accounts',

            'airports',

            'airline_additional_service',

            'contact_settings'

        ));

    }

    private function get_contact_settings()
    {

    
        $business_id = request()->session()->get('user.business_id');

        // $data = ContactLinkedAccount::where('contact_linked_accounts.business_id',$business_id)
        //             ->join('users as u','u.id','contact_linked_accounts.created_by')
        //             ->join('accounts as c','c.id','contact_linked_accounts.customer_advance')
        //             ->join('accounts as s','s.id','contact_linked_accounts.supplier_advance') 
        //             ->leftjoin('accounts as cdr_liability','cdr_liability.id','contact_linked_accounts.customer_deposit_refund_liability_account')
        //             ->leftjoin('accounts as cdr_asset','cdr_asset.id','contact_linked_accounts.customer_deposit_refund_asset_account')
        //             ->select('contact_linked_accounts.*','c.name as cust','s.name as sup','u.username','cdr_liability.name as _customer_deposit_refund_liability_account','cdr_asset.name as _customer_deposit_refund_asset_account')->first();

      
        $data = ContactLinkedAccount::where('contact_linked_accounts.business_id', $business_id)
            ->where('contact_linked_accounts.status', 1)
            ->join('users as u', 'u.id', 'contact_linked_accounts.created_by')
            ->join('accounts as c', 'c.id', 'contact_linked_accounts.customer_advance')
            ->join('accounts as s', 's.id', 'contact_linked_accounts.supplier_advance') 
            ->leftjoin('accounts as cdr_liability', 'cdr_liability.id', 'contact_linked_accounts.customer_deposit_refund_liability_account')
            ->leftjoin('accounts as cdr_asset', 'cdr_asset.id', 'contact_linked_accounts.customer_deposit_refund_asset_account')
            ->select('contact_linked_accounts.*', 'c.name as cust', 's.name as sup', 'u.username', 'cdr_liability.name as _customer_deposit_refund_liability_account', 'cdr_asset.name as _customer_deposit_refund_asset_account')
            ->first();

            if($data){
                $locationIds = json_decode($data->location, true); 

                $locationNames = BusinessLocation::whereIn('id', $locationIds)
                    ->pluck('name')
                    ->toArray();
                $locationNamesString = implode(', ', $locationNames);
            }else{
                $locationNamesString = '';
            }
        




        $liability = AccountType::getAccountTypeIdByName('Current Liabilities', $business_id)->id;


        $liability_accounts = Account::where('business_id', $business_id)->where('account_type_id', $liability)->pluck('name', 'id');



        $asset = AccountType::getAccountTypeIdByName('Current Assets', $business_id)->id;
        $asset_accounts = Account::where('business_id', $business_id)->where('account_type_id', $asset)->pluck('name', 'id');

        return [
            'data' => $data,
            'liability_accounts' => $liability_accounts,
            'location_names' => $locationNamesString,
            'asset_accounts' => $asset_accounts,
        ];


    }

    public function getLocationsContacts(Request $request)
    {


        $locations = BusinessLocation::select('id', 'name as text')->get();


        return response()->json($locations);
    }

    public function storeContactsSettings(Request $request){

        try {
            $input = $request->except('_token');
    
            $input['business_id'] = $business_id = $request->session()->get('user.business_id');

            ContactLinkedAccount::where('business_id', $business_id)->update(['status' => 0]);
    
            $input['customer_advance'] = $request->customer_advance;
            $input['supplier_advance'] = $request->supplier_advance;
            $input['customer_deposit_refund_liability_account'] = $request->customer_deposit_refund_liability_account;
            $input['customer_deposit_refund_asset_account'] = $request->customer_deposit_refund_asset_account;
            $input['created_by'] = auth()->user()->id;
    
            $input['location'] = json_encode($request->location);
    
            $input['status'] = 1;
    
             ContactLinkedAccount::create($input);

    
            // return redirect()->back()->with('success', 'Settings saved successfully.');
        } catch (\Exception $e) {
            // return redirect()->back()->with('error', $e->getMessage());
        }
   
    }


    public function checkLocationExist(Request $request)
    {
        $locations = $request->location;
    
        foreach ($locations as $location) {
            if (ContactLinkedAccount::where('location', $location)->exists()) {
                return response()->json(['exists' => true]);
            }
        }
    
        return response()->json(['exists' => false]);
    }


    private function get_airline_prefix_starting_mode()

    {

        $data = AirlinePrefixStartingMode::with('user')

            ->where('user_id', auth()->user()->id)->get();



        return $data;

    }



    private function get_airline_prefix_starting_no()

    {

        $data = AirlinePrefixStarting::with('user', 'mode')

            ->where('user_id', auth()->user()->id)->get();



        foreach ($data as $dt) {

            $created_at = new \Carbon($dt->created_at);

            $dt['date'] = $created_at->isoFormat('ddd, D MMM Y');

            $dt['status_name'] = $dt->status ? 'Enabled' : 'Disabled';

        }



        return $data;

    }



    private function get_airlines()

    {

        $data = Airline::with('user','flights')

            ->where('user_id', auth()->user()->id)->get();



        foreach ($data as $dt) {

            $created_at = new \Carbon($dt->created_at);

            $dt['date'] = $created_at->isoFormat('ddd, D MMM Y');

        }



        return $data;

    }



    private function get_airports() {

        $data = AirlineAirports::with('user')->where('user_id', auth()->user()->id)->get();

        return $data;

    }





    private function get_airline_agents()

    {

        $data = AirlineAgent::with('user','flights')

            ->where('user_id', auth()->user()->id)->get();



        foreach ($data as $dt) {

            $created_at = new \Carbon($dt->created_at);

            $dt['date'] = $created_at->isoFormat('ddd, D MMM Y');

        }



        return $data;

    }



    private function get_accounts_income_expense_type()

    {

        $businessId = request()->session()->get('business.id');



        $data = AccountType::where('business_id', $businessId)

            ->whereIn('name', ['Income', 'Expenses'])

            ->get();



        return $data;

    }

    

    private function get_airline_additional_service(){

        $data = AdditionalService::where('user_id', auth()->user()->id)

            ->get();



        return $data;

    }

    

    private function get_accounts($selected_types)

    {

        $businessId = request()->session()->get('business.id');



        $data = Account::with('account_type', 'creator')

            ->where('business_id', $businessId)

            ->whereIn('account_type_id', $selected_types)

            ->get();



        foreach ($data as $dt) {

            $created_at = new \Carbon($dt->created_at);

            $dt['date'] = $created_at->isoFormat('ddd, D MMM Y');

        }



        return $data;

    }

    // AIRLINE



    /**

     * create a new airline no.

     */

    public function store_airline()

    {

        request()->validate([

            'airline' => 'required|string:min:3',

        ]);

        try {



            $airline = Airline::create([

                'airline' => request('airline'),

                'user_id' => auth()->user()->id

            ]);



            $response = [

                'statusText' => 'Success',

                'message' => 'Successfully Saved',

                'data' => $airline

            ];



            return response()->json($response, Response::HTTP_OK);

        } catch (QueryException $e) {

            return response()->json([

                'errors' => 'Failed ' . end($e->errorInfo)

            ], Response::HTTP_BAD_REQUEST);

        }

    }



    /**

     * update a new airline no.

     */

    public function edit_airline()

    {

        request()->validate([

            'id' => 'required',

            'airline' => 'required|string:min:3',

        ]);



        try {



            $prefix = Airline::where('user_id', auth()->user()->id)

                ->findOrFail(request('id'));



            $prefix->update([

                'airline' => request('airline'),

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

     * delete a new airline no.

     */

    public function delete_airline()

    {

        try {



            $prefix = Airline::where('user_id', auth()->user()->id)

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



    // PREFIX & STARTING NO



    /**

     * create a new prefix & starting no.

     */

    public function store_multiple_prefix()

    {

        request()->validate([

            'data' => 'array',

        ]);

        try {

            $data = [];

            foreach (request('data') as $dt ) {

                $data[] = $this->store_prefix($dt);

            }



            $response = [

                'statusText' => 'Success',

                'data' => $data

            ];



            return response()->json($response, Response::HTTP_OK);

        } catch (QueryException $e) {

            return response()->json([

                'errors' => 'Failed ' . end($e->errorInfo)

            ], Response::HTTP_BAD_REQUEST);

        }

    }



    private function store_prefix($data)

    {

        try {



           $mode = new AirlinePrefixStartingMode  ;

           $mode->name = $data['mode_name'];

           $mode->user_id = auth()->user()->id;



           $mode->save();



            return AirlinePrefixStarting::create([

                'user_id' => auth()->user()->id,

                'mode_id' => $mode->id,

                'value' => $data['value'],

                'status' => 1

            ]);

        } catch (QueryException $e) {

            return response()->json([

                'errors' => 'Failed ' . end($e->errorInfo)

            ], Response::HTTP_BAD_REQUEST);

        }

    }



    // /**

    //  * update prefix & starting no.

    //  */

    // public function edit_prefix()

    // {

    //     request()->validate([

    //         'id' => 'required',

    //         'mode_id' => 'required',

    //         'value' => 'required|string'

    //     ]);



    //     try {



    //         $prefix = AirlinePrefixStarting::where('user_id', auth()->user()->id)

    //             ->findOrFail(request('id'));



    //         $prefix->update([

    //             'mode_id' => request('mode_id'),

    //             'value' => request('value')

    //         ]);



    //         $response = [

    //             'statusText' => 'Success',

    //             'data' => $prefix

    //         ];



    //         return response()->json($response, Response::HTTP_OK);

    //     } catch (QueryException $e) {

    //         return response()->json([

    //             'errors' => 'Failed ' . end($e->errorInfo)

    //         ], Response::HTTP_BAD_REQUEST);

    //     }

    // }



    /**

     * update status of prefix & starting no.

     */

    public function update_status_prefix()

    {

        request()->validate([

            'id' => 'required',

            'status' => 'required',

        ]);



        try {



            $prefix = AirlinePrefixStarting::where('user_id', auth()->user()->id)

                ->findOrFail(request('id'));



            $prefix->update([

                'status' => request('status')

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

    public function delete_prefix()

    {

        try {



            $prefix = AirlinePrefixStarting::where('user_id', auth()->user()->id)

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



    // AGENT



    /**

     * create a new prefix & starting no.

     */

    public function store_agent()

    {

        request()->validate([

            'agent' => 'required|string:min:3',

        ]);



        try {



            $prefix = AirlineAgent::create([

                'agent' => request('agent'),

                'user_id' => auth()->user()->id

            ]);



            $response = [

                'statusText' => 'Success',

                'message' => 'Successfully Saved',

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

     * update a new prefix & starting no.

     */

    public function edit_agent()

    {

        request()->validate([

            'id' => 'required',

            'agent' => 'required|string:min:3',

        ]);



        try {



            $prefix = AirlineAgent::where('user_id', auth()->user()->id)

                ->findOrFail(request('id'));



            $prefix->update([

                'agent' => request('agent'),

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

    public function delete_agent()

    {

        try {



            $prefix = AirlineAgent::where('user_id', auth()->user()->id)

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



    /**

     * get airports

     */

    public function get_airport_table() {

        if(request()->ajax()) {

            try {

                $airports = AirlineAirports::all();

                if (!empty(request()->start_date) && !empty(request()->end_date)) {

                    $start = request()->start_date;

                    $end =  request()->end_date;

                    $airports = $airports->where('date_added', '>=', $start)

                        ->where('date_added', '<=', $end);

                }



                if (!empty(request()->country)) {

                    $country = request()->country;

                    $airports = $airports->where('country', '=', $country);

                }



                if (!empty(request()->province)) {

                    $province = request()->province;

                    $airports = $airports->where('province', 'LIKE', $province);

                }



                return DataTables::of($airports)

                    ->addColumn('airport_status', function($row) {

                        $html = '<h4>';

                        $html .= '<span class="label ' . ($row->status ? 'label-success': 'label-danger') . '">' . ($row->status ? 'Active' : 'InActive') . '</span>';

                        $html .= '</h4>';

                        return $html;

                    })

                    ->addColumn('action', function($row) {

                        $html = '<h4>';

                        $html .= '<button class="btn btn-sm label label-warning ' . ($row->status ? 'disable' : 'enable') . '" data-airport_name="' . $row->airport_name . '" data-id="' . $row->id . '" data-value="' . $row->status . '">' . ($row->status ? 'Disable' : 'Enable') . '</button>';

                        $html .= '<button  data-href="' . action('\Modules\Airline\Http\Controllers\AirlineSettingController@create_edit_airport', [$row->id]) . '" class="btn-modal btn btn-sm label label-primary" data-container="#airport_form_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</button>';

                        // $html .= '<button class="btn btn-sm label label-danger delete" style="margin-left: 4px" data-airport_name="' . $row->airport_name . '" data-id="' . $row->id . '" >Delete</button>';

                        $html .= '</h4>';

                        return $html;

                    })

                    ->addColumn('username', function ($row) {

                        return $row->user ? $row->user->username : null;

                    })

                    ->rawColumns(['action', 'airport_status'])

                    ->make(true);



            } catch (QueryException $e) {

                return response()->json([

                    'errors' => 'Failed ' . end($e->errorInfo)

                ], Response::HTTP_BAD_REQUEST);

            }        

        }

        return null;

    }



    

    function create_edit_airport($id = null){

        $countries = Country::pluck('country','country');

        if($id){

            $airport = AirlineAirports::find($id);

            return view('airline::airport.edit',compact('countries','airport'));

        }

        return view('airline::airport.create',compact('countries'));

        

    }

    /**

     * add airport

     */

    public function store_airport()

    {

        request()->validate([

            'airport_name' => 'string:min:3',

        ]);

        try {



            $airport = AirlineAirports::create([

                'date_added' => \DateTime::createFromFormat('m/d/Y', request('date_added')),

                'country' => request('country_select'),

                'province' => request('province_select'),

                'airport_name' => request('airport_name'),

                'status' => 1,

                'status_name' => 'inactive',

                'user_id' => auth()->user()->id

            ]);



            $response = [

                'statusText' => 'Success',

                'message'=> 'Successfully Saved',

                'data' => $airport

            ];



            return response()->json($response, Response::HTTP_OK);



        } catch (QueryException $e) {

            return response()->json([

                'errors' => 'Failed ' . end($e->errorInfo),

                'message' => 'Server Error Occured'

            ], Response::HTTP_BAD_REQUEST);

        }

    }



     /**

     * edit airport

     */

    public function edit_airport()

    {

        request()->validate([

            'id' => 'required',

            'airport_name' => 'required|string:min:3',

        ]);



        try {



            $prefix = AirlineAirports::where('user_id', auth()->user()->id)

                ->findOrFail(request('id'));



            $prefix->update([

                'date_added' => \DateTime::createFromFormat('m/d/Y', request('date_added')),

                'country' => request('country_select'),

                'province' => request('province_select'),

                'airport_name' => request('airport_name'),

            ]);



            $response = [

                'statusText' => 'Success',

                'message'=> 'Successfully Updated',

                'data' => $prefix

            ];



            return response()->json($response, Response::HTTP_OK);

        } catch (QueryException $e) {

            return response()->json([

                'errors' => 'Failed ' . end($e->errorInfo),

                'message' => 'Server Error Occured'



            ], Response::HTTP_BAD_REQUEST);

        }

    }



    /**

     * update status

     */

    public function update_status_airport()

    {

        request()->validate([

            'id' => 'required',

            'status' => 'required',

        ]);



        try {



            $airport = AirlineAirports::where('user_id', auth()->user()->id)

                ->findOrFail(request('id'));



            $airport->update([

                'status' => request('status')

            ]);



            $response = [

                'statusText' => 'Success',

                'message' => 'Successfully Updated',

                'data' => $airport

            ];



            return response()->json($response, Response::HTTP_OK);

        } catch (QueryException $e) {

            return response()->json([

                'errors' => 'Failed ' . end($e->errorInfo),

                'message' => 'Server Error Occured'

            ], Response::HTTP_BAD_REQUEST);

        }

    }



    /**

     * delete a airport

     */

    public function delete_airport() {

        request()->validate([

            'id' => 'required',

        ]);



        try {



            $prefix = AirlineAirports::where('user_id', auth()->user()->id)

                ->findOrFail(request('id'));



            $prefix->delete();



            $response = [

                'statusText' => 'Success',

                'message' => 'Successfully Deleted'

            ];



            return response()->json($response, Response::HTTP_OK);



        } catch (QueryException $e) {

            return response()->json([

                'errors' => 'Failed ' . end($e->errorInfo),

                'message' => 'Server Error Occured'

            ], Response::HTTP_BAD_REQUEST);

        }        

    }



   

}

