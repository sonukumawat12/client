<?php

namespace Modules\Hms\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Hms\Entities\HmsRoomType;
use Modules\Hms\Entities\HmsRoom;
use Modules\Hms\Entities\HmsRoomTypePricing;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Media;
use App\Category;
use App\Utils\ModuleUtil;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Entities\Package;
use App\Business;
use App\Chequer\ChequerDefaultSetting;
use App\Chequer\ChequerBankAccount;

class RoomController extends Controller
{

    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        $room_subscribe = '';
        $room_added =  '';
        $room_could_be_added = '';
        if (!auth()->user()->can('superadmin')) {
            // Récupération de l'abonnement actif
            $subscription = Subscription::where('business_id', $business_id)
                ->whereDate('end_date', '>=', now())
                ->first();

            if ($subscription) {
                $package_details = (array) $subscription->package_details;

                $room_count = HmsRoom::join('hms_room_types', 'hms_rooms.hms_room_type_id', '=', 'hms_room_types.id')
                ->where('hms_room_types.business_id', $business_id)->count();

                $package_details['room_added'] = $room_count;

                $room_subscribe = $package_details['room_subscribe'] ?? 0;
                $room_added = $package_details['room_added'] ?? 0;

                $room_could_be_added = max(0, $room_subscribe - $room_added);

                $package_details['room_could_be_added'] = $room_could_be_added;
                $subscription->package_details = $package_details;
                $subscription->save();
            }
        }


        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }


        if (!auth()->user()->can('hms.manage_rooms')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $rooms = DB::table('hms_rooms')
                ->join('hms_room_types', 'hms_rooms.hms_room_type_id', '=', 'hms_room_types.id')
                ->where('hms_room_types.business_id', $business_id)
                ->whereNull('hms_rooms.deleted_at')
                ->orderBy('id', 'DESC')
                ->select(
                    'hms_rooms.*',
                    'hms_room_types.id as room_type_id',
                    'hms_room_types.type',
                    'hms_room_types.no_of_adult',
                    'hms_room_types.no_of_child'
                );

            return Datatables::of($rooms)
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->addColumn('action', function ($row) {
                    $html = '<a type="button" class="btn btn-primary btn-xs" id="edit_room" data-href="' 
                    . action([\Modules\Hms\Http\Controllers\RoomController::class, 'edit'], [$row->room_number, $row->room_type_id]) 
                    . '">
                    <i class="fa fa-edit"></i> ' . __('messages.edit') . '</a>';
                    $html .= ' <a type="button" class="btn btn-danger btn-xs delete_room_confirmation" href="' . action([\Modules\Hms\Http\Controllers\RoomController::class, 'destroy'], [$row->id]) . '">
                    <i class="fa fa-trash"></i> ' . __('messages.delete') . '</a>';
                    if (auth()->user()->can('hms.manage_price')) {
                        $html .= ' <a type="button" class="btn btn-info btn-xs" href="' . action([\Modules\Hms\Http\Controllers\RoomController::class, 'pricing']) . '?room_id=' . $row->room_type_id . '">
                <i class="fa fa-dollar"></i> ' . __('hms::lang.pricing') . '</a>';
                    }

                    return $html;
                })
                ->rawColumns(['created_at', 'action', 'description'])
                ->make(true);
        }
        return view('hms::rooms.index', compact('room_subscribe', 'room_added', 'room_could_be_added'));
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

        if (!auth()->user()->can('hms.manage_rooms')) {
            abort(403, 'Unauthorized action.');
        }


        $amenities = Category::where('business_id', $business_id)
            ->where('category_type', 'amenities')
            ->select(['name', 'id'])->get();

        $room_could_be_added = PHP_INT_MAX;
        if (!auth()->user()->can('superadmin')) {
            $subscription = Subscription::where('business_id', $business_id)
                ->whereDate('end_date', '>=', now())
                ->first();
            $package_details = (array) $subscription->package_details;
            $room_could_be_added = $package_details['room_could_be_added'];
        }

        return view('hms::rooms.create', compact('amenities', 'room_could_be_added'));
    }

    public function accommodation_type(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $query = $request->get('q');

        if($query == ''){
            $rooms = HmsRoomType::orderBy('id', 'DESC')
            ->limit(10)
            ->where('business_id', $business_id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'no_of_adult' => $item->no_of_adult,
                    'no_of_child' => $item->no_of_child,
                ];
            });
        }else{
           $rooms = HmsRoomType::where('type', 'LIKE', "%$query%")
            ->limit(10)
            ->where('business_id', $business_id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'no_of_adult' => $item->no_of_adult,
                    'no_of_child' => $item->no_of_child,
                ];
            });
        }

        return $rooms;
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('hms.manage_rooms')) {
            abort(403, 'Unauthorized action.');
        }



        return view('hms::rooms.dashboard');
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized action.'
            ], 403);
        }

        if (!auth()->user()->can('hms.manage_rooms')) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized action.'
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Traitement des numéros de chambre séparés par virgule
            $room_numbers = explode(',', $request->rooms);
            $room_numbers = array_map('trim', $room_numbers);
            $room_numbers = array_filter($room_numbers); // supprime les vides

            // Récupération du nombre de chambres autorisé
            $room_could_be_added = PHP_INT_MAX;
            if (!auth()->user()->can('superadmin')) {
                $subscription = Subscription::where('business_id', $business_id)
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                if ($subscription) {
                    $package_details_array = json_decode(json_encode($subscription->package_details), true);
                    $room_could_be_added = $package_details_array['room_could_be_added'] ?? PHP_INT_MAX;
                }
            }

            // Comptage des chambres existantes pour ce business
            $totalRooms = HmsRoom::count();

            if ($room_could_be_added <= 0) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'msg' => __('You have reached the maximum number of rooms allowed by your subscription.'),
                    'limit_reached' => true
                ]);
            }

            $input = $request->except(['_token', 'rooms', 'amenities']);
            $input['created_by'] = auth()->user()->id;
            $input['business_id'] = $business_id;

            $newRoomCount = 0;

            if ($request->type_id) {
                $type = HmsRoomType::find($request->type_id);
            } else {
                $type = HmsRoomType::create($input);
            }


            foreach ($room_numbers as $room_number) {
                // Vérifie si la chambre existe déjà
                $already_exists = HmsRoom::where('room_number', $room_number)
                    ->exists();

                if ($already_exists) {
                    continue;
                }

                $data = [
                    'hms_room_type_id' => $type->id,
                    'room_number' => $room_number,
                ];

                // Crée la chambre liée à ce type
                HmsRoom::create($data);

                $newRoomCount++;
            }

            // Mise à jour éventuelle du package
            $business = Business::with('owner')->findOrFail($business_id);
            $subscription = Subscription::active_subscription($business_id);

            if ($subscription) {
                $package_manage = Package::where('only_for_business', $business_id)->first();
                if ($package_manage) {
                    $package_manage->price = $request->annual_fee_package;
                    $package_manage->currency_id = $request->currency_id;
                    $package_manage->save();
                }

                $module_activation_data = [
                    'room_added' => $totalRooms + $newRoomCount,
                ];

                $subscription->module_activation_details = json_encode($module_activation_data);
                $subscription->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => __('Rooms added successfully.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return response()->json([
                'success' => false,
                'msg' => __('Something went wrong. Please try again.')
            ]);
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
    public function edit($room, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('hms.manage_rooms')) {
            abort(403, 'Unauthorized action.');
        }
        $room_type = HmsRoomType::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['rooms', 'media'])
            ->get();

        $existing_amenities = $room_type[0]->categories->map(function ($category) {
            return $category->id;
        })->all();
        $rooms_data = HmsRoom::where(['hms_room_type_id'=> $room_type[0]->id, 'room_number' => $room]) // Check the room type ID
            ->first();

        $room_number = $rooms_data->room_number;

        $business_id = request()->session()->get('user.business_id');

        $amenities = Category::where('business_id', $business_id)
            ->where('category_type', 'amenities')
            ->select(['name', 'id'])->get();

        $room_type->toArray();

        return view('hms::rooms.edit', compact('room_type', 'amenities', 'existing_amenities', 'room_number'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('hms.manage_rooms')) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();
        try {
            $input = $request->except(['_token', '_method', 'rooms', 'amenities']);
            $input['created_by'] = auth()->user()->id;
            $input['business_id'] = $business_id;

            $past_id = $request->past_room_type_id;
            $new_id = $request->type_id;
            $past_room_number = $request->past_room_number;
            $room_number = $request->rooms;

            $roomType = HmsRoomType::where('business_id', $business_id)
                ->findOrFail($new_id && $new_id !== $past_id ? $new_id : $past_id);

            if (isset($request->no_of_adult)) {
                $roomType->no_of_adult = $request->no_of_adult;
                $roomType->no_of_child = $request->no_of_child;
                $roomType->save();
            }
            $room = HmsRoom::where('room_number', $past_room_number)
                ->where('hms_room_type_id', $past_id)
                ->first();
                

            if ($room && ($past_room_number !== $room_number || $new_id !== $past_id)) {
                $room->room_number = $room_number;
                $room->hms_room_type_id = $roomType->id;
                $room->save();
            }
            

            // Update room details if needed (for example, update the room number or other attributes)


            // Optionally update amenities or rooms if needed here

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('room_updated_successfully'),
            ];

            return redirect()
                ->action([\Modules\Hms\Http\Controllers\RoomController::class, 'index'])
                ->with('status', $output);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];

            return back()->with('status', $output)->withInput();
        }
    }


    // pricing index page retune
    public function pricing(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('hms.manage_price')) {
            abort(403, 'Unauthorized action.');
        }


        $id = $request->input('room_id');

        $room_type = [];
        $default_pricing = [];
        $spacial_pricing = [];

        if (!empty($id)) {
            $room_type = HmsRoomType::where('business_id', $business_id)->findOrFail($id);

            $default_pricing = HmsRoomTypePricing::where('hms_room_type_id', $id)->whereNull('adults')->whereNull('childrens')->first();

            $spacial_pricing = HmsRoomTypePricing::where('hms_room_type_id', $id)->whereNotNull('adults')->whereNotNull('childrens')->get();
        }

        $types = HmsRoomType::where('business_id', $business_id)->pluck('type', 'id')->toArray();

        return view('hms::rooms.pricing', compact('room_type', 'default_pricing', 'spacial_pricing', 'types'));
    }

    // get htlm for pricing add more pricing
    public function get_spacial_pricing_html(Request $request)
    {
        $currentIndex = $request->input('currentIndex');
        $id = $request->input('id');
        $room_type = HmsRoomType::findOrFail($id);
        return view('hms::rooms.spacial_pricing', compact('currentIndex', 'room_type'));
    }

    // store or update  pricing 
    public function post_pricing(Request $request)
    {
        $input =  $request->except(['_token']);
        $type = HmsRoomType::findOrFail($input['type_id']);
        
        try {
            DB::beginTransaction();
            $existing_ids = $type->pricings->pluck('id')->toArray();

            $new_created_id = [];

            foreach ($input['pricing'] as $pricing) {
                if (isset($pricing['id']) && in_array($pricing['id'], $existing_ids)) {
                    // Update the existing pricing if it exists in the database
                    HmsRoomTypePricing::where('id', $pricing['id'])->update(
                        [
                            'season_type' => $input['season_type'],
                            'adults' => $pricing['adults'] ?? null,
                            'childrens' => $pricing['childrens'] ?? null,
                            'default_price_per_night' => $pricing['default_price'] ?? null,
                            'price_monday' => $pricing['monday'] ?? null,
                            'price_tuesday' => $pricing['tuesday'] ?? null,
                            'price_wednesday' => $pricing['wednesday'] ?? null,
                            'price_thursday' => $pricing['thursday'] ?? null,
                            'price_friday' => $pricing['friday'] ?? null,
                            'price_saturday' => $pricing['saturday'] ?? null,
                            'price_sunday' => $pricing['sunday'] ?? null,
                        ]
                    );
                } else {
                    // Create a pricing if it doesn't have an ID or doesn't exist in the database
                    $type_pricing = $type->pricings()->create(
                        [
                            'season_type' => $input['season_type'],
                            'adults' => $pricing['adults'] ?? null,
                            'childrens' => $pricing['childrens'] ?? null,
                            'default_price_per_night' => $pricing['default_price'] ?? null,
                            'price_monday' => $pricing['monday'] ?? null,
                            'price_tuesday' => $pricing['tuesday'] ?? null,
                            'price_wednesday' => $pricing['wednesday'] ?? null,
                            'price_thursday' => $pricing['thursday'] ?? null,
                            'price_friday' => $pricing['friday'] ?? null,
                            'price_saturday' => $pricing['saturday'] ?? null,
                            'price_sunday' => $pricing['sunday'] ?? null,
                        ]
                    );
                    $new_created_id[] = $type_pricing->id;
                }
            }


            // Delete pricing that are not in the updated list
            $pricing_to_delete = array_diff($existing_ids, array_column($input['pricing'], 'id'));
            HmsRoomTypePricing::whereIn('id', $pricing_to_delete)->delete();

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success'),
            ];

            return response()->json($output);
        } catch (\Exception $e) {
            DB::rollBack();
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

        if (!auth()->user()->can('hms.manage_rooms')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            // Find the room by ID
            $room = HmsRoom::find($id);

            if ($room) {
                // Store the associated room type ID before deleting
                $roomTypeId = $room->hms_room_type_id;

                // Soft delete the room
                $room->delete();

                // Count how many rooms are still linked to this room type
                $count = HmsRoom::where('hms_room_type_id', $roomTypeId)->count();

                // If no more rooms are linked to the room type, soft delete the room type as well
                if ($count === 0) {
                    $type = HmsRoomType::find($roomTypeId);
                    if ($type) {
                        $type->delete();
                    }
                }
            }

            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
            return redirect()
                ->action([\Modules\Hms\Http\Controllers\RoomController::class, 'index'])
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
}
