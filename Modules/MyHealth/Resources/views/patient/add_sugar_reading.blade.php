<div class="modal-dialog" role="document">
    <div class="modal-content">
        <style>
            .select2 {
                width: 100% !important;
            }
        </style>

        {!! Form::open(['url' => action('\Modules\MyHealth\Http\Controllers\SugerReadingController@update', $sugarReading->id), 'method' =>
        'post', 'id' => 'suggestion_form', 'enctype' => 'multipart/form-data' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Add Sugar Reading</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <!-- Reading Number and Time on the same row -->
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('sugar_reading_number', __('Reading Number')) !!}
                        {!! Form::select('sugar_reading_number', [
                            '1' => __('1'),
                            '2' => __('2'),
                            '3' => __('3')
                        ], null, [
                            'class' => 'form-control select2',
                            'required',
                            'placeholder' => __('Please select'),
                            'id' => 'sugar_reading_number'
                        ]) !!}
                    </div>
                </div>
                 <!-- Time (Hour, Minute, AM/PM) -->
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('time', __('Time')) !!}
                        <div class="row">
                            <div class="col-md-6">
                                {!! Form::number('hour', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'HH',
                                    'min' => 0,
                                    'max' => 12,
                                    'required'
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! Form::number('minute', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'MM',
                                    'min' => 0,
                                    'max' => 59,
                                    'required'
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="radio" name="am_pm" value="AM" checked> AM
                            </label>
                            <label>
                                <input type="radio" name="am_pm" value="PM"> PM
                            </label>
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="row">
                 <!-- Medication For (Illness) Dropdown -->
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('health_issue', __('Medication For (Illness)')) !!}
          @if(!empty($health_issues) && count($health_issues) > 0)
    {!! Form::select('health_issue', $health_issues, null, [
        'class' => 'form-control select2',
        'id' => 'health_issue',
        'placeholder' => __('Select Health Issue')
    ]) !!}
@else
    <p>{{ __('No Health Issues Available') }}</p>
@endif

        </div>
    </div>
                 <!-- Medicine Dropdown -->
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('medicine_name', __('Medicine')) !!}
            {!! Form::select('medicine_name', [], null, [
                'class' => 'form-control select2',
                'id' => 'medicine_name',
                'placeholder' => __('Select Medicine')
            ]) !!}
        </div>
    </div>
            </div>

            <div class="row">
               <!-- Dose Field -->
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('dose', __('Dose')) !!}
            {!! Form::text('dose', null, [
                'class' => 'form-control',
                'id' => 'dose',
                'placeholder' => __('Dose will auto-load')
            ]) !!}
        </div>
    </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('note', __('Note')) !!}
                        {!! Form::textarea('note', null, [
                            'class' => 'form-control',
                            'placeholder' => __('Enter note'),
                            'rows' => 3
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
               
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group ">
                    {!! Form::label('sugar_reading', __('Reading Time')) !!}
                    {!! Form::select('sugar_reading', [
                        'breakfast' => __('Breakfast'),
                        'lunch' => __('Lunch'),
                        'dinner' => __('Dinner')
                    ], null, [
                        'class' => 'form-control select2',
                        'required',
                        'placeholder' => __('Please select'),
                        'id' => 'sugar_reading'
                    ]) !!}
                </div>
             </div>
            <div class='col-md-6'>
                <div class="form-group">
                    {!! Form::label('reading_value', __('Manual Reading')) !!}
                    {!! Form::number('reading_value', null, [
                        'class' => 'form-control',
                        'placeholder' => __('Enter reading'),
                        'required',
                        'min' => 0,
                        'step' => 'any' // Allows decimal input if needed
                    ]) !!}
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save_suggestion_btn">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>

<script>
    // Toggle AM/PM
        $(document).on('change', '#am_pm_toggle', function() {
            $(this).val($(this).is(':checked') ? 'PM' : 'AM');
        });

    // Show/hide member assignment section
    $('#status').change(function(){
        if($(this).val() == 'assigned_to'){
            $('.assigned_to_member').removeClass('hide');
        } else {
            $('.assigned_to_member').addClass('hide');
        }
    });
    
    
   $(document).on('change', '#health_issue', function() {
    var healthIssue = $(this).val();
    
    if (healthIssue) {
        $.ajax({
            url: "{{ route('get-medicines-for-health-issue') }}",
            type: "GET",
            data: {
                health_issue: healthIssue
            },
            success: function(data) {
                $('#medicine_name').empty(); // Clear previous options
                $('#medicine_name').append('<option value="">{{ __('Select Medicine') }}</option>');

                // Populate medicines dropdown
                $.each(data, function(id, medicine_name) {
                    $('#medicine_name').append('<option value="' + id + '">' + medicine_name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching medicines:", error); // Log any errors
            }
        });
    }
});


    // Auto-load dose based on selected medicine
    $('#medicine_name').change(function() {
        var medicineId = $(this).val();
        
        if (medicineId) {
            $.ajax({
                url: "{{ route('get-dose-for-medicine') }}", // Create this API route to get the dose for the selected medicine
                type: "GET",
                data: {
                    medicine_id: medicineId
                },
                success: function(data) {
                    $('#dose').val(data.dose); // Auto-load the dose value
                }
            });
        }
    });
</script>

