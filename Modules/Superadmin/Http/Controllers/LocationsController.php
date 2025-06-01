<?php

namespace Modules\Superadmin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Superadmin\Entities\Areas;
use Modules\Superadmin\Entities\Country;
use Modules\Superadmin\Entities\Province;
use Modules\Leads\Entities\District;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;


class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        return view('superadmin::superadmin_settings.locations.index');
    }

    public function addCountry()
    {
        if (request()->isMethod('post')) {
            $data = request()->only(['country','country_code','currency_code']);
            $rules = [
                'country' => 'required|unique:countries,country',
            ];
            $valid = Validator::make($data, $rules);
            if (count($valid->errors())) {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.fill_required_fields'),
                ];
                return redirect()->back()->with('status', $output);
            }

          $country = DB::table('countries')->insert($data);
            if ($country) {
                $output = [
                    'success' => true,
                    'msg' => __('dsr::lang.country_added_successfully')
                ];
                return redirect()->back()->with('status', $output);
            } else {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.something_went_wrong'),
                ];
                return redirect()->back()->with('status', $output);
            }
        }
        return view('superadmin::superadmin_settings.locations.countries.add');

    }

    public function countries()
    {
        if (request()->ajax()) {
            $countries = DB::table('countries')->select('countries.*');
            return DataTables::of($countries)
                ->make(true);
        }
    }

    public function provinces()
    {
        if (request()->ajax()) {
            $provinces = DB::table('provinces')
                ->leftJoin('countries', 'countries.id', '=', 'provinces.country_id')
                ->select('provinces.*','provinces.name as province','countries.country')
                ->orderBy('provinces.id','desc')
            ;
            return DataTables::of($provinces)
                ->make(true);
        }

    }

    public function addProvince()
    {
        if (request()->isMethod('post')) {
            $data = request()->only(['name','country_id']);
            $rules = [
                'name' => 'required|unique:provinces,name',
            ];
            $valid = Validator::make($data, $rules);
            if (count($valid->errors()) > 0) {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.fill_required_fields'),
                ];
                return redirect()->back()->with('status', $output);
            }

          $province = Province::create($data);
            if ($province) {
                $output = [
                    'success' => true,
                    'msg' => __('dsr::lang.province_added_successfully')
                ];
                return redirect()->back()->with('status', $output);
            } else {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.something_went_wrong'),
                ];
                return redirect()->back()->with('status', $output);
            }
        }
        $countries = DB::table('countries')->orderBy('country')->pluck('country', 'id')->toArray();
        return view('superadmin::superadmin_settings.locations.provinces.add',compact('countries'));
    }

    public function districts() {
        if (request()->ajax()) {
            $districts = DB::table('districts')
                ->leftJoin('provinces', 'provinces.id', '=', 'districts.province_id')
                ->leftJoin('countries', 'countries.id', '=', 'provinces.country_id')
                ->select('districts.*','districts.name as district','provinces.name as province','countries.country')
                ->orderBy('districts.id','desc');
            return DataTables::of($districts)
                ->make(true);
        }
    }

    public function addDistrict()
    {
        if (request()->isMethod('post')) {
            $data = request()->only(['name','province_id']);
            $rules = [
                'name' => 'required|unique:districts,name',
            ];
            $valid = Validator::make($data, $rules);
            if (count($valid->errors()) > 0) {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.fill_required_fields'),
                ];
                return redirect()->back()->with('status', $output);
            }
            
            foreach($data['name'] as $one){
                $district =  District::create(['province_id' => $data['province_id'],'name' => $one]);
            }

          
            if ($district) {
                $output = [
                    'success' => true,
                    'msg' => __('dsr::lang.district_added_successfully')
                ];
                return redirect()->back()->with('status', $output);
            } else {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.something_went_wrong'),
                ];
                return redirect()->back()->with('status', $output);
            }
        }
        $countries = DB::table('countries')->orderBy('country')->pluck('country', 'id')->toArray();
        $provinces = DB::table('provinces')->orderBy('name')->pluck('name', 'id')->toArray();
        return view('superadmin::superadmin_settings.locations.districts.add',compact('provinces','countries'));
    }
    public function getProvinces($country_id ) {

        $provinces = DB::table('provinces')->where('country_id',$country_id)->pluck('name', 'id')->toArray();
        $select = '<select class="form-control" id="province_id" name="province_id">';
        $select .= '<option value="" selected>Select Province</option>';
        foreach ($provinces as $key => $value) {
            $select .= '<option value="'.$key.'">'.$value.'</option>';
        }
        $select .= '</select>';
        return $select;
    }
    public function getDistricts($province_id) {

            $districts = DB::table('districts')->where('province_id',$province_id)->pluck('name', 'id')->toArray();
            $select = '<select class="form-control" id="district_id" name="district_id">';
            $select .= '<option value="" selected>Select District</option>';
            foreach ($districts as $key => $value) {
                $select .= '<option value="'.$key.'">'.$value.'</option>';
            }
            $select .= '</select>';
            return $select;
    }
    public function getAreas()
    {
        $areas = DB::table('areas')->where('district_id',request()->district_id)->pluck('name', 'id')->toArray();
        $select = '<select class="form-control" id="areas" name="areas">';
        foreach ($areas as $key => $value) {
            $select .= '<option value="'.$key.'">'.$value.'</option>';
        }
        $select .= '</select>';
        return $select;

    }
    public function areas()
    {
        if (request()->ajax()) {
            $areas = DB::table('areas')
                ->leftJoin('districts', 'districts.id', '=', 'areas.district_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'districts.province_id')
                ->leftJoin('countries', 'countries.id', '=', 'provinces.country_id')
                ->select('areas.*','areas.name as area','districts.name as district','provinces.name as province','countries.country')
                ->orderBy('areas.id','desc')
            ;
            return DataTables::of($areas)
                ->make(true);
        }

    }

    public function addarea()
    {
        if (request()->isMethod('post')) {
            $data = request()->only(['name','district_id']);
            $rules = [
                'name' => 'required|unique:areas,name',
            ];
            $valid = Validator::make($data, $rules);
            if (count($valid->errors()) > 0) {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.fill_required_fields'),
                ];
                return redirect()->back()->with('status', $output);
            }
            
            foreach($data['name'] as $one){
                $areas =  DB::table('areas')->insert(['district_id' => $data['district_id'],'name' => $one]);
            }
              
            if ($areas) {
                $output = [
                    'success' => true,
                    'msg' => __('dsr::lang.area_added_success')
                ];
                return redirect()->back()->with('status', $output);
            } else {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.something_went_wrong'),
                ];
                return redirect()->back()->with('status', $output);
            }
        }
        $countries = DB::table('countries')->orderBy('country')->pluck('country', 'id')->toArray();        $provinces = DB::table('provinces')->pluck('name', 'id')->toArray();
        $districts = DB::table('districts')->orderBy('name')->pluck('name', 'id')->toArray();
        return view('superadmin::superadmin_settings.locations.areas.add',compact('provinces','countries','districts'));
    }
}
