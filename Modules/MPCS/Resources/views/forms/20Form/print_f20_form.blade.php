<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>F22-{{$details['F21c_from_no']}} {{request()->session()->get('business.name')}}</title>
    {{-- @include('layouts.partials.css') --}}
</head>
<style>

    table {
        border-collapse: collapse;
    }
    table tbody td {
        border: 1px solid black; 
    }
    table thead th {
        border: 1px solid black; 
    }
    th{
        font-size: 13px;
    }
    td{
        font-size: 13px;
    }
    @media print {
    #form_21c_table {
        width: 100%;
        border-collapse: collapse; /* Optional: to make borders collapse into a single line */
    }

    #form_21c_table td, #form_21c_table th {
        padding: 8px; /* Apply padding to table cells */
        border: 1px solid black; /* Ensure borders are visible in print */
    }

    #form_21c_table tr {
        margin-bottom: 4px; /* Optional: adjust row spacing */
    }
}
</style>
<body>
<table  style="width: 100%; border: none !important">
    <!-- Business Name -->
    <tr>
        <td colspan="4" class="text-center fw-bold" align="center">
            <h4>{{ request()->session()->get('business.name') }}</h4>
        </td>
    </tr>

    <!-- Form Title -->
    <tr>
        <td colspan="4" class="text-end" align="right">
            <h3 class="fw-bold">21C</h3>
        </td>
    </tr>

    <!-- Manager Name, Date, Balance Stock, Form Number -->
    <tr>
        <td class="fw-bold text-danger">Manager Name: {{ $details['manager_name'] ?? '' }}</td>
        <td class="fw-bold">Filling Station Date: {{ $details['form_21c_date'] ?? '' }}</td>
        <td class="fw-bold">Balance Stock For The Day:</td>
        <td class="fw-bold text-danger">Form No: {{ $details['F21c_from_no'] ?? '' }}</td>
    </tr>
</table><br>

        @php
        $index = 1;
        use Illuminate\Support\Arr;

        $excludedData = Arr::except($details, ['_token', '16a_location_id', 'form_16a_date', 'F21c_from_no', 'manager_name', 'form_21c_date']);


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
            'price_discounts_for_today' => 'Price Discounts for Today',
            'pre_date' => 'Pre Date',
            'total_discounts' => 'Total Discounts (2)',
            'total_for_today_one_plus_two' => 'Total for Today (1 + 2)',
            'balances' => 'Balances',
            'sub_total_for_today' => 'Sub Total for Today',
            'pump_one_today_last_meter' => 'Pump - 1 Today Last Meter',
            'pump_two_today_opening' => 'Pump - 2 Today Opening',
            'issued_qty_for_today' => 'Issued Qty for Today'
            );
            
       @endphp

       <table class="table table-bordered" id="form_21c_table" style="width: 100%;" cellpadding="2" cellspacing="2">
    <thead>
        <tr>
            <th rowspan="3">Description</th>
            <th rowspan="3">No</th>
            @foreach ($fuelCategory as $categoryName)
                <th colspan="3">{{ $categoryName }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($fuelCategory as $categoryName)
                <th colspan="3">Tank Capacity</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($fuelCategory as $categoryName)
                <th>Qty</th>
                <th colspan="2">Value</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($excludedData as $colKey => $column)
        <tr>
            <td>{{ ucwords(str_replace('_', ' ', $colKey)) }}</td>
            <td>{{ $column['no'] ?? '' }}</td>
            @foreach ($fuelCategory as $categoryKey => $categoryName)
                <td>{{ $column[$categoryKey]['qty'] ?? '' }}</td>
                <td>{{ $column[$categoryKey]['val'] ?? '' }}</td>
                <td>{{ $column[$categoryKey]['dec'] ?? '' }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
    <tfoot class="bg-gray">
                    <tr>
                        <td colspan="11"> That all the details are entered correctly</td>
                    </tr>
                    <tr>
                        <td colspan="7"  class="text-left" style="border: 0px !important">
                            <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                @lang('mpcs::lang.checked_by'): ____________</h5>
                        </td>
                        <td colspan="4" style="border: 0px !important">
                            <h5 style="font-weight: bold; margin-bottom: 0px; ">
                            @lang('mpcs::lang.signature_of_manager'): ____________</h5> <br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" class="text-left" style="border: 0px !important">
                            <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                @lang('mpcs::lang.last_date'): ____________</h5>
                        </td>
                        <td colspan="4" style="border: 0px !important">
                            <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                @lang('mpcs::lang.date'): ____________</h5>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7"  class="text-left" style="border: 0px !important">
                            <h5 style="font-weight: bold; margin-top: 10px; ">@lang('mpcs::lang.user'):
                                {{auth()->user()->username }}</h5>
                        </td>
                        <td colspan="4" style="border: 0px !important">
                            <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                </h5>
                        </td>
                    </tr>
                </tfoot>
</table>
           
       
    </div>


    {{-- @include('layouts.partials.javascripts') --}}
 
</body>

</html>