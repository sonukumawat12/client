@extends('loan::settings.layout')
@section('tab-title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.fee', 2) }}
@endsection

@section('tab-content')
    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        <div class="row">
            @component('components.widget')
                @slot('title')
                    {{ trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.fee', 2) }}
                    @show_tooltip(__('loan::lang.tooltip_loan_charge'))
                @endslot


                @slot('header')
                    <div class="box-tools">
                        <a href="{{ url('contact_loan/charge/create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ trans_choice('core.add', 1) }}
                            @show_tooltip(__('loan::lang.tooltip_loan_chargeadd'))
                        </a>
                    </div>
                @endslot

                @slot('slot')
                    <!-- Main content -->
                    <section class="content">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table id="data-table" class="table table-striped table-condensed table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    {{ trans_choice('core.action', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loan_chargeaction'))
                                                </th>
                                                <th>
                                                    {{ trans_choice('core.name', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loan_chargename'))
                                                </th>
                                                <th>
                                                    <span>{{ trans_choice('loan::general.fee', 1) }}
                                                        {{ trans_choice('core.type', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loan_chargetype'))</span>
                                                </th>
                                                <th>
                                                    <span>{{ trans_choice('core.amount', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loan_chargeamount'))</span>
                                                </th>
                                                <th>
                                                    {{ trans_choice('core.active', 1) }}
                                                    @show_tooltip(__('loan::lang.tooltip_loan_chargeactive'))
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $key)
                                                <tr>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button href="#" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown"
                                                                aria-expanded="false">{{ trans_choice('core.action', 1) }}
                                                                <span class="caret"></span><span class="sr-only"></span>
                                                            </button>

                                                            <div class="dropdown-menu dropdown-menu-left">
                                                                @can('loan.loans.charges.edit')
                                                                    <a href="{{ url('contact_loan/charge/' . $key->id . '/edit') }}" class="dropdown-item">
                                                                        <i class="fas fa-edit"></i>
                                                                        <span>{{ trans_choice('core.edit', 1) }}
                                                                            @show_tooltip(__('loan::lang.tooltip_loan_chargeedit'))</span>
                                                                    </a>
                                                                @endcan
                                                                <div class="divider"></div>
                                                                @can('loan.loans.charges.destroy')
                                                                    <a href="{{ url('contact_loan/charge/' . $key->id . '/destroy') }}"
                                                                        class="dropdown-item confirm">
                                                                        <i class="fas fa-trash"></i>
                                                                        <span>{{ trans_choice('core.delete', 1) }}
                                                                            @show_tooltip(__('loan::lang.tooltip_loan_chargedelete'))</span>
                                                                    </a>
                                                                @endcan
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <a href="#">
                                                            <span>{{ $key->name }}</span> <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if ($key->loan_charge_type_id == 1)
                                                            <span>{{ trans_choice('loan::general.disbursement', 1) }}</span>
                                                        @elseif ($key->loan_charge_type_id == 2)
                                                            <span>{{ trans_choice('loan::general.specified_due_date', 1) }}</span>
                                                        @elseif ($key->loan_charge_type_id == 3)
                                                            <span>{{ trans_choice('loan::general.installment', 1) }}
                                                                {{ trans_choice('loan::general.fee', 2) }}
                                                            </span>
                                                        @elseif ($key->loan_charge_type_id == 4)
                                                            <span>{{ trans_choice('loan::general.overdue', 1) }}
                                                                {{ trans_choice('loan::general.installment', 1) }}
                                                                {{ trans_choice('loan::general.fee', 2) }}
                                                            </span>
                                                        @elseif ($key->loan_charge_type_id == 5)
                                                            <span>{{ trans_choice('loan::general.disbursement_paid_with_repayment', 1) }}</span>
                                                        @elseif ($key->loan_charge_type_id == 6)
                                                            <span>{{ trans_choice('loan::general.loan_rescheduling_fee', 1) }}</span>
                                                        @elseif ($key->loan_charge_type_id == 7)
                                                            <span>{{ trans_choice('loan::general.overdue_on_loan_maturity', 1) }}</span>
                                                        @elseif ($key->loan_charge_type_id == 8)
                                                            <span>{{ trans_choice('loan::general.last_installment_fee', 1) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($key->loan_charge_option_id == 1)
                                                            <span>{{ number_format($key->amount, 2) }}
                                                                {{ trans_choice('loan::general.flat', 1) }}
                                                            </span>
                                                        @elseif ($key->loan_charge_option_id == 2)
                                                            <span>{{ number_format($key->amount, 2) }} %
                                                                {{ trans_choice('loan::general.principal_due_on_installment', 1) }}
                                                            </span>
                                                        @elseif ($key->loan_charge_option_id == 3)
                                                            <span>{{ number_format($key->amount, 2) }} %
                                                                {{ trans_choice('loan::general.principal_interest_due_on_installment', 1) }}
                                                            </span>
                                                        @elseif ($key->loan_charge_option_id == 4)
                                                            <span>{{ number_format($key->amount, 2) }} %
                                                                {{ trans_choice('loan::general.interest_due_on_installment', 1) }}
                                                            </span>
                                                        @elseif ($key->loan_charge_option_id == 5)
                                                            <span>{{ number_format($key->amount, 2) }} %
                                                                {{ trans_choice('loan::general.total_outstanding_loan_principal', 1) }}
                                                            </span>
                                                        @elseif ($key->loan_charge_option_id == 6)
                                                            <span>{{ number_format($key->amount, 2) }} %
                                                                {{ trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1) }}
                                                            </span>
                                                        @elseif ($key->loan_charge_option_id == 7)
                                                            <span>{{ number_format($key->amount, 2) }} %
                                                                {{ trans_choice('loan::general.original_loan_principal', 1) }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($key->active == 1)
                                                            <span class="label label-success">{{ trans_choice('core.yes', 1) }}</span>
                                                        @else
                                                            <span class="label label-danger">{{ trans_choice('core.no', 1) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                @endslot
            @endcomponent
        </div>
    </section>
@endsection

@section('tab-javascript')
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable();
        });
    </script>
    <script>
        var app = new Vue({
            el: "#vue-app",
            data: {
                records: {!! json_encode($data) !!},
                selectAll: false,
                selectedRecords: []
            },
            methods: {
                selectAllRecords() {
                    this.selectedRecords = [];
                    if (this.selectAll) {
                        this.records.data.forEach(item => {
                            this.selectedRecords.push(item.id);
                        });
                    }
                },
            },
        })
    </script>
@endsection
