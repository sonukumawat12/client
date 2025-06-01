<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\BulkNotification;
use Illuminate\Http\Request;
use Modules\HelpGuide\Entities\User;
use Modules\HelpGuide\Entities\CustomerGroup;

use Illuminate\Validation\Rule;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Http\Resources\BulkNotificationResource as BulkNotificationResource;
use Modules\HelpGuide\Mail\BulkNotificationMail;
use Exception;
use Illuminate\Support\Facades\Mail;

class BulkNotificationController extends Controller
{
    public $channels = array(
        ['id' => 'email', 'name' => 'Email'],
        ['id' => 'hutch_sms', 'name' => 'Hutch SMS'],
        ['id' => 'ultimate_sms', 'name' => 'Ultimate SMS']
    );

    public $mode = array(
        ['id' => 'customer_groups', 'name' => 'Customer Groups'],
        ['id' => 'customers', 'name' => 'Customers']
    );

    public function index()
    {
        return view('helpguide::dashboard.bulk_notifications.index');
    }

    public function get_channels(Request $request)
    {
        return response()->json([
            'channels' => $this->channels,
            'mode' => $this->mode,
            'customer_groups' => CustomerGroup::all(),
            'customers' => User::orderBy('id', 'desc')->get()
        ]);
    }

    public function show(Request $request)
    {
        $category = BulkNotification::findOrFail($request->input('id'));
        return new BulkNotificationResource($category);
    }

    public function fetch()
    {
        $category = BulkNotification::orderBy('id','DESC')->paginate(50);
        return BulkNotificationResource::collection($category);
    }



    public function store(Request $request)
    {

        $validatedData = Validator::make($request->all(), [
            'subject' => 'required|max:255',
            'message' => 'required',
            'channel' => 'required',
            'notify_by' => 'required',
            'customer_groups' => 'nullable', // initial rule
            'customers' => 'nullable',       // initial rule
        ])->sometimes('customer_groups', 'required', function ($input) {
            return $input->notify_by['value'] === 'customer_groups';
        })->sometimes('customers', 'required', function ($input) {
            return $input->notify_by['value'] === 'customers';
        });

        if ($validatedData->fails()) {
            return ['status' => 'fail', "errors" => $validatedData->errors()];
        }

        $contacts = [];
        $emails = [];
        $phone_nos = [];
        if ($request->notify_by['value'] === 'customer_groups') {
            foreach ($request->input('customer_groups') as $group) {
                $user = User::orWhereRaw("FIND_IN_SET(?, REPLACE(REPLACE(users.groups, '[', ''), ']', '')) > 0", [$group['value']])->get();
                if (!empty($user)) {
                    foreach ($user as $t) {
                        $contacts[] = $t->id;
                        if (!empty($t->email)) {
                            $emails[] = $t->email;
                        }

                        if (!empty($t->phone_no)) {
                            $phone_nos[] = $t->phone_no;
                        }
                    }
                }
            }
        }

        if ($request->notify_by['value'] === 'customers') {
            foreach ($request->input('customers') as $one) {
                $contacts[] = $one['value'];

                $t = User::findOrFail($one['value']);
                if (!empty($t->email)) {
                    $emails[] = $t->email;
                }

                if (!empty($t->phone_no)) {
                    $phone_nos[] = $t->phone_no;
                }
            }
        }


        $category = new BulkNotification();
        $category->subject = $request->input('subject');
        $category->notify_by = $request->channel['value'];
        $category->body = $request->input('message');
        $category->contacts = $contacts;
        $saved = $category->save();

        if ($saved) {

            if ($request->channel['value'] == 'email') {
                foreach ($emails as $email) {
                    try{
                        Mail::to($email)->send(new BulkNotificationMail($category));
                    }catch(Exception $e){
                        continue;
                    }
                    
                }
            }

            if ($request->channel['value'] == 'hutch_sms') {
                foreach ($phone_nos as $phone_no) {
                    try{
                        BulkNotification::hutchSendSMS($phone_no, $category->body);
                    }catch(Exception $e){
                        continue;
                    }
                    
                }
            }

            if ($request->channel['value'] == 'ultimate_sms') {
                try{
                    BulkNotification::ultimateSMS($phone_no, $category->body);
                }catch(Exception $e){
                }                
            }

            return new BulkNotificationResource($category);
        }
    }


    public function destroy(Request $request, BulkNotification $category)
    {
        if ($category->delete()) {
            return new BulkNotificationResource($category);
        }
    }
}
