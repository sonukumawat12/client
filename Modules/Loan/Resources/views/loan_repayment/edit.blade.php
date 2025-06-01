@extends('layouts.app')
@section('title')
    {{ trans_choice('core.edit', 1) }} {{ trans_choice('loan::general.repayment', 1) }}
@endsection

@section('content')

   

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('slot')
                        <section class="content">
                            <form method="post" action="{{ url('contact_loan/repayment/' . $loan_transaction->id . '/update') }}">
                                {{ csrf_field() }}
                                <div class="card card-bordered card-preview">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="control-label" for="amount">{{ trans_choice('loan::general.amount', 1) }}</label>
                                            <input type="text" name="amount" id="amount" v-model="amount"
                                                class="form-control numeric @error('amount') is-invalid @enderror" required>
                                            @error('amount')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="date"> {{ trans_choice('core.date', 1) }}</label>
                                            <input type="text" v-model="date" class="form-control datepicker @error('date') is-invalid @enderror"
                                                name="date" id="date" required />

                                            @error('date')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div id="payment_details">
                                            <div class="form-group">
                                                <label class="control-label" for="payment_type_id">{{ trans_choice('core.payment', 1) }}
                                                    {{ trans_choice('core.type', 1) }}</label>
                                                <v-select label="name" :options="payment_types" :reduce="payment_type => payment_type.id"
                                                    v-model="payment_type_id">
                                                    <template #search="{attributes, events}">
                                                        <input autocomplete="off" :required="!payment_type_id"
                                                            class="vs__search @error('payment_type_id') is-invalid @enderror" v-bind="attributes"
                                                            v-on="events" />
                                                    </template>
                                                </v-select>
                                                <input type="hidden" name="payment_type_id" v-model="payment_type_id">
                                                @error('payment_type_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label" for="receipt">{{ trans_choice('core.receipt', 1) }}#</label>
                                                <input type="text" name="receipt" id="receipt" v-model="receipt"
                                                    class="form-control @error('receipt') is-invalid @enderror">
                                                @error('receipt')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="description">{{ trans_choice('core.description', 1) }}</label>
                                            <textarea type="text" name="description" v-model="description" id="description"
                                                class="form-control @error('description') is-invalid @enderror">
                                                                                        </textarea>
                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="card-footer border-top ">
                                        <button type="submit"
                                            class="btn btn-primary  float-right">{{ trans_choice('core.save', 1) }}</button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>

@stop
@section('javascript')
    <script>
        var app = new Vue({
            el: '#vue-app',
            data: {
                amount: "{{ old('amount', $loan_transaction->amount) }}",
                date: "{{ old('date', $loan_transaction->submitted_on) }}",
                payment_type_id: "{{ old('payment_type_id', $loan_transaction->payment_detail->payment_type_id) }}",
                account_number: "{{ old('account_number', $loan_transaction->payment_detail->account_number) }}",
                cheque_number: "{{ old('cheque_number', $loan_transaction->payment_detail->cheque_number) }}",
                routing_code: "{{ old('routing_code', $loan_transaction->payment_detail->routing_code) }}",
                receipt: "{{ old('receipt', $loan_transaction->payment_detail->receipt) }}",
                bank_name: "{{ old('bank_name', $loan_transaction->payment_detail->bank_name) }}",
                description: `{{ old('description', $loan_transaction->payment_detail->description) }}`,
                payment_types: {!! json_encode($payment_types) !!},
            }
        });
    </script>
@endsection
