<style>
.full-width-input {
    width: 100% !important; 
    box-sizing: border-box; 
    display: block; 
    padding: 5px; 
    margin: 0;
    border: 1px solid #fff;
    height: 100%; 
}
</style>
<div class="modal-dialog" role="document" style="width: 85%;">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\F21FormController@store21cFormSettings'), 'method' => 'post', 'id' => 'add_21c_form_settings' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'mpcs::lang.add_21_c_form_settings' )</h4>
        </div>

               

                <!-- Date and Time -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.opening_date')</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="datepicker" name="datepicker" data-date-format="yyyy/mm/dd" readonly />
                            <div class="input-group-addon">
                                <i class="fa fa-calendar-o"></i>
                            </div>
                        </div>
                    </div>
                </div>

                  <!-- Date and Time -->
                  <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.time')</label>
                        <div class="input-group">
                            <input class="form-control timepicker" id="time" name="time" type="time" value="12:00" readonly>
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Starting Number -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.form_starting_number') <span class="required" aria-required="true">*</span></label>
                        <input type="text" name="starting_number" class="form-control" value={{$starting_number}} required>
                    </div>
                </div>
                <!-- Manager Name --> 
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('mpcs::lang.manager_name') <span class="required" aria-required="true">*</span></label>
                        <input type="text" id="manager_name" name="manager_name" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-12" >
                    <h4 class="text-center" style="margin:10px">Meter Section Pump Last Meter</h4>
                    <div class="table-responsive">
    @php
        $columnsArray = [
            'receipts' => 'Receipts Section',
            'previous_day' => 'Previous Day Amount',
            'opening_stock' => 'Opening Stock Amount',
            'issue' => 'Issue Section',
            'total_issues' => 'Previous Day Amount'
        ];
    @endphp

    <table class="table table-bordered" id="form_21c_table">
        <thead>
            <tr>
                <th rowspan="2">@lang('mpcs::lang.product')</th>
                @foreach ($fuelCategory as $categoryName)
                    <th colspan="2" class="text-center">{{ $categoryName }}</th>
                @endforeach
                
            </tr>
            <tr>
                @foreach ($fuelCategory as $categoryName)
                    <th>Qty</th>
                    <th>@lang('mpcs::lang.value')</th>
                @endforeach
                
            </tr>
        </thead>
        <tbody>
            @foreach ($columnsArray as $colKey => $column)
                @php
                    $isHeaderRow = in_array($colKey, ['receipts', 'issue']);
                    $color = $isHeaderRow ? 'color: skyblue; font-weight: bold;' : '';
                @endphp
                <tr>
                    <td style="{{ $color }} white-space: nowrap; width: auto; max-width: none;">{{ $column }}</td>

                    @if ($isHeaderRow)
                        <td colspan="{{ count($fuelCategory) * 2 + 2 }}"></td>
                    @else
                        @foreach ($fuelCategory as $categoryKey => $categoryName)
                            <td>
                                <input 
                                    type="number" step="0.01"
                                    name="{{ $colKey }}[{{ $categoryKey }}][qty]"
                                    class="full-width-input qty-input"
                                    id="{{ $colKey }}_qty_{{ $categoryKey }}"
                                >
                            </td>
                            <td>
                                <input 
                                    type="number" step="0.01"
                                    name="{{ $colKey }}[{{ $categoryKey }}][val]"
                                    class="full-width-input val-input"
                                    id="{{ $colKey }}_val_{{ $categoryKey }}"
                                >
                            </td>
                        @endforeach

                        
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

                </div>
                <!-- Other fields remain unchanged -->            
        

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
    

function loadPump(obj) {
    const selectedSubCategory = $(obj).val(); 
    const row = $(obj).closest('tr'); 
    const pumpSelect = row.find('.pump_select'); 

    pumpSelect.html('<option value="">@lang("messages.please_select")</option>');

    let selectedPumps = [];
    $('#pump_last_meter tbody .pump_select').each(function() {
        const pumpValue = $(this).val();
        if (pumpValue) {
            selectedPumps.push(pumpValue);
        }
    });

    if (selectedSubCategory) {
        $.get('/mpcs/get-subcategory-pump/' + selectedSubCategory, function (data) {
            $.each(data, function (id, name) {
                if (!selectedPumps.includes(id.toString())) { // Avoid already selected pumps
                    pumpSelect.append(new Option(name, id)); 
                }
            });
        }).fail(function () {
            toastr.error('No Pumps Found!');
        });
    }
}

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



</script>
