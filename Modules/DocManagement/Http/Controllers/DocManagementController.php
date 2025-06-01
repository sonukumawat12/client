<?php

namespace Modules\DocManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; // Import the File class
use App\BusinessLocation;
use Illuminate\Support\Facades\DB;
use Modules\DocManagement\Entities\DocManagementCategory;
use Modules\DocManagement\Entities\DocManagementType;
use Modules\DocManagement\Entities\DocManagementSignature;
use Modules\DocManagement\Entities\DocManagementPurpose;
use Modules\DocManagement\Entities\DocManagementForwardWith;
use Modules\DocManagement\Entities\DocManagementMandatorySignature;
use Modules\DocManagement\Entities\DocManagementUpload;
use Illuminate\Support\Str; 
use App\Category;
use App\StockConversion;
use Yajra\DataTables\Facades\DataTables;
 
class DocManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        
         $business_id = request()->session()->get('user.business_id');
        $maxId = DocManagementUpload::max('doc_no');
        $docTypes = DocManagementType::pluck('type', 'id');
        $docPurpose = DocManagementPurpose::pluck('purpose_type', 'id');
        $docReferred = user::pluck('username', 'id');
        $newId = $maxId + 1;
         $business_locations = BusinessLocation::forDropdown($business_id);
         $docForwardwith = DocManagementForwardWith::pluck('forwarded_with', 'id');
         
       if (request()->ajax()) {
         
         $route_operations=DocManagementUpload::all();
         

          if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('created_at', '>=', request()->start_date);
                $route_operations->whereDate('created_at', '<=', request()->end_date);
            }
            
            if (!empty(request()->type)) {
                $route_operations->where('document_type', request()->type);
            }
            
            return DataTables::of($route_operations)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                       $html .= '<li><a href="#"><i class="fa fa-eye"></i> ' . __('messages.view') . '</a></li>';
                        $html .= '<li><a href="#"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#"><i class="glyphicon glyphicon-print"></i> ' . __("messages.print") . '</a></li>';
                         $html .= '<li><a href="#" class="open-modal" data-doc-no="' . $row->doc_no . '"><i class="glyphicon glyphicon-edit"></i> ' . __("Referred To") . '</a></li>';
            
 
            
            // JavaScript code to handle the click event and show the modal
            $html .= '<script>
    $(document).ready(function() {
        $(".open-modal").click(function(e) {
            e.preventDefault();
            var docNo = $(this).data("doc-no");
            $("#doc_number").val(docNo);
            $("#referredto_modal").modal("show");
        });
    });
</script>';
                        return $html;
                    }
                )
           
                ->rawColumns(['action', 'payment_status', 'method'])
                ->make(true);
        } 
    
    
        return view('docmanagement::index')
          ->with(compact('newId','docTypes','docPurpose','docReferred' ,'business_locations','docForwardwith'));
    }
 public function get_upload_table() {
     
      
      if (request()->ajax()) {
           
         $route_operations=StockConversion::leftjoin('products', 'products.id', 'stock_conversions.product_convert_from')
            -> select('products.name AS productname','stock_conversions.location','stock_conversions.created_at','stock_conversions.conversion_form_no','stock_conversions.unit_convert_from','stock_conversions.unit_convert_to','stock_conversions.total_qty_convert_from','stock_conversions.product_convert_to','stock_conversions.qty_convert_to','stock_conversions.updated_at','stock_conversions.user')->get();
           
            

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('stock_conversions.updated_at', '>=', request()->start_date);
                $route_operations->whereDate('stock_conversions.updated_at', '<=', request()->end_date);
            }
            
            if (!empty(request()->conversion_from_no)) {
                $route_operations->where('stock_conversions.conversion_form_no', request()->conversion_from_no);
            }
            
            if (!empty(request()->product_convert_from)) {
                $route_operations->where('stock_conversions.unit_convert_from', request()->product_convert_from);
            }
            
            if (!empty(request()->unit_convert_from)) {
                $route_operations->where('air_ticket_invoices.customer', request()->unit_convert_from);
            }
            
            if (!empty(request()->product_convert_to)) {
                $route_operations->where('air_ticket_invoices.airline_agent', request()->product_convert_to);
            }
            
            if (!empty(request()->unit_convert_to)) {
                $route_operations->where('air_ticket_invoices.departure_country', request()->unit_convert_to);
            } 
            
            
            
            
            

            return DataTables::of($route_operations)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a data-href=""' . route('doc-management.view', ['id' => $row->doc_no]) . '"" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href=""' . route('doc-management.view', ['id' => $row->doc_no]) . '""  class="view_payment_modal"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("Edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="#" class="delete-fleet"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        
                        
                        return $html;
                    }
                )
           
                ->rawColumns(['action', 'payment_status', 'method'])
                ->make(true);
        } 
        return null;
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('docmanagement::create');
    }
  
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
     

