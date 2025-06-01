@component('components.widget', ['class' => '', 'title' => 'Document Forwarded with'])

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="document_category">Document Forwarded with</label>
            <input type="text" name="document_fowardwith" id="document_fowardwith" class="form-control" placeholder="Forwarded with">
        </div>
    </div>
   
    <div class="col-md-3" style="padding-top: 22px">
        <button type="button" class="btn btn-primary" id="save_forwardwith">Save</button>
    </div>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="document_forwardwith_tables">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Forwarded with</th>
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
      url: '/DocManagement/document_forwardwith_gets',
    method: 'GET',
    success: function(response) {
      // Handle the success response from the server
      console.log("forwardwith"+response)
      console.log('Data loaded successfully');
       
      // Update the table with the data
      updateCategoryTable(response);
    },
    error: function(xhr, status, error) {
      // Handle the error response from the server
      console.error('Error loading data:', error);
    }
  });
}

function updateCategoryTable(data) {
  var tableBody = $('#document_forwardwith_tables tbody');
  
  // Clear the table body
  tableBody.empty();
  var j=1;
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
      
    var row = '<tr>' +
    '<td>' + j + '</td>' +
      '<td>' + data[i].forwarded_with + '</td>' +
      '<td>' + data[i].user + '</td>' +
      '<td>' + data[i].created_at + '</td>' +
      '</tr>';
      j=j+1;
    tableBody.append(row);
  }
} 

      

       $('#save_forwardwith').on('click', function() {
          var fowardwith = $('#document_fowardwith').val();
        console.log(fowardwith);
          $.ajax({
            url: '/DocManagement/store_forwardwith',
            method: 'get',
            data: {
              fowardwith: fowardwith
            },
            success: function(response) {
              // Handle the success response from the server
            loadTableData();
             $('#fowardwith').val('');
             // console.log('Commission type data sent successfully');
            
              toastr.success('Data sent successfully');
            },
            error: function(xhr, status, error) {
              // Handle the error response from the server
              console.error('Error sending category type data:', error);
            }
          });
    });

   

        function updateTable(data) {
            console.log(data);
  var tableBody = $('#document_forwardwith_tables tbody ');
  
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