<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        {!! Form::model($dsr_settings, ['url' => action('\Modules\Dsr\Http\Controllers\DsrSettingsController@update', $dsr_settings->id), 'method' => 'put', 'id' => 'dsr_settings' ]) !!}
        <div class="modal-body">
            <div class="container">
                <div class="row">


                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('fuel_provider_id', 'Fuel Provider') !!}
                            {!! Form::select('fuel_provider_id', $fuelProviders, $dsr_settings->fuel_provider_id, ['class' => 'form-control', 'id' => 'fuel_provider_id']) !!}
                            <div class="text-danger" id="fuel_provider_id-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('country_id', 'Country') !!}
                            {!! Form::select('country_id', $countries, $dsr_settings->country_id, ['class' => 'form-control select2', 'id' => 'country_id']) !!}
                            <div class="text-danger" id="country_id-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('province_id', 'Province / State') !!}
                            {!! Form::select('province_id', $provinces, $dsr_settings->province_id, ['class' => 'form-control select2', 'id' => 'province_id']) !!}
                            <div class="text-danger" id="province_id-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('district_id', 'District') !!}
                            {!! Form::select('district_id', $districts, $dsr_settings->district_id, ['class' => 'form-control select2', 'id' => 'district_id']) !!}
                            <div class="text-danger" id="district_id-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('areas', 'Areas') !!}
                            {!! Form::select('areas[]', $allAreas , $dsr_settings->areas, ['class' => 'form-control select2', 'multiple', 'style'=> 'width: 100%;']);!!}
                            <div class="text-danger" id="areas-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('dealer_name', 'Dealer Name') !!}
                            {!! Form::text('dealer_name',$dsr_settings->dealer_name, ['class' => 'form-control', 'placeholder' => 'Dealer Name', 'id' => 'dealer_number']) !!}
                            <div class="text-danger" id="dealer_name-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('dealer_number', 'Dealer Number ') !!}
                            {!! Form::text('dealer_number', $dsr_settings->dealer_number, ['class' => 'form-control', 'placeholder' => 'Dealer Number', 'id' => 'dealer_number']) !!}
                            <div class="text-danger" id="dealer_number-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('dsr_starting_number', 'Dsr starting number') !!}
                            {!! Form::text('dsr_starting_number', $dsr_settings->dsr_starting_number, ['class' => 'form-control', 'placeholder' => 'Dsr starting number', 'id' => 'dsr_starting_number']) !!}
                            <div class="text-danger" id="dsr_starting_number-error"></div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="update_dsr_settings">@lang('messages.update')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#update_dsr_settings").on("click", function (event) {
            event.preventDefault()

            let inputName = [ 'fuel_provider_id', 'country_id', 'province_id', 'district_id', 'areas', 'dsr_name', 'dsr_number', 'dsr_starting_number'];

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

            $("#dsr_settings").submit();
        });
        $('.select2').select2();

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    });
</script>
