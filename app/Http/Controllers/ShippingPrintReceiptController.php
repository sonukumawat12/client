<?php


namespace App\Http\Controllers;
use Mpdf\Mpdf;
use App\Account;
use App\AccountTransaction;
use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\ContactGroup;
use App\ContactLedger;
use App\Media;
use Appp\Store;
use App\Product;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\TypesOfService;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\CashRegisterUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\Warranty;
;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Repair\Entities\JobSheet;
use Modules\Superadmin\Entities\Subscription;
use Yajra\DataTables\Facades\DataTables;
use App\new_vehicle;
use Modules\Shipping\Entities\Shipment;

use App\NotificationTemplate;
// use Mike42\Escpos\Printer;
// use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
// use Mike42\Escpos\PrintConnectors\FilePrintConnector;
// use Mike42\Escpos\CapabilityProfile;

class ShippingPrintReceiptController extends Controller
{
   
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */

    public function __construct() {
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    
    /**
     * Shows invoice to guest user.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */

    public function showInvoice($token)
    {
        $id=2;
        $data = Shipment::leftjoin('users', 'shipments.created_by', 'users.id')
                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('business', 'shipments.business_id', 'business.id')
                ->leftjoin('shipping_agents', 'shipments.agent_id', 'shipping_agents.id')
                ->leftjoin('shipping_recipients', 'shipments.recipient_id', 'shipping_recipients.id')
                ->leftjoin('shipping_mode', 'shipments.shipping_mode', 'shipping_mode.id')
                ->leftjoin('shipment_packages', 'shipment_packages.shipment_id', 'shipments.id')
                ->leftjoin('shipping_packages', 'shipments.package_type_id', 'shipping_packages.id')
                ->leftjoin('shipping_delivery', 'shipments.schedule_id', 'shipping_delivery.id')
                ->leftjoin('shipping_partners', 'shipments.shipping_partner', 'shipping_partners.id')
                ->leftjoin('shipping_status', 'shipments.delivery_status', 'shipping_status.id')
                ->join('business_locations', 'shipments.business_id', 'business_locations.business_id')
                ->where('shipments.id', $id)
                ->select([
                    'shipments.*',
                    'users.username as created_by',
                    'contacts.name as sender',
                    'business_locations.name as bl_name',
                    'business_locations.mobile as bl_mobile',
                    'business.name as business_name',
                    'business.company_number as business_number',
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
                    'shipping_delivery.shipping_delivery as delivery',
                    'shipping_partners.name as partner',
                    'shipping_status.shipping_status as status'
                ])->get();   
        if (!empty($data)) {
            $logo = '<img src="https://vimi14.online/public/img/awb.jpg" alt="Logo">';
            $title   = $data[0]->tracking_no;
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A5',
                'orientation' => 'L',
                'autoPageBreak' => false,
                'allow_unsafe_image_resizing' => true,

            ]);          
            $html=view('sale_pos.receipts.shipping_print_receipt')
            ->with(compact('data', 'title','logo'))->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        } else {
            die(__("messages.something_went_wrong"));
        }
    }
}
