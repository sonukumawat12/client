<style>
   .rows {
    padding: 0 !important; 
    margin: 0 !important;
}
.full-width-input {
    width: 100% !important; 
    box-sizing: border-box; 
    display: block; 
    padding: 5px; 
    margin: 0;
    border: 1px solid #fff;
    height: 100%; 
    text-align: right; /* Add this line */
}
.full-width-input-qty {
    width: 100% !important; 
    box-sizing: border-box; 
    display: block; 
    padding: 5px; 
    margin: 0;
    border: 1px solid #fff;
    height: 100%; 
    text-align: left; /* Add this line */
}
.table tbody tr td.rows {
    padding: 0 !important;
    vertical-align: middle !important;
}
.skyblue-border {
        border-color: skyblue !important;
    }
</style>
<!-- Main content -->
<section class="content" style="padding: 10px;"> 
{!! Form::open(['id' => 'f21c_form']) !!}
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('16a_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('16a_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_21c_date_range', __('report.date') . ':') !!}
                    {!! Form::text('form_21c_date_range', @format_date(date('Y-m-d')), ['class' => 'form-control input_number customer_transaction_date', 'id' =>
                      'form_21c_date_range','required','readonly']); !!}
                </div>
            </div>

           

            @endcomponent
        </div>
    </div>
    <div class="row" >
                    <div class="row text-right" style="display: flex; justify-content: end;">
                     
                     <button type="submit" name="submit_type" id="print_div" value="print"
                     class="btn btn-primary pull-right">@lang('mpcs::lang.print')</button>
                    </div>
                </div>
    <div class="row" id ="print_content">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="col-md-12">
                <div class="row">
                <div class="col-md-10">
                    <div class="text-center">
                     <h4 style="font-weight: bold;">{{request()->session()->get('business.name')}}</h4><br> 
                    </div>
                    </div>
                    <div class="col-md-2">
                    <div class="row text-right" style="display: flex; justify-content: end;">
                     <h3 style="font-weight: bold;">21C</h3>
                     
                    </div>
                    </div>
                </div>
                
                
                <div class="row">
                    <div class="col-md-3 text-red"  >
                        <h5 style="font-weight: bold;" class="text-red">Manager Name: {{ optional($settings)->manager_name }}</h5>
                       
                    </div>
                    <div class="col-md-4">
                    <div class="text-center">
                    <h5 style="font-weight: bold;"><span id="openingdate"></span></h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 style="font-weight: bold;">Balance Stock For The Day : </h5>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center pull-left">
                            <h5 style="font-weight: bold;" class="text-red">Form No: {{ $F21c_from_no }}</h5>
                        </div>
                    </div>
                </div>
                       
<div class="table-responsive">
    @php
    $columnsArray = array(
        'receipts' => 'Receipts',
        'today' => 'Today',
        'previous_day' => 'Previous Day',
        'total_receipts' => 'Total Receipts',
        'opening_stock' => 'Opening Stock',
        'total_receipts_today' => 'Total Receipts Today', 
        'issue' => 'Issue',
        'cash_for_today' => 'Cash for Today',
        'credit_for_today' => 'Credit for Today',
        'cooperative_section_for_today' => 'Cooperative Section for Today',
        'total_issues' => 'Total Issues',
        'issues_up_to_last_day' => 'Issues up to Last Day',
        'total_issues_one' => 'Total Issues (1)',
        'price_discounts_for_today' => 'Price Reduction Today',
        'pre_date' => 'Price Reduction Previous Date',
        'total_discounts' => 'Total Discounts (2)',
        'total_for_today_one_plus_two' => 'Total for Today (1 + 2)',
        'balances' => 'Balances',
        
        'pump_meters' => 'Pump Meters'
    );

    $pumps = ($settings) ? json_decode($settings->pumps, true) : [];
    use Modules\Petro\Entities\Pump;
    $pumps_name = Pump::where('business_id', 2)->pluck('pump_name', 'id');
    $groupedPumps = [];
$maxPumpCount = 0;

foreach ($pump_operator as $pump) {
    $groupedPumps[$pump->category_id][] = $pump;
    $maxPumpCount = max($maxPumpCount, count($groupedPumps[$pump->category_id]));
}
   
    @endphp    

    <table style="border-left: 1px solid lightgray; " id="form_21c_table">
    <thead>
        <tr >
            <th rowspan="2" style="border: 1px solid lightgray; text-align: center; ">@lang('mpcs::lang.description')</th>
            <th rowspan="2" style="border: 1px solid lightgray; ">@lang('mpcs::lang.no')</th>
            @foreach ($fuelCategory as $categoryName)
                <th colspan="2" style="border-top: 2px solid skyblue; border-left: 2px solid skyblue; border-right: 2px solid skyblue; border-bottom: none;" >{{ $categoryName }}</th>
            @endforeach
            <th colspan="2" style="border-top: 2px solid skyblue; border-left: 2px solid skyblue; border-right: 2px solid skyblue; border-bottom: none;">Total</th>
        </tr>
    
        <tr>
            @foreach ($fuelCategory as $categoryName)
                <th style="border-top: 2px solid skyblue; border-left: 2px solid skyblue;   border-bottom: none;" >Qty</th>
                <th style="border-top: 2px solid skyblue; border-left: 1px solid lightgray;  border-right: 2px solid skyblue; border-bottom: none;">@lang('mpcs::lang.value')</th>
            @endforeach
            <th  style="border-top: 2px solid skyblue; border-left: 2px solid skyblue;   border-bottom: none;" >Qty</th>
            <th style="border-top: 2px solid skyblue; border-left: 1px solid lightgray;  border-right: 2px solid skyblue; border-bottom: 1px solid lightgray;">@lang('mpcs::lang.value')</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($columnsArray as $colKey => $column)
    @php
        $isHeaderRow = in_array($colKey, ['receipts', 'issue', 'pump_meters']);
        $color = $isHeaderRow ? 'color: skyblue; font-weight: bold;' : '';
        $isFormno = in_array($colKey, ['opening_stock']);
       // $isPumpMeter = in_array($colKey, ['pump_meter_closing', 'pump_meter_opening', 'issued_qty_for_today']);
    @endphp
    <tr>
        <td style="{{ $color }}">{{ $column }}</td>

        @if ($isHeaderRow)
            <td colspan="{{ count($fuelCategory) * 2 + 2 }}"></td>
        @else
        @if($isFormno)
            <td class="rows" style=" border-left: 1px solid lightgray;   border-bottom: no 1px solid lightgrayne;" >
                <input type="text" step="0.01" name="{{ $colKey }}[no]"  value="{{$formNo}}" class="full-width-input-qty">
            </td>
        @else
        <td class="rows"  style=" border-left: 1px solid lightgray;   border-bottom: no 1px solid lightgrayne;">
                <input type="text" step="0.01" name="{{ $colKey }}[no]" class="full-width-input-qty">
            </td>
        @endif

        @foreach ($fuelCategory as $categoryKey => $categoryName)
           


                
            
                <td class="rows" style="border-top: 2px solid skyblue; border-left: 2px solid skyblue;">
                    <input 
                        type="text" step="0.01"
                        name="{{ $colKey }}[{{ $categoryKey }}][qty]" 
                        class="full-width-input-qty qty-input" 
                        id="{{ $colKey }}_qty_{{ $categoryKey }}"
                        data-type="{{ $colKey }}" 
                        data-category="{{ $categoryKey }}"
                        readonly
                    >
                </td>
                <td class="rows" style="border-top: 2px solid skyblue; border-left: 1px solid lightgray; border-right: 2px solid skyblue;">
                    <input 
                        type="text" step="0.01" 
                        name="{{ $colKey }}[{{ $categoryKey }}][val]" 
                        class="full-width-input val-input" 
                        id="{{ $colKey }}_val_{{ $categoryKey }}"
                        data-type="{{ $colKey }}" 
                        data-category="{{ $categoryKey }}"
                        readonly
                    >
                </td>
            
        @endforeach
       
             
            
            <td class="rows">
                <input type="text" step="0.01" id="{{ $colKey }}_qty_total" value="0" readonly class="full-width-input-qty" readonly>
            </td>
            <td class="rows" style="border-top: 2px solid skyblue;  border-left: 1px solid lightgray;  border-right: 2px solid skyblue; border-bottom:  1px solid lightgray;">
                <input type="text" step="0.01" id="{{ $colKey }}_val_total" value="0" readonly class="full-width-input" readonly>
            </td>
            @endif
        
    </tr>
    @if ($colKey == 'pump_meters')
                    @for ($i = 0; $i < $maxPumpCount; $i++)
                        <!-- Pump Row -->
                        <tr class="pump-row">
                            <td style="font-weight: bold; color: red;">Pump</td>
                            <td class="rows" style="border-left: 1px solid lightgray; border-bottom: 1px solid lightgray;">
                                <input type="number" step="0.01" name="pump_meters[no][]" class="full-width-input">
                            </td>
                            @foreach ($fuelCategory as $categoryKey => $categoryName)
                                @php
                                    $category = \App\Category::where('name', $categoryName)->first();
                                    $pump = $groupedPumps[$category->id][$i] ?? null;
                                @endphp
                                @if ($pump)
                                    <td colspan="2" style="font-weight: bold; color: red; border-left: 2px solid skyblue; border-right: 2px solid skyblue;">
                                        {{ $pump->pump_no }}
                                        <input type="hidden" name="pump_ids[{{ $category->id }}][]" value="{{ $pump->pump_id }}">
                                    </td>
                                @else
                                    <td colspan="2" style="border-left: 2px solid skyblue; border-right: 2px solid skyblue;"></td>
                                @endif
                            @endforeach
                            <td colspan="2" style="border-left: 2px solid skyblue; border-right: 2px solid skyblue;"></td>
                        </tr>

                        <!-- Pump Meter Rows -->
                        @foreach (['pump_meter_opening', 'pump_meter_closing', 'issued_qty_for_today'] as $meterType)
                            <tr>
                                <td style="border-left: 1px solid lightgray; border-bottom: {{ $loop->last ? '1px solid lightgray' : 'none' }}">
                                    {{ ucwords(str_replace('_', ' ', $meterType)) }}
                                </td>
                                <td class="rows" style="border-left: 1px solid lightgray; border-bottom: {{ $loop->last ? '1px solid lightgray' : 'none' }}">
                                    <input type="text" step="0.01" name="{{ $meterType }}[no][]" class="full-width-input">
                                </td>
                                @foreach ($fuelCategory as $categoryKey => $categoryName)
                                    @php
                                        $category = \App\Category::where('name', $categoryName)->first();
                                        $pump = $groupedPumps[$category->id][$i] ?? null;
                                    @endphp
                                    <td colspan="2" style="font-weight: bold;  border-left: 2px solid skyblue; border-right: 2px solid skyblue; border-bottom: {{ $loop->parent->last ? '1px solid lightgray' : 'none' }}">
                                        @if ($pump)
                                            <input type="text" step="0.01" 
                                                name="{{ $meterType }}[{{ $categoryKey }}][val][]" 
                                                class="full-width-input pump-meter-input" 
                                                id="{{ $meterType }}_val_{{ $categoryKey }}_{{ $pump->pump_id }}"
                                                data-pump-id="{{ $pump->pump_id }}"
                                                data-category-id="{{ $categoryKey }}"
                                                data-meter-type="{{ $meterType }}"
                                                readonly>
                                        @endif
                                    </td>
                                @endforeach
                                <td colspan="2" style="font-weight: bold; color: red; border-left: 2px solid skyblue; border-right: 2px solid skyblue; border-bottom: {{ $loop->parent->last ? '1px solid lightgray' : 'none' }};"></td>
                            </tr>
                        @endforeach
                    @endfor
                @endif



@endforeach

  

    </tbody>
</table>

</div>


            <div class="row">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('finalize', 1, false, ['class' => 'input-icheck', 'id' => 'finalize']); !!}
                                That all the details are entered correctly
                            </label>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <p>@lang('mpcs::lang.checked_by')____________</p>  <br>
                            <p>@lang('mpcs::lang.date')____________</p> 
                        </div>
                        <div class="col-md-6 text-right">
                            <p>@lang('mpcs::lang.manage_signature')____________</p>  <br>
                            <p>@lang('mpcs::lang.date')____________</p> 
                        </div>
                    </div>

            </div>

            {!! Form::close() !!}

            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->

<script>

function printDiv(divId) {
        // Get the content of the div
        var divContent = document.getElementById(divId).innerHTML;

        // Open a new window
        var printWindow = window.open('', '', 'width=900,height=700');

        // Write the content into the new window
        printWindow.document.write('<html><head><title>Print Table</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
        printWindow.document.write('<style>table{border: 1px solid black}width: 100%; border-collapse: collapse; } th, td { border: 1px solid black; #form_21c_table input { border: none; outline: none;  width: 50px !important; text-align: center;  background: transparent;  }</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(divContent); // Insert the div content
        printWindow.document.write('</body></html>');

        // Close the document and print
        printWindow.document.close();
        printWindow.print();
    }


    $(document).ready(function(){
        $('#form_21c_date').datepicker({
    autoclose: true, // Ensures the calendar closes after selection
    format: 'dd/mm/yyyy' // Adjust format as needed
}).datepicker('setDate', new Date())    });
</script>