
<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\LocationsController@addProvince'), 'method' => 'post', 'id' => 'province_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center">@lang('dsr::lang.add_province')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('country_id', 'Country') !!}
                    {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2', 'style' => "width:100%", 'id'  => 'country_id']) !!}
                    <div class="text-danger" id="country_id-error"></div>
                </div>
                <div class="col-md-12">
                    {!! Form::label('name', 'Province Name') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'name']); !!}
                    <div class="text-danger" id="name-error"></div>
                </div>
                <div class="col-md-12 mt-2 text-right">
                    <button type="button" class="btn btn-success" id="submitBtn">Submit</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>


<script >
    $(()=>{
        $('.select2').select2();
    })
    $(document).ready(function () {
        $('#province_id').empty();
        $('#submitBtn').click(function() {
            event.preventDefault()
            var name = $("#name").val();
            var country_id = $("#country_id").val();

            // Clear previous error messages
            $("#name-error").text("");
            $("#country_id-error").text("");

            // Validate name
            if (name === "") {
                $("#name-error").text("Name is required");
                return false;
            }
            if (country_id === "") {
                $("#country_id-error").text("Country is required");
                return false;
            }
            // If validation passes, submit the form
            $("#province_form").submit();
        });

    });
</script>
