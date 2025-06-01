    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="linkedSupplierAccountModalLabel">{{ __('Linked Supplier Account') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['url' => route('airline.linked_supplier_account'), 'method' => 'post', 'id' => 'linked_supplier_account_form']) !!}

                 <!-- Hidden ID Field -->
               {!! Form::hidden('id', null, ['id' => 'linked_account_id']) !!}
               {!! Form::hidden('business_id', $business_id, ['id' => 'business_id']) !!}
               {!! Form::hidden('supplier_id', $supplier_id, ['id' => 'supplier_id']) !!}
                <!-- Date & Time Field -->
                <div class="form-group">
                    {!! Form::label('date_time', __('Date & Time')) !!}
                    {!! Form::datetimeLocal('date_time', now()->format('Y-m-d\TH:i'), ['class' => 'form-control', 'required', 'readonly']) !!}
                </div>
                
                <!-- Account Group Dropdown -->
                <div class="form-group">
                    {!! Form::label('account_types', __('Account Type')) !!}
                    {!! Form::select('account_types', $account_types, null, ['class' => 'form-control', 'id' => 'account_types', 'placeholder' => 'Select Account Type']) !!}
                </div>
                
                <div class="form-group">
                    {!! Form::label('sub_account_types', __('Sub Account Type')) !!}
                    {!! Form::select('sub_account_types', $sub_account_types, null, ['class' => 'form-control', 'id' => 'sub_account_types', 'placeholder' => 'Select Sub Account Type']) !!}
                </div>
                
                <div class="form-group">
                    {!! Form::label('account', 'Account') !!}
                    {!! Form::select('account', $accounts, null, ['class' => 'form-control', 'id' => 'account', 'placeholder' => 'Select Account']) !!}
                </div>

                <!-- Table for Added Accounts -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered" id="added_accounts_table">
                        <thead>
                            <tr>
                                <th>{{ __('Date & Time') }}</th>
                                <th>{{ __('Account Type') }}</th>
                                <th>{{ __('Sub Account Type') }}</th>
                                <th>{{ __('Added User') }}</th>
                                <th>{{ 'Account' }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamically populated rows -->
                        </tbody>
                    </table>
                </div>

                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary" id="save_linked_account">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
    
  @section('javascript')

<script>
    $(document).ready(function(){
        // Add event listener when the modal is shown
        $('.linked_account_modal').on('shown.bs.modal', function () {
            console.log("Modal opened");

            // Attach the change event to account group dropdown
        $('#account_group').change(function() {
            if (navigator.onLine) { // Check if online
                let accountGroupId = $(this).val();
                $.ajax({
                    url: '{{ route("get.accounts.by.group", ":accountGroupId") }}'.replace(':accountGroupId', accountGroupId),
                    method: 'GET',
                    success: function(data) {
                        $('#account').empty().append('<option value="">{{ __("Select Account") }}</option>');
                        $.each(data.accounts, function(index, account) {
                            $('#account').append('<option value="' + account.id + '">' + account.name + '</option>');
                        });
                    }
                });
            } else {
                alert("You are offline. Please connect to the internet and try again.");
            }
        });
         // Save button to add account to the table
        $('#save_linked_account').click(function() { // ***Using the onclick on the index view***
        let dateTime = $('#date_time').val();
        let accountGroup = $('#account_group option:selected').val(); // Get the selected account group id
        let account = $('#account option:selected').val(); // Get the selected account id
        let account_type = $('#account_types option:selected').val(); // Get the selected account id
        let sub_account_type = $('#sub_account_types option:selected').val(); // Get the selected account id
    
        $.ajax({
            url: '{{ route("airline.submit_linked_supplier_account") }}', // Ensure this route is correct
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                date_time: dateTime,
                account_group_id: accountGroup,
                account_id: account
            },
            success: function(response) {
                if (response.success) {
                    // If save is successful, add the new row to the table
                    let newRow = `<tr>
                                    <td>${dateTime}</td>
                                    <td>${response.accountGroupName}</td>
                                    <td>${response.accountName}</td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-primary edit-account">Edit</button>
                                        <button type="button" class="btn btn-xs btn-danger delete-account">Delete</button>
                                    </td>
                                  </tr>`;
                    $('#added_accounts_table tbody').append(newRow);
                    // Optionally, clear the form after successful save
                    $('#linked_supplier_account_form')[0].reset();
                } else {
                    alert('Error saving account.');
                }
            }
        });
    });

    
        // Edit and Delete functionality in the table
        $('#added_accounts_table').on('click', '.edit-account', function() {
            // Add logic to edit the selected account row
        });
    
        $('#added_accounts_table').on('click', '.delete-account', function() {
            $(this).closest('tr').remove();
        });

        });
    });
</script>

@endsection
