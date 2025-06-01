@extends('layouts.app')
@section('title', __('lang_v1.sms_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('lang_v1.sms_settings')}}</h1>
</section>

<!-- Main content -->
<section class="content">
     {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\MemberController@submitQuickSend'), 'method' =>
    'post', 'id' => 'member_form' ])
    !!}
    <div class="row">
        <div class="col-md-12">
           
            <div class="row">
              
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('member', __('Member') . ':') !!}
                        
                            {!! Form::select('user', $users, null, ['id' => 'user' ,'class' => 'form-control select2', 'placeholder' => __('lang_v1.all')]); !!}
                        
                    </div>
                    
                </div>
               
            </div>
            <div class="row">
                 <div class="col-md-3">
              
            
                 <button type="submit" class="btn btn-primary" id="save_electrorate_btn">@lang( 'messages.save' )</button>
    
           
                    
                </div>
            </div>
            
        </div>
    </div>
  {!! Form::close() !!}
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>


@endsection
