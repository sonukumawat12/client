@extends('loan::settings.layout')
@section('tab-title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.purpose', 2) }}
@endsection

@section('tab-content')
    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('title')
                        {{ $loan_purpose->name }}
                    @endslot

                    @slot('slot')
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h6 class="box-title">{{ $loan_purpose->name }}</h6>

                                <div class="heading-elements">

                                </div>
                            </div>

                            <div class="box-body">

                            </div>
                        </div>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>
@endsection

@section('tab-javascript')
@endsection
