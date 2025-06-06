@component('components.widget', ['class' => '', 'title' => 'Document Category '])

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="document_category">Document Category</label>
            <input type="text" name="document_category" id="document_category" class="form-control" placeholder="Document Category">
        </div>
    </div>
   
    <div class="col-md-3" style="padding-top: 22px">
        <button type="button" class="btn btn-primary" id="save_category">Save</button>
    </div>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="document_category_tables">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Document Type</th>
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
  $.ajax({
      url: '/DocManagement/document_category_gets',
    method: 'GET',
    success: function(response) {
      // Handle the success response from the server
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
  var tableBody = $('#document_category_tables tbody');
  
  // Clear the table body
  tableBody.empty();
  var j=1;
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
      
    var row = '<tr>' +
    '<td>' + j + '</td>' +
      '<td>' + data[i].document_category + '</td>' +
      '<td>' + data[i].user + '</td>' +
      '<td>' + data[i].created_at + '</td>' +
      '</tr>';
      j=j+1;
    tableBody.append(row);
  }
} 

      

       $('#save_category').on('click', function() {
          var categoryType = $('#document_category').val();
        console.log(categoryType);
          $.ajax({
            url: '/DocManagement/store_category_type',
            method: 'get',
            data: {
              categoryType: categoryType
            },
            success: function(response) {
              // Handle the success response from the server
            loadTableData();
             $('#categoryType').val('');
             // console.log('Commission type data sent successfully');
            
              toastr.success('Category Type type data sent successfully');
            },
            error: function(xhr, status, error) {
              // Handle the error response from the server
              console.error('Error sending category type data:', error);
            }
          });
    });

   

        function updateTable(data) {
            console.log(data);
  var tableBody = $('#document_category_tables tbody ');
  
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