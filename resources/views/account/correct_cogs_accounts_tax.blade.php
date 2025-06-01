@extends('layouts.app')
@section('title', "Correct COGS Accounts Tax")

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
    <h1>Correct COGS Accounts Tax</h1>
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
                        <th>Account</th>
                        <th>Entries To Update</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $count = 0;
                    @endphp
                    @foreach ($accounts as $account)
                        @php
                            if ($account->account_transactions_to_correct == 0) {
                                continue;
                            }
                            $count++;
                        @endphp
                        <tr>
                            <td>{{ $account->name }}</td>
                            <td>{{ $account->account_transactions_to_correct }}</td>
                            <td>
                                <a href="{{ route('accounts.updateCOGSAccountsTax', $account->id) }}" 
                                    class="btn btn-success" 
                                    onclick="disableLinks(event, '{{ $account->name }}', {{ $account->account_transactions_to_correct }});">
                                    Correct
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    @if ($count == 0)
                        <tr>
                            <td></td>
                            <td><h2>All COGS Accounts Tax Updated</h2></td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @if ($count != 0)
        <a href="{{ route('accounts.updateCOGSAccountsTax', 'All') }}" 
            class="btn btn-success" 
            onclick="disableLinks(event, 'All');">
            Correct All
        </a>
    @endif
    
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