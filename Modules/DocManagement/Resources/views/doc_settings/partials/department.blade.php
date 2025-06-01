 

@component('components.widget', ['class' => '', 'title' => 'Department'])
 
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="department_table" style="width:100%!important">
                <thead>
                    <tr>
                        <th width="10%">Date Added</th>
                        <th width="20%">Department </th>
                        <th width="20%">Description</th>
                         
                        
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
      url: '/DocManagement/document_department_gets',
    method: 'GET',
    success: function(response) {
      // Handle the success response from the server
      console.log('Data loaded successfully');
       
      // Update the table with the data
      updateDepartmentTable(response);
    },
    error: function(xhr, status, error) {
      // Handle the error response from the server
      console.error('Error loading data:', error);
    }
  });
}

function updateDepartmentTable(data) {
  var tableBody = $('#department_table tbody');
  
  // Clear the table body
  tableBody.empty();
  var j=1;
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
      
    var row = '<tr>' +
       '<td>' + data[i].created_at + '</td>' +
      '<td>' + data[i].department + '</td>' +
      '<td>' + data[i].description + '</td>' +
    
      '</tr>';
    
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
  var tableBody = $('#department_table tbody ');
  
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