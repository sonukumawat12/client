@extends('layouts.app')
@section('title', __('mpcs::lang.F17_form'))

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#f17_from_tab" class="f17_from_tab" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.f17_from')</strong>
                            </a>
                        </li>

                        <li>
                            <a href="#list_f17_from_tab" class="list_f17_from_tab" style="" data-toggle="tab">
                                <i class="fa fa-list"></i> <strong>
                                    @lang('mpcs::lang.list_f17_from') </strong>
                            </a>
                        </li>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="f17_from_tab">
                            @include('mpcs::forms.F17.partials.f17_from')
                        </div>

                        <div class="tab-pane" id="list_f17_from_tab">
                            @include('mpcs::forms.F17.partials.list_f17_from')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->

@endsection
@section('javascript')
    <script type="text/javascript">
        $('#f17_date').datepicker();
        $('#location_id option:eq(1)').attr('selected', true);
        $('#list_form_f17_location_id option:eq(1)').attr('selected', true);
        $(document).ready(function() {
            //form_17_table 
            form_17_table = $('#form_17_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/mpcs/F17/create',
                    data: function(d) {
                        var start_date = $('input#f17_date').val()
                        d.start_date = start_date;
                        d.category_id = $('#product_list_filter_category_id').val();
                        d.unit_id = $('#product_list_filter_unit_id').val();
                        d.brand_id = $('#product_list_filter_brand_id').val();
                        d.location_id = $('#location_id').val();
                        d.store_id = $('#store_id').val();
                    }
                },
                columns: [{
                        data: 'DT_Row_Index',
                        name: 'DT_Row_Index',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sku',
                        name: 'products.sku'
                    },
                    {
                        data: 'product',
                        name: 'products.name'
                    },
                    {
                        data: 'current_stock',
                        name: 'vld.qty_available'
                    },
                    {
                        data: 'unit_price',
                        name: 'variations.default_sell_price'
                    },
                    {
                        data: 'select_mode',
                        name: 'select_mode'
                    },
                    {
                        data: 'new_price',
                        name: 'new_price'
                    },
                    {
                        data: 'unit_price_difference',
                        name: 'unit_price_difference'
                    },
                    {
                        data: 'price_changed_loss',
                        name: 'price_changed_loss'
                    },
                    {
                        data: 'price_changed_gain',
                        name: 'price_changed_gain'
                    },
                    {
                        data: 'signature',
                        name: 'signature'
                    },
                    {
                        data: 'page_no',
                        name: 'page_no'
                    },
                ],
                fnDrawCallback: function(oSettings) {
                    // Handle the pagination update and update form numbers
                    let pageInfo = form_17_table.page.info(); // Get pagination info
                    let pageNumber = pageInfo.page + 1; // Get the current page number (1-based)

                    // Set the form number based on the page number
                    let formNumber = "{{ $F17_from_no }}"; // Or customize this logic as needed
                    if (pageInfo.pages > 1) {
                        let newFormNumber = formNumber + '-' + pageNumber; // Append pagination number
                        $('#F17_from_no').val(newFormNumber); // Update the form number text
                    } else {
                        // If there's only one page, don't append '-1'
                        $('#F16a_from_no').val(
                            formNumber); // Set the base form number in the input field
                        $('#F17_from_no').val(formNumber); // Update the form number text
                    }
                },
                columnDefs: [{
                    width: 20,
                    targets: 6
                }],
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $.fn.dataTable
                    .tables({ visible: true, api: true })
                    .columns.adjust()
                    .responsive.recalc(); // Optional: if using responsive extension
            });

            $(document).on('keyup', '.new_price_value', function() {
                let tr = $(this).parent().parent();
                let unit_price = parseFloat(tr.find('.unit_price').data('orig-value'));
                let select_mode = tr.find('.select_mode').val();
                let current_stock = parseFloat(tr.find('.current_stock').data('orig-value'));

                price_gain = 0;
                price_loss = 0;
                difference = 0;

                difference = parseFloat($(this).val()) - unit_price;

                if (select_mode == 'increase') {
                    price_gain = current_stock * difference;
                }
                if (select_mode == 'decrease') {
                    price_loss = current_stock * difference;
                }


                tr.find('.price_changed_loss').text(__number_f(price_loss, false, false,
                    __currency_precision));
                tr.find('.price_changed_gain').text(__number_f(price_gain, false, false,
                    __currency_precision));
                tr.find('.unit_price_difference').text(__number_f(difference, false, false,
                    __currency_precision));
                tr.find('.price_changed_gain_value').val(price_gain);
                tr.find('.price_changed_loss_value').val(price_loss);
                tr.find('.unit_price_difference_value').val(difference);

            });

            $(document).on('change', '.select_mode', function() {
                let tr = $(this).parent().parent();
                tr.find('.new_price_value').trigger('keyup');
            });

            $('.f17_filter').change(function() {
                form_17_table.ajax.reload();
            });

            $('#f17_save').click(function(e) {
                e.preventDefault();
                $(this).attr('disabled', 'disabled');
                $.ajax({
                    method: 'post',
                    url: '/mpcs/F17',
                    data: {
                        data: form_17_table.$('input, select').serialize(),
                        date: $('#f17_date').val(),
                        form_no: $('#F17_from_no').val(),
                        location_id: $('#location_id').val(),
                        store_id: $('#store_id').val(),
                        category_id: $('#product_list_filter_category_id').val(),
                        unit_id: $('#product_list_filter_unit_id').val(),
                        brand_id: $('#product_list_filter_brand_id').val(),
                    },
                    success: function(result) {
                        console.log(result);

                        if (result.success == 0) {
                            toastr.error(result.msg);
                            return false;
                        } else {
                            window.location.href = '{{ URL::to('/') }}/mpcs/F17';
                        }

                    },
                });
            });

            $('#location_id').change(function() {
                $.ajax({
                    method: 'get',
                    url: '/stock-transfer/get_transfer_store_id/' + $('#location_id').val(),
                    data: {},
                    success: function(result) {
                        $('#store_id').empty();
                        $('#store_id').append(`<option value= "">Please Select</option>`);
                        $.each(result, function(i, location) {
                            $('#store_id').append(`<option value= "` + location.id +
                                `">` + location.name + `</option>`);
                        });
                    },
                });
            });
            $('#list_form_f17_location_id').change(function() {
                $.ajax({
                    method: 'get',
                    url: '/stock-transfer/get_transfer_store_id/' + $('#list_form_f17_location_id')
                        .val(),
                    data: {},
                    success: function(result) {
                        $('#list_store_id').empty();
                        $('#list_store_id').append(`<option value= "">Please Select</option>`);
                        $.each(result, function(i, location) {
                            $('#list_store_id').append(`<option value= "` + location
                                .id + `">` + location.name + `</option>`);
                        });
                    },
                });
            });

            $('#from_no_filter').select2();

            if ($('#list_f17_date_range').length === 1) {
                $('#list_f17_date_range').daterangepicker(dateRangeSettings, function(start, end, label) {
                    console.log("Selected label:", label);

                    if (label === 'Custom Date Range') {
                        $('.custom_date_typing_modal').modal('show');
                    } else {
                        $('#list_f17_date_range').val(
                            start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                        );
                        list_form_f17_table.ajax.reload();
                    }
                });

                // Optional: handle cancel action (clear input)
                $('#list_f17_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#list_f17_date_range').val('');
                });

                // Open modal if user clicks "Custom range" *before* selecting any date
                $('#list_f17_date_range').on('show.daterangepicker', function () {
                    setTimeout(() => {
                        $('.ranges li').off('click').on('click', function () {
                            const label = $(this).text();
                            if (label === 'Custom Date Range') {
                                $('.custom_date_typing_modal').modal('show');
                            }
                        });
                    }, 0);
                });

                // Set default start/end date
                $('#list_f17_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
                $('#list_f17_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));

                $('#custom_date_apply_button').on('click', function () {
                    let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                    let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                    if (startDate.length === 10 && endDate.length === 10) {
                        let formattedStartDate = moment(startDate).format(moment_date_format);
                        let formattedEndDate = moment(endDate).format(moment_date_format);
                        let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                        // === Update #9c_date_range if it exists ===
                        if ($('#list_f17_date_range').length) {
                            $('#list_f17_date_range').val(fullRange);
                            $('#list_f17_date_range').data('daterangepicker').setStartDate(moment(startDate));
                            $('#list_f17_date_range').data('daterangepicker').setEndDate(moment(endDate));
                            $("#report_date_range").text("Date Range: " + fullRange);
                            list_form_f17_table.ajax.reload();
                        }
                        // Hide the modal
                        $('.custom_date_typing_modal').modal('hide');
                    } else {
                        alert("Please select both start and end dates.");
                    }
                });
            }



            //list_form_f17_table 
            list_form_f17_table = $('#list_form_f17_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/mpcs/list-F17',
                    data: function(d) {
                        var start_date = $('input#list_f17_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#list_f17_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                        d.from_no = $('#from_no_filter').val();
                        d.category_id = $('#list_f17_category_id').val();
                        d.unit_id = $('#list_f17_unit_id').val();
                        d.brand_id = $('#list_f17_brand_id').val();
                        d.location_id = $('#list_form_f17_location_id').val();
                        d.store_id = $('#list_store_id').val();
                    }
                },
                columns: [{
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'form_no',
                        name: 'form_no'
                    },
                    {
                        data: 'location',
                        name: 'business_locations.name'
                    },
                    {
                        data: 'category',
                        name: 'categories.name'
                    },
                    {
                        data: 'sub_category',
                        name: 'sub_cat.name'
                    },
                    {
                        data: 'store',
                        name: 'store'
                    },
                    {
                        data: 'select_mode',
                        name: 'select_mode'
                    },
                    {
                        data: 'total_price_change_loss',
                        name: 'total_price_change_loss'
                    },
                    {
                        data: 'total_price_change_gain',
                        name: 'total_price_change_gain'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'page_no',
                        name: 'page_no'
                    },

                ],
                fnDrawCallback: function(oSettings) {

                },
            });

            $('.list_f17_filter').change(function() {
                list_form_f17_table.ajax.reload();
            })


        });
    </script>
@endsection
