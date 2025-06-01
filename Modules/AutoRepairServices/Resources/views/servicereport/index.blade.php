@extends('layouts.app')
@section('title', __('Summary Report'))

@section('content')

    <!-- Main content -->
    <section class="content-header main-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">
                        @if (auth()->user()->can('f9a_form'))
                            <li class="active">
                                <a href="#9a_form_tab" class="9a_form_tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('autorepairservices::lang.sales_income_summary')</strong>
                                </a>
                            </li>
                        @endif
                       
                        
                    </ul>
                    <div class="tab-content">
                        @if (auth()->user()->can('f9a_form'))
                            <div class="tab-pane active" id="9a_form_tab">
                                @include('autorepairservices::servicereport.partials.summary')
                            </div>
                        @endif
                       
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade form_9_c_settings_modal" id="form_9_c_settings_modal" tabindex="-1" role="dialog"
            aria-labelledby="gridSystemModalLabel"></div>
        <div class="modal fade update_form_9_c_settings_modal" id="update_form_9_c_settings_modal" tabindex="-1"
            role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    </section>
    <!-- /.content -->

@endsection
@section('javascript')
<script>
   $(document).ready(function () {
    $('#print_report_btn').click(function (e) {
        e.preventDefault();

        // Get the selected date range
        let dateRange = $('#date_range_filter').val();
        console.log(dateRange);
        
        if (!dateRange) {
            alert('Please select a date range first.');
            return;
        }

        // Store the current page URL
        let currentUrl = window.location.href;

        $.ajax({
            url: '/autorepairservices/servicereport/service-report-print',
            type: 'GET',
            data: { date_range: dateRange },
            success: function (response) {
                // Open the print view in the same window
                window.document.open();
                window.document.write(response);
                window.document.close();

                // Add afterprint event listener
                window.addEventListener('afterprint', function() {
                    // Navigate back to the original page
                    window.location.href = currentUrl;
                });

                // Print the window
                window.print();
            },
            error: function (xhr) {
                alert('Failed to generate print preview.');
                console.error(xhr.responseText);
            }
        });
    });
});

    document.getElementById('whatsApp').addEventListener('click', function () {
        const phone = document.getElementById('whatsapp_phone_no').value.trim();
        const message = "";

        if (!phone ) {
            alert('Please enter both phone number and message.');
            return;
        }

        // Format phone number (remove any non-numeric characters)
        const formattedPhone = phone.replace(/\D/g, '');

        // Encode the message for URL
        const encodedMessage = encodeURIComponent(message);

        // WhatsApp URL
        const whatsappURL = `https://wa.me/${formattedPhone}?text=${encodedMessage}`;

        // Open WhatsApp link in new tab
        window.open(whatsappURL, '_blank');
    });
</script>

<script>
  $(document).on('click', '#cheque_stamp_add', function () {
			var url = $(this).data('href');
           
			$.ajax({
				method: 'GET',
				dataType: 'html',
				url: url,
				success: function (response) {
                    console.log(response);
					$("#cancel_cheque_add_modal").html(response).modal('show');
				}
			});
		});
	$(document).on('click', '.note-btn', function (event) {
    event.preventDefault(); // Prevent default anchor behavior

    var url = $(this).attr('href'); // Get the href value

    $.ajax({
        method: 'GET',
        dataType: 'html',
        url: url,
        success: function (response) {
            console.log(response); // Debugging
            $("#cancel_cheque_note_modal").html(response).modal('show');
        }
    });
});

    if ($('#date_range_filter').length == 1) {
            $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                cancelled_table.ajax.reload();
            });
            $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }

        $('#date_range_filter, #cheque_book, #cheque_number , #users').change(function() {
            cancelled_table.ajax.reload();
        })
        
</script>
<script>   
    cancelled_table = $('#cancelled_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            
            url: '/autorepairservices/service-report',
            data: function(d) {
                
                var start_date = $('input#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                var end_date = $('input#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                d.start_date = start_date;
                d.end_date = end_date;
            }
        },
        @include('layouts.partials.datatable_export_button')
        columns: [ { data: 'job_sheet_no', name: 'job_sheet_no' },
            { data: 'bill_no', name: 'bill_no' },
            { data: 'customer', name: 'customer' },
            { data: 'vehicle_name', name: 'vehicle_name' },
            { data: 'service_type', name: 'service_type' },
            { data: 'final_total', name: 'final_total' },
            { data: 'cash', name: 'cash' },
            { data: 'card', name: 'card' },
            { data: 'final_total', name: 'credit' },
            { data: 'note', name: 'final_total' },
            { data: 'service_staff', name: 'service_staff' },
        ],
        
        fnDrawCallback: function(oSettings) {

        },
    });

    $(document).on('click', 'a.delete_button', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        cancelled_table.ajax.reload();
                    },
                });
            }
        });
    });
    
