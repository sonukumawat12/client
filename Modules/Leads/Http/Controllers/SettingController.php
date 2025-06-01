<?php

namespace Modules\Leads\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Leads\Entities\District;
use Modules\Leads\Entities\LeadsCategory;
use Modules\Leads\Entities\LeadsSettings;
;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $districts = District::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::where('business_id', $business_id)->pluck('username', 'id');
        $categories = LeadsCategory::where('business_id', $business_id)->pluck('name', 'id');

        return view('leads::settings.index')->with(compact('districts', 'users', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('leads::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        
        try {
            $data = $request->except('_token');
            $data['date'] = \Carbon::parse($request->get('date'))->format('Y-m-d');
            $data['quotations'] = $request->get('quotations');
            $data['sales_inv'] = $request->get('sales_inv');
            $data['clients_res'] = $request->get('clients_res');
            $data['action'] = $request->get('action');
            $data['user'] = $request->get('user');
            LeadsSettings::create($data);

            $output = [
                'success' => true,
                'msg' => "Leads settings are created!"
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('leads::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('leads::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
