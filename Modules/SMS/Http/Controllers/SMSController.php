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
use Modules\SMS\Entities\SmsList;
use Modules\SMS\Entities\SmsGroup;
use Yajra\DataTables\Facades\DataTables;
use App\SmsLog;
use League\Csv\Reader;
use App\Contact;



class SMSController extends Controller
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
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $sms_lists = SmsList::leftjoin('users', 'sms_lists.created_by', 'users.id')
                ->where('sms_lists.business_id', $business_id)
                ->select([
                    'sms_lists.*',
                    'users.username as user',
                ]);

            if (!empty(request()->user)) {
                $sms_lists->where('sms_lists.created_by', request()->user);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $sms_lists->whereDate('sms_lists.created_at', '>=', request()->start_date);
                $sms_lists->whereDate('sms_lists.created_at', '<=', request()->end_date);
            }

            // $sms_lists->orderBy('sms_lists.created_at', 'desc');

            return DataTables::of($sms_lists)
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
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="#" data-href="' . action("\Modules\SMS\Http\Controllers\SMSController@show", [$row->id]) . '" class="btn-modal" data-container=".sms_model"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action("\Modules\SMS\Http\Controllers\SMSController@showNumbers", [$row->id]) . '" class="btn-modal" data-container=".sms_model"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("sms::lang.view_numbers") . '</a></li>';
                       

                        return $html;
                    }
                )
                ->editColumn('schedule_date_time','{{@format_datetime($schedule_date_time)}}')
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $usernames = SmsLog::where('business_id',$business_id)->whereNotNull('username')->distinct('username')->pluck('username','username');
        $sender_names = SmsLog::where('business_id',$business_id)->whereNotNull('sender_name')->distinct('sender_name')->pluck('sender_name','sender_name');
        $sms_type = SmsLog::where('business_id',$business_id)->whereNotNull('sms_type_')->distinct('sms_type_')->pluck('sms_type_','sms_type_');
        $sms_status = SmsLog::where('business_id',$business_id)->whereNotNull('sms_status')->distinct('sms_status')->pluck('sms_status','sms_status');
        
        return view('sms::list_sms.index')->with(compact('usernames','sender_names','sms_type','sms_status'));;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $member_groups = Member::pluck('name', 'id');
        $gramseva_vasamas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        $balamandalas = Balamandalaya::pluck('balamandalaya', 'id');

        $timezone_list = $this->businessUtil->allTimeZones();

        return view('sms::list_sms.create')->with(compact(
            'member_groups',
            'gramseva_vasamas',
            'balamandalas',
            'timezone_list'
        ));
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
            $input['business_id'] = $business_id;
            $input['schedule_date_time'] = !empty($input['schedule_date_time']) ? \Carbon::parse($input['schedule_date_time'])->format('Y-m-d H:i:s') : null;
            $input['numbers'] = array_map('trim', array_filter(explode("\n", $input['numbers'])));
            if (!empty($input['remove_duplicates'])) {
                $input['numbers'] = array_unique($input['numbers']);
            }
            $input['count_numbers'] = count($input['numbers']);
            $input['created_by'] = Auth::user()->id;

            SmsList::create($input);

            $output = [
                'success' => true,
                'msg' => __('sms::lang.sms_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $sms = SmsList::leftjoin('users', 'sms_lists.created_by', 'users.id')
            ->leftjoin('member_groups', 'sms_lists.member_group', 'member_groups.id')
            ->leftjoin('balamandalayas', 'sms_lists.balamandala', 'balamandalayas.id')
            ->leftjoin('gramaseva_vasamas', 'sms_lists.gramseva_vasama', 'gramaseva_vasamas.id')
            ->where('sms_lists.id', $id)
            ->select([
                'sms_lists.*',
                'users.username as user',
                'member_groups.member_group as member_group',
                'balamandalayas.balamandalaya as balamandala',
                'gramaseva_vasamas.gramaseva_vasama as gramseva_vasama'
            ])->first();

        return view('sms::list_sms.show')->with(compact('sms'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('sms::edit');
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
    public function showNumbers($id)
    {
        $numbers = SmsList::findOrFail($id)->numbers;

        return view('sms::list_sms.view_numbers')->with(compact('numbers'));
    }
    
    public function smsGroups()
    {
        $business_id = request()->session()->get('business.id');
        $sms_groups = SmsGroup::leftjoin('business', 'business.id', 'sms_groups.business_id')
        ->where('business_id', $business_id)->get();
        foreach ($sms_groups as $group) {
            $membersString = $group->members ?? '{}';
            $membersString = trim($membersString, '{}');
            $membersArray = array_filter(array_map('trim', explode(',', $membersString)));
            $group->member_count = count($membersArray);
        }
    
        // Pass the sms_groups to the view
        return view('sms::send_sms.sms_groups')->with(compact('sms_groups'));
    }

    
    
    public function createSmsGroup(){
        $sms_groups = [];
        return view('sms::send_sms.create_sms_group')->with(compact('sms_groups'));
    }
    
    public function storeSmsGroup(Request $request)
    {
         // Adjust memory limit and execution time
        ini_set('memory_limit', '2048M'); // Set to 1GB or adjust as needed
        ini_set('max_execution_time', '600'); // Increase time in seconds, e.g., 300 for 5 minutes

        $business_id = request()->session()->get('business.id');
    
        $request->validate([
            'date' => 'required|date',
            'group_name' => 'required|string|max:255',
            'import_file' => 'required|file|mimes:csv,txt|max:500000',

        ]);
    
        try {
            $filePath = $request->file('import_file')->store('sms_groups', 'public');
            
            $smsGroup = new SmsGroup();
            $smsGroup->business_id = $business_id;
            $smsGroup->date = $request->input('date');
            $smsGroup->group_name = $request->input('group_name');
            
            $csv = \League\Csv\Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            $csv->setHeaderOffset(0);
    
            $members = [];
            
            foreach ($csv as $record) {
                if (strlen($record['Phone Number']) == 11) {
                    $phone = trim($record['Phone Number']);
                    $members[] = $phone;
                }
            }
    
            if (!empty($members)) {
                $smsGroup->members = json_encode($members); // Store as JSON
                $smsGroup->save();
            } else {
                \Log::warning('No valid contacts found, SMS group not saved.');
            }
    
            $output = [
                'success' => true,
                'msg' => __('sms::lang.sms_group_create_success')
            ];
            
            return redirect()->back()->with('status', $output);
    
        } catch (\Exception $e) {
            \Log::error('Error adding SMS group: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('sms::lang.sms_group_addition_failed')
            ];
            
            return redirect()->back()->with('status', $output);
        }
    }



        // Show edit form for the SMS group
    public function editSmsGroup($id)
    {
        $sms_group = SmsGroup::findOrFail($id);
        return view('sms::send_sms.edit_sms_group')->with(compact('sms_group'));
    }
    
    // Update the SMS group
    public function updateSmsGroup(Request $request, $id)
    {
        $business_id = request()->session()->get('business.id');
        
        // Validate the request
        $request->validate([
            'group_name' => 'required|string|max:255',
        ]);
        
        try {
            // Find the SMS group by ID
            $sms_group = SmsGroup::where('id', $id)
                ->where('business_id', $business_id)
                ->firstOrFail();
            
            // Update the SMS group fields
            $sms_group->group_name = $request->input('group_name');
            $sms_group->save();
            
            $output = [
                'success' => true,
                'msg' => __('sms::lang.sms_group_update_success')
            ];
        } catch (\Exception $e) {
            \Log::error('Error updating SMS group: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect()->back()->with('status', $output);
    }

    public function deleteSmsGroup($id)
    {
        $business_id = request()->session()->get('business.id');
    
        try {
            $sms_group = SmsGroup::where('business_id', $business_id)->findOrFail($id);
            $sms_group->delete();
    
            $output = [
                        'success' => true,
                        'msg' => __('sms::lang.sms_group_delete_success')
            ];
        } catch (\Exception $e) {
            \Log::error('Error deleting SMS group: ' . $e->getMessage());
             $output = [
                        'success' => true,
                        'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()->back()->with('status', $output);
    }



}
