@extends('layouts.app')
@section('title', __('patient.medication'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('patient.medication')</h1>
    <!-- Filters Section -->
    <div class="box box-info">
        <div class="box-header">
            <i class="fa fa-filter" aria-hidden="true"></i>
            <h3 class="box-title">Filters</h3>
        </div>
        <div class="box-body">
            {{ Form::open(['id' => 'filterForm']) }}
            <div class="row">
                <!-- Health Issue Filter -->
                <div class="col-md-3">
                    {!! Form::label('health_issue', __('patient.health_issue') . ':') !!}
                    {!! Form::select('health_issue', $health_issues, ($defaultVal) ? $defaultVal['health_issue'] : null, [
                        'placeholder' => __('report.all'),
                        'class' => 'form-control select2 filter-control',
                        'style' => 'width:100%',
                        'id' => 'health_issue'
                    ]) !!}
                </div>


                <!-- Medicine Name Filter -->
                <div class="col-md-3">
                    {!! Form::label('medicine_name', __('patient.medicine_name') . ':') !!}
                    {!! Form::select('medicine_name', $patient_medicines, ($defaultVal) ? $defaultVal['medicine_name'] : null, [
                        'placeholder' => __('report.all'),
                        'class' => 'form-control select2 filter-control',
                        'style' => 'width:100%',
                        'id' => 'medicine_name'
                    ]) !!}
                </div>

                <!-- Date Range Filter -->
                <div class="col-md-3">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', ($defaultVal) ? $defaultVal['startDate'].' - '.$defaultVal['endDate'] : date('m/01/Y').' - '.date('m/t/Y'), [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control filter-control',
                        'id' => 'date_range',
                        'readonly'
                    ]) !!}
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'patient.prescriptions_list')])

    @slot('tool')
    <div class="box-tools pull-right">
        <div class="box-tools">
          <button type="button" class="btn btn-primary" id="prescription_add" data-href="{{ route('medication.create') }}">
              <i class="fa fa-plus"></i> @lang('messages.add')
            </button>
        </div>
    </div>
    <hr>
    @endslot

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="prescriptions_table">
                <thead>
                    <tr>
                        <th>@lang('patient.prescriptions_records.date')</th>
                        <th>@lang('patient.prescriptions_records.diagnosed_on')</th>
                        <th>@lang('patient.prescriptions_records.health_issue')</th>
                        <th>@lang('patient.prescriptions_records.doctor_name')</th>
                        <th>@lang('patient.prescriptions_records.medicine_name')</th>
                        <th>@lang('patient.prescriptions_records.dose')</th>
                        <th>@lang('patient.prescriptions_records.frequency')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescriptions as $prescription)
                        <tr>
                            <td>{{ $prescription->created_at }}</td>
                            <td>{{ $prescription->diagnosed_date }}</td>
                            <td>{{ $prescription->diagnosis }}</td>
                            <td>{{ $prescription->doctor_name }}</td>
                            <td>{{ $prescription->medicine_name }}</td>
                            <td>{{ $prescription->amount }}</td>
                            <td>{{ $prescription->frequency }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endcomponent

    <div class="modal fade" id="prescription_modal" tabindex="-1" role="dialog"></div>
    <div class="modal fade edit_modal" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>

@endsection

@section('javascript')
<script>
    $(document).ready(function() {

        // Initialize Select2 for dropdowns
        $('.select2').select2();

        $('input#date_range').daterangepicker(
            dateRangeSettings
        );
 $('.filter-control').on('change', function() {
        // Log the element that triggered the change
        console.log("Changed element:", this);
        console.log("Changed value:", $(this).val());

        // Submit the form after logging the change
        $('#filterForm').submit();
    });

        $('#prescriptions_table').DataTable({
           "order": [[0, "desc"]]  
        });


        // Open modal to add a new prescription
        $(document).on('click', '#prescription_add', function () {
            var url = $(this).data('href');

            $.ajax({
                method: 'GET',
                dataType: 'html',
                url: url,
                success: function (response) {
                    $("#prescription_modal").html(response).modal('show');
                },
                error: function () {
                    alert('Failed to load the form. Please try again.');
                }
            });
        });
    });
</script>
@endsection
