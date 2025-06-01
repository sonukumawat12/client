<?php

namespace Modules\Superadmin\Http\Controllers;

use Modules\Superadmin\Entities\DefaultTaxRate;
use Modules\Superadmin\Entities\DefaultGroupSubTax;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Business;

use App\Utils\TaxUtil;
use App\TaxRate;

class DefaultTaxRateController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $taxUtil;

    /**
     * Constructor
     *
     * @param TaxUtil $taxUtil
     * @return void
     */
    public function __construct(TaxUtil $taxUtil)
    {
        $this->taxUtil = $taxUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('tax_rate.view') && !auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            
            $tax_rates = DefaultTaxRate::where('is_tax_group', '0')
                        ->select(['name', 'amount', 'id']);

            return Datatables::of($tax_rates)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Superadmin\Http\Controllers\DefaultTaxRateController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_tax_rate_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    
                        <button data-href="{{action(\'\Modules\Superadmin\Http\Controllers\DefaultTaxRateController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_tax_rate_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('superadmin::tax_rate.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin::tax_rate.create');
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
            $input = $request->only(['name', 'amount']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['amount'] = $this->taxUtil->num_uf($input['amount']);

            $tax_rate = DefaultTaxRate::create($input);
            
            $bls = Business::where('id','!=',$input['business_id'])->get();
            foreach($bls as $bl){
                $input['business_id'] = $bl->id;
                $input['default_tax_id'] = $tax_rate->id;
                TaxRate::create($input);
            }
            
            $output = ['success' => true,
                            'data' => $tax_rate,
                            'msg' => __("tax_rate.added_success")
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
            $tax_rate = DefaultTaxRate::find($id);

            return view('superadmin::tax_rate.edit')
                ->with(compact('tax_rate'));
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
                $input = $request->only(['name', 'amount']);
                $business_id = $request->session()->get('user.business_id');

                $tax_rate = DefaultTaxRate::findOrFail($id);
                $tax_rate->name = $input['name'];
                $tax_rate->amount = $this->taxUtil->num_uf($input['amount']);
                $tax_rate->save();
                
                TaxRate::where('default_tax_id',$id)->update($input);
                
                //update group tax amount
                $group_taxes = DefaultGroupSubTax::where('tax_id', $id)
                                            ->get();
                              
                foreach ($group_taxes as $group_tax) {
                    $this->taxUtil->updateGroupTaxAmount($group_tax->group_tax_id);
                }

                $output = ['success' => true,
                            'msg' => __("tax_rate.updated_success")
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
        if (!auth()->user()->can('tax_rate.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                //update group tax amount
                $group_taxes = DefaultGroupSubTax::where('tax_id', $id)
                                            ->get();
                if ($group_taxes->isEmpty()) {
                    $business_id = request()->user()->business_id;

                    $tax_rate = DefaultTaxRate::findOrFail($id);
                    $tax_rate->delete();
                    TaxRate::where('default_tax_id',$id)->forceDelete();

                    $output = ['success' => true,
                                'msg' => __("tax_rate.deleted_success")
                                ];
                } else {
                    $output = ['success' => false,
                                'msg' => __("tax_rate.can_not_be_deleted")
                                ];
                }
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
