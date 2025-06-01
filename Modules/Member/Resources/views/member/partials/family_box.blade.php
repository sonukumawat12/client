<div class="col-md-12 box box-widget feild-box" > 
    <div class="box-body"> 
          <div class="col-md-4">
            @isset($family_member)
            {!! Form::hidden('family['.$row_number.'][id]', $family_member->id) !!}
              
            @endisset
            <div class="form-group">
              {!! Form::label('username_'.$row_number, __('business.member_code') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('family['.$row_number.'][username]', $family_member->username ?? $member_code, ['class' => 'form-control','placeholder' =>
                __('business.member_code'),
                'id' => 'username_'.$row_number,
                'readonly']); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_name_'.$row_number, __('business.name') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('family['.$row_number.'][member_name]', $family_member->name ??null, ['class' => 'form-control','placeholder' =>
                __('business.name'),
                'id'=> 'member_name_'.$row_number]); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_relation_'.$row_number, __('member::lang.relation') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('family['.$row_number.'][relation]', $family_member->relation_name ?? null, ['class' => 'form-control','placeholder' =>
                __('member::lang.relation'),
                'id'=> 'relation_'.$row_number]); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_mobile_number_1_'.$row_number, __('business.mobile_number_1') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('family['.$row_number.'][member_mobile_number_1]', $family_member->mobile_number_1 ?? null, ['class' => 'form-control','placeholder' =>
                __('business.mobile_number_1'),
                'id'=>'member_mobile_number_1_'.$row_number,
                ]); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_mobile_number_2_'.$row_number, __('business.mobile_number_2') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('family['.$row_number.'][member_mobile_number_2]', $family_member->mobile_number_2 ?? null, ['class' => 'form-control','placeholder' =>
                __('business.mobile_number_2'),
                'id'=>'member_mobile_number_2_'.$row_number,
                ]); !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('member_mobile_number_3_'.$row_number, __('business.mobile_number_3') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text('family['.$row_number.'][member_mobile_number_3]', $family_member->mobile_number_3 ?? null, ['class' => 'form-control','placeholder' =>
                __('business.mobile_number_3'),
                'id'=>'member_mobile_number_3_'.$row_number,
                ]); !!}
              </div>
            </div>
          </div>
            <div class="col-md-4">
                <div class="form-group">
                {!! Form::label('date_of_birth_'.$row_number, __('business.date_of_birth') . ':') !!}
                {{-- <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                    </span>
                    {!! Form::text('family['.$row_number.'][date_of_birth]', (isset($family_member) && isset($family_member->date_of_birth))? date('d/m/Y',strtotime($family_member->date_of_birth)) :null, ['class' => 'form-control','placeholder' =>
                    __('business.date_of_birth'), 'id' => 'date_of_birth_'.$row_number
                    ]); !!}
                </div> --}}
                @php
                $date_feild_name = 'family['.$row_number.'][date_of_birth]';
                $date_feild  = [];
                if(isset($family_member) && $family_member->date_of_birth){
                $data_feild = createDateArray($family_member->date_of_birth);
                }
                @endphp
                @include('components.date_feild_component')
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                {!! Form::label('member_gender_'.$row_number, __('business.gender') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('family['.$row_number.'][member_gender]', ['male' => 'Male', 'female' => 'Female'],$family_member->gender ?? null, ['class' =>
                        'form-control','placeholder' => __('lang_v1.please_select'), 'style' => 'margin:0px', 'id'=> 'member_gender_'.$row_number 
                        ]); !!}
                    </div>
                </div>
            </div>

    </div>
    <div class="box-footer with-border w-100">
      <div class="box-tools pull-right mt-15">
        @if(isset($family_member))
        @if($row_number == $last_row_number)
        <button type="button" class="btn btn-box-tool bg-danger dlt-row" style="display: none"><i class="fa fa-close"></i></button>
        <button type="button" class="btn btn-box-tool bg-success add-row"><i class="fa fa-plus"></i></button>
        @else
        <button type="button" class="btn btn-box-tool bg-danger dlt-row" ><i class="fa fa-close"></i></button>
        <button type="button" class="btn btn-box-tool bg-success add-row" style="display: none"><i class="fa fa-plus"></i></button>
        @endif
        @else
        <button type="button" class="btn btn-box-tool bg-danger dlt-row" style="display: none"><i class="fa fa-close"></i></button>
        <button type="button" class="btn btn-box-tool bg-success add-row"><i class="fa fa-plus"></i></button>
        @endif
      </div>
    </div>
  </div>