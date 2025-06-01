<!-- resources/views/myhealth/patient/sugar_reading.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Your existing HTML for the table and other elements -->

    <!-- Include jQuery if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include SweetAlert JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    <script>
        $(document).ready(function() {
            @if(isset($showAlert) && $showAlert)
                // Show SweetAlert
                swal({
                    title: "Success!",
                    text: "Medicine data has been loaded.",
                    type: "success",
                    timer: 3000, // Duration in milliseconds
                    showConfirmButton: false
                });
            @endif
        });
    </script>
</div>
@endsection