try {
   
  if ($request->hasFile('image')) {
    $imageFiles = $request->file('image');

    foreach ($imageFiles as $imageFile) {
        // Define the directory where you want to store the image files
        //$directory = 'public_html/images';
        $directory = 'public_html/Modules/DocManagement/Resources/assets/images';
        // Generate a unique filename for each uploaded image
        $fileName = uniqid() . '.' . $imageFile->getClientOriginalExtension();

        // Store the image file in the specified directory
        $path = $imageFile->storeAs($directory, $fileName);

        // You can also get the file name, extension, etc.
        $originalFileName = $imageFile->getClientOriginalName();
        $extension = $imageFile->getClientOriginalExtension();

          
    }
}
    
            $business_id = $request->session()->get('user.business_id');
            
            DB::beginTransaction();
            $status= 'Pending';//$request->status;
            $orginator=  $request->orginator;
            $note= $request->note;
            $purpose=$request->purpose;
            $document_type=$request->document_type;
            $referred_to=$request->referred;
            $image=$path;
            $doc_no=$request->doc_no;
            $referred = '';
            
            $docTypes = DocManagementType::where('id', $document_type)->first();
            
            
            $docPurpose = DocManagementPurpose::where('id', $purpose)->first();
            $referred = ''; // Initialize the $referred variable
            $referred_to_length = count($referred_to);
            
                foreach ($referred_to as $key => $item) {
                $docReferred = user::where('id', $item)->first();
                $username = $docReferred->username;
                
                    if ($referred_to_length == 1) {
                    $referred .= $username; // Assign the username without the comma
                    } else {
                    if ($key == 0) {
                    $referred .= $username; // Assign the first username without the comma
                    } else {
                    $referred .= ',' . $username; // Assign subsequent usernames with a comma
                    }
                }
            }

        
        if ($image) {
                 
            
            $Data = [
            'originator' => $orginator,
            'document_type' => $docTypes->type,
             'purpose' => $docPurpose->purpose_type,
             'note' => $note,
             'referred_to' =>$referred,
               'image' => $image,
               'status' =>$status
            ];
            
            $upload= DocManagementUpload::create($Data);
            
            $output = [
            'success' => true,
            'data' => $upload,
            'msg' => __("contact.added_success")
            ];
            DB::commit();
 
       
    } 
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
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('docmanagement::show');
    }
 /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show_status()
    {
       $business_id = request()->session()->get('user.business_id');
        $maxId = DocManagementUpload::max('doc_no');
        $docTypes = DocManagementType::pluck('type', 'id');
        $docPurpose = DocManagementPurpose::pluck('purpose_type', 'id');
        $docReferred = user::pluck('username', 'id');
        $newId = $maxId + 1;
         $business_locations = BusinessLocation::forDropdown($business_id);
         $docForwardwith = DocManagementForwardWith::pluck('forwarded_with', 'id');
         
       if (request()->ajax()) {
           
         $route_operations=DocManagementUpload::all();
           

          if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('created_at', '>=', request()->start_date);
                $route_operations->whereDate('created_at', '<=', request()->end_date);
            }
            
            if (!empty(request()->type)) {
                $route_operations->where('document_type', request()->type);
            }
            
            return DataTables::of($route_operations)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                         $html .= '<li><a href="#" class="open-modal" data-doc-no="' . $row->doc_no . '"><i class="glyphicon glyphicon-print"></i> ' . __("Print") . '</a></li>';
                    
                    // JavaScript code to handle the click event and show the modal
                    $html .= '<script>
                    $(document).ready(function() {
                    $(".open-modal").click(function(e) {
                    e.preventDefault();
                    var docNo = $(this).data("doc-no");
                    $("#doc_number").val(docNo);
                    $("#print_modal").modal("show");
                    });
                    });
                    </script>';
                    return $html;
                    }
                )
           
                ->rawColumns(['action', 'payment_status', 'method'])
                ->make(true);
        } 
    //chart data
    $statusCounts = DB::table('doc_management_uploads')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();

    // Prepare the data for the charts
    $labels = $statusCounts->pluck('status');
    $counts = $statusCounts->pluck('count');

    // Generate the datasets for the charts
    $pieDataset = [
        'data' => $counts->toArray(),
        'backgroundColor' => [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            // Add more colors if needed
        ],
    ];

    $barDataset = [
        'label' => 'Counts',
        'data' => $counts->toArray(),
        'backgroundColor' => '#36A2EB',
    ];

    // Generate the chart data as JSON
    $chartData = [
        'pie' => [
            'labels' => $labels->toArray(),
            'datasets' => [$pieDataset],
        ],
        'bar' => [
            'labels' => $labels->toArray(),
            'datasets' => [$barDataset],
        ],
    ];
    
        return view('docmanagement::show')
          ->with(compact('newId','docTypes','docPurpose','docReferred' ,'business_locations','docForwardwith','chartData'));
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('docmanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }
/**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update_referred(Request $request )
    {
        

try {
   
 
    
            $business_id = $request->session()->get('user.business_id');
            
            DB::beginTransaction();
            $note= $request->note;
            $purpose=$request->docForwardwith;
            $referred_to=$request->referred_to;
            $doc_no=$request->doc_number;
            $referred = '';
            
            $docPurpose = DocManagementForwardWith::where('id', $purpose)->first();
            $referred = ''; // Initialize the $referred variable
            $referred_to_length = count($referred_to);
            
                foreach ($referred_to as $key => $item) {
                $docReferred = user::where('id', $item)->first();
                $username = $docReferred->username;
                
                    if ($referred_to_length == 1) {
                    $referred .= $username; // Assign the username without the comma
                    } else {
                    if ($key == 0) {
                    $referred .= $username; // Assign the first username without the comma
                    } else {
                    $referred .= ',' . $username; // Assign subsequent usernames with a comma
                    }
                }
            }
          
$status = (trim($docPurpose->forwarded_with) == "Approved") ? "Approved" : "Referred Back";
        
        if ($doc_no) {
                 
            
            $Data = [
             'purpose' => $docPurpose->forwarded_with,
             'note' => $note,
             'referred_to' =>$referred,
               'status' =>$status
            ];
            
            // Update the record with matching doc_no
    $upload = DocManagementUpload::where('doc_no', $doc_no)
                ->update($Data);
            
            $output = [
            'success' => true,
            'data' => $upload,
            'msg' => __("contact.added_success")
            ];
            DB::commit();
 
       
    } 
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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