</script>
<script>
$(document).ready(function () {
    fetchSalesData();
    function fetchSalesData() {
    $.ajax({
        url: '/autorepairservices/sales-report-detail-daily', // sales-report-detail  
        method: "GET",
        dataType: "json",
        beforeSend: function () {
            $('#salesData').html('<tr><td colspan="5" style="text-align: center;">Loading...</td></tr>');
        },
        success: function (response) {
            if (response.success) {
                console.log("total",response);
                let data = response.data;
                let tableRows = '';

                // Initialize totals
                let totalBillAmount = 0, totalCash = 0, totalCard = 0, totalCredit = 0;

                if (response.success && Array.isArray(response.totals)) {

                    response.totals.forEach(totals => {
                        // Convert values to numbers and add to totals
                        let totalcredit = parseFloat(totals.total_credit) || 0;
                        let billAmount=0.0;
                        if(totalcredit>0){
                            billAmount = parseFloat(totals.total_credit) || 0;
                        }
                        else
                        {
                             billAmount = parseFloat(totals.total_sales) || 0;
                        }
                        console.log(totals.total_credit);
                       //let billAmount = parseFloat(totals.total_sales) || 0;
                        let cash = parseFloat(totals.total_cash) || 0;
                        let card = parseFloat(totals.total_card) || 0;
                        let credit = parseFloat(totals.total_credit) || 0;
 
                        totalBillAmount += billAmount;
                        totalCash += cash;
                        totalCard += card;
                        totalCredit += credit;

                        tableRows += `<tr>
                            <td>${totals.invoice_no}</td>
                            <td>${billAmount}</td>
                            <td>${cash}</td>
                            <td>${card}</td>
                            <td>${credit}</td>
                        </tr>`;
                    });
                } else {
                    tableRows = '<tr><td colspan="5" style="text-align: center;">No sales data found.</td></tr>';
                }

                // Update table body
                $('#salesData').html(tableRows);

                // Update the specific table footer
                // $('#salesFooter tr').html(`
                //     <td><strong>Total</strong></td>
                //     <td><strong>${totalBillAmount}</strong></td>
                //     <td><strong>${totalCash}</strong></td>
                //     <td><strong>${totalCard}</strong></td>
                //     <td><strong>${totalCredit}</strong></td>
                // `);
                $('#salesbill').text(totalBillAmount.toFixed(2));
                    $('#salescash').text(totalCash.toFixed(2));
                    $('#salescard').text(totalCard.toFixed(2));
                    $('#salescredit').text(totalCredit.toFixed(2));

                    $('#credit').text(totalCredit.toFixed(2));
                    $('#card').text(totalCard.toFixed(2));
                    $('#cash').text(totalCash.toFixed(2));

                    let expense = parseFloat($('#expense').text()) || 0;
                    let credit = parseFloat($('#credit').text()) || 0;
                    let card = parseFloat($('#card').text()) || 0;
                    let cash = parseFloat($('#cash').text()) || 0;

                    // Calculate the total
                    let total = expense + credit + card + cash;

                    // Update the #totals element
                    $('#totals').text(total.toFixed(2));

                    //total
               // Update totals using response values
                let totalService = parseFloat(response.total_service) || 0;
                let totalSales = parseFloat(response.total_sales) || 0;

                // Update individual totals in the DOM with labels
                $('#totalService').text("Service: " + totalService.toFixed(2));
                $('#totalSales').text("POS: " + totalSales.toFixed(2));

                // Calculate and update the grand total
                let grandtotal = totalService + totalSales;
                $('#grandtotal').text("Grand Total: " + grandtotal.toFixed(2));



            } else {
                $('#salesData').html('<tr><td colspan="5" style="text-align: center;">Error fetching data.</td></tr>');
            }
        },
        error: function () {
            $('#salesData').html('<tr><td colspan="5" style="text-align: center;">Error fetching data.</td></tr>');
        }
    });
}

// Fetch data on page load
fetchSalesData();


// Fetch data on page load
fetchSalesData();
$(document).ready(function () {
    fetchTotalSales();

    function fetchTotalSales() {
        $.ajax({
            url: '/autorepairservices/sales-report-detail',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log(response.expenses);
                if (response.success && Array.isArray(response.expenses)) {

                    const totalCash = parseFloat(response.total_cash) || 0;
                    const totalCredit = parseFloat(response.total_credit) || 0;
                    const totalCard = parseFloat(response.total_card) || 0;
                    const totalSales = parseFloat(response.total_sales) || 0;

                    let expenseHtml = '';
                    let totalExpenses = 0;

                    response.expenses.forEach(expense => {
                        const amount = parseFloat(expense.total_expense) || 0;
                        expenseHtml += `
                            <tr>
                                <td>${expense.category}</td>
                                <td>${amount.toFixed(2)}</td>
                            </tr>`;
                        totalExpenses += amount;
                    });

                    $('#expenseBody').html(expenseHtml);
                    $('#totalExpenses').text(totalExpenses.toFixed(2));

                    // Optionally update other totals
                    $('#expense').text(totalExpenses);
                   
                } else {
                    $('.sales-data').text('No data available.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                $('.sales-data').text('Error fetching data.');
            }
        });
    }
});





// Print functionality
$("#print_div").click(function() {
        printDiv();
    });
function printDiv() {
            var w = window.open('', '_self');
            var html = `
                <html>
                    <head>
                        <style>
                            @page {
                                size: landscape;
                            }
                            body {
                                width: 100%;
                                margin: 0;
                                padding: 0;
                            }
                            @media print {
                                html, body {
                                    width: 100%;
                                    overflow: visible !important;
                                }
                                * {
                                    font-size: 8pt;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        ${document.getElementById("print_content").innerHTML}
                    </body>
                </html>
            `;
            $(w.document.body).html(html);
            w.print();
            w.close();
            window.location.href = "{{ URL::to('/') }}/autorepairservices/service-report";
        }
}); 





</script>
@endsection