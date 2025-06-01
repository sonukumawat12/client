<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;
use App\UserStorePermission;
use App\User;

use App\BusinessLocation;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StoreController extends Controller
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

            $stores = Store::where('stores.business_id', $business_id)
                ->join('business_locations', 'stores.location_id', 'business_locations.id')
                ->select('stores.*', 'business_locations.location_id as location_id', 'business_locations.name as location_name');

            //Add condition for location,used in sales representative expense report & list of expense
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $stores->where('stores.location_id', $location_id);
                }
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $stores->whereIn('location_id', $permitted_locations);
            }


            return Datatables::of($stores)
                ->addColumn(
                    'action',
                    
                    '<div class="btn-group">
                        <button data-href="{{action(\'StoreController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_store_button" {{$is_main == 1 ? "disabled" : ""}}><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        <a data-href="{{action(\'StoreController@destroy\', [$id])}}" class="delete_store" {{$is_main == 1 ? "hidden" : ""}}><i class="glyphicon glyphicon-trash" style="color:brown;"></i> @lang("messages.delete")</a>
                    </div>'
                )
                ->editColumn(
                    'status',
                    function ($row) {
                        $html = '';
                        if ($row->status == 1) {
                            $html = 'Active';
                        } else {
                            $html = 'Not Active';
                        }
                        return $html;
                    }

                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $locations = DB::table('business_locations')->where('business_id', $business_id)->get();
        return view('stores.index')
            ->with(compact('locations', 'business_locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('store.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('stores.create')
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
        if (!auth()->user()->can('store.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['location_id', 'name', 'contact_number', 'address']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['status'] = !empty($request->input('status')) ? $request->input('status') : 0;

            $store = Store::create($input);
            $output = [
                'success' => true,
                'data' => $store,
                'msg' => __("store.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('store.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $store = Store::where('business_id', $business_id)->find($id);

            $business_locations = BusinessLocation::forDropdown($business_id, true);

            return view('stores.edit')
                ->with(compact('store', 'business_locations'));
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
        if (!auth()->user()->can('store.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['location_id', 'name', 'contact_number', 'address']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['status'] = !empty($request->input('status')) ? $request->input('status') : 0;

            $store = Store::where('id', $id)->update($input);
            $output = [
                'success' => true,
                'data' => $store,
                'msg' => __("store.update_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('store.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $store = Store::where('business_id', $business_id)->where('id', $id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("store.delete_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
    
    public function destroyPermission($id)
    {
        if (!auth()->user()->can('store.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $store = UserStorePermission::where('business_id', $business_id)->where('id', $id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("store.delete_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    public function locationHasStoreCount($location_id){
        $business_id = request()->user()->business_id;

        $count = Store::where('business_id', $business_id)->where('location_id', $location_id)->count();

        return ['count' => $count];
    }
    
    public function fetchUserStorePermissions(){
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $stores = UserStorePermission::where('stores.business_id', $business_id)
                ->join('stores', 'stores.id', 'user_store_permissions.store_id')
                ->leftjoin('users as user','user.id','user_store_permissions.user_id')
                ->leftjoin('users as created','created.id','user_store_permissions.created_by')
                ->select('user_store_permissions.*', 'stores.name as store', 'user.username as username','created.username as created_by');


            return Datatables::of($stores)
                ->addColumn(
                    'action',
                    
                    '<div class="btn-group">
                        <button data-href="{{action(\'StoreController@editUserPermission\', [$id])}}" class="btn btn-xs btn-primary edit_store_button" ><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        <a data-href="{{action(\'StoreController@destroyPermission\', [$id])}}" class="delete_store"><i class="glyphicon glyphicon-trash" style="color:brown;"></i> @lang("messages.delete")</a>
                    </div>'
                )
                ->editColumn(
                    'sell',
                    function ($row) {
                        $html = '';
                        if ($row->sell == 1) {
                            $html = '<span class="badge bg-success">Active</span>';
                        } else {
                            $html = '<span class="badge bg-danger">Not Active</span>';
                        }
                        return $html;
                    }

                )
                ->editColumn(
                    'purchase',
                    function ($row) {
                        $html = '';
                        if ($row->purchase == 1) {
                            $html = '<span class="badge bg-success">Active</span>';
                        } else {
                            $html = '<span class="badge bg-danger">Not Active</span>';
                        }
                        return $html;
                    }

                )
                
                ->editColumn(
                    'stores_transfer',
                    function ($row) {
                        $html = '';
                        if ($row->stores_transfer == 1) {
                            $html = '<span class="badge bg-success">Active</span>';
                        } else {
                            $html = '<span class="badge bg-danger">Not Active</span>';
                        }
                        return $html;
                    }

                )
                
                ->editColumn(
                    'stock_adjustment',
                    function ($row) {
                        $html = '';
                        if ($row->stock_adjustment == 1) {
                            $html = '<span class="badge bg-success">Active</span>';
                        } else {
                            $html = '<span class="badge bg-danger">Not Active</span>';
                        }
                        return $html;
                    }

                )
                
                ->editColumn(
                    'sell_return',
                    function ($row) {
                        $html = '';
                        if ($row->sell_return == 1) {
                            $html = '<span class="badge bg-success">Active</span>';
                        } else {
                            $html = '<span class="badge bg-danger">Not Active</span>';
                        }
                        return $html;
                    }

                )
                
                ->editColumn('created_at', '{{@format_date($created_at)}}')
                
                ->removeColumn('id')
                ->rawColumns(['action','sell','purchase','stores_transfer','stock_adjustment','sell_return'])
                ->make(true);
        }

        $business_id = request()->session()->get('user.business_id');

        $stores = Store::forDropdown($business_id, true);
        $users = User::forDropdown($business_id);
        
        return view('store_permissions.index')
            ->with(compact('users', 'stores'));
    }
    
    
    public function createStorePermission()
    {
        if (!auth()->user()->can('store.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $stores = Store::forDropdown($business_id, true);
        $users = User::forDropdown($business_id);

        return view('store_permissions.create')
            ->with(compact('users', 'stores'));
    }
    
    public function storeUserPermission(Request $request)
    {
        if (!auth()->user()->can('store.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['store_id', 'user_id', 'sell', 'purchase','stores_transfer','stock_adjustment','sell_return']);
            
            foreach (['sell', 'purchase', 'stores_transfer', 'stock_adjustment', 'sell_return'] as $field) {
                if (!isset($input[$field])) {
                    $input[$field] = 0;
                }
            }
            
            
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            $store = UserStorePermission::updateOrCreate(['store_id' => $input['store_id'],'user_id' => $input['user_id']], $input);
            $output = [
                'success' => true,
                'data' => $store,
                'msg' => __("messages.success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function editUserPermission($id)
    {
        if (!auth()->user()->can('store.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $store = UserStorePermission::where('business_id', $business_id)->find($id);

           

            $stores = Store::forDropdown($business_id, true);
            $users = User::forDropdown($business_id);
    
            return view('store_permissions.edit')
                ->with(compact('users', 'stores','store'));
        }
    }
    
}
