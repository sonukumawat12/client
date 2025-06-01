<?php

namespace Modules\Loan\Http\Controllers;

use App\BusinessLocation;
use Modules\Accounting\Services\FlashService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Contact;
use App\User;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Exports\LoanExport;
use Modules\Loan\Http\Services\LoanReportService;
use PDF;

class LoanReportController extends Controller
{
    private $default_start_date;
    private $default_end_date;
    private $loan_report_service;

    public function __construct(LoanReportService $loan_report_service)
    {
        $this->default_start_date = date('Y-m-d', strtotime('30 days ago'));
        $this->default_end_date = date('Y-m-d');
        $this->loan_report_service = $loan_report_service;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $reports = (new LoanReportService())->getReports();
        return view('loan::report.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function collection_sheet(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $loan_product_id = $request->loan_product_id;
        $loan_officer_id = $request->loan_officer_id;
        $users = User::where('business_id',session('business.id'))->get();
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();

        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
                ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("business_locations", "loans.location_id", "business_locations.id")
                ->join("contacts", "loans.contact_id", "contacts.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date]);
                })
                ->when($location_id, function ($query) use ($location_id) {
                    $query->where('loans.location_id', $location_id);
                })
                ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loans.loan_officer_id', $loan_officer_id);
                })
                ->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })
                ->where('loans.status', 'active')
                ->where('business_locations.business_id', session('business.id'))
                ->selectRaw("concat(contacts.name) contact,concat(users.first_name,' ',users.last_name) loan_officer,business_locations.name business_location,contacts.mobile,loans.contact_id,loan_products.name loan_product,loan_repayment_schedules.loan_id,loans.expected_maturity_date,loan_repayment_schedules.total_due,(loan_repayment_schedules.principal+loan_repayment_schedules.interest+loan_repayment_schedules.fees+loan_repayment_schedules.penalties-loan_repayment_schedules.principal_written_off_derived-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.interest_waived_derived-loan_repayment_schedules.fees_waived_derived-loan_repayment_schedules.penalties_waived_derived) expected_amount,loan_repayment_schedules.due_date")
                ->get();
        }
        return view('loan::report.collection_sheet', compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'users', 'loan_officer_id', 'loan_product_id', 'loan_products'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function repayment(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $users = User::where('business_id',session('business.id'))->get();
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        if (!empty($start_date)) {
            $data = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("business_locations", "loans.location_id", "business_locations.id")
                ->join("contacts", "loans.contact_id", "contacts.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                // ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                // ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_transactions.submitted_on', [$start_date, $end_date]);
                })
                ->when($location_id, function ($query) use ($location_id) {
                    $query->where('loans.location_id', $location_id);
                })
                ->where('business_locations.business_id', session('business.id'))
                ->where('loan_transaction_type_id', 2)
                ->selectRaw("concat(contacts.name) contact,concat(users.first_name,' ',users.last_name) loan_officer,business_locations.name business_location,loans.contact_id,loan_transactions.id,loan_transactions.loan_id,loan_transactions.principal_repaid_derived,loan_transactions.interest_repaid_derived,loan_transactions.fees_repaid_derived,loan_transactions.penalties_repaid_derived,loan_transactions.submitted_on,'' as payment_type")
                ->get();
        }

        return view('loan::report.repayment', compact('start_date', 'end_date', 'location_id', 'data', 'business_locations'));
    }

    public function expected_repayment(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
                ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
                ->join("business_locations", "loans.location_id", "business_locations.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date]);
                })
                ->when($location_id, function ($query) use ($location_id) {
                    $query->where('loans.location_id', $location_id);
                })
                ->where('loans.status', 'active')
                ->where('business_locations.business_id', session('business.id'))
                ->selectRaw("business_locations.name business_location,loans.location_id,coalesce(sum(loan_repayment_schedules.principal-loan_repayment_schedules.principal_written_off_derived),0) principal,coalesce(sum(loan_repayment_schedules.interest-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.interest_waived_derived),0) interest,coalesce(sum(loan_repayment_schedules.fees-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.fees_waived_derived),0) fees,coalesce(sum(loan_repayment_schedules.penalties-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.penalties_waived_derived),0) penalties,coalesce(sum(loan_repayment_schedules.principal_repaid_derived),0) principal_repaid_derived,coalesce(sum(loan_repayment_schedules.interest_repaid_derived),0) interest_repaid_derived,coalesce(sum(loan_repayment_schedules.fees_repaid_derived),0) fees_repaid_derived,coalesce(sum(loan_repayment_schedules.penalties_repaid_derived),0) penalties_repaid_derived")
                ->groupBy('business_locations.id')
                ->get();

            //check if we should download
            if ($request->download) {

                if (!empty($data->first())) {
                    $view = view('loan::report.expected_repayment_pdf', compact('start_date', 'end_date', 'location_id', 'data', 'business_locations'));
                    $file_name = $data->first()->contact . ' ' . trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ')';

                    if ($request->type == 'pdf') {
                        $pdf = PDF::loadView(theme_view_file('loan::report.expected_repayment_pdf'), compact('start_date', 'end_date', 'location_id', 'data', 'business_locations'));
                        return $pdf->download("$file_name.pdf");
                    } elseif ($request->type == 'excel_2007') {
                        return Excel::download(new LoanExport($view), "$file_name.xlsx");
                    } elseif ($request->type == 'excel') {
                        return Excel::download(new LoanExport($view), "$file_name.xls");
                    } elseif ($request->type == 'csv') {
                        return Excel::download(new LoanExport($view), "$file_name.csv");
                    }
                } else {
                    // (new FlashService())->onWarning(trans('accounting::core.client_not_found_check_id', ['id' => 'shares']));
                }
            }
        }
        return view('loan::report.expected_repayment', compact('start_date', 'end_date', 'location_id', 'data', 'business_locations'));
    }

    public function arrears(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        if (!empty($end_date)) {
            $data = Loan::with("repayment_schedules")
                ->join(DB::raw("(SELECT * FROM loan_repayment_schedules WHERE loan_repayment_schedules.due_date BETWEEN '$start_date' AND '$end_date' AND total_due > 0) loan_repayment_schedules"), "loan_repayment_schedules.loan_id", "loans.id")
                ->join("business_locations", "loans.location_id", "business_locations.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("contacts", "loans.contact_id", "contacts.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($location_id, function ($query) use ($location_id) {
                    $query->where('loans.location_id', $location_id);
                })
                ->where('loans.status', 'active')
                ->where('business_locations.business_id', session('business.id'))
                ->selectRaw("concat(contacts.name) contact,contacts.mobile,concat(users.first_name,' ',users.last_name) loan_officer,business_locations.name business_location,contacts.mobile,loans.contact_id,loan_products.name loan_product,loans.expected_maturity_date,loans.disbursed_on_date,loans.id,(SELECT submitted_on FROM loan_transactions WHERE loan_id=loans.id ORDER BY submitted_on DESC LIMIT 1) last_payment_date,loans.principal")
                ->groupBy('loans.id')
                ->get();

            //check if we should download and that the data for contact is present
            if ($request->download) {
                $view = view('loan::report.arrears_pdf', compact('start_date', 'end_date', 'location_id', 'data', 'business_locations'));
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.arrears_pdf'), compact(
                        'start_date',
                        'end_date',
                        'location_id',
                        'data',
                        'business_locations'
                    ))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.arrears', 1) . '( as at ' . $end_date . ').pdf');
                } elseif ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.arrears', 1) . '(as at ' . $end_date . ').xlsx');
                } elseif ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.arrears', 1) . '(as at ' . $end_date . ').xls');
                } elseif ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.arrears', 1) . '(as at' . $end_date . ').csv');
                }
            }
        }
        return view(
            'loan::report.arrears',
            compact(
                'start_date',
                'end_date',
                'location_id',
                'data',
                'business_locations',
            )
        );
    }

    public function disbursement(Request $request)
    {
        $start_date = $request->start_date ?: $this->default_start_date;
        $end_date = $request->end_date ?: $this->default_end_date;
        $location_id = $request->location_id;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        $loan_officer_id = $request->loan_officer_id;
        $users = User::where('business_id',session('business.id'))->get();
        $loan_products = LoanProduct::forDropdown();
        $data = [];
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        if (!empty($start_date)) {
            $data = Loan::with("repayment_schedules")
                ->join("business_locations", "loans.location_id", "business_locations.id")
                ->join("loan_purposes", "loans.loan_purpose_id", "loan_purposes.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("contacts", "loans.contact_id", "contacts.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loans.disbursed_on_date', [$start_date, $end_date]);
                })
                ->when($location_id, function ($query) use ($location_id) {
                    $query->where('loans.location_id', $location_id);
                })
                ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loans.loan_officer_id', $loan_officer_id);
                })
                ->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })
                ->when($status, function ($query) use ($status) {
                    $query->where('loans.status', $status);
                })
                ->where('business_locations.business_id', session('business.id'))
                ->selectRaw("concat(contacts.name) contact,contacts.mobile,concat(users.first_name,' ',users.last_name) loan_officer,loan_purposes.name loan_purpose,business_locations.name business_location,contacts.mobile,loans.contact_id,loan_products.name loan_product,loans.expected_maturity_date,loans.disbursed_on_date,loans.id,loans.principal,loans.status,loans.repayment_frequency,loans.repayment_frequency_type")
                ->get();

            //check if we should download
            if ($request->download) {

                $view = view('loan::report.arrears_pdf', compact('start_date', 'end_date', 'location_id', 'data', 'business_locations', 'loan_officer_id', 'loan_product_id', 'loan_products', 'users', 'status'));
                $file_name = $data->first()->contact . ' ' . trans_choice('accounting::core.account_statement', 1) . '(' . $start_date . ' to ' . $end_date . ')';

                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.disbursement_pdf'), compact(
                        'start_date',
                        'end_date',
                        'location_id',
                        'data',
                        'business_locations',
                        'loan_officer_id',
                        'loan_product_id',
                        'loan_products',
                        'users',
                        'status'
                    ))->setPaper('A4', 'landscape');
                    return $pdf->download("$file_name.pdf");
                } elseif ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), "$file_name.xlsx");
                } elseif ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), "$file_name.xls");
                } elseif ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), "$file_name.csv");
                }
            }
        }
        return view(
            'loan::report.disbursement',
            compact(
                'start_date',
                'end_date',
                'location_id',
                'data',
                'business_locations',
                'loan_officer_id',
                'loan_product_id',
                'loan_products',
                'users',
                'status',
            )
        );
    }

    public function account_statement()
    {
        $start_date = request()->start_date ?: $this->default_start_date;
        $end_date = request()->end_date ?: $this->default_end_date;

        $data = (new LoanReportService())
            ->setStartDate($start_date)
            ->setEndDate($end_date)
            ->getAccountStatementData();
        return view('loan::report.account_statement')->with($data);
    }

    public function awaiting_disbursement(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('approved')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.awaiting_disbursement', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function pending_approval(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('pending_and_submitted')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.pending_approval', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function rescheduled_loans(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->where('rescheduled_notes', '!=', null)->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.rescheduled_loans', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function written_off_loans(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('written_off')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.written_off', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function fully_paid_loans(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('fully_paid')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.fully_paid', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function active_past_maturity_loans(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        $unfiltered_data = Loan::with("contact")
            ->with("repayment_schedules")
            ->with("overdue_repayment_schedules")
            ->with("business_location")
            ->with("loan_purpose")
            ->forBusiness()
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where("loans.loan_product_id", $loan_product_id);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("loans.location_id", $location_id);
            })
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('loans.created_at', [$start_date, $end_date]);
            })
            ->forStatus('active')
            ->get();

        $data = $unfiltered_data->filter(function ($loan) {
            return $loan->maturity_date < date('Y-m-d');
        });

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.active_past_maturity', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function active_loans_in_last_installment(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $orderBy = request()->order_by;
        $orderByDir = request()->order_by_dir;
        $status = request()->status;
        $contact_id = request()->contact_id;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $user_id = request()->user_id;
        $location_id = request()->location_id;

        $unfiltered_data = Loan::with("contact")
            ->with("repayment_schedules")
            ->with("loan_product")
            ->with("business_location")
            ->with("loan_officer")
            ->forBusiness()
            ->when($contact_id, function ($query) use ($contact_id) {
                $query->where("loans.contact_id", $contact_id);
            })
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where("loans.loan_product_id", $loan_product_id);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("loans.location_id", $location_id);
            })
            ->when($status, function ($query) use ($status) {
                $query->forStatus($status);
            })
            ->when($user_id, function ($query) use ($user_id) {
                $query->whereHas('pending_approval', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                });
            })
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })->forStatus('active')->get();

        $data = $unfiltered_data->filter(function ($loan) {
            return $loan->repayment_schedules->where('paid_by_date', null)->count('paid_by_date') == 1;
        });

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.active_loans_in_last_installment', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function active_loan_summary_per_branch(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $orderBy = request()->order_by;
        $orderByDir = request()->order_by_dir;
        $status = request()->status;
        $contact_id = request()->contact_id;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        $data = Loan::with("contact")
            ->with("repayment_schedules")
            ->with("overdue_repayment_schedules")
            ->with("business_location")
            ->with("loan_officer")
            ->forBusiness()
            ->when($contact_id, function ($query) use ($contact_id) {
                $query->where("loans.contact_id", $contact_id);
            })
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where("loans.loan_product_id", $loan_product_id);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("loans.location_id", $location_id);
            })
            ->when($status, function ($query) use ($status) {
                $query->forStatus($status);
            })
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('loans.created_at', [$start_date, $end_date]);
            })
            ->forStatus('active')
            ->get();

        $business_locations = $data->pluck('business_location')->unique('id');
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.active_loan_summary_per_branch', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function loan_payments_received(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.loan_payments_received', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function active_loans_by_disbursal_period(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.active_loans_by_disbursal_period', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function overdue_loan_payments_received(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.overdue_loan_payments_received', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function closed_loans(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('closed')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.closed_loans', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function loan_payments_due(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.loan_payments_due', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function overdue_loan_payments_due(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.overdue_loan_payments_due', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function aging_detail(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.aging_detail', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function loans_awaiting_disbursal_summary(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.loans_awaiting_disbursal_summary', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function loans_awaiting_disbursal_by_month(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.loans_awaiting_disbursal_summary_by_month', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function active_loans_details(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.active_loans_details', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function active_loans_summary(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = $data->pluck('business_location')->unique();
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.active_loans_summary', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function overdue_mature_loans(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        $unfiltered_data = Loan::with("contact")
            ->with("repayment_schedules")
            ->with("overdue_repayment_schedules")
            ->with("business_location")
            ->with("loan_purpose")
            ->forBusiness()
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where("loans.loan_product_id", $loan_product_id);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("loans.location_id", $location_id);
            })
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('loans.created_at', [$start_date, $end_date]);
            })
            ->forStatus('active')
            ->get();

        $data = $unfiltered_data->filter(function ($loan) {
            return $loan->maturity_date < date('Y-m-d');
        });

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.overdue_mature_loans', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function loan_transactions_detailed(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        $loan_transactions = LoanTransaction::with('loan')
            ->with("created_by")
            ->forBusiness()
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loan_transactions.created_by_id", $loan_officer_id);
            })
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('loan_transactions.created_at', [$start_date, $end_date]);
            })
            ->get();

        $loan_officers = $loan_transactions->pluck('created_by')->unique();

        return view('loan::report.loan_transactions_detail', compact('loan_transactions', 'loan_officers', 'start_date', 'end_date'));
    }

    public function loan_transactions_summary(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        $loan_transactions = LoanTransaction::with('loan')
            ->with("created_by")
            ->forBusiness()
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loan_transactions.created_by_id", $loan_officer_id);
            })
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('loan_transactions.created_at', [$start_date, $end_date]);
            })
            ->get();

        $loan_officers = $loan_transactions->pluck('created_by')->unique();

        return view('loan::report.loan_transactions_summary', compact('loan_transactions', 'loan_officers', 'start_date', 'end_date'));
    }

    public function loan_funds_movement(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.loan_funds_movement', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function loan_classification_by_product(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])
            ->whereIn('status', ['active', 'fully_paid', 'closed'])
            ->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = $data->pluck('loan_product')->unique();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.loan_classification_by_product', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function active_past_maturity_loans_summary(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        $unfiltered_data = Loan::with("contact")
            ->with("repayment_schedules")
            ->with("overdue_repayment_schedules")
            ->with("business_location")
            ->with("loan_purpose")
            ->forBusiness()
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where("loans.loan_product_id", $loan_product_id);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("loans.location_id", $location_id);
            })
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('loans.created_at', [$start_date, $end_date]);
            })
            ->forStatus('active')
            ->get();

        $data = $unfiltered_data->filter(function ($loan) {
            return $loan->maturity_date < date('Y-m-d');
        });

        $business_locations = $data->pluck('business_location')->unique();
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.active_past_maturity_summary', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function aging_summary_in_months(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $monthly_bands = Loan::getMonthlyBands();

        return view('loan::report.aging_summary_in_months', compact('data', 'monthly_bands', 'start_date', 'end_date'));
    }

    public function aging_summary_in_weeks(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $weekly_bands = Loan::getWeeklyBands();

        return view('loan::report.aging_summary_in_weeks', compact('data', 'weekly_bands', 'start_date', 'end_date'));
    }

    public function balance_outstanding(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get()->where('current_balance', '>', 0);

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.balance_outstanding', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function branch_expected_cash_flow(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.branch_expected_cash_flow', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function basic_expected_payment_by_date(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        $data = Loan::with("contact")
            ->with(["unpaid_repayment_schedules" => function ($query) use ($start_date, $end_date) {
                if ($start_date && $end_date) {
                    return $query->whereBetween('due_date', [$start_date, $end_date]);
                }
            }])
            ->with("loan_product")
            ->with("business_location")
            ->with("loan_officer")
            ->forBusiness()
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where("loans.loan_product_id", $loan_product_id);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("loans.location_id", $location_id);
            })
            ->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.basic_expected_payment_by_date', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function formatted_expected_payment_by_date(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.formatted_expected_payment_by_date', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function loan_trends_by_month_by_created(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        $data = Loan::forBusiness()
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where("loans.loan_product_id", $loan_product_id);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("loans.location_id", $location_id);
            })
            ->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        $months = array_keys($this->get_months(date('Y')));

        return view('loan::report.loan_trends_by_month_by_created', compact('data', 'months', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }
    
    function get_months($year)
    {
        return [
            'January' => 1, 'February' => 2, 'March' =>3, 'April' => 4, 'May' => 5, 'June' => 6,
            'July' => 7, 'August' => 8, 'September' => 9, 'October' =>10, 'November' => 11, 'December' => 12
        ];
    }


    public function loan_trends_by_month_by_disbursed(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        $data = Loan::forBusiness()
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where("loans.loan_product_id", $loan_product_id);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("loans.location_id", $location_id);
            })
            ->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        $months = array_keys($this->get_months(date('Y')));

        return view('loan::report.loan_trends_by_month_by_disbursed', compact('data', 'months', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function obligation_met_loans_details(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $unfiltered_data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])
            ->whereNotIn('status', ['pending', 'submitted', 'approved', 'written_off'])->get();

        $data = $unfiltered_data->where('arrears_days', 0);

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.obligation_met_loans_details', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function portfolio_at_risk(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.portfolio_at_risk', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }

    public function portfolio_at_risk_by_branch(Request $request)
    {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $data = $this->loan_report_service->getReportQuery([
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->forStatus('active')->get();

        $business_locations = $data->pluck('business_location')->unique();
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $contacts = Contact::has('loans')->where('business_id',session('business.id'))->get(['id', 'name']);

        return view('loan::report.portfolio_at_risk_by_branch', compact('data', 'business_locations', 'loan_products', 'loan_officers', 'contacts', 'start_date', 'end_date'));
    }
}
