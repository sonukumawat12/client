<?php

namespace Modules\Crm\Http\Controllers;



use App\Category;
use App\Contact;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Modules\Crm\Entities\CrmContact;
use Modules\Crm\Entities\Schedule;
use Modules\Crm\Utils\CrmUtil;
use Yajra\DataTables\Facades\DataTables;

class CrmDashboardController extends Controller
{
    public function index()
    {
       
        $statuses = []; // Replace with actual data
        $todays_followups = 0; 
        $my_leads = 0;
        $my_conversion = 0;

        // Pass all variables to the view  /Crm/Resources/views/crm_dashboard/index
        return view('crm::crm_dashboard.index', compact('statuses', 'todays_followups', 'my_leads', 'my_conversion'));
    }
}
