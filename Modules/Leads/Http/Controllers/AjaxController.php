<?php

namespace Modules\Leads\Http\Controllers;

use App\Category;
use App\User;
use App\Utils\ModuleUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Leads\Entities\Lead;
use Illuminate\Support\Facades\DB;
use Modules\Leads\Entities\Town;
use Illuminate\Database\Query\JoinClause;

class AjaxController extends Controller
{
    protected $moduleUtil;
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil =  $moduleUtil;
    }

    public function ajax_mobile(Request $request)
    {
        if ($request->ajax()) {
            if ($request->post('postData')) {

                $result = DB::table('leads')
                    ->join('leads_categories', 'leads_categories.id', '=', 'leads.category_id')
                    ->where('leads.mobile_no_1', $request->post('postData'))
                    ->orWhere('leads.mobile_no_2', $request->post('postData'))
                    ->get();

                if (count($result) > 0) {
                    return $result[0];
                } else {
                    return false;
                }
            }else{
                return false;
            }
        }
    }
    
    public function ajax_town(Request $request){
        if($request->ajax()){
            if($request->post('postData')){
                $dis_id = DB::table('districts')->select('name', 'id')->where('name', $request->post('postData'))->pluck('id')->toArray();
                $towns = DB::table('towns')->select('name', 'id')->where('district_id', $dis_id[0])->get();
                
                return $towns;
            }
        }
    }
    
     public function ajax_district(Request $request){
        if($request->ajax()){
            if($request->post('postData')){
                $towns = DB::table('districts')->select('name', 'id')->where('country_id', $request->post('postData'))->get();
                
                return $towns;
            }
        }
    }
}
