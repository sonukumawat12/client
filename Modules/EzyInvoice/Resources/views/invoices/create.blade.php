@extends('layouts.app')
@section('title', __('ezyinvoice::lang.invoices'))

@section('content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('ezyinvoice::lang.ezy_invoice')</a></li>
                    <li><span>@lang( 'ezyinvoice::lang.invoices')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<section class="content main-content-inner">
    @if(!empty($message)) {!! $message !!} @endif
    @component('components.widget', ['class' => 'box-primary below_box', 'id' => 'below_box'])
    <div class="row">
        <div class="col-md-12">
            @include('ezyinvoice::invoices.add')
        </div>
    </div>

    @endcomponent
</section>
<!-- /.content -->
@endsection


@section('javascript')
<script src="{{url('Modules/EzyInvoice/Resources/assets/js/payment.js?v=2')}}"></script>
<script>
    $(document).ready(function(){
        $("#credit_sale_customer_id").val($("#credit_sale_customer_id option:eq(0)").val()).trigger('change');
        $('#order_date').datepicker("setDate", new Date());
        $('#credit_sale_product_id').select2();
        $('#credit_sale_customer_id').select2();
        $('#customer_reference').select2();
    });
    
    $(document).on('change', '#customer_reference_one_time', function(){
        if($(this).val() !== '' && $(this).val() !== null && $(this).val() !== undefined){
            $('#customer_reference').attr('disabled', 'disabled');
            $('.quick_add_customer_reference').attr('disabled', 'disabled');
        }else{
            $('#customer_reference').removeAttr('disabled');
            $('.quick_add_customer_reference').removeAttr('disabled');
        }
    })

    $(document).on('submit', '#customer_reference_add_form', function(e){
        e.preventDefault();
        let url = $('#customer_reference_add_form').attr('action');
        let data = $('#customer_reference_add_form').serialize();
        $.ajax({
            method: 'POST',
            url: url,
            dataType: 'json',
            data: data,
            success: function(result) {
                if(result.success){
                    let customer_reference = result.customer_reference;
                    $('#credit_sale_customer_id').trigger('change');
                }

                $('.view_modal').modal('hide');
            },
        });
    })
    
    $(document).ready( function(){
        $('#settlement_print').css('visibility', 'hidden');
        var columns = [
                { data: 'action', searchable: false, orderable: false },
                { data: 'status', name: 'status' },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'location_name', name: 'business_locations.name' },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'created_by',searchable: false, name: 'created_by' }
            ];
      
        list_settlement = $('#list_settlement').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@index')}}',
                data: function(d) {
                    d.location_id = $('select#location_id').val();
                    d.pump_operator = $('select#pump_operator').val();
                    d.settlement_no = $('select#settlement_no').val();
                    d.start_date = $('input#expense_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#expense_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                },
            },
            columnDefs: [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
            } ],
            columns: columns,
            fnDrawCallback: function(oSettings) {
                total_amount = 0.00;
                $("#list_settlement tbody tr").each(function(){
                    let number = $(this).find("td").eq(-2).text();
                    if (number !== 'No data available in table') {
                        total_amount += parseFloat(number.replace(/,/g, ''));
                    }
                });
                
                total_amount = total_amount === 0 ? '0.00' : parseFloat(total_amount).toLocaleString(undefined, {
                              minimumFractionDigits: 2,
                              maximumFractionDigits: 2,
                              useGrouping: true
                            });
                $("#list_settlement").append('<tr class="bg-gray font-17 footer-total text-center"><td>Total</td><td></td><td></td><td></td><td></td>'+total_amount+'</td></tr>');
            },
        });
        $('#location_id, #pump_operator, #pump_operator, #settlement_no, #type, #expense_date_range').change(function(){
            list_settlement.ajax.reload();
        });
    
        $(document).on('click', 'a.delete_settlement_button', function(e) {
    		e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).attr('href');
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
                            list_settlement.ajax.reload();
                        },
                    });
                }
            });
        });

    $(document).on('click', 'a.delete_reference_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                console.log(href);
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            page_details.remove();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        list_settlement.ajax.reload();
                    },
                });
            }
        });
    });
});

$(document).on('click', '.edit_contact_button', function(e) {
    e.preventDefault();
    $('div.pump_operator_modal').load($(this).attr('href'), function() {
        $(this).modal('show');
    });
});

$('#location_id').select2();


//save settlement
$(document).on('click', '.print_settlement_button', function () {
    console.log("aaa");
    var url = $(this).data('href');
    $.ajax({
        method: 'get',
        url: url,
        data: {},
        success: function(result) {
            $('#settlement_print').html(result);

            var divToPrint=document.getElementById('settlement_print');

            var newWin=window.open('','Print-Ledger');
        
            newWin.document.open();
        
            newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
        
            newWin.document.close();
            
        },
    });
});


</script>
@endsection


