

<style>
    .select2-container {
        display: inherit !Important;
    }
</style>
<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Dsr\Http\Controllers\DesignatedDsrController@store'), 'method' => 'post', 'id' => 'designated_officer' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center">@lang('dsr::lang.add_designated_officer')</h4>
        </div>
        <div class="modal-body">
            <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __('dsr::lang.name')) !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.name'), 'id' => 'name']) !!}
                        <div class="text-danger" id="name-error"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('fuel_provider_id', 'Fuel Provider') !!}
                        {!! Form::select('fuel_provider_id', $fuelProviders, null, ['class' => 'form-control select2','style' => "width: 100%", 'id' => 'fuel_provider_id']) !!}
                        <div class="text-danger" id="fuel_provider_id-error"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('country_id', 'Country') !!}
                        {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2', 'style' => "width: 100%",'id' => 'country_id']) !!}
                        <div class="input-group-append" id="addCountryButtonGroup" style="display:none;">
                            <button class="btn btn-outline-secondary" type="button" id="addCountryButton">+</button>
                        </div>
                        <div class="text-danger" id="country_id-error"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('province_id', 'Province / State') !!}
                        {!! Form::select('province_id[]', $provinces, null, ['class' => 'form-control select2', 'multiple','style' => "width: 100%",'id' => 'province_id']) !!}
                        <div class="input-group" id="addProvinceButtonGroup" style="display: none;">
                            <button class="btn btn-outline-secondary" type="button" id="addProvinceButton">+</button>
                        </div>
                        <div class="text-danger" id="province_id-error"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('district_id', 'District') !!}
                        {!! Form::select('district_id[]', $districts, null, ['class' => 'form-control select2','multiple','style' => "width: 100%", 'id' => 'district_id'], )!!}
                        <div class="text-danger" id="district_id-error"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('areas', 'Areas') !!}
                        {!! Form::select('areas[]', [] , null, ['class' => 'form-control select2', 'multiple', 'style'=> 'width: 100%;','id' => 'areas_dropdown']);!!}
                        <div class="text-danger" id="areas-error"></div>
                    </div>
                </div>
             </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('officer_name', 'Designated Officer Name') !!}
                        {!! Form::text('officer_name', null, ['class' => 'form-control', 'placeholder' => 'Designated Officer Name', 'id' => 'officer_name']) !!}
                        <div class="text-danger" id="officer_name-error"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('officer_mobile', 'Designated Officer Mobile') !!}
                        {!! Form::text('officer_mobile', null, ['class' => 'form-control', 'placeholder' => 'Designated Officer Mobile', 'id' => 'officer_mobile']) !!}
                        <div class="text-danger" id="officer_mobile-error"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('officer_username', 'Designated Officer User Name') !!}
                        {!! Form::text('officer_username', null, ['class' => 'form-control', 'placeholder' => 'Designated Officer User Name', 'id' => 'officer_username']) !!}
                        <div class="text-danger" id="officer_username-error"></div>
                    </div>
                </div>
             </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('officer_password', 'Designated Officer Password') !!}
                        {!! Form::password('officer_password', ['class' => 'form-control', 'placeholder' => 'Designated Officer Password', 'id' => 'officer_password']) !!}
                        <div class="text-danger" id="officer_password-error"></div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <button type="button" class="btn btn-primary" id="save_designated_officer">@lang('messages.save')</button>
            </div>
            </div>
        </div>
        </div>
        {!! Form::close() !!}
    </div>


<script >
    $(()=>{
        $('.select2').select2();
    })
    $(document).ready(function () {
        $('.select2').select2();
        $('#province_id').empty();
        $('#district_id').empty();
        $('#areas_dropdown').empty();
        $("#save_designated_officer").on("click", function (event) {
            event.preventDefault()

            let inputName = ['name', 'officer_password', 'officer_username', 'officer_mobile', 'officer_name', 'district_id', 'province_id', 'country_id', 'fuel_provider_id'];

            let errors = [];

            for (let i = 0; i < inputName.length; i++) {
                let input = $("#" + inputName[i]);
                let errorDiv = $("#" + inputName[i] + "-error");

                // Clear previous error messages
                errorDiv.text("");

                // Validate input
                if (input.val() === "") {
                    errors.push(capitalizeFirstLetter(inputName[i]) + " is required");
                    errorDiv.text(capitalizeFirstLetter(inputName[i]) + " is required");
                }
            }
            if (errors.length > 0) {
                // Additional handling for displaying errors if needed
                console.log("Form validation failed. Please check the errors.");
                return false; // Prevent form submission
            }
            $("#designated_officer").submit();


        });

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    });
    $('#country_id').on('change', function () {
        var country_id = $(this).val();
        $('#province_id').empty();
        $('#district_id').empty();
        $.ajax({
            url: '/dsr/get-provinces-multiple',
            type: 'GET',
            data: {'country_id': country_id},
            success: function (data) {
                $('#province_id').html(data);
            }
        });
    });
   $('#province_id').on('change', function () {
        $('#district_id').empty();
        var province_id = $(this).val();
        $.ajax({
            url: '/dsr/get-districts-multiple',
            type: 'GET',
            data: {'province_id' : province_id},
            success: function (data) {
                $('#district_id').html(data);
            }
        });
    });
    $('#district_id').on('change', function () {
        $('#areas_dropdown').empty();
        console.log($('#areas_dropdown'));
        var district_id = $(this).val();
        $.ajax({
            url: '/dsr/get-areas-multiple',
            type: 'GET',
            data: {'district_id' : district_id},
            success: function (data) {
                $('#areas_dropdown').append(data);
            }
        });
    });
</script>
