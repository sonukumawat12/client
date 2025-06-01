@php

use App\Transaction;
$business_id = request()->session()->get('user.business_id');

$banks = Transaction::where('transactions.type','cheque_return')
    ->where('transactions.business_id',$business_id)
    ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
    ->select('transaction_payments.bank_name')
    ->distinct()
    ->pluck('bank_name','bank_name');
    
$cheque_nos = Transaction::where('transactions.type','cheque_return')
    ->where('transactions.business_id',$business_id)
    ->select('transaction_payments.cheque_number')
    ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
    ->distinct()
    ->pluck('cheque_number','cheque_number');
    
$amounts = Transaction::where('transactions.type','cheque_return')
    ->where('transactions.business_id',$business_id)
    ->select('transaction_payments.amount')
    ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
    ->distinct()
    ->pluck('amount','amount');



@endphp



@extends('layouts.app')

@section('content')

<!-- Content Header (Page header) -->


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Contacts</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Contacts</a></li>
                    <li><span>Returned Cheques</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>



<!-- Main content -->
<section class="content main-content-inner">
    <div class="row">
        <div class="col-sm-12">
            @component('components.filters', ['title' => __('report.filters')])
             <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('cheque_return_date_range', __('lang_v1.date_range').':') !!}
                                {!! Form::text('cheque_return_date_range', null, ['class' => 'form-control ', 'style' => 'width: 100%;']); !!}
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('cheque_return_user_id', __('lang_v1.customer').':') !!}
                                {!! Form::select('cheque_return_user_id', $contacts, null, ['class' => 'form-control
                                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('cheque_return_cheque_no', __('lang_v1.cheque_no').':') !!}
                                {!! Form::select('cheque_return_cheque_no', $cheque_nos, null, ['class' => 'form-control
                                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('cheque_return_amount', __('lang_v1.amount').':') !!}
                                {!! Form::select('cheque_return_amount', $amounts, null, ['class' => 'form-control
                                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                            </div>
                        </div>
                        </div>
                        <div class="col-md-12">
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('cheque_return_bank', __('lang_v1.bank').':') !!}
                                {!! Form::select('cheque_return_bank', $banks, null, ['class' => 'form-control
                                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('cheque_return_date', __('lang_v1.cheque_date').':') !!}
                                {!! Form::date('cheque_return_date', null, ['class' => 'form-control
                                ', 'style' => 'width: 100%;']); !!}
                            </div>
                        </div>
                        
                        
                    </div>
                </div>

            @endcomponent
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="returned_cheques_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang( 'lang_v1.date' )</th>
                    <th>@lang( 'lang_v1.contact_type' )</th>
                    <th>@lang( 'lang_v1.name' )</th>
                    <th>@lang( 'lang_v1.bank' )</th>
                    <th>@lang( 'lang_v1.cheque_number' )</th>
                    <th>@lang( 'lang_v1.amount' )</th>
                    <th>@lang( 'lang_v1.cheque_date' )</th>
                    <th>@lang( 'lang_v1.cheque_return_ref_no' )</th>
                    @if(auth()->user()->can('edit_received_outstanding') || auth()->user()->can('add_received_outstanding'))
                        <th>Action</th>
                    @endif
                    
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

</section>
<!-- /.content -->

@endsection


@section('javascript')
<script>
    // $('#cheque_return_date_range').daterangepicker(
    //     dateRangeSettings,
    //     function (start, end) {
    //         $('#cheque_return_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
    //         returned_cheques_table.ajax.reload();
    //     }
    // );
    $('#cheque_return_date_range').daterangepicker({
                    singleDatePicker: false, // For selecting a single date
                    showDropdowns: true, // To show the dropdown for predefined date ranges
                    locale: {
                        format: 'YYYY-MM-DD', // Adjust the date format according to your needs
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Custom Date Range': [moment().startOf('month'), moment().endOf(
                            'month')], // Default custom date range (this can be modified)
                    }
                }, function(start, end, label) {
                    if (label === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Set the selected date in the input
                        $('#cheque_return_date_range').val(start.format('YYYY-MM-DD'));

                        // Refresh DataTable with new date
                        returned_cheques_table.ajax.reload();
                    }
                });

                $('#custom_date_apply_button').on('click', function () {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);
                    let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                    // === Update #9c_date_range if it exists ===
                    if ($('#cheque_return_date_range').length) {
                        $('#cheque_return_date_range').val(fullRange);
                        $('#cheque_return_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#cheque_return_date_range').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        returned_cheques_table.ajax.reload();
                    }
                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
    $('#cheque_return_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#cheque_return_date_range').val('');
        returned_cheques_table.ajax.reload();
    });
    
    $(document).ready(function () {
        returned_cheques_table = $('#returned_cheques_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "/returned-cheque-details",
                data: function(d){
                    if($('#cheque_return_date_range').val()) {
                        var start = $('#cheque_return_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#cheque_return_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.amount = $('#cheque_return_amount').val();
                    d.cheque_number = $('#cheque_return_cheque_no').val();
                    d.bank_name = $('#cheque_return_bank').val();
                    d.user_id = $('#cheque_return_user_id').val();
                    d.contact_type = $("#cheque_return_contact_type").val();
                    d.cheque_date = $("#cheque_return_cheque_date").val();
                    
                    
                    
                }
            },
            columnDefs:[
                {
                    "targets": 0, // Transaction Date
                    "width": "10%", // Set the desired width for this column
                },
                {
                    "targets": 1, // Contact Type
                    "width": "12%", // Set the desired width for this column
                },
                {
                    "targets": 2, // Customer
                    "width": "12%", // Set the desired width for this column
                },
                {
                    "targets": 3, // Bank Name
                    "width": "10%", // Set the desired width for this column
                },
                {
                    "targets": 4, // Payment Cheque Number
                    "width": "10%", // Set the desired width for this column
                },
                {
                    "targets": 5, // Amount
                    "width": "8%", // Set the desired width for this column
                },
                {
                    "targets": 6,
                    "orderable": false,
                    "searchable": false,
                    "width" : "10%",
                },
                {
                    "targets": 7, // Cheque Date
                    "width": "10%", // Set the desired width for this column
                },
                {
                    "targets": 8, // Cheque Date
                    "width": "8%", // Set the desired width for this column
                }
                ],
            columns: [
                {data: 'transaction_date', name: 'transaction_date'},
                {data: 'contact_type', name: 'contact_type'},
                {data: 'customer', name: 'customer'},
                {data: 'bank_name', name: 'bank_name'},
                {data: 'payment_cheque_number', name: 'payment_cheque_number'},
                {data: 'amount', name: 'amount'},
                {data: 'cheque_date', name: 'cheque_date'},
                {data: 'cheque_ref_no', name: 'cheque_ref_no'},
                @if(auth()->user()->can('edit_received_outstanding') || auth()->user()->can('add_received_outstanding'))
                    {data: 'action', name: 'action'},
                @endif
            ],
            @include('layouts.partials.datatable_export_button')
            "fnDrawCallback": function (oSettings) {
                
                __currency_convert_recursively($('#returned_cheques_table'));
            },
            "rowCallback": function( row, data, index ) {
                
            }
        });
        
        $('#cheque_return_amount, #cheque_return_cheque_no, #cheque_return_bank, #cheque_return_user_id,#cheque_return_date').on('change', function() {
           returned_cheques_table.ajax.reload();
            
        });
})
    
    
</script>
@endsection

