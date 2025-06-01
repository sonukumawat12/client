@component('components.widget', ['class' => '', 'title' => 'Commission Types'])
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="commision_type">Commision type</label>
                <input type="text" name="commision_type" id="commision_type" class="form-control"
                    placeholder="Commision Type">
            </div>
        </div>

        <div class="col-md-3" style="padding-top: 22px">
            <button type="button" class="btn btn-primary" id="save_commision">Save</button>
        </div>
    </div>

    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="transactions">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Commision Type</th>
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
                url: '/airline/airport_commision_type_get',
                method: 'GET',
                success: function(response) {
                    // Handle the success response from the server
                    console.log('Data loaded successfully');

                    // Update the table with the data
                    updateCommisionTable(response);
                },
                error: function(xhr, status, error) {
                    // Handle the error response from the server
                    console.error('Error loading data:', error);
                }
            });
        }

        function updateCommisionTable(data) {
            var tableBody = $('#transactions tbody');
            tableBody.empty();
            var j = 1;

            for (var i = 0; i < data.length; i++) {
                // extract only the date part
                var dateOnly = data[i].created_at.split('T')[0];

                var row = '<tr>' +
                    '<td>' + (j++) + '</td>' +
                    '<td>' + data[i].commsion_type + '</td>' +
                    '<td>' + data[i].user + '</td>' +
                    '<td>' + dateOnly + '</td>' +
                    '</tr>';

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
                options = accounts.filter(function(acc) {
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

        $('#save_commision').on('click', function() {
            var commissionType = $('#commision_type').val();
            console.log(commissionType);
            $.ajax({
                url: '/airline/airport_commision_type',
                method: 'get',
                data: {
                    commissionType: commissionType
                },
                success: function(response) {
                    // Handle the success response from the server
                    loadTableData();
                    $('#commision_type').val('');
                    // console.log('Commission type data sent successfully');

                    toastr.success('Commission type data sent successfully');
                },
                error: function(xhr, status, error) {
                    // Handle the error response from the server
                    console.error('Error sending commission type data:', error);
                }
            });
        });



        function updateTable(data) {
            console.log(data);
            var tableBody = $('#transactions tbody ');

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
