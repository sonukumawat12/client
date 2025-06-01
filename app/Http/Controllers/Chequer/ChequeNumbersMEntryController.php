<?php

namespace App\Http\Controllers\Chequer;

use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Account;
use App\Contact;
use App\DefaultSettings;
use App\Utils\ModuleUtil;
use App\Chequer\ChequeNumber;
use App\Chequer\ChequeNumbersMEntry;
use App\Chequer\PrintedChequeDetail;
use App\Http\Controllers\Controller;
use App\Chequer\ChequerDefaultSetting;

class ChequeNumbersMEntryController extends Controller
{
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    public function index()
    {
        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
            
            $cheque_numbers_m_entry = ChequeNumbersMEntry::where('cheque_numbers_m_entries.business_id', $business_id)
            ->leftJoin('users as created_by_user', 'cheque_numbers_m_entries.created_by', '=', 'created_by_user.id')
            ->leftJoin('users as edited_by_user', 'cheque_numbers_m_entries.edited_by', '=', 'edited_by_user.id')
            ->leftJoin('accounts', 'cheque_numbers_m_entries.bank_id', '=', 'accounts.id')
            ->leftJoin('cheque_numbers', 'cheque_numbers_m_entries.cheque_number_id', '=', 'cheque_numbers.id')
            ->orderBy('cheque_numbers_m_entries.id', 'desc')
            ->select(
                'cheque_numbers_m_entries.*',
                'created_by_user.username as created_by_username',
                'edited_by_user.username as edited_by_username',
                'accounts.name as bank_account',
                'cheque_numbers.reference_no'
            );

            if (request()->has('date_range') && !empty(request()->date_range)) {
                $dates = explode(' - ', request()->date_range);
                $start_date = date('Y-m-d', strtotime($dates[0]));
                $end_date = date('Y-m-d', strtotime($dates[1]));
                $cheque_numbers_m_entry->whereBetween('cheque_numbers_m_entries.date_time', [$start_date, $end_date]);
            }

            if (request()->has('bank_id') && !empty(request()->bank_id)) {
                $cheque_numbers_m_entry->where('cheque_numbers_m_entries.bank_id', request()->bank_id);
            }

            if (request()->has('cheque_no') && !empty(request()->cheque_no)) {
                $cheque_numbers_m_entry->where('cheque_numbers_m_entries.cheque_number_id', request()->cheque_no);
            }

            if (request()->has('new_cheque_number_to_print') && !empty(request()->new_cheque_number_to_print)) {
                $cheque_numbers_m_entry->where('cheque_numbers_m_entries.new_cheque_number_to_print', request()->new_cheque_number_to_print);
            }

            if (request()->has('user_id') && !empty(request()->user_id)) {
                $cheque_numbers_m_entry->where(function ($query) {
                    $query->where('cheque_numbers_m_entries.created_by', request()->user_id)
                    ->orWhere('cheque_numbers_m_entries.edited_by', request()->user_id);
                });
            }

            return Datatables::of($cheque_numbers_m_entry)
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a href="#" data-href="' . action('Chequer\ChequeNumbersMEntryController@edit', [$row->id]) . '" class="cheque_number_m_entries_edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                        </ul></div>';
                })
                ->editColumn('note', function ($row) {
                    $note = $row->note;
                    if(!empty($row->edited_by_username)){
                        $note = $note . '<br><small class="text-muted">Edited by: ' . $row->edited_by_username . '</small>';
                    }
                    return '<span class="label bg-light-green show-note" data-raw-note="' . htmlspecialchars($note) . '"><i class="glyphicon glyphicon-info-sign"></i> Note</span>';
                })
                ->editColumn('date_time','{{@format_date($date_time)}}')
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
                ->rawColumns(['note', 'action'])
                ->make(true);
        }

        $accounts = Account::where('business_id', $business_id)->where('is_need_cheque', 'Y')->notClosed()->pluck('name', 'id');
        $cheque_numbers = ChequeNumber::where('cheque_numbers.business_id', $business_id)
        ->pluck('cheque_numbers.reference_no', 'cheque_numbers.id');
        $cheque_numbers_m_entries = ChequeNumbersMEntry::where('cheque_numbers_m_entries.business_id', $business_id)
        ->pluck('cheque_numbers_m_entries.new_cheque_number_to_print');
        $cheque_numbers_m_entry_users = ChequeNumbersMEntry::leftJoin('users', 'cheque_numbers_m_entries.created_by', 'users.id')
        ->select('cheque_numbers_m_entries.created_by as user_id', 'users.username')
        ->distinct('cheque_numbers_m_entries.created_by')
        ->pluck('username', 'user_id');
        $cheque_numbers_m_entry_users_edited_by = ChequeNumbersMEntry::leftJoin('users', 'cheque_numbers_m_entries.edited_by', 'users.id')
        ->select('cheque_numbers_m_entries.edited_by as user_id', 'users.username')
        ->distinct('cheque_numbers_m_entries.edited_by')
        ->pluck('username', 'user_id');
        $cheque_numbers_m_entry_users = $cheque_numbers_m_entry_users->merge($cheque_numbers_m_entry_users_edited_by)->unique('user_id');

        return view('chequer.cheque_numbers_m_entries.index')->with(compact('accounts', 'cheque_numbers', 'cheque_numbers_m_entries', 'cheque_numbers_m_entry_users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id'); 
        $accounts = Account::where('business_id', $business_id)->where('is_need_cheque', 'Y')->notClosed()->pluck('name', 'id');
        $cheque_numbers = ChequeNumber::where('cheque_numbers.business_id', $business_id)
        ->pluck('cheque_numbers.reference_no', 'cheque_numbers.id');    

        return view('chequer/cheque_numbers_m_entries/create')->with(compact('accounts','cheque_numbers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $data = array(
                'date_time' => $request->date_time,
                'bank_id' => $request->bank_id,
                'cheque_number_id' => $request->cheque_number_id,
                'next_cheque_number_to_print' => $request->next_cheque_number_to_print,
                'new_cheque_number_to_print' => $request->new_cheque_number_to_print,
                'next_cheque_number_to_auto_print' => $request->next_cheque_number_to_auto_print,
                'note' => $request->note,
                'business_id' => $business_id,
                'created_by' => Auth::user()->id,
            );
            ChequeNumbersMEntry::create($data);

            $output = [
                'success' => 1,
                'msg' => __('cheque.cheque_number_m_entry_add_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
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
        $business_id = request()->session()->get('business.id'); 
        $accounts = Account::where('business_id', $business_id)->where('is_need_cheque', 'Y')->notClosed()->pluck('name', 'id');
        $cheque_numbers = ChequeNumber::where('cheque_numbers.business_id', $business_id)
        ->pluck('cheque_numbers.reference_no', 'cheque_numbers.id');
        $cheque_numbers_m_entry = ChequeNumbersMEntry::findOrFail($id);

        return view('chequer/cheque_numbers_m_entries/edit')->with(compact('accounts','cheque_numbers', 'cheque_numbers_m_entry'));
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
        try {
            \Log::debug($request->all());
            $data = array(
                'bank_id' => $request->bank_id,
                'cheque_number_id' => $request->cheque_number_id,
                'next_cheque_number_to_print' => $request->next_cheque_number_to_print,
                'new_cheque_number_to_print' => $request->new_cheque_number_to_print,
                'next_cheque_number_to_auto_print' => $request->next_cheque_number_to_auto_print,
                'note' => $request->note,
                'edited_by' => Auth::user()->id,
            );
            $ChequeNumbersMEntry = ChequeNumbersMEntry::where('id', $id)->update($data);

            $output = [
                'success' => 1,
                'msg' => __('cheque.cheque_number_m_entry_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return response()->json($output);

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
        //
    }
}

