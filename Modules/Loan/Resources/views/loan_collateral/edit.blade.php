@extends('layouts.app')
@section('title')
    {{ trans_choice('core.edit', 1) }} {{ trans_choice('loan::general.collateral', 1) }}
@endsection

@section('content')

    
    

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('header')
                        <div class="box-tools">
                            <a href="{{ url('contact_loan/' . $loan_collateral->loan_id . '/show') }}" class="btn btn-default">
                                {{ trans('core.back') }}
                            </a>
                        </div>
                    @endslot

                    @slot('slot')
                        <section class="content">
                            <form method="post" action="{{ url('contact_loan/collateral/' . $loan_collateral->id . '/update') }}"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="card card-bordered card-preview">
                                    <div class="card-body">

                                        {{-- Required Fields --}}

                                        <div class="alert bg-gray">
                                            {{ trans_choice('core.required_field', 2) }}
                                        </div>

                                        <div class="form-group">
                                            <label for="loan_collateral_type_id" class="control-label">{{ trans_choice('loan::general.type', 1) }}</label>
                                            <v-select label="name" :options="loan_collateral_types"
                                                :reduce="loan_collateral_type => loan_collateral_type.id" v-model="loan_collateral_type_id">
                                                <template #search="{attributes, events}">
                                                    <input autocomplete="off" class="vs__search @error('loan_collateral_type_id') is-invalid @enderror"
                                                        v-bind="attributes" v-bind:required="!loan_collateral_type_id" v-on="events" />
                                                </template>
                                            </v-select>
                                            <input type="hidden" name="loan_collateral_type_id" v-model="loan_collateral_type_id">
                                            <a href="{{ url('contact_loan/collateral_type') }}">
                                                {{ trans('core.add') }}/{{ trans('core.edit') }}
                                            </a>
                                            @error('loan_collateral_type_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="product_name" class="control-label">
                                                {{ trans_choice('core.product', 1) }} {{ trans_choice('core.name', 1) }}
                                            </label>
                                            <input type="text" name="product_name" v-model="product_name" id="product_name"
                                                class="form-control  @error('product_name') is-invalid @enderror">
                                            @error('product_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="registration_date" class="control-label">
                                                {{ trans_choice('loan::general.registration_date', 1) }}
                                            </label>
                                            <input type="text" name="registration_date" v-model="registration_date" id="registration_date"
                                                class="form-control datepicker @error('registration_date') is-invalid @enderror">
                                            @error('registration_date')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="value" class="control-label">{{ trans_choice('loan::general.value', 1) }}</label>
                                            <input type="number" step=".01" name="value" v-model="value" id="value"
                                                class="form-control  @error('value') is-invalid @enderror numeric">
                                            @error('value')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        {{-- Current Status --}}

                                        <div class="alert bg-gray">
                                            {{ trans('core.current') }} {{ trans('core.status') }}:
                                            {{ trans('loan::general.' . $loan_collateral->status) }}

                                            <p class="text-muted">
                                                {{ trans('loan::lang.status_changed') }}:
                                                {{ $status_change_dates->last() }}
                                            </p>

                                            <a href="#" @click="change_status = !change_status" class="text-primary">
                                                <span v-if="!change_status">{{ trans('core.change') }}</span>
                                                <span v-if="change_status">{{ trans('core.cancel') }}</span>
                                            </a>
                                        </div>

                                        <div v-if="change_status">
                                            @foreach ($statuses as $status => $label)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="{{ $status }}" name="status"
                                                        v-model="status" id="{{ $status }}" :disabled="!change_status">
                                                    <label class="form-check-label" for="{{ $status }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            @endforeach

                                            <div class="form-group">
                                                <label for="status_change_date" class="control-label">
                                                    {{ trans_choice('core.date', 1) }}
                                                    {{ trans_choice('core.of', 1) }}
                                                    {{ trans_choice('core.status', 1) }}
                                                    {{ trans_choice('core.change', 1) }}
                                                </label>
                                                <input type="text" name="status_change_date" v-model="status_change_date" id="status_change_date"
                                                    class="form-control datepicker @error('status_change_date') is-invalid @enderror"
                                                    :disabled="!change_status">
                                                @error('status_change_date')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Optional Fields --}}

                                        <div class="alert bg-gray">
                                            {{ trans_choice('core.optional', 1) }}
                                            {{ trans_choice('core.field', 2) }}
                                        </div>

                                        <div class="form-group">
                                            <label for="serial_number" class="control-label">
                                                {{ trans_choice('core.serial_number', 1) }}
                                            </label>
                                            <input type="text" name="serial_number" v-model="serial_number" id="serial_number"
                                                class="form-control  @error('serial_number') is-invalid @enderror">
                                            @error('serial_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="model_name" class="control-label">
                                                {{ trans_choice('core.model_name', 1) }}
                                            </label>
                                            <input type="text" name="model_name" v-model="model_name" id="model_name"
                                                class="form-control  @error('model_name') is-invalid @enderror">
                                            @error('model_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="model_number" class="control-label">
                                                {{ trans_choice('core.model_number', 1) }}
                                            </label>
                                            <input type="text" name="model_number" v-model="model_number" id="model_number"
                                                class="form-control  @error('model_number') is-invalid @enderror">
                                            @error('model_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="color" class="control-label">
                                                {{ trans_choice('loan::lang.color', 1) }}
                                            </label>
                                            <input type="text" name="color" v-model="color" id="color"
                                                class="form-control  @error('color') is-invalid @enderror">
                                            @error('color')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="manufacture_date" class="control-label">
                                                {{ trans_choice('core.manufacture_date', 1) }}
                                            </label>
                                            <input type="text" name="manufacture_date" v-model="manufacture_date" id="manufacture_date"
                                                class="form-control datepicker @error('manufacture_date') is-invalid @enderror">
                                            @error('manufacture_date')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="condition" class="control-label">
                                                {{ trans_choice('loan::general.condition', 1) }}
                                            </label>
                                            <select type="text" name="condition" v-model="condition" id="condition"
                                                class="form-control @error('condition') is-invalid @enderror">
                                                <option value="" disabled></option>
                                                @foreach ($conditions as $condition => $label)
                                                    <option value="{{ $condition }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('condition')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="address" class="control-label">
                                                {{ trans_choice('core.address', 1) }}
                                            </label>
                                            <input type="text" name="address" v-model="address" id="address"
                                                class="form-control  @error('address') is-invalid @enderror">
                                            <p class="text-muted">
                                                {{ trans('loan::general.address_helper_text') }}
                                            </p>
                                            @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="description" class="control-label">{{ trans_choice('core.description', 1) }}</label>
                                            <textarea type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                                rows="3" v-model="description"></textarea>
                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="photo" class="control-label">{{ trans_choice('core.photo', 1) }}</label>
                                            <input type="file" name="photo" id="photo"
                                                class="form-control @error('photo') is-invalid @enderror">
                                            @error('photo')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="file" class="control-label">{{ trans_choice('core.file', 1) }}</label>
                                            <input type="file" name="file" id="file"
                                                class="form-control @error('file') is-invalid @enderror">
                                            @error('file')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="alert bg-gray">
                                            {{ trans('loan::general.for_vehicles_only') }}
                                        </div>

                                        <div class="form-group">
                                            <label for="registration_number" class="control-label">
                                                {{ trans_choice('core.registration', 1) }}
                                                {{ trans_choice('core.number', 1) }}
                                            </label>
                                            <input type="text" name="registration_number" v-model="registration_number" id="registration_number"
                                                class="form-control  @error('registration_number') is-invalid @enderror">
                                            @error('registration_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="mileage" class="control-label">
                                                {{ trans_choice('loan::general.mileage', 1) }}
                                            </label>
                                            <input type="text" name="mileage" v-model="mileage" id="mileage"
                                                class="form-control  @error('mileage') is-invalid @enderror">
                                            @error('mileage')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="engine_number" class="control-label">
                                                {{ trans_choice('loan::general.engine_number', 1) }}
                                            </label>
                                            <input type="text" name="engine_number" v-model="engine_number" id="engine_number"
                                                class="form-control  @error('engine_number') is-invalid @enderror">
                                            @error('engine_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="card-footer border-top ">
                                        <button type="submit"
                                            class="btn btn-primary float-right">{{ trans_choice('core.save', 1) }}</button>
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
                loan_collateral_types: {!! json_encode($loan_collateral_types) !!},
                //required fields
                loan_collateral_type_id: parseInt("{{ old('loan_collateral_type_id', $loan_collateral->loan_collateral_type_id) }}"),
                product_name: "{{ old('product_name', $loan_collateral->product_name) }}",
                registration_date: "{{ old('registration_date', $loan_collateral->registration_date) }}",
                value: "{{ old('value', $loan_collateral->value) }}",

                //current status fields
                status: "{{ old('status', $loan_collateral->status) }}",
                status_change_date: "{{ old('status_change_date', $status_change_dates->last()) }}",

                //optional fields
                serial_number: "{{ old('serial_number', $loan_collateral->serial_number) }}",
                model_name: "{{ old('model_name', $loan_collateral->model_name) }}",
                model_number: "{{ old('model_number', $loan_collateral->model_number) }}",
                color: "{{ old('color', $loan_collateral->color) }}",
                manufacture_date: "{{ old('manufacture_date', $loan_collateral->manufacture_date) }}",
                condition: "{{ old('condition', $loan_collateral->condition) }}",
                address: "{{ old('address', $loan_collateral->address) }}",
                description: "{{ old('description', $loan_collateral->description) }}",

                //for vehicles only
                registration_number: "{{ old('registration_number', $loan_collateral->registration_number) }}",
                mileage: "{{ old('mileage', $loan_collateral->mileage) }}",
                engine_number: "{{ old('engine_number', $loan_collateral->engine_number) }}",

                change_status: false
            },
        })
    </script>
@endsection
