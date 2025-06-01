<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Crm\Entities\CrmCallLog;
use Modules\Crm\Entities\CrmContact;
use Modules\Crm\Entities\Schedule;
use Modules\Crm\Utils\CrmUtil;

class CreDashboardController extends Controller
{
    public function index()
    {
        $data = [
            'sources' => Source::pluck('name', 'id'), 
            'users' => User::pluck('name', 'id'),     
        ];

        
        return view('crm::cre_dashboard', $data); 
        }

    public function store(Request $request)
    {
     
        return redirect()->route('crm.dashboard')->with('success', 'Data saved successfully!');
    }
}
