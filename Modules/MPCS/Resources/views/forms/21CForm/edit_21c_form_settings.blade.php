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
    {!! Form::open(['url' => action([\Modules\MPCS\Http\Controllers\F21FormController::class, 'mpcs21Update'], [$latestForm->id]), 'method' => 'post', 'id' => 'update_21c_form_settings' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit 21 C Form Settings</h4>
        </div>

        <!-- Date and Time -->
        <div class="col-md-3">
            <div class="form-group">
                <label>@lang('mpcs::lang.opening_date')</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="datepicker" name="datepicker" data-date-format="yyyy/mm/dd" value="{{$latestForm->date}}"   readonly />
                    <div class="input-group-addon">
                        <i class="fa fa-calendar-o"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time -->
        <div class="col-md-3">
            <div class="form-group">
                <label>@lang('mpcs::lang.time')</label>
                <div class="input-group">
                    <input class="form-control timepicker" id="time" name="time" type="time" value="{{$latestForm->time}}" readonly>
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
                <input type="text" name="starting_number" class="form-control" value="{{$latestForm->starting_number}}" required>
            </div>
        </div>

        <!-- Manager Name -->
        <div class="col-md-3">
            <div class="form-group">
                <label>@lang('mpcs::lang.manager_name') <span class="required" aria-required="true">*</span></label>
                <input type="text" id="manager_name" name="manager_name" class="form-control" value="{{$latestForm->manager_name}}"   required>
            </div>
        </div>

        <div class="col-md-12">
            <h4 class="text-center" style="margin:10px">Meter Section Pump Last Meter</h4>
            <div class="table-responsive">
    @php
        $columnsArray = [
            'receipts' => 'Receipts Section',
            'previous_day' => 'Previous Day Amount',
            'opening_stock' => 'Opening Stock Amount',
            'issue' => 'Issue Section',
            'total_issues' => 'Total Issues Amount'
        ];
    @endphp

    <table class="table table-bordered" id="form_21c_table">
        <thead>
            <tr>
                <th class="no-wrap align-middle" rowspan="2">@lang('mpcs::lang.product')</th>
                @foreach ($fuelCategory as $categoryName)
                    <th colspan="2" class="text-center">{{ $categoryName }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($fuelCategory as $categoryName)
                    <th class="text-center">Qty</th>
                    <th class="text-center">Val</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($columnsArray as $colKey => $column)
                <tr>
                @php
    $highlight = in_array($colKey, ['receipts', 'issue']) ? 'color: skyblue; font-weight: bold;' : '';
@endphp
<td style="{{ $highlight }} white-space: nowrap; width: auto; max-width: none;">{{ $column }}</td>


                    @if (in_array($colKey, ['receipts', 'issue']))
                        <td colspan="{{ count($fuelCategory) * 2 }}"></td>
                    @else
                        @foreach ($fuelCategory as $categoryId => $categoryName)
                            @php
                                $qty = $categoriesData[$categoryId][$colKey]['qty'] ?? '';
                                $val = $categoriesData[$categoryId][$colKey]['val'] ?? '';
                            @endphp
                            <td>
                                <input type="number"
                                       step="0.01"
                                       name="{{ $colKey }}[{{ $categoryId }}][qty]"
                                       class="full-width-input"
                                       placeholder="Qty"
                                       value="{{ $qty }}">
                            </td>
                            <td>
                                <input type="number"
                                       step="0.01"
                                       name="{{ $colKey }}[{{ $categoryId }}][val]"
                                       class="full-width-input"
                                       placeholder="Val"
                                       value="{{ $val }}">
                            </td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
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

$(document).ready(function(){
    // $('#datepicker').datepicker('setDate', new Date());

    // let now = new Date();
    // let hours = String(now.getHours()).padStart(2, "0");
    // let minutes = String(now.getMinutes()).padStart(2, "0");
    // let currentTime = `${hours}:${minutes}`;

    // document.getElementById("time").value = currentTime;
});
</script>
