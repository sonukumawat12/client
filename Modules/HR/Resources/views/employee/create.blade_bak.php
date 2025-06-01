<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\HR\Http\Controllers\EmployeeController@store'), 'method' => 'post', 'id' => 'add_employee_form', 'files' => true ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'hr::lang.add_employee' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12"><br />

                <!-- Department Field -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.department') <span class="required" aria-required="true">*</span></label>
                        {!! Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select'), 'id' => 'department_id']) !!}
                    </div>
                </div>

                <!-- Designation Field -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.designation') <span class="required" aria-required="true">*</span></label>
                        {!! Form::select('designation_id', [], null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select'), 'id' => 'designation_id']) !!}
                    </div>
                </div>

                <!-- Probation Period -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.probation_period')</label>
                        <input type="text" name="probation_period" class="form-control" id="probation_period" readonly>
                    </div>
                </div>

                <!-- Probation Ends -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.probation_ends')</label>
                        <input type="text" name="probation_ends" class="form-control" id="probation_ends" readonly>
                    </div>
                </div>

                <!-- Employee Number -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.employee_number') <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="employee_number" class="form-control" value="{{$employee_number}}" readonly>
                    </div>
                </div>

                <!-- Business Location -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.business_location') <span class="required" aria-required="true">*</span></label>
                        <select name="business_location" id="business_location" class="form-control select2">
                            <option value="">Please Select</option>
                            @foreach ($locations as $location)
                                <option value="{{$location->id}}">{{$location->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- First Name -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.first_name') <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="first_name" class="form-control">
                    </div>
                </div>

                <!-- Last Name -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('hr::lang.last_name') <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="last_name" class="form-control">
                    </div>
                </div>

                <!-- Date of Birth -->
                <div class="col-md-6">
                    <div class="form-group form-group-bottom">
                        <label>@lang('hr::lang.date_of_birth') <span class="required" aria-required="true">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="datepicker" name="date_of_birth" data-date-format="yyyy/mm/dd">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar-o"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other fields remain unchanged -->

            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#datepicker').datepicker();

    // Populate Designations based on Department
    $('#department_id').change(function() {
        var departmentId = $(this).val();
        $.ajax({
            url: '/get-designations/' + departmentId, // Adjust this route as per your backend
            method: 'GET',
            success: function(response) {
                $('#designation_id').empty().append('<option value="">@lang("lang_v1.please_select")</option>');
                $.each(response, function(key, value) {
                    $('#designation_id').append('<option value="' + key + '">' + value + '</option>');
                });
            }
        });
    });

    // Calculate Probation Ends based on Date Joined and Probation Period
    $('#probation_period').change(function() {
        var dateJoined = $('#date_joined').val(); // Ensure Date Joined field exists
        var probationPeriod = $(this).val();
        if (dateJoined && probationPeriod) {
            var probationEnds = new Date(dateJoined);
            probationEnds.setMonth(probationEnds.getMonth() + parseInt(probationPeriod));
            $('#probation_ends').val(probationEnds.toISOString().split('T')[0]);
        }
    });
</script>
