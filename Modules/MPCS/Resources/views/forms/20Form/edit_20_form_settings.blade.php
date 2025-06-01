<style>
    .single-cat{
        padding: 5px 20px;
        background-color: orange;
        margin-bottom: 4px;
        min-width: max-content;
        text-align: center;
    }
    .selected{
        padding: 5px 20px;
        background-color: green;
        margin-bottom: 4px;
    }
</style>
<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
    {!! Form::open(['url' => action([\Modules\MPCS\Http\Controllers\F20FormController::class, 'mpcs20Update'], [$settings->id]), 'method' => 'post', 'id' => 'update_21c_form_settings' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit 20 Form Settings</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12"><br />

                <div class="row">
                <!-- Date and Time -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Opening Date</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="datepicker" name="date" data-date-format="yyyy/mm/dd" readonly value="{{$settings->opening_date}}"/>
                            <div class="input-group-addon">
                                <i class="fa fa-calendar-o"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Date and Time -->
                <div class="col-md-6">
                    
                </div>
                </div>
                <br>

                <!-- Form Starting Number -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Form Type <span class="" aria-required="true"></span></label>
                        <label>@lang('mpcs::lang.form_starting_number') .<span class="required" aria-required="true"></span></label>
                        <input type="hidden" name="starting_number" class="form-control" value="{{ $settings->starting_number }}" required>
                    </div>
                </div>

                <!-- Ref Previous Form Number -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Total Sale <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="total_sale" class="form-control" value="{{ $settings->total_sale }}" required>
                    </div>
                </div>

                <!-- Receipt Section Previous Day Amount -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Cash Sale <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="cash_sale" class="form-control" value="{{ $settings->cash_sale }}" required>
                    </div>
                </div>

                <!-- Receipt Section Opening Stock Amount -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Credit Sale <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="credit_sale" class="form-control" value="{{ $settings->credit_sale }}" required>
                    </div>
                </div>


                <div class="col-md-12">
                    <h4 class="text-center">Select which Product sub categories to show details, by clicking below buttons.
                    Selected buttons will change the colour to green colour</h4>
                    <div id="category-list">
                        @foreach ($categories as $id => $category)
                        <div class="col-md-3">
                            <div class="category single-cat @if(in_array($id, $categoryId)) selected @endif" data-id="{{ $id }}">{{ $category }}</div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="selected_categories" id="selected_categories" value="{{ $settings->category }}">
                    @foreach($categoryId as $cat)
                    <input type="hidden" name="cat[]" value="{{$cat}}"> 
                    @endforeach

                </div>
                <!-- Other fields remain unchanged -->

            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" id="close_21c_modal" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>


$(document).on('submit', 'form#update_21c_form_settings', function(e) {
    e.preventDefault();

    // Disable the submit button to prevent multiple clicks
    $(this).find('button[type="submit"]').attr('disabled', true);

    // Serialize the form data
    var data = $(this).serializeArray();

    // Send the form data via AJAX
    $.ajax({
        method: $(this).attr('method'),
        url:  $(this).attr('action'),
        dataType: 'json', // URL to send the data
        data: data,
        success: function(result) {
            if (result.success === true) {
                toastr.success(result.msg);
                $('#form_21c_settings_table').DataTable().ajax.reload(null, false);
                $('div#update_form_16_a_settings_modal').modal('hide');
            } else {
                toastr.success(result.msg);
                $('#form_21c_settings_table').DataTable().ajax.reload(null, false);
                $('div#update_form_16_a_settings_modal').modal('hide');
            }
        },
        error: function() {
            toastr.error('An error occurred while submitting the form.');
        },
        complete: function() {
            $(this).find('button[type="submit"]').attr('disabled', false);
        }
    });
});

$(document).ready(function () {
    let selectedCategories = [];
    let inputs = document.querySelectorAll('input[name="cat[]"]');
    let values = Array.from(inputs).map(input => parseInt(input.value));
    if(values){
        selectedCategories = values;
    }

    $('.category').click(function () {
        let categoryId = $(this).data('id');
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected').css('background-color', 'orange');
            selectedCategories = selectedCategories.filter(id => id !== categoryId);
        } else {
            $(this).addClass('selected').css('background-color', 'green');
            selectedCategories.push(categoryId);
        }
        console.log(selectedCategories);
        $('#selected_categories').val(selectedCategories.join(','));
    });
});

</script>
