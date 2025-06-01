<style>
    .select2-container {
        display: inherit !Important;
    }
</style>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="area-section">
        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\LocationsController@addarea'), 'method' => 'post' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center">@lang('dsr::lang.add_areas')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    {!! Form::label('country_id', 'Country') !!}
                    {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2','style'=>"width:100%",  'id' => 'country_id']) !!}
                    <div class="text-danger" id="country_id-error"></div>
                </div>
                <div class="col-md-4">
                    {!! Form::label('province_id', 'Province') !!}
                    {!! Form::select('province_id', $provinces, null, ['class' => 'form-control select2', 'style'=>"width:100%", 'id' => 'province_id']) !!}
                    <div class="text-danger" id="province_id-error"></div>
                </div>
                <div class="col-md-4">
                    {!! Form::label('district_id', 'District') !!}
                    {!! Form::select('district_id', $districts, null, ['class' => 'form-control select2', 'style'=>"width:100%", 'id' => 'district_id','required']) !!}
                    <div class="text-danger" id="district_id-error"></div>
                </div>
            </div>
            <div class="row" id="area_names">
                <div class="col-md-12">
                    {!! Form::label('name', 'Area Name') !!}
                    <div class="text-danger" id="name-error"></div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                     {!! Form::text('name[]', null, ['class' => 'form-control', 'required']); !!}
                      <span  class="input-group-addon bg-success" id="area_add_name"> + </span>
                    </div>
                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mt-2 text-right">
                    <button type="submit" class="btn btn-success" >Submit</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>


<script >
    
    $(document).ready(function () {
        $('.select2').select2();
        $('#province_id').empty();
        $('#district_id').empty();
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
    
    $("#area_add_name").on('click',function(){
        var html = `
                <div class="col-md-4 added_row">
                    <div class="input-group">
                     {!! Form::text('name[]', null, ['class' => 'form-control', 'required']); !!}
                      <span  class="input-group-addon bg-danger area_remove_name"> - </span>
                    </div>
                    
                </div>`;
        $("#area_names").append(html);
    });
    
    $(document).on('click','.area_remove_name',function(){
        $(this).closest('.added_row').remove();
    })
 

</script>
