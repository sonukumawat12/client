<style>
    .rows {
        padding: 0 !important;
        margin: 0 !important;
    }

    .full-width-input {
        width: 100% !important;
        box-sizing: border-box;
        padding: 5px;
        margin: 0;
        border: 1px solid #ccc;
        height: 100%;
    }

    .table tbody tr td.rows {
        padding: 0 !important;
        vertical-align: middle !important;
    }

    .text-center {
        text-align: center;
    }

    .text-red {
        color: red;
    }

    .f20_location_name {
        font-size: 20px;
    }

    .table th,
    .table td {
        text-align: center;
        vertical-align: middle;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .bg-gray {
        background-color: #f7f7f7;
    }

    .text-bold {
        font-weight: bold;
    }

    .page-button {
        padding: 5px 10px;
        margin: 0 5px;
        border: 1px solid #ddd;
        cursor: pointer;
    }

    .page-button:hover {
        background-color: #f1f1f1;
    }

    .page-button.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    #pagination-controls {
        margin-top: 10px;
        text-align: center;
    }
</style>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 text-red" style="margin-top: 14px;"></div>

                        <div class="col-md-5 text-center">
                            <h5 style="font-weight: bold;">
                                {{ request()->session()->get('business.name') }} <br>
                                @foreach ($business_locations as $location)
                                    <span class="f20_location_name">{{ $location }}</span>
                                @endforeach
                            </h5>
                        </div>

                        <div class="col-md-3 text-left">
                            <h5 style="font-weight: bold;" class="form-control">
                                @lang('mpcs::lang.form_no') : <span id="form_no1" style="display: none;" >{{ $F20_form_sn }}</span>
                            </h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4" id="location_filter"></div>

                        <div class="col-md-4 text-center">
                            <p>Filling Station Stock Sale Summary</p>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('type', 'Form Type:') !!}
                                {!! Form::select('16a_location_id', ['Cash' => 'Cash', 'Credit' => 'Credit'], null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'id' => 'form_type_select', 
                                    'placeholder' => __('lang_v1.all'),
                                ]) !!}
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                                {!! Form::text(
                                    'form_20_date_range',
                                    @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'),
                                    [
                                        'placeholder' => __('lang_v1.select_a_date_range'),
                                        'class' => 'form-control',
                                        'id' => 'form_20_date_range_data',
                                        'readonly',
                                    ],
                                ) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            @component('components.widget', ['class' => 'box-primary'])
                                <div class="col-md-12">
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="table-responsive">
                                            <div id="table-container">
                                          <table class="table table-bordered table-striped" id="form_20_table_data">
    <thead>
        <tr>
            <th rowspan="2">@lang('mpcs::lang.bill_no')</th>
            <th rowspan="2">@lang('mpcs::lang.settlement_no')</th>
            <th colspan="2">@lang('mpcs::lang.product_name')</th>
            @foreach ($products as $productId => $product)
                <th rowspan="1">{{ data_get($product, '0.product_name', '') }}</th>
            @endforeach
        </tr>
        <tr>
            <th colspan="2">@lang('mpcs::lang.product_code')</th>
            @foreach ($products as $productId => $product)
                <th rowspan="1">{{ data_get($product, '0.product_sku', '') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <!-- Rows will be populated dynamically via AJAX -->
    </tbody>
    <tfoot class="bg-gray">
        <tr>
            <td></td>
            <td></td>
            <td class="text-bold" colspan="2">Total Qty</td>
            @foreach ($products as $productId => $product)
                <td class="text-bold"></td>
            @endforeach
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td class="text-bold" colspan="2">Unit Sales Price</td>
            @foreach ($products as $productId => $product)
                <td class="text-bold"></td>
            @endforeach
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td class="text-bold" colspan="2">Total Amount</td>
            @foreach ($products as $productId => $product)
                <td class="text-bold"></td>
            @endforeach
        </tr>
    </tfoot>
</table>
                                                <!-- Pagination Controls -->
                                                <div id="pagination-controls"></div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endcomponent
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        function fetchFormNumber() {
            var formType = $('#form_type_select').val();
            var formNumber = $('#form_no1').text();
            var formDate = $('#form_20_date_range_data').val();

            $.ajax({
                url: '/mpcs/fetch-form-number',
                method: 'GET',
                data: {
                    form_type: formType,
                    form_number: formNumber,
                    form_date: formDate
                },
                success: function(response) {
                    $('#form_no1').text(response.form_number).show();
                },
                error: function(xhr, status, error) {
                    var errorMessage = "Error: " + error + "\n";
                    errorMessage += "Status: " + status + "\n";
                    errorMessage += "Response: " + xhr.responseText;
                    alert(errorMessage);
                }
            });
        }
        // Trigger on form date change
        $('#form_20_date_range_data').on('apply.daterangepicker', function(ev, picker) {
            fetchFormNumber();
        });
        
        // Trigger on form type change
        $('#form_type_select').change(fetchFormNumber);
        
        // Trigger immediately on page load
        fetchFormNumber();

       
    });
</script>

