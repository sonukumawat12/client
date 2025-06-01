@component('components.widget', ['class' => '', 'title' => 'Document Purpose '])

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="document_category">Document Purpose</label>
            <input type="text" name="document_type" id="document_purpose" class="form-control" placeholder="Document Purpose">
        </div>
    </div>
   
    <div class="col-md-3" style="padding-top: 22px">
        <button type="button" class="btn btn-primary" id="save_purpose">Save</button>
    </div>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="document_purposes_tables">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Document Purpose</th>
                    <th>User</th>
                    <th>Date Create</th>
                </tr>
            </thead>
            <tbody>
              
            </tbody>
        </table>
    </div>
</div>
 

 
@endcomponent
  
<script>
    $(document).ready(() => {
        loadTableData();
    function loadTableData() {
          console.log('Data loaded');
  $.ajax({
      url: '/DocManagement/document_purpose_gets',
    method: 'GET',
    success: function(response) {
      // Handle the success response from the server
      console.log('Data loaded successfully');
       
      // Update the table with the data
      updatePurposeTable(response);
    },
    error: function(xhr, status, error) {
      // Handle the error response from the server
      console.error('Error loading data:', error);
    }
  });
}

function updatePurposeTable(data) {
  var tableBody = $('#document_purposes_tables tbody');
  
  // Clear the table body
  tableBody.empty();
  var j=1;
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
      
    var row = '<tr>' +
    '<td>' + j + '</td>' +
      '<td>' + data[i].purpose_type + '</td>' +
      '<td>' + data[i].user + '</td>' +
      '<td>' + data[i].created_at + '</td>' +
      '</tr>';
      j=j+1;
    tableBody.append(row);
  }
} 

      

       $('#save_purpose').on('click', function() {
          var purpose = $('#document_purpose').val();
        console.log(purpose);
          $.ajax({
            url: '/DocManagement/store_purpose',
            method: 'get',
            data: {
              purpose: purpose
            },
            success: function(response) {
              // Handle the success response from the server
            loadTableData();
             $('#purpose').val('');
             // console.log('Commission type data sent successfully');
            
              toastr.success('Saved successfully');
            },
            error: function(xhr, status, error) {
              // Handle the error response from the server
              console.error('Error sending data:', error);
            }
          });
    });

   

        function updateTable(data) {
            console.log(data);
  var tableBody = $('#document_purposes_tables tbody ');
  
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