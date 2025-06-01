<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Http\Services\LoanService;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('loan::dashboard.index');
    }

    public function get_totals()
    {
        if (request()->ajax()) {
            $output = [];

            $loan_transactions = Loan::getAggregates();
            $output['total_disbursed'] = $loan_transactions->total_disbursed;
            $output['total_repayment'] = $loan_transactions->total_repayment;

            $loan = Loan::with('repayment_schedules')->forBusiness()->get();
            $output['total_arrears'] = $loan->sum('arrears_amount');
            $output['total_outstanding'] = $loan->sum('outstanding_amount');
            $output['total_pending'] = $loan->sum('total_pending');

            //count
            $output['no_loans_pending'] = $loan->whereIn('status', ['pending', 'submitted'])->count();
            $output['no_loans_active'] = $loan->where('status', 'active')->count();
            $output['no_loans_awaiting_disbursement'] = $loan->whereIn('status', 'approved')->count();
            $output['no_loans_not_taken_up'] = LoanService::getLoansNotTakenUp()->count();

            return $output;
        }
    }

    public function get_loans_awaiting_disbursement_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $colors = get_chart_colors();

        $loans = Loan::join('business_locations', 'business_locations.id', '=', 'loans.location_id')
            ->awaitingDisbursement()
            ->where('business_locations.business_id', session('business.id'))
            ->select('loans.created_at')
            ->get();

        $no_loans_awaiting_disbursement = array_map(function ($month) use ($loans) {
            return $loans->whereBetween('created_at', [$month->start, $month->end])->count();
        }, $months);

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => $request->label,
                    'backgroundColor' => $colors->red,
                    'borderColor' => $colors->red,
                    'data' => $no_loans_awaiting_disbursement,
                ]
            ],
        ];

        return json_encode($data);
    }

    public function get_loans_rejected_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $colors = get_chart_colors();

        $loans = Loan::join('business_locations', 'business_locations.id', '=', 'loans.location_id')
            ->rejected()
            ->where('business_locations.business_id', session('business.id'))
            ->select('loans.created_at')
            ->get();

        $no_loans_rejected = array_map(function ($month) use ($loans) {
            return $loans->whereBetween('created_at', [$month->start, $month->end])->count();
        }, $months);

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => $request->label,
                    'backgroundColor' => $colors->red,
                    'borderColor' => $colors->red,
                    'data' => $no_loans_rejected,
                ]
            ],
        ];

        return json_encode($data);
    }

    public function get_principal_projected_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $principal_due_total = LoanService::getRepaymentDataPerMonth($months, 'total_principal');

        $colors = get_chart_colors();

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => trans_choice('accounting::core.principal', 2) . ' ' . trans('loan::general.projected'),
                    'backgroundColor' => $colors->red,
                    'borderColor' => $colors->red,
                    'data' => $principal_due_total,
                ]
            ],
        ];

        return json_encode($data);
    }

    public function get_principal_collected_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $principal_collected_total = LoanService::getRepaymentDataPerMonth($months, 'principal_repaid_derived');

        $colors = get_chart_colors();

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => trans_choice('accounting::core.principal', 2) . ' ' . trans('accounting::core.collected'),
                    'backgroundColor' => $colors->blue,
                    'borderColor' => $colors->blue,
                    'data' => $principal_collected_total,
                ],
            ],
        ];

        return json_encode($data);
    }

    public function get_fees_projected_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $fees_due_total = LoanService::getRepaymentDataPerMonth($months, 'total_fees');

        $colors = get_chart_colors();

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => trans_choice('accounting::core.fee', 2) . ' ' . trans('loan::general.projected'),
                    'backgroundColor' => $colors->red,
                    'borderColor' => $colors->red,
                    'data' => $fees_due_total,
                ]
            ],
        ];

        return json_encode($data);
    }

    public function get_fees_collected_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $fees_collected_total = LoanService::getRepaymentDataPerMonth($months, 'fees_repaid_derived');

        $colors = get_chart_colors();

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => trans_choice('accounting::core.fee', 2) . ' ' . trans('accounting::core.collected'),
                    'backgroundColor' => $colors->blue,
                    'borderColor' => $colors->blue,
                    'data' => $fees_collected_total,
                ],
            ],
        ];

        return json_encode($data);
    }

    public function get_penalties_projected_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $penalties_collected_total = LoanService::getRepaymentDataPerMonth($months, 'total_penalties');

        $colors = get_chart_colors();

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => trans_choice('accounting::core.penalty', 2) . ' ' . trans('accounting::core.collected'),
                    'backgroundColor' => $colors->red,
                    'borderColor' => $colors->red,
                    'data' => $penalties_collected_total,
                ],
            ],
        ];

        return json_encode($data);
    }

    public function get_penalties_collected_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $penalties_collected_total = LoanService::getRepaymentDataPerMonth($months, 'penalties_repaid_derived');

        $colors = get_chart_colors();

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => trans_choice('accounting::core.penalty', 2) . ' ' . trans('accounting::core.collected'),
                    'backgroundColor' => $colors->blue,
                    'borderColor' => $colors->blue,
                    'data' => $penalties_collected_total,
                ],
            ],
        ];

        return json_encode($data);
    }

    public function get_interest_projected_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $interest_collected_total = LoanService::getRepaymentDataPerMonth($months, 'total_interest');

        $colors = get_chart_colors();

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => trans_choice('accounting::core.interest', 1) . ' ' . trans('accounting::core.collected'),
                    'backgroundColor' => $colors->red,
                    'borderColor' => $colors->red,
                    'data' => $interest_collected_total,
                ],
            ],
        ];

        return json_encode($data);
    }

    public function get_interest_collected_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $interest_collected_total = LoanService::getRepaymentDataPerMonth($months, 'interest_repaid_derived');

        $colors = get_chart_colors();

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => trans_choice('accounting::core.interest', 1) . ' ' . trans('accounting::core.collected'),
                    'backgroundColor' => $colors->blue,
                    'borderColor' => $colors->blue,
                    'data' => $interest_collected_total,
                ],
            ],
        ];

        return json_encode($data);
    }

    public function get_total_paid_chart(Request $request)
    {
        $year = $request->year ?: get_default_year();

        $months = get_months($year);

        $interest_collected_total = LoanService::getRepaymentDataPerMonth($months, 'total_paid');

        $colors = get_chart_colors();

        $data = [
            'type' => 'line',
            'chart_id' => $request->chart_id,
            'labels' => array_keys($months),
            'datasets' => [
                [
                    'label' => trans_choice('accounting::core.total', 1) . ' ' . trans('accounting::core.paid'),
                    'backgroundColor' => $colors->blue,
                    'borderColor' => $colors->blue,
                    'data' => $interest_collected_total,
                ],
            ],
        ];

        return json_encode($data);
    }
}
