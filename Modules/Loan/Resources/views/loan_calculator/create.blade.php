@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.calculator', 1) }}
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
                            <form method="post" action="{{ url('contact_loan/calculator') }}">
                                {{ csrf_field() }}
                                <div class="card card-bordered card-preview">
                                    <div class="card-body">
                                        <input name="loan_product_id" v-model="loan_product_id" type="hidden">
                                        <div class="form-group">
                                            <label for="loan_product" class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                                                {{ trans_choice('loan::general.product', 1) }}</label>
                                            <v-select label="name" :options="loan_products" :reduce="loan_product => loan_product.id"
                                                v-model="loan_product_id">
                                                <template #search="{attributes, events}">
                                                    <input autocomplete="off" class="vs__search @error('loan_product_id') is-invalid @enderror"
                                                        v-bind="attributes" v-bind:required="!loan_product_id" v-on="events" />
                                                </template>
                                            </v-select>
                                            <input type="hidden" name="loan_product_id" v-model="loan_product_id">
                                            @error('loan_product_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="row" v-cloak>
                                            <div class="col-md-4" v-if="product.variations">
                                                <div class="form-group">
                                                    <label for="applied_amount" class="control-label">{{ trans_choice('loan::general.principal', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loancreateloanprincipal'))</label>

                                                    <div v-if="product.variations.length > 1">
                                                        <select name="applied_amount" id="applied_amount"
                                                            class="form-control @error('applied_amount') is-invalid @enderror numeric"
                                                            v-model="applied_amount" required>
                                                            <option v-for="variation in product.variations" :value="variation.sell_price_inc_tax">
                                                                @{{ variation.name }} (@{{ currency.code }} @{{ number_format(variation.sell_price_inc_tax) }})
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div v-else>
                                                        <select name="applied_amount" id="applied_amount"
                                                            class="form-control @error('applied_amount') is-invalid @enderror numeric"
                                                            v-model="applied_amount" required>
                                                            <option v-for="variation in product.variations" :value="variation.sell_price_inc_tax">
                                                                @{{ currency.code }} @{{ number_format(variation.sell_price_inc_tax) }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    @error('applied_amount')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="interest_rate" class="control-label">
                                                        {{ trans_choice('loan::general.interest', 1) }} {{ trans_choice('loan::general.rate', 1) }}

                                                    </label>
                                                    <input type="text" name="interest_rate" id="interest_rate" v-model="interest_rate"
                                                        class="form-control @error('interest_rate') is-invalid @enderror text" required>
                                                    @error('interest_rate')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4" v-if="product.variations">
                                                <div class="form-group">
                                                    <label for="interest_rate_type" class="control-label">{{ trans_choice('loan::general.per', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loan_productperterestratetype'))</label>
                                                    <select class="form-control  @error('interest_rate_type') is-invalid @enderror" name="interest_rate_type"
                                                        v-model="interest_rate_type" id="interest_rate_type" required>
                                                        <option value=""></option>
                                                        <option value="month">{{ trans_choice('loan::general.month', 1) }}</option>
                                                        <option value="year">{{ trans_choice('loan::general.year', 1) }}</option>
                                                    </select>
                                                    @error('interest_rate_type')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="loan_term" class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                                                        {{ trans_choice('loan::general.term', 1) }}</label>
                                                    <input type="text" name="loan_term" id="loan_term"
                                                        class="form-control @error('loan_term') is-invalid @enderror numeric" v-model="loan_term" required>
                                                    @error('loan_term')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="repayment_frequency" class="control-label">{{ trans_choice('loan::general.repayment', 1) }}
                                                        {{ trans_choice('loan::general.frequency', 1) }}</label>
                                                    <input type="text" name="repayment_frequency" id="repayment_frequency" v-model="repayment_frequency"
                                                        class="form-control @error('repayment_frequency') is-invalid @enderror numeric" required>
                                                    @error('repayment_frequency')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="repayment_frequency_type"
                                                        class="control-label">{{ trans_choice('core.type', 1) }}</label>
                                                    <select class="form-control  @error('repayment_frequency_type') is-invalid @enderror"
                                                        name="repayment_frequency_type" v-model="repayment_frequency_type" id="repayment_frequency_type"
                                                        required>
                                                        <option value=""></option>
                                                        <option value="days">{{ trans_choice('loan::general.day', 2) }}</option>
                                                        <option value="weeks">{{ trans_choice('loan::general.week', 2) }}</option>
                                                        <option value="months">{{ trans_choice('loan::general.month', 2) }}</option>
                                                    </select>
                                                    @error('repayment_frequency_type')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="expected_disbursement_date"
                                                        class="control-label">{{ trans_choice('loan::general.expected', 1) }}
                                                        {{ trans_choice('loan::general.disbursement', 1) }}
                                                        {{ trans_choice('core.date', 1) }}</label>
                                                    <input type="text" v-model="expected_disbursement_date"
                                                        class="form-control datepicker @error('expected_disbursement_date') is-invalid @enderror"
                                                        name="expected_disbursement_date" id="expected_disbursement_date" required />
                                                    @error('expected_disbursement_date')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="expected_first_payment_date"
                                                        class="control-label">{{ trans_choice('loan::general.expected', 1) }}
                                                        {{ trans_choice('loan::general.first_payment_date', 1) }}</label>
                                                    <input type="text" v-model="expected_first_payment_date"
                                                        class="form-control datepicker @error('expected_first_payment_date') is-invalid @enderror"
                                                        name="expected_first_payment_date" id="expected_first_payment_date" required />

                                                    @error('expected_first_payment_date')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer border-top ">
                                        <button type="submit"
                                            class="btn btn-primary  float-right">{{ trans_choice('loan::general.calculate', 1) }}</button>
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
    <script src="{{ Module::asset('accounting:js/helper-functions.js') }}"></script>
    @php
    $next_month = \Illuminate\Support\Carbon::today()
        ->addMonths(1)
        ->format('Y-m-d');
    @endphp

    <script>
        var app = new Vue({
            el: '#vue-app',
            data: {
                loan_product: "{{ old('loan_product') }}",
                loan_product_id: parseInt("{{ old('loan_product_id') }}"),
                applied_amount: "{{ old('applied_amount') }}",
                loan_term: "{{ old('loan_term') }}",
                repayment_frequency: "{{ old('repayment_frequency') }}",
                repayment_frequency_type: "{{ old('repayment_frequency_type') }}",
                interest_rate: "{{ old('interest_rate') }}",
                expected_disbursement_date: "{{ old('expected_disbursement_date', date('Y-m-d')) }}",
                expected_first_payment_date: "{{ old('expected_first_payment_date', $next_month) }}",
                interest_rate: "{{ old('interest_rate') }}",
                interest_rate_type: "{{ old('interest_rate_type') }}",
                loan_products: {!! json_encode($loan_products) !!},
                currency: {!! json_encode($currency) !!}
            },

            computed: {
                product() {
                    return this.loan_products.find(product => product.id == this.loan_product_id) ?? {
                        variations: null
                    };
                },
            }
        });
        console.log(app);
    </script>
@endsection
