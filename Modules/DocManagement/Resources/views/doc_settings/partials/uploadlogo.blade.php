 

@component('components.widget', ['class' => '', 'title' => 'Upload Logo'])
    <div class="row" style="margin-bottom: 4px;">
        <button type="button" class="btn btn-primary btn-modal pull-right" id="create_airports">
            <i class="fa fa-plus"></i>
            Add
        </button>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="document_logo_tables" style="width:100%!important">
                <thead>
                    <tr>
                         <th width="10%">Date</th>
                        <th width="20%">Upload Logo</th>
                        <th width="20%">Logo Position</th>
                        <th width="20%">Enable Button</th>
                        <th width="10%">Username</th>
                   
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
<div class="modal fade" id="airport_form_modals" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document"  >
        {!! Form::open(['url' => action('\Modules\DocManagement\Http\Controllers\DocManagementSettingsController@store_logo'), 'method' => 'post', 'id' => 'logo','enctype'=>"multipart/form-data",'files' => true ]) !!}

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Upload Logo</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                          <div class="col-md-6"> 
                        <div class="form-group" style="width:100%;"> 
                        {!! Form::label('image',__('Upload Logo')) !!} 
                        {!! Form::file('image', ['id' => 'image','accept' => 'image/*']); !!}
                        </div>
                        </div> 
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('country_select', __('Location') . ':*') !!}
                        <div class="input-group" style="width:100%;">
                            {!! Form::select('location', ['left' => 'Left', 'right' => 'Right', 'center' => 'Center'], null, [
                                'class' => 'form-control select2',
                                'id' => 'location',
                                'required',
                                'placeholder' => __('Location'),
                            ]) !!}
                        </div>
                    </div>
                </div>
                        </div>
                        
                         <div class="row">
                               <div class="col-md-6 ">
                        <div class="form-group">
                              {!! Form::label('country_select', __('User ') . ':*') !!}
                               <div class="input-group" style="width:100%;">
                          
                            {!! Form::select('user', $username, null, [
                                'class' => 'form-control select2',
                                'id' => 'user',
                                'required',
                                'placeholder' => __('User'),
                            ]) !!}
                        </div>
                        </div>
                         </div>
                          <div class="col-md-6">
    <div class="form-group">
        {!! Form::label('Option ', __('Option ') . '') !!}
       <div class="input-group" style="width:100%;">
     {!! Form::select('enable_disable_buttons', ['enabled' => 'enabled', 'disabled' => 'disabled'], null, [
                                'class' => 'form-control select2',
                                'id' => 'enable_disable_buttons',
                                'required',
                                'placeholder' => __('Option'),
                            ]) !!}
</div>
    </div>
</div>
                          </div>
                           
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"  data-dismiss="modal">Close</button>
                        <button type="submit"   class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>

 
<script>
$(document).ready(function() {
    $('#enable_button').click(function() {
        $('#designations').prop('disabled', false);
        toggleEnableDisableButtons(false);
    });

    $('#disable_button').click(function() {
        $('#designations').prop('disabled', true);
        toggleEnableDisableButtons(true);
    });

    function toggleEnableDisableButtons(disabled) {
        if (disabled) {
            $('#enable_button').show();
            $('#disable_button').hide();
        } else {
            $('#enable_button').hide();
            $('#disable_button').show();
        }
    }
    
    // Initially hide the "Disable" button
    toggleEnableDisableButtons(true);
});
$(document).ready(function() {
    // Add event listener to the "Add" button
    $('#create_airports').click(function() {
        // Show the modal
        $('#airport_form_modals').modal('show');
    });
});
  $(document).ready(() => {
      $(document).ready(function() {
   $('#logo').submit(function(event) {
    event.preventDefault(); // Prevent the form from submitting normally

    // Make an AJAX request
    $.ajax({
      url: $(this).attr('action'), // Get the form action URL
      method: $(this).attr('method'), // Get the form method (POST in this case)
      data: new FormData(this), // Get the form data

      // Configure AJAX to handle file uploads
      processData: false,
      contentType: false,

      success: function(response) {
        // Display a success toast message
        toastr.success('Data saved successfully!', 'Success');
      },
      error: function(xhr, status, error) {
        // Display an error toast message
        toastr.error('Failed to save data!', 'Error');
      }
    });
  });  
});
        loadUploadTableData();
    function loadUploadTableData() {
       
  $.ajax({
      url: '/DocManagement/document_uploadlogo_gets',
    method: 'GET',
    success: function(response) {
       
     
      console.log('Data loaded successfully');
       
      // Update the table with the data
      updateUploadTable(response);
    },
    error: function(xhr, status, error) {
      // Handle the error response from the server
      console.error('error:', error);
    }
  });
}

function updateUploadTable(data) {
  var tableBody = $('#document_logo_tables tbody');
  console.log("type table");
  // Clear the table body
  tableBody.empty();
  var j=1;
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
      
    var row = '<tr>' +
       '<td>' + data[i].created_at + '</td>' +
       '<td>' + data[i].upload_logo + '</td>' +
    '<td>' + data[i].position + '</td>' +
    '<td>' + data[i].enable_button + '</td>' +
    '<td>' + data[i].username + '</td>' +
   
        
     
      '</tr>';
     
    tableBody.append(row);
  }
} 

 
   

        function updateTable(data) {
            console.log(data);
  var tableBody = $('#document_type_tables tbody ');
  
  // Clear the table body
  tableBody.empty();
  
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
    var row = '<tr>' +
      '<td>' + data[i].column1 + '</td>' +
      '<td>' + data[i].column2 + '</td>' +
      '<td>' + data[i].column3 + '</td>' +
      // Add more columns as needed
      '</tr>';
      
    tableBody.append(row);
  }
}
    }); 
</script>

<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.toggle-switch .toggle-input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-switch .toggle-label {
    position: absolute;
    top: 0;
    left: 0;
    width: 60px;
    height: 34px;
    background-color: #ccc;
    border-radius: 34px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.toggle-switch .toggle-label:before {
    content: "";
    position: absolute;
    width: 26px;
    height: 26px;
    top: 4px;
    left: 4px;
    background-color: #fff;
    border-radius: 50%;
    transition: transform 0.3s;
}

.toggle-switch .toggle-input:checked + .toggle-label {
    background-color: #4CAF50;
}

.toggle-switch .toggle-input:checked + .toggle-label:before {
    transform: translateX(26px);
}
    .select2-container .select2-selection {
        height: 34px;
        border: 1px solid #ccc;
    }
</style>
