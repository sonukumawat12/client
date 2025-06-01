<!-- Nav Item - Utilities Collapse Menu -->
<li class="nav-item treeview {{ in_array($request->segment(1), ['patient', 'medication']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#patientmodule-menu" aria-expanded="true" aria-controls="patientmodule-menu">
        <i class="fa fa-medkit"></i>
        <span>@lang('patient.module_name')</span>
    </a>
    <div id="patientmodule-menu" class="collapse" aria-labelledby="patientmodule-menu" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('patient.module_name'):</h6>
            <a class="collapse-item {{ $request->segment(1) == 'patient' ? 'active' : '' }}" href="{{ action('\Modules\MyHealth\Http\Controllers\PatientController@index') }}">@lang('patient.home')</a>
            <a class="collapse-item {{ $request->segment(1) == 'medication' ? 'active' : '' }}" href="{{ action('\Modules\MyHealth\Http\Controllers\MedicationController@index') }}">@lang('patient.medications')</a>
        </div>
    </div>
</li>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item {{ in_array($request->segment(1), ['patient-test-records']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#patienttest-menu" aria-expanded="true" aria-controls="patienttest-menu">
        <i class="fa fa-heartbeat"></i>
        <span>@lang('patient.test_records.module_name')</span>
    </a>
    <div id="patienttest-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('patient.test_records.module_name'):</h6>
            <a class="collapse-item {{ $request->segment(1) == 'patient' ? 'active' : '' }}" href="{{ action('\Modules\MyHealth\Http\Controllers\PatientController@index') }}">@lang('patient.test_records.sugar_testing')</a>
            <a class="collapse-item {{ $request->segment(1) == 'patient' ? 'active' : '' }}" href="{{ action('\Modules\MyHealth\Http\Controllers\SugerReadingController@index') }}">@lang('Sugar Reading')</a>

            <a class="collapse-item {{ $request->segment(1) == 'patient' ? 'active' : '' }}" href="{{ action('\Modules\MyHealth\Http\Controllers\PatientController@index') }}">@lang('patient.test_records.pressure_testing')</a>
        </div>
    </div>
</li>