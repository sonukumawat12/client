@extends('loan::settings.layout')
@section('tab-title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.status', 2) }}
@endsection

@section('tab-content')
    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        <div class="row">

            @component('components.widget')
                @slot('title')
                    {{ trans_choice('core.edit', 1) }} {{ trans_choice('loan::general.status', 1) }}
                @endslot

                @slot('slot')
                    <section class="content">
                        <form method="post" action="{{ url('contact_loan/status/' . $loan_status->id . '/update') }}">
                            @csrf
                            @method('put')
                            <div class="card card-bordered card-preview">
                                <div class="card-body">
                                    <div class="row gy-4">

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="parent_status" class="control-label">
                                                    {{ trans_choice('core.parent', 1) }} {{ trans_choice('core.status', 1) }}
                                                </label>
                                                <select name="parent_status" id="parent_status" v-model="parent_status" class="form-control">
                                                    <option value=""></option>
                                                    <option v-for="(status, key) in parent_statuses" :value="key">@{{ status.name }}
                                                    </option>
                                                </select>
                                                @error('parent_status')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="name" class="control-label">{{ trans_choice('core.name', 1) }}</label>
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
                                                <label for="active">
                                                    <input type="checkbox" id="active" name="active" v-model="active" />
                                                    {{ trans_choice('core.active', 1) }}
                                                </label>
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
                                    <button type="submit" class="btn btn-primary float-right">{{ trans_choice('core.save', 1) }}</button>
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
                parent_statuses: {!! json_encode($parent_statuses) !!},
                name: "{{ old('name', $loan_status->name) }}",
                parent_status: "{{ old('parent_status', $loan_status->parent_status) }}",
                active: "{{ old('active', $loan_status->active) }}",
            }
        })
    </script>
@endsection
