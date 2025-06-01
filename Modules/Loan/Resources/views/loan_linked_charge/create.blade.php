@extends('layouts.app')
@section('title')
    {{ trans_choice('core.add', 1) }} {{ trans_choice('loan::general.fee', 1) }}
@endsection

@section('content')

    

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        <div class="row">
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
                        <form method="post" action="{{ url('contact_loan/' . $loan->id . '/charge/store') }}">
                            {{ csrf_field() }}
                            <div class="card card-bordered card-preview">
                                @include('loan::loan_linked_charge.partials.create-form')
                                <div class="card-footer border-top ">
                                    <button type="submit" class="btn btn-primary  float-right">{{ trans_choice('core.save', 1) }}</button>
                                </div>
                            </div>
                        </form>
                    </section>
                @endslot
            @endcomponent
        </div>
    </section>

@stop
@section('javascript')
    <script>
        var app = new Vue({
            el: '#vue-app',
            data: {
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
        })
    </script>
@endsection
