<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\FormsSettingController@store16aFormSetting'), 'method' => 'post', 'id' => 'add_16a_form_settings',    'onsubmit' => 'return false;' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.add_16_a_form_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12"><br />

                <!-- Date and Time -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.date')</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="datepicker" name="datepicker" data-date-format="yyyy/mm/dd" >
                            <!--<div class="input-group-addon">-->
                            <!--    <i class="fa fa-calendar-o"></i>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>

                  <!-- Date and Time -->
                  <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.time')</label>
                        <div class="input-group">
                            <input class="form-control timepicker" id="time" name="time" type="time" value="12:00" >
                            <!--<div class="input-group-addon">-->
                            <!--    <i class="fa fa-clock-o" aria-hidden="true"></i>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>

                <!-- Form Starting Number -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.form_starting_number') <span class="required" aria-required="true">*</span></label>
                        <input type="number" name="starting_number" value="{{$startingNumber}}" class="form-control" required>
                    </div>
                </div>
                
                
                <!-- No of rows to show per page -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.no_of_rows_to_show_per_page') <span class="required" aria-required="true">*</span></label>
                        <input type="number" name="no_of_rows_per_page" class="form-control" required>
                    </div>
                </div>

                <!-- Ref Previous Form Number -->
                <!--<div class="col-md-6">-->
                <!--    <div class="form-group">-->
                <!--        <label>@lang('mpcs::lang.ref_previous_form_number') <span class="required" aria-required="true">*</span></label>-->
                <!--        <input type="text" name="ref_pre_form_number" class="form-control" required>-->
                <!--    </div>-->
                <!--</div>-->

                <!-- Total Sale up to Previous Day -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.total_previous_total_purchase_with_vat') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="total_purchase_price_with_vat" name="total_purchase_price_with_vat" class="form-control" required>
                    </div>
                </div>

                <!-- Previous Day Cash Sale -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.total_previous_total_sale_with_vat') <span class="required" aria-required="true">*</span></label>
                        <input type="number" step="0.01" id="total_sale_price_with_vat" name="total_sale_price_with_vat" class="form-control" required>
                    </div>
                </div>
                
                <!-- Other fields remain unchanged -->

            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="add_16a_form_settings_submit_button">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>

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
   
//   $('#add_16a_form_settings').on('submit', function(e) {
//         e.preventDefault(); // Prevent default form submission

//         $.ajax({
//             url: $(this).attr('action'),
//             method: 'POST',
//             data: $(this).serialize(),
//             success: function(response) {
//                 // Close the modal
//                 $('#add_16a_form_settings').closest('.modal').modal('hide');

//                 // Refresh the page and activate the tab
//                 window.location.href = window.location.pathname + '#16a_form_list_tab';
//                 window.location.reload(); // Force reload to ensure tab activation
//             },
//             error: function(xhr) {
//                 // Handle errors (e.g., validation)
//                 toastr.error(xhr.responseJSON.message || 'Error saving data.');
//             }
//         });
//     });
    
   $('#add_16a_form_settings_submit_button').on('click', function(e) {
        e.preventDefault(); // Prevent default action of the button (if any)
    
        var form = $('#add_16a_form_settings'); // Get the form element
    //  console.log(form,'form tabLink');
        $.ajax({
            url: form.attr('action'), // Get the form's action URL
            method: form.attr('method'), // Get the form's method (e.g., POST)
            data: form.serialize(), // Serialize the form data
            success: function(response) {
                // Close the modal
                form.closest('.modal').modal('hide');
    
                // Refresh the page and activate the tab
                window.location.href = window.location.pathname + '#16a_form_list_tab';
                window.location.reload(); // Force reload to ensure tab activation
            },
            error: function(xhr) {
                // Handle errors (e.g., validation)
                toastr.error(xhr.responseJSON.message || 'Error saving data.');
            }
        });
    });
    // Activate tab if URL has a hash (e.g., #16a_form_tab)
    if (window.location.hash) {
        const tabId = window.location.hash;
        $('.nav-tabs a[href="' + tabId + '"]').tab('show');
    }
 
</script>
