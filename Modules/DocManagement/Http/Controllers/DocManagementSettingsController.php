<?php

namespace Modules\DocManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\User;
use App\BusinessLocation;
use Illuminate\Support\Facades\DB;
use Modules\DocManagement\Entities\DocManagementCategory;
use Modules\DocManagement\Entities\DocManagementType;
use Modules\DocManagement\Entities\DocManagementSignature;
use Modules\DocManagement\Entities\DocManagementPurpose;
use Modules\DocManagement\Entities\DocManagementForwardWith;
use Modules\DocManagement\Entities\DocManagementMandatorySignature;
use Modules\DocManagement\Entities\DocManagementLogo;
use Modules\HR\Entities\Department;
use App\Category;
class DocManagementSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
       $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $username = user::pluck('username', 'username');
         $signatureLevel = DocManagementMandatorySignature::all()->pluck('signature_level','signature_level');
        $designations = Department::all('department')->pluck('department','department');
       // dd($signatureLevel);
        return view('docmanagement::doc_settings.settings_index')
          ->with(compact('business_locations','username','designations','signatureLevel'));
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
    public function store_category(Request $request)
    {
          if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

try {
   
        $business_id = $request->session()->get('user.business_id');
        
        $business_id = $request->session()->get('user.business_id');
        $username = User::where('id', $business_id)->pluck('username')->first();
        DB::beginTransaction();
        $categoryType =  $request->categoryType;
        $user=$username;
        //dd($commisiontype);
        if ($categoryType) {
            
            
            $Data = [
            'document_category' => $categoryType,
            'user' => $user
            ];
            
            $category= DocManagementCategory::create($Data);
            $output = [
            'success' => true,
            'data' => $category,
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
    
     public function store_type(Request $request)
    {
          if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

try {
   
        $business_id = $request->session()->get('user.business_id');
     
        $business_id = $request->session()->get('user.business_id');
        $username = User::where('id', $business_id)->pluck('username')->first();
        DB::beginTransaction();
        $documentType =  $request->documentType;
        $user=$username;
       
        if ($documentType) {
            
            
            $Data = [
            'type' => $documentType,
            'user' => $user
            ];
            
            $type= DocManagementType::create($Data);
            $output = [
            'success' => true,
            'data' => $type,
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
     public function store_purpose(Request $request)
    {
          if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

try {
   
        $business_id = $request->session()->get('user.business_id');
        
        $business_id = $request->session()->get('user.business_id');
        $username = User::where('id', $business_id)->pluck('username')->first();
        DB::beginTransaction();
        $purpose =  $request->purpose;
        $user=$username;
   
        if ($purpose) {
            
            
            $Data = [
            'purpose_type' => $purpose,
            'user' => $user
            ];
            
            $purpose =DocManagementPurpose::create($Data);
            $output = [
            'success' => true,
            'data' => $purpose,
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
    
      public function store_forwardwith(Request $request)
    {
          if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

try {
   
        $business_id = $request->session()->get('user.business_id');
        
        $business_id = $request->session()->get('user.business_id');
        $username = User::where('id', $business_id)->pluck('username')->first();
        DB::beginTransaction();
        $document_forwardwith =  $request->fowardwith;
        $user=$username;
   
        if ($document_forwardwith) {
            
            
            $Data = [
            'forwarded_with' => $document_forwardwith,
            'user' => $user
            ];
            
            $document_forwardwiths =DocManagementForwardWith::create($Data);
            $output = [
            'success' => true,
            'data' => $document_forwardwiths,
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
    
      public function store_mandatorySignature(Request $request)
    {
          if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

try {
   
        $business_id = $request->session()->get('user.business_id');
        
        $business_id = $request->session()->get('user.business_id');
        $username = User::where('id', $business_id)->pluck('username')->first();
        DB::beginTransaction();
        $number=  $request->number;
         $mandatory=  $request->mandatory;
        $user=$username;
   
        if ($mandatory) {
            
            
            $Data = [
            'no_of_mandatory' => $number,
            'signature_level' => $mandatory,
            'user' => $user
            ];
            
            $mandatory =DocManagementMandatorySignature::create($Data);
            $output = [
            'success' => true,
            'data' => $mandatory,
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
    
       public function store_signatures(Request $request)
    {
          

try {
 
        $business_id = $request->session()->get('user.business_id');
        
        $business_id = $request->session()->get('user.business_id');
        $username = User::where('id', $business_id)->pluck('username')->first();
        DB::beginTransaction();
        $date =  $request->date;
        $location =  $request->location;
        $user =  $request->user;
        $designations =  $request->designations;
        $signature_level =  $request->signature_level;
        $image =  $request->image;
        $path='';
       
   if ($request->hasFile('image')) {
    $imageFiles = $request->file('image');
 
    foreach ($imageFiles as $imageFile) {
        // Define the directory where you want to store the image files
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
      
        if ($signature_level) {
            
            
            $Data = [
            'date' => $date,
            'location' => $location,
             'user' => $user,
             'designations' => $designations,
            
             'upload_signature' => $path,
              'signature_levels' => $signature_level
            ];
           
            $type= DocManagementSignature::create($Data);
            $output = [
            'success' => true,
            'data' => $type,
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
    
      public function store_logo(Request $request)
    {
          

try {
  
        $business_id = $request->session()->get('user.business_id');
        
        $business_id = $request->session()->get('user.business_id');
        $username = User::where('id', $business_id)->pluck('username')->first();
        DB::beginTransaction();
   
        $location =  $request->location;
        $user =  $request->user;
        $image =  $request->image;
        $enable_disable_buttons=$request->enable_disable_buttons;
        $path='';
        $toggle=0;
         if($enable_disable_buttons=='enabled')
         {
               $toggle=1;
         }
         
         
  if ($request->hasFile('image')) {
    $imageFiles = $request->file('image');

    foreach ($imageFiles as $imageFile) {
        // Define the directory where you want to store the image files
       // $directory = 'public_html/images';
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
      
        if ($image) {
            
            
            $Data = [
            'upload_logo' => $path,
            'position' => $location,
             'enable_button' => $toggle,
             'user' => $user
             
            ];
           
            $type= DocManagementLogo::create($Data);
            $output = [
            'success' => true,
            'data' => $type,
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
    /***
     * Fetching document category types
     * 
     * 
    */
    public function doc_category_gets()
    {
         
        $categoryType = DocManagementCategory::all();
        return  $categoryType;
    }
      public function doc_department_gets()
    {
         
        $department = Department::all();
        return  $department;
    }
      public function doc_type_gets()
    {
         
        $type = DocManagementType::all();
         return  $type;
    }
       public function doc_purpose_gets()
    {
        //  dd("test");
        $purpose = DocManagementPurpose::all();
       
         return  $purpose;
    }
        public function doc_forwardwith_get()
    {
        //  dd("test");
        $forwardwith = DocManagementForwardWith::all();
       
         return  $forwardwith;
    }
         public function doc_mandatorysignature_gets()
    {
        //  dd("test");
        $mandatory = DocManagementMandatorySignature::all();
      
         return  $mandatory;
    }
      public function doc_upload_gets()
    {
         
        $upload = DocManagementSignature::all();
        return  $upload;
    }
       public function doc_uploadlogo_gets()
    {
         
        $uploadlogo = DocManagementLogo::all();
        return  $uploadlogo;
    }
          public function document_purpose_gets()
    {
         
        $purpose = DocManagementPurpose::all();
        return  $purpose;
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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
