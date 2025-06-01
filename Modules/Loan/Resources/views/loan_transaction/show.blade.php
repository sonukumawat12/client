@extends('layouts.app')

@section('title')
    {{ trans_choice('loan::general.transaction', 1) }} {{ trans_choice('core.detail', 2) }}
@endsection

@section('content')


    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        <div class="row">

            @component('components.widget')
                @slot('header')
                    <div class="box-tools">
                        <a href="{{ url('contact_loan/' . $loan_transaction->loan->id . '/show') }}" class="btn btn-outline-light bg-white">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core.back', 1) }}</span>
                        </a>
                        <a href="#" class="btn btn-info btn-sm send-notification d-none"
                            action="{{ action('NotificationController@getTemplate', [$contact->id, 'general']) }}"
                            data-type="loan">{{ trans_choice('core.send', 1) }}</a>
                        <a href="{{ url('contact_loan/transaction/' . $loan_transaction->id . '/pdf') }}" target="_blank"
                            class="btn btn-info btn-sm">{{ trans_choice('core.pdf', 1) }}
                        </a>
                        <a href="{{ url('contact_loan/transaction/' . $loan_transaction->id . '/print') }}" target="_blank"
                            class="btn btn-info btn-sm">{{ trans_choice('core.print', 1) }}
                        </a>
                    </div>
                @endslot

                @slot('slot')
                    @include('loan::loan_transaction.partials.loan_transaction_body')
                @endslot
            @endcomponent

        </div>
    </section>

@stop
@section('javascript')
    <script src="{{ Module::asset('accounting:js/notification.js') }}"></script>
@endsection
