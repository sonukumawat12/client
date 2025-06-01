@component('components.widget', ['class' => '', 'title' => 'Linked Accounts'])


<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="account_type">Account type</label>
            <select name="account_type" id="account_type" class="form-control select2">
                <option value="all" selected>All</option>
                @foreach ($accounts_type as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="account">Account</label>
            <select name="account" id="account" class="form-control select2">
                @foreach ($accounts as $acc)
                <option data-type="{{ $acc->account_type_id }}" value="{{ $acc->id }}">{{ $acc->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3" style="padding-top: 22px">
        <button type="button" class="btn btn-primary" id="save_account">Save</button>
    </div>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="transaction">
            <thead>
                <tr>
                      <th>No</th>
                    <th>Account Type</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    
                    <th>Created By</th>
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
        console.log("Linked account");
  $.ajax({
      url: '/airline/airport_linked_account_get',
    method: 'GET',
    success: function(response) {
      // Handle the success response from the server
      console.log('Data loaded successfully');
      // Update the table with the data
      updateAirlineLinkedTable(response);
    },
    error: function(xhr, status, error) {
      // Handle the error response from the server
      console.error('Error loading data:', error);
    }
  });
}

function updateAirlineLinkedTable(data) {
  var tableBody = $('#transaction tbody');
  
  // Clear the table body
  tableBody.empty();
  var j=1;
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
      
    var row = '<tr>' +
    '<td>' + j + '</td>' +
      '<td>' + data[i].AccountType + '</td>' +
      '<td>' + data[i].AcoountName + '</td>' +
      '<td>' + data[i].AccountNumber + '</td>' +
       '<td>' + data[i].user + '</td>' +
        '<td>' + data[i].created_at.split('T')[0] + '</td>' +
          
      '</tr>';
      j=j+1;
    tableBody.append(row);
  }
}
        // to apply select2
        // use event shown.bs.tab is not working
        $('a[href="#linkedaccounts"]').on('click', function() {
           
            setTimeout(() => {
                $('#account_type').select2();
                $('#account').select2();
            }, 200);
        });

        const accounts = @json($accounts);

        $('#account_type').change(function() {
            const type = this.value;
            const acc = $('#account');
            acc.empty();

            let options = accounts;

            if (type != 'all') {
                options = accounts.filter(function (acc) {
                    return acc.account_type_id == type;
                });
            }


            for (let i = 0; i < options.length; i++) {
                const opt = options[i];
                const option = document.createElement('option');
                option.value = opt.id;
                option.innerHTML = opt.name;
                acc.append(option);
            }

        });

        $('#save_account').on('click', function() {
           var accountType = document.getElementById('account_type').options[document.getElementById('account_type').selectedIndex].text;
          
     
    var accountName = $('#account option:selected').text();
    var accountId = $('#account option:selected').val();

            var data = {
            
              account_id: accountId,
              account_type:accountType,
              account_name:accountName
              
            };
                  $.ajax({
                  url: '/airline/save_account',
                  method: 'GET',
                  data: data,
                  success: function(response) {
                    toastr.success(response.msg);
                     console.log('Linked Account sent successfully');
                   loadTableData();
                  },
                  error: function(xhr, textStatus, errorThrown) {
                    // Handle error if any
                    console.error(errorThrown);
                  }
                });
             
           //  }
        });

      
    });
</script>