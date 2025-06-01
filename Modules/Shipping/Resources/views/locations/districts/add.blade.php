
<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\LocationsController@addDistrict'), 'method' => 'post', 'id' => 'district_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center">@lang('shipping::lang.add_districts')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('country_id', 'Country') !!}
                    {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2', 'id' => 'district_add_country_id']) !!}
                    <div class="text-danger" id="country_id-error"></div>
                </div>
                <div class="col-md-12">
                    {!! Form::label('province_id', 'Province') !!}
                    {!! Form::select('province_id', $provinces, null, ['class' => 'form-control select2', 'id' => 'district_add_province_id']) !!}
                    <div class="text-danger" id="province_id-error"></div>
                </div>
                <div class="col-md-12">
                    {!! Form::label('name', 'District Name') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'district_add_name']); !!}
                    <div class="text-danger" id="name-error"></div>
                </div>
                <div class="col-md-12 mt-2 text-right">
                    <button type="button" class="btn btn-success" id="district_submitBtn">Submit</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>


<script >
    $(document).ready(function () {
        $(".select2").select2();
        
        $('#district_add_province_id').empty();
        $('#district_submitBtn').click(function(event) {
            event.preventDefault();
            var name = $("#district_add_name").val();
            var country_id = $("#district_add_country_id").val();
            var province_id = $("#district_add_province_id").val();

            // Clear previous error messages
            $("#district_add_name-error").text("");
            $("#district_add_country_id-error").text("");
            $("#district_add_province_id-error").text("");

            // Validate name
            if (name === "" || name === null) {
                $("#district_add_name-error").text("Name is required");
                return false;
            }
            if (country_id === "" || country_id === null) {
                $("#district_add_country_id-error").text("Country is required");
                return false;
            }
            // if (province_id === "" || province_id === null) {
            //     $("#district_add_province_id-error").text("Province is required");
            //     return false;
            // }

            $("#district_form").submit();
        });
    });
    $('#district_add_country_id').on('change', function () {
        var country_id = $(this).val();
        $.ajax({
            url: '/shipping/get-provinces/' + country_id,
            type: 'GET',
            success: function (data) {
                $('#district_add_province_id').html(data);
            }
        });
    });
</script>
