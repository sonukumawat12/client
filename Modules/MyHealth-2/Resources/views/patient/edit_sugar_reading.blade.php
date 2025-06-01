<div class="modal-dialog" role="document">
    <div class="modal-content">

        <style>
            .select2 {
                width: 100% !important;
            }
        </style>
        {!! Form::open(['url' => action('\Modules\MyHealth\Http\Controllers\SugerReadingController@update', $sugarReading->id), 'method' =>
        'post', 'id' => 'suggestion_form', 'enctype' => 'multipart/form-data' ])
        !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Update Sugar Reading</h4>
        </div>

     <div class="modal-body">
    <div class="col-md-6">
         <div class="form-group">
            {!! Form::label('sugar_reading_number', __('Reading Number')) !!}
            {!! Form::select('sugar_reading_number', [
                '1' => __('1'),
                '2' => __('2'),
                '3' => __('3')
            ], $number, [
                'class' => 'form-control select2',
                'required',
                'placeholder' => __('Please select'),
                'id' => 'sugar_reading_number'
            ]) !!}
        </div>
        <div class="form-group">
            {!! Form::hidden('sugar_reading_id', $sugarReading->id, ['id' => 'sugar_reading_id']) !!}
            {!! Form::label('sugar_reading', __('Reading Time')) !!}
            {!! Form::select('sugar_reading', [
                'breakfast' => __('Breakfast'),
                'lunch' => __('Lunch'),
                'dinner' => __('Dinner')
            ], $type, [
                'class' => 'form-control select2',
                'required',
                'placeholder' => __('Please select'),
                'id' => 'sugar_reading'
            ]) !!}
        </div>
        
        <div class="form-group">
            {!! Form::label('time', __('Time')) !!}
            <div class="row">
                <div class="col-md-6">
                    {!! Form::number('hour', $hour, [
                        'class' => 'form-control',
                        'placeholder' => 'HH',
                        'min' => 0,
                        'max' => 12,
                        'required'
                    ]) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::number('minute', $minute, [
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

        <div class="form-group">
            {!! Form::label('reading_value', __('Manual Reading')) !!}
            {!! Form::number('reading_value', $reading, [
                'class' => 'form-control',
                'placeholder' => __('Enter reading'),
                'required',
                'min' => 0,
                'step' => 'any' // Allows decimal input if needed
            ]) !!}
        </div>

        <div class="form-group">
            {!! Form::label('note', __('Note')) !!}
            {!! Form::textarea('note', $note, [
                'class' => 'form-control',
                'placeholder' => __('Enter note'),
                'rows' => 3
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

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
// document.getElementById('am_pm_toggle').addEventListener('change', function() {
//         this.value = this.checked ? 'PM' : 'AM';
//     });
      $(document).on('click', '#sugar_reading', function(){
   
            var readingType = $(this).val();
            var sugar_reading_id =  $('#sugar_reading_id').val();
            console.log(sugar_reading_id);
            // Perform AJAX request to fetch data
            $.ajax({
                  url : "{{action('\Modules\MyHealth\Http\Controllers\SugerReadingController@fetchData')}}",
                method: 'GET',
                data: { 
                    type: readingType,
                    sugar_reading_id:sugar_reading_id
                },
                success: function(response) {
                   console.log(response);
                    // Assuming response contains note, time, and reading_value
                    if (response) {
                        // Update the fields with the fetched data
                        $('#reading_value').val(response.reading_value);
                        $('#note').val(response.note);
                        
                        // Split time into hour and minute if time is in HH:MM:SS format
                        var timeParts = response.time.split(':');
                        if (timeParts.length === 3) {
                            $('input[name="hour"]').val(timeParts[0]);
                            $('input[name="minute"]').val(timeParts[1]);
                        }
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });
</script>