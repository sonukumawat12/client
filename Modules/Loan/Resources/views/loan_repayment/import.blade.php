@extends('layouts.app')
@section('title', __('loan::lang.import_loan') . ' ' . trans_choice('core.repayment', 2))

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('loan::lang.import_loan') {{ trans_choice('core.repayment', 2) }}</h1>
    </section>

    <!-- Main content -->
    <section class="content">

        @if (session('notification') || !empty($notification))
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        @if (!empty($notification['msg']))
                            {{ $notification['msg'] }}
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
                    {!! Form::open(['url' => url('contact_loan/store_import_repayments'), 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    {!! Form::label('name', __('product.file_to_import') . ':') !!}
                                    {!! Form::file('bulk_loan_csv', ['accept' => '.xls, .xlsx', 'required' => 'required']) !!}
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
                            <a href="{{ Module::asset('loan:files/import_loan_repayment_template.xlsx') }}" class="btn btn-success" download>
                                <i class="fa fa-download"></i>
                                @lang('lang_v1.download_template_file')
                            </a>
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

                        @foreach ($instructions as $index => $instruction)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $instruction['column_name'] }} <small class="text-muted">({{ $instruction['required'] }})</small>
                                </td>
                                @if (array_key_exists('instruction', $instruction))
                                    <td>{!! $instruction['instruction'] !!}</td>
                                @else
                                    <td>&nbsp;</td>
                                @endif
                            </tr>
                        @endforeach

                    </table>
                @endcomponent
            </div>
        </div>
    </section>
    <!-- /.content -->

@endsection
