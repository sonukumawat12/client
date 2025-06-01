<?php

namespace Modules\Member\Http\Controllers;

use App\User;
use App\Member;
use App\UserSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Modules\Member\Entities\District;
use Modules\Member\Entities\Province;
use Modules\Member\Entities\MemberGroupTransferHistory;
use Spatie\Activitylog\Models\Activity;
use Modules\Member\Entities\Electrorate;
use Modules\Member\Entities\MemberGroup;
use Yajra\DataTables\Facades\DataTables;
use Modules\Member\Entities\Balamandalaya;
use Modules\Member\Entities\GramasevaVasama;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Business;


class MemberController extends Controller
{
    //   protected $businessUtil;
    protected $transactionUtil;
    //   public function __construct(BusinessUtil $businessUtil, TransactionUtil $transactionUtil)
    // {
    //     $this->businessUtil = $businessUtil;
    //     $this->transactionUtil =  $transactionUtil;
    // }

    public function __construct()
    {
        $this->transactionUtil =  app(TransactionUtil::class);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        // Get the current month date range

        if (request()->ajax()) {
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $db_start_date = request()->input('dob_start_date');
            $db_end_date = request()->input('dob_end_date');
            
        //     $member = Member::with('members')->leftjoin('gramaseva_vasamas', 'members.gramasevaka_area', 'gramaseva_vasamas.id')
        //         // ->leftjoin('balamandalayas', 'members.bala_mandalaya_area', 'balamandalayas.id')
        //         ->leftjoin('electrorates', 'members.electrorate_id', 'electrorates.id')
        //         ->leftjoin('districts', 'districts.id', 'electrorates.district_id')
        //         ->leftjoin('provinces', 'provinces.id', 'electrorates.province_id')
        //         ->leftjoin('member_groups', 'members.member_group', 'member_groups.id')
        //         ->select([
        //             'members.*',
        //             'gramaseva_vasamas.gramaseva_vasama as gramasevaka_area',
        //             'electrorates.name as electrorate',
        //             'districts.name as district',
        //             'provinces.name as province',
        //             // 'balamandalayas.balamandalaya as bala_mandalaya_area',
        //             'member_groups.member_group'
        //         ]);
        //     // if (!empty(request()->username)) {
        //     //     $member->where('username', request()->username);
        //     // }
        //     if (!empty($start_date) && !empty($end_date)) {
        //         $member->whereDate('members.created_at', '>=', $start_date);
        //         $member->whereDate('members.created_at', '<=', $end_date);
        //     }

        //     if (!empty($db_start_date) && !empty($db_end_date)) {
        //         $member->whereDate('members.date_of_birth', '>=', $db_start_date);
        //         $member->whereDate('members.date_of_birth', '<=', $db_end_date);
        //         $member->orWhereNull('members.date_of_birth');
        //     }
        //     if (!empty(request()->electrorate)) {
        //         $member->where('members.electrorate_id', request()->electrorate);
        //     }
        //     if (!empty(request()->province)) {
        //         $member->where('electrorates.province_id', request()->province);
        //     }
        //     if (!empty(request()->gramasevaka_area)) {
        //         $member->where('gramasevaka_area', request()->gramasevaka_area);
        //     }
        //     // if (!empty(request()->bala_mandalaya_area)) {
        //     //     $member->where('bala_mandalaya_area', request()->bala_mandalaya_area);
        //     // }
        //     if (!empty(request()->gender)) {
        //         $member->where('members.gender', request()->gender);
        //     }
        //     if (!empty(request()->district)) {
        //         $member->where('electrorates.district_id', request()->district);
        //     }
            
        //     if (!empty(request()->member_group)) {
        //         $member->where('members.member_group', request()->member_group);
        //     }
         
        //   //$member->whereNotNull('members.created_by')->where('members.created_by',auth()->id());
           
        //     $member->where(function ($query) {
        //         $query->whereNull('members.created_by') 
        //               ->orWhere('members.created_by', auth()->id()); 
        //     }); 
           
           $member = Member::with('members')->leftjoin('gramaseva_vasamas', 'members.gramasevaka_area', 'gramaseva_vasamas.id')
                // ->leftjoin('balamandalayas', 'members.bala_mandalaya_area', 'balamandalayas.id')
                ->leftjoin('electrorates', 'members.electrorate_id', 'electrorates.id')
                ->leftjoin('districts', 'districts.id', 'electrorates.district_id')
                ->leftjoin('provinces', 'provinces.id', 'electrorates.province_id')
                ->leftjoin('member_groups', 'members.member_group', 'member_groups.id')
                ->select([
                    'members.*',
                    'gramaseva_vasamas.gramaseva_vasama as gramasevaka_area',
                    'electrorates.name as electrorate',
                    'districts.name as district',
                    'provinces.name as province',
                    // 'balamandalayas.balamandalaya as bala_mandalaya_area',
                    'member_groups.member_group'
                ]);




            // if (!empty(request()->username)) {
            //     $member->where('username', request()->username);
            // }
            if (!empty($start_date) && !empty($end_date)) {
                $member->whereDate('members.created_at', '>=', $start_date);
                $member->whereDate('members.created_at', '<=', $end_date);
            }

            if (!empty($db_start_date) && !empty($db_end_date)) {
                $member->whereDate('members.date_of_birth', '>=', $db_start_date);
                $member->whereDate('members.date_of_birth', '<=', $db_end_date);
                $member->orWhereNull('members.date_of_birth');
            }

            if (!empty(request()->electrorate)) {
                $member->where('members.electrorate_id', request()->electrorate);
            }
            if (!empty(request()->province)) {
                $member->where('electrorates.province_id', request()->province);
            }
            if (!empty(request()->gramasevaka_area)) {
                $member->where('gramasevaka_area', request()->gramasevaka_area);
            }
            // if (!empty(request()->bala_mandalaya_area)) {
            //     $member->where('bala_mandalaya_area', request()->bala_mandalaya_area);
            // }
            if (!empty(request()->gender)) {
                $member->where('members.gender', request()->gender);
            }
            if (!empty(request()->district)) {
                $member->where('electrorates.district_id', request()->district);
            }
            


            if (!empty(request()->member_group)) {
                $member->where('members.member_group', request()->member_group);
            }

            //$member->whereNull('members.created_by')->orWhere('members.created_by',auth()->id());
            $member->where(function ($query) {
                $query->whereNull('members.created_by') // Fetch Parents (created_by is NULL)
                      ->orWhere('members.created_by', auth()->id()); // Fetch members created by the logged-in user
            });
           
           //dd($member->get()->toarray());
           
            return DataTables::of($member)
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
                        $html .= '<li><a href="#" data-href="' . action("\Modules\Member\Http\Controllers\MemberController@edit", [$row->id]) . '" class="btn-modal" data-container=".member_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Member\Http\Controllers\MemberController@show', [$row->id]) . "?view=member_info" . '"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action("\Modules\Member\Http\Controllers\MemberController@edits", [$row->id]) . '" class="btn-modal" data-container=".member_model"><i class="fa fa-eye-slash"></i> ' . __("Change Member Group") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Member\Http\Controllers\MemberController@show', [$row->id]) . "?view=documents_and_notes" . '"><i class="fa fa-paperclip"></i> ' . __("lang_v1.documents_and_notes") . '</a></li>';

                        return $html;
                    }
                )
                ->editColumn('date_of_birth', 
                function ($row) {
                    return ($row->date_of_birth)? date('m/d/Y',strtotime($row->date_of_birth)):' ';
                })
                ->editColumn('created_at', '{{@format_date($created_at)}}')
                ->editColumn('mobile_number_1', function ($row) {
                    $html = '';
                    $html .= $row->mobile_number_1;
                    $html .= '<br>'.$row->mobile_number_2;
                    $html .= '<br>'.$row->mobile_number_3;
                    return $html;
                    
                })
                ->editColumn('parent_id',function($row){
                    $html = '';
                    if($row->members->count() > 0)
                    {
                        foreach($row->members as $member){
                            $html .= '<button data-href="'.action('\Modules\Member\Http\Controllers\MemberController@view_profile_model',[$member->id]).'" data-container=".member_view_model" class="btn btn-xs btn-link btn-modal mb-5"><i class="glyphicon glyphicon-eye-open"></i> '.$member->name.'</button>';
                        }
                    }
                    return $html;
                    
                })
                ->editColumn('username', function($row){
                        $html = '<span class="badge ' . ($row->status == 0 ? 'bg-success' : 'bg-danger') . '">' . 
                                  $row->username . '</span>';
                        $html .= '&nbsp;&nbsp;&nbsp;';          
                        $html .= '<input type="checkbox" data-toggle="toggle" class="status-toggle" data-id="' . $row->id . '" 
                                  ' . ($row->status == 0 ? 'checked' : '') . '>';


                        return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['action','username','mobile_number_1','parent_id'])
                ->make(true);
        }

        $gramasevaka_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        // $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $member_groups = MemberGroup::pluck('member_group', 'id');
        // $towns = Member::distinct('town')->pluck('town', 'town');
        // $districts = Member::distinct('district')->pluck('district', 'district');
        // $usernames = Member::distinct('username')->pluck('username', 'username');
        $provinces = Province::has('electrorate')->pluck('name','id');
        $districts = District::has('electrorate')->pluck('name','id');
        $electrorates = Electrorate::pluck('name','id');
        
        return view('member::member.index')->with(compact(
            'gramasevaka_areas',
            // 'bala_mandalaya_areas',
            'member_groups',
            // 'towns',
            'districts',
            // 'usernames',
            'provinces',
            'electrorates'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $gramasevaka_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        // $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $member_groups = MemberGroup::pluck('member_group', 'id');
        $member_count = Member::count() + 1;
        $member_username = 'MEM' . $member_count;
        $electrorates = Electrorate::pluck('name','id');
        return view('member::member.create')->with(compact(
            'gramasevaka_areas',
            // 'bala_mandalaya_areas',
            'member_groups',
            'member_count',
            'member_username',
            'electrorates'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
     
        $business_id = $request->session()->get('business.id');
        Log::info($request);
        try {
            $data = $request->except('_token');
            $data['business_id'] = $business_id;
            $data['give_away_gifts'] = $request->give_away_gifts;
            $data['created_by'] = auth()->id;
            $data['date_of_birth'] = !empty($data['date_of_birth']) ? Carbon::parse($data['date_of_birth'])->format('Y-m-d') : date('Y-m-d');
            Member::create($data);
            
            
         
        $user_details = [
            'surname' => $data['name'],  // Assuming member has surname
            'username' => $data['username'] ,
            'password' => $data['password'] ?? Hash::make('default_password'),  // You can either use a default or user-provided password
            'language' => 'en',  // Set default language if needed
        ];

        // Automatically create a user for the member
        User::create_user($user_details);

            $output = [
                'success' => true,
                'msg' => __('member::lang.member_group_add_success')
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
        $member = Member::with('electrorate','electrorate.district','electrorate.province')->findOrFail($id);

        $member_dropdown = Member::pluck('name', 'id');

        //get contact view type : ledger, notes etc.
        $view_type = request()->get('view');
        if (is_null($view_type)) {
            $view_type = 'member_info';
        }

        return view('member::member.show')->with(compact(
            'member',
            'member_dropdown',
            'view_type'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $member = Member::with('members')->findOrFail($id);
        $gramasevaka_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        // $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $member_groups = MemberGroup::pluck('member_group', 'id');
        $electrorates = Electrorate::pluck('name','id');
       //, 'bala_mandalaya_areas'
        return view('member::member.edit')->with(compact('member', 'gramasevaka_areas', 'member_groups','electrorates'));
    }
       public function edits($id)
    {
         $member = Member::find($id);
        $member_group=$member->member_group;
         $member = Member::with('members')->findOrFail($id);
        $gramasevaka_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        // $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $member_groups = MemberGroup::pluck('member_group', 'id');
        $electrorates = Electrorate::pluck('name','id');
         $transfer_histories = MemberGroupTransferHistory::where('member_id', $member->id)
         ->with(['transferredFromGroup', 'transferredToGroup'])
                                ->orderBy('transferred_at', 'desc')
                                ->get();
        //$member_groups = MemberGroup::pluck('member_group', 'id');

        return view('member::member.change')->with(compact('member', 'gramasevaka_areas', 'member_groups','electrorates','member_group', 'transfer_histories'));
    }


    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method','family','add_on_member','member_date_of_birth');
            $data['date_of_birth'] = ($request->member_date_of_birth)?createDate($request->member_date_of_birth) : null;
            $old_record = Member::find($id)->toArray();
            $member = Member::where('id',$id)->update($data);
            $new_record = Member::find($id)->toArray();
            $attributes['attributes'] = [
                'old' => $old_record,
                'new' => $new_record,
            ];
            if($request->has('family'))
            {
                $attributes['attributes']['sub_member'] = $this->addOrUpdateMember($request->family,$id,$new_record);
            }
            if($member){
                $record = [
                    'id' => $id,
                    'type' => 'update',
                    'attributes' => $attributes,
                ];
                Member::storeActivity($record);
            }
            $output = [
                'success' => true,
                'msg' => __('member::lang.member_group_edit_success')
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
  public function member_update(Request $request, $id)
    {
        try {
            
            $data = $request->except('_token');
           
         // Get the existing member details before update
            $member = Member::find($id);
        $current_member_group = $member->member_group; // Store current group before the update

        // Parse the updated_at date if provided
        $data['updated_at'] = !empty($request->date_of_birth_filters) 
            ? Carbon::createFromFormat('m/d/Y', $request->date_of_birth_filters)->timestamp 
            : null;

        // Update the member's group
        $member->update([
            'member_group' => $data['transferto_member_group'],
            'updated_at' => $data['updated_at']
        ]);
         
           // Store the transfer history after updating the member's group
       MemberGroupTransferHistory::create([
            'member_id' => $id,
            'transferred_from' => $current_member_group, // Store the previous group
            'transferred_to' => $data['transferto_member_group'], // New group after update
            'transferred_by' => auth()->user()->id, // The user making the change
            'transferred_at' => now(), // Timestamp for when the transfer occurred
        ]);
          
            $output = [
                'success' => true,
                'msg' => __('member::lang.member_update_success')
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
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $old_record = Member::find($id)->toArray();
            
            $attributes['attributes'] = [
                'old' => $old_record,
                'sub_member' => Member::where('parent_id',$id)->toArray(),
            ];
            Member::where('id', $id)->delete();
            Member::where('parent_id',$id)->delete();
            $record = [
                'id' => $id,
                'type' => 'delete',
                'attributes' => $attributes,
            ];
            Member::storeActivity($record);
            $output = [
                'success' => true,
                'msg' => __('member::lang.member_delete_success')
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

    public function home()
    { 
        return view('member::member.home');
        
      
    }

    public function getProfile()
    {
        $member = Member::findOrFail(Auth::user()->id);
        $gramasevaka_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        $bala_mandalaya_areas = Balamandalaya::pluck('balamandalaya', 'id');
        $member_groups = MemberGroup::pluck('member_group', 'id');

        return view('member::member.profile')->with(compact(
            'member', 'gramasevaka_areas', 'bala_mandalaya_areas', 'member_groups'
        ));
    }

    public function updateProfile(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method');
            $data['date_of_birth'] = !empty($data['date_of_birth']) ? Carbon::parse($data['date_of_birth'])->format('Y-m-d') : null;

            Member::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('member::lang.profile_update_success')
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
     * Add Fimaily Member row 
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function addMememberRow(Request $request)
    {
        $row_number = $request->row + 1;
        $member_code = $request->member_code.'-'.$row_number;
        $output = [
            'success' => true,
            'html' => view('member::member.partials.family_box',compact('member_code','row_number'))->render(),
            'row' => $row_number,
        ];
        return $output;
    }

    /**
     * Create Or Update Family Memeber
     *
     *  
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function addOrUpdateMember($member_records, $family_member_id,$member_dataa)
    {

         $electrorate=(int) $member_dataa['electrorate_id'];
         $gramasevaka_area= (int) $member_dataa['gramasevaka_area'];
         $password=  Hash::make('123456');
         $address=  $member_dataa['address'];
       
        if(empty($member_records))
        return false;
        $member_id = [];
        $backup_recod = [];
        $old_recod =  Member::where('parent_id',$family_member_id)->get()->toArray();
        
        foreach ($member_records as $key => $value) {
            if($value['member_name']){
                $member_data = [
                    'username' => $value['username'],
                    'password' => $password,
                    'name' => $value['member_name'],
                    'address' => $address,
                    'created_by' => auth()->user()->id,
                    'district' =>   null,
                    'town' =>    null,
                    'mobile_number_1' => $value['member_mobile_number_1'] ?? null,
                    'mobile_number_2' => $value['member_mobile_number_2'] ?? null,
                    'mobile_number_3' => $value['member_mobile_number_3'] ?? null,
                    'land_number' =>   null,
                    'gender' => $value['member_gender'] ?? null,
                    'date_of_birth' =>($value['date_of_birth'])?createDate($value['date_of_birth']) : null,
                    'gramasevaka_area' => $gramasevaka_area,
                    'bala_mandalaya_area' =>null,
                    'member_group' =>null,
                    'electrorate_id' =>  $electrorate,
                    'parent_id' => $family_member_id,
                    'relation_name' => $value['relation'] ?? null,

                ];
               
                if(isset($value['id'])){
                $member = Member::find($value['id'])->update($member_data);
                $member_id[] = $value['id'];
                $member_data['activity_type'] = 'update';
                $member_data['id'] = $value['id'];
                $backup_recod[] = $member_data;
                }else{
                    
                $member = Member::create($member_data);
               
                $member_id[] = $member->id;
                $member = $member->toArray(); 
                $member['activity_type'] = 'create';
                $backup_recod[] =  $member;
                }
            }
        }

        Member::whereNotIn('id',$member_id)->where('parent_id',$family_member_id)->delete();
         
        return [ 
            'old' => $old_recod, 
            'new' => $backup_recod,
        ];
        
    }
    /**
     *  View Model Profile of Member 
     *
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function view_profile_model($id)
    {
        $member = Member::find($id);
        return view('member::member.family_member.show',compact('member'));
       
    }

    /**
     * Get list of family members 
     * @throws conditon
     **/
    public function all_sub_member()
    {
        
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $id = request()->input('member_id');
            $member = Member::select([
                    'members.*',
                ]);
            $member->where('parent_id',$id);

            return DataTables::of($member)
                ->editColumn('date_of_birth', 
                function ($row) {
                    return ($row->date_of_birth)? date('m/d/Y',strtotime($row->date_of_birth)):' ';
                })
                ->editColumn('created_at', '{{@format_date($created_at)}}')
                // ->editColumn('mobile_number_1', function ($row) {
                //     $html = '';
                //     $html .= $row->mobile_number_1;
                //     $html .= '<br>'.$row->mobile_number_2;
                //     $html .= '<br>'.$row->mobile_number_3;
                //     return $html;
                    
                // })
                ->editColumn('name',function($row){
                    $html = '';
                    $html .= '<button data-href="'.action('\Modules\Member\Http\Controllers\MemberController@view_profile_model',[$row->id]).'" data-container=".member_view_model" class="btn btn-xs btn-link btn-modal mb-5">'.$row->name.'</button>';
                        
                    return $html;
                    
                })
               
                ->removeColumn('id')
                ->rawColumns(['name'])
                
                ->make(true);
        }
    }

    /**
     * @doc: 6928 Member Module User Activity
     * @dev: Sakhawat (sakhideveloper@gmail.com)
     *
     * Member User Activity 
     *
     **/
    public function memberUserActivity()
    {
        if (!auth()->user()->can('user_activity.view')) {

            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');
         
      
        if (request()->ajax()) {

            $with = [];
            $start_date=request()->startDate;
            $end_date=request()->endDate;
          $business_users = User::where('business_id', $business_id)->pluck('id')->toArray();
        
               $activity = Activity::whereIn('causer_id', $business_users)
                    ->where('subject_type', 'App\Member')
                    ->get();     
         
           
            if (!empty(request()->user) && request()->user !='All') {

                $user = request()->user;

                $activity->where('causer_id', $user);
            }
            
            if(!empty(request()->type) && request()->type !='All') {
                $type = request()->type;
        
                $activity->where('description', $type);
            }
            if(!empty(request()->subject) && request()->subject !='All') {
               
                $subject = request()->subject;

                $activity->where('log_name', $subject);
            }
            
          if(!empty($start_date) && !empty($end_date) ) {
                
            $activity->whereBetween('created_at', [$start_date, $end_date]);
               
            }

            $datatable = Datatables::of($activity)

                ->editColumn('created_at', '{{ @format_datetime($created_at) }}')

                ->removeColumn('id')

                ->editColumn('causer_id', function ($row) {

                    $causer_id = $row->causer_id;

                    $username = User::where('id', $causer_id)->select('username')->first()->username;

                    return $username;
                })
                ->addColumn('ref_no',function($row){
                    $attributes = json_decode($row->properties,true);
                    
                    $attributes = $attributes['attributes'] ?? [];
                    $prev_activity = Activity::where('id','<',$row->id)->where('subject_id',$row->subject_id)->where('subject_type','App\Member')->latest()->first();
                    if($prev_activity){
                    $username = User::where('id', $prev_activity->causer_id)->select('username')->first()->username;

                    return $username;
                    }
                    return '';
                })
                ->addColumn('description_details',function($row){
                    $feild_name = Member::FIELDS;
                    $html = "";
                    
                    if($row->description == 'update'){
                       
                        $attributes = json_decode($row->properties,true);
                        $new = $attributes['attributes']['new'] ?? [];
                        $old = $attributes['attributes']['old'] ?? [];
                        $html ='<b>Members:</b> <br>';
                        foreach ($new as $key => $newValue) {
                            if($key != 'created_at' && $key != 'updated_at' && $key != 'id'){
                                $oldValue = $old[$key] ?? null;
                            
                                if ($newValue !== $oldValue) {
                                    if(key_exists($key,$feild_name)){
                                    $originalKey = __($feild_name[$key]);
                                    if($key != 'password'){
                                    $html .= "<span class='text-info'>$originalKey</span> : $oldValue <span class='text-primary'>changed to </span> $newValue <br>";
                                    }
                                    else
                                    {
                                        $html .= "<span class='text-info'>Change $originalKey</span> <br>";
                                       
                                    }
                                    }
                                }
                            }
                                
                        }

                      //  $subMumber = $attributes['attributes']['sub_member'];
                        if (isset($attributes['attributes']['sub_member'])) {
                                $subMember = $attributes['attributes']['sub_member'];
                            } else {
                                // Handle the case when sub_member is not found
                                $subMember = null; // or set a default value
                                // Optionally log or throw an error
                                // Log::warning('sub_member not found in attributes', ['row' => $row]);
                            }
                        $oldSubMumber = $subMumber['old'] ?? [];
                        $newSubMumber = $subMumber['new'] ?? [];
                        if(!empty($newSubMumber))
                        {
                            $html .='<b>Family Members:</b><br>';
                            foreach($newSubMumber as  $singleMember)
                            {
                                if(isset($singleMember['activity_type']) && $singleMember['activity_type'] == 'update'){
                                    $html .= '<span class="text-info"><i class="fa fa-edit"></i>'.__('business.name').'</span>: '.$singleMember['name'].'<br>';
                                    foreach($oldSubMumber as $k =>$old){
                                        if($old['id'] == $singleMember['id']){  
                                            unset($oldSubMumber[$k]);
                                            foreach($singleMember as $key => $newValue){
                                                if($key != 'created_at' && $key != 'updated_at' && $key != 'id' && $key != 'activity_type'){
                                                    $oldValue = $old[$key] ?? null;
                                                    if ($newValue !== $oldValue) {
                                                        if($key != 'password' && key_exists($key,$feild_name)){
                                                            $originalKey = __($feild_name[$key]);
                                                            if($key == 'electrorate_id'){
                                                                $oldValue = Electrorate::find($oldValue)->name;
                                                                $newValue = Electrorate::find($newValue)->name;
                                                                $html .= "<span class='text-info'>$originalKey</span> : $oldValue <span class='text-primary'>changed to </span> $newValue <br>";
                                                            
                                                            }
                                                            elseif($key == 'bala_mandalaya_area'){
                                                                $oldValue = Balamandalaya::find($oldValue)->balamandalaya;
                                                                $newValue = Balamandalaya::find($newValue)->balamandalaya;
                                                                $html .= "<span class='text-info'>$originalKey</span> : $oldValue <span class='text-primary'>changed to </span> $newValue <br>";
                                                            
                                                            }
                                                            elseif($key == 'gramasevaka_area'){
                                                                $oldValue = GramasevaVasama::find($oldValue)->gramaseva_vasama;
                                                                $newValue = GramasevaVasama::find($newValue)->gramaseva_vasama;
                                                                $html .= "<span class='text-info'>$originalKey</span> : $oldValue <span class='text-primary'>changed to </span> $newValue <br>";
                                                            
                                                            }
                                                            elseif($key != 'password'){
                                                                $html .= "<span class='text-info'>$originalKey</span> : $oldValue <span class='text-primary'>changed to </span> $newValue <br>";
                                                            }
                                                            else
                                                            {
                                                                $html .= "<span class='text-info'>Change $originalKey</span> <br>";
                                                            
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            break;
                                        }
                                    }
                                }
                                else
                                {
                                    $html .= '<span class="text-info"><i class="fa fa-plus"></i>'.__('business.name').'</span>: '.$singleMember['name'].'<br>';
                               
                                }

                               

                            }
                            if(!empty($oldSubMumber))
                            {
                                 foreach ($oldSubMumber as $singleMember) {
                                    $html .= '<span class="text-info"><i class="fa fa-trash"></i>'.__('business.name').'</span>: '.$singleMember['name'].'<br>';
                                }
                            }
                            
                        }

                        //
                    }elseif($row->description == 'delete'){
                        $attributes = json_decode($row->properties,true);
                        $old = $attributes['attributes']['old'] ?? [];
                        $old_sub_member = $attributes['attributes']['sub_member'] ?? [];
                        $html ='<b>Members:</b> <br>';
                        $html .= '<span class="text-info"><i class="fa fa-trash"></i>'.__('business.name').'</span>: '.$old['name'].'<br>';
                        if(!empty($old_sub_member)){
                        $html .='<b>Family Members:</b><br>';
                            foreach($old_sub_member as $old)
                            {
                                $html .= '<span class="text-info"><i class="fa fa-trash"></i>'.__('business.name').'</span>: '.$old['name'].'<br>';
                                
                            }
                        }
                        
                        
                    }
                    elseif($row->description == 'created'){
                        $attributes = json_decode($row->properties,true);
                        $attributes = $attributes['attributes'] ?? [];
                        $html ='<b>Members:</b> <br>';
                           
                        $html .= '<span class="text-info">'.__('business.name').'</span>: '.$attributes['name'].'<br>';
                        $html .= '<span class="text-info">'.__('business.address').'</span>: '.$attributes['address'].'<br>';
                        if(key_exists('sub_member',$attributes))
                        {
                            $html .='<b>Family Members:</b><br>';
                            foreach($attributes['sub_member']['new'] as $member){
                            $html .= '<span class="text-info"><i class="fa fa-plus"></i> '.__('business.name').'</span>: '.$member['name'].'<br>';
                            }
                        }    
                    }
                    else
                    {
                        $html = "";
                    }
                    
                    return $html;
                });

            $rawColumns = ['description_details'];

            return $datatable->rawColumns($rawColumns)

                ->make(true);
        }

        $users = User::where('business_id', $business_id)->pluck('username', 'id');
        
        $type = Activity::distinct()->where('subject_type','App\Member')->pluck('description');
        $subject = Activity::distinct()->where('subject_type','App\Member')->pluck('log_name');

        return view('member::activity_report.index')

            ->with(compact('users','type','subject'));
    
    }
        public function smsSettings()
    {
        // if (!auth()->user()->can('user_activity.view')) {

        //     abort(403, 'Unauthorized action.');
        // }
         $business_id = request()->session()->get('user.business_id');
         
        $users = User::where('member',1)->pluck('username', 'id');
         
        return view('member::member.sms_settings')

            ->with(compact('users'));
    }
    
    public function submitQuickSend(Request $request){
        
        $business_id = request()->session()->get('business.id');
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            
            $user=User::where('id',$request->user)->first();

            if (!$user) {
                throw new \Exception('User not found');
            }

            $member=Member::where('username',$user->username)->first();
             
            DB::beginTransaction();
             $member_password=123456;
              $user_data = [
                'password' => ($member_password)? Hash::make($member_password) :'',
               

            ];
           
            
           // $correct_phones = $this->transactionUtil->validateNos($member->mobile_number_1)['valid'];
         
            
            $business = Business::where('id', $business_id)->first();
            
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
             
            $message="Your new Passcode is:123456";
            if(!empty($sms_settings)){
               
                
                if(!empty(!empty($member->mobile_number_1))){
                      
                  $data = [
                    'sms_settings' => $sms_settings,
                    'mobile_number' => is_array($member->mobile_number_1) ? implode(',', $member->mobile_number_1) : $member->mobile_number_1,
                    'sms_body' => $message
                ];
                 
                    
                    $this->transactionUtil->sendSms($data,'Quick Send');
            
                       DB::table('users')
                        ->where('id', $request->users) // Specify the condition for the update
                        ->update(array_filter($user_data)); // Use array_filter to skip null values
            
                      DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
                }
                
                
            }
          
        } catch (\Exception $e) {
            DB::rollback();
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong') . $e->getMessage()
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    
    public function updateStatus(Request $request)
    {
        $member = Member::findOrFail($request->id);
        $member->status = $request->status;
        $member->save();
    
        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

}