@component('components.filters', ['title' => __('airline::lang.filters')])
  <div class="row">
   <div class="col-md-3">
    <div class="form-group">
        {!! Form::label('airports_filter_date_range', __('airline::lang.date_range') . ':') !!}
        {!! Form::text('airports_filter_date_range', null, [
            'placeholder' => __('lang_v1.select_a_date_range'),
            'class' => 'form-control',
            'readonly',
        ]) !!}
    </div>
</div>
    <div class="form-group col-sm-3">
        {!! Form::label('province_select', __('Location') . ':*') !!}
         {!! Form::select('location', $business_locations, null, [
                                'class' => 'form-control select2',
                                'id' => 'location',
                                'required',
                                'placeholder' => __('Location'),
                            ]) !!}
    </div>
       <div class="form-group col-sm-3">
        {!! Form::label('province_select', __('User ') . ':*') !!}
       {!! Form::select('user', $username, null, [
                                'class' => 'form-control select2',
                                'id' => 'user',
                                'required',
                                'placeholder' => __('User'),
                            ]) !!}
    </div>
<div class="form-group col-sm-3">
        {!! Form::label('province_select', __('Designations') . ':*') !!}
       {!! Form::select('designations', $designations, null, [
                                'class' => 'form-control select2',
                                'id' => 'designations',
                                'required',
                                'placeholder' => __('Designations'),
                            ]) !!}
    </div>
</div>
 <div class="row">
   
    
   <div class="form-group col-sm-3">
    {!! Form::label('province_select', __('Signature Levels') . ':*') !!}
      {!! Form::select('signature_level', $signatureLevel, null, [
                                'class' => 'form-control select2',
                                'id' => 'signature_level',
                                'required',
                                'placeholder' => __('Signature Level'),
                            ]) !!}
</div>
  
</div>
@endcomponent

