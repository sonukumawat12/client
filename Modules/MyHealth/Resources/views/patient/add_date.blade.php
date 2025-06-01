<style>
    .feild-box {
        border: 1px solid #8080803b;
        margin-top: 10px;
        padding: 10px;
    }
    .mb-5 {
        margin-bottom: 5px;
    }
    fieldset {
        margin-top: -15px;
    }
    .p-0 {
        padding: 0px 5px !important;
    }
    .field-inline-block {
        display: inline-flex;
    }
    .l-date {
        padding: 0px;
        margin: 0px;
        font-size: 10px;
        font-weight: 500;
    }
    .date-field {
        margin-right: 2px;
        padding: 0px 3px;
        text-align: center !important;
        height: 54px;  /* Doubled the height */
        width: 80px;   /* Doubled the width */
    }
    .col {
        margin-left: 10px;
    }
</style>

<div  id= "add_new_date" class="modal-dialog" role="document" style="width: 35%">
    <div class="modal-content">
        <style>
            .select2 {
                width: 100% !important;
            }
        </style>
        {!! Form::open(['url' => action('\Modules\MyHealth\Http\Controllers\SugerReadingController@store'), 'method' => 'post', 'id' => 'suggestion_form', 'enctype' => 'multipart/form-data' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Sugar Reading: Date / Month / Year</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                @php
                    $date_field_name = 'new';
                    $data_field = [];
                    $today = \Carbon\Carbon::now();
                    // Get today's year, month, and day
                    $year = $today->year;
                    $month = $today->month;
                    $day = $today->day;
                    // Split year into individual digits
                    $yearDigits = str_split($year);
                    // Split month and day into two digits
                    $monthDigits = str_split(str_pad($month, 2, '0', STR_PAD_LEFT));
                    $dayDigits = str_split(str_pad($day, 2, '0', STR_PAD_LEFT));
                @endphp

                <fieldset>
                    <div class="row">
                        <div class="col-md-12 p-0">
                            <div class="col-md-3 p-0">
                                <label class="text-center">Date</label>
                                <div class="field-inline-block w-100 text-center">
                                    <input type="text" pattern="[0-9]*" maxlength="1" class="date-field form-control d-inline-block" placeholder="D" name="date1" value="{{ $dayDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                    <input type="text" pattern="[0-9]*" maxlength="1" class="date-field form-control d-inline-block" placeholder="D" name="date2" value="{{ $dayDigits[1] ?? '' }}" style="width: 40px; display: inline-block;">
                                </div>
                            </div>

                            <div class="col-md-3 p-0">
                                <label class="text-center">Month</label>
                                <div class="field-inline-block w-100 text-center">
                                    <input type="text" pattern="[0-9]*" maxlength="1" class="date-field form-control d-inline-block" placeholder="M" name="month1" value="{{ $monthDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                    <input type="text" pattern="[0-9]*" maxlength="1" class="date-field form-control d-inline-block" placeholder="M" name="month2" value="{{ $monthDigits[1] ?? '' }}" style="width: 40px; display: inline-block;">
                                </div>
                            </div>

                            <div class="col-md-6 p-0">
                                <label class="text-center">Year</label>
                                <div class="field-inline-block w-100 text-center">
                                    <input type="text" pattern="[0-9]*" maxlength="1" class="date-field form-control d-inline" placeholder="Y" name="year1" value="{{ $yearDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                    <input type="text" pattern="[0-9]*" maxlength="1" class="date-field form-control d-inline" placeholder="Y" name="year2" value="{{ $yearDigits[1] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                    <input type="text" pattern="[0-9]*" maxlength="1" class="date-field form-control d-inline" placeholder="Y" name="year3" value="{{ $yearDigits[2] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                    <input type="text" pattern="[0-9]*" maxlength="1" class="date-field form-control d-inline" placeholder="Y" name="year4" value="{{ $yearDigits[3] ?? '' }}" style="width: 40px; display: inline-block;">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            <button type="submit" class="btn btn-primary" id="save_member_btn">@lang('messages.save')</button>
        </div>

        {!! Form::close() !!}
    </div> 
</div>

<script>
    document.querySelectorAll('.date-field').forEach((input, index) => {
        input.addEventListener('input', function() {
            if (this.value.length >= this.maxLength) {
                // Move focus to the next input
                const nextInput = document.querySelectorAll('.date-field')[index + 1];
                if (nextInput) {
                    nextInput.focus();
                }
            }
        });
    });
</script>
