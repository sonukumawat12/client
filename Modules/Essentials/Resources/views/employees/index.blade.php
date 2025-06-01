@extends('layouts.app')
@section('title', __('essentials::lang.employees'))

@section('content')
@include('essentials::layouts.nav_hrm')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('essentials::lang.list_employees')</h4>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Main content -->
<section class="content" style="padding-top: 0px !important">
    <div class="row">
        <div class="col-md-12">
        @component('components.filters', ['title' => __('essentials::lang.departments'), 'class' => 'box-solid'])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('department',  __('essentials::lang.departments') . ':') !!}
                    {!! Form::select('department', $departments, null, ['class' => 'form-control department_filter select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('employees_employee_no',  __('essentials::lang.employee_no') . ':') !!}
                    {!! Form::select('employees_employee_no', $employee_nos, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('designation',  __('essentials::lang.designations') . ':') !!}
                    {!! Form::select('designation', $designations, null, ['class' => 'form-control select2 designation_filter', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('employees_added_on', __('essentials::lang.added_on') . ':') !!}
                    {!! Form::text('employees_added_on', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('employees_date_joined', __('essentials::lang.date_joined') . ':') !!}
                        {!! Form::text('employees_date_joined', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('employees_probation_ends', __('essentials::lang.probation_ends') . ':') !!}
                        {!! Form::text('employees_probation_ends', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
            
        @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-12">
			<div class="box-tools">
				<button type="button" class="btn pull-right btn-primary btn-modal" data-href="{{action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'create'])}}" data-container="#add_holiday_modal">
					<i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
			</div>
		</div>
	</div>
	<div class="row">
    	<div class="col-12">
		    @component('components.widget', ['class' => 'box-solid'])
                
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="employees_table">
                        <thead>
                            <tr>
                                <th>@lang( 'messages.action' )</th>
                                <th>@lang( 'essentials::lang.date_joined' )</th>
                                <th>@lang( 'essentials::lang.employee_name' )</th>
                                <th>@lang( 'essentials::lang.employee_no' )</th>
                                <th>@lang( 'category.nic' )</th>
                                <th>@lang('lang_v1.dob')</th>
                                <th>@lang('lang_v1.address')</th>
                                <th>@lang( 'essentials::lang.designation' )</th>
                                <th>@lang( 'essentials::lang.department' )</th>
                                <th>@lang( 'essentials::lang.current_salary' )</th>
                                <th>@lang( 'essentials::lang.probation_ends' )</th>
                                <th>@lang( 'essentials::lang.added_by' )</th>
                                
                               
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade" id="add_holiday_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
</div>
        
<div class="modal fade" id="noteModal" role="dialog" 
aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">@lang( 'lang_v1.note' )</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <p id="noteContent" class="text-center text-bold"></p>
        </div>

      </div>
    </div>
  </div>

@endsection
<input type="hidden" id="date_filter_changed" value="no">
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
            $(document).on('click', '.note_btn', function(e){
                e.preventDefault();
              let note = $(this).data('string');
              $("#noteContent").html(note);
              $("#noteModal").modal('show');
               
            });
            
            // $('.department_filter').change(function(){
            //   var cat = $('.department_filter').val();
            //   $.ajax({
            //     method: 'POST',
            //     url: '/products/get_sub_categories',
            //     dataType: 'html',
            //     data: { cat_id: cat },
            //     success: function(result) {
            //         console.log(result);
            //       if (result) {
            //         $('.designation_filter').html(result);
            //       }
            //     },
            //   });
            // });
            //
            // $(document).on( 'change', '.department_input', function() {
            //   var cat = $('.department_input').val();
            //   $.ajax({
            //     method: 'POST',
            //     url: '/products/get_sub_categories',
            //     dataType: 'html',
            //     data: { cat_id: cat },
            //     success: function(result) {
            //         console.log(result);
            //       if (result) {
            //         $('.designation_input').html(result);
            //       }
            //     },
            //   });
            // });
            
            
            
            
            
            employees_table = $('#employees_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'index'])}}",
                    "data" : function(d) {
                        d.department = $('#department').val();
                        d.employees_employee_no = $('#employees_employee_no').val();
                        d.designation = $('#esignation').val();
                        
                        if($('#employees_added_on').val() && $('#date_filter_changed').val() == 'yes') {
                            var start = $('#employees_added_on').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#employees_added_on').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.added_start_date = start;
                            d.added_end_date = end;
                        }
                        
                        if($('#employees_probation_ends').val() && $('#date_filter_changed').val() == 'yes') {
                            var start = $('#employees_probation_ends').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#employees_probation_ends').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.probation_start_date = start;
                            d.probation_end_date = end;
                        }
                        
                        if($('#employees_date_joined').val() && $('#date_filter_changed').val() == 'yes') {
                            var start = $('#employees_date_joined').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#employees_date_joined').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.joined_start_date = start;
                            d.joined_end_date = end;
                        }
                    }
                },
                
                columns: [
                    { data: 'action', name: 'action', searchable: false },
                    { data: 'date_joined', name: 'date_joined', searchable: false },
                    { data: 'name', name: 'name'},
                    { data: 'employee_no', name: 'employee_no' },
                    { data: 'nic', name: 'nic' },
                    { data: 'dob', name: 'dob' },
                    { data: 'address', name: 'address' },
                    { data: 'designation', name: 'designation', searchable: false},
                    { data: 'department', name: 'department', searchable: false},
                    { data: 'salary', name: 'salary'},
                    { data: 'probation_ends', name: 'probation_ends', searchable: false},
                    { data: 'username', name: 'username', searchable: false},
                    
                    
                ],
            });

            $('#employees_added_on').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#employees_added_on').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                }
            );
            $('#custom_date_apply_button').on('click', function() {
                if($('#target_custom_date_input').val() == "employees_added_on"){
                    let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                    let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();
    
                    if (startDate.length === 10 && endDate.length === 10) {
                        let formattedStartDate = moment(startDate).format(moment_date_format);
                        let formattedEndDate = moment(endDate).format(moment_date_format);
    
                        $('#employees_added_on').val(
                            formattedStartDate + ' ~ ' + formattedEndDate
                        );
    
                        $('#employees_added_on').data('daterangepicker').setStartDate(moment(startDate));
                        $('#employees_added_on').data('daterangepicker').setEndDate(moment(endDate));
    
                        $('.custom_date_typing_modal').modal('hide');
                    } else {
                        alert("Please select both start and end dates.");
                    }
                }
            });
            $('#employees_added_on').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('#target_custom_date_input').val('employees_added_on');
                    $('.custom_date_typing_modal').modal('show');
                }
            });
            $('#employees_added_on').on('cancel.daterangepicker', function(ev, picker) {
                $('#employees_added_on').val('');
                employees_table.ajax.reload();
            });
            
            
            $('#employees_probation_ends').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#employees_probation_ends').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                }
            );
            $('#custom_date_apply_button').on('click', function() {
                if($('#target_custom_date_input').val() == "employees_probation_ends"){
                    let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                    let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();
    
                    if (startDate.length === 10 && endDate.length === 10) {
                        let formattedStartDate = moment(startDate).format(moment_date_format);
                        let formattedEndDate = moment(endDate).format(moment_date_format);
    
                        $('#employees_probation_ends').val(
                            formattedStartDate + ' ~ ' + formattedEndDate
                        );
    
                        $('#employees_probation_ends').data('daterangepicker').setStartDate(moment(startDate));
                        $('#employees_probation_ends').data('daterangepicker').setEndDate(moment(endDate));
    
                        $('.custom_date_typing_modal').modal('hide');
                    } else {
                        alert("Please select both start and end dates.");
                    }
                }
            });
            $('#employees_probation_ends').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('#target_custom_date_input').val('employees_probation_ends');
                    $('.custom_date_typing_modal').modal('show');
                }
            });
            $('#employees_probation_ends').on('cancel.daterangepicker', function(ev, picker) {
                $('#employees_probation_ends').val('');
                employees_table.ajax.reload();
            });
            
            
            $('#employees_date_joined').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#employees_date_joined').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                }
            );
            $('#custom_date_apply_button').on('click', function() {
                if($('#target_custom_date_input').val() == "employees_date_joined"){
                    let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                    let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();
    
                    if (startDate.length === 10 && endDate.length === 10) {
                        let formattedStartDate = moment(startDate).format(moment_date_format);
                        let formattedEndDate = moment(endDate).format(moment_date_format);
    
                        $('#employees_date_joined').val(
                            formattedStartDate + ' ~ ' + formattedEndDate
                        );
    
                        $('#employees_date_joined').data('daterangepicker').setStartDate(moment(startDate));
                        $('#employees_date_joined').data('daterangepicker').setEndDate(moment(endDate));
    
                        $('.custom_date_typing_modal').modal('hide');
                    } else {
                        alert("Please select both start and end dates.");
                    }
                }
            });
            $('#employees_date_joined').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('#target_custom_date_input').val('employees_date_joined');
                    $('.custom_date_typing_modal').modal('show');
                }
            });
            $('#employees_date_joined').on('cancel.daterangepicker', function(ev, picker) {
                $('#employees_date_joined').val('');
                employees_table.ajax.reload();
            });
            
            
            $('#employees_probation_ends').data('daterangepicker').setStartDate(moment().startOf('month'));
        	$('#employees_probation_ends').data('daterangepicker').setEndDate(moment().endOf('month'));
        	
        	$('#employees_date_joined').data('daterangepicker').setStartDate(moment().startOf('month'));
        	$('#employees_date_joined').data('daterangepicker').setEndDate(moment().endOf('month'));
        	
        	$('#employees_added_on').data('daterangepicker').setStartDate(moment().startOf('month'));
        	$('#employees_added_on').data('daterangepicker').setEndDate(moment().endOf('month'));
            
            
            

            $(document).on( 'change', '#employees_added_on, #employees_probation_ends,#employees_date_joined, #department,#employees_employee_no, #designation', function() {
                employees_table.ajax.reload();
            });
            $(document).on( 'change', '#employees_added_on, #employees_probation_ends,#employees_date_joined', function() {
                $('#date_filter_changed').val('yes');
            });

            $('#add_holiday_modal').on('shown.bs.modal', function(e) {
                $('#add_holiday_modal .select2').select2();
            });

            $(document).on('submit', 'form#add_employee_form', function(e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();

                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            
                            toastr.success(result.msg);
                            employees_table.ajax.reload();
                            
                            if ($('#salary_history_table').length > 0) {
                                salary_history_table.ajax.reload();
                                $(this).find('button[type="submit"]').attr('disabled', false);
                            }else{
                                $('div#add_holiday_modal').modal('hide');
                            }
                            
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
            $(document).on('submit', 'form#add_earning_form', function(e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();

                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            
                            toastr.success(result.msg);
                            employees_table.ajax.reload();
                            
                            if ($('#salary_history_table').length > 0) {
                                salary_history_table.ajax.reload();
                                $(this).find('button[type="submit"]').attr('disabled', false);
                            }else{
                                $('div#add_holiday_modal').modal('hide');
                            }
                            
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });

        $(document).on('click', 'button.delete-holiday', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                employees_table.ajax.reload();
                                
                                if ($('#salary_history_table').length > 0) {
                                    salary_history_table.ajax.reload();
                                    $(this).find('button[type="submit"]').attr('disabled', false);
                                }
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });


    </script>
 <script>
$(document).ready(function () {
    // Function to calculate the probation end date
    window.calculateProbationEnd = function () {
        var probationPeriod = $('#probation_period').val(); // Get the probation period value
        var dateJoined = $('#date_joined').val(); // Get the date joined value

        // Debugging console logs
        console.log("Probation Period: ", probationPeriod);
        console.log("Date Joined: ", dateJoined);
        if (!$('#department_select').val() || !$('#designation_select').val()){
            toastr.error("Select department and designation");
            return;
        }

        // Check if both fields have values
        if (probationPeriod && dateJoined) {
            // Convert dateJoined to a JavaScript Date object
            var startDate = new Date(dateJoined);

            $.ajax({
                url: '{{ route('hrm.get.probation_duration') }}',
                type: 'POST',
                data: {
                    department_id: $('#department_select').val(),
                    designation_id: $('#designation_select').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function (result) {
                    if (result.success == true) {
                        if(result.duration == "days"){
                            // Add the probation period (in days) to the start date
                            startDate.setDate(startDate.getDate() + parseInt(probationPeriod));
                            // Format the probation end date as YYYY-MM-DD
                            var probationEnds = startDate.toISOString().split('T')[0];
                            // Set the calculated probation end date in the corresponding field
                            $('#probation_ends').val(probationEnds);
                            // Display the probation period value (in days) in the field
                            $('#probation_period_value').val(probationPeriod + ' days');
                        } else {
                            // Add the probation period (in months) to the start date
                            startDate.setMonth(startDate.getMonth() + parseInt(probationPeriod));
                            // Format the probation end date as YYYY-MM-DD
                            var probationEnds = startDate.toISOString().split('T')[0];
                            // Set the calculated probation end date in the corresponding field
                            $('#probation_ends').val(probationEnds);
                            // Display the probation period value (in months) in the field
                            $('#probation_period_value').val(probationPeriod + ' months');
                        }
                    } else {
                        toastr.error(result.msg);
                        // Default to months?
                        // Add the probation period (in months) to the start date
                        startDate.setMonth(startDate.getMonth() + parseInt(probationPeriod));
                        // Format the probation end date as YYYY-MM-DD
                        var probationEnds = startDate.toISOString().split('T')[0];
                        // Set the calculated probation end date in the corresponding field
                        $('#probation_ends').val(probationEnds);
                        // Display the probation period value (in months) in the field
                        $('#probation_period_value').val(probationPeriod + ' months');
                    }
                }
            });
        } else {
            // Clear the fields if the required values are missing
            $('#probation_ends').val('');
            $('#probation_period_value').val('');
        }
    };

    // Trigger the calculation function on page load (if necessary)
    $('#date_joined, #probation_period').trigger('blur');
});


</script>

    <script>
        $(document).ready(function () {
            $('.department_filter').change(function () {
                var departmentId = $(this).val();

                if (departmentId) {
                    $.ajax({
                        url: '{{ route('hrm.get.designations') }}',
                        type: 'POST',
                        data: {
                            department_id: departmentId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (data) {
                            $('.designation_filter').empty().append('<option value="">{{ __("essentials::lang.select_designation") }}</option>');
                            $.each(data, function (key, value) {
                                $('.designation_filter').append('<option value="' + key + '">' + value + '</option>');
                            });
                        }
                    });
                } else {
                    $('#designation_filter').empty().append('<option value="">{{ __("essentials::lang.select_designation") }}</option>');
                }
            });
        });
    </script>


@endsection
