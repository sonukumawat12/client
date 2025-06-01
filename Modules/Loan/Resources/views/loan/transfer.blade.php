@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.transfer', 1) }} {{ trans_choice('loan::general.excess', 2) }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/plugins/vue.custom.css') }}">
@endsection

@section('content')


    <!-- Main content -->
    <section class="content no-print" id="vue-app">

        @component('components.widget')
            @slot('header')
                <div class="box-tools">
                    <a href="{{ url('contact_loan/' . $loan->id . '/show') }}" class="btn btn-default">
                        {{ trans('core.back') }}
                    </a>
                </div>
            @endslot

            @slot('slot')
                <section class="content">
                    <form method="post" action="{{ url('contact_loan/' . $loan->id . '/store_transfer') }}">
                        {{ csrf_field() }}

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="excess">{{ trans_choice('loan::general.excess', 1) }}
                                    {{ trans('loan::general.amount') }}</label>
                                <input type="text" class="form-control" name="excess" v-model="excess" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transfer_to">{{ trans_choice('core.transfer', 1) }} {{ trans('core.to') }}</label>
                                <select name="transfer_to" v-model="transfer_to" class="form-control">
                                    <option value=""></option>
                                    @foreach ($transfer_to_options as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div v-if="transfer_to == 'savings'" class="col-md-6">
                            <div class="form-group">
                                <label for="savings_id" class="control-label">{{ trans_choice('savings::general.savings', 1) }}</label>
                                <v-select label="id" :options="savings" :reduce="savings => savings.id" v-model="savings_id">
                                    <template #search="{attributes, events}">
                                        <input autocomplete="off" class="vs__search @error('savings_id') is-invalid @enderror" v-bind="attributes"
                                            v-bind:required="!savings_id" v-on="events" />
                                    </template>
                                </v-select>
                                <input type="hidden" name="savings_id" v-model="savings_id">
                                @error('savings_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div v-if="transfer_to == 'share'" class="col-md-6">
                            <div class="form-group">
                                <label for="share_id" class="control-label">{{ trans_choice('share::general.share', 1) }}</label>
                                <v-select label="id" :options="shares" :reduce="share => share.id" v-model="share_id">
                                    <template #search="{attributes, events}">
                                        <input autocomplete="off" class="vs__search @error('share_id') is-invalid @enderror" v-bind="attributes"
                                            v-bind:required="!share_id" v-on="events" />
                                    </template>
                                </v-select>
                                <input type="hidden" name="share_id" v-model="share_id">
                                @error('share_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div v-if="transfer_to == 'wallet'" class="col-md-6">
                            <div class="form-group">
                                <label for="wallet_id" class="control-label">{{ trans_choice('wallet::general.wallet', 1) }}</label>
                                <v-select label="id" :options="wallets" :reduce="wallet => wallet.id" v-model="wallet_id">
                                    <template #search="{attributes, events}">
                                        <input autocomplete="off" class="vs__search @error('wallet_id') is-invalid @enderror" v-bind="attributes"
                                            v-bind:required="!wallet_id" v-on="events" />
                                    </template>
                                </v-select>
                                <input type="hidden" name="wallet_id" v-model="wallet_id">
                                @error('wallet_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="date" class="control-label">
                                    {{ trans_choice('core.date', 1) }}
                                </label>
                                <input type="text" class="form-control datepicker" name="date" v-model="date">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="payment_type_id" class="control-label">{{ trans_choice('loan::general.payment', 1) }}
                                    {{ trans_choice('core.type', 1) }}
                                </label>
                                <select class="form-control" name="payment_type_id" id="payment_type_id" v-model="payment_type_id" required>
                                    <option value=""></option>
                                    @foreach ($payment_types as $key)
                                        <option value="{{ $key->id }}">{{ $key->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="amount">{{ trans('core.amount') }}</label>
                                <input type="number" class="form-control" name="amount" v-model="amount" required>
                                <strong v-if="feedback_message">@{{ feedback_message }}</strong>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="receipt">{{ trans_choice('core.transaction', 1) }} #</label>
                                <input type="text" class="form-control" name="receipt" v-model="receipt" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes" class="control-label">{{ trans_choice('core.note', 2) }}</label>
                                <textarea name="notes" class="form-control" id="notes" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" :disabled="is_btn_disabled">
                                    {{ trans_choice('core.save', 1) }}
                                </button>
                            </div>
                        </div>
                    </form>
                </section>
            @endslot
        @endcomponent

    </section>

@stop
@section('javascript')
    <script>
        var app = new Vue({
            el: '#vue-app',
            data() {
                return {
                    excess: parseInt("{{ $excess }}"),
                    amount: "{{ old('amount') }}",
                    date: "{{ old('date', date('Y-m-d')) }}",
                    receipt: "{{ old('receipt') }}",
                    notes: "{{ old('notes') }}",
                    transfer_to: "{{ old('transfer_to') }}",
                    savings_id: parseInt("{{ old('savings_id') }}"),
                    share_id: parseInt("{{ old('share_id') }}"),
                    wallet_id: parseInt("{{ old('wallet_id') }}"),
                    savings: {!! !empty($transfer_to->savings) ? json_encode($transfer_to->savings) : json_encode([]) !!},
                    shares: {!! !empty($transfer_to->share) ? json_encode($transfer_to->share) : json_encode([]) !!},
                    wallets: {!! !empty($transfer_to->wallet) ? json_encode($transfer_to->wallet) : json_encode([]) !!},
                }
            },
            computed: {
                is_btn_disabled() {
                    return !this.amount.length > 0 || this.amount > this.excess;
                },

                feedback_message() {
                    if (!this.is_btn_disabled) {
                        return '';
                    }

                    if (this.amount == '') {
                        return 'Please enter an amount';
                    }

                    if (this.amount > this.excess) {
                        return 'The amount cannot be more than the excess';
                    }
                }
            }
        });
    </script>
@endsection
