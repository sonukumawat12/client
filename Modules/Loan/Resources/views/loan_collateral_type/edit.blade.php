@extends('loan::settings.layout')
@section('tab-title')
    {{ trans_choice('loan::general.collateral', 1) }} {{ trans_choice('core.type', 2) }}
@endsection

@section('tab-content')
    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('title')
                        {{ trans_choice('core.edit', 1) }} {{ trans_choice('core.type', 1) }}
                    @endslot

                    @slot('slot')
                        <section class="content">
                            <form method="post" action="{{ url('contact_loan/collateral_type/' . $loan_collateral_type->id . '/update') }}">
                                {{ csrf_field() }}
                                <div class="card card-bordered card-preview">
                                    <div class="card-body">
                                        <div class="row gy-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name" class="control-label">{{ trans_choice('core.name', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loancollateraltypessettingsname'))</label>
                                                    <input type="text" name="name" v-model="name" id="name"
                                                        class="form-control @error('name') is-invalid @enderror" required>
                                                    @error('name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer border-top ">
                                        <button type="submit" class="btn btn-primary  float-right">{{ trans_choice('core.save', 1) }}</button>
                                    </div>
                                </div><!-- .card-preview -->
                            </form>
                        </section>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>
@endsection

@section('tab-javascript')
    <script>
        var app = new Vue({
            el: "#vue-app",
            data: {
                name: "{{ old('name', $loan_collateral_type->name) }}",
            }
        })
    </script>
@endsection
