<?php

namespace Modules\MyHealth\Http\Controllers;

use Modules\MyHealth\Entities\PatientDoctor;
use Modules\MyHealth\Entities\Specialization;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //business id is hospital business id
         $business_id = $request->session()->get('business.id');
        try {
            $doctor_data = array(
                'business_id' => $business_id,
                'doctor_name' => $request->doctor_name,
                'qualification' => Specialization::find($request->input('specialization'))->name,
                'signatures' => $request->doctor_name
            );

            PatientDoctor::create($doctor_data);
            $doctors = PatientDoctor::select('doctor_name', 'id')->get();
            $output = [
                'success' => 1,
                'msg' => __('myhealth::patient.doctor_add_success'),
                'doctors' => $doctors
            ];

            return $output;
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }
  

    public function getSpecialization()
    {
        $specializations = Specialization::all();
        return response()->json($specializations);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
     * get the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDocorts(Request $request)
    {
        $hospital_name = $request->hospital_name;

        $doctors = PatientDoctor::where('hospital_name', $hospital_name)->select('doctor_name', 'id')->get();

        $output = [
            'success' => 1,
            'msg' => __('myhealth::patient.doctor_add_success'),
            'doctors' => $doctors
        ];

        return $output;
    }
    
    

    

    
    
}
