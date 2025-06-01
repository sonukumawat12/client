@extends('layouts.app')
@section('title', "Correct Transaction Sell Lines Decimal Difference")

@section('content')
<section class="content main-content-inner">
    <style>
        .disabled {
            pointer-events: none; /* Prevent any interaction */
            opacity: 0.6;
            text-decoration: none;
            cursor: not-allowed;
        }
    </style>
    <h2>Correct Transaction Sell Lines Decimal Difference</h2>
    <h3>for current product prices</h3>
    @if(session('msg'))
        <div class="alert alert-success">
            {{ session('msg') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Affected Entries</th>
                        <th>Affected Accounts</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($transaction_sell_lines > 0)
                        <tr>
                            <td>{{ $transaction_sell_lines }}</td>
                            <td>{{ $account_transactions_to_correct_count }}</td>
                            <td>
                                <a href="{{ route('accounts.updateSellLinesDecimalDifference') }}" 
                                    class="btn btn-success" 
                                    onclick="disableLinks(event, '', {{ $transaction_sell_lines }});">
                                    Correct
                                </a>
                            </td>
                        </tr>
                    @endif
                    @if ($transaction_sell_lines == 0)
                        <tr>
                            <td><h2>No Affected Transaction Sell Lines Decimal Difference for current product prices</h2></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        function disableLinks(event, accountName = '', affectedEntries = 0) {
            // Confirm action
            const confirmMessage = accountName === 'All'
                ? `Update all entries?`
                : `Update ${accountName} (${affectedEntries} entries)?`;
    
            if (!confirm(confirmMessage)) {
                event.preventDefault(); // Prevent the link action if canceled
                return;
            }
    
            // Disable all links
            const links = document.querySelectorAll('a');
            links.forEach(link => {
                link.classList.add('disabled');
                link.style.pointerEvents = 'none'; // Disable click events
                link.style.opacity = '0.6'; // visually indicate disabled state
            });
        }
    </script>
</section>
@endsection