<?php

namespace Modules\MPCS\Http\Controllers;

use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\Store;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\MPCS\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Modules\MPCS\Entities\Mpcs15FormDetails;
use Modules\MPCS\Entities\FormF15TransactionData; 
use Modules\MPCS\Entities\FormF15Header; 
use App\Contact;
use App\Transaction;
class F15FormController extends Controller
{ 
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $util;
 
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $util)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->util = $util;
    }
 
    public function index(Request $request)
{
      if (!auth()->check()) {
            return redirect()->route('login');
        } 
    $business_id = request()->session()->get('business.id');
    $settings = MpcsFormSetting::where('business_id', $business_id)->first(); 
    $suppliers = Contact::suppliersDropdown($business_id, false);
    $business_locations = BusinessLocation::forDropdown($business_id); 
            $business_name = BusinessLocation::where('business_id', $business_id)->value('name');
    $currency_precision = Business::where('id', $business_id)->value('currency_precision');

            $row = FormF15Header::where('created_by', auth()->user()->id)->first();
 $last_form = FormF15Header::latest()->first();
    $next_form_number = $last_form ? $last_form->id + 1 : 1;
    if (!empty($row) && (auth()->user()->can('user') || auth()->user()->id === $row->created_by)) {
        $headers = $row;
    } else {
        $headers = null;
    }
    $setting = MpcsFormSetting::where('business_id', $business_id)->first(); 
          
             
              //$form_f15 = FormF15Detail::where('transaction_id', $lastRecord['id'])->first();
                return view('mpcs::forms.F15')->with(compact(
                    'business_locations',
                    'setting',
                    'suppliers',
                            'next_form_number',
                    'headers','business_name',
                            'currency_precision'

                ));
            }


 
    public function getFormF15Data(Request $request)
{ 
    $startDate = $request->input('startDate');
    $endDate = $request->input('endDate'); 
    $startDate = $startDate ? $startDate : date('Y-m-d');
    $endDate = $endDate ? $endDate : date('Y-m-d');

    $query = FormF15TransactionData::orderBy('id');

    $data = $query->get(); 
     

    return response()->json([
        'data' => $data, 
    ]);
}


// TAB 2
    //By Zamaluddin : Time 09:00 AM : 29 January 2025
     public function get15FormSetting() {

        return view('mpcs::forms.partials.create_15_form_settings');

    }

 

 public function store15FormSetting(Request $request)
    {
        DB::beginTransaction();
        try {
            $business_id = session()->get('user.business_id');
            $id_form_labels = $request->input('form15_label_id', []);
            $rupees = $request->input('rupees', []);
            
            $header = FormF15Header::create([
                'business_id' => $business_id,
                'dated_at' => $request->input('dated_at', date('Y-m-d')),
                'created_by' => auth()->user()->id,
            ]);
            
            $data_to_insert_settings = [];
            foreach ($id_form_labels as $key => $id_form_label) {
                $data_to_insert_settings[] = [
                    'f15_form_id' => $header->id,
                    'form15_label_id' => $id_form_label,
                    'rupees' => $rupees[$key] ?? 0,
                ];
            }
            Mpcs15FormDetails::insert($data_to_insert_settings);

            DB::commit();
return redirect()->back()->with('success', __('mpcs::lang.form_15_settings_add_success'));
            
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => 0, 'msg' => __('mpcs::lang.form_15_settings_add_failed'), 'error' => $e->getMessage()], 500);
        }
    }








    public function mpcs15FormSettings()
{
    if (request()->ajax()) {
        $business_id = session()->get('business.id');

        $header = Mpcs15FormDetails::with(['fheader'])
            ->whereHas('fheader', function ($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })
            ->orderBy('id', 'ASC')
            ->get();
            
    
        return DataTables::of($header)
            ->addColumn('action', function ($row) {
                if (auth()->user()->can('superadmin')) {
                return '
                    <button type="button" 
                        data-href="' . url('/mpcs/edit-15-form-settings/' . $row->id) . '" 
                        class="btn-modal btn btn-primary btn-xs" 
                        data-container=".update_form_15_settings_modal">
                        <i class="fa fa-edit"></i> Edit
                    </button>';
                    
                    
                }else{
                    return '';
                }
                    // <button type="button" 
                    //     data-href="' . url('/mpcs/delete-15-form-settings/' . $row->id) . '"
                    //     class="btn btn-danger btn-xs" 
                    //     onclick="deleteFormSetting(this)">
                    //     <i class="fa fa-trash"></i> Delete
                    // </button>
            })
            ->editColumn('dated_at', function ($row) {
                return !empty($row->fheader->dated_at) ? date('Y-m-d', strtotime($row->fheader->dated_at)) : '-';
                
                
                
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    $business_id = session()->get('business.id');
    $business_locations = BusinessLocation::forDropdown($business_id);
    return view('mpcs::forms.form_15', compact('business_locations'));
}



public function edit15FormSetting($id)
{
    if (request()->ajax()) {
        $business_id = session()->get('business.id');
        
        $settings = Mpcs15FormDetails::where('id', $id)
                                   ->first();

        return view('mpcs::forms.partials.edit_15_form_settings')
               ->with(compact('settings'));
    }
}
    
    public function mpcs15Update($id, Request $request)
    {
        DB::beginTransaction();
    
        try {
            $business_id = session()->get('user.business_id'); 
            $rupees = $request->input('rupees'); 
     
     
            Mpcs15FormDetails::where('id',$id)->update(['rupees' => $rupees]);
    
            DB::commit(); 
    
            return response()->json([
                'success' => 1,
                'msg' => __('mpcs::lang.form_15_settings_update_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function delete15FormSetting($id){
    try {
        $business_id = session()->get('business.id');
        $formSettings = Mpcs15FormDetails::where('id', $id)
                                          ->delete();

        if ($formSettings) {
            $output = [
                'success' => true,
                'msg' => __('Delete Success')
            ];
        } else {
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
    } catch (\Exception $e) {
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong')
        ];
    }

    return response()->json($output);
}

}
