<?php

namespace Modules\MyHealth\Http\Controllers;

use App\Business;
use App\Contact;
use Illuminate\Http\Request;
use Modules\MyHealth\Entities\PatientDetail;
use Modules\MyHealth\Entities\PatientMedicine;
use Modules\MyHealth\Entities\PatientPrescription;
use Modules\MyHealth\Entities\PrescriptionMedicine;
use Modules\MyHealth\Entities\PatientDoctor;
use Modules\MyHealth\Entities\Specialization;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class MedicationController extends Controller
{
    protected $businessUtil;
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index(Request $request)
{
    
    // dd($request);
    $business_id = $request->session()->get('business.id');
      $patient_medicines = PatientMedicine::pluck('medicine_name', 'id');
    
    $health_issues = PatientPrescription::distinct()->pluck('diagnosis', 'diagnosis'); // Pluck the diagnosis as both key and value

        $defaultVal = null;

    // Start building the query
    $query = PatientPrescription::join('prescription_medicines', 'patient_prescriptions.id', '=', 'prescription_medicines.prescription_id')
        ->join('patient_medicines', 'prescription_medicines.medicine_id', '=', 'patient_medicines.id')
        ->join('patient_doctors', 'patient_prescriptions.doctor_id', '=', 'patient_doctors.id')
        ->where('patient_prescriptions.business_id', $business_id);
    
    // Apply health issue filter
    if ($request->has('health_issue') && $request->input('health_issue') != null ) {
        $query->where('patient_prescriptions.diagnosis', $request->input('health_issue'));
    }
    
    // Apply medicine name filter
    if ($request->has('medicine_name') && !empty($request->input('medicine_name'))) {
        $query->where('patient_medicines.id', $request->input('medicine_name'));
    }
    
    // Apply date range filter
    if ($request->has('date_range') && !empty($request->input('date_range'))) {
        $dates = explode(' - ', $request->input('date_range'));
        $start_date = date('Y-m-d', strtotime($dates[0]));
        $end_date = date('Y-m-d', strtotime($dates[1]));
        $query->whereBetween('patient_prescriptions.created_at', [$start_date, $end_date]);
    }


    // Paginate the results
    $prescriptions = $query->paginate(10, [
        'patient_prescriptions.created_at',
        'patient_prescriptions.diagnosed_date',
        'patient_prescriptions.diagnosis',
        'patient_doctors.doctor_name as doctor_name',
        'patient_medicines.medicine_name',
        'patient_prescriptions.amount',
        'patient_prescriptions.frequency'
    ]);

    // Pass the data to the view
    return view('myhealth::medication.index', compact('prescriptions', 'patient_medicines', 'defaultVal', 'health_issues'));
}

public function viewMed($id, Request $request)
{
    try {
        // Check if business_id exists in session
        $business_id = $request->session()->get('business.id');
        if (!$business_id) {
            // Log the error and return an error response
            \Log::error('Business ID not found in session');
            return redirect()->back()->with('error', 'Business session not found.');
        }

        // Find the patient medicine record
       $patient_medicines = PatientMedicine::where('business_id', $business_id)->where('id', $id)->get();
       

// Add a variable to indicate whether the modal should be shown
    $showModal = false;
        if (!$patient_medicines) {
            // Log the error and return an error response
            \Log::warning("PatientMedicine with ID {$id} not found for business ID {$business_id}");
            return redirect()->back()->with('error', 'Medicine not found.');
        }else{
            // Add a variable to indicate whether the modal should be shown
    $showModal = true;

        }
        
        \Log::info($showModal);

        // Return the modal view with patient medicines
        return view('myhealth::patient.sugar_reading', compact('patient_medicines', 'showModal'));

    } catch (\Exception $e) {
        // Log the exception and return a generic error response
        \Log::error("Error retrieving medicine for ID {$id}: " . $e->getMessage());
        return redirect()->back()->with('error', 'An unexpected error occurred.');
    }
}




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $defaultVal = null;
    
        // Pluck will automatically create an associative array with 'id' as the key and 'doctor_name' as the value
        $patient_doctors = PatientDoctor::where('business_id', $business_id)
                            ->pluck('doctor_name', 'id'); // 'id' becomes doctor_id
        $specializations = Specialization::all();
    
        return view('myhealth::medication.create')->with(compact('patient_doctors', 'defaultVal', 'specializations'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
    public function store(Request $request)
    {
        // Begin a transaction
        DB::beginTransaction();
    
        try {
            // Create a new PatientPrescription entry
            $prescription = PatientPrescription::create([
                'date' => $request->input('date'),
                'diagnosed_date' => $request->input('diagnosed_date'),
                'diagnosis' => $request->input('health_issue'),
                'doctor_id' => $request->input('doctor_name'),
                'amount' => $request->input('dose'),
                'frequency' => $request->input('frequency'),
                'business_id' => request()->session()->get('business.id'), // Add this if you are using business logic
            ]);
            
            // Create a new PatientMedicine entry
            $medicine = PatientMedicine::create([
                'medicine_name' => $request->input('medicine_name'),
                'business_id' => request()->session()->get('business.id'), // Add this if you are using business logic
            ]);
            
            PrescriptionMedicine::create([
                'prescription_id' => $prescription->id,
                'medicine_id' => $medicine->id
            ]);
            
            
    
            // Commit the transaction
            DB::commit();
    
            $output = [

                'success' => 1,
                
                'msg' => __('patient.prescription_add_success')

            ];
            
        } catch (\Exception $e) {
            // Rollback the transaction if something went wrong
            DB::rollback();
    
            // Log the error if necessary
            \Log::error('Error storing medication record: ' . $e->getMessage());
    
            // Redirect back with an error message
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];
            
        }
          return redirect()->back()->with('status', $output);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Check if subscribed or not, then check for location quota
        if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
            return $this->moduleUtil->expiredResponse();
        }

        $blood_groups = ['AB', 'A-', 'B-', 'O-', 'AB+', 'A+', 'B+', 'O+', 'Not Known', ''];
        $marital_statuss = ['Married', 'UnMarried', ''];
        $genders = ['Male', 'Female', ''];
        $patient_details = PatientDetail::where('user_id', $id)->first();

        $blood_group = $blood_groups[!empty($patient_details->blood_group) ? $patient_details->blood_group - 1 : 9];
        $marital_status = $marital_statuss[!empty($patient_details->marital_status) ? $patient_details->marital_status-1 : 2];
        $gender = $genders[!empty($patient_details->gender) ? $patient_details->gender-1 : 2];

        $dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age = $diff->format('%y');

        $patient_code = User::where('id', $id)->select('username')->first()->username;

        $business_id = request()->session()->get('business.id');
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        return view('myhealth::patient.index')->with(compact('date_filters', 'patient_code', 'id', 'patient_details', 'blood_group', 'age', 'marital_status', 'gender'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $blood_groups = ['AB', 'A-', 'B-', 'O-', 'AB+', 'A+', 'B+', 'O+', 'Not Known', ''];
        $marital_statuss = ['Married', 'UnMarried', ''];
        $genders = ['Male', 'Female', ''];
        $patient_details = PatientDetail::where('user_id', $id)->first();

        $blood_group = $blood_groups[!empty($patient_details->blood_group) ? $patient_details->blood_group - 1 : 9];
        $marital_status = $marital_statuss[!empty($patient_details->marital_status) ? $patient_details->marital_status-1 : 2];
        $gender = $genders[!empty($patient_details->gender) ? $patient_details->gender-1 : 2];

        $dateOfBirth =  date('Y-m-d', strtotime($patient_details->date_of_birth));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age = $diff->format('%y');

        return view('myhealth::patient.edit')->with(compact('patient_details', 'blood_group', 'age', 'marital_status', 'gender', 'id'));
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

        try {

            $update_data = array(
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'mobile' => $request->mobile,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
                'blood_group' => $request->blood_group,
                'height' => $request->height,
                'weight' => $request->weight,
                'guardian_name' => $request->guardian_name,
                'known_allergies' => $request->known_allergies,
                'notes' => $request->notes
            );
            if (!file_exists('./public/img/patient_photos')) {
                mkdir('./public/img/patient_photos', 0777, true);
            }
            if ($request->hasfile('fileToImage')) {
                $file = $request->file('fileToImage');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/img/patient_photos', $filename);
                $uploadFileFicon = 'public/img/patient_photos/' . $filename;
                $update_data['profile_image'] =  $uploadFileFicon;
            }

            $patient_details = PatientDetail::where('user_id', $id)->update($update_data);
            $output = [
                'success' => 1,
                'msg' => __("myhealth::patient.details_update_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }


        return back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


      /**
     * Retrieves list of customers, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getPatient()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');
            $patient = Business::where('is_patient', 1)->leftjoin('users', 'business.id', 'users.business_id')
                    ->leftjoin('patient_details', 'users.id', 'patient_details.user_id');
      

            if (!empty($term)) {
                $patient->where(function ($query) use ($term) {
                    $query->Where('users.username', 'like', '%' . $term .'%')
                            ->orWhere('patient_details.mobile', 'like', '%' . $term .'%')
                            ->orWhere('patient_details.name', 'like', '%' . $term .'%');
                });
            }

            $patient->select(
                'users.id',
                'username',
                'patient_details.mobile',
                'patient_details.name'
            );

          
            $patient = $patient->get();
            return json_encode($patient);
        }
    }
    
    
}
