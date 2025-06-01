<div class="box box-widget collapsed-box">
    <div class="box-header with-border w-100">
        <span>Account Information</span>
        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
      </div>
    </div>
    <div class="box-body" style="display: none">
        <div class="col-md-11">  
            <div class="col-md-4">
                <div class="form-group">
                {!! Form::label('member_gender', __('business.gender') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('member_gender', ['male' => 'Male', 'female' => 'Female'],null, ['class' =>
                        'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px'
                        ]); !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                {!! Form::label('member_date_of_birth', __('business.date_of_birth') . ':') !!}
                {{-- <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                    </span>
                    {!! Form::text('member_date_of_birth', null, ['class' => 'form-control','placeholder' =>
                    __('business.date_of_birth'), 'id' => 'date_of_birth'
                    ]); !!}
                </div> --}}
                @php
                    $date_feild_name = 'member_date_of_birth';
                    $date_feild  = [];
                @endphp
                @include('components.date_feild_component')
                </div>
            </div>
        
            {{-- <div class="col-md-4">
                <div class="form-group">
                {!! Form::label('bala_mandalaya_area', __('business.bala_mandalaya_area') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                    </span>
                    {!! Form::select('bala_mandalaya_area', $bala_mandalaya_areas, null,
                    ['class' => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px',
                    ]); !!}
                </div>
                </div>
            </div> --}}
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_group', __('business.member_group') . ':') !!}
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </span>
                    {!! Form::select('member_group', $member_groups, null,
                    ['class' => 'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px']); !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('member_password', __('business.password') . ':') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-key"></i>
                </span>

                {!! Form::text('member_password','123456', ['class' => 'form-control', 'id' => 'password', 'disabled' => 'disabled', 'style' =>
                'margin: 0px;','placeholder'
                => __('business.password')]); !!}
                </div>
                <p class="help-block" style="color: #222;">At least 6 character.</p>
            </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('member_confirm_password', __('business.confirm_password') . ':*') !!}
                    <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-key"></i>,
                    </span>
                    {!! Form::text('member_confirm_password','123456', ['class' => 'form-control', 'id' =>
                    'confirm_password',  'disabled' => 'disabled','style' => 'margin: 0px;', 'placeholder' =>
                    __('business.confirm_password')]); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
      <!-- /.box-body -->
</div>