<?php

namespace Modules\Shipping\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Shipping\Entities\ShippingAccount;
use Modules\Shipping\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;

use App\Category;

class ShippingAccountController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
       $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            
            $types = ShippingAccount::leftjoin('users', 'shipping_accounts.added_by', 'users.id')
                ->leftJoin('shipping_partners', 'shipping_accounts.shipping_partner', 'shipping_partners.id')
                ->leftJoin('shipping_mode', 'shipping_accounts.shipping_mode', 'shipping_mode.id')
                ->leftJoin('accounts as income', 'shipping_accounts.income', 'income.id')
                ->leftJoin('accounts as expense', 'shipping_accounts.expense', 'expense.id')
                ->where('shipping_accounts.business_id', $business_id)
                ->select(['shipping_accounts.*', 'users.username as created_by','shipping_mode.shipping_mode as shippingMode','shipping_partners.name as shippingPartner','income.name as incomeAccount','expense.name as expenseAccount']);
        
            
 
            return DataTables::of($types)
                ->addColumn('action', function ($row) {
                    $html =
                        '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                            data-toggle="dropdown" aria-expanded="false">' .
                        __('messages.actions') .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\ShippingAccountController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</a></li>';
                    
                    $html .= '</ul></div>';
                    return $html;
                })
                
                ->editColumn('created_at', '{{ @format_date($created_at) }}')
                ->editColumn('shippingMode','{{empty($shippingMode) ? __("lang_v1.all") : $shippingMode}}')
                ->editColumn('shippingPartner','{{empty($shippingPartner) ? __("lang_v1.all") : $shippingPartner}}')
                ->removeColumn('id')
                ->rawColumns(['action','status'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $data = $request->except('_token');
            $data['business_id'] = $business_id;
            $data['added_by'] = auth()->user()->id;

            
            ShippingAccount::updateOrCreate(
                ['business_id' => $business_id,'shipping_mode' => $data['shipping_mode'], 'shipping_partner' => $data['shipping_partner']], 
                $data
            );

            $output = [
                'success' => true,
                'tab' => 'account',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'account',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            ShippingAccount::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
}
