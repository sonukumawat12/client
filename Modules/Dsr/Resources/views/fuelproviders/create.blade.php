<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Dsr\Http\Controllers\FuelProvidersController@store'), 'method' => 'post', 'id' => 'fuel_provider_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('dsr::lang.add_fuel_provider')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __('dsr::lang.name')) !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.name'), 'id' => 'name']) !!}
                <div class="text-danger" id="name-error"></div>
            </div>
            <div class="form-group">
                {!! Form::label('email', __('dsr::lang.email')) !!}
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.email'), 'id' => 'email']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('phone', __('dsr::lang.phone')) !!}
                {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.phone'), 'id' => 'phone']) !!}
                <div class="text-danger" id="phone-error"></div>
            </div>
            <div class="form-group">
                {!! Form::label('address', __('dsr::lang.address')) !!}
                {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.address'), 'id' => 'address']) !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="save_fuel_provider_btn">@lang('messages.save')</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(document).ready(function () {
        // Custom form validation
        $("#save_fuel_provider_btn").on("click", function (event) {
            event.preventDefault()
            var name = $("#name").val();
            var phone = $("#phone").val();

            // Clear previous error messages
            $("#name-error").text("");
            $("#phone-error").text("");

            // Validate name
            if (name === "") {
                $("#name-error").text("Name is required");
                return false; // Prevent form submission
            }

            // Validate phone
            if (phone === "") {
                $("#phone-error").text("Phone is required");
                return false; // Prevent form submission
            }

            // If validation passes, submit the form
            $("#fuel_provider_form").submit();
        });
    });
</script>
