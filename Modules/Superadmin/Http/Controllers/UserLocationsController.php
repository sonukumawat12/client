<?php

namespace Modules\Superadmin\Http\Controllers;


use Illuminate\Routing\Controller;
use Yajra\DataTables\DataTables;
use App\UserLocation;

class UserLocationsController extends Controller
{
    
    function index(){
        $access_types = [
                'user_login' => __('superadmin::lang.user_locations_user_login'), 
                'member_register' => __('superadmin::lang.user_locations_member_reg'), 
            ];
        
        $countries = (Array) UserLocation::select('country')->distinct()->get()->toArray();
        $countries = $countries ? array_combine( $countries[0], $countries[0]):[];
        
        $states = (Array) UserLocation::select('state')->distinct()->get()->toArray();
        $states = $states ? array_combine( $states[0], $states[0]):[];
        
        $cities = (Array) UserLocation::select('city')->distinct()->get()->toArray();
        $cities = $cities ? array_combine( $cities[0], $cities[0]):[];
        
        $districts = (Array) UserLocation::select('district')->distinct()->get()->toArray();
        $districts = $districts ? array_combine( $districts[0], $districts[0]):[];
        
        if (request()->ajax()) {
            $location = UserLocation::join('users', 'user_locations.user_id', '=', 'users.id')
                ->select('user_locations.*', 'users.first_name', 'users.last_name');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $location->where('user_locations.access_time', '>=', strtotime(request()->start_date));
                $location->where('user_locations.access_time', '<=', strtotime(request()->end_date));
            }
            if (!empty(request()->access_type)) {
                $location->where('user_locations.access_type', request()->access_type);
            }
            
            return DataTables::of($location->get())
                ->editColumn('date_time', '{{ date("d-m-Y H:i", $access_time) }}')
                ->editColumn('name', '{{ ucfirst($first_name) }} {{ ucfirst($last_name) }}')
                ->editColumn('access_type', function($row) use ($access_types){
                    return isset($access_types[ $row->access_type ]) ? $access_types[ $row->access_type ]:$row->access_type;
                })
                ->removeColumn('id')
                ->make(true);
            
        }
        
        return view('superadmin::userlocations.index')
            ->with(compact( 'access_types', 'countries', 'states', 'cities', 'districts' ))
            ;
    }

}

