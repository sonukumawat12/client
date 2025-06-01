<div class="modal-dialog" role="document" style="width: 65%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\MemberController@update',
    $member->id), 'method' => 'PUT', 'id' => 'member_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.edit_member' ) ({{$member->username}})</h4>
    </div>

    <div class="modal-body">
      <div class="box box-widget">
        <div class="box-header with-border w-100">
           <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
    
      <!-- /.box-tools -->
      </div>
      <div class="box-body">
        {!! Form::hidden('username', $member->username,['id'=>'username']); !!}
        <div class="col-md-11">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_name', __('business.name') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('name', $member->name, ['class' => 'form-control','placeholder' =>
                __('business.name'),
                'required']); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_address', __('business.address') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('address', $member->address, ['class' => 'form-control','placeholder' =>
                __('business.address'),
                'required']); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('electrorate_id', __('member::lang.electrorate') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::select('electrorate_id',$electrorates, $member->electrorate_id ?? null, [
                    'class' => 'form-control select2',
                    'id' => 'electrorate_select',
                    'required',
                    'placeholder' => __('messages.please_select'),
                ]) !!}
              </div>
            </div>
    
          </div>
        </div>
        <div class="col-md-11">  
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('gramasevaka_area', __('business.gramasevaka_area') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::select('gramasevaka_area', $gramasevaka_areas, $member->gramasevaka_area, ['class'
                => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
                ]); !!}
              </div>
            </div>
          </div>
          {{-- <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_town', __('business.town') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('town', $member->town, ['class' => 'form-control','placeholder' =>
                __('business.town'),
                'required']); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_district', __('business.district') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('district', $member->district, ['class' => 'form-control','placeholder' =>
                __('business.district'),
                'required']); !!}
              </div>
            </div>
          </div> --}}
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_mobile_number_1', __('business.mobile_number_1') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('mobile_number_1', $member->mobile_number_1, ['class' => 'form-control','placeholder' =>
                __('business.mobile_number_1'),
                'required']); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_mobile_number_2', __('business.mobile_number_2') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('mobile_number_2',  $member->mobile_number_2, ['class' => 'form-control','placeholder' =>
                __('business.mobile_number_2')
                ]); !!}
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-11">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_mobile_number_3', __('business.mobile_number_3') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('mobile_number_3',  $member->mobile_number_3, ['class' => 'form-control','placeholder' =>
                __('business.mobile_number_3')
                ]); !!}
              </div>
            </div>
          </div>

          {{-- <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_land_number', __('business.land_number') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('land_number', $member->land_number, ['class' => 'form-control','placeholder' =>
                __('business.land_number')
                ]); !!}
              </div>
            </div>
          </div> --}}
        </div>
    </div>
  <!-- /.box-body -->
</div>
<div class="clearfix">
</div>
<hr>
<div class="box box-widget collapsed-box">
  <div class="box-header with-border w-100">
      <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
    </div>

<!-- /.box-tools -->
</div>
<div class="box-body" style="display: none">
  <div class="col-md-11"> 
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_gender', __('business.gender') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::select('gender', ['male' => 'Male', 'female' => 'Female'], $member->gender, ['class' =>
            'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px']); !!}
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_date_of_birth', __('business.date_of_birth') . ':*') !!}
          @php
          $date_feild_name = 'member_date_of_birth';
          $date_feild  = [];
          if($member->date_of_birth){
          $data_feild = createDateArray($member->date_of_birth);
          }
          @endphp
          @include('components.date_feild_component')
               
          
          {{-- <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::text('date_of_birth', !empty($member->date_of_birth) ? \Carbon::parse($member->date_of_birth)->format('m/d/Y') : null, ['class' => 'form-control','placeholder' =>
            __('business.date_of_birth'), 'id' => 'date_of_birth'
            ]); !!}
          </div> --}}
        </div>
      </div>
      
      {{-- <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('bala_mandalaya_area', __('business.bala_mandalaya_area') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::select('bala_mandalaya_area', $bala_mandalaya_areas, $member->bala_mandalaya_area,
            ['class' => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
            ]); !!}
          </div>
        </div>
      </div> --}}
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('member_group', __('business.member_group') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user"></i>
            </span>
            {!! Form::select('member_group', $member_groups, $member->member_group,
            ['class' => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px', 'disabled=>true']); !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
<div class="clearfix">
</div>
<hr>
@include('member::member.partials.member_family_feilds')
<div class="clearfix"></div>

    </div>

    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    
      <button type="submit" class="btn btn-primary" id="save_member_btn">@lang( 'member::lang.update'
        )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('.date-field').autotab('number');
  
    $('#electrorate_select').select2({
        width: '100%'
    });
</script>