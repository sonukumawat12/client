@component('components.filters', ['title' => __('airline::lang.filters')])
    @include('airline::airline_settings.partials.airports_filters')
@endcomponent

@component('components.widget', ['class' => '', 'title' => 'Airports'])
    <div class="row" style="margin-bottom: 4px;">
        
        <button type="button" class="btn  btn-primary btn-modal pull-right"
        data-href="{{action('\Modules\Airline\Http\Controllers\AirlineSettingController@create_edit_airport')}}"
        data-container="#airport_form_modal">
            <i class="fa fa-plus"></i>
            Add
        </button>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="airport_table" style="width:100%!important">
                <thead>
                    <tr>
                        <th width="10%">Date Added</th>
                        <th width="20%">Country</th>
                        <th width="20%">Province</th>
                        <th width="20%">Airport</th>
                        <th width="10%">User</th>
                        <th width="10%">Status</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
<div class="modal fade" id="airport_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    
</div>


<div class="modal fade" id="airport_status_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    Are you sure to update
                    <strong id="update_status_airport_name"></strong>
                    to
                    <strong id="airport_status_value"></strong>
                </h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="update_status_airport"></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="airport_delete_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Are you sure to remove <strong id="airport_delete_name"></strong></h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="delete_airport">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>

    
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
            fetch("{{url('/airline/get-countries')}}")
                .then(response => response.json())
                .then(data => {
                    var countrySelect = document.getElementById("country_select");
                    var filtercountrySelect = document.getElementById("airports_filter_country_select");

                    data.forEach(country => {
                        var option = new Option(country.country, country.country);
                        filtercountrySelect.add(option);
                    });

                    data.forEach(country => {
                        var option = new Option(country.country, country.country);
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

        submit: (data) => {
            if (!airport_module.edit_id) {
                $.ajax({
                    url: '/airline/add_airport',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: function(data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#airport_form_modal').modal('hide');
                            airport_module.airport_table.ajax.reload();
                        }
                    },
                    error: function(data, b, c) {
                        toastr.success(data.message);
                    }
                });
            } else {
                data['id'] = airport_module.edit_id;
                $.ajax({
                    url: '/airline/edit_airport',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: function(data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#airport_form_modal').modal('hide');
                            airport_module.airport_table.ajax.reload();
                        }
                    },
                    error: function(data, b, c) {
                        toastr.success(data.message);
                    }
                });
            }
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
            $('#airport_form .form-group').removeClass('hass-error');
            $('#airport_form .invalid-feedback').hide();
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

            $('#airport_form').submit((e) => {
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
            });

            $('#airport_table').on("click", ".enable", function(e) {
                let data = $(e.target).data();
                airport_module.show_update_status_modal(1, data);
            });

            $('#airport_table').on("click", ".disable", function(e) {
                let data = $(e.target).data();
                airport_module.show_update_status_modal(0, data);
            });

            $('#airport_table').on('dblclick', 'tbody tr.active', function() {
                var data = $(this).closest('table').DataTable().row(this).data();
                airport_module.edit_id = data.id;
                airport_module.resetError();
                $('#airport_title').html('Edit Airport');
                $('#date_added').datepicker('setDate', new Date(data.date_added));
                $('#country_select').val(data.country).trigger('change');
                $('#province_select').val(data.province);
                $('#airport_name').val(data.airport_name);
                $('#airport_form_modal').modal('show');
            });

            $('#airport_table').on("click", ".delete", function(e) {
                let data = $(e.target).data();
                airport_module.delete_id = data.id;
                $('#airport_delete_name').html(data.airport_name);

                $('#airport_delete_modal').modal('show');
            });

            $('#update_status_airport').click((e) => {
                const data = $(e.target).data();
                this.update_status(data);
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

    $(document).on('submit','#airport_form',function(){
        event.preventDefault();
        var data = $(this).serialize();
    
                $.ajax({
                    url: '/airline/add_airport',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: function(data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#airport_form_modal').modal('hide');
                            airport_module.airport_table.ajax.reload();
                        }
                    },
                    error: function(data, b, c) {
                        toastr.success(data.message);
                    }
                });
    })            
    $(document).on('submit','#edit_airport_form',function(){
    event.preventDefault();
    var data = $(this).serialize();
                $.ajax({
                    url: '/airline/edit_airport',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: function(data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#airport_form_modal').modal('hide');
                            airport_module.airport_table.ajax.reload();
                        }
                    },
                    error: function(data, b, c) {
                        toastr.success(data.message);
                    }
                });
    });
</script>

<style>
    .select2-container .select2-selection {
        height: 34px;
        border: 1px solid #ccc;
    }
</style>
