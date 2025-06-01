@extends('layouts.app')
@section('title', "Update Cash Account Pos Sales Tax")

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
    <h2>Update Cash Account Pos Sales Tax</h2>
    <hr>
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
                        <th>Entries to update</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($account_transactions > 0)
                        <tr>
                            <td>{{ $account_transactions }}</td>
                            <td>
                                <a href="{{ route('accounts.updateCashAccountPosSaleTax', 0) }}" 
                                    class="btn btn-success" 
                                    onclick="disableLinks(event, '', {{ $account_transactions }});">
                                    Update
                                </a>
                            </td>
                        </tr>
                    @endif
                    @if ($account_transactions == 0)
                        <tr>
                            <td><h4>No Cash Account Pos Sales Tax to update</h4></td>
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