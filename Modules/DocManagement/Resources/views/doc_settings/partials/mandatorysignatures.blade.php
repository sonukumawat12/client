@component('components.widget', ['class' => '', 'title' => 'Mandatory Signatures'])

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="document_category">No. Mandatory Signatures</label>
            <input type="text" name="no_signature" id="no_signature" class="form-control" placeholder="No. Mandatory Signatures">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="document_category">Mandatory Signatures</label>
            <input type="text" name="document_signature" id="document_signature" class="form-control" placeholder="Mandatory Signatures">
        </div>
    </div>
   
    <div class="col-md-3" style="padding-top: 22px">
        <button type="button" class="btn btn-primary" id="save_mandatory">Save</button>
    </div>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="document_mandatory_tables">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Mandatory Signature</th>
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
        loadTableMandatoryData();
    function loadTableMandatoryData() {
          console.log('Data loaded');
  $.ajax({
      url: '/DocManagement/document_mandatorysignature_gets',
    method: 'GET',
    success: function(response) {
        console.log(response)
      // Handle the success response from the server
      console.log('Data loaded successfully Mandatory');
       
      // Update the table with the data
      updateTable(response);
    },
    error: function(xhr, status, error) {
      // Handle the error response from the server
      console.error('Error loading data:', error);
    }
  });
}

function updateTable(data) {
    console.log("mandatory data");
  var tableBody = $('#document_mandatory_tables tbody');
   
  // Clear the table body
  tableBody.empty();
  var j=1;
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
      
    var row = '<tr>' +
    '<td>' + j + '</td>' +
      '<td>' + data[i].no_of_mandatory + '</td>' +
      '<td>' + data[i].signature_level + '</td>' +
      '<td>' + data[i].user + '</td>' +
      '<td>' + data[i].created_at + '</td>' +
      '</tr>';
      j=j+1;
    tableBody.append(row);
  }
} 

      

       $('#save_mandatory').on('click', function() {
            var number = $('#no_signature').val();
          var mandatory = $('#document_signature').val();
          
        console.log(mandatory);
          $.ajax({
            url: '/DocManagement/store_mandatorySignature',
            method: 'get',
            data: {
            number:number,
              mandatory: mandatory
            },
            success: function(response) {
              // Handle the success response from the server
            loadTableMandatoryData();
             $('#document_signature').val('');
                $('#no_signature').val('');
             // console.log('Commission type data sent successfully');
            
              toastr.success('Data sent successfully');
            },
            error: function(xhr, status, error) {
              // Handle the error response from the server
              console.error('Error sending category type data:', error);
            }
          });
    });

   

        function updateMandatoryTable(data) {
           
  var tableBody = $('#document_mandatory_tables tbody ');
  
  // Clear the table body
  tableBody.empty();
  
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
    var row = '<tr>' +
      '<td>' + data[i].column1 + '</td>' +
      '<td>' + data[i].column2 + '</td>' +
      '<td>' + data[i].column3 + '</td>' +
       '<td>' + data[i].column4 + '</td>' +
        // Add more columns as needed
      '</tr>';
      
    tableBody.append(row);
  }
}
    });
</script>