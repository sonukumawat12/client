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
            <h4 class="modal-title">Add Sugar Reading</h4>
        </div>

     <div class="modal-body">
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
        <div class="form-group">
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
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save_suggestion_btn">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
document.getElementById('am_pm_toggle').addEventListener('change', function() {
        this.value = this.checked ? 'PM' : 'AM';
    });
   $('#status').change(function(){
        if($(this).val() == 'assigned_to'){
            $('.assigned_to_member').removeClass('hide');
        }else{
            $('.assigned_to_member').addClass('hide');
        }
   })
</script>