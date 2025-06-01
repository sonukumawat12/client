<?php

namespace Modules\Superadmin\Http\Controllers;

use Modules\Superadmin\Entities\SmsApiClient;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

use Modules\Superadmin\Entities\SmsRefillPackage;
use App\Business;
use Illuminate\Support\Str;


class SmsApiClientController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
       if (request()->ajax()) {
            
            $drivers = SmsApiClient::all();
            
            
            return DataTables::of($drivers)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\SmsApiClientController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\SmsApiClientController@destroy', [$row->id]) . '" class="delete_record"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                       
                        
                        return $html;
                    }
                )
                ->editColumn('date','{{ @format_date($date) }}')
                ->make(true);
        }
        
    }
    
    
    public function create()
    {
        $data = array();
        $api_key = Str::uuid();
        
        return view('superadmin::sms_api_clients.create')
                ->with(compact('data','api_key'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'username' => 'required|unique:sms_api_clients,username', 
        ];
    
        $messages = [
            'username.unique' => 'The username has already been taken.',
        ];
    
        // Perform validation
        $validator = \Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->first(),
                'tab' => 'external_api_clients'
            ];
            return redirect()->back()->with('status', $output);
        }
    
        
        try {
            							
            $data = $request->except('_token');
            
            SmsApiClient::create($data);
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'tab' => 'external_api_clients'
            ];
            
        } catch (\Exception $e) {

            logger($e);
            
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong'),
                'tab' => 'external_api_clients'

            ];

        }
        

        return redirect()->back()->with('status', $output);
    }
    
    public function edit($id)
    {
    
        $data = SmsApiClient::findOrFail($id);
        return view('superadmin::sms_api_clients.edit')
                ->with(compact('data'));
    }

   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $rules = [
            'username' => 'required|unique:sms_api_clients,username,' . $id, 
        ];
    
        $messages = [
            'username.unique' => 'The username has already been taken.',
        ];
    
        // Perform validation
        $validator = \Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->first(),
                'tab' => 'external_api_clients'
            ];
            return redirect()->back()->with('status', $output);
        }
        
        
        try {
            $data = $request->except('_token','_method');
            
            SmsApiClient::where('id',$id)->update($data);
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'tab' => 'external_api_clients'
            ];
            
        } catch (\Exception $e) {

            logger($e);
            
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong'),
                'tab' => 'external_api_clients'

            ];

        }
        

        return redirect()->back()->with('status', $output);;
    }
    
    public function destroy($id)
    {
        try {
            
            SmsApiClient::where('id', $id)->delete();


            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
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
}
