@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('core.detail', 2) }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/plugins/vue.custom.css') }}">
@endsection

@section('content')

    
    <!-- Main content -->
    <section class="content no-print" id="vue-app">

        @component('components.widget')
            @slot('title')
                {{ $loan->loan_product->name }} (#{{ $loan->id }})
            @endslot

            @slot('header')
                <div class="box-tools">

                    {{-- Pending Approval Buttons --}}
                    @if (in_array($loan->status, ['submitted', 'pending']))
                        @if ($can_approve_loan)
                            @can('loan.loans.approve_loan')
                                <!--Approve Disbursement -->
                                <a href="#" id="btn_approve_loan" class="btn btn-primary">
                                    <i class="fas fa-check"></i>
                                    {{ trans_choice('loan::general.approve', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanshowstatusapproveasinitialdisbursement'))
                                </a>
                                
                                @can('loan.loans.edit')
                                    <a href="{{ url('contact_loan/' . $loan->id . '/edit') }}" class="btn btn-info">
                                        <i class="fa fa-edit"></i>
                                        {{ trans_choice('core.edit', 1) }}
                                        @show_tooltip(__('loan::lang.tooltip_loanindexactionedit'))
                                    </a>
                                @endcan
                                
                                <a href="#" id="btn_reject_loan" class="btn btn-danger">
                                    <i class="fas fa-times"></i> {{ trans_choice('loan::general.reject', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanshowstatusreject'))
                                </a>

                            @endcan
                        @endif
                    @endif

                    {{-- Awaiting Disbursement Buttons --}}
                    @if ($loan->status == 'approved')
                        @can('loan.loans.disburse_loan')
                            <!--Disburse as Initial Disbursement -->
                            <a href="#" data-toggle="modal" data-target="#disburse_loan_modal" class="btn btn-primary"><i class="fas fa-flag"></i>
                                {{ trans_choice('loan::general.disburse', 1) }}
                                @show_tooltip(__('loan::lang.tooltip_loanshowstatusdisburseeasinitialdisbursement'))
                            </a>
                        @endcan

                        @component('components.dropdown_button',
                            [
                                'label' => trans_choice('core.more_action', 2),
                            ])
                            @can('loan.loans.edit')
                                <a href="#" data-toggle="modal" data-target="#change_loan_officer_modal" class="dropdown-item"><i class="fa fa-edit"></i>
                                    {{ trans_choice('loan::general.change', 1) }}
                                    {{ trans_choice('loan::general.loan', 1) }}
                                    {{ trans_choice('loan::general.officer', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loancreateofficer'))
                                </a>
                            @endcan
                            @can('loan.loans.approve_loan')
                                <a href="{{ url('contact_loan/' . $loan->id . '/undo_approval') }}" class="dropdown-item confirm"><i class="fa fa-undo"></i>
                                    {{ trans_choice('loan::general.undo', 1) }}
                                    {{ trans_choice('loan::general.approval', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanshowstatusundoapproval'))
                                </a>
                            @endcan
                        @endcomponent
                    @endif

                    {{-- Rejected Buttons --}}
                    @if ($loan->status == 'rejected')
                        @can('loan.loans.approve_loan')
                            <a href="{{ url('contact_loan/' . $loan->id . '/undo_rejection') }}" class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                {{ trans_choice('loan::general.undo', 1) }}
                                {{ trans_choice('loan::general.rejection', 1) }}
                            </a>
                        @endcan
                    @endif

                    {{-- Active Buttons --}}
                    @if ($loan->status == 'active')
                        @can('loan.loans.transactions.create')
                            <a href="{{ url('contact_loan/' . $loan->id . '/repayment/create') }}" class="btn btn-primary"><i
                                    class="fas fa-dollar-sign"></i>
                                {{ trans_choice('loan::general.make', 1) }}
                                {{ trans_choice('loan::general.repayment', 1) }}
                                @show_tooltip(__('loan::lang.tooltip_loanshowmakerepayment'))
                            </a>
                        @endcan

                        @component('components.dropdown_button',
                            [
                                'label' => trans_choice('core.more_action', 2),
                            ])
                            @can('loan.loans.edit')
                                <a href="#" data-toggle="modal" data-target="#change_loan_officer_modal" class="dropdown-item"><i class="fa fa-edit"></i>
                                    {{ trans_choice('loan::general.change', 1) }} {{ trans_choice('loan::general.loan', 1) }}
                                    {{ trans_choice('loan::general.officer', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loancreateofficer'))
                                </a>
                            @endcan
                            @can('loan.loans.charges.create')
                                <a href="{{ url('contact_loan/' . $loan->id . '/charge/create') }}" class="dropdown-item"><i class="fa fa-plus"></i>
                                    {{ trans_choice('core.add', 1) }} {{ trans_choice('loan::general.fee', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanshowcharges'))
                                </a>
                            @endcan
                            @can('loan.loans.transactions.edit')
                                <a href="#" data-toggle="modal" data-target="#waive_interest_modal" class="dropdown-item"><i
                                        class="fa fa-money-bill-wave"></i>
                                    {{ trans_choice('loan::general.waive', 1) }}
                                    {{ trans_choice('loan::general.interest', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanshowwaiveinterest'))
                                </a>
                            @endcan
                            @can('loan.loans.reschedule_loan')
                                <a href="#" data-toggle="modal" data-target="#reschedule_loan_modal" class="dropdown-item"><i class="fa fa-calendar"></i>
                                    {{ trans_choice('loan::general.reschedule', 1) }}
                                    {{ trans_choice('loan::general.loan', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanshowreschedule'))
                                </a>
                            @endcan
                            @can('loan.loans.disburse_loan')
                                <a href="{{ url('contact_loan/' . $loan->id . '/undo_disbursement') }}" class="dropdown-item text-danger confirm"><i
                                        class="fa fa-undo"></i>
                                    {{ trans_choice('loan::general.undo', 1) }}
                                    {{ trans_choice('loan::general.disbursement', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanshowstatusundodisbursement'))
                                </a>
                            @endcan
                            @can('loan.loans.write_off_loan')
                                <a href="#" data-toggle="modal" data-target="#write_off_loan_modal" class="dropdown-item text-danger"><i
                                        class="fa fa-ban"></i>
                                    {{ trans_choice('loan::general.write_off', 1) }}
                                    {{ trans_choice('loan::general.loan', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanshowwriteoff'))
                                </a>
                            @endcan
                            @can('loan.loans.edit')
                                @if ($loan->status != 'closed')
                                    <a href="#" data-toggle="modal" data-target="#close_loan_modal" class="dropdown-item text-danger"><i
                                            class="fa fa-times"></i>
                                        {{ trans_choice('core.close', 1) }}
                                        {{ trans_choice('loan::general.loan', 1) }}
                                        @show_tooltip(__('loan::lang.tooltip_loanshowclosed'))
                                    </a>
                                @endif
                            @endcan
                        @endcomponent
                    @endif

                    {{-- Written Off Buttons --}}
                    @if ($loan->status == 'written_off')
                        <a href="{{ url('contact_loan/' . $loan->id . '/repayment/create') }}" class="btn btn-primary"><i
                                class="fas fa-dollar-sign"></i>
                            {{ trans_choice('loan::general.recovery', 1) }}
                            {{ trans_choice('loan::general.payment', 1) }}
                            @show_tooltip(__('loan::lang.tooltip_loanshowstatusrecoverypayments'))
                        </a>
                        <a href="{{ url('contact_loan/' . $loan->id . '/undo_write_off') }}" class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                            {{ trans_choice('loan::general.undo', 1) }} {{ trans_choice('loan::general.loan', 1) }}
                            {{ trans_choice('loan::general.write_off', 1) }}
                            @show_tooltip(__('loan::lang.tooltip_loanshowstatuswriteoff'))
                        </a>
                    @endif

                    {{-- Closed Buttons --}}
                    @if ($loan->status == 'closed')
                        <a href="{{ url('contact_loan/' . $loan->id . '/undo_loan_close') }}" class="btn btn-primary confirm"><i
                                class="fa fa-undo"></i>
                            {{ trans_choice('loan::general.undo', 1) }} {{ trans_choice('loan::general.loan', 1) }}
                            {{ trans_choice('core.close', 1) }}
                            @show_tooltip(__('loan::lang.tooltip_loanshowstatusclosed'))
                        </a>
                    @endif
                </div>
            @endslot

            @slot('slot')
                <section class="content">

                    <div class="row mt-2">
                        <div class="col-12">
                            <!--If the loan has not been approved yet one of the banners is displayed-->
                            @if (in_array($loan->status, ['submitted', 'pending']))
                                <!--If the loan does not have officers set -->
                                @if (!count($loan->approval_officers) > 0)
                                    <div class="alert alert-info">
                                        {{ trans('loan::general.select_officers_to_approve') }}
                                        <a href="{{ url('contact_loan/' . $loan->id . '/edit') }}">
                                            {{ trans('loan::general.here') }}
                                        </a>
                                    </div>
                                @else
                                    <!--If the product and loan has officers set to approve the loan-->
                                    <div class="alert alert-info">
                                        {{ trans('loan::general.approval') }}
                                        {{ trans_choice('loan::general.status', 1) }}
                                        <ul>
                                            @foreach ($loan->approval_officers as $officer)
                                                <li>{{ $officer->user_full_name }} :
                                                    {{ ucfirst($officer->pivot->status) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                        {{ trans('core.edit') }}
                                        {{ trans_choice('loan::general.loan_officer', 2) }}
                                        {{ trans_choice('core.to', 1) }}
                                        {{ trans_choice('loan::general.approve', 1) }}
                                        <a href="{{ url('contact_loan/' . $loan->id . '/edit') . '?action=change_approval_officers' }}">
                                            {{ trans('loan::general.here') }}
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="panel-group row">
                        <div class="panel panel-primary">
                            <div class="panel-heading" data-toggle="collapse" href="#collapse_basic_info">
                                <h4 class="panel-title">
                                    <a href="#">
                                        {{ trans('core.basic') }}
                                        {{ trans_choice('loan::general.loan', 1) }}
                                        {{ trans('loan::general.info') }}
                                    </a>
                                    <span class="float-right">
                                        <i class="fa fa-chevron-circle-down"></i>
                                    </span>
                                </h4>
                            </div>
                            <div id="collapse_basic_info" class="panel-collapse collapse in">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card ">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="float-right">
                                                                    @if (in_array($loan->status, ['submitted', 'pending']))
                                                                        @can('loan.loans.approve_loan')
                                                                            <div class="modal fade" id="approve_loan_modal">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">
                                                                                                {{ trans_choice('loan::general.approve', 1) }}
                                                                                                {{ trans_choice('loan::general.loan', 1) }}
                                                                                                @show_tooltip(__('loan::lang.tooltip_loanshowstatusapproveasinitialdisbursement'))
                                                                                            </h4>
                                                                                            <button type="button" class="close" data-dismiss="modal"
                                                                                                aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>

                                                                                        <form method="post"
                                                                                            action="{{ url('contact_loan/' . $loan->id . '/approve_loan') }}">
                                                                                            {{ csrf_field() }}
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label for="approved_on_date"
                                                                                                        class="control-label">{{ trans_choice('core.date', 1) }}</label>
                                                                                                    <input type="text"
                                                                                                        class="form-control datepicker @error('approved_on_date') is-invalid @enderror"
                                                                                                        name="approved_on_date"
                                                                                                        value="{{ date('Y-m-d') }}" id="approved_on_date"
                                                                                                        required>
                                                                                                </div>
                                                                                                @if (count($loan->loan_product->variations) > 1)
                                                                                                    <div class="form-group">
                                                                                                        <label for="approved_amount"
                                                                                                            class="control-label">{{ trans_choice('core.amount', 1) }}</label>
                                                                                                        <input type="text" class="form-control numeric"
                                                                                                            id="approved_amount"
                                                                                                            value="{{ $loan->variation->name . ' (' . $currency->code . ' ' . number_format($loan->applied_amount, 2) . ')' }}"
                                                                                                            required readonly>
                                                                                                    </div>
                                                                                                @else
                                                                                                    <div class="form-group">
                                                                                                        <label for="approved_amount"
                                                                                                            class="control-label">{{ trans_choice('core.amount', 1) }}</label>
                                                                                                        <input type="text" class="form-control numeric"
                                                                                                            id="approved_amount"
                                                                                                            value="{{ $currency->code . ' ' . number_format($loan->applied_amount, 2) }}"
                                                                                                            required readonly>
                                                                                                    </div>
                                                                                                @endif
                                                                                                <input type="hidden" name="approved_amount"
                                                                                                    v-model="approved_amount">
                                                                                                <div class="form-group">
                                                                                                    <label for="approved_notes"
                                                                                                        class="control-label">{{ trans_choice('core.note', 2) }}</label>
                                                                                                    <textarea name="approved_notes" class="form-control" id="approved_notes" rows="3"></textarea>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-default float-left"
                                                                                                    data-dismiss="modal">
                                                                                                    {{ trans_choice('core.close', 1) }}
                                                                                                </button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="modal fade" id="reject_loan_modal">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">
                                                                                                {{ trans_choice('loan::general.reject', 1) }}
                                                                                                {{ trans_choice('loan::general.loan', 1) }}
                                                                                                @show_tooltip(__('loan::lang.tooltip_loanshowstatusreject'))
                                                                                            </h4>
                                                                                            <button type="button" class="close" data-dismiss="modal"
                                                                                                aria-label="Close">
                                                                                                <span aria-hidden="true">×</span></button>
                                                                                        </div>
                                                                                        <form method="post"
                                                                                            action="{{ url('contact_loan/' . $loan->id . '/reject_loan') }}">
                                                                                            {{ csrf_field() }}
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label for="rejected_notes"
                                                                                                        class="control-label">{{ trans_choice('core.note', 2) }}</label>
                                                                                                    <textarea name="rejected_notes" class="form-control" id="rejected_notes" rows="3" required=""></textarea>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-default pull-left"
                                                                                                    data-dismiss="modal">
                                                                                                    {{ trans_choice('core.close', 1) }}
                                                                                                </button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal fade" id="withdraw_loan_modal">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">
                                                                                                {{ trans_choice('loan::general.withdraw', 1) }}
                                                                                                {{ trans_choice('loan::general.loan', 1) }}
                                                                                                @show_tooltip(__('loan::lang.tooltip_loanshowstatuswithdrawn'))
                                                                                            </h4>
                                                                                            <button type="button" class="close" data-dismiss="modal"
                                                                                                aria-label="Close">
                                                                                                <span aria-hidden="true">×</span></button>
                                                                                        </div>
                                                                                        <form method="post"
                                                                                            action="{{ url('contact_loan/' . $loan->id . '/withdraw_loan') }}">
                                                                                            {{ csrf_field() }}
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label for="withdrawn_notes"
                                                                                                        class="control-label">{{ trans_choice('core.note', 2) }}</label>
                                                                                                    <textarea name="withdrawn_notes" class="form-control" id="withdrawn_notes" rows="3" required=""></textarea>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-default pull-left"
                                                                                                    data-dismiss="modal">
                                                                                                    {{ trans_choice('core.close', 1) }}
                                                                                                </button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endcan
                                                                    @endif
                                                                    @if ($loan->status == 'active')
                                                                        @can('loan.loans.edit')
                                                                            <div class="modal fade" id="change_loan_officer_modal">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">
                                                                                                {{ trans_choice('loan::general.change', 1) }}
                                                                                                {{ trans_choice('loan::general.loan', 1) }}
                                                                                                {{ trans_choice('loan::general.officer', 1) }}</h4>
                                                                                            <button type="button" class="close" data-dismiss="modal"
                                                                                                aria-label="Close">
                                                                                                <span aria-hidden="true">×</span></button>
                                                                                        </div>
                                                                                        <form method="post"
                                                                                            action="{{ url('contact_loan/' . $loan->id . '/change_loan_officer') }}">
                                                                                            {{ csrf_field() }}
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label for="loan_officer_id"
                                                                                                        class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                                                                                                        {{ trans_choice('loan::general.officer', 1) }}</label>
                                                                                                    <select class="form-control select2"
                                                                                                        name="loan_officer_id" id="loan_officer_id"
                                                                                                        v-model="loan_officer_id" required>
                                                                                                        <option value=""></option>
                                                                                                        <option v-for="user in users"
                                                                                                            :value="user.id">
                                                                                                            @{{ user.user_full_name }}</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-default pull-left"
                                                                                                    data-dismiss="modal">
                                                                                                    {{ trans_choice('core.close', 1) }}
                                                                                                </button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endcan
                                                                        @can('loan.loans.transactions.edit')
                                                                            <div class="modal fade" id="waive_interest_modal">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">
                                                                                                {{ trans_choice('loan::general.waive', 1) }}
                                                                                                {{ trans_choice('loan::general.interest', 1) }}</h4>
                                                                                            <button type="button" class="close" data-dismiss="modal"
                                                                                                aria-label="Close">
                                                                                                <span aria-hidden="true">×</span></button>
                                                                                        </div>
                                                                                        <form method="post"
                                                                                            action="{{ url('contact_loan/' . $loan->id . '/waive_interest') }}">
                                                                                            {{ csrf_field() }}
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label for="date"
                                                                                                        class="control-label">{{ trans_choice('core.date', 1) }}</label>
                                                                                                    <input type="text"
                                                                                                        class="form-control datepicker @error('date') is-invalid @enderror"
                                                                                                        name="date" value="{{ date('Y-m-d') }}"
                                                                                                        id="date" required>

                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label for="interest_waived_amount"
                                                                                                        class="control-label">{{ trans_choice('core.amount', 1) }}</label>
                                                                                                    <input type="text" name="interest_waived_amount"
                                                                                                        class="form-control numeric" value=""
                                                                                                        required="" id="interest_waived_amount">
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label for="description"
                                                                                                        class="control-label">{{ trans_choice('core.note', 2) }}</label>
                                                                                                    <textarea name="description" class="form-control" id="description" rows="3"></textarea>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-default pull-left"
                                                                                                    data-dismiss="modal">
                                                                                                    {{ trans_choice('core.close', 1) }}
                                                                                                </button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @can('loan.loans.write_off_loan')
                                                                                <div class="modal fade" id="write_off_loan_modal">
                                                                                    <div class="modal-dialog">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h4 class="modal-title">
                                                                                                    {{ trans_choice('loan::general.write_off', 1) }}
                                                                                                    {{ trans_choice('loan::general.loan', 1) }}</h4>
                                                                                                <button type="button" class="close" data-dismiss="modal"
                                                                                                    aria-label="Close">
                                                                                                    <span aria-hidden="true">×</span></button>
                                                                                            </div>
                                                                                            <form method="post"
                                                                                                action="{{ url('contact_loan/' . $loan->id . '/write_off_loan') }}">
                                                                                                {{ csrf_field() }}
                                                                                                <div class="modal-body">
                                                                                                    <div class="form-group">
                                                                                                        <label for="written_off_on_date"
                                                                                                            class="control-label">{{ trans_choice('core.date', 1) }}</label>
                                                                                                        <input type="text"
                                                                                                            class="form-control datepicker @error('written_off_on_date') is-invalid @enderror"
                                                                                                            name="written_off_on_date"
                                                                                                            value="{{ date('Y-m-d') }}"
                                                                                                            id="written_off_on_date" required>

                                                                                                    </div>

                                                                                                    <div class="form-group">
                                                                                                        <label for="written_off_notes"
                                                                                                            class="control-label">{{ trans_choice('core.note', 2) }}</label>
                                                                                                        <textarea name="written_off_notes" class="form-control" id="written_off_notes" rows="3" required></textarea>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="button" class="btn btn-default pull-left"
                                                                                                        data-dismiss="modal">
                                                                                                        {{ trans_choice('core.close', 1) }}
                                                                                                    </button>
                                                                                                    <button type="submit"
                                                                                                        class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endcan
                                                                            @can('loan.loans.write_off_loan')
                                                                                <div class="modal fade" id="close_loan_modal">
                                                                                    <div class="modal-dialog">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h4 class="modal-title">
                                                                                                    {{ trans_choice('core.close', 1) }}
                                                                                                    {{ trans_choice('loan::general.loan', 1) }}</h4>
                                                                                                <button type="button" class="close" data-dismiss="modal"
                                                                                                    aria-label="Close">
                                                                                                    <span aria-hidden="true">×</span></button>
                                                                                            </div>
                                                                                            <form method="post"
                                                                                                action="{{ url('contact_loan/' . $loan->id . '/close_loan') }}">
                                                                                                {{ csrf_field() }}
                                                                                                <div class="modal-body">
                                                                                                    <div class="form-group">
                                                                                                        <label for="closed_on_date"
                                                                                                            class="control-label">{{ trans_choice('core.date', 1) }}</label>
                                                                                                        <input type="text"
                                                                                                            class="form-control datepicker @error('closed_on_date') is-invalid @enderror"
                                                                                                            name="closed_on_date"
                                                                                                            value="{{ date('Y-m-d') }}" id="closed_on_date"
                                                                                                            required>

                                                                                                    </div>

                                                                                                    <div class="form-group">
                                                                                                        <label for="closed_notes"
                                                                                                            class="control-label">{{ trans_choice('core.note', 2) }}</label>
                                                                                                        <textarea name="closed_notes" class="form-control" id="closed_notes" rows="3" required></textarea>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="button" class="btn btn-default pull-left"
                                                                                                        data-dismiss="modal">
                                                                                                        {{ trans_choice('core.close', 1) }}
                                                                                                    </button>
                                                                                                    <button type="submit"
                                                                                                        class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endcan
                                                                        @endcan
                                                                        @can('loan.loans.reschedule_loan')
                                                                            <div class="modal fade" id="reschedule_loan_modal">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">
                                                                                                {{ trans_choice('loan::general.reschedule', 1) }}
                                                                                                {{ trans_choice('loan::general.loan', 1) }}</h4>
                                                                                            <button type="button" class="close" data-dismiss="modal"
                                                                                                aria-label="Close">
                                                                                                <span aria-hidden="true">×</span></button>
                                                                                        </div>
                                                                                        <form method="post"
                                                                                            action="{{ url('contact_loan/' . $loan->id . '/reschedule_loan') }}">
                                                                                            {{ csrf_field() }}
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label for="rescheduled_from_date"
                                                                                                        class="control-label">
                                                                                                        {{ trans_choice('loan::general.reschedule_from_installment_on', 1) }}
                                                                                                    </label>

                                                                                                    <select v-model="rescheduled_from_date"
                                                                                                        name="rescheduled_from_date"
                                                                                                        class="form-control @error('rescheduled_from_date') is-invalid @enderror"
                                                                                                        :required="true" id="rescheduled_from_date">
                                                                                                        <option value="">
                                                                                                            {{ trans_choice('core.select', 1) }}
                                                                                                        </option>
                                                                                                        @foreach ($loan->unpaid_repayment_schedules->pluck('installment', 'due_date') as $due_date => $installment)
                                                                                                            <option value="{{ $due_date }}">
                                                                                                                #{{ $installment }} :
                                                                                                                {{ $due_date }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>

                                                                                                <div class="form-group">
                                                                                                    <label for="rescheduled_on_date"
                                                                                                        class="control-label">
                                                                                                        {{ trans_choice('core.submitted_on', 1) }}
                                                                                                    </label>
                                                                                                    <input type="text" v-model="rescheduled_on_date"
                                                                                                        name="rescheduled_on_date"
                                                                                                        id="rescheduled_on_date"
                                                                                                        class="form-control datepicker @error('rescheduled_on_date') is-invalid @enderror"
                                                                                                        required>
                                                                                                </div>

                                                                                                <div class="form-group">
                                                                                                    <label for="rescheduled_first_payment_date_checkbox">
                                                                                                        <input type="checkbox"
                                                                                                            id="rescheduled_first_payment_date_checkbox"
                                                                                                            name="rescheduled_first_payment_date_checkbox"
                                                                                                            v-model="rescheduled_first_payment_date_checkbox" />
                                                                                                        {{ trans_choice('loan::general.change_repayment_date', 1) }}
                                                                                                    </label>
                                                                                                </div>
                                                                                                {{-- When rescheduled_first_payment_date_checkbox is checked --}}
                                                                                                <div class="form-group"
                                                                                                    v-show="rescheduled_first_payment_date_checkbox">
                                                                                                    <label for="rescheduled_first_payment_date"
                                                                                                        class="control-label">
                                                                                                        {{ trans_choice('loan::general.adjusted_due_date', 1) }}
                                                                                                    </label>
                                                                                                    <input type="text"
                                                                                                        id="rescheduled_first_payment_date"
                                                                                                        v-model="rescheduled_first_payment_date"
                                                                                                        name="rescheduled_first_payment_date"
                                                                                                        class="form-control @error('rescheduled_first_payment_date') is-invalid @enderror"
                                                                                                        :required="rescheduled_first_payment_date_checkbox">
                                                                                                </div>
                                                                                                {{-- When rescheduled_first_payment_date_checkbox is unchecked --}}
                                                                                                <div v-if="!rescheduled_first_payment_date_checkbox">
                                                                                                    <input type="hidden" v-model="rescheduled_from_date"
                                                                                                        name="rescheduled_first_payment_date">
                                                                                                </div>

                                                                                                <div class="form-group">
                                                                                                    <label for="installment_amount_checkbox">
                                                                                                        <input type="checkbox"
                                                                                                            id="installment_amount_checkbox"
                                                                                                            name="installment_amount_checkbox"
                                                                                                            v-model="installment_amount_checkbox"
                                                                                                            :disabled="reschedule_add_extra_installments_checkbox == true" />
                                                                                                        {{ trans_choice('core.change', 1) }}
                                                                                                        {{ trans_choice('loan::general.installment', 1) }}
                                                                                                        {{ trans_choice('loan::general.amount', 1) }}
                                                                                                    </label>@show_tooltip(__('loan::lang.tooltip_installment_amount'))
                                                                                                </div>
                                                                                                <div class="form-group"
                                                                                                    v-if="installment_amount_checkbox">
                                                                                                    <label for="installment_amount" class="control-label">
                                                                                                        {{ trans_choice('loan::general.installment', 1) }}
                                                                                                        {{ trans_choice('loan::general.amount', 1) }}
                                                                                                    </label>
                                                                                                    <input type="number" v-model="installment_amount"
                                                                                                        name="installment_amount" id="installment_amount"
                                                                                                        class="form-control @error('installment_amount') is-invalid @enderror"
                                                                                                        required>
                                                                                                </div>

                                                                                                <div class="form-group">
                                                                                                    <label
                                                                                                        for="reschedule_add_extra_installments_checkbox">
                                                                                                        <input type="checkbox"
                                                                                                            id="reschedule_add_extra_installments_checkbox"
                                                                                                            name="reschedule_add_extra_installments_checkbox"
                                                                                                            v-model="reschedule_add_extra_installments_checkbox"
                                                                                                            :disabled="installment_amount_checkbox == true" />
                                                                                                        {{ trans_choice('loan::general.add_extra_installments', 1) }}
                                                                                                    </label>
                                                                                                    @show_tooltip(__('loan::lang.tooltip_reschedule_add_extra_installments_amount'))
                                                                                                </div>
                                                                                                <div class="form-group"
                                                                                                    v-if="reschedule_add_extra_installments_checkbox">
                                                                                                    <label for="reschedule_extra_installments"
                                                                                                        class="control-label">
                                                                                                        {{ trans_choice('loan::general.extra_installment', 2) }}
                                                                                                    </label>
                                                                                                    <input type="text"
                                                                                                        id="reschedule_extra_installments"
                                                                                                        name="reschedule_extra_installments"
                                                                                                        v-model="reschedule_extra_installments"
                                                                                                        class="form-control numeric" required />
                                                                                                </div>

                                                                                                <div class="form-group">
                                                                                                    <label
                                                                                                        for="reschedule_adjust_loan_interest_rate_checkbox">
                                                                                                        <input type="checkbox"
                                                                                                            id="reschedule_adjust_loan_interest_rate_checkbox"
                                                                                                            name="reschedule_adjust_loan_interest_rate_checkbox"
                                                                                                            v-model="reschedule_adjust_loan_interest_rate_checkbox" />
                                                                                                        {{ trans_choice('loan::general.adjust_loan_interest_rate', 1) }}
                                                                                                    </label>
                                                                                                </div>
                                                                                                <div class="form-group"
                                                                                                    v-if="reschedule_adjust_loan_interest_rate_checkbox">
                                                                                                    <label for="reschedule_interest_rate"
                                                                                                        class="control-label">
                                                                                                        {{ trans_choice('loan::general.interest', 1) }}
                                                                                                        {{ trans_choice('loan::general.rate', 1) }}
                                                                                                    </label>
                                                                                                    <input type="text" id="reschedule_interest_rate"
                                                                                                        name="reschedule_interest_rate"
                                                                                                        v-model="reschedule_interest_rate"
                                                                                                        class="form-control" required />
                                                                                                </div>

                                                                                                <div class="form-group">
                                                                                                    <label for="reschedule_enable_grace_periods_checkbox">
                                                                                                        <input type="checkbox"
                                                                                                            id="reschedule_enable_grace_periods_checkbox"
                                                                                                            name="reschedule_enable_grace_periods_checkbox"
                                                                                                            v-model="reschedule_enable_grace_periods_checkbox" />
                                                                                                        {{ trans_choice('loan::general.introduce_grace_periods', 1) }}
                                                                                                    </label>
                                                                                                </div>

                                                                                                <div class="form-group"
                                                                                                    v-if="reschedule_enable_grace_periods_checkbox">
                                                                                                    <label for="reschedule_grace_on_principal_paid"
                                                                                                        class="control-label">
                                                                                                        {{ trans_choice('loan::general.grace_on_principal_paid', 1) }}
                                                                                                    </label>
                                                                                                    <input type="text"
                                                                                                        id="reschedule_grace_on_principal_paid"
                                                                                                        name="reschedule_grace_on_principal_paid"
                                                                                                        v-model="reschedule_grace_on_principal_paid"
                                                                                                        class="form-control numeric" />
                                                                                                </div>
                                                                                                <div class="form-group"
                                                                                                    v-if="reschedule_enable_grace_periods">
                                                                                                    <label for="reschedule_grace_on_interest_paid"
                                                                                                        class="control-label">
                                                                                                        {{ trans_choice('loan::general.grace_on_interest_paid', 1) }}
                                                                                                    </label>
                                                                                                    <input type="text"
                                                                                                        id="reschedule_grace_on_interest_paid"
                                                                                                        name="reschedule_grace_on_interest_paid"
                                                                                                        v-model="reschedule_grace_on_interest_paid"
                                                                                                        class="form-control numeric" />
                                                                                                </div>

                                                                                                <div class="form-group">
                                                                                                    <label for="rescheduled_notes"
                                                                                                        class="control-label">{{ trans_choice('core.note', 2) }}</label>
                                                                                                    <textarea name="rescheduled_notes" v-model="rescheduled_notes" class="form-control" id="rescheduled_notes" rows="3" required></textarea>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-default pull-left"
                                                                                                    data-dismiss="modal">
                                                                                                    {{ trans_choice('core.close', 1) }}
                                                                                                </button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endcan
                                                                    @endif
                                                                    @if ($loan->status == 'approved')
                                                                        @can('loan.loans.edit')
                                                                            <div class="modal fade" id="change_loan_officer_modal">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">
                                                                                                {{ trans_choice('loan::general.change', 1) }}
                                                                                                {{ trans_choice('loan::general.loan', 1) }}
                                                                                                {{ trans_choice('loan::general.officer', 1) }}</h4>
                                                                                            <button type="button" class="close" data-dismiss="modal"
                                                                                                aria-label="Close">
                                                                                                <span aria-hidden="true">×</span></button>
                                                                                        </div>
                                                                                        <form method="post"
                                                                                            action="{{ url('contact_loan/' . $loan->id . '/change_loan_officer') }}">
                                                                                            {{ csrf_field() }}
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label for="loan_officer_id"
                                                                                                        class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                                                                                                        {{ trans_choice('loan::general.officer', 1) }}</label>
                                                                                                    <select class="form-control select2"
                                                                                                        name="loan_officer_id" id="loan_officer_id"
                                                                                                        v-model="loan_officer_id" required>
                                                                                                        <option value=""></option>
                                                                                                        <option v-for="user in users"
                                                                                                            :value="user.id">
                                                                                                            @{{ user.user_full_name }}</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-default pull-left"
                                                                                                    data-dismiss="modal">
                                                                                                    {{ trans_choice('core.close', 1) }}
                                                                                                </button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endcan
                                                                        @can('loan.loans.disburse_loan')
                                                                            <div class="modal fade in" id="disburse_loan_modal">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">
                                                                                                {{ trans_choice('loan::general.disburse', 1) }}
                                                                                                {{ trans_choice('loan::general.loan', 1) }}</h4>
                                                                                            <button type="button" class="close" data-dismiss="modal">
                                                                                                <span>×</span></button>
                                                                                        </div>
                                                                                        <form method="post"
                                                                                            action="{{ url('contact_loan/' . $loan->id . '/disburse_loan') }}">
                                                                                            {{ csrf_field() }}
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label for="disbursed_on_date"
                                                                                                        class="control-label">{{ trans_choice('loan::general.actual', 1) }}
                                                                                                        {{ trans_choice('loan::general.disbursement', 1) }}
                                                                                                        {{ trans_choice('core.date', 1) }}</label>

                                                                                                    <input type="text" name="disbursed_on_date"
                                                                                                        value="{{ $loan->expected_disbursement_date }}"
                                                                                                        class="form-control datepicker @error('disbursed_on_date') is-invalid @enderror"
                                                                                                        :required="true" id="rescheduled_from_date">
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label for="first_payment_date"
                                                                                                        class="control-label">{{ trans_choice('core.first', 1) }}
                                                                                                        {{ trans_choice('loan::general.repayment', 1) }}
                                                                                                        {{ trans_choice('core.date', 1) }}</label>

                                                                                                    <input type="text" name="first_payment_date"
                                                                                                        value="{{ $loan->expected_first_payment_date }}"
                                                                                                        class="form-control datepicker @error('first_payment_date') is-invalid @enderror"
                                                                                                        :required="true" id="first_payment_date">
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label for="payment_type_id"
                                                                                                        class="control-label">{{ trans_choice('loan::general.payment', 1) }}
                                                                                                        {{ trans_choice('core.type', 1) }}
                                                                                                    </label>
                                                                                                    <select class="form-control" name="payment_type_id"
                                                                                                        id="payment_type_id" v-model="payment_type_id"
                                                                                                        required>
                                                                                                        <option value=""></option>
                                                                                                        @foreach ($payment_types as $key)
                                                                                                            <option value="{{ $key->id }}">
                                                                                                                {{ $key->name }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <div class="form-group">
                                                                                                        <label for="receipt_number"
                                                                                                            class="control-label">{{ trans_choice('core.receipt', 1) }}
                                                                                                            #</label>
                                                                                                        <input type="text" name="receipt_number"
                                                                                                            class="form-control" value=""
                                                                                                            id="receipt_number">
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="form-group">
                                                                                                    <label for="disbursed_notes"
                                                                                                        class="control-label">{{ trans_choice('core.note', 2) }}</label>
                                                                                                    <textarea name="disbursed_notes" class="form-control" id="disbursed_notes" rows="3"></textarea>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-default pull-left"
                                                                                                    data-dismiss="modal">
                                                                                                    {{ trans_choice('core.close', 1) }}
                                                                                                </button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">{{ trans_choice('core.save', 1) }}</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endcan
                                                                    @endif
                                                                    @if ($loan->status == 'withdrawn')
                                                                        @can('loan.loans.approve_loan')
                                                                            <a href="{{ url('contact_loan/' . $loan->id . '/undo_withdrawn') }}"
                                                                                class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                                                {{ trans_choice('loan::general.undo', 1) }}
                                                                                {{ trans_choice('loan::general.withdrawn', 1) }}
                                                                            </a>
                                                                        @endcan
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="table-responsive">
                                                            {{-- Basic Info Table --}}
                                                            <div class="col-12">
                                                                <table class="table table-bordered bg-gray">
                                                                    <tbody>
                                                                        <tr>
                                                                            {{-- Loan ID --}}
                                                                            <td>
                                                                                <p class="text-muted">
                                                                                    {{ trans_choice('loan::general.loan', 1) }}
                                                                                    {{ trans_choice('core.id', 1) }}
                                                                                </p>
                                                                                <p>
                                                                                    {{ $loan->id }}
                                                                                </p>
                                                                            </td>

                                                                            {{-- Contact Name --}}
                                                                            <td>
                                                                                <p class="text-muted">
                                                                                    {{ trans_choice('core.contact', 1) }}
                                                                                    {{ trans_choice('core.name', 1) }}
                                                                                </p>
                                                                                <p>
                                                                                    <a class="link-dark"
                                                                                        href="{{ action('ContactController@show', [$loan->contact->id]) }}">
                                                                                        {{ $loan->contact->name }}
                                                                                    </a>
                                                                                </p>
                                                                            </td>

                                                                            {{-- Loan Amount --}}
                                                                            <td>
                                                                                <p class="text-muted">
                                                                                    {{ trans_choice('loan::general.loan', 1) }}
                                                                                    {{ trans_choice('core.amount', 1) }}
                                                                                </p>
                                                                                <p>
                                                                                    {{ $loan->currency->code }}
                                                                                    {{ number_format($loan->applied_amount, 2) }}
                                                                                </p>
                                                                            </td>

                                                                            {{-- Product --}}
                                                                            <td>
                                                                                <p class="text-muted">
                                                                                    {{ trans_choice('loan::general.loan', 1) }}
                                                                                    {{ trans_choice('core.product', 1) }}
                                                                                </p>
                                                                                <p>
                                                                                    {{ $loan->loan_product->name }}
                                                                                </p>
                                                                            </td>

                                                                            {{-- Created on --}}
                                                                            <td>
                                                                                <p class="text-muted">
                                                                                    {{ trans_choice('core.created_on', 1) }}
                                                                                </p>
                                                                                <p>
                                                                                    {{ @format_date($loan->created_at) }}
                                                                                </p>
                                                                            </td>

                                                                            {{-- Status --}}
                                                                            <td>
                                                                                <p class="text-muted">
                                                                                    {{ trans_choice('core.current', 1) }}
                                                                                    {{ trans_choice('core.status', 1) }}
                                                                                </p>
                                                                                <p>
                                                                                    {!! $loan->status_label !!}
                                                                                    @if ($loan->status_tooltip)
                                                                                        @show_tooltip($loan->status_tooltip)
                                                                                    @endif
                                                                                </p>
                                                                            </td>

                                                                            {{-- Interest rate --}}
                                                                            <td>
                                                                                <p class="text-muted">
                                                                                    {{ trans_choice('loan::general.interest_rate', 1) }}
                                                                                </p>
                                                                                <p>
                                                                                    {{ number_format($loan->interest_rate, 2) }}%
                                                                                    {{ strtolower(trans('loan::general.per')) }}
                                                                                    {{ $loan->interest_rate_type }}
                                                                                </p>
                                                                            </td>

                                                                            {{-- Expected Maturity Date --}}
                                                                            @if (in_array($loan->status, ['submitted', 'pending', 'approved']))
                                                                                <td>
                                                                                    <p class="text-muted">
                                                                                        {{ trans_choice('loan::general.expected', 1) }}
                                                                                        {{ trans_choice('loan::general.disbursement', 1) }}
                                                                                        {{ trans_choice('loan::general.date', 1) }}
                                                                                    </p>
                                                                                    <p>
                                                                                        {{ @format_date($loan->expected_disbursement_date) }}
                                                                                    </p>
                                                                                </td>
                                                                            @elseif ($loan->status == 'active')
                                                                                <td>
                                                                                    <p class="text-muted">
                                                                                        {{ trans_choice('loan::general.expected', 1) }}
                                                                                        {{ trans_choice('loan::general.maturity', 1) }}
                                                                                        {{ trans_choice('loan::general.date', 1) }}
                                                                                    </p>
                                                                                    <p>
                                                                                        {{ @format_date($loan->maturity_date) }}
                                                                                    </p>
                                                                                </td>
                                                                            @endif
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="col-12">
                                                                <table class="table table-bordered bg-gray">
                                                                    <tbody>
                                                                        @if (in_array($loan->status, ['active', 'fully_paid', 'closed', 'written_off', 'overpaid', 'rescheduled']))
                                                                            <h3>
                                                                                {{ trans('loan::general.perfomance_history') }}
                                                                            </h3>

                                                                            <tr>
                                                                                {{-- (Number of instalments paid) of (Total number of instalments) --}}
                                                                                <td>
                                                                                    <p class="text-muted">
                                                                                        {{ trans('loan::general.no_of_repayments') }}
                                                                                    </p>
                                                                                    <p>
                                                                                        {{ $loan->number_of_repayments }}
                                                                                        {{ trans('core.of') }}
                                                                                        {{ $loan->number_of_installments }}
                                                                                    </p>
                                                                                </td>

                                                                                {{-- Maturity Date --}}
                                                                                <td>
                                                                                    <p class="text-muted">
                                                                                        {{ trans('core.maturity_date') }}
                                                                                    </p>
                                                                                    <p>
                                                                                        {{ $loan->maturity_date }}
                                                                                    </p>
                                                                                </td>

                                                                                {{-- Loan Standing --}}
                                                                                <td>
                                                                                    <p class="text-muted">
                                                                                        {{ trans_choice('core.loan', 1) }}
                                                                                        {{ trans_choice('loan::general.standing', 1) }}
                                                                                    </p>
                                                                                    <p>
                                                                                        @php
                                                                                            $loan_standing = $loan->loan_standing;
                                                                                        @endphp
                                                                                        @if (!empty($loan_standing->status) && !empty($loan_standing->label_class))
                                                                                            <a href="#"
                                                                                                class="label label-{{ $loan_standing->label_class }}">
                                                                                                {{ $loan_standing->status }}
                                                                                            </a>
                                                                                        @endif
                                                                                    </p>
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="col-12">
                                                                <?php $current_loan_balance = 0; ?>

                                                                @if (in_array($loan->status, ['active', 'fully_paid', 'closed', 'written_off', 'overpaid', 'rescheduled']))
                                                                    <?php
                                                                    
                                                                    $fees_outstanding = $loan->fees_outstanding;
                                                                    $balance = $loan->balance;
                                                                    $arrears_amount = $loan->arrears_amount;
                                                                    $arrears_days = $loan->arrears_days;
                                                                    
                                                                    $penalties = $loan->penalties;
                                                                    $penalties_waived = $loan->penalties_waived;
                                                                    $penalties_written_off = $loan->penalties_written_off;
                                                                    $penalties_paid = $loan->penalties_paid;
                                                                    $penalties_outstanding = $loan->penalties_outstanding;
                                                                    $penalties_overdue = $loan->penalties_overdue;
                                                                    
                                                                    $principal = $loan->principal;
                                                                    $principal_waived = $loan->principal_waived;
                                                                    $principal_paid = $loan->principal_paid;
                                                                    $principal_written_off = $loan->principal_written_off;
                                                                    $principal_outstanding = $loan->principal_outstanding;
                                                                    $principal_overdue = $loan->principal_overdue;
                                                                    
                                                                    $interest = $loan->interest;
                                                                    $interest_paid = $loan->interest_paid;
                                                                    $interest_waived = $loan->interest_waived;
                                                                    $interest_written_off = $loan->interest_written_off;
                                                                    $interest_outstanding = $loan->interest_outstanding;
                                                                    $interest_overdue = $loan->interest_overdue;
                                                                    
                                                                    $fees = $loan->fees;
                                                                    $fees_paid = $loan->fees_paid;
                                                                    $fees_waived = $loan->fees_waived;
                                                                    $fees_written_off = $loan->fees_written_off;
                                                                    $fees_overdue = $loan->fees_overdue;
                                                                    
                                                                    $total_paid = $principal_paid + $interest_paid + $fees_paid + $penalties_paid;
                                                                    $current_loan_balance = $loan->current_balance;
                                                                    ?>

                                                                    <br>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if ($loan->status == 'active')
                                                            <div class="row">
                                                                <!-- ./col -->
                                                                <div class="col-lg-4 col-xs-6">
                                                                    <!-- small box -->
                                                                    <div class="small-box bg-green">
                                                                        <div class="inner">
                                                                            <h4><strong><span class="">&nbsp;</span>
                                                                                    {{ number_format($balance, 2) }}
                                                                                </strong></h4>
                                                                            <p>
                                                                                {{ trans_choice('loan::general.projected_balance', 1) }}
                                                                                @show_tooltip(__('loan::lang.tooltip_projected_balance'))
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- ./col -->
                                                                <!-- ./col -->
                                                                <div class="col-lg-4 col-xs-6">
                                                                    <!-- small box -->
                                                                    <div class="small-box bg-green">
                                                                        <div class="inner">
                                                                            <h4><strong><span
                                                                                        class="">&nbsp;</span>{{ number_format($current_loan_balance, 2) }}
                                                                                </strong></h4>
                                                                            <p>
                                                                                {{ trans('core.current') }}
                                                                                {{ trans_choice('loan::general.balance', 1) }}
                                                                                @show_tooltip(__('loan::lang.tooltip_current_balance'))
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- ./col -->
                                                                <!-- ./col -->
                                                                <div class="col-lg-4 col-xs-6">
                                                                    <!-- small box -->
                                                                    <div class="small-box bg-aqua">
                                                                        <div class="inner">
                                                                            <h4>
                                                                                <strong>
                                                                                    <span
                                                                                        class="@if ($arrears_amount) text-danger @endif">&nbsp;</span>
                                                                                    {{ number_format($arrears_amount, 2) }}
                                                                                </strong>
                                                                            </h4>
                                                                            <p>{{ trans_choice('loan::general.amount', 1) }}
                                                                                {{ trans_choice('core.in', 1) }}
                                                                                {{ trans_choice('loan::general.arrears', 1) }}
                                                                                @show_tooltip(__('loan::lang.tooltip_loanshowamountinarrears'))
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- ./col -->
                                                                <!-- /.col -->
                                                                <div class="col-lg-4 col-xs-6">
                                                                    <div class="small-box bg-yellow">
                                                                        <div class="inner">
                                                                            <h4>
                                                                                <strong>
                                                                                    <span
                                                                                        class="@if ($arrears_days) text-danger @endif">&nbsp;</span>{{ $arrears_days }}
                                                                                </strong>
                                                                            </h4>
                                                                            <p>{{ trans_choice('loan::general.day', 2) }}
                                                                                {{ trans_choice('core.in', 1) }}
                                                                                {{ trans_choice('loan::general.arrears', 1) }}
                                                                                @show_tooltip(__('loan::lang.tooltip_loanshowdaysinarrears'))
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- ./col -->

                                                                <!-- ./col -->
                                                                <div class="col-lg-4 col-xs-6">
                                                                    <!-- small box -->
                                                                    <div class="small-box bg-aqua">
                                                                        <div class="inner">
                                                                            <h4>
                                                                                <strong>
                                                                                    <span class="">&nbsp;</span>
                                                                                    {{ number_format($loan->repayment_schedules->sum('total_interest'), 2) }}
                                                                                </strong>
                                                                            </h4>
                                                                            <p>
                                                                                {{ trans('sale.total') }}
                                                                                {{ trans_choice('loan::general.interest', 2) }}
                                                                                {{ trans_choice('loan::general.due', 1) }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- ./col -->
                                                                <!-- /.col -->
                                                                <div class="col-lg-4 col-xs-6">
                                                                    <div class="small-box bg-yellow">
                                                                        <div class="inner">
                                                                            <h4>
                                                                                <strong>
                                                                                    <span class="">&nbsp;</span>
                                                                                    {{ $loan->percentage_of_timely_repayments }}%
                                                                                </strong>
                                                                            </h4>
                                                                            <p>{{ trans_choice('loan::general.timely', 1) }}
                                                                                {{ trans_choice('loan::general.repayment', 2) }}
                                                                                {{ trans_choice('core.rate', 1) }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- ./col -->
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="panel-group row">
                        <div class="panel panel-success">
                            <div class="panel-heading" data-toggle="collapse" href="#collapse_other_info">
                                <h4 class="panel-title">
                                    <a href="#">
                                        {{ trans('core.other') }}
                                        {{ trans_choice('loan::general.loan', 1) }}
                                        {{ trans('loan::general.info') }}
                                    </a>
                                    <span class="float-right">
                                        <i class="fa fa-chevron-circle-down"></i>
                                    </span>
                                </h4>
                            </div>
                            <div id="collapse_other_info" class="panel-collapse collapse">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        @include('loan::loan.partials.loan_tabs')
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if (in_array($loan->status, ['active', 'fully_paid', 'closed', 'written_off', 'overpaid', 'rescheduled']))
                        <div class="panel-group row">
                            <div class="panel panel-default">
                                <div class="panel-heading" data-toggle="collapse" href="#collapse_activity_log">
                                    <h4 class="panel-title">
                                        <a href="#">
                                            {{ trans_choice('lang_v1.activity_log', 1) }}
                                        </a>
                                        <span class="float-right">
                                            <i class="fa fa-chevron-circle-down"></i>
                                        </span>
                                    </h4>
                                </div>
                                <div id="collapse_activity_log" class="panel-collapse collapse">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            @include('loan::loan.partials.activity_log')
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </section>
            @endslot
        @endcomponent

    </section>

@stop
@section('javascript')
    @if ($loan->status == 'active')
        <script>
            $("#interest_waived_amount").val("{{ $interest_outstanding }}");

            $('#loan_transactions_table').DataTable({
                order: [
                    [5, 'asc']
                ]
            });
        </script>
    @endif
    
    <script>
        
    </script>
    <script>
        var app = new Vue({
            el: '#vue-app',
            data() {
                return {
                    loan_officer_id: "{{ old('loan_officer_id', $loan->loan_officer_id) }}",
                    rescheduled_on_date: "{{ old('rescheduled_on_date', date('Y-m-d')) }}",
                    rescheduled_from_date: "{{ old('rescheduled_from_date') }}",
                    rescheduled_first_payment_date: "{{ old('rescheduled_first_payment_date') }}",
                    reschedule_on: 'outstanding_principal',
                    rescheduled_notes: "{{ old('rescheduled_notes') }}",
                    reschedule_enable_grace_periods: false,
                    reschedule_adjust_loan_interest_rate: false,
                    reschedule_add_extra_installments: false,
                    reschedule_first_payment_date: false,
                    reschedule_grace_on_principal_paid: "{{ old('reschedule_grace_on_principal_paid') }}",
                    reschedule_grace_on_interest_paid: "{{ old('reschedule_grace_on_interest_paid') }}",
                    reschedule_grace_on_interest_charged: "{{ old('reschedule_grace_on_interest_charged') }}",
                    reschedule_interest_rate: "{{ old('reschedule_interest_rate') }}",
                    reschedule_extra_installments: "{{ old('reschedule_extra_installments') }}",
                    installment_amount: "{{ old('installment_amount') }}",

                    // Checkboxes
                    rescheduled_first_payment_date_checkbox: "{{ old('rescheduled_first_payment_date_checkbox') }}",
                    reschedule_adjust_loan_interest_rate_checkbox: "{{ old('reschedule_adjust_loan_interest_rate_checkbox') }}",
                    reschedule_add_extra_installments_checkbox: "{{ old('reschedule_add_extra_installments_checkbox') }}",
                    reschedule_enable_grace_periods_checkbox: "{{ old('reschedule_enable_grace_periods_checkbox') }}",
                    installment_amount_checkbox: "{{ old('installment_amount_checkbox') }}",

                    //Loan approval and top up attributes
                    approved_amount: {!! $loan->applied_amount !!},
                    collateral: {!! $loan->collateral->sum('value') !!},
                    loan_approval_officers: [],
                    loan: {!! json_encode($loan) !!},

                    //Loan top up form attributes
                    users: {!! json_encode($users) !!},

                    //pro rata pending dues
                    pro_rata_date: "{{ date('Y-m-d') }}",
                    pro_rata_pending_due: {},

                    loading: {
                        pro_rata_pending_due: false,
                    },
                }
            },

            mounted() {
                this.loan_approval_officers.push(this.loan_officer_id);

                //get pro-rata pending dues
                this.get_pro_rata_pending_dues();

                // Init datepickers that conflict with vue v-model
                const datepicker_options = {
                    format: 'yyyy-mm-dd',
                    defaultDate: new Date()
                };
                // rescheduled_first_payment_date
                $("#rescheduled_first_payment_date").datepicker(datepicker_options).on("changeDate", () => {
                    this.rescheduled_first_payment_date = $('#rescheduled_first_payment_date').val();
                }).attr('readonly', '');
            },

            methods: {
                number_format(number) {
                    return new Intl.NumberFormat().format(number);
                },

                get_pro_rata_pending_dues() {
                    const url = `/contact_loan/${this.loan.id}/get_pending_dues?due_date=${this.pro_rata_date}`;
                    this.loading.pro_rata_pending_due = true;

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            this.pro_rata_pending_due = data;
                            this.loading.pro_rata_pending_due = false;
                        })
                        .catch(err => console.error(err));
                },

                set_pro_rata_date() {
                    this.pro_rata_date = document.getElementById('pro_rata_date').value;
                }
            },

            watch: {
                pro_rata_date() {
                    this.get_pro_rata_pending_dues();
                }
            }
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#btn_approve_loan").click(function(){
               $("#approve_loan_modal").modal('show');
            });
            
            $("#btn_reject_loan").click(function(){
               $("#reject_loan_modal").modal('show');
            });
        
        
            $('#al_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
                    $('#al_date_filter').val(
                        start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                    );
                    loan_activity_log_table.ajax.reload();
                })
                .on('cancel.daterangepicker', function(ev, picker) {
                    $('#al_date_filter').val('');
                    loan_activity_log_table.ajax.reload();
                });

            $(document).on('change', '#al_users_filter', function() {
                loan_activity_log_table.ajax.reload();
            });

            const loan_activity_log_table = $('#loan_activity_log_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'asc']
                ],
                "ajax": {
                    "url": '{{ action('\Modules\Loan\Http\Controllers\LoanController@activity_log', ['loan_id' => $loan->id]) }}',
                    "data": function(d) {
                        const user_id = document.getElementById('al_users_filter').value;

                        if ($('#al_date_filter').val()) {
                            d.start_date = $('input#al_date_filter')
                                .data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            d.end_date = $('input#al_date_filter')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD');
                        }

                        if (user_id) {
                            d.user_id = user_id;
                        }
                    }
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'subject_type',
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'note',
                        name: 'note'
                    }
                ]
            });
        });
    </script>
@endsection
