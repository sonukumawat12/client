<?php

namespace Modules\Dsr\Http\Controllers;

;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Dsr\Entities\FuelProvider;
use Yajra\DataTables\Facades\DataTables;

class FuelProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $fuel_providers = FuelProvider::where('id', '>=', 1)
            ->select('id', 'name', 'address', 'phone', 'email','created_at as date')
            ->where('business_id', $business_id)
                ->where('state','active')
            ;
            return DataTables::of($fuel_providers)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Dsr\Http\Controllers\FuelProvidersController@edit\',[$id])}}" data-container=".dsr_fuel_provider_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Dsr\Http\Controllers\FuelProvidersController@destroy\',[$id])}}" data-container=".fuel_provider_del" class="btn btn-xs btn-danger provider_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.edit")</button>
                   
                    '
                )
                ->editColumn('date', function ($item) {
                    $date = $item->date;
                    $date = date_create($date);
                    return $date->format('F j, Y g:i A');
                })
                ->rawColumns(['action','date'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('dsr::fuelproviders.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
            $data = $request->except(['_token','_method']);
            $rules = [
                'name' => 'required',
                'phone' => 'required',
            ];
            $validator = \Validator::make($data, $rules);
            if ($validator->fails()) {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.fill_required_fields'),
                ];
                return redirect()->back()->with('status', $output);
            }
            $data['added_by'] = Auth::user()->id;
            $data['business_id'] = request()->session()->get('business.id') ?? null;
            $fuel_provider = FuelProvider::create($data);
            if (!$fuel_provider) {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.something_went_wrong'),
                ];
                return redirect()->back()->with('status', $output);
            }
            $output = [
                'success' => true,
                'tab' => 'fuel_providers',
                'msg' => __('dsr::lang.fuel_provider_created_successfully')
            ];
        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('dsr::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $fuel_provider = FuelProvider::findOrFail($id);
        return view('dsr::fuelproviders.edit')->with(compact('fuel_provider'));    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

                $data = $request->except(['_token','_method']);
                $rules = [
                    'name' => 'required',
                    'phone' => 'required',
                ];
                $validator = \Validator::make($data, $rules);
                if ($validator->fails()) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.fill_required_fields'),
                    ];
                    return redirect()->back()->with('status', $output);
                }
                $fuel_provider = FuelProvider::where('id', $id)->update($data);
                if (!$fuel_provider) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.something_went_wrong'),
                    ];
                    return redirect()->back()->with('status', $output);
                }
                $output = [
                    'success' => true,
                    'tab' => 'fuel_providers',
                    'msg' => __('dsr::lang.fuel_provider_updated_successfully')
                ];
            return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //change state to inactive
        $fuel_provider = FuelProvider::where('id', $id)->update(['state' => 'inactive']);
        if (!$fuel_provider) {
            $output = [
                'success' => false,
                'msg' => __('lang_v1.something_went_wrong'),
            ];
            return redirect()->back()->with('status', $output);
        }
        $output = [
            'success' => true,
            'tab' => 'fuel_providers',
            'msg' => __('dsr::lang.fuel_provider_deleted_successfully')
        ];
        return redirect()->back()->with('status', $output);

    }
    public function allFuelProviders()
    {
        $fuelProviders = FuelProvider::where('state','active')->get()->pluck('name', 'id');
        return response()->json($fuelProviders);
    }
}
