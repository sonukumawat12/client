
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\LocationsController@addDistrict'), 'method' => 'post', 'id' => 'location_district_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center">@lang('dsr::lang.add_districts')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('country_id', 'Country') !!}
                    {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2', 'style'=>"width:100%", 'id' => 'country_id', 'required']) !!}
                    <div class="text-danger" id="country_id-error"></div>
                </div>
                <div class="col-md-6">
                    {!! Form::label('province_id', 'Province') !!}
                    {!! Form::select('province_id', $provinces, null, ['class' => 'form-control select2', 'style'=>"width:100%", 'id' => 'province_id', 'required']) !!}
                    <div class="text-danger" id="province_id-error"></div>
                </div>
            </div>
            <div class="row" id="district_names">
                <div class="col-md-12">
                    {!! Form::label('name', 'District Name') !!}
                    <div class="text-danger" id="name-error"></div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                     {!! Form::text('name[]', null, ['class' => 'form-control', 'required']); !!}
                      <span  class="input-group-addon bg-success" id="district_add_name"> + </span>
                    </div>
                    
                </div>
            </div>
                
            <div class="row">
                <div class="col-md-12 mt-2 text-right">
                    <button type="submit" class="btn btn-success">Submit</button>
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
    $("#district_add_name").on('click',function(){
        var html = `
                <div class="col-md-4 added_row">
                    <div class="input-group">
                     {!! Form::text('name[]', null, ['class' => 'form-control', 'required']); !!}
                      <span  class="input-group-addon bg-danger district_remove_name"> - </span>
                    </div>
                    
                </div>`;
        $("#district_names").append(html);
    });
    
    $(document).on('click','.district_remove_name',function(){
        $(this).closest('.added_row').remove();
    })
</script>
