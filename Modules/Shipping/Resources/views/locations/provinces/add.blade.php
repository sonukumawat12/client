
<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\LocationsController@addProvince'), 'method' => 'post', 'id' => 'province_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center">@lang('shipping::lang.add_province')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('country_id', 'Country') !!}
                    {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2', 'id' => 'province_add_country_id']) !!}
                    <div class="text-danger" id="country_id-error"></div>
                </div>
                <div class="col-md-12">
                    {!! Form::label('name', 'Province Name') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'province_add_name']); !!}
                    <div class="text-danger" id="name-error"></div>
                </div>
                <div class="col-md-12 mt-2 text-right">
                    <button type="button" class="btn btn-success" id="province_submitBtn">Submit</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>


<script >
    $(document).ready(function () {
        $(".select2").select2();
        
        $('#province_add_province_id').empty();
        $('#province_submitBtn').click(function() {
            event.preventDefault()
            var name = $("#province_add_name").val();
            var country_id = $("#province_add_country_id").val();

            // Clear previous error messages
            $("#province_add_name-error").text("");
            $("#province_add_country_id-error").text("");

            // Validate name
            if (name === "") {
                $("#province_add_name-error").text("Name is required");
                return false;
            }
            if (country_id === "") {
                $("#province_add_country_id-error").text("Country is required");
                return false;
            }
            // If validation passes, submit the form
            $("#province_form").submit();
        });

    });
</script>
