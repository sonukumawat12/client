<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        {!! Form::model($dsr_officer, ['url' => action('\Modules\Dsr\Http\Controllers\DesignatedDsrController@update', $dsr_officer->id), 'method' => 'put', 'id' => 'designated_officer' ]) !!}
        <div class="modal-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('name', __('dsr::lang.name')) !!}
                            {!! Form::text('name', $dsr_officer->name, ['class' => 'form-control', 'placeholder' => __('dsr::lang.name'), 'id' => 'name']) !!}
                            <div class="text-danger" id="name-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('fuel_provider_id', 'Fuel Provider') !!}
                            {!! Form::select('fuel_provider_id', $fuelProviders, $dsr_officer->fuel_provider_id, ['class' => 'form-control', 'id' => 'fuel_provider_id']) !!}
                            <div class="text-danger" id="fuel_provider_id-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('country_id', 'Country') !!}
                            {!! Form::select('country_id', $countries, $dsr_officer->country_id, ['class' => 'form-control', 'id' => 'country_id']) !!}
                            <div class="text-danger" id="country_id-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('province_id', 'Province / State') !!}
                            {!! Form::select('province_id', $provinces, $dsr_officer->province_id, ['class' => 'form-control', 'id' => 'province_id']) !!}
                            <div class="text-danger" id="province_id-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('district_id', 'District') !!}
                            {!! Form::select('district_id', $districts, $dsr_officer->district_id, ['class' => 'form-control', 'id' => 'district_id']) !!}
                            <div class="text-danger" id="district_id-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('areas', 'Areas') !!}
                            {!! Form::select('areas[]', $allAreas , $dsr_officer->areas, ['class' => 'form-control select2', 'multiple', 'style'=> 'width: 100%;']);!!}
                            <div class="text-danger" id="areas-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('officer_name', 'Designated Officer Name') !!}
                            {!! Form::text('officer_name', $dsr_officer->officer_name, ['class' => 'form-control', 'placeholder' => 'Designated Officer Name', 'id' => 'officer_name']) !!}
                            <div class="text-danger" id="officer_name-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('officer_mobile', 'Designated Officer Mobile') !!}
                            {!! Form::text('officer_mobile', $dsr_officer->officer_mobile, ['class' => 'form-control', 'placeholder' => 'Designated Officer Mobile', 'id' => 'officer_mobile']) !!}
                            <div class="text-danger" id="officer_mobile-error"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('officer_username', 'Designated Officer User Name') !!}
                            {!! Form::text('officer_username', $dsr_officer->officer_username, ['class' => 'form-control', 'placeholder' => 'Designated Officer User Name', 'id' => 'officer_username']) !!}
                            <div class="text-danger" id="officer_username-error"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="update_designated_officer">@lang('messages.update')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#update_designated_officer").on("click", function (event) {
            event.preventDefault()

            let inputName = ['name', 'fuel_provider_id', 'country_id', 'province_id', 'district_id', 'areas', 'officer_name', 'officer_mobile', 'officer_username', 'officer_password'];

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
        $('.select2').select2();

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    });
</script>
