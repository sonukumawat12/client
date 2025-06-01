

@php
    $bus_id = request()->session()->get('business.id');
    $package = \Modules\Superadmin\Entities\Subscription::active_subscription($bus_id);

@endphp
<!-- Main content -->
<section class="content">
  
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'myhealth::patient.all_your_prescription' )])
        {{-- @can('prescription.create') --}}
            @slot('tool')
                <div class="box-tools">
                    @if (!empty($package))
                    @if (session()->get('business.is_hospital'))
                    <a  class="btn btn-block btn-primary" id='prescription_add_btn'
                        href="{{action('\Modules\MyHealth\Http\Controllers\PrescriptionController@create',['patient_code' => $patient_code])}}" >
                      
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
                    @endif
                    @if (session()->get('business.is_patient'))
                    <a  class="btn btn-block btn-primary" id='prescription_add_btn'
                        href="{{action('\Modules\MyHealth\Http\Controllers\PrescriptionController@create',['patient_code' => $patient_code])}}" >
                      
                        <i class="fa fa-upload"></i> @lang( 'myhealth::patient.upload' )</a>
                    @endif
                    @endif
                  
                </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="prescription_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width:45px;">@lang( 'myhealth::patient.date' )</th>
                            <th>@lang( 'myhealth::patient.hospital' )</th>
                            <th>@lang( 'myhealth::patient.doctor' )</th>
                            @if (request()->session()->get('business.is_patient'))
                            <th>@lang( 'myhealth::patient.amount' )</th>
                            @endif
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        {{-- @endcan --}}
    @endcomponent

</section>
<!-- /.content -->

