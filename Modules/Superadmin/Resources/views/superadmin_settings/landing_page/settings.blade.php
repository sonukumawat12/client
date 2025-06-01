@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Business')

@section('content')
<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">Landing page / <small> Settings</small></h3>
        </div>
        <hr>
        <div class="box-body" style="background: white !important;">
            <div class="col-md-12 bg-white">
                {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@savelandingSettings'), 'method' => 'post', 'id' => 'landing_settings_form','files' => false ]) !!}
                    
                    <div class="row">
                        <div class="col-sm-3 ">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('about', 1, !empty($data['about']) ? $data['about'] :false, ['class' => 'input-icheck', 'id' =>
                                    'about']); !!}
                                     About
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3 ">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('how_it_works', 1, !empty($data['how_it_works']) ? $data['how_it_works'] : false, ['class' => 'input-icheck', 'id' =>
                                    'how_it_works']); !!}
                                     How it Works
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3 ">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('features', 1, !empty($data['features']) ? $data['features'] : false, ['class' => 'input-icheck', 'id' =>
                                    'features']); !!}
                                     Features
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3 ">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('pricing', 1, !empty($data['pricing']) ? $data['pricing'] : false, ['class' => 'input-icheck', 'id' =>
                                    'pricing']); !!}
                                     Pricing
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3 ">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('contact', 1, !empty($data['contact']) ? $data['contact'] : false, ['class' => 'input-icheck', 'id' =>
                                    'contact']); !!}
                                     Contact
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-sm-3 ">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('faq', 1, !empty($data['faq']) ? $data['faq'] : false, ['class' => 'input-icheck', 'id' =>
                                    'faq']); !!}
                                     FAQs
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-sm-3 ">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('language', 1, !empty($data['language']) ? $data['language'] : false, ['class' => 'input-icheck', 'id' =>
                                    'language']); !!}
                                     Language
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-sm-3 ">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('login', 1, !empty($data['login']) ? $data['login'] : false, ['class' => 'input-icheck', 'id' =>
                                    'login']); !!}
                                     Login
                                </label>
                            </div>
                        </div>
                        
                        
                        <div class="col-sm-3 ">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('signup', 1, !empty($data['signup']) ? $data['signup'] : false, ['class' => 'input-icheck', 'id' =>
                                    'signup']); !!}
                                     Signup
                                </label>
                            </div>
                        </div>
                        
                        
                    </div>
                    
                
                    {!! Form::submit(__('messages.submit'), ['class' => 'btn btn-success pull-right']) !!}
                    {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade brands_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection


@section('javascript')
    @if(session('toast_error'))
        <script>
            toastr.error('{{ session('toast_error') }}');
        </script>
    @endif
    
    @if(session('toast_success'))
        <script>
            toastr.success('{{ session('toast_success') }}');
        </script>
    @endif

    <script type="text/javascript">
        
    </script>
@endsection