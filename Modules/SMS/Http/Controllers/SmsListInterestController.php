<?php

namespace Modules\SMS\Http\Controllers;

use App\Member;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\Balamandalaya;
use Modules\Member\Entities\GramasevaVasama;
use Modules\SMS\Entities\SmsListInterest;
use Yajra\DataTables\Facades\DataTables;

class SmsListInterestController extends Controller
{
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param Util $businessUtil
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil =  $moduleUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->business_id;
        $type = request()->type;
        $form_no = SmsListInterest::where('business_id',$business_id)->where('type',$type)->get()->last()->form_no ?? 0;
        $form_no += 1;
        
        return view('sms::sms_list_interests.index')->with(compact(
            'form_no'
        ));
    }
    
    public function listInterests(){
        if (request()->ajax()) {
            $business_id = request()->business_id;
            $type = request()->type;
            
            $sms_lists = SmsListInterest::leftjoin('users', 'sms_list_interests.created_by', 'users.id')
                ->where('sms_list_interests.business_id', $business_id)
                ->where('sms_list_interests.type', $type)
                ->select([
                    'sms_list_interests.*',
                    'users.username as user_added',
                ]);

            $sms_lists->orderBy('sms_list_interests.created_at', 'desc');

            return DataTables::of($sms_lists)
                ->editColumn('date','{{@format_date($date)}}')
                ->editColumn('amount','{{@num_format($amount)}}')
                ->editColumn('note',function($row){
                    return nl2br($row->note);
                })
                ->rawColumns(['action','note'])
                ->make(true);
        }
    }
    

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $input = $request->except('_token');
            $input['created_by'] = Auth::user()->id;

            $record = SmsListInterest::create($input);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'form_no' => $record->form_no + 1
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
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    /**
     * View the specified resource from storage.
     * @return Response
     */
    
}
