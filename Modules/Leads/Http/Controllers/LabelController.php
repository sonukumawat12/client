<?php

namespace Modules\Leads\Http\Controllers;

;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Leads\Entities\LeadsLabel;
use Yajra\DataTables\Facades\DataTables;

class LabelController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $leads_categories = LeadsLabel::leftjoin('users', 'leads_labels.created_by', 'users.id')
                ->where('leads_labels.business_id', $business_id)
                ->select([
                    'leads_labels.*',
                    'users.username as user'
                ]);

            if (!empty(request()->category)) {
                $leads_categories->where('leads_labels.id', request()->category);
            }
            if (!empty(request()->user)) {
                $leads_categories->where('leads_labels.created_by', request()->user);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $leads_categories->whereDate('date', '>=', request()->start_date);
                $leads_categories->whereDate('date', '<=', request()->end_date);
            }

            return DataTables::of($leads_categories)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Leads\Http\Controllers\LabelController@edit\',[$id])}}" data-container=".category_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Leads\Http\Controllers\LabelController@destroy\',[$id])}}" class="btn btn-xs btn-danger leads_category_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
                    '
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('leads::settings.labels.create');
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
            $input['label_1'] = $request->label_1;
            $input['label_2'] = $request->label_2;
            $input['label_3'] = $request->label_3;
            $input['created_by'] = Auth::user()->id;
            $input['business_id'] = $business_id;

            LeadsLabel::create($input);

            $output = [
                'success' => true,
                'tab' => 'labels',
                'msg' => __('leads::lang.category_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'labels',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $category = LeadsLabel::findOrFail($id);
        return view('leads::settings.labels.edit')->with(compact('category'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input['label_1'] = $request->label_1;
            $input['label_2'] = $request->label_2;
            $input['label_3'] = $request->label_3;

            LeadsLabel::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'tab' => 'labels',
                'msg' => __('leads::lang.category_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'labels',
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
            LeadsLabel::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('leads::lang.category_delete_success')
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
}
