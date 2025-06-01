@extends('layouts.app')
@section('title', __('mpcs::lang.16A_form'))
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="page-title-area">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="breadcrumbs-area clearfix">
                        <h4 class="page-title pull-left">FORM F16A</h4>
                        <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                            <li><a href="#">F16A</a></li>
                            <li><span>Last Record</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">           
                        @if (auth()->user()->can('f16a_form'))
                            <li class="active">
                                <a href="#16A_form_tab" class="16A_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.16A_form')</strong>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->can('f16a_form'))
                            <li class="">
                                <a href="#16a_form_list_tab" class="16a_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.16A_form_settings')</strong>
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">

                        @if (auth()->user()->can('f16a_form'))
                            <div class="tab-pane active" id="16A_form_tab">
                                @include('mpcs::forms.partials.16a_form')
                            </div>
                        @endif
                        @if (auth()->user()->can('16a_form'))
                            <div class="tab-pane" id="16a_form_list_tab">
                                @include('mpcs::forms.partials.list_f16')
                            </div>
                        @endif


                    </div>

                </div>
            </div>
        </div>

    </section>

    <!-- /.content -->

@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#form-id').hide();
            let previousFormNo = null;
            let previousInvoiceNo = null;
            let previousSupplier = null;
            let previousDateRange = null;

            form_f22_list_table = $('#form_f22_list_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/mpcs/get-form-f16-list',
                    data: function(d) {
                        let formNo = $('select[id="form_no"] option:selected').text();
                        let invoiceNo = $('select[id="invoice_no"] option:selected').text();
                        let supplierName = $('select[id="supplier"] option:selected').text();
                        let dateRange = $('#form_16a_date_range_list').val();

                        if (formNo !== previousFormNo) {
                            d.form_no = formNo;
                            previousFormNo = formNo;
                        }
                        if (invoiceNo !== previousInvoiceNo) {
                            d.invoice_no = invoiceNo;
                            previousInvoiceNo = invoiceNo;
                        }
                        if (supplierName !== previousSupplier) {
                            d.supplier = supplierName;
                            previousSupplier = supplierName;
                        }
                        if (dateRange !== previousDateRange) {
                            let [start, end] = dateRange.split(' - ');
                            d.start_date = start;
                            d.end_date = end;
                            previousDateRange = dateRange;
                        }
                        return d;
                    }
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'supplier',
                        name: 'Supplier'
                    },
                    {
                        data: 'form_no',
                        name: 'form_no'
                    },
                    {
                        data: 'invoice_no',
                        name: 'Invoice No'
                    },
                    {
                        data: 'this_form_total',
                        name: 'this_form_total'
                    },
                    {
                        data: 'last_form_total',
                        name: 'last_form_total'
                    },
                    {
                        data: 'grand_total',
                        name: 'grand_total'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ],
                fnDrawCallback: function(oSettings) {
                    // Call the function to update previous totals after table draw.
                    get_previous_value_16a();
                }
            });

           $(document).ready(function () {
    $('#form_16a_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false, // Prevents auto setting to allow manual formatting
        locale: {
            format: 'YYYY-MM-DD'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Custom Date Range': [moment().startOf('month'), moment().endOf('month')],
        }
    }, function (start, end, label) {
        const formattedDate = start.format('YYYY-MM-DD');
        $('#form_16a_date').val(formattedDate); // Set formatted date in input

        // Optional: update your table
        form_16a_table.ajax.reload();
    });

    // Cancel resets the input
    $('#form_16a_date').on('cancel.daterangepicker', function (ev, picker) {
        $('#form_16a_date').val('');
        form_16a_table.ajax.reload();
    });

    // Set default date on page load
    const today = moment().format('YYYY-MM-DD');
    $('#form_16a_date').val(today);
    $('#form_16a_date').data('daterangepicker').setStartDate(moment());
});



            $('#print_form_16a_btn').on('click', function(e) {
                e.preventDefault();
                let printContent = document.getElementById('printarea').innerHTML;
                let originalContent = document.body.innerHTML;

                document.body.innerHTML = printContent;
                window.print();
                document.body.innerHTML = originalContent;
                // Ensure form ID is correctly fetched
                let formId = $('#form_id').val();

                if (!formId) {
                    // alert('Form ID not found.');
                    //return;
                    //   function printForm() {
                    //     window.print();
                    //}
                }

                $.ajax({
                    url: 'mcps/print-form-f16',
                    type: 'GET',
                    data: {
                        formId: formId
                    },
                    success: function(response) {
                        if (!response || !response.html) {
                            alert('Error: No response received.');
                            return;
                        }

                        let printWindow = window.open('', '_blank');
                        printWindow.document.write(response.html);
                        printWindow.document.close();
                        printWindow.focus();
                        printWindow.print();
                    },

                    error: function(xhr, status, error) {
                        
                    }
                });
            });

            $('#form_no').on('change', function() {
                form_f22_list_table.ajax.reload();
            });

            $('#invoice_no').on('change', function() {
                form_f22_list_table.ajax.reload();
            });

            $('#supplier').on('change', function() {
                form_f22_list_table.ajax.reload();
            });

            $('#form_16a_date_range, #16a_location_id').change(function() {
                if ($('#16a_location_id').val() !== '' && $('#16a_location_id').val() !== undefined) {
                    $('.f16a_location_name').text($('#16a_location_id :selected').text());
                    form_16a_table.ajax.reload();
                } else {
                    $('.f16a_location_name').text('All');
                    form_16a_table.ajax.reload();
                }
            });

            $('#f16_save').click(function(e) {
                e.preventDefault();
                var transactionid = $('#form-index').text();
                var formNumber = $('#form-number').text();
                var formInvoice = $('#form-invoice').text();
                var formSupplier = $('#form-supplier').text();
                var stockNo = $('#new_price_value').val();
                var stockBook = $('#new_price_value2').val();
                var thisbook = $('#this_book').val();
                var prev_book = $('#prev_book').val();
                var grand_book = $('#grand_book').val();
                var thisformtotals = $('#this_form_input').val();
                var prev_formtotals = parseFloat($('#this_form_prevs').val()).toFixed(2);
                var grand_formtotals = parseFloat($('#this_form_grands').val()).toFixed(2);
                console.log(prev_formtotals);
                console.log(grand_formtotals);

                $.ajax({
                    method: 'POST',
                    url: '/mpcs/save-form-f16',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        transaction_id: transactionid,
                        form_number: formNumber,
                        form_invoice: formInvoice,
                        formSupplier: formSupplier,
                        prevformtotal: prev_formtotals,
                        grandformtotal: grand_formtotals,
                        thisformtotal: thisformtotals,
                        stockNo: stockNo,
                        stockBook: stockBook,
                        thisbook: thisbook,
                        prevbook: prev_book,
                        grandbook: grand_book
                        // Include any other data you want to send
                    },
                    success: function(result) {
                        if (result.success == 0) {
                            toastr.error(result.msg);
                            return false;
                        }
                        toastr.success('Record Saved');
                    },
                });
            });
        }); // End of document ready

        // Functions outside document ready are fine
        function get_previous_value_16a() {
            const dateRange = $('#form_16a_date').val();
              const singleDate = moment(dateRange, 'YYYY-MM-DD').format('YYYY-MM-DD');
//             var selectedDate = $('#form_16a_date').datepicker('getDate');
           
//             if (selectedDate) {
//                 var formattedDate = selectedDate.getFullYear() + '-' +
//                     ('0' + (selectedDate.getMonth() + 1)).slice(-2) + '-' +
//                     ('0' + selectedDate.getDate()).slice(-2);
//             }
//  console.log("date",formattedDate);
            var location_id = $('#16a_location_id').val();

            $.ajax({
                method: 'GET',
                url: '/mpcs/get_previous_value_16a',
                data: {
                    start_date: singleDate,
                    end_date: singleDate,
                    location_id: location_id
                },
                success: function(result) {
                    console.log(result);
                    let total_purchase_price = result.pre_total_purchase_price;//__read_number($('#total_this_p_prev'));
                    let total_sale_price = result.pre_total_sale_price;//__read_number($('#total_this_s_prev'));
                    $('#pre_F16A_total_purchase_price').text(
                        __number_f(total_purchase_price, false, false, __currency_precision)
                    );
                    $('#pre_F16A_total_sale_price').text(
                        __number_f(total_sale_price, false, false, __currency_precision)
                    );
                    let footer_total_purchase_price = __read_number($('#total_this_p'));
                    let footer_total_sale_price = __read_number($('#total_this_s'));
                    let grand_total_purchase_price = footer_total_purchase_price + parseFloat(total_purchase_price);
                    let grand_total_sale_price = footer_total_sale_price + parseFloat(total_sale_price);
                    $('#grand_F16A_total_purchase_price').text(
                        __number_f(grand_total_purchase_price, false, false, __currency_precision)
                    );
                    $('#grand_F16A_total_sale_price').text(
                        __number_f(grand_total_sale_price, false, false, __currency_precision)
                    );
                }
            });
        }


        form_16a_table = $('#form_16a_table').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            ajax: {
                url: '/mpcs/get-form-16a',
                data: function(d) {

                    var selectedDate = $('#form_16a_date').val(); // Get selected date from input field

                    if (selectedDate) {
                        d.start_date = selectedDate;
                        d.end_date = selectedDate;
                        $('.from_date').text(selectedDate); // Update UI
                    }
                    d.location_id = $('#16a_location_id').val();
                }
            },
            columns: [{
                    data: 'index_no',
                    name: 'index_no'
                },
                {
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'received_qty',
                    name: 'received_qty'
                },
                {
                    data: 'unit_purchase_price',
                    name: 'unit_purchase_price'
                },
                {
                    data: 'total_purchase_price',
                    name: 'total_purchase_price'
                },
                {
                    data: 'unit_sale_price',
                    name: 'unit_sale_price'
                },
                {
                    data: 'total_sale_price',
                    name: 'total_sale_price'
                },
                {
                    data: 'reference_no',
                    name: 'reference_no'
                },
                {
                    data: 'stock_book_no',
                    name: 'stock_book_no'
                },
            ],
            fnDrawCallback: function(oSettings) {
                // Handle the pagination update and update form numbers
                let pageInfo = form_16a_table.page.info(); // Get pagination info
                let pageNumber = pageInfo.page + 1; // Get the current page number (1-based)

                // Set the form number based on the page number
                let formNumber = "{{ $F16a_from_no }}"; // Or customize this logic as needed
                // Check if the total pages are more than 1
                if (pageInfo.pages > 1) {
                    // If more than one page, append the page number to the form number
                    let newFormNumber = formNumber + '-' + pageNumber;
                    $('#form_no1').text(newFormNumber); // Update the form number on the page
                    $('#F16a_from_no').val(newFormNumber); // Update the form number field
                } else {
                    // If there's only one page, don't append '-1'
                    $('#form_no1').text(formNumber); // Just show the base form number
                    $('#F16a_from_no').val(formNumber); // Set the base form number in the input field
                }
                caculateF16AFromTotal();
                get_previous_value_16a();
            },
        });

        function caculateF16AFromTotal() {
            var total_purchase_price =
                @if (optional($setting)->F16A_first_day_after_stock_taking == 1)
                    0
                @else
                    sum_table_col($('#form_16a_table'), 'total_purchase_price')
                @endif ;
            $('#footer_F16A_total_purchase_price').text(__number_f(total_purchase_price, false, false,
                __currency_precision));
            var total_sale_price =
                @if (optional($setting)->F16A_first_day_after_stock_taking == 1)
                    0
                @else
                    sum_table_col($('#form_16a_table'), 'total_sale_price')
                @endif ;
            $('#footer_F16A_total_sale_price').text(__number_f(total_sale_price, false, false, __currency_precision));
            $('#total_this_p').val(total_purchase_price);
            $('#total_this_s').val(total_sale_price);
        }

        // Note: You already defined get_previous_value_16a() above.
    </script>



@endsection
