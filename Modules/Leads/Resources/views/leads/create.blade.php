<div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content">

        <style>
            .select2 {
                width: 100% !important;
            }
        </style>
        {!! Form::open(['url' => action('\Modules\Leads\Http\Controllers\LeadsController@store'), 'method' =>
        'post', 'id' => 'leads_form', 'enctype' => 'multipart/form-data' ])
        !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'leads::lang.add_leads' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('date', __( 'leads::lang.date' )) !!}
                    {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control', 'required', 'placeholder' => __(
                    'leads::lang.date' ),
                    'id' => 'leads_date']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('time', __( 'leads::lang.time' )) !!}
                    {!! Form::text('time', date('h:i:s'), ['class' => 'form-control', 'required', 'placeholder' => __(
                    'leads::lang.time' ), 'id' => 'leads_time']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('mobile_no_1', __( 'leads::lang.mobile_no_1' )) !!}
                    {!! Form::text('mobile_no_1', null, ['class' => 'form-control input_number', 'required','placeholder' => __(
                    'leads::lang.mobile_no_1' ),
                    'id' => 'mobile_no_1']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('sector', __( 'leads::lang.sector' )) !!}
                    {!! Form::select('sector', ['private' => __('leads::lang.private'), 'government' =>
                    __('leads::lang.government')], null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'sector']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('category_id', __( 'leads::lang.category' )) !!}
                    {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'category_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('main_organization', __( 'leads::lang.main_organization' )) !!}
                    {!! Form::text('main_organization', null, ['class' => 'form-control', 'placeholder' => __(
                    'leads::lang.main_organization' ), 'required', 
                    'id' => 'main_organization']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('business', __( 'leads::lang.business' )) !!}
                    {!! Form::text('business', null, ['class' => 'form-control', 'placeholder' => __(
                    'leads::lang.business' ), 'required',
                    'id' => 'business']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('address', __( 'leads::lang.address' )) !!}
                    {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => __(
                    'leads::lang.address' ), 'required', 
                    'id' => 'address']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('country', __( 'leads::lang.country' )) !!}
                    {!! Form::select('country', $countries, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'country']);
                    !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('district', __( 'leads::lang.district' )) !!}
                    {!! Form::select('district', $districts->pluck('name', 'name'), null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'district']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('town', __( 'leads::lang.town' )) !!}
                    {!! Form::select('town', array(), null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'town']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('mobile_no_2', __( 'leads::lang.mobile_no_2' )) !!}
                    {!! Form::text('mobile_no_2', null, ['class' => 'form-control input_number', 'placeholder' => __(
                    'leads::lang.mobile_no_2' ), 'required', 
                    'id' => 'mobile_no_2']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('mobile_no_3', __( 'leads::lang.mobile_no_3' )) !!}
                    {!! Form::text('mobile_no_3', null, ['class' => 'form-control input_number', 'placeholder' => __(
                    'leads::lang.mobile_no_3' ), 'required',
                    'id' => 'mobile_no_3']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('land_number', __( 'leads::lang.land_number' )) !!}
                    {!! Form::text('land_number', null, ['class' => 'form-control input_number', 'placeholder' => __(
                    'leads::lang.land_number' ), 'required',
                    'id' => 'land_number']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('email', __( 'leads::lang.email' )) !!}
                    {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => __(
                    'leads::lang.email' ), 'required',
                    'id' => 'email']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('client_response', __( 'leads::lang.client_resp' )) !!}
                    {!! Form::text('client_response', null, ['class' => 'form-control', 'placeholder' => __(
                    'leads::lang.client_resp' ), 'required',
                    'id' => 'client_response']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('follow_up_date', __( 'leads::lang.follow_up_date' )) !!}
                    {!! Form::text('follow_up_date', null, ['class' => 'form-control date', 'required', 'placeholder' => __(
                    'leads::lang.follow_up_date' ),
                    'id' => 'leads_follow_up_date']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('label_id', __( 'leads::lang.labels' )) !!}
                    {!! Form::select('label_id', $labels, null, ['class' => 'form-control select2',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'label_id']);
                    !!}
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            <div class="form-group">
                {!! Form::label('note', __( 'brand.note' )) !!}
                {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]);
                !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save_leads_btn" >@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

 <!-- Modal -->
 <div class="modal fade" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" id="md2"><span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title" id="myModalLabel"> Your Number Already Exist! </h4>
       </div>
       <div class="modal-body" id="getCode" style="overflow-x: scroll;">
          //ajax success content here.
       </div>
    </div>
   </div>
 </div>

<script>
$('#leads_follow_up_date').datepicker({
    format: 'mm/dd/yyyy'
});
$('.select2').select2();

$("#leads_form").validate({
    rules: {
      mobile_no_1: "required",
      sector: "required",
      category_id: "required",
      main_organization: "required",
      business: "required",
      address: "required",
      town: "required",
      mobile_no_2: "required",
      mobile_no_3: "required",
      land_number: "required",
      email: "required",
      client_response: "required",
      follow_up_date: "required",
    },
    
    submitHandler: function(form) {
      $.ajax({
            url: $('#leads_form').attr('action'),
            type: "POST",
            data: $('#leads_form').serialize(),
            success: function(response) {
                $('#save_leads_btn').html('Submit');
                toastr.success("Leads created successfully!");
                leads_table.ajax.reload();
                document.getElementById("leads_form").reset();
                $('.leads_model').modal("hide");
    
            }
        });
        return false;
    }
  });


function genModalContent(data){
    
    return `<div class="container"><ul class="list-group list-group-flush">
                <li class="list-group-item"> Date Created: ${data.created_at
                }</li>
                <li class="list-group-item">Mobile No 1: ${data.mobile_no_1
                }</li>
                <li class="list-group-item">Sector: ${data.sector
                }</li>
                <li class="list-group-item">Category: ${data.category_id}</li>
                <li class="list-group-item">Main Organization: ${data.main_organization
                }</li>
                <li class="list-group-item">Client Name: ${data.email
                }</li>
                <li class="list-group-item">District: ${data.district}</li>
                <li class="list-group-item">Town: ${data.town}</li>
                <li class="list-group-item">Mobile No2: ${data.mobile_no_2}</li>
                <li class="list-group-item">EMail ID: ${data.email}</li>
                <li class="list-group-item">Follow up Date: ${data.follow_up_date}</li>
                <li class="list-group-item">User: ${data.business_id
                }</li>
                </ul></div>`;

}

$(document).ready(function() {
    $("#leads_date").attr("readonly", true);
    $("#leads_time").attr("readonly", true);
    
    // Mobile 1 ajax query.
    $('#mobile_no_1').blur(function() {
        var mobile1 = $(this).val();
        $.ajax({
            url: '{{route('ajax_mobile')}}',
            type: 'POST',
            data: {
                'postData': mobile1
            },
            success: function(data) {
                if(data) {
                    $("#save_leads_btn").prop("disabled", true);
                    //   show modal 
                    let content = genModalContent(data);
                    document.getElementById('getCode').innerHTML="";
                    $( "#getCode" ).empty("").append(content);
                    $("#getCodeModal").modal('show');
                }else{
                    console.log('data not found');
                    $("#save_leads_btn").prop("disabled", false);
                }
            }
        });
    });
    

    // Mobile 2 ajax query
    $('#mobile_no_2').blur(function() {
        var mobile2 = $(this).val();
        $.ajax({
            url: '{{route('ajax_mobile')}}',
            type: 'POST',
            data: {
                'postData': mobile2
            },
            success: function(data) {
                if(data) {
                    $("#save_leads_btn").prop("disabled", true);
                        
                    //   show modal 
                    let content = genModalContent(data);
                    // document.getElementById('getCode').innerHTML="";
                    // document.getElementById('getCodeModal').innerHTML="";
                    $("#getCode").empty("").append(content);
                    $("#getCodeModal").modal('show');
                } else {
                    console.log('data not found');
                    $("#save_leads_btn").prop("disabled", false);
                }
            }
        });

        
    });
    
    $('#district').change(function(event){
        let district = $(this).val();
        
       $.ajax({
           url: '{{route('ajax_town')}}',
           type: 'POST',
           data: {'postData': district},
           success: function(data){
                // console.log(data[0]);
                $('#town').find('option').not(':first').remove();
                var len = 0;
                    if(data != null){
                        len = data.length;
                    }
                if(len > 0){
                    // Read data and create <option >
                    for(var i=0; i<len; i++){
                        var id = data[i].id;
                        var name = data[i].name;
                        var option = "<option value='"+name+"'>"+name+"</option>";
                        $("#town").append(option);
                    }
                }
           }
       });
    });
    
    $('#country').change(function(event){
        let country = $(this).val();
        
       $.ajax({
           url: '{{route('ajax_district')}}',
           type: 'POST',
           data: {'postData': country},
           success: function(data){
                // console.log(data[0]);
                $('#district').find('option').not(':first').remove();
                var len = 0;
                    if(data != null){
                        len = data.length;
                    }
                if(len > 0){
                    // Read data and create <option >
                    for(var i=0; i<len; i++){
                        var id = data[i].id;
                        var name = data[i].name;
                        var option = "<option value='"+name+"'>"+name+"</option>";
                        $("#district").append(option);
                    }
                }
           }
       });
    });

});
    
    
// close sesocnd modal
$("#md2").click(function(){
     document.getElementById('getCode').innerHTML="";
    $("#addModal").addClass("add_modal");
    $('#getCodeModal').modal('hide');
});
</script>
<style>
.add_modal {
    position: absolute !important;
}
</style>