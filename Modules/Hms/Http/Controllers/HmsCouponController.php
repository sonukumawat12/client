<?php

namespace Modules\Hms\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Hms\Entities\HmsRoomType;
use Modules\Hms\Entities\HmsCustomerCouponUsage;
use Modules\Hms\Entities\HmsCoupon;
use App\Utils\Util;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Transaction;
use Carbon\Carbon;


class HmsCouponController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;

    public function __construct(
        Util $commonUtil, ModuleUtil $moduleUtil

    ) {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        $business_id = request()->session()->get('user.business_id');
        
        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if(!auth()->user()->can( 'hms.manage_coupon')){
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $extra = HmsCoupon::where('hms_coupons.business_id', $business_id)
                ->leftJoin('hms_room_types as type', 'type.id', '=', 'hms_coupons.hms_room_type_id')
                ->select(['hms_coupons.*', 'type.type as type']);
            
            $extra2 = HmsCoupon::where('hms_coupons.business_id', $business_id)
                ->whereNotNull('hms_coupons.room_type_id')  // Add condition to only get rows where room_type_id is not null
                ->select([
                    'hms_coupons.*', 
                    \DB::raw("JSON_UNQUOTE(JSON_EXTRACT(hms_coupons.room_type_id, '$[*]')) as type") // Retrieve all room types as a JSON array from room_type_id
                ]);
            
            // Combine the queries using the union operator
            $combined = $extra->union($extra2);
            
            // Get the results from the combined queries
            $results = $combined->get();
            
            // Manually set 'any' for rows where type is null or where hms_room_type_id is 0 or null
            $results->map(function ($item) {
                // If no type is found or it is null, set it as 'any'
                if (is_null($item->type) || $item->hms_room_type_id == 0) {
                    $item->type = 'any';  // Explicitly set 'any' for these rows
                } else {
                    // If it has multiple room types, return them as a string
                    if (is_array(json_decode($item->type))) {
                        $item->type = implode(', ', json_decode($item->type));  // Join the array of room types into a string
                    }
                }
                return $item;
            });
            
            // Filter the results by 'id' to ensure uniqueness
            $uniqueResults = $results->unique('id');
            
            // Now return the Datatables with the modified 'type' column
            return Datatables::of($uniqueResults)
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->addColumn('action', function ($row) {
                    $html = '<a type="button" class="btn btn-primary btn-xs" id="edit_coupon_modal" data-href="' . action([\Modules\Hms\Http\Controllers\HmsCouponController::class, 'edit'], ['coupon' => $row->id]) . '">
                    <i class="fa fa-edit"></i>  ' . __('messages.edit') . '</a>';
                    $html .= ' <a type="button" class="btn btn-danger btn-xs delete_room_confirmation" href="' . action([\Modules\Hms\Http\Controllers\HmsCouponController::class, 'destroy'], [$row->id]) . '">
                    <i class="fa fa-trash"></i> ' . __('messages.delete') . '</a>';
                    return $html;
                })
                ->editColumn('start_date', '{{@format_date($start_date)}}')
                ->editColumn('end_date', '{{@format_date($end_date)}}')
                ->editColumn('type', function ($row) {
                    // Check if the coupon has multiple room types
                    if ($row->room_type_id && $row->type === 'any') {
                        // Decode the JSON-encoded room_type_id and fetch related room types
                        $roomTypeIds = json_decode($row->room_type_id); // Decode JSON string
                        $roomTypes = \Modules\Hms\Entities\HmsRoomType::whereIn('id', $roomTypeIds)->pluck('type')->toArray();
                        
                        // Return all room types associated with the coupon as a comma-separated string
                        return implode(', ', $roomTypes);
                    }
                    
                    // If the coupon only has one type (not 'any'), return the single type
                    return $row->type;
                })
                ->rawColumns(['created_at', 'action', 'start_date', 'end_date'])
                ->make(true);



        }

        return view('hms::coupons.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {    
        $business_id = request()->session()->get('user.business_id');
        
        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }
        if(!auth()->user()->can( 'hms.manage_coupon')){
            abort(403, 'Unauthorized action.');
        }

        $types = HmsRoomType::where('business_id', $business_id)->pluck('type', 'id')->toArray();

        $discount_type = [
            'fixed' => "Fixed",
            'Percentage' => "Percentage",
        ];

        return view('hms::coupons.create', compact('types', 'discount_type'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $business_id = request()->session()->get('user.business_id');
        
        // Check for required permissions
        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }
        if (!auth()->user()->can('hms.manage_coupon')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $input = $request->except(['_token']);
            $input['business_id'] = request()->session()->get('user.business_id');
            $input['start_date'] = $this->commonUtil->uf_date($input['start_date']);
            $input['end_date'] = $this->commonUtil->uf_date($input['end_date']);
            
            // Handle the new fields
            $input['is_next_visit'] = isset($input['apply_next_visit']) ? true : false;  // Store as a boolean
            $input['next_visit_start_date'] = $input['valid_from'] ?? null;  // Store valid_from
            $input['next_visit_end_date'] = $input['valid_until'] ?? null;  // Store valid_until
        
            // Check if hms_room_type_id is an array (multiple room types)
            if (is_array($input['hms_room_type_id'])) {
                // Store '0' in hms_room_type_id column and the room_type_id array in the JSON column
                $input['room_type_id'] = json_encode($input['hms_room_type_id']); // Store the array as JSON
                $input['hms_room_type_id'] = 0;
            } else {
                // If it's not an array, just use the hms_room_type_id as usual
                $input['room_type_id'] = null;  // If it's not an array, room_type_id is null
            }
        
            // Create the coupon record
            $coupon = HmsCoupon::create($input);
    
            // If there are multiple room types, associate them with the coupon
            if (is_array($input['hms_room_type_id'])) {
                foreach ($input['hms_room_type_id'] as $roomTypeId) {
                    // Attach each room type to the coupon
                    $coupon->hmsRoomTypes()->attach($roomTypeId);  // assuming the relation method 'hmsRoomTypes' is defined in the HmsCoupon model
                }
            }
        
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success'),
            ];
        
            return redirect()
                ->action([\Modules\Hms\Http\Controllers\HmsCouponController::class, 'index'])
                ->with('status', $output);
        
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
        
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        
            return back()->with('status', $output)->withInput();
        }
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('hms::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('hms.manage_coupon')) {
            abort(403, 'Unauthorized action.');
        }

        $coupon = HmsCoupon::findOrFail($id);

        $business_id = request()->session()->get('user.business_id');
        $types = HmsRoomType::where('business_id', $business_id)->pluck('type', 'id')->toArray();

        // Check if the coupon is associated with multiple room types (i.e., 'any' type)
        $is_multiple_types = false;
        $selected_room_types = [];
        
        if ($coupon->room_type_id != null) {
            $selected_room_types = json_decode($coupon->room_type_id); // Decode the JSON array of room type ids
            $is_multiple_types = true; // This indicates the coupon has multiple room types
        } else {
            $selected_room_types[] = $coupon->hms_room_type_id; // Single room type
        }

        $discount_type = [
            'fixed' => "Fixed",
            'percentage' => "Percentage",
        ];

        return view('hms::coupons.edit', compact('coupon', 'discount_type', 'types', 'is_multiple_types', 'selected_room_types'));
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }
        if(!auth()->user()->can( 'hms.manage_coupon')){
            abort(403, 'Unauthorized action.');
        }

        try{

            $input =  $request->except(['_token']);
            $input['start_date'] = $this->commonUtil->uf_date($input['start_date']);
            $input['end_date'] = $this->commonUtil->uf_date($input['end_date']);
            
            // Handle the new fields
            $input['is_next_visit'] = isset($input['apply_next_visit']) ? true : false;  // Store as a boolean
            $input['next_visit_start_date'] = $input['valid_from'] ?? null;  // Store valid_from
            $input['next_visit_end_date'] = $input['valid_until'] ?? null;  // Store valid_until

            if (is_array($input['hms_room_type_id'])) {
                // Store the room types as JSON if multiple are selected
                $input['room_type_id'] = json_encode($input['hms_room_type_id']);
                $input['hms_room_type_id'] = 0; // Use 0 as the default
            } else {
                // Single room type, store normally
                $input['room_type_id'] = null;
            }
            
            $coupon = HmsCoupon::findOrFail($id);
            $coupon->update($input);

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success'),
            ];

            return redirect()
                ->action([\Modules\Hms\Http\Controllers\HmsCouponController::class, 'index'])
                ->with('status', $output);

        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];

            return back()->with('status', $output)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }
        if(!auth()->user()->can( 'hms.manage_coupon')){
            abort(403, 'Unauthorized action.');
        }
        
        
        try {

            HmsCoupon::where('id', $id)->delete();

            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
            return redirect()
                ->action([\Modules\Hms\Http\Controllers\HmsCouponController::class, 'index'])
                ->with('status', $output);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];

            return back()->with('status', $output)->withInput();
        }
    }

    public function get_coupon_discount(Request $request)
    {
        $booking_date = $this->commonUtil->uf_date($request->booking_date);

       // Get the coupon based on the coupon code and booking date
        $coupon = HmsCoupon::where('coupon_code', $request->coupon_code)
        ->first();
        if($coupon) {
            if ($coupon->is_next_visit) {
                // dd("i am in the new line next ");
                // Check if the booking date is within the next visit date range
//                 dd([
//     'booking_date' => $booking_date,
//     'next_visit_start_date' => $coupon->next_visit_start_date,
//     'next_visit_end_date' => $coupon->next_visit_end_date,
// ]);
                    if (
                        Carbon::parse($coupon->next_visit_start_date)->toDateString() <= $booking_date &&
                        Carbon::parse($coupon->next_visit_end_date)->toDateString() >= $booking_date
                    ) {
                        // Coupon is valid for next visit, no need to check the start_date and 

                        $checkInCount = Transaction::where('contact_id', $request->contact_id)
                            ->whereNotNull('check_in')
                            ->count();
                            // dd("i am in the new line ".$checkInCount);
                        if ($checkInCount >= 1) {
                            $usage = HmsCustomerCouponUsage::where('customer_id', $request->contact_id)
                                ->where('coupon_id', $coupon->id) // Matching both customer_id and coupon_id
                                ->first();
                            if(!$usage){
                                // Coupon is valid for next visit, return success message
                                $data = ['status' => 1,'coupon' => $coupon, 'msg' => __('lang_v1.success')];
                                return $data;
                            }else{
                                // Coupon is not valid for next visit, return an error message
                                $data = ['status' => 0, 'msg' => __('messages.something_went_wrong')];
                                return $data;
                            }
                        } else {
                            // Coupon is not valid for next visit, return an error message
                            $data = ['status' => 0, 'msg' => __('messages.something_went_wrong')];
                            return $data;
                        }
                        
                    }else{
                        // Coupon is not valid for next visit, return an error message
                        $data = ['status' => 0, 'msg' => __('messages.something_went_wrong')];
                        return $data;
                    }
            }
        }else{
            $data = ['status' => 0, 'msg' => __('messages.something_went_wrong')];
            return $data;
        }


        if ($this->commonUtil->uf_date($coupon->start_date) <= $booking_date &&
            $this->commonUtil->uf_date($coupon->end_date) >= $booking_date) {
            // Retrieve the valid room types associated with the coupon
            $roomTypesQuery = HmsCoupon::where('hms_coupons.business_id', $coupon->business_id)
                ->leftJoin('hms_room_types as type', 'type.id', '=', 'hms_coupons.hms_room_type_id')
                ->where('hms_coupons.coupon_code', $request->coupon_code)
                ->select(['hms_coupons.*', 'type.type as room_type'])
                ->get();

            // Handle JSON room types if needed
            $roomTypesQuery->map(function ($item) {
                    if (is_array(json_decode($item->room_type))) {
                        $item->room_type = implode(', ', json_decode($item->room_type));  // Convert JSON to string
                    }
                return $item;
            });

            // Check if the selected room type is in the list of room types for the coupon
            $selectedRoomType = $request->room_type;  // Assuming the user selects a room type in the request
            $validRoomTypes = $roomTypesQuery->pluck('room_type')->toArray();

            // If the selected room type is not in the list of valid room types, return an error
            if (!in_array($selectedRoomType, $validRoomTypes) && !in_array('any', $validRoomTypes)) {
                $data = ['status' => 0, 'msg' => __('messages.coupon_not_valid_for_room_type')];
                return $data;
            }

            // Apply the coupon discount (if room type matches)
            $data = ['status' => 1, 'coupon' => $coupon, 'msg' => __('lang_v1.success')];
            return $data;
        } else {
            $data = ['status' => 0, 'msg' => __('messages.something_went_wrong')];
            return $data;
        }
    }

}
     