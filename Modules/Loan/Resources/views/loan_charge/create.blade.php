@extends('loan::settings.layout')
@section('tab-title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.fee', 2) }}
@endsection

@section('tab-content')
    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        <div class="row">

            @component('components.widget')
                @slot('title')
                    {{ trans_choice('core.add', 1) }} {{ trans_choice('loan::general.fee', 1) }}
                @endslot

                @slot('slot')
                    <section class="content">
                        <form method="post" action="{{ url('contact_loan/charge/store') }}">
                            {{ csrf_field() }}
                            <div class="card card-bordered card-preview">
                                <div class="card-body">
                                    <div class="row gy-4">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="name" class="control-label">{{ trans_choice('core.name', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loan_chargename'))</label>
                                                <input type="text" name="name" v-model="name" id="name"
                                                    class="form-control @error('name') is-invalid @enderror" required>
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="loan_charge_type_id" class="control-label">{{ trans_choice('loan::general.fee', 1) }}
                                                    {{ trans_choice('core.type', 1) }}@show_tooltip(__('loan::lang.tooltip_loan_chargetype'))
                                                </label>
                                                <v-select label="name" :options="charge_types" :reduce="charge_type => charge_type.id"
                                                    v-model="loan_charge_type_id">
                                                    <template #search="{attributes, events}">
                                                        <input autocomplete="off" class="vs__search @error('loan_charge_type_id') is-invalid @enderror"
                                                            :required="!loan_charge_type_id" v-bind="attributes" v-on="events" />
                                                    </template>
                                                </v-select>
                                                <input type="hidden" name="loan_charge_type_id" v-model="loan_charge_type_id">
                                                @error('loan_charge_type_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="amount" class="control-label">{{ trans_choice('core.amount', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loan_chargeamount'))</label>
                                                <input type="text" name="amount" v-model="amount" id="amount"
                                                    class="form-control numeric @error('amount') is-invalid @enderror" required>
                                                @error('amount')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="loan_charge_option_id" class="control-label">{{ trans_choice('loan::general.fee', 1) }}
                                                    {{ trans_choice('core.option', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loan_chargeoption'))
                                                </label>
                                                <v-select label="name" :options="charge_options" :reduce="charge_option => charge_option.id"
                                                    v-model="loan_charge_option_id">
                                                    <template #search="{attributes, events}">
                                                        <input autocomplete="off" class="vs__search @error('loan_charge_type_id') is-invalid @enderror"
                                                            :required="!loan_charge_option_id" v-bind="attributes" v-on="events" />
                                                    </template>
                                                </v-select>
                                                <input type="hidden" name="loan_charge_option_id" v-model="loan_charge_option_id">
                                                @error('loan_charge_option_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="currency_id" class="control-label">{{ trans_choice('core.currency', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loan_chargecurrency'))
                                                </label>
                                                <v-select label="currency" :options="currencies" :reduce="currency => currency.id"
                                                    v-model="currency_id">
                                                    <template #search="{attributes, events}">
                                                        <input autocomplete="off" class="vs__search @error('currency_id') is-invalid @enderror"
                                                            :required="!currency_id" v-bind="attributes" v-on="events" />
                                                    </template>
                                                </v-select>
                                                <input type="hidden" name="currency_id" v-model="currency_id">
                                                @error('currency_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="is_penalty" class="control-label">{{ trans_choice('loan::general.penalty', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loan_chargepenalty'))</label>
                                                <select v-model="is_penalty" class="form-control @error('is_penalty') is-invalid @enderror"
                                                    name="is_penalty" id="is_penalty" required>
                                                    <option value="0" selected>{{ trans_choice('core.no', 1) }}</option>
                                                    <option value="1">{{ trans_choice('core.yes', 1) }}</option>
                                                </select>
                                                @error('is_penalty')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="allow_override" class="control-label">{{ trans('loan::general.override') }}
                                                    @show_tooltip(__('loan::lang.tooltip_loan_chargeoverride'))</label>
                                                <select v-model="allow_override" class="form-control @error('allow_override') is-invalid @enderror"
                                                    name="allow_override" id="allow_override" required>
                                                    <option value="0" selected>{{ trans_choice('core.no', 1) }}</option>
                                                    <option value="1">{{ trans_choice('core.yes', 1) }}</option>
                                                </select>
                                                @error('allow_override')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="active"
                                                    class="control-label">{{ trans('core.active') }}@show_tooltip(__('loan::lang.tooltip_loan_chargeactives'))</label>
                                                <select v-model="active" class="form-control @error('active') is-invalid @enderror" name="active"
                                                    id="active" required>
                                                    <option value="0" selected>{{ trans_choice('core.no', 1) }}</option>
                                                    <option value="1">{{ trans_choice('core.yes', 1) }}</option>
                                                </select>
                                                @error('active')
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
                                        class="btn btn-primary  float-right">{{ trans_choice('core.save', 1) }}</button>
                                </div>
                            </div><!-- .card-preview -->
                        </form>
                    </section>
                @endslot
            @endcomponent

        </div>
    </section>
@endsection

@section('tab-javascript')
    <script>
        var app = new Vue({
            el: "#vue-app",
            data: {
                name: "{{ old('name') }}",
                currency_id: parseInt("{{ old('currency_id') }}"),
                loan_charge_option_id: parseInt("{{ old('loan_charge_option_id') }}"),
                loan_charge_type_id: parseInt("{{ old('loan_charge_type_id') }}"),
                amount: "{{ old('amount') }}",
                active: "{{ old('active', 1) }}",
                is_penalty: "{{ old('is_penalty', 0) }}",
                allow_override: "{{ old('allow_override', 0) }}",
                charge_types: {!! json_encode($charge_types) !!},
                charge_options: {!! json_encode($charge_options) !!},
                currencies: {!! json_encode($currencies) !!},
            }
        })
    </script>
@endsection
