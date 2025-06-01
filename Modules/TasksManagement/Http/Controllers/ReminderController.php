<?php

namespace Modules\TasksManagement\Http\Controllers;

;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\TasksManagement\Entities\Reminder;
use Yajra\DataTables\Facades\DataTables;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $reminders = Reminder::where('business_id', $business_id)->orderBy('reminder_date','desc')->select([
                'reminders.*'
            ]);
            $options = Reminder::getOptionArray();
            $other_pages = Reminder::getOtherPagesArray();
            $yes_no = ['0' => 'No', '1' => 'Yes'];

            return DataTables::of($reminders)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\ReminderController@show', [$row->id]) . '" class="btn-modal" data-container=".reminder_model"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\ReminderController@edit', [$row->id]) . '" class="btn-modal" data-container=".reminder_model"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\ReminderController@destroy', [$row->id]) . '" class="delete_reminder"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</a></li>';
                        
                        if($row->cancel == 1){
                            $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\ReminderController@enable', [$row->id]) . '" class="enable_disable"><i class="fa fa-toggle-on" aria-hidden="true"></i> ' . __("messages.enable") . '</a></li>';

                        }else{
                            $html .= '<li><a href="#" data-href="' . action('\Modules\TasksManagement\Http\Controllers\ReminderController@disable', [$row->id]) . '" class="enable_disable"><i class="fa fa-toggle-off" aria-hidden="true"></i> ' . __("messages.disable") . '</a></li>';

                        }
                        
                        $html .=  '</ul></div>';
                        return $html;
                    }
                )


                ->editColumn('options',  function ($row) use ($options) {
                    return $options[$row->options];
                })
                ->editColumn('reminder_date', '{{@format_datetime($reminder_date)}}')
                ->editColumn('snooze',  function ($row) use ($yes_no) {
                    return $yes_no[$row->snooze];
                })
                ->editColumn('cancel',  function ($row) use ($yes_no) {
                    return $yes_no[$row->cancel];
                })
                ->editColumn('other_pages',  function ($row) use ($other_pages) {
                    $pages = '';
                    if (!empty($row->other_pages)) {
                        foreach ($row->other_pages as $other_page) {
                            $pages .= $other_pages[$other_page];
                            $pages .= ', ';
                        }
                    }
                    return $pages;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'color', 'shared_with_users'])
                ->make(true);
        }


        return view('tasksmanagement::reminders.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $options = Reminder::getOptionArray();
        $other_pages = Reminder::getOtherPagesArray();

        return view('tasksmanagement::reminders.create')->with(compact('options', 'other_pages'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->except('_token');
            $input['business_id'] = request()->session()->get('business.id');
            
            if(strtotime($input['reminder_date']) > time()){
                $minutes =ceil((strtotime($input['reminder_date']) - time()) / 60);
                $input['time_type'] = "minutes";
                $input['time'] = $minutes;
            }elseif(strtotime($input['reminder_date']) < time()){
                $input['cancel'] = 1;
            }
            
            Reminder::create($input);

            $output = [
                'success' => true,
                'msg' => __('tasksmanagement::lang.reminder_create_success')
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
        $options = Reminder::getOptionArray();
        $other_pages = Reminder::getOtherPagesArray();
        $reminder = Reminder::findOrFail($id);
        return view('tasksmanagement::reminders.show')->with(compact('options', 'other_pages','reminder'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $options = Reminder::getOptionArray();
        $other_pages = Reminder::getOtherPagesArray();
        $reminder = Reminder::findOrFail($id);
        return view('tasksmanagement::reminders.edit')->with(compact('options', 'other_pages','reminder'));;
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
    public function destroy($id)
    {
        $reminder = Reminder::findOrFail($id);
        $deleted = $reminder->delete();
        if($deleted){
            $output = [
                    'success' => true,
                    'msg'     => __('messages.deleted_successfully'),
                ];
        }else{
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return $output;
        
    }
    
    public function enable($id)
    {
        $enabled = Reminder::where('id', $id)->update(['cancel' => '0']);
        if($enabled){
            $output = [
                    'success' => true,
                    'msg'     => __('messages.deleted_successfully'),
                ];
        }else{
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return $output;
        
    }
    public function disable($id)
    {
        $enabled = Reminder::where('id', $id)->update(['cancel' => '1']);
        if($enabled){
            $output = [
                    'success' => true,
                    'msg'     => __('messages.deleted_successfully'),
                ];
        }else{
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return $output;
        
    }
    /**
     * Cancel reminder
     * @return Response
     */
    public function cancelReminder($id)
    {
        Reminder::where('id', $id)->update(['cancel' => '1']);

        return redirect()->back();
    }
    /**
     * snooze reminder
     * @return Response
     */
    public function snoozeReminder($id, Request $request)
    {
        $reminder = Reminder::findOrFail($id);
        if($reminder->crm_reminder == 0){
            if( $request->time_type == 'minutes'){
                $snoozed_at = \Carbon::now()->addMinutes($request->time)->format('Y-m-d H:i:s');
            }
            if( $request->time_type == 'hours'){
                $snoozed_at = \Carbon::now()->addHours($request->time)->format('Y-m-d H:i:s');
            }
            if( $request->time_type == 'days'){
                $snoozed_at = \Carbon::now()->addDays($request->time)->format('Y-m-d H:i:s');
            }
            if( $request->time_type == 'weeks'){
                $snoozed_at = \Carbon::now()->addWeeks($request->time)->format('Y-m-d H:i:s');
            }
            if( $request->time_type == 'months'){
                $snoozed_at = \Carbon::now()->addMonths($request->time)->format('Y-m-d H:i:s');
            }
            Reminder::where('id', $id)->update(['time' => $request->time, 'time_type' => $request->time_type, 'snooze' => '1', 'snoozed_at' => $snoozed_at]);

        }else{
            $snooze_time = $request->snooze_time;
            $snooze_date = \Carbon::parse($request->snooze_date)->format('Y-m-d');
            Reminder::where('id', $id)->update(['snooze' => '1', 'snoozed_at' => $snooze_date.' '.$snooze_time]);
        }



        return redirect()->back();
    }
}
