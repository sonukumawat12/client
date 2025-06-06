<div class="modal-dialog" role="document" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title no-print">
                @lang( 'purchase.view_payments' )
                (
                @if(in_array($transaction->type, ['purchase', 'expense', 'purchase_return', 'payroll']))
                @lang('purchase.ref_no'): {{ $transaction->ref_no }}
                @elseif(in_array($transaction->type, ['sell', 'sell_return']))
                @lang('sale.invoice_no'): {{ $transaction->invoice_no }}
                @endif
                )
            </h4>
            <h4 class="modal-title visible-print-block">
                @if(in_array($transaction->type, ['purchase', 'expense', 'purchase_return', 'payroll']))
                @lang('purchase.ref_no'): {{ $transaction->ref_no }}
                @elseif($transaction->type == 'sell')
                @lang('sale.invoice_no'): {{ $transaction->invoice_no }}
                @endif
            </h4>
            
        </div>

        <div class="modal-body">
            
            <div class="row">
                @php
                    $deleted_by = null;
                    if(!empty($transaction->deleted_by)){
                        $deletedBy = \App\User::find($transaction->deleted_by);
                    }
                
                @endphp
                @if(!empty($transaction->deleted_by))
                    <div class="alert alert-danger text-center">@lang('sale.deleted_by') : @if(!empty($deletedBy)) {{$deletedBy->username}}  @endif</div>
                @endif
            </div>
            
            @if(in_array($transaction->type, ['purchase', 'purchase_return']))
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    @include('transaction_payment.transaction_supplier_details')
                </div>
                <div class="col-md-4 invoice-col">
                    @include('transaction_payment.payment_business_details')
                </div>

                <div class="col-sm-4 invoice-col">
                    <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br />
                    <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br />
                    <b>@lang('purchase.purchase_status'):</b> {{ ucfirst( $transaction->status ) }}<br>
                    <b>@lang('purchase.payment_status'):</b> {{ ucfirst( $transaction->payment_status ) }}<br>
                    @if(!empty($transaction->overpayment_setoff) && $transaction->overpayment_setoff == 1)
                        <span class="badge bg-danger">@lang('lang_v1.overpayment_setoff')</span>
                    @endif
                </div>
            </div>
            @elseif($transaction->type == 'expense')
            <div class="row invoice-info">
                @if(!empty($transaction->contact))
                <div class="col-sm-4 invoice-col">
                    @lang('expense.expense_for'):
                    <address>
                        <strong>{{ $transaction->contact->supplier_business_name }}</strong>
                        {{ $transaction->contact->name }}
                        @if(!empty($transaction->contact->landmark))
                        <br>{{$transaction->contact->landmark}}
                        @endif
                        @if(!empty($transaction->contact->city) || !empty($transaction->contact->state) ||
                        !empty($transaction->contact->country))
                        <br>{{implode(',', array_filter([$transaction->contact->city, $transaction->contact->state, $transaction->contact->country]))}}
                        @endif
                        @if(!empty($transaction->contact->tax_number))
                        <br>@lang('contact.tax_no'): {{$transaction->contact->tax_number}}
                        @endif
                        @if(!empty($transaction->contact->mobile))
                        <br>@lang('contact.mobile'): {{$transaction->contact->mobile}}
                        @endif
                        @if(!empty($transaction->contact->email))
                        <br>Email: {{$transaction->contact->email}}
                        @endif
                    </address>
                </div>
                @endif
                <div class="col-md-4 invoice-col">
                    @include('transaction_payment.payment_business_details')
                </div>

                <div class="col-sm-4 invoice-col">
                    <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br />
                    <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br />
                    <b>@lang('purchase.payment_status'):</b> {{ ucfirst( $transaction->payment_status ) }}<br>

                    <b>@lang('lang_v1.is_recurring'):</b> @if($transaction->is_recurring) Yes @else No @endif<br>
                    <b>@lang('lang_v1.recur_interval'):</b>
                    {{$transaction->recur_interval}}{{ucfirst($transaction->recur_interval_type)}}<br>
                    <b>@lang('lang_v1.no_of_repetitions'):</b> {{$transaction->recur_repetitions}}<br>

                </div>
            </div>
            @elseif($transaction->type == 'payroll')
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    @lang('essentials::lang.payroll_for'):
                    <address>
                        <strong>{{ $transaction->transaction_for->user_full_name }}</strong>
                        @if(!empty($transaction->transaction_for->address))
                        <br>{{$transaction->transaction_for->address}}
                        @endif
                        @if(!empty($transaction->transaction_for->contact_number))
                        <br>@lang('contact.mobile'): {{$transaction->transaction_for->contact_number}}
                        @endif
                        @if(!empty($transaction->transaction_for->email))
                        <br>Email: {{$transaction->transaction_for->email}}
                        @endif
                    </address>
                </div>
                <div class="col-md-4 invoice-col">
                    @include('transaction_payment.payment_business_details')
                </div>
                <div class="col-sm-4 invoice-col">
                    <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br />
                    @php
                    $transaction_date = \Carbon::parse($transaction->transaction_date);
                    @endphp
                    <b>@lang( 'essentials::lang.month_year' ):</b> {{ $transaction_date->format('F') }}
                    {{ $transaction_date->format('Y') }}<br />
                    <b>@lang('purchase.payment_status'):</b> {{ ucfirst( $transaction->payment_status) }}<br>


                </div>
            </div>
            @else
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    @if(!empty($transaction->contact))
                    @lang('contact.customer'):
                    <address>
                        <strong>{{ $transaction->contact->name }}</strong>

                        @if(!empty($transaction->contact->landmark))
                        <br>{{$transaction->contact->landmark}}
                        @endif
                        @if(!empty($transaction->contact->city) || !empty($transaction->contact->state) ||
                        !empty($transaction->contact->country))
                        <br>{{implode(',', array_filter([$transaction->contact->city, $transaction->contact->state, $transaction->contact->country]))}}
                        @endif
                        @if(!empty($transaction->contact->tax_number))
                        <br>@lang('contact.tax_no'): {{$transaction->contact->tax_number}}
                        @endif
                        @if(!empty($transaction->contact->mobile))
                        <br>@lang('contact.mobile'): {{$transaction->contact->mobile}}
                        @endif
                        @if(!empty($transaction->contact->email))
                        <br>Email: {{$transaction->contact->email}}
                        @endif
                    </address>
                    @endif
                </div>
                <div class="col-md-4 invoice-col">
                    @include('transaction_payment.payment_business_details')
                </div>
                <div class="col-sm-4 invoice-col">
                    <b>@lang('sale.invoice_no'):</b> #{{ $transaction->invoice_no }}<br />
                    <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br />
                    <b>@lang('purchase.payment_status'):</b> {{ ucfirst( $transaction->payment_status ) }}<br>
                </div>
            </div>
            @endif
            
            @if(empty($deletedBy))

            @can('send_notification')
            @if($transaction->type == 'purchase')
            <div class="row no-print">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info btn-modal btn-xs"
                        data-href="{{action('NotificationController@getTemplate', ['transaction_id' => $transaction->id,'template_for' => 'payment_paid'])}}"
                        data-container=".view_modal"><i class="fa fa-envelope"></i>
                        @lang('lang_v1.payment_paid_notification')</button>
                </div>
            </div>
            <br>
            @endif
            @if($transaction->type == 'sell' || $transaction->type == 'hms_booking')
            <div class="row no-print">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info btn-modal btn-xs"
                        data-href="{{action('NotificationController@getTemplate', ['transaction_id' => $transaction->id,'template_for' => 'payment_received'])}}"
                        data-container=".view_modal"><i class="fa fa-envelope"></i>
                        @lang('lang_v1.payment_received_notification')</button>

                    @if($transaction->payment_status != 'paid')
                    &nbsp;
                    <button type="button" class="btn btn-warning btn-modal btn-xs"
                        data-href="{{action('NotificationController@getTemplate', ['transaction_id' => $transaction->id,'template_for' => 'payment_reminder'])}}"
                        data-container=".view_modal"><i class="fa fa-envelope"></i>
                        @lang('lang_v1.send_payment_reminder')</button>
                    @endif
                </div>
            </div>
            <br>
            @endif
            @endcan
            @if($transaction->payment_status != 'paid')
            <div class="row">
                <div class="col-md-12">
                    @if((auth()->user()->can('purchase.payments') && (in_array($transaction->type, ['purchase',
                    'purchase_return']) )) || (auth()->user()->can('sell.payments') && (in_array($transaction->type,
                    ['sell', 'sell_return']))) || (auth()->user()->can('expense.access') ) )
                    <a href="{{ action('TransactionPaymentController@addPayment', [$transaction->id]) }}"
                        class="btn btn-primary btn-xs pull-right add_payment_modal no-print"><i class="fa fa-plus"
                            aria-hidden="true"></i> @lang("purchase.add_payment")</a>
                    @endif
                </div>
            </div>
            @endif
            
            @endif

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('payment_filter_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('payment_filter_date_range', null, ['placeholder' =>
                        __('lang_v1.select_a_date_range'), 'class'
                        => 'form-control','id'=>'payment_filter_date_range', 'readonly']); !!}
                    </div>
                </div>
                @if($transaction->type == 'property_sell')
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('on_account_of', __('property::lang.on_account_of') . ':*') !!}
                        {!! Form::select('on_account_of', $on_account_ofs, null, ['class' => 'form-control select2','id'=>'on_account_of',
                        'placeholder' => __('lang_v1.all')]) !!}

                    </div>
                </div>
                @endif
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('ref_no', __('lang_v1.ref_no') . ':*') !!}
                        {!! Form::select('ref_no', $ref_nos, null, ['class' => 'form-control select2','id'=>'ref_no',
                        'placeholder' => __('lang_v1.all')]) !!}

                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('payment_method', __('property::lang.payment_method') . ':*') !!}
                        {!! Form::select('payment_method', $payment_types, null, ['class' => 'form-control select2','id'=>'payment_method',
                        'placeholder' => __('lang_v1.all')]) !!}

                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('user_id', __('lang_v1.users') . ':*') !!}
                        {!! Form::select('user_id', $users, null, ['class' => 'form-control select2','id'=>'user_id',
                        'placeholder' => __('lang_v1.all')]) !!}

                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped" id="view_payment_table">
                        <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('purchase.ref_no')</th>
                                @if($transaction->type == 'property_sell')
                                <th>@lang('property::lang.on_account_of')</th>
                                @endif
                                <th>@lang('purchase.amount')</th>
                                <th>@lang('purchase.payment_method')</th>
                                <th>@lang('purchase.payment_note')</th>
                                @if($accounts_enabled)
                                <th>@lang('lang_v1.payment_account')</th>
                                @endif
                                <th class="no-print">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                         
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($show_shortage)
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="box-title text-center">Shortages</h4>
                        <table class="table table-striped" id="view_settlement_shortages_table">
                            <thead>
                                <tr>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('petro::lang.pump_operator')</th>
                                    <th>@lang('purchase.amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary no-print" aria-label="Print"
                onclick="$(this).closest('div.modal').printThis();">
                <i class="fa fa-print"></i> @lang( 'messages.print' )
            </button>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close'
                )</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('.payment_modal').on('shown.bs.modal', function (e) {
        if ($('#payment_filter_date_range').length > 0 ) {
            // Assuming $transaction->transaction_date is a valid date string
            var transactionDate = moment("<?php echo $transaction->transaction_date; ?>");
        
            // Calculate the start and end of the month for the transaction date
            var startDate = transactionDate.clone().startOf('month');
            var endDate = transactionDate.clone().endOf('month');
        
            // Construct the date range settings with the calculated start and end dates
            var dateRangeSettings = {
                startDate: startDate,
                endDate: endDate,
                // other options...
            };
        
            $('#payment_filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#payment_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                }
            );
        
            $('#payment_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#payment_filter_date_range').val('');
            });
        }


    })
    
    $(document).ready(function(){
        $(".select2").select2();
    
        view_payment_table = $('#view_payment_table').DataTable({
            processing: true,
            serverSide: true,
            //destroy: true,
            ajax: {
                url: '{{action("TransactionPaymentController@getPaymentDatatable", $id)}}',
                data: function(d) {
                    //console.log(d);
                    if ($('#payment_filter_date_range').val() && $('#payment_filter_date_range').length >0) {
                        if($('#payment_filter_date_range').data('daterangepicker'))
                            d.start_date = $('#payment_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        if($('#payment_filter_date_range').data('daterangepicker'))
                            d.end_date = $('#payment_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    }
                    d.ref_no = $('#ref_no').val();
                    d.method = $('#payment_method').val();
                    d.user_id = $('#user_id').val();
                    d.payment_option = $('#on_account_of').val();
                    //alert();
                }
            },
            columns: [
                { data: 'paid_on', name: 'paid_on' },
                { data: 'payment_ref_no', name: 'payment_ref_no' },
                @if($transaction->type == 'property_sell')
                { data: 'on_account_of', name: 'on_account_of' },
                @endif
                { data: 'amount', name: 'amount' },
                { data: 'method', name: 'method' },
                { data: 'note', name: 'note' },
                @if($accounts_enabled)
                { data: 'account_name', name: 'account_name' },
                @endif
                { data: 'action', name: 'action' },
            ],
            "fnDrawCallback": function (oSettings) {
                $('#total_payments').text(__number_f(sum_table_col($('#payments_table'), 'amount')));
            },
        });
        $('#payment_filter_date_range, #payment_method, #user_id, #on_account_of, #ref_no').change(function () {
            view_payment_table.ajax.reload();
        });

        view_settlement_shortages_table = $('#view_settlement_shortages_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{action("TransactionPaymentController@getTransactionShortagesDataTable", $id)}}',
                data: function(d) {
                    // d.ref_no = $('#ref_no').val();
                }
            },
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'name', name: 'name' },
                { data: 'final_total', name: 'final_total' },
            ],
        });

    })


</script>