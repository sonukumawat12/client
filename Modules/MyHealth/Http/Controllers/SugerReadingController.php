<?php

namespace Modules\MyHealth\Http\Controllers;

use App\Ad;
use App\AdPage;
use App\Business;
use App\Contact;
use Illuminate\Http\Request;
use Modules\MyHealth\Entities\PatientDetail;
use Modules\MyHealth\Entities\PatientSugarReading;
use Modules\MyHealth\Entities\SugarReadingBreakfast;
use Modules\MyHealth\Entities\SugarReadingLunchs;
use Modules\MyHealth\Entities\SugarReadingDinner;
use Modules\MyHealth\Entities\PatientMedicine;
use Modules\MyHealth\Entities\PatientPrescription;
use Modules\MyHealth\Entities\PrescriptionMedicine;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Utils\BusinessUtil;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use DateTime;

class SugerReadingController extends Controller
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
    public function index()
    {


        if (request()->ajax()) {
            // $users = User::where('id', Auth::user()->id)
            //     ->where('member', 0)
            //     ->first();

            // if ($users) {
            //$member = Member::where('username', $users->username)->first();

            $sugar_readings = PatientSugarReading::leftJoin('sugar_reading_breakfasts', 'patient_sugar_readings.id', '=', 'sugar_reading_breakfasts.sugar_reading_id')
                ->leftJoin('sugar_reading_lunchs', 'patient_sugar_readings.id', '=', 'sugar_reading_lunchs.sugar_reading_id')
                ->leftJoin('sugar_reading_dinners', 'patient_sugar_readings.id', '=', 'sugar_reading_dinners.sugar_reading_id')
                ->where('member_id', Auth::user()->id)
                ->select([
                    'patient_sugar_readings.id as id',
                    'patient_sugar_readings.sugar_reading_breakfast as sugar_reading_breakfast',
                    'patient_sugar_readings.sugar_reading_lunch as sugar_reading_lunch',
                    'patient_sugar_readings.sugar_reading_dinner as sugar_reading_dinner',
                    'patient_sugar_readings.date as sugar_reading_date',
                    'sugar_reading_breakfasts.reading_number_one as breakfast_reading_number_one',
                    'sugar_reading_breakfasts.reading_number_two as breakfast_reading_number_two',
                    'sugar_reading_breakfasts.reading_number_three as breakfast_reading_number_three',
                    'sugar_reading_breakfasts.time_one as breakfast_time_one',
                    'sugar_reading_breakfasts.reading_one as breakfast_reading_one',
                    'sugar_reading_breakfasts.note_one as breakfast_note_one',
                    'sugar_reading_breakfasts.time_two as breakfast_time_two',
                    'sugar_reading_breakfasts.reading_two as breakfast_reading_two',
                    'sugar_reading_breakfasts.note_two as breakfast_note_two',
                    'sugar_reading_breakfasts.time_three as breakfast_time_three',
                    'sugar_reading_breakfasts.reading_three as breakfast_reading_three',
                    'sugar_reading_breakfasts.note_three as breakfast_note_three',
                    'sugar_reading_breakfasts.medicine_one as breakfast_medicine_one',
                    'sugar_reading_breakfasts.medicine_two as breakfast_medicine_two',
                    'sugar_reading_breakfasts.medicine_three as breakfast_medicine_three',
                    'sugar_reading_breakfasts.health_issue_one as breakfast_health_issue_one',
                    'sugar_reading_breakfasts.health_issue_two as breakfast_health_issue_two',
                    'sugar_reading_breakfasts.health_issue_three as breakfast_health_issue_three',
                    'sugar_reading_lunchs.reading_number_one as lunch_reading_number_one',
                    'sugar_reading_lunchs.reading_number_two as lunch_reading_number_two',
                    'sugar_reading_lunchs.reading_number_three as lunch_reading_number_three',
                    'sugar_reading_lunchs.time_one as lunch_time_one',
                    'sugar_reading_lunchs.reading_one as lunch_reading_one',
                    'sugar_reading_lunchs.note_one as lunch_note_one',
                    'sugar_reading_lunchs.time_two as lunch_time_two',
                    'sugar_reading_lunchs.reading_two as lunch_reading_two',
                    'sugar_reading_lunchs.note_two as lunch_note_two',
                    'sugar_reading_lunchs.time_three as lunch_time_three',
                    'sugar_reading_lunchs.reading_three as lunch_reading_three',
                    'sugar_reading_lunchs.note_three as lunch_note_three',
                    'sugar_reading_lunchs.medicine_one as lunch_medicine_one',
                    'sugar_reading_lunchs.medicine_two as lunch_medicine_two',
                    'sugar_reading_lunchs.medicine_three as lunch_medicine_three',
                    'sugar_reading_lunchs.health_issue_one as lunch_health_issue_one',
                    'sugar_reading_lunchs.health_issue_two as lunch_health_issue_two',
                    'sugar_reading_lunchs.health_issue_three as lunch_health_issue_three',
                    'sugar_reading_dinners.reading_number_one as dinner_reading_number_one',
                    'sugar_reading_dinners.reading_number_two as dinner_reading_number_two',
                    'sugar_reading_dinners.reading_number_three as dinner_reading_number_three',
                    'sugar_reading_dinners.time_one as dinner_time_one',
                    'sugar_reading_dinners.reading_one as dinner_reading_one',
                    'sugar_reading_dinners.note_one as dinner_note_one',
                    'sugar_reading_dinners.time_two as dinner_time_two',
                    'sugar_reading_dinners.reading_two as dinner_reading_two',
                    'sugar_reading_dinners.note_two as dinner_note_two',
                    'sugar_reading_dinners.time_three as dinner_time_three',
                    'sugar_reading_dinners.reading_three as dinner_reading_three',
                    'sugar_reading_dinners.note_three as dinner_note_three',
                    'sugar_reading_dinners.medicine_one as dinner_medicine_one',
                    'sugar_reading_dinners.medicine_two as dinner_medicine_two',
                    'sugar_reading_dinners.medicine_three as dinner_medicine_three',
                    'sugar_reading_dinners.health_issue_one as dinner_health_issue_one',
                    'sugar_reading_dinners.health_issue_two as dinner_health_issue_two',
                    'sugar_reading_dinners.health_issue_three as dinner_health_issue_three',
                ]); // Execute the query

            return DataTables::of($sugar_readings)

                ->addColumn('breakfast1', function ($row) {
    $html = '<div class="btn-group">';
    if ($row->sugar_reading_breakfast == "0") {
        $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
            <i class="fa fa-plus"></i> ' . __("messages.add") . '
        </a>';
    } else {
        if ($row->breakfast_reading_number_one == "1") {
            $formatted_time = date('h:i A', strtotime($row->breakfast_time_one));  // Time formatting
            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@edit', [$row->id, 'type' => 'breakfast', 'number' => $row->breakfast_reading_number_one]) . '" class="btn btn-warning btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: green; color: white; border: none; margin-right: 5px;">
                <i class="fa fa-edit"></i> ' . __("messages.edit") . ' </a>';

            // Medicine button logic
            if (!empty($row->breakfast_medicine_one)) {
                         $html .= '<a href="#" data-href="' . route('medicine.view', $row->breakfast_medicine_one) . '" class="btn btn-xs btn-modal" style="background-color: orange; color: white; border: none;">
                                <i class="fa fa-medkit"></i> ' . __("Medicine") . '
                              </a>';
                        }

            if (!empty($row->breakfast_note_one)) {
        // Add the note content directly in a data attribute
        $html .= '<a href="#" class="btn btn-xs btn-show-note" data-note="' . $row->breakfast_note_one . '" style="background-color: #007bff; color: white; border: none;">
                    <i class="fa fa-sticky-note"></i> ' . __("Note") . '
                  </a>';
    }

            $html .= '<br> Time: ' . $formatted_time;
            $html .= '<br><span style="color: red;">Reading: ' . $row->breakfast_reading_number_one . '</span>';
        } else {
            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                <i class="fa fa-plus"></i> ' . __("messages.add") . '
            </a>';
        }
    }

    $html .= '</div>';
    return $html;
})

                ->addColumn('breakfast2', function ($row) {
                    $html = '<div class="btn-group">';
                    if ($row->sugar_reading_breakfast == "0") {

                        $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                    } else {
                        if ($row->breakfast_reading_number_two == "2") {
                            $formatted_time = date('h:i A', strtotime($row->breakfast_reading_number_two));
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@edit', [$row->id, 'type' => 'breakfast', 'number' => $row->breakfast_reading_number_two]) . '" class="btn btn-warning btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: green; color: white; border: none; margin-right: 5px;">
                    <i class="fa fa-edit"></i> ' . __("messages.edit") . '   </a>';
                        // Add Medicine button logic: Show only if medicine is added
                        if (!empty($row->breakfast_medicine_two)) {
                             
                         $html .= '<a href="#" data-href="' . route('medicine.view', $row->breakfast_medicine_two) . '" class="btn btn-xs btn-modal" style="background-color: orange; color: white; border: none;">
                                <i class="fa fa-medkit"></i> ' . __("Medicine") . '
                              </a>';
                        
                        }
                         if (!empty($row->breakfast_note_two)) {
        // Add the note content directly in a data attribute
        $html .= '<a href="#" class="btn btn-xs btn-show-note" data-note="' . $row->breakfast_note_two . '" style="background-color: #007bff; color: white; border: none;">
                    <i class="fa fa-sticky-note"></i> ' . __("Note") . '
                  </a>';
    }
                            $html .= '<br>';
                            $html .= ' Time: ' .  $formatted_time;
                            $html .= '<br>';
                            $html .= '<span style="color: red;">Reading: ' . $row->breakfast_reading_number_two . '</span>';
                            $html .= '<br>';
                           
                        } else {
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                        }
                    }
                   
                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('breakfast3', function ($row) {
                    $html = '<div class="btn-group">';
                    if ($row->sugar_reading_breakfast == "0") {
                        $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                    } else {

                        if ($row->breakfast_reading_number_three == "3") {
                            $formatted_time = date('h:i A', strtotime($row->breakfast_reading_number_three));
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@edit', [$row->id, 'type' => 'breakfast', 'number' => $row->breakfast_reading_number_three]) . '" class="btn btn-warning btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: green; color: white; border: none; margin-right: 5px;">
                    <i class="fa fa-edit"></i> ' . __("messages.edit") . '   </a>';
                        // Add Medicine button logic: Show only if medicine is added
                        if (!empty($row->breakfast_medicine_three)) {
                            $html .= '<a href="#" data-href="' . route('medicine.view', $row->breakfast_medicine_three) . '" class="btn btn-xs btn-modal" data-container=".medicine_modal" style="background-color: orange; color: white; border: none;">
                <i class="fa fa-medkit"></i> ' . __("Medicine") . '
              </a>';
                        }
                         if (!empty($row->breakfast_note_three)) {
        // Add the note content directly in a data attribute
        $html .= '<a href="#" class="btn btn-xs btn-show-note" data-note="' . $row->breakfast_note_three . '" style="background-color: #007bff; color: white; border: none;">
                    <i class="fa fa-sticky-note"></i> ' . __("Note") . '
                  </a>';
    }
                            $html .= '<br>';
                            $html .= ' Time: ' . $formatted_time;
                            $html .= '<br>';
                            $html .= '<span style="color: red;">Reading: ' . $row->breakfast_reading_number_three . '</span>';
                            $html .= '<br>';
                         
                        } else {
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                        }
                    }
                    
                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('breakfast', function ($row) {

                    return '';
                })


                ->addColumn('lunch1', function ($row) {
                    $html = '<div class="btn-group">';
                    if ($row->sugar_reading_lunch == "0") {
                        $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                    } else {
                        if ($row->lunch_reading_number_one == "1") {
                            $formatted_time = date('h:i A', strtotime($row->lunch_reading_number_one));
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@edit', [$row->id, 'type' => 'lunch', 'number' => $row->lunch_reading_number_one]) . '" class="btn btn-warning btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: green; color: white; border: none; margin-right: 5px;">
                    <i class="fa fa-edit"></i> ' . __("messages.edit") . '   </a>';
                        // Add Medicine button logic: Show only if medicine is added
                        if (!empty($row->lunch_medicine_one)) {
                          $html .= '<a href="#" data-href="' . route('medicine.view', $row->lunch_medicine_one) . '" class="btn btn-xs btn-modal" style="background-color: orange; color: white; border: none;">
                                <i class="fa fa-medkit"></i> ' . __("Medicine") . '
                              </a>';
                        }
                         if (!empty($row->lunch_note_one)) {
        // Add the note content directly in a data attribute
        $html .= '<a href="#" class="btn btn-xs btn-show-note" data-note="' . $row->lunch_note_one . '" style="background-color: #007bff; color: white; border: none;">
                    <i class="fa fa-sticky-note"></i> ' . __("Note") . '
                  </a>';
    }
                            $html .= '<br>';
                            $html .= ' Time: ' . $formatted_time;
                            $html .= '<br>';
                            $html .= '<span style="color: red;">Reading: ' . $row->lunch_reading_number_one . '</span>';
                            $html .= '<br>';
                         
                        } else {
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                        }
                    }
                    
                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('lunch2', function ($row) {
                    $html = '<div class="btn-group">';
                    if ($row->sugar_reading_lunch == "0") {
                        $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                    } else {
                        if ($row->lunch_reading_number_two == "2") {
                            $formatted_time = date('h:i A', strtotime($row->lunch_reading_number_two));
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@edit', [$row->id, 'type' => 'lunch', 'number' => $row->lunch_reading_number_two]) . '" class="btn btn-warning btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: green; color: white; border: none; margin-right: 5px;">
                    <i class="fa fa-edit"></i> ' . __("messages.edit") . '   </a>';
                        // Add Medicine button logic: Show only if medicine is added
                        if (!empty($row->lunch_medicine_two)) {
                     $html .= '<a href="#" data-href="' . route('medicine.view', $row->lunch_medicine_two) . '" class="btn btn-xs btn-modal" style="background-color: orange; color: white; border: none;">
                                <i class="fa fa-medkit"></i> ' . __("Medicine") . '
                              </a>';
                        }
                         if (!empty($row->lunch_note_two)) {
        // Add the note content directly in a data attribute
        $html .= '<a href="#" class="btn btn-xs btn-show-note" data-note="' . $row->lunch_note_two . '" style="background-color: #007bff; color: white; border: none;">
                    <i class="fa fa-sticky-note"></i> ' . __("Note") . '
                  </a>';
    }
                            $html .= '<br>';
                            $html .= ' Time: ' . $formatted_time;
                            $html .= '<br>';
                            $html .= '<span style="color: red;">Reading: ' . $row->lunch_reading_number_two . '</span>';
                            $html .= '<br>';
                           
                        } else {
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                        }
                    }
                    
                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('lunch3', function ($row) {
                    $html = '<div class="btn-group">';
                    if ($row->sugar_reading_lunch == "0") {
                        $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                    } else {
                        if ($row->lunch_reading_number_three == "3") {
                            $formatted_time = date('h:i A', strtotime($row->lunch_reading_number_three));
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@edit', [$row->id, 'type' => 'lunch', 'number' => $row->lunch_reading_number_three]) . '" class="btn btn-warning btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: green; color: white; border: none; margin-right: 5px;">
                    <i class="fa fa-edit"></i> ' . __("messages.edit") . '   </a>';
                        // Add Medicine button logic: Show only if medicine is added
                        if (!empty($row->lunch_medicine_three)) {
                          $html .= '<a href="#" data-href="' . route('medicine.view', $row->lunch_medicine_three) . '" class="btn btn-xs btn-modal" style="background-color: orange; color: white; border: none;">
                                <i class="fa fa-medkit"></i> ' . __("Medicine") . '
                              </a>';
                        }
                         if (!empty($row->lunch_note_three)) {
        // Add the note content directly in a data attribute
        $html .= '<a href="#" class="btn btn-xs btn-show-note" data-note="' . $row->lunch_note_three . '" style="background-color: #007bff; color: white; border: none;">
                    <i class="fa fa-sticky-note"></i> ' . __("Note") . '
                  </a>';
    }
                            $html .= '<br>';
                            $html .= ' Time: ' . $formatted_time;
                            $html .= '<br>';
                            $html .= '<span style="color: red;">Reading: ' . $row->lunch_reading_number_three . '</span>';
                            $html .= '<br>';
                        
                        } else {
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                        }
                    }
                   
                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('lunch', function ($row) {

                    return '';
                })


                ->addColumn('dinner1', function ($row) {
    $html = '<div class="btn-group">';

    // If no sugar reading for dinner, show the 'Add' button
    if ($row->sugar_reading_dinner == "0") {
        $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                    <i class="fa fa-plus"></i> ' . __("messages.add") . '
                  </a>';
    } else {
        // If sugar reading is available
        if ($row->dinner_reading_number_one == "1") {
            // Correcting the formatting of the dinner reading time
            $formatted_time = date('h:i A', strtotime($row->dinner_time_one));  

            // Edit button for dinner reading
            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@edit', [$row->id, 'type' => 'dinner', 'number' => $row->dinner_reading_number_one]) . '" class="btn btn-warning btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: green; color: white; border: none; margin-right: 5px;">
                        <i class="fa fa-edit"></i> ' . __("messages.edit") . '  
                      </a>';

            // Medicine button logic: Show only if medicine is added for dinner
            if (!empty($row->dinner_medicine_one)) {
                $html .= '<a href="#" data-href="' . route('medicine.view', $row->dinner_medicine_one) . '" class="btn btn-xs btn-modal" data-container=".medicine_modal" style="background-color: orange; color: white; border: none; margin-right: 5px;">
                            <i class="fa fa-medkit"></i> ' . __("Medicine") . '
                          </a>';
            }
             if (!empty($row->dinner_note_one)) {
        // Add the note content directly in a data attribute
        $html .= '<a href="#" class="btn btn-xs btn-show-note" data-note="' . $row->dinner_note_one . '" style="background-color: #007bff; color: white; border: none;">
                    <i class="fa fa-sticky-note"></i> ' . __("Note") . '
                  </a>';
    }

            // Displaying time, reading, and notes
            $html .= '<br>';
            $html .= ' Time: ' . $formatted_time;  // Display formatted time
            $html .= '<br>';
            $html .= '<span style="color: red;">Reading: ' . $row->dinner_reading_number_one . '</span>';
            $html .= '<br>';
        
        } else {
            // If dinner reading number is not 1, allow adding a reading
            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                        <i class="fa fa-plus"></i> ' . __("messages.add") . '
                      </a>';
        }
    }

    $html .= '</div>';

    return $html;
})

                ->addColumn('dinner2', function ($row) {
                    $html = '<div class="btn-group">';
                    if ($row->sugar_reading_dinner == "0") {
                        $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                    } else {
                        if ($row->dinner_reading_number_two == "2") {
                            $formatted_time = date('h:i A', strtotime($row->dinner_reading_number_two));
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@edit', [$row->id, 'type' => 'dinner', 'number' => $row->dinner_reading_number_two]) . '" class="btn btn-warning btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: green; color: white; border: none; margin-right: 5px;">
                    <i class="fa fa-edit"></i> ' . __("messages.edit") . '   </a>';
                            // Add Medicine button logic: Show only if medicine is added
                            if (!empty($row->dinner_medicine_two)) {
                             $html .= '<a href="#" data-href="' . route('medicine.view', $row->dinner_medicine_two) . '" class="btn btn-xs btn-modal" style="background-color: orange; color: white; border: none;">
                                <i class="fa fa-medkit"></i> ' . __("Medicine") . '
                              </a>';
                            }
                             if (!empty($row->dinner_note_two)) {
        // Add the note content directly in a data attribute
        $html .= '<a href="#" class="btn btn-xs btn-show-note" data-note="' . $row->dinner_note_two . '" style="background-color: #007bff; color: white; border: none;">
                    <i class="fa fa-sticky-note"></i> ' . __("Note") . '
                  </a>';
    }
                            $html .= '<br>';
                            $html .= ' Time: ' . $formatted_time;
                            $html .= '<br>';
                            $html .= '<span style="color: red;">Reading: ' . $row->dinner_reading_number_two . '</span>';
                            $html .= '<br>';
                           
                        } else {
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                        }
                    }

                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('dinner3', function ($row) {
                    $html = '<div class="btn-group">';
                    if ($row->sugar_reading_dinner == "0") {
                        $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                    } else {
                        if ($row->dinner_reading_number_three == "3") {
                            $formatted_time = date('h:i A', strtotime($row->dinner_reading_number_two));
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@edit', [$row->id, 'type' => 'dinner', 'number' => $row->dinner_reading_number_three]) . '" class="btn btn-warning btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: green; color: white; border: none; margin-right: 5px;">
                    <i class="fa fa-edit"></i> ' . __("messages.edit") . '   </a>';
                        // Add Medicine button logic: Show only if medicine is added
                        if (!empty($row->dinner_medicine_three)) {
                          $html .= '<a href="#" data-href="' . route('medicine.view', $row->dinner_medicine_three) . '" class="btn btn-xs btn-modal" style="background-color: orange; color: white; border: none;">
                                <i class="fa fa-medkit"></i> ' . __("Medicine") . '
                              </a>';
                        }
                         if (!empty($row->dinner_note_three)) {
        // Add the note content directly in a data attribute
        $html .= '<a href="#" class="btn btn-xs btn-show-note" data-note="' . $row->dinner_note_three . '" style="background-color: #007bff; color: white; border: none;">
                    <i class="fa fa-sticky-note"></i> ' . __("Note") . '
                  </a>';
    }
                            $html .= '<br>';
                            $html .= ' Time: ' . $formatted_time;
                            $html .= '<br>';
                            $html .= '<span style="color: red;">Reading: ' . $row->dinner_reading_number_three . '</span>';
                            $html .= '<br>';
                        } else {
                            $html .= '<a href="#" data-href="' . action('\Modules\MyHealth\Http\Controllers\SugerReadingController@add', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".suger_reading_model" style="background-color: #61CBF3; color: white; border: none;">
                                <i class="fa fa-plus"></i> ' . __("messages.add") . '
                              </a>';
                        }
                    }
                    
                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('dinner', function ($row) {

                    return '';
                })


                ->removeColumn('id')
                ->rawColumns(['breakfast1', 'breakfast2', 'breakfast3', 'breakfast', 'lunch1', 'lunch2', 'lunch3', 'lunch', 'dinner1', 'dinner2', 'dinner3', 'dinner'])

                ->make(true);
            // }
        }



        return view('myhealth::patient.sugar_reading');
    }
    public function sugar_reading(Request $request)
    {


        return view('myhealth::patient.sugar_reading');
    }

    public function fetchData(Request $request)
    {
        $type = $request->input('type');
        $reading_id = $request->input('sugar_reading_id');

        // Initialize the data array
        $data = [
            'reading_value' => null,
            'note' => null,
            'time' => null,
        ];


        $model = null;

        if ($type == 'breakfast') {
            $model = SugarReadingBreakfast::class;
        } elseif ($type == 'lunch') {
            $model = SugarReadingLunchs::class;
        } elseif ($type == 'dinner') {
            $model = SugarReadingDinner::class;
        }


        if ($model) {
            $result = $model::where('sugar_reading_id', $reading_id)->first();

            if ($result) {
                $data['reading_value'] = $result->reading;
                $data['note'] = $result->note;
                $data['time'] = $result->time;
            }
        }


        return response()->json($data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('myhealth::patient.add_date');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');
        $year = request('year1') . request('year2') . request('year3') . request('year4');
        $month = request('month1') . request('month2');
        $day = request('date1') . request('date2');

        $dateString = "$year-$month-$day";
        $user = User::where('id', auth()->user()->id)->first();
        // Use Carbon to create a date object
        $date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString);
        try {
            $tests_data = array(
                'date' => $date,
                'member_id' => $user->id,
            );
            $result = PatientSugarReading::create($tests_data);

            $output = [
                'success' => 1,
                'msg' => __('myhealth::patient.test_add_success')
            ];

            return redirect()->back()->with('status', $output);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $type = request()->query('type');
        $number = request()->query('number');
        $sugarReading = PatientSugarReading::findOrFail($id); // Replace with your actual model

        if ($type === 'breakfast') {
            $sugerReading = SugarReadingBreakfast::where('sugar_reading_id', $sugarReading->id)->first();
            if ($number == 1) {

                $time = $sugerReading->time_one;
                $dateTime = new DateTime($time);
                $hour = $dateTime->format('H'); // 24-hour format
                $minute = $dateTime->format('i');
                $reading = $sugerReading->reading_one;
                $note = $sugerReading->note_one;
            } elseif ($number == 2) {

                $time = $sugerReading->time_two;
                $dateTime = new DateTime($time);
                $hour = $dateTime->format('H'); // 24-hour format
                $minute = $dateTime->format('i');
                $reading = $sugerReading->reading_two;
                $note = $sugerReading->note_two;
            } else {
                $time = $sugerReading->time_three;
                $dateTime = new DateTime($time);
                $hour = $dateTime->format('H'); // 24-hour format
                $minute = $dateTime->format('i');
                $reading = $sugerReading->reading_three;
                $note = $sugerReading->note_three;
            }
        } elseif ($type === 'lunch') {

            $sugerReading = SugarReadingLunchs::where('sugar_reading_id', $sugarReading->id)->first();
            if ($number == 1) {

                $time = $sugerReading->time_one;
                $dateTime = new DateTime($time);
                $hour = $dateTime->format('H'); // 24-hour format
                $minute = $dateTime->format('i');
                $reading = $sugerReading->reading_one;
                $note = $sugerReading->note_one;
            } elseif ($number == 2) {

                $time = $sugerReading->time_two;
                $dateTime = new DateTime($time);
                $hour = $dateTime->format('H'); // 24-hour format
                $minute = $dateTime->format('i');
                $reading = $sugerReading->reading_two;
                $note = $sugerReading->note_two;
            } else {
                $time = $sugerReading->time_three;
                $dateTime = new DateTime($time);
                $hour = $dateTime->format('H'); // 24-hour format
                $minute = $dateTime->format('i');
                $reading = $sugerReading->reading_three;
                $note = $sugerReading->note_three;
            }
        } else {

            $sugerReading = SugarReadingDinner::where('sugar_reading_id', $sugarReading->id)->first();
            if ($number == 1) {

                $time = $sugerReading->time_one;
                $dateTime = new DateTime($time);
                $hour = $dateTime->format('H'); // 24-hour format
                $minute = $dateTime->format('i');
                $reading = $sugerReading->reading_one;
                $note = $sugerReading->note_one;
            } elseif ($number == 2) {

                $time = $sugerReading->time_two;
                $dateTime = new DateTime($time);
                $hour = $dateTime->format('H'); // 24-hour format
                $minute = $dateTime->format('i');
                $reading = $sugerReading->reading_two;
                $note = $sugerReading->note_two;
            } else {
                $time = $sugerReading->time_three;
                $dateTime = new DateTime($time);
                $hour = $dateTime->format('H'); // 24-hour format
                $minute = $dateTime->format('i');
                $reading = $sugerReading->reading_three;
                $note = $sugerReading->note_three;
            }
        }

        return view('myhealth::patient.edit_sugar_reading', compact('sugarReading', 'reading', 'type', 'hour', 'minute', 'note', 'number'));
    }
    public function breakfast_one($id)
    {


        $sugarReading = PatientSugarReading::findOrFail($id);


        return view('myhealth::patient.edit_sugar_reading', compact('sugarReading', 'breakfastReading'));
    }
    public function breakfast_two($id)
    {


        $sugarReading = PatientSugarReading::findOrFail($id); // Replace with your actual model

        return view('myhealth::patient.edit_sugar_reading', compact('sugarReading'));
    }
    public function breakfast_three($id)
    {


        $sugarReading = PatientSugarReading::findOrFail($id); // Replace with your actual model

        return view('myhealth::patient.edit_sugar_reading', compact('sugarReading'));
    }
    public function add(Request $request, $id)
    {
        // Find the sugar reading for the given ID
        $sugarReading = PatientSugarReading::findOrFail($id);

        // Fetch the business ID from the session
        $business_id = $request->session()->get('business.id');

        // Fetch the list of health issues from the patient prescriptions
        $health_issues = PatientPrescription::where('business_id', $business_id)
            ->distinct()
            ->pluck('diagnosis', 'diagnosis'); // Get diagnosis as both key and value for dropdown




        // Return the view with the necessary data
        return view('myhealth::patient.add_sugar_reading', compact('sugarReading', 'health_issues'));
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
        $breakfast = 0;
        $lunch = 0;
        $dinner = 0;
        $data = $request->except('_token');
        $hour = $request->input('hour');
        $minute = $request->input('minute');
        $second = 0; // Fixed value for seconds
        Log:
        info($request);

        // Format the time as HH:MM:SS
        $time = sprintf('%02d:%02d:%02d', $hour, $minute, $second);

        try {
            if ($request->sugar_reading == 'breakfast') {
                $breakfast = 1;
                $update_data = array(
                    'sugar_reading_breakfast' => $breakfast
                );
                $patient_details = PatientSugarReading::where('id', $id)->update($update_data);

                $sugarReadingId = $id;
                if ($request->sugar_reading_number == "1") {
                    $breakfastData = [
                        'reading_number_one' => $request->sugar_reading_number,
                        'time_one' => $time,
                        'reading_one' => $request->reading_value,
                        'note_one' => $request->note,
                        'medicine_one' => $request->medicine_name,
                        'health_issue_one' => $request->health_issue
                    ];
                } elseif ($request->sugar_reading_number == "2") {
                    $breakfastData = [
                        'reading_number_two' => $request->sugar_reading_number,
                        'time_two' => $time,
                        'reading_two' => $request->reading_value,
                        'note_two' => $request->note,
                        'medicine_one' => $request->medicine_name,
                        'health_issue_one' => $request->health_issue
                    ];
                } else {
                    $breakfastData = [
                        'reading_number_three' => $request->sugar_reading_number,
                        'time_three' => $time,
                        'reading_three' => $request->reading_value,
                        'note_three' => $request->note,
                        'medicine_one' => $request->medicine_name,
                        'health_issue_one' => $request->health_issue
                    ];
                }

                $result = SugarReadingBreakfast::updateOrCreate(
                    ['sugar_reading_id' => $sugarReadingId],
                    $breakfastData
                );

                $output = [
                    'success' => 1,
                    'msg' => __("myhealth::patient.details_update_success")
                ];
                return redirect(url()->previous())->with('status', $output);
            } elseif ($request->sugar_reading == 'lunch') {
                $lunch = 1;
                $update_data = array(
                    'sugar_reading_lunch' => $lunch
                );
                $patient_details = PatientSugarReading::where('id', $id)->update($update_data);

                $sugarReadingId = $id;

                if ($request->sugar_reading_number == "1") {
                    $lunch_data = [
                        'reading_number_one' => $request->sugar_reading_number,
                        'time_one' => $time,
                        'reading_one' => $request->reading_value,
                        'note_one' => $request->note,
                        'medicine_one' => $request->medicine_name,
                        'health_issue_one' => $request->health_issue
                    ];
                } elseif ($request->sugar_reading_number == "2") {
                    $lunch_data = [
                        'reading_number_two' => $request->sugar_reading_number,
                        'time_two' => $time,
                        'reading_two' => $request->reading_value,
                        'note_two' => $request->note,
                        'medicine_two' => $request->medicine_name,
                        'health_issue_two' => $request->health_issue
                    ];
                } else {
                    $lunch_data = [
                        'reading_number_three' => $request->sugar_reading_number,
                        'time_three' => $time,
                        'reading_three' => $request->reading_value,
                        'note_three' => $request->note,
                        'medicine_two' => $request->medicine_name,
                        'health_issue_two' => $request->health_issue
                    ];
                }

                $result = SugarReadingLunchs::updateOrCreate(
                    ['sugar_reading_id' => $sugarReadingId],
                    $lunch_data
                );

                $output = [
                    'success' => 1,
                    'msg' => __("myhealth::patient.details_update_success")
                ];
                return redirect(url()->previous())->with('status', $output);
            } elseif ($request->sugar_reading == 'dinner') {
                $dinner = 1;
                $update_data = array(
                    'sugar_reading_dinner' => $dinner
                );
                $patient_details = PatientSugarReading::where('id', $id)->update($update_data);

                $sugarReadingId = $id;

                if ($request->sugar_reading_number == "1") {
                    $dinner_data = [
                        'reading_number_one' => $request->sugar_reading_number,
                        'time_one' => $time,
                        'reading_one' => $request->reading_value,
                        'note_one' => $request->note,
                        'medicine_one' => $request->medicine_name,
                        'health_issue_one' => $request->health_issue
                    ];
                } elseif ($request->sugar_reading_number == "2") {
                    $dinner_data = [
                        'reading_number_two' => $request->sugar_reading_number,
                        'time_two' => $time,
                        'reading_two' => $request->reading_value,
                        'note_two' => $request->note,
                        'medicine_two' => $request->medicine_name,
                        'health_issue_two' => $request->health_issue
                    ];
                } else {
                    $dinner_data = [
                        'reading_number_three' => $request->sugar_reading_number,
                        'time_three' => $time,
                        'reading_three' => $request->reading_value,
                        'note_three' => $request->note,
                        'medicine_three' => $request->medicine_name,
                        'health_issue_three' => $request->health_issue
                    ];
                }

                $result = SugarReadingDinner::updateOrCreate(
                    ['sugar_reading_id' => $sugarReadingId],
                    $dinner_data
                );

                $output = [
                    'success' => 1,
                    'msg' => __("myhealth::patient.details_update_success")
                ];
                return redirect(url()->previous())->with('status', $output);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            // Define the $output in case of an error
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
            return redirect(url()->previous())->with('status', $output);
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
                    $query->Where('users.username', 'like', '%' . $term . '%')
                        ->orWhere('patient_details.mobile', 'like', '%' . $term . '%')
                        ->orWhere('patient_details.name', 'like', '%' . $term . '%');
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

    public function getMedicinesForHealthIssue(Request $request)
    {

        $business_id = $request->session()->get('business.id');
        $health_issue = $request->input('health_issue');

        // Fetch medicines linked to the health issue
        $medicines = PatientMedicine::join('prescription_medicines', 'patient_medicines.id', '=', 'prescription_medicines.medicine_id')
            ->join('patient_prescriptions', 'prescription_medicines.prescription_id', '=', 'patient_prescriptions.id')
            ->where('patient_prescriptions.business_id', $business_id)
            ->where('patient_prescriptions.diagnosis', $health_issue)
            ->pluck('patient_medicines.medicine_name', 'patient_medicines.id');

        return response()->json($medicines);
    }



    public function getDoseForMedicine(Request $request)
    {
        $medicine_id = $request->input('medicine_id'); // Get the medicine ID from the request

        // Fetch the prescription_id from the prescription_medicines table
        $prescription_id = PrescriptionMedicine::where('medicine_id', $medicine_id)
            ->value('prescription_id');

        if ($prescription_id) {
            // Fetch the dose (amount) from the PatientPrescription table using the prescription_id
            $dose = PatientPrescription::where('id', $prescription_id)
                ->value('amount'); // Fetch the amount (dose) from the prescription
        } else {
            $dose = null; // If no prescription found, set dose to null
        }

        return response()->json(['dose' => $dose]);
    }
}
