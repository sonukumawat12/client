@extends('layouts.app')
@section('title', __('vat::lang.import_contacts'))

@section('content')



<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('vat::lang.import_contacts')</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Products</a></li>
                    <li><span>@lang('vat::lang.import_contacts')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content main-content-inner">
    
    @if (session('notification') || !empty($notification))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    @if(!empty($notification['msg']))
                        {{$notification['msg']}}
                    @elseif(session('notification.msg'))
                        {{ session('notification.msg') }}
                    @endif
                </div>
            </div>  
        </div>     
    @endif
    
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary'])
                {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\ImportContactsController@store'), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="col-sm-8">
                            <div class="form-group">
                                {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                                {!! Form::file('products_csv', ['accept'=> '.xls', 'required' => 'required']); !!}
                              </div>
                        </div>
                        <div class="col-sm-4">
                        <br>
                            <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
                        </div>
                        </div>
                    </div>

                {!! Form::close() !!}
                <br><br>
                <div class="row">
                    <div class="col-sm-4">
                        <a href="{{ asset('files/vat_import_contacts_csv_template.xls') }}" class="btn btn-success" download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.instructions')])
                <strong>@lang('lang_v1.instruction_line1')</strong><br>
                    @lang('lang_v1.instruction_line2')
                    <br><br>
                <table class="table table-striped">
                    <tr>
                        <th>@lang('lang_v1.col_no')</th>
                        <th>@lang('lang_v1.col_name')</th>
                        <th>@lang('lang_v1.instruction')</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>@lang('vat::lang.contact_name') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('vat::lang.contact_name')</td>
                    </tr>
                   
                    <tr>
                        <td>2</td>
                        <td>@lang('vat::lang.contact_type') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('vat::lang.contact_type') <br>
                            <strong>@lang('vat::lang.available_options'): customer, supplier</strong></td>
                    </tr>
                    
                    <tr>
                        <td>3</td>
                        <td>@lang('vat::lang.contact_id') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('vat::lang.contact_id')</td>
                    </tr>
                    
                    <tr>
                        <td>4</td>
                        <td>@lang('vat::lang.mobile') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('vat::lang.mobile') </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>@lang('vat::lang.alternate_number') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('vat::lang.alternate_number')
                        </td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>@lang('vat::lang.vat_no') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('vat::lang.vat_no')
                        </td>
                    </tr>

                </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection