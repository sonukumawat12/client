<div class="row gy-4" id="change_approval_officers_div">
    <div class="col-md-3">
        <div class="form-group">
            <label for="loan_purpose_id" class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                {{ trans_choice('loan::general.purpose', 1) }} @show_tooltip(__('loan::lang.tooltip_loancreatepurpose'))</label>
            {!! Form::select('loan_purpose_id', $loan_purposes, null, ['class' => 'form-control select2', 'id' => 'loan_purpose_id']) !!}
            
            @error('loan_purpose_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-3 @if (Request::get('action') == 'change_approval_officers') border border-success @endif">
        <div class="form-group">
            <label for="loan_approval_officers" class="control-label">
                {{ trans_choice('loan::general.loan_officers_to_approve_loan', 2) }}
                @show_tooltip(__('loan::lang.tooltip_loan_approval_officers'))
            </label>
            {!! Form::select('loan_approval_officers', $users, null, ['class' => 'form-control select2', 'id' => 'loan_approval_officers','multiple']) !!}
            @error('loan_approval_officers')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            @if (Request::get('action') == 'change_approval_officers')
                <span class="text-success">{{ trans('loan::general.select_one_or_more_officers_here') }}</span>
            @endif
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="grace_on_principal_paid" class="control-label">{{ trans_choice('loan::general.grace_on_principal_paid', 1) }}
                @show_tooltip(__('loan::lang.tooltip_loan_productgraceonprincipalpayment'))</label>
            <input type="text" name="grace_on_principal_paid" value="0" id="grace_on_principal_paid"
                v-model="grace_on_principal_paid" class="form-control numeric @error('grace_on_principal_paid') is-invalid @enderror"
                required>
            @error('grace_on_principal_paid')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="grace_on_interest_paid" class="control-label">{{ trans_choice('loan::general.grace_on_interest_paid', 1) }}
                @show_tooltip(__('loan::lang.tooltip_loan_productgraceoninterestpayment'))</label>
            <input type="text" name="grace_on_interest_paid" value="0" id="grace_on_interest_paid" v-model="grace_on_interest_paid"
                class="form-control numeric @error('grace_on_interest_paid') is-invalid @enderror" required>
            @error('grace_on_interest_paid')
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
            <label for="grace_on_interest_charged" class="control-label">{{ trans_choice('loan::general.grace_on_interest_charged', 1) }}
                @show_tooltip(__('loan::lang.tooltip_loan_productgraceoninterestcharged'))</label>
            <input type="text" name="grace_on_interest_charged" value="0" id="grace_on_interest_charged"
                v-model="grace_on_interest_charged" class="form-control numeric @error('grace_on_interest_charged') is-invalid @enderror"
                required>
            @error('grace_on_interest_charged')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="interest_methodology" class="control-label">{{ trans_choice('loan::general.interest_methodology', 1) }}
                @show_tooltip(__('loan::lang.tooltip_loan_productinterestmethodology'))</label>
            <select class="form-control  @error('interest_methodology') is-invalid @enderror" name="interest_methodology"
                v-model="interest_methodology" id="interest_methodology" required>
                <option value="flat">{{ trans_choice('loan::general.flat', 1) }}</option>
                <option value="declining_balance">{{ trans_choice('loan::general.declining_balance', 1) }}
                </option>
            </select>
            @error('interest_methodology')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="amortization_method" class="control-label">{{ trans_choice('loan::general.amortization_method', 1) }}
                @show_tooltip(__('loan::lang.tooltip_loan_productamortizationmethod'))</label>
            {!! Form::select('amortization_method', $amortization_methods, null, ['class' => 'form-control select2', 'id' => 'amortization_method']) !!}
            
            @error('amortization_method')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="loan_transaction_processing_strategy_id"
                class="control-label">{{ trans_choice('loan::general.loan_transaction_processing_strategy', 1) }}
                @show_tooltip(__('loan::lang.tooltip_loan_productloantransactionprocessingstrategy'))</label>
            {!! Form::select('loan_transaction_processing_strategy_id', $loan_transaction_processing_strategies, null, ['class' => 'form-control select2', 'id' => 'loan_transaction_processing_strategy_id']) !!}
            
            @error('loan_transaction_processing_strategy_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>

