@extends('layouts.app')
@section('title', "Update Databases")

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
    <h2>Update Databases</h2>
    <hr>
    @if(session('msg'))
        <div class="alert alert-success">
            {{ session('msg') }}
        </div>
    @endif
    @if(session('processLogs'))
        <div class="alert alert-info">
            <ul>
                @foreach(session('processLogs') as $log)
                    <li>{{ $log }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <h4><b>Databases to update</b></h4>
            <ul>
                @foreach ($tenantIds as $tenantId)
                    <div class="col-md-2">
                        <li>
                            {{ $prefix.$tenantId }}
                        </li>
                    </div>
                @endforeach
            </ul>
            <div class="col-md-12">
                <hr>
                <form action="{{ route('database.updateDatabases') }}" method="post" enctype="multipart/form-data" onsubmit="return disableLinks(event, 'All');">
                    @csrf
                    <div class="form-group">
                        <label for="sqlFile">Upload SQL File</label>
                        <input type="file" name="sqlFile" id="sqlFile" class="form-control" accept=".sql" required>
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-success">Update Databases</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function disableLinks(event, database = '') {
            // Confirm action
            const confirmMessage = database === 'All'
                ? `Update all databases?`
                : `Update ${database} database?`;
    
            if (!confirm(confirmMessage)) {
                event.preventDefault(); // Prevent the submission
                return;
            }
    
            // Disable submit button to prevent multiple submissions
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';
        }
    </script>
</section>
@endsection