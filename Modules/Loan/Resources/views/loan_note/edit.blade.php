@extends('layouts.app')
@section('title')
    {{ trans_choice('core.edit', 1) }} {{ trans_choice('core.note', 1) }}
@endsection

@section('content')


    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('slot')
                        <section class="content">
                            <form method="post" action="{{ url('contact_loan/note/' . $loan_note->id . '/update') }}">
                                {{ csrf_field() }}
                                <div class="card card-bordered card-preview">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="description" class="control-label">{{ trans_choice('core.description', 1) }}</label>
                                            <textarea type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                                v-model="description" required></textarea>
                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
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
                description: `{{ old('description', $loan_note->description) }}`,
            }
        })
    </script>
@endsection
