<?php

namespace Modules\Loan\Http\Services;

use App\BusinessLocation;
use App\Contact;
use Modules\Accounting\Services\FlashService;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Exports\LoanExport;
use PDF;

class LoanReportService extends LoanService
{
    private $start_date;
    private $end_date;
    private $contact;

    public function setStartDate($value)
    {
        $this->start_date = $value;
        return $this;
    }

    public function setEndDate($value)
    {
        $this->end_date = $value;
        return $this;
    }

    public function setContact($value)
    {
        $this->contact = $value;
        return $this;
    }

    public function getAccountStatementData()
    {
        $report_data = []; //What is returned
        $start_date = !empty($this->start_date) ? $this->start_date : request()->start_date;
        $end_date = !empty($this->end_date) ? $this->end_date : request()->end_date;
        $contact_id = !empty($this->contact) ? $this->contact->id : request()->contact_id;
        $location_id = request()->location_id;
        $loan_id = request()->loan_id;

        if (!empty($this->contact)) {
            //Gets details for specific contact
            $report_data['contact'] = $this->contact;
        } else {
            //gets contacts to populate the dropdown
            $report_data['contacts'] = Contact::where('business_id',session('business.id'))->get();
        }

        $loans = Loan::forBusiness()->get();
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        if (!empty($start_date)) {
            $data = LoanTransaction::join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("business_locations", "loans.location_id", "business_locations.id")
                ->join("contacts", "loans.contact_id", "contacts.id")
                // ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                // ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->where('business_locations.business_id', session('business.id'))
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_transactions.submitted_on', [$start_date, $end_date]);
                })
                ->when($contact_id, function ($query) use ($contact_id) {
                    $query->where('loans.contact_id', $contact_id);
                })
                ->when($loan_id, function ($query) use ($loan_id) {
                    $query->where('loans.id', $loan_id);
                })
                ->when($location_id, function ($query) use ($location_id) {
                    $query->where('loans.location_id', $location_id);
                })
                ->where('loan_transactions.reversed', 0)
                ->selectRaw("
                    concat(contacts.name) contact,
                    concat(users.first_name,' ',users.last_name) loan_officer,
                    business_locations.name as business_location,'' as receipt,'' as payment_type,
                    loan_products.name loan_product,
                    loan_transactions.id,
                    loan_transactions.name transaction_type,
                    loan_transactions.amount,
                    loan_transactions.credit,
                    loan_transactions.debit,
                    loan_transactions.amount,
                    loan_transactions.submitted_on,
                    loans.id loan_id,
                    loans.account_number loan_account_number")
                ->orderBy('loan_transactions.submitted_on')
                ->groupBy('loan_transactions.id')
                ->get();
            //check if we should download
            if (request()->download) {
                if (!empty($data->first())) {
                    $view = view('loan::report.account_statement_pdf', compact('start_date', 'end_date', 'location_id', 'data', 'business_locations'));
                    $file_name = $data->first()->contact . ' ' . trans_choice('accounting::core.account_statement', 1) . '(' . $start_date . ' to ' . $end_date . ')';

                    if (request()->type == 'pdf') {
                        $pdf = PDF::loadView(theme_view_file('loan::report.account_statement_pdf'), compact('start_date', 'end_date', 'location_id', 'data', 'business_locations'));
                        return $pdf->download("$file_name.pdf");
                    } elseif (request()->type == 'excel_2007') {
                        return Excel::download(new LoanExport($view), "$file_name.xlsx");
                    } elseif (request()->type == 'excel') {
                        return Excel::download(new LoanExport($view), "$file_name.xls");
                    } elseif (request()->type == 'csv') {
                        return Excel::download(new LoanExport($view), "$file_name.csv");
                    }
                } else {
                    // (new FlashService())->onWarning(trans('accounting::core.client_not_found_check_id', ['id' => 'loan']));
                }
            }
        }

