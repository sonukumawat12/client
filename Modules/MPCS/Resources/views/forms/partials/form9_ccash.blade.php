<!-- Main content -->
<section class="content">
     <style>
        /* Simple CSS for the popup */
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            border: 1px solid #ccc;
            background-color: #fff;
            padding: 20px;
            z-index: 1000;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
    
    
 <h1>Popup Form</h1>

    <button class="btn btn-primary"  onclick="openPopup()">Form 9 C Cash Settings</button>

    <div class="popup-overlay" id="popup-overlay"></div>
    <div class="popup" id="popup-form">
        <form action="{{ url('popup-form') }}" method="POST">
            @csrf

            <label for="date_time">Date & Time:</label>
            <input type="text" id="date_time" name="date_time" value="{{ now()->format('Y-m-d H:i:s') }}" readonly><br><br>

            <label for="starting_number">Form Starting Number:</label>
            <input type="text" id="starting_number" name="starting_number" required><br><br>

            <label for="previous_note_amount">Previous Note Amount:</label>
            <input type="number" step="0.01" id="previous_note_amount" name="previous_note_amount" required><br><br>

            <button type="submit">Submit</button>
            <button type="button" onclick="closePopup()">Close</button>
        </form>
    </div>
    
    <!-- Display Settings -->
<table class="table">
    <thead>
        <tr>
            <th>Action</th>
            <th>Date & Time</th>
            <th>Form Starting Number</th>
            <th>Previous Note</th>
            <th>User Added</th>
        </tr>
    </thead>
    <tbody>
        
        <tr>
            <td>
                @if(auth()->user()->role == 'Super Admin')
                    <a href="" class="btn btn-sm btn-warning">Edit</a>
                @endif
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        
    </tbody>
</table>


    <script>
        function openPopup() {
            document.getElementById('popup-overlay').style.display = 'block';
            document.getElementById('popup-form').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('popup-overlay').style.display = 'none';
            document.getElementById('popup-form').style.display = 'none';
        }
    </script>
</section>
<!-- /.content -->