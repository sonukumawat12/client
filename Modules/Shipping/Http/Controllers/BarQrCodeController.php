<?php

namespace Modules\Shipping\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Modules\Shipping\Entities\ShippingBarQrCode;
use Modules\Shipping\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;
use Modules\Shipping\Entities\Shipment;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

use Modules\Shipping\Entities\ShippingMode;

class BarQrCodeController extends Controller
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
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()
                ->session()
                ->get('user.business_id');

            $types = ShippingBarQrCode::leftjoin('users', 'shipping_bar_qr_code.created_by', 'users.id')
            ->where('shipping_bar_qr_code.business_id', $business_id)
                ->select(['shipping_bar_qr_code.id','details','bar_code','qr_code','username'])->get();

            $details    = ['Tracking No','Shipper','Shipper Tracking No','Sender / Customer Name','Recipient Name','Recipient Address','Package Description','Package Name','Delivery  Status'];
           
            if($types->count() <=0 ){
                $firstRecord = ShippingBarQrCode::first();

                if ($firstRecord) {
                    $businessId = $firstRecord->business_id;
                }
                $types = ShippingBarQrCode::leftjoin('users', 'shipping_bar_qr_code.created_by', 'users.id')
                ->where('shipping_bar_qr_code.business_id', $business_id)
                ->select([DB::raw('0 AS id'),'details',DB::raw('0 AS bar_code'),DB::raw('0 AS qr_code'),DB::raw('"" AS username')])->get();

            }

            foreach ($types as $type) {
                $extraRow = [
                    'id' => '',
                    'details' => '<b>User: </b>'.$type->username,
                    'bar_code' => '',
                    'qr_code' => '',
                    'username' => $type->username,
                ];
                $types->push($extraRow);
                break;
            }
            
            

            return DataTables::of($types)
                ->editColumn('bar_code',function($row){
                    if($row['bar_code'] == 1){
                        $html = "<input type='checkbox' checked class='bar_code_".$row['id']."' />";
                    }else if($row['bar_code'] == ''){
                        $html = "";
                    }else{
                         $html = "<input type='checkbox'  class='bar_code_".$row['id']."' />";
                    }
                    
                    return $html;
                })
                ->editColumn('qr_code',function($row){
                    if($row['qr_code'] == 1){
                        $html = "<input type='checkbox' checked  class='qr_code_".$row['id']."' />";
                    }else if($row['qr_code'] == ''){
                        $html = '<button type="button" class="btn  btn-primary btn-modal pull-left bar_qr_save_btn">'.__('messages.save').'</button>';
                    }else{
                         $html = "<input type='checkbox' class='qr_code_".$row['id']."' />";
                    }
                    
                    return $html;
                })
                ->rawColumns(['details','bar_code','qr_code'])
                ->make(true);
        }
    }

    public function scanCode(Request $request)
    {
        $id = $request->input('id');
        if (request()->ajax()) {
            $business_id = request()
                ->session()
                ->get('user.business_id');

            $types = ShippingBarQrCode::leftjoin('users', 'shipping_bar_qr_code.created_by', 'users.id')
            ->where('shipping_bar_qr_code.business_id', $business_id)
                ->select(['shipping_bar_qr_code.id','details','bar_code','qr_code','username'])->get();

            $details    = ['Tracking No','Shipper','Shipper Tracking No','Sender / Customer Name','Recipient Name','Recipient Address','Package Description','Package Name','Delivery  Status'];
           
            if($types->count() <=0 ){
                $firstRecord = ShippingBarQrCode::first();

                if ($firstRecord) {
                    $businessId = $firstRecord->business_id;
                }
                $types = ShippingBarQrCode::leftjoin('users', 'shipping_bar_qr_code.created_by', 'users.id')
                ->where('shipping_bar_qr_code.business_id', $businessId)
                ->select([DB::raw('0 AS id'),'details',DB::raw('0 AS bar_code'),DB::raw('0 AS qr_code'),DB::raw('"" AS username')])->get();

            }

            return view('shipping::shipping.shipping_barqrcode')->with(
                compact(
                    'types',
                    'id'
                )
            );
        }
    }

    public function createShipmentCode(Request $request)
    {
        // Your shipment creation logic

        // Generate barcode for the shipment
        $detail_id      = $request->input('detail_id');
        $shipment_id    = $request->input('shipment_id');
        $type   = $request->input('type');
        $height = $request->input('height');
        $width  = $request->input('width');
        $bar_width = 2;
        $bar_heigth = 35;
        $qr_width = 3;
        $qr_heigth = 3;
        $qrCode = '';
        $barCode = '';

        if($type == 'qr'){
            if($height == 0 || $height == '')
                $qr_heigth = $qr_heigth;
            else
                $qr_heigth = $height;
            if($width == 0 || $width == '')
                $qr_width = $qr_width;
            else
                $qr_width = $width;
        }else{
            if($height == 0 || $height == '')
                $bar_heigth = $bar_heigth;
            else
                $bar_heigth = $height;
            if($width == 0 || $width == '')
                $bar_width = $bar_width;
            else
                $bar_width = $width;
        }
        
        $data = Shipment::leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('shipping_agents', 'shipments.agent_id', 'shipping_agents.id')
                ->leftjoin('shipping_recipients', 'shipments.recipient_id', 'shipping_recipients.id')
                ->leftjoin('shipping_mode', 'shipments.shipping_mode', 'shipping_mode.id')
                ->leftjoin('shipment_packages', 'shipment_packages.shipment_id', 'shipments.id')
                ->leftjoin('shipping_packages', 'shipments.package_type_id', 'shipping_packages.id')
                ->leftjoin('shipping_partners', 'shipments.shipping_partner', 'shipping_partners.id')
                ->leftjoin('shipping_status', 'shipments.delivery_status', 'shipping_status.id')
                ->join('business_locations', 'shipments.business_id', 'business_locations.business_id')
                ->where('shipments.id', $shipment_id)
                ->select([
                    'shipments.*',
                    'contacts.name as sender',
                    'contacts.address as address',
                    'contacts.mobile as mobile',
                    'contacts.city as c_city',
                    'contacts.country as c_country',
                    'contacts.state as c_state',
                    'contacts.landmark as c_landmark',
                    'shipping_recipients.address as rec_address',
                    'shipping_recipients.mobile_1 as rec_mobile_1',
                    'shipping_recipients.mobile_2 as rec_mobile_2',
                    'shipping_recipients.land_no as rec_land_no',
                    'shipping_recipients.postal_code as rec_postal_code',
                    'shipping_recipients.landmarks as rec_landmarks',
                    'shipping_agents.name as agent',
                    'shipping_recipients.name as recipient',
                    'shipping_mode.shipping_mode as mode',
                    'shipping_packages.package_name as package',
                    'shipment_packages.fixed_price as fixed_price_value',
                    'shipment_packages.package_description as package_description',
                    'shipment_packages.length as length',
                    'shipment_packages.width as width',
                    'shipment_packages.height as height',
                    'shipment_packages.weight as weight',
                    'shipment_packages.rate_per_kg as rate_per_kg',
                    'shipment_packages.volumetric_weight as volumetric_weight',
                    'shipment_packages.price_type as price_type',
                    'shipment_packages.shipping_charge as shipping_charge',
                    'shipment_packages.declared_value as declared_value',
                    'shipment_packages.service_fee as service_fee',
                    'shipping_partners.name as partner',
                    'shipping_status.shipping_status as status'
                ])->get();   
        if (!empty($data)) {
            if($detail_id == 'Tracking No'){
                $trackingNo = $data[0]->tracking_no;
            }else if($detail_id == 'Shipper'){
                if(isset($data[0]->agent))
                    $trackingNo = $data[0]->agent;
                else
                    $trackingNo = 'No Agent';
            }else if($detail_id == 'Shipper Tracking No'){
                $trackingNo = $data[0]->shipper_tracking_no;
            }else if($detail_id == 'Sender / Customer Name'){
                $trackingNo = $data[0]->sender;
            }else if($detail_id == 'Recipient Name'){
                $trackingNo = $data[0]->recipient;
            }else if($detail_id == 'Recipient Address'){
                $trackingNo = $data[0]->rec_address;
            }else if($detail_id == 'Package Description'){
                $trackingNo = $data[0]->package_description;
            }else if($detail_id == 'Package Name'){
                $trackingNo = $data[0]->package;
            }else if($detail_id == 'Delivery Status'){
                $trackingNo = $data[0]->status;
            }else{
                $trackingNo = $data[0]->tracking_no;
            }
        }
        if($type == 'bar' || $type == 'showbar'){
            $shipmentBarcode = new DNS1D();
            $shipmentBarcode->setStorPath(__DIR__ . "/cache/");
            //$barcodePNG = $shipmentBarcode->getBarcodePNG($trackingNo, 'C39+');
            $barCode = $shipmentBarcode->getBarcodeSVG($trackingNo, 'C39+',$bar_width,$bar_heigth);
        }

        if($type == 'qr' || $type == 'showqr'){ 
            $trackingNo  = route('shipping.index')."?id=".$data[0]->id."&type=".urlencode($detail_id)."&data=".urlencode($trackingNo);
       
            // Generate QR code for the shipment
            $shipmentQR = new DNS2D();
            $shipmentQR->setStorPath(__DIR__ . "/cache/");
            $qrCode = $shipmentQR->getBarcodePNG($trackingNo, 'QRCODE',$qr_width,$qr_heigth);
            //echo $qrPNG = $shipmentQR->getBarcodeSVG('shm3--34', 'QRCODE');
        }

        return view('shipping::shipping.shipping_scan_code')->with(
            compact(
                'qrCode',
                'barCode',
                'shipment_id',
                'detail_id',
                'type'
            )
        );


        // Save the QR code image or handle it accordingly
        //file_put_contents(public_path('qrcodes/shipment_qr.png'), $qrPNG);

        // Rest of your shipment creation logic
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
    public function store(Request $request): RedirectResponse
    {
        $business_id = request()
            ->session()
            ->get('business.id');
        try {
            for($i = 0;$i < count($request->bar_code); $i++){
                $id = $i+1;
                //echo $request->details[$i];echo "  :  ";
                $details = $request->details[$i];
                $record = ShippingBarQrCode::where('business_id', $business_id)->where('details', $details)->first();
                if ($record) {
                    $record->qr_code    = ($request->qr_code[$i] == 'true') ? 1 : 0;
                    $record->bar_code   = ($request->bar_code[$i] == 'true') ? 1 : 0;
                    $record->save();
                }else{
                    ShippingBarQrCode::create([
                        'details' => $details,
                        'qr_code' => ($request->qr_code[$i] == 'true') ? 1 : 0,
                        'bar_code' => ($request->bar_code[$i] == 'true') ? 1 : 0,
                        'created_by' => Auth::user()->id,
                        'business_id' => $business_id
                    ]);
                }
            }
            ShippingBarQrCode::where('business_id', $business_id)->update(['created_by' => Auth::user()->id]);
            

            $output = [
                'success' => true,
                'tab' => 'package',
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'package',
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with('success', 'Data stored successfully');

    
        
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        //
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
       
    }
}
