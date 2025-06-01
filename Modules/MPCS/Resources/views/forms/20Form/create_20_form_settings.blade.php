<style>
    .single-cat{
        padding: 5px 20px;
        background-color: orange;
        margin-bottom: 4px;
        min-width: max-content;
        text-align: center;
    }
</style>

<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        {!! Form::open(['url' => '#', 'method' => 'post', 'id' => 'add_21c_form_settings' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Add 20 Form Settings</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12"><br />
                <div class="row">
                <!-- Date and Time -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Opening Date</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="datepicker" name="date" data-date-format="yyyy/mm/dd" readonly />
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

                <!-- Form Starting Number -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Form Type <span class="" aria-required="true"></span></label>
                        <label>@lang('mpcs::lang.form_starting_number') .<span class="required" aria-required="true"></span></label>
                        <input type="hidden" name="starting_number" class="form-control" value="00">
                    </div>
                </div>

                <!-- Ref Previous Form Number -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Total Sale <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="total_sale" class="form-control" required>
                    </div>
                </div>

                <!-- Receipt Section Previous Day Amount -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Cash Sale <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="cash_sale" class="form-control" required>
                    </div>
                </div>

                <!-- Receipt Section Opening Stock Amount -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Credit Sale <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="credit_sale" class="form-control" required>
                    </div>
                </div>


                <div class="col-md-12">
                    <h4 class="text-center">Select which Product sub categories to show details, by clicking below buttons.
                    Selected buttons will change the colour to green colour</h4>
                    <div id="category-list">
                        @foreach ($categories as $id => $category)
                        <div class="col-md-3">
                            <div class="category single-cat" data-id="{{ $id }}">{{ $category }}</div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="selected_categories" id="selected_categories">

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

if (typeof hiddenCategories === 'undefined') {
    var hiddenCategories = new Set();
}

$(document).ready( function(){
    $('#datepicker').datepicker('setDate',new Date());

     // Get current time in HH:MM format
     let now = new Date();
        let hours = String(now.getHours()).padStart(2, "0");
        let minutes = String(now.getMinutes()).padStart(2, "0");
        let currentTime = `${hours}:${minutes}`;

        // Set the value of the time input field
        document.getElementById("time").value = currentTime;
});
  

$('#add_new_pump_row').on('click', function(){
    let lastRow = $('#pump_last_meter tbody tr:last'); // Get the last row
    let categorySelect = lastRow.find('.category_select'); 
    let pumpSelect = lastRow.find('.pump_select');
    
    let category = categorySelect.val();
    let pump = pumpSelect.val();
    let meter = lastRow.find('.meter').val();

    if (!category || !pump || !meter) {
        toastr.error('Please Enter All the Fields');
        return;
    }

    $.ajax({
        method: 'POST',
        url: '/mpcs/add-newpump-row',
        dataType: 'json',
        success: function(result) {
            let newRow = $(result.html); // Store new row
            $('#pump_last_meter tbody').append(newRow);

            let newCategorySelect = newRow.find('.category_select'); 
            let newPumpSelect = newRow.find('.pump_select'); 

            // **Check if only one pump option remains in the last row**
            if (pumpSelect.find('option').length <= 2) { 
                hiddenCategories.add(category); // Add to global hidden list
            }

            // **Remove already hidden categories from the new row**
            hiddenCategories.forEach(function(cat) {
                newCategorySelect.find('option[value="' + cat + '"]').remove();
            });
        },
    });
});

    // Remove row functionality
$(document).on('click', '.remove_row', function() {
    $(this).closest('tr').remove();
});

$(document).off('change', '.category_select', function () {
    loadPump(this);
});

$(document).on('submit', 'form#add_21c_form_settings', function(e) {
    e.preventDefault();

    // Disable the submit button to prevent multiple clicks
    $(this).find('button[type="submit"]').attr('disabled', true);

    // Serialize the form data
    var data = $(this).serializeArray();

    // Send the form data via AJAX
    $.ajax({
        method: $(this).attr('method'),
        url: '/mpcs/store-20-form-setting',  // URL to send the data
        dataType: 'json',
        data: data,
        success: function(result) {
            if (result.success === true) {
                toastr.success(result.msg);
                $('div#form_16_a_settings_modal').modal('hide');
            } else {
                toastr.success(result.msg);
                $('#form_21c_settings_table').DataTable().ajax.reload(null, false);
                $('div#form_16_a_settings_modal').modal('hide');
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

    $('.category').click(function () {
        let categoryId = $(this).data('id');
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected').css('background-color', 'orange');
            selectedCategories = selectedCategories.filter(id => id !== categoryId);
        } else {
            $(this).addClass('selected').css('background-color', 'green');
            selectedCategories.push(categoryId);
        }
        $('#selected_categories').val(selectedCategories.join(','));
    });
});
</script>
