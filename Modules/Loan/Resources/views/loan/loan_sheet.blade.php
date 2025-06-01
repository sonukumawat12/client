<style>
    body {
        font-size: 9px;
    }

    .table {
        width: 100%;
        border: 1px solid #ccc;
        border-collapse: collapse;
    }

    .table th,
    td {
        padding: 5px;
        text-align: left;
        border: 1px solid #ccc;
    }

    .light-heading th {
        background-color: #eeeeee
    }

    .green-heading th {
        background-color: #4CAF50;
        color: white;
    }

    .text-center {
        text-align: center;
    }

    .table-striped tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .text-danger {
        color: #a94442;
    }

    .text-success {
        color: #3c763d;
    }

    a {
        text-decoration: none;
    }
</style>
<h3 class="text-center">{{ get_business_name() }}</h3>
<h3 class="text-center"> {{ trans_choice('loan::general.loan_sheet', 1) }}</h3>
<table id="data-table" class="table table-striped table-condensed table-hover">
    <thead>
        <tr>
            <th>
                <span>{{ trans_choice('core.id', 1) }}</span>
            </th>
            <th>
                <span>{{ trans_choice('core.location', 1) }}</span>
            </th>
            <th>
                <span>{{ trans_choice('loan::general.loan_officer', 1) }}</span>
            </th>
            <th>
                <span>{{ trans_choice('core.contact', 1) }}</span>
            </th>
            <th>
                <span>{{ trans_choice('core.amount', 1) }}</span>
            </th>
            <th>
                <span>{{ trans_choice('loan::general.balance', 1) }}</span>
            </th>
            <th>
                <span>{{ trans('loan::general.disbursed') }}</span>
            </th>
            <th>
                <span>{{ trans_choice('loan::general.product', 1) }}</span>
            </th>
            <th>
                <span>{{ trans_choice('loan::general.status', 1) }}</span>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key)
            <tr>
                <td>
                    <span>{{ $key->id }}</span>
                </td>
                <td>
                    <span>{{ $key->location }}</span>
                </td>
                <td>
                    <span>{{ $key->loan_officer }}</span>
                </td>
                <td>
                    <span>{{ $key->contact }}</span>
                </td>
                <td>
                    <span>{{ number_format($key->principal, 2) }}</span>
                </td>
                <td>
                    <span>{{ number_format($key->total_principal - $key->principal_repaid_derived - $key->principal_written_off_derived + ($key->total_interest - $key->interest_repaid_derived - $key->interest_written_off_derived - $key->interest_waived_derived) + ($key->total_fees - $key->fees_repaid_derived - $key->fees_written_off_derived - $key->fees_waived_derived) + ($key->total_penalties - $key->penalties_repaid_derived - $key->penalties_written_off_derived - $key->penalties_waived_derived), $key->decimals) }}</span>
                </td>
                <td>
                    <span>{{ $key->disbursed_on_date }}</span>
                </td>
                <td>
                    <span>{{ $key->loan_product }}</span>
                </td>
                <td>
                    @if ($key->status == 'pending')
                        <span class="label label-warning">{{ trans_choice('loan::general.pending_approval', 1) }}</span>
                    @endif
                    @if ($key->status == 'submitted')
                        <span class="label label-warning">{{ trans_choice('loan::general.pending_approval', 1) }}</span>
                    @endif
                    @if ($key->status == 'overpaid')
                        <span class="label label-warning">{{ trans_choice('loan::general.overpaid', 1) }}</span>
                    @endif
                    @if ($key->status == 'approved')
                        <span class="label label-warning">{{ trans_choice('loan::general.awaiting_disbursement', 1) }}</span>
                    @endif
                    @if ($key->status == 'active')
                        <span class="label label-info">{{ trans_choice('loan::general.active', 1) }}</span>
                    @endif
                    @if ($key->status == 'rejected')
                        <span class="label label-danger">{{ trans_choice('loan::general.rejected', 1) }}</span>
                    @endif
                    @if ($key->status == 'withdrawn')
                        <span class="label label-danger">{{ trans_choice('loan::general.withdrawn', 1) }}</span>
                    @endif
                    @if ($key->status == 'written_off')
                        <span class="label label-danger">{{ trans_choice('loan::general.written_off', 1) }}</span>
                    @endif
                    @if ($key->status == 'closed')
                        <span class="label label-success">{{ trans_choice('loan::general.closed', 1) }}</span>
                    @endif
                    @if ($key->status == 'pending_reschedule')
                        <span class="label label-warning">{{ trans_choice('loan::general.pending_reschedule', 1) }}</span>
                    @endif
                    @if ($key->status == 'rescheduled')
                        <span class="label label-info">{{ trans_choice('loan::general.rescheduled', 1) }}</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
