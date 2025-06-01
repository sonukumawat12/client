<?php

namespace Modules\Superadmin\Http\Controllers;

use Modules\Superadmin\Entities\DefaultTaxRate;
use Modules\Superadmin\Entities\DefaultGroupSubTax;
use Datatables;
use Illuminate\Routing\Controller;
use App\Business;
use App\TaxRate;

use Illuminate\Http\Request;

class DefaultGroupTaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $tax_rates = DefaultTaxRate::where('is_tax_group', '1')
                        ->with(['sub_taxes']);

            return Datatables::of($tax_rates)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Superadmin\Http\Controllers\DefaultGroupTaxController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".tax_group_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        <button data-href="{{action(\'\Modules\Superadmin\Http\Controllers\DefaultGroupTaxController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_tax_group_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->editColumn('sub_taxes', function ($row) {
                    $sub_taxes = [];
                    foreach ($row->sub_taxes as $sub_tax) {
                        $sub_taxes[] = $sub_tax->name;
                    }
                    return implode(' + ', $sub_taxes);
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $taxes = DefaultTaxRate::where('is_tax_group', '0')->pluck('name', 'id');
        return view('superadmin::tax_group.create')
                ->with(compact('taxes'));
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
            $input['name'] = $request->input('name');
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $sub_tax_ids = $request->input('taxes');

            $sub_taxes = DefaultTaxRate::whereIn('id', $sub_tax_ids)->get();
            $amount = 0;
            foreach ($sub_taxes as $sub_tax) {
                $amount += $sub_tax->amount ;
            }
            $input['amount'] = $amount;
            $input['is_tax_group'] = 1;

            $tax_rate = DefaultTaxRate::create($input);
            $tax_rate->sub_taxes()->sync($sub_tax_ids);
            
            $bls = Business::get();
            foreach($bls as $bl){
                $input['business_id'] = $bl->id;
                $input['default_tax_id'] = $tax_rate->id;
                TaxRate::create($input);
            }

            $output = ['success' => true,
                            'msg' => __("tax_rate.tax_group_added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
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
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $tax_rate = DefaultTaxRate::with(['sub_taxes'])->find($id);

            $taxes = DefaultTaxRate::where('is_tax_group', '0')->pluck('name', 'id');

            $sub_taxes = [];
            foreach ($tax_rate->sub_taxes as $sub_tax) {
                $sub_taxes[] = $sub_tax->id;
            }
            return view('superadmin::tax_group.edit')
                ->with(compact('taxes', 'sub_taxes', 'tax_rate'));
        }
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
        if (request()->ajax()) {
            try {
                $business_id = $request->session()->get('user.business_id');
                $sub_tax_ids = $request->input('taxes');

                $sub_taxes = DefaultTaxRate::whereIn('id', $sub_tax_ids)->get();
                $amount = 0;
                foreach ($sub_taxes as $sub_tax) {
                    $amount += $sub_tax->amount ;
                }
           
                $tax_rate = DefaultTaxRate::findOrFail($id);
                $tax_rate->name = $request->input('name');
                $tax_rate->amount = $amount;
                $tax_rate->save();
                $tax_rate->sub_taxes()->sync($sub_tax_ids);
                
                $input = ['name' => $request->input('name'), 'amount' => $amount];
                
                TaxRate::where('default_tax_id',$id)->update($input);

                $output = ['success' => true,
                            'msg' => __("tax_rate.tax_group_updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $tax_rate = DefaultTaxRate::findOrFail($id);
                $tax_rate->delete();
                TaxRate::where('default_tax_id',$id)->forceDelete();

                $output = ['success' => true,
                            'msg' => __("tax_rate.deleted_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }
}
