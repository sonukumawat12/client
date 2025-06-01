<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomerSmsSetting;
use Yajra\DataTables\Facades\DataTables;
use App\BusinessLocation;

class CustomerSmsSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $crm_group =CustomerSmsSetting::leftjoin('business_locations','business_locations.id','customer_sms_settings.location_id')->leftjoin('users','users.id','customer_sms_settings.created_by')->where('customer_sms_settings.business_id', $business_id)->select('users.username', 'customer_sms_settings.*','business_locations.name as lname')->orderBy('id','DESC');

            return Datatables::of($crm_group)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'CustomerSmsSettingController@edit\', [$id])}}" class="btn btn-xs btn-primary crm_group_edit_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                     <button data-href="{{action(\'CustomerSmsSettingController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_crm_group_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                      '
                )

                ->removeColumn('id')
                ->editColumn('date_time','{{@format_datetime($date_time)}}')
                ->editColumn('show_customer','{{$show_customer == 1 ? "Yes" : "No"}}')
                ->editColumn('show_supplier','{{$show_supplier == 1 ? "Yes" : "No"}}')
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_locations = BusinessLocation::where('business_id', request()->session()->get('user.business_id'))->pluck('name', 'id');
        return view('customer_sms_settings.create')
                ->with(compact('business_locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        

        try {
            $input = $request->only(['location_id','show_customer','show_supplier']);
            $input['date_time'] = date('Y-m-d H:i:s');
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            $customer_group = CustomerSmsSetting::create($input);
            $output = [
                            'success' => true,
                            'msg' => __("lang_v1.success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        if (request()->ajax()) {
            $business_locations = BusinessLocation::where('business_id', request()->session()->get('user.business_id'))->pluck('name', 'id');
            $data = CustomerSmsSetting::findOrFail($id);

            return view('customer_sms_settings.edit')
                ->with(compact('data','business_locations'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        

        if (request()->ajax()) {
            try {
                $data = CustomerSmsSetting::findOrFail($id);
                
                $input = $request->only(['location_id','show_customer','show_supplier']);
                $input['date_time'] = date('Y-m-d H:i:s');
                $input['business_id'] = $data->business_id;
                $input['created_by'] = $request->session()->get('user.id');


                $customer_group = CustomerSmsSetting::create($input);

                $output = ['success' => true,
                            'msg' => __("lang_v1.success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        

        if (request()->ajax()) {
            try {
                
                $cg = CustomerSmsSetting::findOrFail($id);
                $cg->delete();

                $output = ['success' => true,
                            'msg' => __("lang_v1.success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }
}
