@extends('layouts.app')
@section('title')
    {{ trans_choice('core.add', 1) }} {{ trans_choice('loan::general.repayment', 1) }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/plugins/vue.custom.css') }}">
@endsection

@section('content')

    
   

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('slot')
                        <section class="content">
                            <form method="post" action="{{ url('contact_loan/' . $id . '/repayment/store') }}">
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
                                                    class="form-control  @error('receipt') is-invalid @enderror">
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
                                                class="form-control @error('description') is-invalid @enderror"></textarea>
                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input name="apply_charge" type="checkbox" v-model="apply_charge">
                                                {{ trans('core.apply') }} {{ trans_choice('core.fee', 1) }}
                                            </label>
                                        </div>
                                    </div>

                                    <transition name="fade">
                                        <div v-show="apply_charge" v-cloak>
                                            @include('loan::loan_linked_charge.partials.create-form')
                                        </div>
                                    </transition>

                                    <div class="card-footer border-top ">
                                        <button type="submit" class="btn btn-primary  float-right">{{ trans_choice('core.save', 1) }}</button>
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

                apply_charge: false,
                loan_charge_id: "{{ old('loan_charge_id') }}",
                charge_amount: "{{ old('charge_amount') }}",
                charge_date: "{{ old('charge_date', date('Y-m-d')) }}",
                charges: {!! json_encode($charges) !!},
                allow_override: true
            },
            methods: {
                setChargeAmount(charge_amount) {
                    this.charge_amount = charge_amount;
                },

                setAllowOverride(allow_override) {
                    //If override is allowed, then readonly is false
                    const value = allow_override == 1;
                    this.allow_override = value;
                },
            },

            watch: {
                loan_charge_id(charge_id) {
                    const charge = !charge_id == "" ? this.charges.find(charge => charge.id == charge_id) : null;

                    if (charge == null) {
                        this.setChargeAmount("");
                        this.setAllowOverride(1);
                        return;
                    }

                    this.setChargeAmount(charge.amount);
                    this.setAllowOverride(charge.allow_override);
                }
            }
        });
    </script>
@endsection
