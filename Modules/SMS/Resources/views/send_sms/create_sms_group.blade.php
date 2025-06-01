<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title" id="exampleModalLabel">@lang('sms::lang.add_sms_group')</h3>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('\Modules\SMS\Http\Controllers\SMSController@storeSmsGroup'), 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('date', __('sms::lang.date') . ':') !!}
                        <div class="input-group">
                            {!! Form::text('date', date('Y/m/d'), ['class' => 'form-control', 'placeholder' => __('sms.date'), 'required', 'readonly']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('sms_group_name', __('sms::lang.sms_group_name') . ':') !!}
                        <div class="input-group">
                            {!! Form::text('group_name', null, ['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('import_file', __('sms::lang.import_file') . ':') !!}
                        <div class="input-group">
                            {!! Form::file('import_file', ['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('template_download', __('sms::lang.template_format') . ':') !!}
                        <div class="input-group">
                           <a href="{{ url('storage/app/import_file_demo.csv') }}" class="btn btn-info">
                                @lang('sms::lang.download_csv')
                            </a>

                            <a href="{{ url('storage/app/import_file_demo.txt') }}" class="btn btn-info" style="margin-left: 10px;">
                                @lang('sms::lang.download_text')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <button type="button" style="margin-right: 5px;" class="pull-right btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" style="margin-right: 5px;" class="pull-right btn btn-primary">@lang('messages.save')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