        $report_data = array_merge($report_data, compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'contact_id', 'loan_id', 'loans'));

        return $report_data;
    }

    public function getAccountStatement()
    {
        $business_id = session('user.business_id');

        $start_date = request()->start_date;
        $end_date =  request()->end_date;
        $contact_id = request()->contact_id;
        $contact = Contact::where('business_id', $business_id)->find($contact_id);

        $data = $this
            ->setStartDate($start_date)
            ->setEndDate($end_date)
            ->setContact($contact)
            ->getAccountStatementData();

        if (request()->input('action') == 'pdf') {
            $data['for_pdf'] = true;
            $html = view('contact.statements.loan_statement')->with($data)->render();
            $mpdf = getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }

        return view('contact.statements.loan_statement')->with($data);
    }

    public function getReports()
    {
        return collect([
            (object) [
                'url' => url('report/contact_loan/collection_sheet'),
                'title' => trans_choice('loan::general.collection_sheet', 1),
                'description' => trans_choice('loan::general.collection_sheet_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/repayment'),
                'title' => trans_choice('loan::general.repayment', 2),
                'description' => trans_choice('loan::general.repayment_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/arrears'),
                'title' => trans_choice('loan::general.arrears', 1),
                'description' => trans_choice('loan::general.arrears_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/disbursement'),
                'title' => trans_choice('loan::general.disbursement', 1) . ' ' . trans_choice('accounting::core.report', 1),
                'description' => trans_choice('loan::general.disbursement_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/account_statement'),
                'title' =>  trans_choice('loan::general.loan_account_statement', 1),
                'description' => trans_choice('loan::general.loan_account_statement_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/expected_repayment'),
                'title' => trans_choice('loan::general.expected', 2) . ' ' . trans_choice('loan::general.repayment', 2),
                'description' => trans_choice('loan::general.expected_repayment_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/awaiting_disbursement'),
                'title' => trans_choice('loan::general.awaiting_disbursement', 1),
                'description' => trans_choice('loan::general.loan_awaiting_disbursement_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/pending_approval'),
                'title' => trans_choice('loan::general.pending_approval', 1),
                'description' => trans_choice('loan::general.loans_pending_approval_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/rescheduled_loans'),
                'title' => trans_choice('loan::general.rescheduled', 1) . ' ' . trans_choice('loan::general.loan', 2),
                'description' => trans_choice('loan::general.rescheduled_loans_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/written_off_loans'),
                'title' => trans_choice('loan::general.written_off', 1) . ' ' . trans_choice('loan::general.loan', 2),
                'description' => trans_choice('loan::general.written_off_loans_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/fully_paid_loans'),
                'title' => trans_choice('loan::general.fully_paid', 1) . ' ' . trans_choice('loan::general.loan', 2),
                'description' => trans_choice('loan::general.fully_paid_loans_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/active_past_maturity_loans'),
                'title' => trans_choice('loan::general.active', 1) . ' ' . trans_choice('loan::general.loan', 2) . ' ' . trans('accounting::core.past_maturity'),
                'description' => trans_choice('loan::general.active_past_maturity_loans_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/active_loans_in_last_installment'),
                'title' => trans_choice('loan::general.active', 1) . ' ' . trans_choice('loan::general.loan', 2) . ' ' . trans('loan::general.in_last_installment'),
                'description' => trans_choice('loan::general.active_loans_in_last_installment_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/active_loan_summary_per_branch'),
                'title' => trans_choice('loan::general.active_loan_summary_per_branch', 1),
                'description' => trans_choice('loan::general.active_loan_summary_per_branch_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/active_loans_by_disbursal_period'),
                'title' => trans('loan::general.active_loans_by_disbursal_period'),
                'description' => trans_choice('loan::general.active_loans_by_disbursal_period_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/closed_loans'),
                'title' => trans('loan::general.closed') . ' ' . trans_choice('loan::general.loan', 2),
                'description' => trans_choice('loan::general.closed_loan_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/aging_detail'),
                'title' => trans_choice('loan::general.aging', 2) . ' ' . trans_choice('accounting::core.detail', 1),
                'description' => trans_choice('loan::general.aging_detail_report_description', 1),
            ],
            // (object) [
            //     'url' => url('report/contact_loan/loans_awaiting_disbursal_summary'),
            //     'title' => trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.awaiting_disbursal', 2) . ' ' . trans_choice('accounting::core.summary', 1),
            //     'description' => trans_choice('loan::general.loans_awaiting_disbursal_summary_report_description', 1),
            // ],
            // (object) [
            //     'url' => url('report/contact_loan/loans_awaiting_disbursal_by_month'),
            //     'title' => trans_choice('loan::general.loan', 2) . ' ' . trans_choice('loan::general.awaiting_disbursal', 2) . ' ' . trans_choice('accounting::core.summary', 1) . ' ' . trans_choice('accounting::core.by', 1) . ' ' . trans_choice('accounting::core.month', 1),
            //     'description' => trans_choice('loan::general.loans_awaiting_disbursal_by_month_report_description', 1),
            // ],
            (object) [
                'url' => url('report/contact_loan/active_loans_details'),
                'title' => trans_choice('loan::general.active_loans_details', 1),
                'description' => trans_choice('loan::general.active_loans_details_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/active_loans_summary'),
                'title' => trans_choice('loan::general.active_loans_summary', 1),
                'description' => trans_choice('loan::general.active_loans_summary_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/overdue_mature_loans'),
                'title' => trans_choice('loan::general.overdue_mature_loans', 1),
                'description' => trans_choice('loan::general.overdue_mature_loans_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/loan_transactions_detailed'),
                'title' => trans_choice('loan::general.loan_transactions_detailed', 1),
                'description' => trans_choice('loan::general.loan_transactions_detailed_report_description', 1),
            ],
            // (object) [
            //     'url' => url('report/contact_loan/loan_transactions_summary'),
            //     'title' => trans_choice('loan::general.loan_transactions_summary', 1),
            //     'description' => trans_choice('loan::general.loan_transactions_summary_report_description', 1),
            // ],
            (object) [
                'url' => url('report/contact_loan/loan_funds_movement'),
                'title' => trans_choice('loan::general.loan_funds_movement', 1),
                'description' => trans_choice('loan::general.loan_funds_movement_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/loan_classification_by_product'),
                'title' => trans_choice('loan::general.loan_classification_by_product', 1),
                'description' => trans_choice('loan::general.loan_classification_by_product_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/active_past_maturity_loans_summary'),
                'title' => trans_choice('loan::general.active', 1) . ' ' . trans_choice('loan::general.loan', 2) . ' ' . trans('accounting::core.past_maturity') . ' ' . trans('accounting::core.summary'),
                'description' => trans_choice('loan::general.active_past_maturity_loans_summary_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/aging_summary_in_months'),
                'title' => trans_choice('loan::general.aging_summary_in_months', 1),
                'description' => trans_choice('loan::general.aging_summary_in_months_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/aging_summary_in_weeks'),
                'title' => trans_choice('loan::general.aging_summary_in_weeks', 1),
                'description' => trans_choice('loan::general.aging_summary_in_weeks_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/balance_outstanding'),
                'title' => trans_choice('accounting::core.balance', 1) . ' ' . trans('accounting::core.outstanding'),
                'description' => trans_choice('loan::general.balance_outstanding_report_description', 1),
            ],
            // (object) [
            //     'url' => url('report/contact_loan/branch_expected_cash_flow'),
            //     'title' => trans_choice('accounting::core.location', 1) . ' ' . trans('accounting::core.expected') . ' ' . trans('accounting::core.cash_flow'),
            //     'description' => trans_choice('loan::general.branch_expected_cash_flow_report_description', 1),
            // ],
            (object) [
                'url' => url('report/contact_loan/basic_expected_payment_by_date'),
                'title' => trans_choice('loan::general.basic_expected_payment_by_date', 1),
                'description' => trans_choice('loan::general.basic_expected_payment_by_date_report_description', 1),
            ],
            // (object) [
            //     'url' => url('report/contact_loan/formatted_expected_payment_by_date'),
            //     'title' => trans_choice('loan::general.formatted_expected_payment_by_date', 1),
            //     'description' => trans_choice('loan::general.formatted_expected_payment_by_date_report_description', 1),
            // ],
            (object) [
                'url' => url('report/contact_loan/loan_trends_by_month_by_created'),
                'title' => trans_choice('loan::general.loan_trends_by_month_by_created', 1),
                'description' => trans_choice('loan::general.loan_trends_by_month_by_created_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/loan_trends_by_month_by_disbursed'),
                'title' => trans_choice('loan::general.loan_trends_by_month_by_disbursed', 1),
                'description' => trans_choice('loan::general.loan_trends_by_month_by_disbursed_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/obligation_met_loans_details'),
                'title' => trans_choice('loan::general.obligation_met_loans_details', 1),
                'description' => trans_choice('loan::general.obligation_met_loans_details_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/portfolio_at_risk'),
                'title' => trans_choice('loan::general.portfolio_at_risk', 2),
                'description' => trans_choice('loan::general.portfolio_at_risk_report_description', 1),
            ],
            (object) [
                'url' => url('report/contact_loan/portfolio_at_risk_by_branch'),
                'title' => trans_choice('loan::general.portfolio_at_risk', 2) . ' ' . trans('accounting::core.by') . ' ' . trans_choice('accounting::core.location', 1),
                'description' => trans_choice('loan::general.portfolio_at_risk_by_branch_report_description', 1),
            ],
        ]);
    }

    public function getReportQuery($params)
    {
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        // $contact_id = !empty($this->contact) ? $this->contact->id : request()->contact_id;

        return $this->getLoanQuery()->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('loans.created_at', [$start_date, $end_date]);
        });
    }
}
