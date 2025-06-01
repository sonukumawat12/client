<?php

namespace Modules\Subscription\Http\Controllers;


use App\Utils\ModuleUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Modules\Subscription\Entities\SubscriptionUserActivity;

use Yajra\DataTables\DataTables;

class SubscriptionUserActivityController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        
        if (request()->ajax()) {
            $settings = SubscriptionUserActivity::leftjoin('users','users.id','subscription_user_activities.created_by')->where('subscription_user_activities.business_id', $business_id)->orderBy('subscription_user_activities.id','DESC')->select('users.username as user','subscription_user_activities.*')->get();

            return DataTables::of($settings)
                
                 ->editColumn('model', function($setting) {
                    return "Modules\Subscription\Entities\\" . $setting->model;
                })
                ->editColumn('properties','{!! nl2br($properties) !!}')
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->removeColumn('id')
                ->rawColumns(['action','properties'])
                ->make(true);
        }

        return view('subscription::user_activity.index')->with(compact('business_id'));
    }
    
    
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        
    }
    
    
    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('subscription::user_activity.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
       
    }
}
