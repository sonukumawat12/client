<?php
namespace Modules\Vat\Http\Controllers;

use Modules\Vat\Entities\VatContact;


;
use Illuminate\Support\Facades\DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel as MatExcel;


use App\Utils\TransactionUtil;
use App\Utils\Util;

class VatContactController extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;
    
    public function __construct(
        Util $commonUtil,
        TransactionUtil $transactionUtil
    ) {

        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
    }
    
    public function index()
    {

        $type = request()->get('type');
        $business_id = request()->session()->get('user.business_id');
        

        if (request()->ajax()) {
            return $type == 'supplier' ? $this->indexSupplier() : ($type == 'customer' ? $this->indexCustomer() : abort(404));
        }
        

        // Check customer code and get contact ID
        $contact_id = $this->check_vat_customer_code($business_id);

        return view('vat::contact.index', compact('type', 'contact_id'));
    }
    
    public function check_vat_customer_code($business_id)
    {
        $ref_no_prefixes = request()->session()->get('business.ref_no_prefixes');
        $ref_no_starting_number = request()->session()->get('business.ref_no_starting_number');
        $prefix =   $ref_no_prefixes['contacts'];
        $starting_number =   $ref_no_starting_number['contacts'];
        $contact_id = '';
        $latest = VatContact::where('business_id', $business_id)->get()->last();
        if(empty($latest)){
            $next_number = $starting_number;
        }else{
            $next_number = (int) (explode('-',$latest->contact_id)[1]) + 1;
        }
        
        $next_number =  str_pad($next_number, 4, 0, STR_PAD_LEFT);
        $contact_id =  $prefix . '-' . $next_number . '-' . $business_id;

        return $contact_id;
    }
    

    private function indexSupplier()
    {
        
        $businessId = request()->session()->get('user.business_id');

        $query = $this->getSupplierContactsData($businessId);

        return Datatables::of($query)
            ->addColumn('action', function ($row) {
                return view('vat::contact.supplier-actions', $this->getSupplierActionData($row))->render();
            })
            
            ->addColumn('mass_delete', function ($row) {
                return  '<input type="checkbox" class="row-select" value="' . $row->id . '">';
            })
            
            ->editColumn('created_at',  function ($row) {
                return $this->transactionUtil->format_date($row->created_at);
            })
            ->rawColumns(['action','mass_delete'])
            ->make(true);
    }
    
    private function getSupplierActionData($row)
    {
        $business_id = request()->session()->get('user.business_id');
        return [
            'id' => $row->id,
            'should_notify' => $row->should_notify,
            'type' => $row->type,
            'active' => $row->active
        ];
    }
    private function getSupplierContactsData($business_id)
    {
        return  $contact = VatContact::where('vat_contacts.business_id', $business_id)
            ->where(function($q) {
                $q->where('vat_contacts.type', 'supplier')
                    ->orWhere('vat_contacts.type', 'both');
            })
            ->select([
                'vat_contacts.contact_id',  'vat_contacts.active', 'vat_contacts.name', 'vat_contacts.created_at', 'vat_contacts.mobile',
                'vat_contacts.type', 'vat_contacts.id','should_notify'
            ])
            ->groupBy('vat_contacts.id');

    }

    /**
     * Returns the database object for customer
     *
     * @return \Illuminate\Http\Response
     */

    private function getCustomerContact($businessId) {
        return  VatContact::where('vat_contacts.business_id', $businessId)
            ->where(function ($q) {
                $q->where('vat_contacts.type', 'customer')
                    ->orWhere('vat_contacts.type', 'both');
            })
            ->select([
                'should_notify', 'vat_contacts.contact_id', 'vat_contacts.name', 'vat_contacts.created_at', 'vat_contacts.active',
                'mobile', 'vat_contacts.id'
            ])
            ->groupBy('vat_contacts.id');
    }
    private function indexCustomer()
    {
        
        $business_id = request()->session()->get('user.business_id');
        $query = $this->getCustomerContact($business_id);
        
        $contacts = Datatables::of($query)
            ->addColumn('action', function ($row) {
                return view('vat::contact.customer-actions', $this->getCustomerActionData($row))->render();
            })

            
            ->addColumn('mass_delete', function ($row) {
                return  '<input type="checkbox" class="row-select" value="' . $row->id . '">';
            })
            
            ->editColumn('created_at', function ($row) {
                return $this->transactionUtil->format_date($row->created_at);
            });
            
        return $contacts->rawColumns(['action', 'mass_delete'])
            ->make(true);
    }
    private function getCustomerActionData($row)
    {
        $business_id = request()->session()->get('user.business_id');
        return [
            'id' => $row->id,
            'should_notify' => $row->should_notify,
            'type' => $row->type,
            'active' => $row->active
        ];
    }
    
    
    public function create()
    {
        $type = request()->type;
        $business_id = request()->session()->get('user.business_id');
        $contact_id = $this->check_vat_customer_code($business_id);

        return view('vat::contact.create')
            ->with(compact('contact_id', 'type'));
    }
    
    
    public function store(Request $request)
    {
        
        try {

            $business_id = $request->session()->get('user.business_id');
            
            DB::beginTransaction();
            
            $input = $request->only(['vat_no','credit_notification','should_notify','type','name','mobile', 'alternate_number','contact_id']);
            
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            
            $contact = VatContact::create($input);
                $output = [
                    'success' => true,
                    'data' => $contact,
                    'msg' => __("contact.added_success")
                ];
            DB::commit();
            
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong"),
                'error' => $e->getMessage()
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
        
        $business_id = request()->session()->get('user.business_id');
        
        $contact = VatContact::where('vat_contacts.id', $id)
            ->where('vat_contacts.business_id', $business_id)
            ->with(['business'])
            ->select(
                'vat_contacts.*'
            )->first();


        $contact_dropdown = VatContact::contactDropdown($business_id, false, false);
        $view_type = 'contact_info';
        
        return view('vat::contact.show')
            ->with(compact('contact', 'contact_dropdown','view_type'));
    }
    
    
    public function edit($id)
    {
        
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $contact = VatContact::select([
                        'vat_contacts.*',
                     ])
                    ->where('vat_contacts.business_id', $business_id)->find($id);
            
            $contact_id = $this->check_vat_customer_code($business_id);
            return view('vat::contact.edit')
                ->with(compact('contact', 'contact_id'));
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
        
        if(request()->ajax()) {
            try {                                                                              
                $input = $request->only(['vat_no','credit_notification','should_notify','contact_id', 'type', 'name', 'mobile','alternate_number']);
                $business_id = $request->session()->get('user.business_id');
                
                $contact = VatContact::where('business_id', $business_id)->findOrFail($id);
                foreach ($input as $key => $value) {
                    $contact->$key = $value;
                }
                $contact->save();
                
                $output = [
                    'success' => true,
                    'msg' => __("contact.updated_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
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
                $contact = VatContact::where('business_id', $business_id)->findOrFail($id);
                $contact->delete();
                $output = [
                    'success' => true,
                    'msg' => __("contact.deleted_success")
                ];
                    
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }
    /**
     * Mass deletes contact.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        
        try {
            if (!empty($request->input('selected_rows'))) {
                $business_id = $request->session()->get('user.business_id');
                $selected_rows = explode(',', $request->input('selected_rows'));
                
                $contacts = VatContact::where('business_id', $business_id)
                    ->whereIn('id', $selected_rows)
                    ->get();
                DB::beginTransaction();
                foreach ($contacts  as $contact) {
                    $contact->delete();
                }
                DB::commit();
            }
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.deleted_success')
            ];
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        
        return redirect()->back()->with($output);
    }
 
    public function toggleActivate($contact_id)
    {
        $contact = VatContact::findOrFail($contact_id);
        $active_status = $contact->active;
        $contact->active = !$active_status;
        $contact->save();
        if ($active_status) {
            $output = ['success' => 1, 'msg' => __('lang_v1.contact_deactivate_success')];
        } else {
            $output = ['success' => 1, 'msg' => __('lang_v1.contact_activate_success')];
        }
        return redirect()->back()->with('status', $output);
    }
    


    public function checkMobile(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        $mobile_number = $request->input('mobile_number');

        $query = VatContact::where('business_id', $business_id)
                        ->where('mobile', 'like', "%{$mobile_number}");

        if (!empty($request->input('contact_id'))) {
            $query->where('id', '!=', $request->input('contact_id'));
        }

        $contacts = $query->pluck('name')->toArray();

        return [
            'is_mobile_exists' => !empty($contacts),
            'msg' => __('lang_v1.mobile_already_registered', ['contacts' => implode(', ', $contacts), 'mobile' => $mobile_number])
        ];
    }
}
