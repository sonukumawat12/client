<div class="row my-4">
    <div class="col-md-12">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item active">
                        <a href="#account_details" class="nav-link" data-toggle="tab">
                            {{ trans_choice('loan::general.account', 1) }} {{ trans_choice('core.detail', 2) }}
                            @show_tooltip(__('loan::lang.tooltip_loanshowaccountdetails'))
                        </a>
                    </li>
                    @if (in_array($loan->status, ['active', 'fully_paid', 'closed', 'written_off', 'overpaid', 'rescheduled']))
                        <li class="nav-item">
                            <a href="#repayment_schedule" class="nav-link" data-toggle="tab">
                                {{ trans_choice('loan::general.loan', 1) }}
                                {{ trans('loan::general.emi') }}
                                @show_tooltip(__('loan::lang.tooltip_emi_description'))
                            </a>
                        </li>
                        @can('loan.loans.transactions.index')
                            <li class="nav-item">
                                <a href="#pending_dues" class="nav-link" data-toggle="tab">
                                    {{ trans_choice('loan::general.pending', 1) }}
                                    {{ trans_choice('loan::general.due', 2) }}
                                </a>
                            </li>
                        @endcan
                        @can('loan.loans.transactions.index')
                            <li class="nav-item">
                                <a href="#loan_transactions" class="nav-link" data-toggle="tab">
                                    {{ trans_choice('loan::general.transaction', 2) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanshowtransactions'))
                                </a>
                            </li>
                        @endcan
                    @endif
                    @can('loan.loans.charges.index')
                        <li class="nav-item">
                            <a href="#loan_charges" class="nav-link" data-toggle="tab">
                                {{ trans_choice('loan::general.fee', 2) }}
                                @show_tooltip(__('loan::lang.tooltip_loanshowcharges'))
                            </a>
                        </li>
                    @endcan
                    @can('loan.loans.files.index')
                        <li class="nav-item">
                            <a href="#loan_files" class="nav-link" data-toggle="tab">
                                {{ trans_choice('loan::general.documents_and_proof', 2) }} @show_tooltip(__('loan::lang.tooltip_loanshowfiles'))
                            </a>
                        </li>
                    @endcan
                    @can('loan.loans.collateral.index')
                        <li class="nav-item">
                            <a href="#loan_collateral" class="nav-link" data-toggle="tab">
                                {{ trans_choice('loan::general.collateral', 2) }}
                                @show_tooltip(__('loan::lang.tooltip_loanshowcollateral'))
                            </a>
                        </li>
                    @endcan
                    @can('loan.loans.notes.index')
                        <li class="nav-item">
                            <a href="#loan_notes" class="nav-link" data-toggle="tab">
                                {{ trans_choice('core.note', 2) }} @show_tooltip(__('loan::lang.tooltip_loanshownotes'))
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="account_details">
                        <table class="table table-striped table-hover">
                            <tbody>
                                <tr>
                                    <td colspan="2" class="bg-primary">
                                        {{ trans_choice('loan::general.loan', 1) }}
                                        {{ trans_choice('loan::general.term', 2) }}
                                        {{ trans_choice('core.and', 1) }}
                                        {{ trans_choice('core.detail', 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.loan_officer', 1) }}</td>
                                    <td>{{ $loan->loan_officer->user_full_name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.purpose', 1) }}</td>
                                    <td>{{ $loan->loan_purpose->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('core.external_id', 1) }}
                                    </td>
                                    <td>{{ $loan->external_id }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.loan_transaction_processing_strategy', 1) }}</td>
                                    <td>
                                        @if (!empty($loan->loan_transaction_processing_strategy))
                                            {{ $loan->loan_transaction_processing_strategy->translated_name }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.loan', 1) }}
                                        {{ trans_choice('loan::general.term', 1) }}
                                    </td>
                                    <td>
                                        {{ $loan->loan_term }}
                                        {{-- In Loan Model --}}
                                        {{ $loan->repayment_frequency_type_label }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ trans('loan::general.amortization_method') }}
                                    </td>
                                    <td>
                                        {{ trans('loan::general.' . $loan->amortization_method) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.repayment', 2) }}</td>
                                    <td>
                                        {{ trans_choice('loan::general.every', 1) }} {{ $loan->repayment_frequency }}
                                        {{ $loan->repayment_frequency_type_label }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.interest_methodology', 1) }}</td>
                                    <td>
                                        @if ($loan->interest_methodology == 'flat')
                                            {{ trans_choice('loan::general.flat', 1) }}
                                        @elseif ($loan->interest_methodology == 'declining_balance')
                                            {{ trans_choice('loan::general.declining_balance', 1) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.interest', 1) }}</td>
                                    <td>
                                        {{ number_format($loan->interest_rate, 2) }} %
                                        {{ trans_choice('loan::general.per', 1) }}
                                        @if ($loan->interest_rate_type == 'month')
                                            {{ trans_choice('loan::general.month', 1) }}
                                        @elseif ($loan->interest_rate_type == 'year')
                                            {{ trans_choice('loan::general.year', 1) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.grace_on_principal_paid', 1) }}</td>
                                    <td>
                                        {{ $loan->grace_on_principal_paid }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.grace_on_interest_paid', 1) }}</td>
                                    <td>
                                        {{ $loan->grace_on_interest_paid }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.grace_on_interest_charged', 1) }}</td>
                                    <td>
                                        {{ $loan->grace_on_interest_charged }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans('loan::general.interest_free_period') }}</td>
                                    <td>
                                        {{ $loan->interest_free_period }}
                                        {{-- In Loan Model --}}
                                        {{ $loan->repayment_frequency_type_label }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans('loan::general.interest_calculation_period') }}</td>
                                    <td>
                                        {{ $loan->interest_calculation_period }}
                                        {{-- In Loan Model --}}
                                        {{ $loan->repayment_frequency_type_label }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>{{ trans_choice('core.submitted_on', 1) }}</td>
                                    <td>
                                        {{ $loan->submitted_on_date }}
                                        @if (!empty($loan->submitted_by))
                                            {{ trans_choice('core.by', 1) }}
                                            {{ $loan->submitted_by->first_name }} {{ $loan->submitted_by->last_name }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.approved', 1) }}
                                        {{ trans_choice('core.on', 1) }}</td>
                                    <td>
                                        {{ $loan->approved_on_date }}
                                        @if (!empty($loan->approved_by))
                                            {{ trans_choice('core.by', 1) }}
                                            {{ $loan->approved_by->first_name }} {{ $loan->approved_by->last_name }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans_choice('loan::general.disbursed', 1) }}
                                        {{ trans_choice('core.on', 1) }}
                                    </td>
                                    <td>
                                        {{ $loan->disbursed_on_date }}
                                        @if (!empty($loan->disbursed_by))
                                            {{ trans_choice('core.by', 1) }}
                                            {{ $loan->disbursed_by->first_name }} {{ $loan->disbursed_by->last_name }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans('core.maturity_date') }}</td>
                                    <td>
                                        {{ $loan->maturity_date }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-primary">
                                        {{ trans_choice('loan::general.description', 1) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans('core.created_on') }}</td>
                                    <td>{{ @format_datetime($loan->created_at) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('core.last') }} {{ trans('core.updated_at') }}</td>
                                    <td>{{ @format_datetime($loan->updated_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if (in_array($loan->status, ['active', 'fully_paid', 'closed', 'written_off', 'overpaid', 'rescheduled']))
                        <div class="tab-pane" id="repayment_schedule">
                            <div class="row" style="padding: 15px">
                                <div class="btn-group float-right">
                                    <button href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                        {{ trans_choice('core.action', 1) }}
                                        <i class="fas fa-caret-down"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-left">
                                        <a href="{{ url('contact_loan/' . $loan->id . '/schedule/email') }}" class="dropdown-item confirm">
                                            <i class="fas fa-envelope"></i>
                                            {{ trans_choice('core.email', 1) }}
                                            {{ trans_choice('loan::general.emi', 1) }}
                                        </a>

                                        <a href="{{ url('contact_loan/' . $loan->id . '/schedule/print') }}" class="dropdown-item"
                                            target="_blank">
                                            <i class="fas fa-print"></i>
                                            {{ trans_choice('core.print', 1) }}
                                            {{ trans_choice('loan::general.emi', 1) }}
                                        </a>

                                        <a href="{{ url('contact_loan/' . $loan->id . '/schedule/pdf') }}" class="dropdown-item"
                                            target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                            {{ trans_choice('core.download', 1) }}
                                            {{ trans_choice('core.pdf', 1) }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding: 15px">
                                <div class="table-responsive">
                                    <table class="pretty displayschedule datatable" style="margin-top: 20px;">
                                        <colgroup span="3"></colgroup>
                                        <colgroup span="3">
                                            <col class="lefthighlightcol">
                                            <col>
                                            <col class="righthighlightcol">
                                        </colgroup>
                                        <colgroup span="3">
                                            <col class="lefthighlightcol">
                                            <col>
                                            <col class="righthighlightcol">
                                        </colgroup>
                                        <colgroup span="3"></colgroup>
                                        <thead>
                                            <tr>
                                                <th class="empty" scope="colgroup" colspan="5">&nbsp;</th>
                                                <th class="highlightcol" scope="colgroup" colspan="3">
                                                    {{ trans_choice('loan::general.loan_amount_and_balance', 1) }}</th>
                                                <th class="highlightcol" scope="colgroup" colspan="3">
                                                    {{ trans_choice('loan::general.total_cost_of_loan', 1) }}</th>
                                                <th class="empty" scope="colgroup" colspan="1">&nbsp;</th>
                                            </tr>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{ trans_choice('core.date', 1) }}</th>
                                                <th scope="col"># {{ trans_choice('loan::general.day', 2) }}</th>
                                                <th scope="col">{{ trans_choice('loan::general.paid', 1) }}
                                                    {{ trans_choice('core.by', 1) }}</th>
                                                <th scope="col"></th>
                                                <th class="lefthighlightcolheader" scope="col">
                                                    {{ trans_choice('loan::general.disbursement', 1) }}</th>
                                                <th scope="col">{{ trans_choice('loan::general.principal', 1) }}
                                                    {{ trans_choice('loan::general.due', 1) }}</th>
                                                <th class="righthighlightcolheader" scope="col">
                                                    {{ trans_choice('loan::general.principal', 1) }}
                                                    {{ trans_choice('loan::general.balance', 1) }}</th>

                                                <th class="lefthighlightcolheader" scope="col">
                                                    {{ trans_choice('loan::general.interest', 1) }}
                                                    {{ trans_choice('loan::general.due', 1) }}</th>
                                                <th scope="col">{{ trans_choice('loan::general.fee', 2) }}</th>
                                                <th class="righthighlightcolheader" scope="col">
                                                    {{ trans_choice('loan::general.penalty', 2) }}

                                                </th>
                                                <th scope="col">{{ trans_choice('loan::general.total', 1) }}
                                                    {{ trans_choice('loan::general.due', 1) }}</th>
                                                <th scope="col">{{ trans_choice('loan::general.total', 1) }}
                                                    {{ trans_choice('loan::general.paid', 1) }}</th>
                                                <th scope="col">{{ trans_choice('loan::general.total', 1) }}
                                                    {{ trans_choice('loan::general.outstanding', 1) }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td scope="row"></td>
                                                <td>{{ $loan->disbursed_on_date }}</td>
                                                <td></td>
                                                <td><span style="color: #eb2442;"></span></td>
                                                <td>&nbsp;</td>
                                                <td class="lefthighlightcolheader">
                                                    {{ number_format($loan->principal, 2) }}
                                                </td>
                                                <td></td>
                                                <td class="righthighlightcolheader">
                                                    {{ number_format($loan->principal, 2) }}
                                                </td>
                                                <td class="lefthighlightcolheader"></td>
                                                <td>{{ number_format($loan->disbursement_charges, 2) }}</td>
                                                <td class="righthighlightcolheader"></td>
                                                <td>{{ number_format($loan->disbursement_charges, 2) }}</td>
                                                <td>{{ number_format($loan->disbursement_charges, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            <?php
                                            $count = 1;
                                            $total_days = 0;
                                            $total_principal = 0;
                                            $total_interest = 0;
                                            $total_fees = 0 + $loan->disbursement_charges;
                                            $total_penalties = 0;
                                            $total_due = 0;
                                            $total_paid = 0 + $loan->disbursement_charges;
                                            $total_outstanding = 0;
                                            $balance = $loan->principal;
                                            ?>
                                            @foreach ($loan->repayment_schedules as $key)
                                                <?php
                                                $days = \Carbon\Carbon::parse($key->due_date)->diffInDays(\Illuminate\Support\Carbon::parse($key->from_date));
                                                $total_days = $total_days + $days;
                                                $balance = $balance - $key->principal;
                                                $principal = $key->principal - $key->principal_waived_derived - $key->principal_written_off_derived;
                                                $interest = $key->interest - $key->interest_waived_derived - $key->interest_written_off_derived;
                                                $fees = $key->fees - $key->fees_waived_derived - $key->fees_written_off_derived;
                                                $penalties = $key->penalties - $key->penalties_waived_derived - $key->penalties_written_off_derived;
                                                $due = $principal + $interest + $fees + $penalties;
                                                $paid = $key->principal_repaid_derived + $key->interest_repaid_derived + $key->fees_repaid_derived + $key->penalties_repaid_derived;
                                                $outstanding = $due - $paid;
                                                $total_principal = $total_principal + $principal;
                                                $total_interest = $total_interest + $interest;
                                                $total_fees = $total_fees + $fees;
                                                $total_penalties = $total_penalties + $penalties;
                                                $total_due = $total_due + $due;
                                                $total_paid = $total_paid + $paid;
                                                $total_outstanding = $total_outstanding + $outstanding;
                                                ?>
                                                <tr>
                                                    <td scope="row">{{ $count }}</td>
                                                    <td>{{ $key->due_date }}</td>
                                                    <td>{{ $days }}</td>
                                                    <td>
                                                        @if ($outstanding <= 0)
                                                            <span
                                                                style="@if (\Illuminate\Support\Carbon::parse($key->paid_by_date)->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date))) color: #eb2442; @endif">{{ $key->paid_by_date }}</span>
                                                        @elseif($outstanding > 0 && \Illuminate\Support\Carbon::now()->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))
                                                            <span
                                                                style="color: #eb2442;">{{ trans_choice('loan::general.overdue', 1) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($outstanding <= 0)
                                                            @if (\Illuminate\Support\Carbon::parse($key->paid_by_date)->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))
                                                                <i class="fa fa-question-circle"></i>
                                                            @else
                                                                <i class="fa fa-check-circle"></i>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="lefthighlightcolheader"></td>
                                                    <td>{{ number_format($principal, 2) }}</td>
                                                    <td class="righthighlightcolheader">
                                                        {{ number_format($balance, 2) }}
                                                    </td>
                                                    <td class="lefthighlightcolheader">
                                                        {{ number_format($interest, 2) }}
                                                    </td>
                                                    <td>{{ number_format($fees, 2) }}</td>
                                                    <td class="righthighlightcolheader">
                                                        {{ number_format($penalties, 2) }}
                                                    </td>
                                                    <td>{{ number_format($due, 2) }}</td>
                                                    <td>{{ number_format($paid, 2) }}</td>
                                                    <td>{{ number_format($outstanding, 2) }}</td>
                                                </tr>
                                                <?php
                                                $count++;
                                                ?>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="ui-widget-header">
                                            <tr>
                                                <th colspan="2">{{ trans_choice('loan::general.total', 1) }}</th>
                                                <th>{{ $total_days }}</th>
                                                <th></th>
                                                <th></th>
                                                <th class="lefthighlightcolheader">
                                                    {{ number_format($loan->principal, 2) }}
                                                </th>
                                                <th>{{ number_format($total_principal, 2) }}</th>
                                                <th class="righthighlightcolheader">&nbsp;</th>
                                                <th class="lefthighlightcolheader">
                                                    {{ number_format($total_interest, 2) }}
                                                </th>
                                                <th>{{ number_format($total_fees, 2) }}</th>
                                                <th class="righthighlightcolheader">
                                                    {{ number_format($total_penalties, 2) }}
                                                </th>
                                                <th>{{ number_format($total_due, 2) }}</th>
                                                <th>{{ number_format($total_paid, 2) }}</th>
                                                <th>{{ number_format($total_outstanding, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @can('loan.loans.transactions.index')
                            <div class="tab-pane" id="pending_dues">
                                {{-- Based on loan terms --}}
                                <table class="table table-bordered" style="margin-top: 20px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 200px">{{ trans('loan::general.based_on_loan_terms') }}
                                            </th>
                                            <th>{{ trans('core.principal') }}</th>
                                            <th>{{ trans_choice('core.interest', 1) }}</th>
                                            <th>{{ trans_choice('core.fee', 2) }}</th>
                                            <th>{{ trans_choice('core.penalty', 2) }}</th>
                                            <th>{{ trans_choice('core.total', 2) }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="bg-red">{{ trans('loan::general.total_due') }}</td>
                                            {{-- principal --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('total_principal'), 2) }}
                                            </td>
                                            {{-- interest --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('total_interest'), 2) }}
                                            </td>
                                            {{-- fees --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('total_fees'), 2) }}</td>
                                            {{-- penalties --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('total_penalties'), 2) }}
                                            </td>
                                            {{-- due --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('amount_due'), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="bg-green">{{ trans('loan::general.total_paid') }}</td>
                                            {{-- principal --}}
                                            <td>
                                                {{ number_format($loan->repayment_schedules->sum('principal_repaid_derived'), 2) }}
                                            </td>
                                            {{-- interest --}}
                                            <td>
                                                {{ number_format($loan->repayment_schedules->sum('interest_repaid_derived'), 2) }}
                                            </td>
                                            {{-- fees --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('fees_repaid_derived'), 2) }}
                                            </td>
                                            {{-- penalties --}}
                                            <td>
                                                {{ number_format($loan->repayment_schedules->sum('penalties_repaid_derived'), 2) }}
                                            </td>
                                            {{-- paid --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('total_paid'), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="bg-gray">{{ trans('core.balance') }}</td>
                                            {{-- principal --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('principal_due'), 2) }}
                                            </td>
                                            {{-- interest --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('interest_due'), 2) }}
                                            </td>
                                            {{-- fees --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('fees_due'), 2) }}</td>
                                            {{-- penalties --}}
                                            <td>{{ number_format($loan->repayment_schedules->sum('penalties_due'), 2) }}
                                            </td>
                                            {{-- current_balance --}}
                                            <td>{{ number_format($loan->projected_balance, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- Based on loan EMI --}}
                                <table class="table table-bordered" style="margin-top: 20px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 200px">{{ trans('loan::general.based_on_loan_emi') }}
                                            </th>
                                            <th>{{ trans('core.principal') }}</th>
                                            <th>{{ trans_choice('core.interest', 1) }}</th>
                                            <th>{{ trans_choice('core.fee', 2) }}</th>
                                            <th>{{ trans_choice('core.penalty', 2) }}</th>
                                            <th>{{ trans_choice('core.total', 2) }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="bg-red">{{ trans('loan::general.total_due') }}</td>
                                            {{-- principal --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('total_principal'), 2) }}
                                            </td>
                                            {{-- interest --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('total_interest'), 2) }}
                                            </td>
                                            {{-- fees --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('total_fees'), 2) }}
                                            </td>
                                            {{-- penalties --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('total_penalties'), 2) }}
                                            </td>
                                            {{-- due --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('amount_due'), 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="bg-green">{{ trans('loan::general.total_paid') }}</td>
                                            {{-- principal --}}
                                            <td>
                                                {{ number_format($loan->current_repayment_schedules->sum('principal_repaid_derived'), 2) }}
                                            </td>
                                            {{-- interest --}}
                                            <td>
                                                {{ number_format($loan->current_repayment_schedules->sum('interest_repaid_derived'), 2) }}
                                            </td>
                                            {{-- fees --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('fees_repaid_derived'), 2) }}
                                            </td>
                                            {{-- penalties --}}
                                            <td>
                                                {{ number_format($loan->current_repayment_schedules->sum('penalties_repaid_derived'), 2) }}
                                            </td>
                                            {{-- paid --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('total_paid'), 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="bg-gray">{{ trans('core.balance') }}</td>
                                            {{-- principal --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('principal_due'), 2) }}
                                            </td>
                                            {{-- interest --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('interest_due'), 2) }}
                                            </td>
                                            {{-- fees --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('fees_due'), 2) }}
                                            </td>
                                            {{-- penalties --}}
                                            <td>{{ number_format($loan->current_repayment_schedules->sum('penalties_due'), 2) }}
                                            </td>
                                            {{-- current_balance --}}
                                            <td>{{ number_format($loan->balance_today, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="clearfix"></div>

                                {{-- Date Filter --}}
                                <div class="col-md-3" v-cloak>
                                    <div class="form-group d-flex">
                                        <input type="text" class="form-control datepicker" id="pro_rata_date" v-model="pro_rata_date"
                                            placeholder="{{ trans('core.date') }}">
                                        <button class="btn btn-primary" @click="set_pro_rata_date" :disabled="loading.pro_rata_pending_due">
                                            <span v-if="loading.pro_rata_pending_due">{{ trans('core.loading') }}</span>
                                            <span v-if="!loading.pro_rata_pending_due">{{ trans('core.submit') }}</span>
                                        </button>
                                    </div>
                                </div>

                                <table class="table table-bordered" style="margin-top: 20px;" v-cloak>
                                    <thead>
                                        <tr>
                                            <th style="width: 200px">
                                                {{ trans('loan::general.based_on_pro_rata_basis') }}
                                            </th>
                                            <th>{{ trans('core.principal') }}</th>
                                            <th>{{ trans_choice('core.interest', 1) }}</th>
                                            <th>{{ trans_choice('core.fee', 2) }}</th>
                                            <th>{{ trans_choice('core.penalty', 2) }}</th>
                                            <th>{{ trans_choice('core.total', 2) }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="bg-red">{{ trans('loan::general.total_due') }}</td>
                                            {{-- principal --}}
                                            <td>
                                                @{{ pro_rata_pending_due.total_principal }}
                                            </td>
                                            {{-- interest --}}
                                            <td>
                                                @{{ pro_rata_pending_due.total_interest }}
                                            </td>
                                            {{-- fees --}}
                                            <td>
                                                @{{ pro_rata_pending_due.total_fees }}
                                            </td>
                                            {{-- penalties --}}
                                            <td>
                                                @{{ pro_rata_pending_due.total_penalties }}
                                            </td>
                                            {{-- due --}}
                                            <td>
                                                @{{ pro_rata_pending_due.amount_due }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="bg-green">{{ trans('loan::general.total_paid') }}</td>
                                            {{-- principal --}}
                                            <td>
                                                @{{ pro_rata_pending_due.principal_repaid_derived }}
                                            </td>
                                            {{-- interest --}}
                                            <td>
                                                @{{ pro_rata_pending_due.interest_repaid_derived }}
                                            </td>
                                            {{-- fees --}}
                                            <td>
                                                @{{ pro_rata_pending_due.fees_repaid_derived }}
                                            </td>
                                            {{-- penalties --}}
                                            <td>
                                                @{{ pro_rata_pending_due.penalties_repaid_derived }}
                                            </td>
                                            {{-- paid --}}
                                            <td>@{{ pro_rata_pending_due.total_paid }}</td>
                                        </tr>
                                        <tr>
                                            <td class="bg-gray">{{ trans('core.balance') }}</td>
                                            {{-- principal --}}
                                            <td>@{{ pro_rata_pending_due.principal_due }}</td>
                                            {{-- interest --}}
                                            <td>@{{ pro_rata_pending_due.interest_due }}</td>
                                            {{-- fees --}}
                                            <td>@{{ pro_rata_pending_due.fees_due }}</td>
                                            {{-- penalties --}}
                                            <td>@{{ pro_rata_pending_due.penalties_due }}</td>
                                            {{-- current_balance --}}
                                            <td>@{{ pro_rata_pending_due.current_balance }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        @endcan

                        @can('loan.loans.transactions.index')
                            <div class="tab-pane" id="loan_transactions">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="loan_transactions_table">
                                        <thead>
                                            <tr>
                                                <th>{{ trans_choice('core.action', 1) }}</th>
                                                <th>{{ trans_choice('core.created_by', 1) }}</th>
                                                <th>{{ trans_choice('core.date', 1) }}</th>
                                                <th>{{ trans_choice('core.submitted_on', 1) }}</th>
                                                <th>{{ trans_choice('loan::general.transaction', 1) }}
                                                    {{ trans_choice('core.type', 1) }}</th>
                                                <th>{{ trans_choice('loan::general.transaction', 1) }}
                                                    {{ trans_choice('core.id', 1) }}</th>
                                                <th style="text-align:right">{{ trans_choice('general.debit', 1) }}
                                                </th>
                                                <th style="text-align:right">{{ trans_choice('general.credit', 1) }}
                                                </th>
                                                <th style="text-align:right">{{ trans_choice('loan::general.balance', 1) }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $balance = 0;
                                            ?>
                                            @foreach ($loan->transactions->sortBy('id') as $key)
                                                <?php
                                                $balance = $balance + $key->debit - $key->credit;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button href="#" class="btn btn-info dropdown-toggle btn-xs"
                                                                data-toggle="dropdown"
                                                                aria-expanded="false">{{ trans_choice('core.action', 1) }}
                                                                <span class="caret"></span><span class="sr-only"></span>
                                                            </button>

                                                            <div class="dropdown-menu dropdown-menu-left">
                                                                <a href="{{ url('contact_loan/transaction/' . $key->id . '/show') }}"
                                                                    class="dropdown-item"><i class="fas fa-eye"></i>
                                                                    {{ trans_choice('core.view', 2) }}
                                                                </a>
                                                                @if ($key->loan_transaction_type_id == 2 && $key->reversed == 0)
                                                                    <a href="{{ url('contact_loan/transaction/' . $key->id . '/pdf') }}"
                                                                        target="_blank" class="dropdown-item"><i class="fas fa-file-pdf"></i>
                                                                        {{ trans_choice('core.receipt', 1) }}
                                                                    </a>
                                                                    <a href="{{ url('contact_loan/transaction/' . $key->id . '/print') }}"
                                                                        target="_blank" class="dropdown-item"><i class="fas fa-print"></i>
                                                                        {{ trans_choice('core.print', 1) }}
                                                                    </a>
                                                                    @can('loan.loans.transactions.edit')
                                                                        <a href="{{ url('contact_loan/repayment/' . $key->id . '/edit') }}"
                                                                            class="dropdown-item"><i class="fas fa-edit"></i>
                                                                            {{ trans_choice('core.edit', 1) }}
                                                                        </a>
                                                                    @endcan
                                                                    @can('loan.loans.transactions.edit')
                                                                        <a href="{{ url('contact_loan/repayment/' . $key->id . '/reverse') }}"
                                                                            class="dropdown-item confirm"><i class="fas fa-undo"></i>
                                                                            {{ trans_choice('loan::general.reverse', 1) }}
                                                                        </a>
                                                                    @endcan
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $key->created_by->first_name }} {{ $key->created_by->last_name }}
                                                    </td>
                                                    <td>{{ $key->created_on }}</td>
                                                    <td>{{ $key->submitted_on }}</td>
                                                    <td>{{ $key->loan_transaction_type }}</td>
                                                    <td>{{ $key->id }}</td>
                                                    <td style="text-align:right">
                                                        {{ number_format($key->debit, 2) }}
                                                    </td>
                                                    <td style="text-align:right">
                                                        {{ number_format($key->credit, 2) }}
                                                    </td>
                                                    <td style="text-align:right">
                                                        {{ number_format($balance, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endcan
                    @endif

                    @can('loan.loans.charges.index')
                        <div class="tab-pane" id="loan_charges">
                            @can('loan.loans.charges.create')
                                <a href="{{ url('contact_loan/' . $loan->id . '/charge/create') }}"
                                    class="btn btn-info float-right m-2">{{ trans_choice('core.add', 1) }}
                                    {{ trans_choice('loan::general.fee', 1) }}</a>
                            @endcan
                            <table class="table table-striped table-bordered table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>{{ trans_choice('core.name', 1) }}</th>
                                        <th>{{ trans_choice('loan::general.fee', 1) }}
                                            {{ trans_choice('core.option', 1) }}
                                        </th>
                                        <th>{{ trans('core.amount') }}</th>
                                        <th>{{ trans_choice('loan::general.fee', 1) }}
                                            {{ trans_choice('core.type', 1) }}</th>
                                        <th>{{ trans_choice('core.status', 1) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loan->charges as $key)
                                        <tr>
                                            <td>{{ $key->name }}</td>
                                            <td>
                                                @if ($key->loan_charge_option_id == 1)
                                                    {{ number_format($key->amount, 2) }}
                                                    {{ trans_choice('loan::general.flat', 1) }}
                                                @elseif ($key->loan_charge_option_id == 2)
                                                    {{ number_format($key->amount, 2) }}
                                                    % {{ trans_choice('loan::general.principal_due_on_installment', 1) }}
                                                @elseif ($key->loan_charge_option_id == 3)
                                                    {{ number_format($key->amount, 2) }}
                                                    % {{ trans_choice('loan::general.principal_interest_due_on_installment', 1) }}
                                                @elseif ($key->loan_charge_option_id == 4)
                                                    {{ number_format($key->amount, 2) }}
                                                    % {{ trans_choice('loan::general.interest_due_on_installment', 1) }}
                                                @elseif ($key->loan_charge_option_id == 5)
                                                    {{ number_format($key->amount, 2) }}
                                                    % {{ trans_choice('loan::general.total_outstanding_loan_principal', 1) }}
                                                @elseif ($key->loan_charge_option_id == 6)
                                                    {{ number_format($key->amount, 2) }}
                                                    %
                                                    {{ trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1) }}
                                                @elseif ($key->loan_charge_option_id == 7)
                                                    {{ number_format($key->amount, 2) }}
                                                    % {{ trans_choice('loan::general.original_loan_principal', 1) }}
                                                @endif
                                            </td>
                                            <td>{{ number_format($key->calculated_amount, 2) }}</td>
                                            <td>
                                                @if ($key->loan_charge_type_id == 1)
                                                    {{ trans_choice('loan::general.disbursement', 1) }}
                                                @elseif ($key->loan_charge_type_id == 2)
                                                    {{ trans_choice('loan::general.specified_due_date', 1) }}
                                                @elseif ($key->loan_charge_type_id == 3)
                                                    {{ trans_choice('loan::general.installment', 1) }}
                                                    {{ trans_choice('loan::general.fee', 2) }}
                                                @elseif ($key->loan_charge_type_id == 4)
                                                    {{ trans_choice('loan::general.overdue', 1) }}
                                                    {{ trans_choice('loan::general.installment', 1) }}
                                                    {{ trans_choice('loan::general.fee', 1) }}
                                                @elseif ($key->loan_charge_type_id == 5)
                                                    {{ trans_choice('loan::general.disbursement_paid_with_repayment', 1) }}
                                                @elseif ($key->loan_charge_type_id == 6)
                                                    {{ trans_choice('loan::general.loan_rescheduling_fee', 1) }}
                                                @elseif ($key->loan_charge_type_id == 7)
                                                    {{ trans_choice('loan::general.overdue_on_loan_maturity', 1) }}
                                                @elseif ($key->loan_charge_type_id == 8)
                                                    {{ trans_choice('loan::general.last_installment_fee', 1) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($key->loan_charge_type_id == 1 || $key->loan_charge_type_id == 5)
                                                    {{ trans_choice('loan::general.fee', 1) }}
                                                    {{ trans_choice('loan::general.paid', 1) }}
                                                @elseif ($key->waived == 1)
                                                    {{ trans_choice('loan::general.fee', 1) }}
                                                    {{ trans_choice('loan::general.waived', 1) }}
                                                @else
                                                    @can('loan.loans.transactions.edit')
                                                        <a href="{{ url('contact_loan/charge/' . $key->id . '/waive') }}"
                                                            class="btn btn-danger confirm">
                                                            {{ trans_choice('loan::general.waive', 1) }}
                                                            {{ trans_choice('loan::general.fee', 1) }}</a>
                                                    @endcan
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endcan

                    @can('loan.loans.files.index')
                        <div class="tab-pane" id="loan_files">
                            @can('loan.loans.files.create')
                                <a href="{{ url('contact_loan/' . $loan->id . '/file/create') }}"
                                    class="btn btn-info float-right m-2">{{ trans_choice('core.add', 1) }}
                                    {{ trans_choice('loan::general.documents_and_proof', 1) }}
                                </a>
                            @endcan
                            <table class="table table-striped table-bordered table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>{{ trans_choice('core.description', 1) }}</th>
                                        <th>{{ trans_choice('core.action', 1) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loan->media as $media)
                                        <tr>
                                            <td>{{ $media->description_label }}</td>
                                            <td>
                                                <a href="#download"
                                                    onclick="document.getElementById('media-{{ $media->id }}-form').submit()">
                                                    <i class="fa fa-download"></i>
                                                </a>

                                                <a href="#delete" class="confirm-delete"
                                                    form_id="delete-media-{{ $media->id }}-form" action="">
                                                    <i class="fa fa-trash"></i>
                                                </a>

                                                {{-- Download form --}}
                                                <form id="media-{{ $media->id }}-form"
                                                    action=""
                                                    method="post">
                                                    @csrf
                                                    <input type="hidden" name="file_name" value="{{ $media->description_label }}">
                                                </form>

                                                {{-- Delete form --}}
                                                <form id="delete-media-{{ $media->id }}-form" method="post"
                                                    action="#">
                                                    <input type="hidden" name="business_id" value="{{ session('business.id') }}">
                                                    @method('delete')
                                                    @csrf
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endcan

                    @can('loan.loans.collateral.index')
                        <div class="tab-pane" id="loan_collateral">
                            @can('loan.loans.collateral.create')
                                <a href="{{ url('contact_loan/' . $loan->id . '/collateral/create') }}"
                                    class="btn btn-info float-right m-2">{{ trans_choice('core.add', 1) }}
                                    {{ trans_choice('loan::general.collateral', 1) }}</a>
                            @endcan
                            <table class="table table-striped table-bordered table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>{{ trans_choice('loan::general.type', 1) }}</th>
                                        <th>{{ trans_choice('loan::general.value', 1) }}</th>
                                        <th>{{ trans_choice('core.description', 1) }}</th>
                                        <th>{{ trans_choice('core.file', 2) }}</th>
                                        <th>{{ trans_choice('core.action', 1) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loan->collateral as $key)
                                        <tr>
                                            <td>
                                                @if (!empty($key->collateral_type))
                                                    {{ $key->collateral_type->name }}
                                                @endif
                                            </td>
                                            <td>{{ number_format($key->value, 2) }}</td>
                                            <td>{{ $key->description }}</td>
                                            <td>
                                                @if (!empty($key->file->first()))
                                                    {{ trans_choice('core.file', 1) }}:
                                                    <a href="{{ $key->file->first()->display_url }}"
                                                        download>{{ $key->file->first()->display_name }}</a>
                                                @endif
                                                @if (!empty($key->photo->first()))
                                                    {{ trans_choice('core.photo', 1) }}:
                                                    <a href="{{ $key->photo->first()->display_url }}"
                                                        download>{{ $key->photo->first()->display_name }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('loan.loans.collateral.edit')
                                                    <a href="{{ url('contact_loan/collateral/' . $key->id . '/edit') }}"><i
                                                            class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('loan.loans.collateral.destroy')
                                                    <a href="{{ url('contact_loan/collateral/' . $key->id . '/destroy') }}" class="confirm"><em
                                                            class="fas fa-trash"></em> </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endcan

                    @can('loan.loans.notes.index')
                        <div class="tab-pane" id="loan_notes">
                            @can('loan.loans.notes.create')
                                <a href="{{ url('contact_loan/' . $loan->id . '/note/create') }}"
                                    class="btn btn-info float-right m-2">{{ trans_choice('core.add', 1) }}
                                    {{ trans_choice('core.note', 1) }}</a>
                            @endcan

                            <div class="clearfix"></div>

                            <div class="comments-list clearfix">
                                @foreach ($loan->notes as $key)
                                    <div class="media pt-2">
                                        <div class="media-body">
                                            <h4 class="media-heading user_name">
                                                @if (!empty($key->created_by))
                                                    
                                                @endif
                                                <small>{{ trans_choice('core.on', 1) }} {{ $key->created_at }}</small>
                                            </h4>
                                            <p>{{ $key->description }}</p>
                                            <p>
                                                @can('loan.loans.notes.edit')
                                                    <a href="{{ url('contact_loan/note/' . $key->id . '/edit') }}" class="btn btn-xs btn-tool">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('loan.loans.notes.destroy')
                                                    <a href="{{ url('contact_loan/note/' . $key->id . '/destroy') }}"
                                                        class="btn btn-xs btn-tool link-danger confirm">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                @endcan
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                @foreach ($loan->additional_notes as $key)
                                    @if (!empty($key->note))
                                        <div class="media pt-2">
                                            <div class="media-body">
                                                <h4 class="media-heading user_name">
                                                    {{ $key->created_when }}
                                                </h4>
                                                <h4 class="media-heading user_name">
                                                    @if (!empty($key->created_by))
                                                        
                                                    @endif
                                                    <small>{{ trans_choice('core.on', 1) }}
                                                        {{ $key->created_at }}</small>
                                                </h4>
                                                <p>{{ $key->note }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endcan
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
    </div>
</div>