@component('components.widget', ['class' => '', 'title' => 'Upload Signature'])
    <div class="row" style="margin-bottom: 4px;">
        <button type="button" class="btn btn-primary btn-modal pull-right" id="create_airport">
            <i class="fa fa-plus"></i>
            Add
        </button>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="document_upload_tables" style="width:100%!important">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="10%">Date</th>
                        <th width="20%">Location</th>
                        <th width="20%">user</th>
                        <th width="20%">Designations</th>
                        <th width="10%">Signature Upload</th>
                        <th width="10%">Signature Level</th>
                   
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
<div class="modal fade" id="airport_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document"  >
        {!! Form::open(['url' => action('\Modules\DocManagement\Http\Controllers\DocManagementSettingsController@store_signatures'), 'method' => 'post', 'id' => 'sinature','enctype'=>"multipart/form-data",'files' => true ]) !!}

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Upload Signature</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 ">
                        <div class="form-group">
                              {!! Form::label('date_added', __('airline::lang.date_added') . ':*') !!}
                            <div class="input-group" style="width:100%;">
                          
                            {!! Form::text('date', @format_date(date('Y-m-d')), [
                                'class' => 'form-control',
                                 'id' => 'date',
                                'required',
                                'placeholder' => __('airline::lang.date_added'),
                            ]) !!}
                        </div>
                            </div>
                            </div>
                            <div class="col-md-6 ">
                        <div class="form-group">
                             {!! Form::label('country_select', __('Location') . ':*') !!}
                             <div class="input-group" style="width:100%;">
                           
                            {!! Form::select('location', $business_locations, null, [
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
                          <div class="col-md-6 ">
                       <div class="form-group">
                            {!! Form::label('country_select', __('Designations ') . ':*') !!}
                             <div class="input-group" style="width:100%;">
                           
                            {!! Form::select('designations', $designations, null, [
                                'class' => 'form-control select2',
                                'id' => 'designations',
                                'required',
                                'placeholder' => __('Designations'),
                            ]) !!}
                        </div>
                         </div>
                         </div>
                          </div>
                          
                          <div class="row">
                        <div class="col-md-6"> 
                        <div class="form-group" style="width:100%;"> 
                        {!! Form::label('image',__('Upload Signature')) !!} 
                        {!! Form::file('image', ['id' => 'image','accept' => 'image/*']); !!}
                        </div>
                        </div> 
                           <div class="form-group col-sm-6">
                               {!! Form::label('country_select', __('Signature Level ') . ':*') !!}
                                <div class="input-group" style="width:100%;">
                            
                            {!! Form::select('signature_level', $signatureLevel, null, [
                                'class' => 'form-control select2',
                                'id' => 'signature_level',
                                'required',
                                'placeholder' => __('Signature Level'),
                            ]) !!}
                            
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
  $(document).ready(() => {
      $(document).ready(function() {
 $('#sinature').submit(function(event) {
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
          location.reload();
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
      url: '/DocManagement/document_upload_gets',
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
  var tableBody = $('#document_upload_tables tbody');
  console.log("type table");
  // Clear the table body
  tableBody.empty();
  var j=1;
  // Iterate over the received data and append rows to the table
  for (var i = 0; i < data.length; i++) {
      
    var row = '<tr>' +
        '<td>' + j + '</td>' +
        '<td>' + data[i].date + '</td>' +
        '<td>' + data[i].location + '</td>' +
        '<td>' + data[i].user + '</td>' +
        '<td>' + data[i].designations + '</td>' +
        '<td>' + data[i].upload_signature + '</td>' +
        '<td>' + data[i].signature_levels + '</td>' +
        
     
      '</tr>';
      j=j+1;
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
    const airport_module = {
        edit_id: null,
        delete_id: null,
        airport_table: null,
        init: function() {
            $('#airports_filter_country_select').on('change', function() {
                airport_module.applyFilters($(this).val());
            });
            $('#date_added').datepicker({
                dateFormat: "Y-m-d" // Set your desired date format here
            });

            $('#airports_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#airports_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end
                        .format(moment_date_format));
                    airport_module.airport_table.ajax.reload();
                }
            );
            $('#airports_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#airports_filter_date_range').val('');
                airport_module.airport_table.ajax.reload();
            });

            this.airport_table = $('#airport_table').DataTable({
                paging: true,
                searching: true,
                ordering: false,
                info: false,
                lengthMenu: [
                    [5, 10, 25, -1],
                    [5, 10, 25, "All"]
                ],
                pageLength: 10,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ action('\Modules\Airline\Http\Controllers\AirlineSettingController@get_airport_table') }}",
                    data: function(d) {
                        if ($('#airports_filter_date_range').val()) {
                            var start = $('#airports_filter_date_range').data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            var end = $('#airports_filter_date_range').data('daterangepicker')
                                .endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }

                        d.country = $('#airports_filter_country_select').val();
                        // No need to send province filter here

                        // Log the parameters for debugging
                        // console.log("params", d);
                    }
                },
                columns: [{
                        data: 'date_added',
                        name: 'date_added'
                    },
                    {
                        data: 'country',
                        name: 'country'
                    },
                    {
                        data: 'province',
                        name: 'province'
                    },
                    {
                        data: 'airport_name',
                        name: 'airport_name'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'airport_status',
                        name: 'airport_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                createdRow: function(row, data, dataIndex) {
                    // Customize row creation
                    if (data['status']) {
                        row.style = "cursor:pointer";
                        $(row).addClass('active');
                    }
                }
            });

            // Add an event listener for the Province/State filter
            $('#airports_filter_province_select').on('keyup', function() {
                airport_module.airport_table
                    .column('province:name')
                    .search(this.value) // Apply search on the 'province' column
                    .draw();
            });


            this.populate_countries();

            this.listener();

        },
        applyFilters: function(d) {
            var selectedCountry = $('#airports_filter_country_select').val();
            var airportName = $('#airports_filter_airport_name').val();

            $.ajax({
                url: '{{ route('airports') }}',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    country: selectedCountry
                },
                success: function(data, b, c) {
                    populateAirportDropdown(data);
                    populateProvinceDropdown(data);

                    // Initialize or update Select2 for the province/state dropdown
                    $('#airports_filter_province_select').select2({
                        minimumResultsForSearch: 0,
                        width: '100%',
                        placeholder: 'Enter Province/State Name',
                        allowClear: true
                    }).on('change', function() {
                        // Get the selected province name
                        var selectedProvince = $(this).val();
                    });

                    // Initialize or update Select2 for the airport names dropdown
                    $('#airports_filter_airport_name').select2({
                        minimumResultsForSearch: 0,
                        width: '100%',
                        placeholder: 'Enter Airport Name',
                        allowClear: true
                    }).on('change', function() {
                        // Get the selected airport name
                        var selectedAirport = $(this).val();

                        // Apply the filter to the DataTable
                        airport_module.airport_table
                            .column('airport_name:name')
                            .search(selectedAirport)
                            .draw();
                    });

                },
                error: function(data, b, c) {
                    toastr.success(data.message);
                }
            });

            $('#airports_filter_province_select').on('change', function() {
                var selectedProvince = $(this).val();
                console.log(selectedProvince);

                $.ajax({
                    url: '{{ route('airports') }}',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        country: selectedCountry,
                        province: selectedProvince
                    },
                    success: function(data, b, c) {
                        populateAirportDropdown(data);
                    }
                });
            });

            function populateAirportDropdown(data) {
                $('#airports_filter_airport_name').empty();
                var airportNames = Object.values(data);
                airportNames.forEach(function(airport) {
                    var option = new Option(airport.airport_name, airport.airport_name);
                    $('#airports_filter_airport_name').append(option);
                });
            }

            function populateProvinceDropdown(data) {
                $('#airports_filter_province_select').empty();
                var provinceNames = Object.values(data);
                var provinceArray = [];
                provinceNames.forEach(function(airport) {
                    provinceArray.push(airport.province);
                });
                provinceArray = Array.from(new Set(provinceArray));
                provinceArray.forEach(function(province) {
                    var option = new Option(province, province);
                    $('#airports_filter_province_select').append(option);
                });
                $('#airports_filter_province_select').val('');
            }

            // Update the DataTable filter based on the selected country and airport name
            airport_module.airport_table
                .column('country:name')
                .search(selectedCountry)
                .draw();

            airport_module.airport_table
                .column('airport_name:name')
                .search(airportName)
                .draw();
        },

        populate_countries: function() {
            fetch("https://restcountries.com/v2/all")
                .then(response => response.json())
                .then(data => {
                    var countrySelect = document.getElementById("country_select");
                    var filtercountrySelect = document.getElementById("airports_filter_country_select");

                    data.forEach(country => {
                        var option = new Option(country.name, country.name);
                        filtercountrySelect.add(option);
                    });

                    data.forEach(country => {
                        var option = new Option(country.name, country.name);
                        countrySelect.add(option);
                    });

                    // Initialize Select2 on the country select element
                    $(countrySelect).select2({
                        width: '100%'
                    });

                    $(filtercountrySelect).select2({
                        width: '100%'
                    });
                    if (data.length > 0) {
                        $(countrySelect).val(data[0].name).trigger('change');
                    }
                })
                .catch(error => console.log(error));
        },

        populate_provinces: function() {
            // var countrySelect = document.getElementById("country_select");
            // var provinceSelect = document.getElementById("province_select");

            // // Clear previous options
            // provinceSelect.innerHTML = "";

            // // Fetch provinces based on the selected country
            // var selectedCountry = countrySelect.value;
            // fetch(`https://restcountries.com/v2/name/${selectedCountry}`)
            //     .then(response => {
            //         console.log(response.data);
            //         var provinces = response.data[0].provinces;
            //         if (provinces) {
            //             Object.keys(provinces).forEach(key => {
            //                 var option = new Option(provinces[key].name, key);
            //                 provinceSelect.add(option);
            //             });
            //         }

            //         $(provinceSelect).select2({
            //             width: '100%'
            //         });
            //     })
            //     .catch(error => console.log(error));
        },

        
        update_status: (patchData) => {
            $.ajax({
                url: '/airline/update_status_airport',
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: patchData,
                success: function(data, b, c) {
                    if (data.statusText == 'Success') {
                        toastr.success(data.message);
                        $('#airport_status_modal').modal('hide');
                        airport_module.airport_table.ajax.reload();
                    }
                },
                error: function(data, b, c) {
                    toastr.success(data.message);
                }
            });
        },
        delete: (deleteData) => {
            $.ajax({
                url: '/airline/delete_airport',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: deleteData,
                success: function(data, b, c) {
                    if (data.statusText == 'Success') {
                        toastr.success(data.message);
                        $('#airport_delete_modal').modal('hide');
                        airport_module.airport_table.ajax.reload();
                    }
                },
                error: function(data, b, c) {
                    toastr.success(data.message);
                }
            });
        },
        show_update_status_modal: (status, data) => {
            const status_name = status ? 'Enable' : 'Disable';
            const status_current = 1 - status ? 'Enable' : 'Disable';
            $('#update_status_airport_name').html(data.airport_name + ': ' + status_current);
            $('#airport_status_value').html(status_name);
            $('#update_status_airport').html(status_name);
            $('#update_status_airport').data({
                id: data.id,
                status: status
            });
            if (status) {
                $('#update_status_airport').removeClass('btn-danger').addClass('btn-primary');
            } else {
                $('#update_status_airport').removeClass('btn-primary').addClass('btn-danger');
            }
            $('#airport_status_modal').modal('show');
        },
        resetError: () => {
            $('#signature .form-group').removeClass('hass-error');
            $('#signature .invalid-feedback').hide();
        },
        listener: function() {
            $('#create_airport').click(function() {
                airport_module.edit_id = null;
                airport_module.resetError();
                $('#airport_title').html('Create Airport');
                $('#date_added').datepicker('setDate', new Date());
                // $('#country_select').val('');
                $('#province_select').val('');
                $('#airport_name').val('');
                $('#airport_form_modal').modal('show');
            });

         /*   $('#airport_form').submit((e) => {
                e.preventDefault();

                const data = {};
                const inputs = $('#airport_form .form-control');
                for (let i = 0; i < inputs.length; i++) {
                    const value = $(inputs[i]).val();
                    console.log(value);
                    // const required = inputs[i].required;
                    // if(required && value) {
                    //     $(input[i]).css('error-text').show();
                    // }
                    if (value) {
                        data[inputs[i].name] = value;
                    }
                }
                this.submit(data);
            }); */

            $('#airport_table').on("click", ".enable", function(e) {
                let data = $(e.target).data();
                airport_module.show_update_status_modal(1, data);
            });

            $('#airport_table').on("click", ".disable", function(e) {
                let data = $(e.target).data();
                airport_module.show_update_status_modal(0, data);
            });

          

           

            $('#delete_airport').click((e) => {
                if (airport_module.delete_id) {
                    this.delete({
                        id: airport_module.delete_id
                    });
                }
            })
        }

    }
    $(document).ready(() => {
        airport_module.init();
    });
</script>

<style>
    .select2-container .select2-selection {
        height: 34px;
        border: 1px solid #ccc;
    }
</style>
