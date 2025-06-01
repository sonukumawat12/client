<?php

namespace Modules\Member\Http\Controllers;
use App\User;
use App\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\Balamandalaya;
use Modules\Member\Entities\ServiceArea;
use Modules\Member\Entities\Suggestion;
use Yajra\DataTables\Facades\DataTables;
use Modules\Member\Entities\MemberStaff;
use Modules\Member\Entities\GramasevaVasama;
use App\Utils\LocationUtil;


class SuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        
        $business_id = request()->session()->get('business.id');
        $bala_mandalaya_areas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        
        $service_areas = ServiceArea::pluck('service_area', 'id');
        $name_of_suggestions = Suggestion::pluck('heading', 'id');
        $details_of_suggestions = Suggestion::pluck('details', 'id');
        $area_which_involved = Suggestion::pluck('area_name', 'id');
        $state_of_urgencies  = Suggestion::getStateOfUrgenciesArray();
        $solution_givens = Suggestion::getSolutionGivenArray();
        $suggestion_number = Suggestion::pluck('suggestion_number','id');
        if (request()->ajax()) {
             $users = User::where('id',Auth::user()->id)
                        ->where('member', 1)
                        ->first();
            if ($users) {
              $member = Member::where('username',$users->username)
                        ->first();
            $suggestions = Suggestion::with('staff')->leftjoin('gramaseva_vasamas', 'suggestions.balamandalaya_id', 'gramaseva_vasamas.id')
                ->leftjoin('service_areas', 'suggestions.service_area_id', 'service_areas.id')
                ->leftjoin('members', 'suggestions.member_id', 'members.id')
                 ->where('member_id', $member->id)
                ->select([
                    'suggestions.*',
                    'service_areas.service_area',
                    'gramaseva_vasamas.gramaseva_vasama',
                    'members.name as member_name'
                ]);
                }
                else
                {
                     $suggestions = Suggestion::with('staff')->leftjoin('gramaseva_vasamas', 'suggestions.balamandalaya_id', 'gramaseva_vasamas.id')
                ->leftjoin('service_areas', 'suggestions.service_area_id', 'service_areas.id')
                ->leftjoin('members', 'suggestions.member_id', 'members.id')
                ->select([
                    'suggestions.*',
                    'service_areas.service_area',
                     'gramaseva_vasamas.gramaseva_vasama',
                    'members.name as member_name',
                    
                ]);
                }

            if (!empty(request()->balamandalaya_id)) {
                $suggestions->where('balamandalaya_id', request()->balamandalaya_id);
            }
            if (!empty(request()->service_area_id)) {
                $suggestions->where('service_area_id', request()->service_area_id);
            }
            if (!empty(request()->heading)) {
                $suggestions->where('heading', request()->heading);
            }
            if (!empty(request()->details)) {
                $suggestions->where('details', request()->details);
            }
            if (!empty(request()->is_common_problem)) {
                $suggestions->where('is_common_problem', request()->is_common_problem);
            }
            if (!empty(request()->area_name)) {
                $suggestions->where('area_name', request()->area_name);
            }
            if (!empty(request()->state_of_urgency)) {
                $suggestions->where('state_of_urgency', request()->state_of_urgency);
            }
            if (!empty(request()->solution_given)) {
                $suggestions->where('solution_given', request()->solution_given);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $suggestions->whereDate('suggestions.date', '>=', request()->start_date);
                $suggestions->whereDate('suggestions.date', '<=', request()->end_date);
            }
            if (Suggestion::checkMemberorNot()) {
                $suggestions->where('member_id', Auth::user()->id);
            }
            if (!empty(request()->suggestion_number)) {
                $suggestions->where('suggestion_number', request()->suggestion_number);
            }
            return DataTables::of($suggestions)
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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Member\Http\Controllers\SuggestionController@show', [$row->id]) . '" class="btn-modal" data-container=".suggestion_model"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Member\Http\Controllers\SuggestionController@edit', [$row->id]) . '" class="btn-modal" data-container=".suggestion_model"><i class="fa fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                        if (auth()->user()->can('update_status_of_issue')) {
                            $html .= '<li><a href="#" data-href="' . action("\Modules\Member\Http\Controllers\SuggestionController@getUpdateStatus", [$row->id]) . '" class="btn-modal" data-container=".suggestion_model"><i class="glyphicon glyphicon-edit"></i> ' . __("member::lang.change_status") . '</a></li>';
                        }
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Member\Http\Controllers\MemberStaffController@assignToSuggestion', [$row->id]) . '" class="btn-modal" data-container=".suggestion_model"><i class="fa fa-user"></i> ' . __("member::lang.assigned_staff") . '</a></li>';
                       $html .= '<li><a href="#" data-href="' . action('\Modules\Member\Http\Controllers\SuggestionController@remark', [$row->id]) . '" class="btn-modal" data-container=".suggestion_model"><i class="fa fa-comment"></i> ' . __("Remark") . '</a></li>';
                       $html .= '<li><a href="#" data-href="' . action('\Modules\Member\Http\Controllers\SuggestionController@document', [$row->id]) . '" class="btn-modal" data-container=".suggestion_model"><i class="fa fa-file"></i> ' . __("Document") . '</a></li>';
                        $html .= '</ul></div>';
                        $html .= '<li class="dropdown-header">Status: ' . htmlspecialchars($row->status) . '</li>';
                        return $html;
                    }
                )
                ->editColumn('date', '{{@format_date($date)}}')
               ->editColumn('assigned_to_member_id', function ($row) {
                    $staff = MemberStaff::find($row->member_id);
                    return $staff ? $staff->name : ''; 
                })
                ->editColumn('state_of_urgency', '{{ucfirst($state_of_urgency)}}')
               ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }


        return view('member::suggestion.index')->with(compact(
            'bala_mandalaya_areas',
            'service_areas',
            'name_of_suggestions',
            'details_of_suggestions',
            'area_which_involved',
            'state_of_urgencies',
            'solution_givens',
            'suggestion_number'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {

        $balamandalayas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        
        $service_areas = ServiceArea::pluck('service_area', 'id');
        $state_of_urgencies = Suggestion::getStateOfUrgenciesArray();
        $solution_givens = Suggestion::getSolutionGivenArray();
        $members = Member::pluck('name', 'id');
 
 
        return view('member::suggestion.create')->with(compact(
            'balamandalayas',
            'state_of_urgencies',
            'solution_givens',
            'members',
            'service_areas'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $location_resp = LocationUtil::getRequestLocation( $request );
        
        try {
            
            $input = $request->except('_token');
            $input['date'] = !empty($input['date']) ? Carbon::parse($input['date'])->format('Y-m-d') : date('Y-m-d');

            if (Suggestion::checkMemberorNot()) {
                $input['member_id'] = Auth::user()->id;
            }
            
            //upload suggestion file
            if (!file_exists('./uploads/suggestion/' . $input['member_id'])) {
                mkdir('./uploads/suggestion/' . $input['member_id'], 0777, true);
            }

            if ($request->hasfile('upload_document')) {

                $file = $request->file('upload_document');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/uploads/suggestion/' . $input['member_id'], $filename);
                $uploadFileFicon = 'uploads/suggestion/' . $input['member_id'] . '/' . $filename;
                $input['upload_document'] = $uploadFileFicon;
            } else {
                $input['upload_document'] = '';
            }
            $maxSuggestionNumber = Suggestion::max('suggestion_number');
            
            $newSuggestionNumber = $maxSuggestionNumber ? $maxSuggestionNumber + 1 : 1;
            
            
            $input['suggestion_number'] = $newSuggestionNumber;
            $input['status'] = 'Pending';
            $suggestion = Suggestion::create($input);
            
            if( $location_resp['success'] ){
                LocationUtil::storeUserLocation( Auth::user()->id, 'suggestion_create', $location_resp['data'] );
            }
            
            
            $output = [
                'success' => true,
                'modal'  => 'correct',
                'msg' => __('Thank you for your feedback. Your suggestions No. ' . $suggestion->id)
            ];
            return redirect()->back()->with('status', $output);
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
               $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $users = User::where('id',Auth::user()->id)
                        ->first();
                         
        $suggestion = Suggestion::leftjoin('gramaseva_vasamas', 'suggestions.balamandalaya_id', 'gramaseva_vasamas.id')
            ->leftjoin('service_areas', 'suggestions.service_area_id', 'service_areas.id')
            ->leftjoin('members', 'suggestions.member_id', 'members.id')
            ->where('suggestions.id', $id)->select(
                'suggestions.*',
                'gramaseva_vasamas.gramaseva_vasama',
                'service_areas.service_area'
            )->first();
        $user=$users->username;
        return view('member::suggestion.show')->with(compact(
            'suggestion','user'
        ));
    }
    public function document($id)
    {
        $suggestion = Suggestion::leftjoin('gramaseva_vasamas', 'suggestions.balamandalaya_id', 'gramaseva_vasamas.id')
            ->leftjoin('service_areas', 'suggestions.service_area_id', 'service_areas.id')
            ->leftjoin('members', 'suggestions.member_id', 'members.id')
            ->where('suggestions.id', $id)->select(
                'suggestions.*',
                'gramaseva_vasamas.gramaseva_vasama',
                'service_areas.service_area'
            )->first();

        return view('member::suggestion.document')->with(compact(
            'suggestion'
        ));
    }
    public function remark($id)
    {
        $suggestion = Suggestion::leftjoin('gramaseva_vasamas', 'suggestions.balamandalaya_id', 'gramaseva_vasamas.id')
            ->leftjoin('service_areas', 'suggestions.service_area_id', 'service_areas.id')
            ->leftjoin('members', 'suggestions.member_id', 'members.id')
            ->where('suggestions.id', $id)->select(
                'suggestions.*',
                   'gramaseva_vasamas.gramaseva_vasama',
                'service_areas.service_area'
            )->first();

        return view('member::suggestion.remark')->with(compact(
            'suggestion'
        ));
    }
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        
        $balamandalayas = GramasevaVasama::pluck('gramaseva_vasama', 'id');
        $service_areas = ServiceArea::pluck('service_area', 'id');
        $state_of_urgencies = Suggestion::getStateOfUrgenciesArray();
        $solution_givens = Suggestion::getSolutionGivenArray();
        $members = Member::pluck('name', 'id');
        
      $suggestion = Suggestion::leftjoin('gramaseva_vasamas', 'suggestions.balamandalaya_id', 'gramaseva_vasamas.id')
            ->leftjoin('service_areas', 'suggestions.service_area_id', 'service_areas.id')
            ->leftjoin('members', 'suggestions.member_id', 'members.id')
            ->where('suggestions.id', $id)->select(
                'suggestions.*',
                  'gramaseva_vasamas.gramaseva_vasama',
                'service_areas.service_area'
            )->first();
 
        return view('member::suggestion.update')->with(compact(
            'suggestion','members','balamandalayas','state_of_urgencies','solution_givens','service_areas'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        try {
           
                $input = $request->except('_token');
                $input['date'] = !empty($input['date']) ? Carbon::parse($input['date'])->format('Y-m-d') : date('Y-m-d');
                
                if (Suggestion::checkMemberorNot()) {
                    $input['member_id'] = Auth::user()->id;
                }
                
                // Determine if we're updating an existing suggestion
                if (isset($input['input_id'])) {
                    $suggestion = Suggestion::findOrFail($input['input_id']); // Find the existing record
                    
                    // Update the record with new input
                    $suggestion->fill($input); // Fill with new data
                } else {
                    // Create a new suggestion if no input_id is provided
                    $maxSuggestionNumber = Suggestion::max('suggestion_number');
                    $newSuggestionNumber = $maxSuggestionNumber ? $maxSuggestionNumber + 1 : 1;
                    $input['suggestion_number'] = $newSuggestionNumber;
                    $input['status'] = 'Pending';
                
                    $suggestion = new Suggestion(); // Create a new instance
                    $suggestion->fill($input); // Fill with new data
                }
                
                
                $suggestion->save();
             $output = [
                'success' => true,
                'msg' => __('member::lang.status_update_success')
            ];
                
            } catch (\Exception $e) {
                Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
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
    public function destroy()
    {
    }
    /**
     * change status specified resource from storage.
     * @return Response
     */
    public function getUpdateStatus($id)
    {
        $status_array = Suggestion::getStatusArray();
        $suggestion = Suggestion::findOrFail($id);
        $members = Member::pluck('name', 'id');

        return view('member::suggestion.update_status')->with(compact(
            'status_array',
            'suggestion',
            'members'
        ));
    }
    /**
     * change status specified resource from storage.
     * @return Response
     */
    public function postUpdateStatus($id)
    {
        try {
            Suggestion::where('id', $id)->update([
                'status' => request()->status
            ]);
            $output = [
                'success' => true,
                'msg' => __('member::lang.status_update_success')
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
    
}
