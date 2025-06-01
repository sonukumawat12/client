@component('components.widget', ['class' => '', 'title' => 'Passenger Types'])







<div class="row">

    <form action="" id="passenger_type_form">

        <div class="col-md-3">


                <div class="form-group">

                    <label for="">Passenger type</label>

                    <input type="text" name="type_name" id="type_name" class="form-control" placeholder="Type Name" required>

                </div>
               

            

        </div>
        <div class="col-md-3">


        <div class="form-group">

            <label for="">Description</label>

            <input type="text" name="description" id="description" class="form-control" placeholder="Description" required>


        </div>
        </div>
    

        <div class="col-md-3" style="padding-top: 22px">

            <button type="submit" class="btn btn-primary" >Save</button>

        </div>

</form>

</div>


<div class="row">

    <div class="table-responsive">

        <table class="table table-bordered table-striped" id="passenger_type">

            <thead>

                <tr>

                     <th>No</th>  

                    <th>Name</th>

                    <th>Description</th>


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

        loadTableDataPassenger();

            function loadTableDataPassenger() {

                    $.ajax({

                        url: '/airline/passenger_type_get',

                        method: 'GET',

                        success: function(response) {


                        console.log('Data loaded successfully');

                        console.log(response);


                        updatePassengerType(response);

                        },

                        error: function(xhr, status, error) {


                        console.error('Error loading data:', error);

                        }

                    });

            }



                function updatePassengerType(data) {

                    var tableBody = $('#passenger_type tbody');
            
                    tableBody.empty();

                    var j=1;


                    for (var i = 0; i < data.length; i++) {

                        var row = '<tr>' +

                        '<td>' + j + '</td>' +

                        '<td>' + data[i].type_name + '</td>' +

                        '<td>' + data[i].description + '</td>' +


                        '</tr>';

                        j=j+1;

                        tableBody.append(row);

                    }

                }


                $('#passenger_type_form').on('submit', function(e) {
                    e.preventDefault(); 

                    var formData = {
                        type_name: $('#type_name').val(),
                        description: $('#description').val()
                    };

                    $.ajax({
                        url: '/airline/passenger_type_store',
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            loadTableDataPassenger();
                            $('#passenger_type_form')[0].reset();
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                });



     });

             


</script>