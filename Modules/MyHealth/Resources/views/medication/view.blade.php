<!-- resources/views/medicine/view.blade.php -->
<div class="modal-header">
    <h5 class="modal-title">Patient Medicines</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    @if($patient_medicines)
        <table class="table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Dosage</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patient_medicines as $medicine)
                    <tr>
                        <td>{{ $medicine->medicine_name }}</td>
                        <td>{{ $medicine->amount }}</td>
                        <td>{{ $medicine->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No medicines found for this patient.</p>
    @endif
</div>
