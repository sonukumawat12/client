<?php

namespace App\Http\Controllers\Chequer;

use App\User;
use App\Account;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use App\Chequer\CancelCheque;
use App\Chequer\ChequeNumber;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class CancellChequeController extends Controller
{
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {

            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
            $cancelCheque = CancelCheque::with('user','account','chequeBookNo')->where('business_id', $business_id)->latest();
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $cancelCheque->whereDate('reg_datetime', '>=', request()->start_date);
                $cancelCheque->whereDate('reg_datetime', '<=', request()->end_date);
            }
            if (!empty(request()->user) && !empty(request()->user)) {
                $cancelCheque->where('user_id',request()->user);
            }
            if (!empty(request()->cheque_book) && !empty(request()->cheque_book)) {
                $cancelCheque->where('cheque_bk_id',request()->cheque_book);
            }
            if (!empty(request()->cheque_number) && !empty(request()->cheque_number)) {
                $cancelCheque->where('cheque_no',request()->cheque_number);
            }

            return Datatables::of($cancelCheque)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu" style="overflow-y: auto;">';
                            $html .= '<li><a href="' . action('\App\Http\Controllers\Chequer\CancellChequeController@edit', [$row->id]) . '" ><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                            $html .= '<li><a href="#" data-href="' . action('\App\Http\Controllers\Chequer\CancellChequeController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                           
                            return $html;
                        }
                    )
                    
                    ->addColumn(
                                'note',
                                function ($row) {
                                    return '<div class="btn-group">
                                                <a href="' . action('\App\Http\Controllers\Chequer\CancellChequeController@note', ['id' => $row->id]) . '" 
                                                    class="btn btn-info btn-xs note-btn"
                                                    id="note-btn' . $row->id . '">
                                                    <i class="glyphicon glyphicon-edit"></i> ' . __("Note") . '
                                                </a>
                                            </div>';
                                }
                            )
                            

                ->editColumn('reg_datetime', '{{date("Y-m-d H:i ", strtotime($reg_datetime))}}')
              
                ->removeColumn('id')
                ->rawColumns(['action','note'])
                ->make(true);
        }

        $cheque_books = ChequeNumber::has('cancelCheque')->where('business_id', $business_id)->pluck('reference_no','id');
        $cheque_number = CancelCheque::where('business_id', $business_id)->pluck('cheque_no','cheque_no');
        $users = User::has('cancelCheque')->where('business_id', $business_id)->pluck('username','id');
        return view('chequer.cancell_cheque.index',compact('cheque_books','cheque_number','users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $bankAccounts = Account::has('chequeBankAccount')->where('business_id', $business_id)->where('is_need_cheque','Y')->pluck('name','id');
        return view('chequer.cancell_cheque.create',compact('bankAccounts'));
    }

  public function add()
    {
        $business_id = request()->session()->get('business.id');
        $bankAccounts = Account::has('chequeBankAccount')->where('business_id', $business_id)->where('is_need_cheque','Y')->pluck('name','id');
          $cheque_books = Account::has('chequeBankAccount')->where('business_id', $business_id)->where('is_need_cheque','Y')->pluck('account_number','id');
        $cheque_number = CancelCheque::where('business_id', $business_id)->pluck('cheque_no','cheque_no');
        return view('chequer.cancell_cheque.add',compact('bankAccounts','cheque_books','cheque_number'));
    }
     public function note(Request $request)
    {
       $note_id=$request->id;
     $cheque_number = CancelCheque::find($request->id);
     $note=$cheque_number->note;
        return view('chequer.cancell_cheque.note',compact('note_id','note'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            
            $data = array(
                'business_id' => $business_id,
                'account_id' => $request->bank_account,
                'cheque_bk_id' => $request->account_book_number,
                'cheque_no' => $request->cheque_numbers,
                'note' => $request->note,
                'user_id' => Auth::user()->id
            );
            
            if($request->note == null){
                $output = [
                    'success' => 0,
                    'msg' => __('please enter notes.')
                ];
            }else{
                ChequeNumber::find($request->account_book_number)
                ->decrement('no_of_cheque_leaves');

                CancelCheque::create($data);
                // PrintedChequeDetail::where('cheque_no', $request->cheque_no)->update(['status'=>'Cancelled']);
                $output = [
                    'success' => 1,
                    'msg' => __('cheque.success_cancelled_cheque')
                ];
            }    
            
        } catch (\Exception $e) {

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()->back()->with('status', $output);
    }



 public function store_note(Request $request)
{
    try {
        $business_id = $request->session()->get('business.id');

        // Find the existing CancelCheque entry by note_id
        $cancelCheque = CancelCheque::find($request->note_id);

        if (!$cancelCheque) {
            return redirect()->back()->with('status', [
                'success' => 0,
                'msg' => __('Note ID not found. Unable to update.')
            ]);
        }

        // Update the note field
        $cancelCheque->update([
            'note' => $request->note
        ]);

        $output = [
            'success' => 1,
            'msg' => __('cheque.success_cancelled_cheque')
        ];
        
    } catch (\Exception $e) {
        Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

        $output = [
            'success' => 0,
            'msg' => __('messages.something_went_wrong')
        ];
    }

    return redirect()->back()->with('status', $output);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cancelCheque =  CancelCheque::find($id);
        $business_id = request()->session()->get('business.id');
        $bankAccounts = Account::has('chequeBankAccount')->where('business_id', $business_id)->where('is_need_cheque','Y')->pluck('name','id');
        $chequeNumbers = ChequeNumber::where('account_no',$cancelCheque->account_id)->whereBusinessId($business_id)->whereNotIn('status',['used','stop'])->pluck('reference_no','id');
        $cheques = $this->getAutoIncrementChequeNo($cancelCheque->cheque_bk_id,$cancelCheque->cheque_no);
        return view('chequer.cancell_cheque.create',compact('bankAccounts','cancelCheque','chequeNumbers','cheques'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        try {
            
            $business_id = $request->session()->get('business.id');
            
            $data = array(
                'business_id' => $business_id,
                'account_id' => $request->bank_account,
                'cheque_bk_id' => $request->account_book_number,
                'cheque_no' => $request->cheque_number,
                'note' => $request->note
            );
            
            if($request->note == null){
                $output = [
                    'success' => 0,
                    'msg' => __('please enter notes.')
                ];
            }else{
                
                CancelCheque::where('id',$id)->update($data);
                $output = [
                    'success' => 1,
                    'msg' => __('cheque.update_success_cancelled_cheque')
                ];
            }    
            
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            CancelCheque::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
    /**
     * get Bank Book Number
     *
     * Undocumented function long description
     *
     **/
    public function getAccountBookNumber($bankAccount_id)
    {
        $business_id = request()->session()->get('business.id');

        $chequeNumbers = ChequeNumber::where('account_no',$bankAccount_id)->whereBusinessId($business_id)->whereNotIn('status',['used','stop'])->pluck('reference_no','id');
        $output = [

            'success' => 1,
            'data' => $chequeNumbers

        ];
        return $output;
    }

    /**
     * get cheques against account of bank cheque book
     * cheque_no,bank_account_no
     **/
    public function getAccountBookNumberCheques($chequeBookId)
    {
        try {
            
       
        $output = [

            'success' => 1,
            'data' => $this->getAutoIncrementChequeNo($chequeBookId),

        ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return  $output;
    }

    function getAutoIncrementChequeNo($chequeBookId,$chaqueNo = 0) {
        $business_id = request()->session()->get('business.id');

        $chequeBookNumber = ChequeNumber::find($chequeBookId);
        $cancelCheque = [];
        $cheque_book_page = [];
     
        $cancelCheque = CancelCheque::whereBusinessId($business_id)->where('cheque_bk_id',$chequeBookNumber->id)->where('account_id',$chequeBookNumber->account_no)->pluck('cheque_no')->toArray();
   
        if($chaqueNo != 0)
        {

            $cheque_book_page[$chaqueNo] = $chaqueNo;
            if (($key = array_search($chaqueNo,$cancelCheque)) !== false) {
                unset($cancelCheque[$key]);
            }
        }
        $start_cheque_no = (!is_null($chequeBookNumber->latest_cheque_issue))?$chequeBookNumber->latest_cheque_issue:$chequeBookNumber->first_cheque_no;
        $end_cheque_no = $chequeBookNumber->last_cheque_no;
       
        for($start_cheque_no ; $start_cheque_no <= $end_cheque_no; $start_cheque_no++ )
        {
            if(!in_array($start_cheque_no,$cancelCheque))
            {
                $cheque_book_page[$start_cheque_no] = $start_cheque_no;
            }

        }
        
        return $cheque_book_page;
         
    }
}
