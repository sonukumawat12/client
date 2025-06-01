<div class="row">
    @component('components.widget')
        @slot('slot')
            <form method="get" action="{{ Request::url() }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="start_date">{{ trans_choice('core.start_date', 1) }}</label>
                                <input type="text" value="{{ $start_date }}"
                                    class="form-control datepicker @error('start_date') is-invalid @enderror" name="start_date" id="start_date" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="end_date">{{ trans_choice('core.end_date', 1) }}</label>
                                <input type="text" value="{{ $end_date }}"
                                    class="form-control datepicker @error('end_date') is-invalid @enderror" name="end_date" id="end_date" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="location_id">{{ trans_choice('core.location', 1) }}</label>
                                <select class="form-control select2" name="location_id" id="location_id">
                                    <option value="" selected>{{ trans_choice('core.all', 1) }}
                                        {{ trans_choice('core.location', 2) }}</option>
                                    @foreach ($business_locations as $key)
                                        <option value="{{ $key->id }}" @if (Request::get('location_id') == $key->id) selected @endif>
                                            {{ $key->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="loan_officer_id" class="control-label">{{ trans_choice('loan::general.loan_officer', 1) }}
                                @show_tooltip(__('loan::lang.tooltip_loanindexofficer'))</label>
                            <select class="form-control" name="loan_officer_id" id="loan_officer_id" v-model="loan_officer_id">
                                <option value="">{{ trans_choice('core.all', 1) }}
                                    {{ trans_choice('loan::general.loan_officer', 2) }}</option>
                                @foreach ($loan_officers as $loan_officer)
                                    <option value="{{ $loan_officer->id }}">{{ $loan_officer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="loan_product_id" class="control-label">{{ trans_choice('loan::general.loan_product', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanindexproduct'))</label>
                                <select class="form-control" name="loan_product_id" id="loan_product_id" v-model="loan_product_id">
                                    <option value="">{{ trans_choice('core.all', 1) }}
                                        {{ trans_choice('loan::general.loan_product', 2) }}</option>
                                    @foreach ($loan_products as $loan_product)
                                        <option value="{{ $loan_product->id }}">{{ $loan_product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="row">
                        <div class="col-xl-2 col-lg-2 col-md-2 col-xs-2">
                            <span class="input-group-btn">
                                <button type="submit" class="btn bg-olive btn-flat">{{ trans_choice('core.filter', 1) }}
                                </button>
                            </span>
                            <span class="input-group-btn">
                                <a href="{{ Request::url() }}"
                                    class="btn bg-purple  btn-flat pull-right">{{ trans_choice('core.reset', 1) }}!
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        @endslot
    @endcomponent
</div>
