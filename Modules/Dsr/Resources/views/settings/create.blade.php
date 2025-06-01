
<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Dsr\Http\Controllers\DsrSettingsController@store'), 'method' => 'post', 'id' => 'dsr_settings' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center">@lang('dsr::lang.add_dsr_settings')</h4>
        </div>
        <div class="modal-body">
            <div class="container">

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
                        {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2','style' => "width: 100%", 'id' => 'country_id']) !!}
                        <div class="text-danger" id="country_id-error"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('province_id', 'Province / State') !!}
                        <div class="span d-flex">
                            {!! Form::select('province_id', $provinces, null, ['class' => 'form-control select2', 'style' => "width: 100%",'id' => 'province_id']) !!}
                        </div>
                        <div class="text-danger" id="province_id-error"></div>
                    </div>
                    <div class="col-md-12form-group" id="newProvContainer" style="display: none;">
                        <span class="span d-flex">
                                <button type="button" class="btn btn-warning mt-2" id="removeProvincetbtn">-</button>
                                {!! Form::text('name', null, ['class' => 'form-control','id' => "addProvince"]) !!}
                                <button type="button" class="btn btn-success" id="submitprovice">Submit</button>
                            </span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('district_id', 'District') !!}
                        <div class="span d-flex">
                            {!! Form::select('district_id', $districts, null, ['class' => 'form-control select2','style' => "width: 100%", 'id' => 'district_id']) !!}
                        </div>
                        <div class="text-danger" id="district_id-error"></div>
                    </div>
                    <div class="col-md-12form-group" id="newDistrictContainer" style="display: none;">
                       <span class="span d-flex">
                            <button type="button" class="btn btn-warning mt-2" id="removeDistrictbtn">-</button>
                            {!! Form::text('name', null, ['class' => 'form-control','id' => 'newDistrictInput']) !!}
                            <button type="button" class="btn btn-success" id="submitDistrict">Submit</button>
                        </span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('areas', 'Areas') !!}
                        <div class="span d-flex">
                            {!! Form::select('areas[]', $areas , null, ['class' => 'form-control select2', 'id'=>'areas', 'multiple', 'style'=> 'width: 100%;']);!!}
                        </div>
                        <div class="text-danger" id="areas-error"></div>
                    </div>
                    <div class="col-md-12form-group " id="newAreaContainer" style="display: none;">
                        <span class=" span d-flex">
                            <button type="button" class="btn btn-warning mt-2" id="removeAreabtn">-</button>
                            {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'newAreaInput']) !!}
                            <button type="button" class="btn btn-success" id="submitNewAreaBtn">Submit</button>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('business_id', __('dsr::lang.business') . ':') !!}
                        <div class="span d-flex">
                        {!! Form::select('business_id', $businesses, null, ['class' => 'form-control
                        select2',  'id' => 'business_id','style'=> 'width: 100%;']); !!}
                        </div>
                        <div class="text-danger" id="business_id-error"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('dealer_name', 'Dealer Name') !!}
                        {!! Form::text('dealer_name', null, ['class' => 'form-control', 'placeholder' => 'Dealer Name', 'id' => 'dealer_name']) !!}
                        <div class="text-danger" id="dealer_name-error"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('dealer_number', 'Dealer Number ') !!}
                        {!! Form::text('dealer_number', null, ['class' => 'form-control', 'placeholder' => 'Dealer Number', 'id' => 'dealer_number']) !!}
                        <div class="text-danger" id="dealer_number-error"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('dsr_starting_number', 'Dsr starting number') !!}
                        {!! Form::text('dsr_starting_number', null, ['class' => 'form-control', 'placeholder' => 'Dsr starting number', 'id' => 'dsr_starting_number']) !!}
                        <div class="text-danger" id="dsr_starting_number-error"></div>
                    </div>
                </div>

            </div>
            <div class="col-3">
                <button type="button" class="btn btn-primary" id="save_dsr_settings">@lang('messages.save')</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>

<script >
    $(()=>{
        $('.select2').select2();
        $('#province_id').empty();
        $('#district_id').empty();
        $('#areas').empty();
    })
    $(document).ready(function () {
        $('#submitprovice').click(function() {
            var name = $('#addProvince').val();
            $.ajax({
                method: 'POST',
                url: '/dsr/create-province',
                data: { name: name },
                success: function(response) {
                },
            });
        });
        $('#submitDistrict').click(function() {
            var name = $('#newDistrictInput').val();
            $.ajax({
                method: 'POST',
                url: '/dsr/create-district',
                data: { name: name },
                success: function(response) {
                },
            });
        });
        $('#submitNewAreaBtn').click(function() {
            var name = $('#newAreaInput').val();
            $.ajax({
                method: 'POST',
                url: '/dsr/create-area',
                data: { name: name },
                success: function(response) {
                },
            });
        });

        $('#country_id').on('change', function () {
            var country_id = $(this).val();
            $.ajax({
                url: '/dsr/get-provinces/' + country_id,
                type: 'GET',
                success: function (data) {
                    $('#province_id').html(data);
                }
            });
        });
        $('#province_id').on('change', function () {
            var province_id = $(this).val();
            $.ajax({
                url: '/dsr/get-districts/' + province_id,
                type: 'GET',
                success: function (data) {
                    $('#district_id').html(data);
                }
            });
        });
        $('#district_id').on('change', function () {
            var district_id = $(this).val();
            $.ajax({
                url: '/dsr/get-areas/' + district_id,
                type: 'GET',
                success: function (data) {
                    $('#areas').html(data);
                }
            });
        });



        $('.select2').select2();

        $("#save_dsr_settings").on("click", function (event) {
            event.preventDefault();

            let inputName = ['fuel_provider_id','dealer_number','dealer_name', 'business_id', 'country_id', 'province_id', 'district_id', 'areas', 'dsr_name', 'dsr_number', 'dsr_starting_number'];
            let isValid = true;
            if ($("#province_id").val() == null || $("#province_id").val() == "") {
                $("#province_id-error").text("Province is required");
                isValid = false;
                console.log($("#province_id").val() == null || $("#province_id").val() == "")

            }
            if ($("#district_id").val() == null  || $("#district_id").val() == "")  {
                $("#district_id-error").text("District is required");
                isValid = false;
            }
            if ($("#areas").val() == null || $("#areas").val() == "") {
                $("#areas-error").text("Area is required");
                isValid = false;
                console.log($("#district_id").val() == null  || $("#district_id").val() == "")

            }
            if($("#business_id").val() == null || $("#business_id").val() == "") {
                $("#business_id-error").text("Business is required");
                isValid = false;
                console.log($("#areas").val() == null || $("#areas").val() == "")


            }
            if($("#fuel_provider_id").val() == null || $("#fuel_provider_id").val() == "") {
                $("#fuel_provider_id-error").text("Fuel Provider is required");
                isValid = false;
                console.log($("#business_id").val() == null || $("#business_id").val() == "")

            }
            for (let i = 0; i < inputName.length; i++) {
                let input = $("#" + inputName[i]);
                let errorDiv = $("#" + inputName[i] + "-error");
                errorDiv.text("");

                if (input.val() === "") {
                    isValid = false;
                    errorDiv.text(capitalizeFirstLetter(inputName[i]) + " is required");
                }
            }

            if (isValid) {
                $("#dsr_settings").submit();
            } else {
                console.log("Form validation failed. Please check the errors.");
            }
        });

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    });
</script>
