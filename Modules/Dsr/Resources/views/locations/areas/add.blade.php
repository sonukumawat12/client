<style>
    .select2-container {
        display: inherit !Important;
    }
</style>
<div class="modal-dialog modal-md" role="document">
    <div class="modal-content" id="area-section">
        {!! Form::open(['url' => action('\Modules\Dsr\Http\Controllers\LocationsController@addarea'), 'method' => 'post', 'id' => 'areas_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center">@lang('dsr::lang.add_areas')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('country_id', 'Country') !!}
                    {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2','style'=>"width:100%",  'id' => 'country_id']) !!}
                    <div class="text-danger" id="country_id-error"></div>
                </div>
                <div class="col-md-12">
                    {!! Form::label('province_id', 'Province') !!}
                    {!! Form::select('province_id', $provinces, null, ['class' => 'form-control select2', 'style'=>"width:100%", 'id' => 'province_id']) !!}
                    <div class="text-danger" id="province_id-error"></div>
                </div>
                <div class="col-md-12">
                    {!! Form::label('district_id', 'District') !!}
                    {!! Form::select('district_id', $districts, null, ['class' => 'form-control select2', 'style'=>"width:100%", 'id' => 'district_id']) !!}
                    <div class="text-danger" id="district_id-error"></div>
                </div>
                <div class="col-md-12">
                    {!! Form::label('name', 'Area Name') !!}
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
        $('#province_id').empty();
        $('#district_id').empty();
    })
    $(document).ready(function () {

        $('#submitBtn').click(function(event) {
            event.preventDefault()
            var name = $("#name").val();
            var country_id = $("#country_id").val();
            var province_id = $("#province_id").val();
            var district_id = $("#district_id").val();

            // Clear previous error messages
            $("#name-error").text("");
            $("#country_id-error").text("");
            $("#province_id-error").text("");
            $("#district_id-error").text("");

            // Validate name
            if (name === "" || name === null) {
                $("#name-error").text("Name is required");
                return false;
            }
            if (country_id === "" || country_id === null) {
                $("#country_id-error").text("Country is required");
                return false;
            }
            if (province_id === "" || province_id === null) {
                $("#province_id-error").text("Province is required");
                return false;
            }
             if (district_id === "" || district_id === null) {
                $("#district_id-error").text("District is required");
                return false;
            }
             $("#areas_form").submit();

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
 

</script>
