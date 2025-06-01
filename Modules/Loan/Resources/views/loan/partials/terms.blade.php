<div v-show="loan_product">
    <div class="row gy-4">

        <div class="col-md-3" v-if="product.variations">

            <div class="form-group">
                <label for="variation_id" class="control-label">{{ trans_choice('loan::general.principal', 1) }}
                    @show_tooltip(__('loan::lang.tooltip_loancreateloanprincipal'))</label>

                    {!! Form::text('applied_amount', null, ['class' => 'form-control required input_number', 'id' => 'applied_amount']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group" v-if="loan_product">
                <label for="interest_rate" class="control-label">
                    {{ trans_choice('loan::general.interest', 1) }}
                    {{ trans_choice('loan::general.rate', 1) }} @show_tooltip(__('loan::lang.tooltip_loancreateloaninterestrate'))
                    <span v-if="interest_rate_type=='month'">
                        (% {{ trans_choice('loan::general.per', 1) }}
                        {{ trans_choice('loan::general.month', 1) }})
                    </span>

                    <span v-if="interest_rate_type=='year'">
                        (% {{ trans_choice('loan::general.per', 1) }}
                        {{ trans_choice('loan::general.year', 1) }}
                        )
                    </span>
                </label>
                <input type="text" name="interest_rate" id="interest_rate" v-model="interest_rate"
                    class="form-control @error('interest_rate') is-invalid @enderror text" required>
                @error('interest_rate')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="interest_rate_type" class="control-label">{{ trans_choice('loan::general.per', 1) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productperterestratetype'))</label>
                <select class="form-control  @error('interest_rate_type') is-invalid @enderror" name="interest_rate_type"
                    v-model="interest_rate_type" id="interest_rate_type" required>
                    <option value="month">{{ trans_choice('loan::general.month', 1) }}</option>
                    <option value="year">{{ trans_choice('loan::general.year', 1) }}</option>
                </select>
                @error('interest_rate_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
        <transition name="fade" v-if="is_contact_type_chosen">
            <div class="form-group">
                <label for="external_id">{{ trans('core.external_id') }}</label>
                <input type="text" class="form-control" name="external_id" id="external_id" v-model="external_id"
                    @if (!empty($loan->external_id)) readonly @endif >
                @if (empty($loan->external_id))
                    <span class="text-muted">
                        {{ trans('lang_v1.leave_empty_to_autogenerate') }}
                    </span>
                @endif
            </div>
        </transition>
    </div>
    </div>

    <div class="row gy-4">
        <div class="col-md-3">
            <div class="form-group">
                <label for="loan_term" class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                    {{ trans_choice('loan::general.term', 1) }} @show_tooltip(__('loan::lang.tooltip_loancreateloanterm'))</label>
                <input type="text" name="loan_term" id="loan_term" class="form-control @error('loan_term') is-invalid @enderror numeric"
                    v-model="loan_term" required>
                @error('loan_term')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="repayment_frequency" class="control-label">{{ trans_choice('loan::general.repayment', 1) }}
                    {{ trans_choice('loan::general.frequency', 1) }}
                    @show_tooltip(__('loan::lang.tooltip_loancreateloanrepaymentfrequency'))</label>
                <input type="text" name="repayment_frequency" id="repayment_frequency" v-model="repayment_frequency"
                    class="form-control @error('repayment_frequency') is-invalid @enderror numeric" required>
                @error('repayment_frequency')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="repayment_frequency_type" class="control-label">{{ trans_choice('core.type', 1) }}
                    @show_tooltip(__('loan::lang.tooltip_loancreateloanrepaymentfrequencytype'))</label>
               {!! Form::select('repayment_frequency_type', $repayment_frequency_types, null, ['class' => 'form-control select2 variation_id',
                  'style' => 'width:100%', 'id' => 'repayment_frequency_type', 'placeholder' => __('lang_v1.all')]); !!}
                @error('repayment_frequency_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="expected_disbursement_date" class="control-label">{{ trans_choice('loan::general.expected', 1) }}
                    {{ trans_choice('loan::general.disbursement', 1) }}
                    {{ trans_choice('core.date', 1) }}
                    @show_tooltip(__('loan::lang.tooltip_loancreateloanexpecteddisbursementdate'))</label>
                <input type="date" v-model="expected_disbursement_date"
                    class="form-control datepicker @error('expected_disbursement_date') is-invalid @enderror"
                    name="expected_disbursement_date" id="expected_disbursement_date" required />
                @error('expected_disbursement_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row gy-4">
        <div class="col-md-3">
            <div class="form-group">
                <label for="loan_officer_id" class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                    {{ trans_choice('loan::general.officer', 1) }} @show_tooltip(__('loan::lang.tooltip_loancreateofficer'))</label>
                {!! Form::select('loan_officer_id', $users, null, ['class' => 'form-control select2', 'id' => 'loan_officer_id']) !!}
                @error('loan_officer_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="expected_first_payment_date" class="control-label">{{ trans_choice('loan::general.expected', 1) }}
                    {{ trans_choice('loan::general.first_payment_date', 1) }}
                    @show_tooltip(__('loan::lang.tooltip_loancreateexpectedfirstrepaymentdate'))</label>
                <input type="date" v-model="expected_first_payment_date"
                    class="form-control datepicker  @error('expected_first_payment_date') is-invalid @enderror"
                    name="expected_first_payment_date" id="expected_first_payment_date" required />
                @error('expected_first_payment_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
      <div class="form-group">
          {!! Form::label('variation_id', __('lang_v1.variations') . ':') !!}
          {!! Form::select('variation_id', $variations, null, ['class' => 'form-control select2 variation_id',
          'style' =>
          'width:100%', 'id' => 'variation_id', 'placeholder' => __('lang_v1.all')]); !!}
          </div>
      </div>
    </div>

</div>
