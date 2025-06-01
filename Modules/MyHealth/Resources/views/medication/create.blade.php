<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title" id="exampleModalLabel">@lang('patient.add_health_record')</h3>
        </div>
        <div class="modal-body">
            <!-- Form start -->
       {!! Form::open(['route' => 'medication.store', 'method' => 'post', 'id' => 'frmAddHealthRecord']) !!}

            
            <div class="row">
                <!-- a. Date => Date Picker (Current date by default) -->
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('date', __('patient.date') . ':') !!}
                        <div class="input-group" style="width:100%">
                            {!! Form::date('date', now()->format('Y-m-d'), ['class' => 'form-control', 'required', 'id' => 'date_picker', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                </div>


                <!-- b. Health issue diagnosed on => Date Picker (Current date by default) -->
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('diagnosed_date', __('patient.diagnosed_on') . ':') !!}
                        <div class="input-group" style="width:100%">
                            {!! Form::date('diagnosed_date', now()->format('Y-m-d'), ['class' => 'form-control', 'required', 'style' => 'width:100%', 'id' => 'diagnosed_date_picker']) !!}
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="row">
                <!-- c. Health Issue => Manual entry (Compulsory) -->
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('health_issue', __('patient.health_issue') . ':') !!}
                        {!! Form::text('health_issue', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>

                <!-- d. Doctor (Name) => Manual entry -->
           <div class="col-md-6">
    <div class="form-group">
        {!! Form::label('doctor_name', __('patient.doctor_name') . ':') !!}
        <div class="input-group" style="display: flex; width: 100%;">
            {!! Form::select('doctor_name', $patient_doctors, ($defaultVal) ? $defaultVal['doctor_name'] : null, [
                'class' => 'form-control select2 filter-control flex-grow-1',
                'style' => 'width:100%',
                'id' => 'doctor_name'
            ]) !!}
           <!-- Add Doctor Button (replacing modal trigger) -->
<div class="input-group-append">
    <button class="btn btn-primary" type="button" id="addDoctorButton">
        <i class="fa fa-plus"></i>
    </button>
</div>
        </div>
    </div>
</div>



            </div>

            <div class="row">
                <!-- e. Medicine Name => Manual entry -->
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('medicine_name', __('patient.medicine_name') . ':') !!}
                        {!! Form::text('medicine_name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>

                <!-- f. Dose => Manual entry -->
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('dose', __('patient.dose') . ':') !!}
                        {!! Form::text('dose', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('frequency', __('patient.frequency') . ':') !!}
                        {!! Form::text('frequency', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <!-- Save Button -->
                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>




<!-- Datepicker Script -->
@section('javascript')
<script>
    // Initialize datepickers
    $('#date_picker, #diagnosed_date_picker').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY/MM/DD'
        }
    });
    
    

</script>

@endsection
<!-- CSS for Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

<!-- JS for Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<style>
    /* Ensures the Choices.js dropdown appears below the select input */
    .choices {
        position: relative; /* Ensures proper positioning */
        z-index: 9999; /* Base z-index for the container */
    }

    .choices__list--dropdown {
        position: relative; /* Make dropdown absolute */
        top: 100%; /* Position it below the input */
        left: 0; /* Align it with the left edge of the input */
        z-index: 9999; /* Higher z-index to appear above other elements */
    }
</style>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('addDoctorButton').addEventListener('click', function() {
    // Fetch the specializations from the backend
    $.ajax({
        url: '/specializations', // Fetch specializations using the GET route
        method: 'GET',
     success: function(specializations) {
    let specializationOptions = '';
    specializations.forEach(spec => {
        specializationOptions += `<option value="${spec.id}">${spec.name}</option>`;
    });

    Swal.fire({
        title: 'Add Doctor',
        width: '600px',  // Increase popup width
        height: '600px',
        html: `
            <form id="addDoctorForm">
                <div class="form-group">
                    <label for="doctorNameInput">Doctor Name</label>
                    <input type="text" class="form-control" id="doctorNameInput" placeholder="Enter doctor's name">
                </div>
                <div class="form-group">
                    <label for="specializationSelect">Specialization</label>
                    <select class="form-control" id="specializationSelect">
                        <option value="">Select Specialization</option>
                        ${specializationOptions}
                    </select>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save',
        didOpen: () => {
            // Initialize Choices.js for the specialization dropdown
            const specializationSelect = document.getElementById('specializationSelect');
            const choices = new Choices(specializationSelect, {
                searchEnabled: true, // Enable searching
                placeholder: true,
                placeholderValue: 'Select Specialization',
                removeItemButton: true,
                shouldSort: true,
            });
        },
        preConfirm: () => {
            const doctorName = document.getElementById('doctorNameInput').value;
            const specialization = document.getElementById('specializationSelect').value;

            if (!doctorName) {
                Swal.showValidationMessage('Please enter a doctor\'s name');
            }
            if (!specialization) {
                Swal.showValidationMessage('Please select a specialization');
            }

            return { doctorName: doctorName, specialization: specialization };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const doctorName = result.value.doctorName;
            const specialization = result.value.specialization;

            // AJAX call to save doctor
            $.ajax({
                url: '/add_doctor', // POST route to save doctor
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    doctor_name: doctorName,
                    specialization: specialization
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Saved!', 'Doctor added successfully', 'success');
                        const newOption = new Option(doctorName, response.newDoctorId);
                        $('#doctor_name').append(newOption).val(response.newDoctorId);
                    } else {
                        Swal.fire('Error!', 'Something went wrong', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Unable to add doctor', 'error');
                }
            });
        }
    });
}



    });
});


</script>


