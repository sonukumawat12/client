<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\Ticket;
use Modules\HelpGuide\Models\Statistic;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\HelpGuide\Http\Controllers\Controller;

class StatisticsController extends Controller
{

    public function ticketsOverview()
    {
      // // $this->authorize('view', Statistic::class);
      return Ticket::overview();
    }

    public function ticketsCreatedMonthly()
    {

      $tickets = Ticket::where('created_at', '>=', Carbon::now()->subDays(7))
      ->groupBy('date')
      ->orderBy('date', 'DESC')
      ->get([
          DB::raw('DATE(created_at) as date'),
          DB::raw('COUNT(*) as "tickets"')
      ])->pluck('tickets','date')->toArray();

    }
}
