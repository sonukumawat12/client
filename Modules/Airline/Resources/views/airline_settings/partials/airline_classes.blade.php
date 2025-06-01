@component('components.widget', ['class' => '', 'title' => 'Airline CLasses'])



<div class="row">
    <form action="" id="airline_classes_form">
        <div class="col-md-3">
                <div class="form-group"> 
                    <label for="">Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Name Class" required>
                </div>

        </div>
        <div class="col-md-3" style="padding-top: 22px">
            <button type="submit" class="btn btn-primary" >Save</button>
        </div>
</form>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="airline_classes">
            <thead>
                <tr>
                     <th>No</th>  
                    <th>Name</th>
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

                        url: '/airline/airline_classes_get',

                        method: 'GET',

                        success: function(response) {


                        // console.log('Data loaded successfully');

                        // console.log(response);


                        airlineClasess(response);

                        },

                        error: function(xhr, status, error) {


                        console.error('Error loading data:', error);

                        }

                    });

            }



                function airlineClasess(data) {

                    var tableBody = $('#airline_classes tbody');
            
                    tableBody.empty();

                    var j=1;


                    for (var i = 0; i < data.length; i++) {

                        var row = '<tr>' +

                        '<td>' + j + '</td>' +

                        '<td>' + data[i].name + '</td>' +


                        '</tr>';

                        j=j+1;

                        tableBody.append(row);

                    }

                }


                $('#airline_classes_form').on('submit', function(e) {
                    e.preventDefault(); 

                    var formData = {
                        name: $('#name').val(),
                    };

                    $.ajax({
                        url: '/airline/airline_classes_store',
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            loadTableDataPassenger();
                            $('#airline_classes_form')[0].reset();
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                });



     });

             


</script>