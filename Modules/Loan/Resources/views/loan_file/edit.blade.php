@extends('layouts.app')
@section('title')
    {{ trans_choice('core.edit', 1) }} {{ trans_choice('loan::general.file', 1) }}
@endsection

@section('content')

    

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('slot')
                        <section class="content">
                            <form method="post" action="{{ url('contact_loan/file/' . $loan_file->id . '/update') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="card card-bordered card-preview">
                                    <div class="card-body">

                                        <div class="form-group">
                                            <label for="name" class="control-label">{{ trans_choice('core.name', 1) }} </label>
                                            <input type="text" name="name" value="{{ old('name') }}" id="name"
                                                class="form-control @error('name') is-invalid @enderror" v-model="name" required>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="file" class="control-label">{{ trans_choice('core.file', 1) }}</label>
                                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror">
                                            @error('file')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="description" class="control-label">{{ trans_choice('core.description', 1) }}</label>
                                            <textarea type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                                v-model="description"></textarea>
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
                name: "{{ old('name', $loan_file->name) }}",
                description: `{{ old('description', $loan_file->description) }}`,
            },
            methods: {
                change_charge() {
                    this.amount = charges[this.loan_charge_id].amount;
                }
            }
        })
    </script>
@endsection
