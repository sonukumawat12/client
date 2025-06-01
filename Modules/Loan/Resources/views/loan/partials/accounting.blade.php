<div class="row gy-4">
    <div class="col-md-3">
        <div class="form-group">
            <label for="accounting_rule" class="control-label">{{ trans_choice('loan::general.accounting_rule', 1) }}
                @show_tooltip(__('loan::lang.tooltip_loan_productaccountingrule'))</label>
            <select class="form-control  @error('accounting_rule') is-invalid @enderror" name="accounting_rule" v-model="accounting_rule"
                id="accounting_rule" required>
                <option value="none" selected>{{ trans_choice('loan::general.none', 1) }}</option>
                <option value="cash">{{ trans_choice('loan::general.cash', 1) }}</option>
                <option value="accrual_periodic">{{ trans_choice('loan::general.accrual_periodic', 1) }}</option>
                <option value="accrual_upfront">{{ trans_choice('loan::general.accrual_upfront', 1) }}</option>
            </select>
            @error('accounting_rule')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
            <div class="form-group">
                <label for="fund_source_chart_of_account_id" class="control-label">{{ trans_choice('loan::general.fund_source', 1) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productaccountingrulefundsource'))</label><br>
                {!! Form::select('fund_source_chart_of_account_id', $assets, null, ['class' => 'form-control select2', 'id' => 'fund_source_chart_of_account_id']) !!}
            
            @error('fund_source_chart_of_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="loan_portfolio_chart_of_account_id"
                class="control-label">{{ trans_choice('loan::general.loan_portfolio', 1) }}
                @show_tooltip(__('loan::lang.tooltip_loan_productaccountingruleloanportfolio'))</label><br>
           {!! Form::select('loan_portfolio_chart_of_account_id', $assets, null, ['class' => 'form-control select2', 'id' => 'loan_portfolio_chart_of_account_id']) !!}
        
            @error('loan_portfolio_chart_of_account_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
            <div class="form-group">
                <label for="suspended_income_chart_of_account_id"
                    class="control-label">{{ trans_choice('loan::general.suspended_income', 1) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productaccountingrulesuspendedincome'))</label>
                {!! Form::select('suspended_income_chart_of_account_id', $assets, null, ['class' => 'form-control select2', 'id' => 'suspended_income_chart_of_account_id']) !!}
            
                @error('suspended_income_chart_of_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
</div>

<div id="accounting_rule_div"
    >
   
    <div class="row gy-4">
        <div class="col-md-3">
                        <div class="form-group">
                            <label for="interest_receivable_chart_of_account_id"
                                class="control-label">{{ trans_choice('loan::general.interest_receivable', 2) }}
                                @show_tooltip(__('loan::lang.tooltip_loan_product_accounting_rule_interest_receivable'))</label><br>
                            {!! Form::select('interest_receivable_chart_of_account_id', $assets, null, ['class' => 'form-control select2', 'id' => 'interest_receivable_chart_of_account_id']) !!}
                    
                            @error('interest_receivable')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="fees_receivable" class="control-label">{{ trans_choice('loan::general.fees_receivable', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_product_accounting_rule_fees_receivable'))</label><br>
                {!! Form::select('fees_receivable_chart_of_account_id', $assets, null, ['class' => 'form-control select2', 'id' => 'fees_receivable_chart_of_account_id']) !!}
                @error('fees_receivable_chart_of_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
                <div class="form-group">
                    <label for="penalties_receivable_chart_of_account_id"
                        class="control-label">{{ trans_choice('loan::general.penalties_receivable', 2) }}
                        @show_tooltip(__('loan::lang.tooltip_loan_product_accounting_rule_penalties_receivable'))</label><br>
                    {!! Form::select('penalties_receivable_chart_of_account_id', $assets, null, ['class' => 'form-control select2', 'id' => 'penalties_receivable_chart_of_account_id']) !!}
                    
                    @error('penalties_receivable_chart_of_account_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="transfer_in_suspense_chart_of_account_id"
                    class="control-label">{{ trans_choice('loan::general.transfer_in_suspense', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_product_accounting_rule_transfer_in_suspense'))</label><br>
                {!! Form::select('transfer_in_suspense_chart_of_account_id', $assets, null, ['class' => 'form-control select2', 'id' => 'transfer_in_suspense_chart_of_account_id']) !!}
                    
                @error('transfer_in_suspense_chart_of_account_id')
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
                <label for="income_from_interest_chart_of_account_id"
                    class="control-label">{{ trans_choice('loan::general.income_from_interest', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productaccountingruleincomefrominterest'))</label><br>
                {!! Form::select('income_from_interest_chart_of_account_id', $income, null, ['class' => 'form-control select2', 'id' => 'income_from_interest_chart_of_account_id']) !!}
                
                @error('income_from_interest_chart_of_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="income_from_penalties_chart_of_account_id"
                    class="control-label">{{ trans_choice('loan::general.income_from_penalties', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productaccountingruleincomefrompenalties'))</label><br>
                {!! Form::select('income_from_penalties_chart_of_account_id', $income, null, ['class' => 'form-control select2', 'id' => 'income_from_penalties_chart_of_account_id']) !!}
                
                @error('income_from_penalties_chart_of_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="income_from_fees_chart_of_account_id"
                    class="control-label">{{ trans_choice('loan::general.income_from_fees', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productaccountingruleincomefromfees'))</label><br>
                {!! Form::select('income_from_fees_chart_of_account_id', $income, null, ['class' => 'form-control select2', 'id' => 'income_from_fees_chart_of_account_id']) !!}
                
                @error('income_from_fees_chart_of_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="income_from_recovery_chart_of_account_id"
                    class="control-label">{{ trans_choice('loan::general.income_from_recovery', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productaccountingruleincomefromrecovery'))</label>
                {!! Form::select('income_from_recovery_chart_of_account_id', $income, null, ['class' => 'form-control select2', 'id' => 'income_from_recovery_chart_of_account_id']) !!}
                
                @error('income_from_recovery_chart_of_account_id')
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
                <label for="losses_written_off_chart_of_account_id"
                    class="control-label">{{ trans_choice('loan::general.losses_written_off', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productaccountingrulelosseswrittenoff'))</label>
                {!! Form::select('losses_written_off_chart_of_account_id', $expenses, null, ['class' => 'form-control select2', 'id' => 'losses_written_off_chart_of_account_id']) !!}
                
                @error('losses_written_off_chart_of_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="interest_written_off_chart_of_account_id"
                    class="control-label">{{ trans_choice('loan::general.interest_written_off', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productaccountingruleinterestwrittenoff'))</label>
                {!! Form::select('interest_written_off_chart_of_account_id', $expenses, null, ['class' => 'form-control select2', 'id' => 'interest_written_off_chart_of_account_id']) !!}
                
                @error('interest_written_off_chart_of_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="overpayments_chart_of_account_id" class="control-label">{{ trans_choice('loan::general.overpayment', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_productaccountingruleoverpayments'))</label>
                {!! Form::select('overpayments_chart_of_account_id', $liabilities, null, ['class' => 'form-control select2', 'id' => 'overpayments_chart_of_account_id']) !!}
                
                @error('overpayments_chart_of_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

</div>

<hr class="gray">

<div class="row gy-4">
    <div class="col-md-3">
        <div class="form-group">
            <label for="auto_disburse" class="control-label">{{ trans_choice('loan::general.auto_disburse', 1) }}
                @show_tooltip(__('loan::lang.tooltip_loan_productautodisburse'))</label>
            <select class="form-control  @error('auto_disburse') is-invalid @enderror" name="auto_disburse" id="auto_disburse"
                v-model="auto_disburse" required>
                <option value="0" selected>{{ trans_choice('core.no', 1) }}</option>
                <option value="1">{{ trans_choice('core.yes', 1) }}</option>
            </select>
            @error('auto_disburse')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
