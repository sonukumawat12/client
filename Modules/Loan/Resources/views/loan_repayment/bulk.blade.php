@extends('layouts.app')
@section('title')
    {{ trans_choice('core.add', 1) }} {{ trans_choice('loan::general.bulk_repayment', 1) }}
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.0/jquery.bootstrap-touchspin.min.css" />
    <style>
        input,
        select {
            min-width: 150px !important;
        }
    </style>
@endsection

@section('content')

    
    
    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('header')
                        <div class="box-tools">
                            <a href="{{ url('contact_loan/bulk_import_repayments') }}" class="btn btn-primary">
                                <i class="fa fa-upload"></i>
                                {{ trans('core.from') }} {{ trans('core.excel') }}
                            </a>
                        </div>
                    @endslot


                    @slot('slot')
                        <input type="hidden" name="bulk_upload" value="1">

                        <p>{{ trans_choice('core.bulk_repayments_msg', 1) }}</p>

                        <form method="post" action="{{ url('contact_loan/store_bulk_repayment') }}">

                            {{ csrf_field() }}

                            <div class="table-responsive">
                                <table id="editrow" class="table table-striped table-hover">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>{{ trans_choice('core.row', 1) }}</th>
                                            <th>{{ trans_choice('core.loan', 1) }} @show_tooltip(__('loan::lang.tooltip_loanlists'))</th>
                                            <th>{{ trans_choice('core.amount', 1) }} @show_tooltip(__('loan::lang.tooltip_loanamount'))</th>
                                            <th>{{ trans_choice('core.method', 1) }} @show_tooltip(__('loan::lang.tooltip_loanmethod'))</th>
                                            <th>{{ trans_choice('core.collection', 1) }} {{ trans_choice('core.date', 1) }}
                                                @show_tooltip(__('loan::lang.tooltip_loandate'))</th>
                                            <th>{{ trans_choice('core.description', 1) }} ({{ trans_choice('core.optional', 1) }})
                                                @show_tooltip(__('loan::lang.tooltip_loandescription'))</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($count = 0; $count < 20; $count++)
                                            <tr>
                                                <td>
                                                    {{ $count + 1 }}
                                                </td>
                                                <td>
                                                    <select class="form-control select2 loan_id" name="loan_id_{{ $count }}"
                                                        id="loan_id_{{ $count }}" style='width: 200px'>
                                                        <option value="" class='but_read'></option>
                                                        @foreach ($loans as $key)
                                                            <option value="{{ $key->id }}">{{ $key->contact }} Loan #{{ $key->id }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step=".01" class="form-control repayment_amount"
                                                        name="repayment_amount_{{ $count }}" id="repayment_amount_{{ $count }}" />
                                                </td>
                                                <td>
                                                    <select class="form-control" name="payment_type_id_{{ $count }}"
                                                        id="payment_type_id_{{ $count }}">
                                                        @foreach ($payment_types as $key)
                                                            <option value="{{ $key->id }}">{{ $key->name }}</option>
                                                        @endforeach;
                                                    </select>

                                                    <div class="form-group">
                                                        <label for="receipt_number_{{ $count }}"
                                                            class="control-label">{{ trans_choice('core.receipt', 1) }}
                                                            #</label>
                                                        <input type="text" name="receipt_number_{{ $count }}" class="form-control"
                                                            value="" id="receipt_number_{{ $count }}">
                                                    </div>
                                                    <small>
                                                        <a href="#" id="SetDefaultMethods">{{ trans_choice('core.set_default', 1) }}</a>
                                                    </small>
                                                </td>
                                                <td>
                                                    <input class="form-control datepicker" type="text" name="repayment_date_{{ $count }}"
                                                        id="repayment_date_{{ $count }}" readonly>
                                                    @error('repayment_date')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    <small><a href="#" id="SetDefaultDates">Set Default</a></small>
                                                </td>
                                                <td>
                                                    <textarea type="text" name="description_{{ $count }}" id="description_{{ $count }}"
                                                        class="form-control @error('notes') is-invalid @enderror"></textarea>
                                                </td>
                                            </tr>
                                        @endfor

                                        <tr>
                                            <td>&nbsp;</td>
                                            <td class="text-bold text-right">
                                                {{ trans_choice('core.total', 1) }}:
                                            </td>
                                            <td class="text-bold text-right">
                                                <div id="RepaymentAmountTotal">0</div>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>

                            <button type="submit" class="btn btn-primary pull-right m-3" class="btn btn-info pull-right"
                                data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait. This can take a few minutes.">
                                {{ trans_choice('core.submit', 1) }}
                            </button>
                        </form>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>

@stop
@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.0/jquery.bootstrap-touchspin.min.js"></script>

    <script>
        var app = new Vue({
            el: '#vue-app',
            data: {
                amount: "{{ old('amount') }}",
                date: "{{ old('date', date('Y-m-d')) }}",
                payment_type_id: parseInt("{{ old('payment_type_id') }}"),
                account_number: "{{ old('account_number') }}",
                cheque_number: "{{ old('cheque_number') }}",
                routing_code: "{{ old('routing_code') }}",
                receipt: "{{ old('receipt') }}",
                bank_name: "{{ old('bank_name') }}",
                description: `{{ old('description') }}`,
                payment_types: {!! json_encode($payment_types) !!},
                dob: "{{ old('dob') }}",
            }
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            //Set default method
            $("#SetDefaultMethods").click(function() {
                var inputRepaymentMethodId1 = $("#payment_type_id_0 option:selected").index() + 1;
                for (var i = 1; i < 20; i++) {
                    $("#payment_type_id_" + i + " :nth-child(" + inputRepaymentMethodId1 + ")").prop("selected", true);
                }
            });

            //Set default dates
            $("#SetDefaultDates").click(function() {
                var inputRepaymentDate1 = document.getElementById("repayment_date_0").value;
                for (var i = 1; i < 20; i++) {
                    $("#repayment_date_" + i).val(inputRepaymentDate1);
                }
            });

            //Update total sum of repayment amounts
            $('.repayment_amount').change(function() {
                updatesum();
            });

            function updatesum() {
                var inputRepaymentAmountTotal = 0;

                for (var i = 0; i < 20; i++) {
                    var inputRepaymentAmount = document.getElementById("repayment_amount_" + i).value;

                    if (inputRepaymentAmount == "")
                        inputRepaymentAmount = 0;

                    inputRepaymentAmountTotal = parseFloat(inputRepaymentAmountTotal) + parseFloat(inputRepaymentAmount) * 100;
                }
                document.getElementById("RepaymentAmountTotal").innerHTML =
                    numberWithCommas((inputRepaymentAmountTotal / 100).toFixed(2));
            }

            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            //Initialize Touchspin 
            $(".repayment_amount").TouchSpin({
                min: 0,
                max: 1000000000,
                step: 5000,
                forcestepdivisibility: 'none', //To stop rounding off of figures
                maxboostedstep: 10000000,
                initval: 0,
                verticalbuttons: true
            });

            // Initialize select2
            $(".loan_id").select2();

            // Read selected option
            $('.but_read').click(function() {
                var name = $('.loan_id option:selected').text();
                var loan_id = $('.loan_id').val();
                $('.result').html("id : " + loan_id + ", name : " + loan_id);
            });
        });
    </script>
@endsection
