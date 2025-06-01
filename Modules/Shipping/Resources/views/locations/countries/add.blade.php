
<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\LocationsController@addCountry'), 'method' => 'post', 'id' => 'country_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center">@lang('shipping::lang.add_country')</h4>
        </div>
        <div class="modal-body">
               <div class="row">
                    <div class="col-md-12">

                        {!! Form::label('name', __('shipping::lang.country') . ':*') !!}
                        {!! Form::text('country', null, ['class' => 'form-control', 'required', 'id' => 'name']); !!}
                        <div class="text-danger" id="name-error"></div>

                    </div>
                   <div class="col-md-12">
                           {!! Form::label('country_code', 'Country Code') !!}
                           {!! Form::text('country_code', null, ['class' => 'form-control', 'id' => 'country_code']) !!}
                           <div class="text-danger" id="country-code-error"></div>
                   </div>
                   <div class="col-md-12">
                           {!! Form::label('currency_code', 'Currency Code') !!}
                           {!! Form::text('currency_code', null, ['class' => 'form-control', 'id' => 'currency_code']) !!}
                           <div class="text-danger" id="currency-code-error"></div>
                   </div>
                   <div class="col-md-12 mt-2 text-center">
                       <button type="button" class="btn btn-success" id="submitBtn">Submit</button>
                   </div>
                </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>


<script >
    $(document).ready(function () {
        $('#submitBtn').click(function() { 
                event.preventDefault()
                var name = $("#name").val();
                var country_code = $("#country_code").val();
                var currency_code = $("#currency_code").val();

                // Clear previous error messages
                $("#name-error").text(""); 
                   $("#country-code-error").text("");
                     $("#currency-code-error").text("");
                // Validate name
                if (name === "" || name === null) {
                    $("#name-error").text("Name is required");
                    return false;
                }
                // Validate country code
                if (country_code === "" || country_code === null) {
                    $("#country-code-error").text("Country Code is required");
                    return false;
                }
                // Validate currency code
                if (currency_code === "" || currency_code === null) {
                    $("#currency-code-error").text("Currency Code is required");
                    return false;
                }

                // If validation passes, submit the form
                $("#country_form").submit();
        });

    });
</script>
