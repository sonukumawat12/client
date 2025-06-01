<!-- Edit Modal -->
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title" id="exampleModalLabel">@lang('sms::lang.edit_sms_group')</h3>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('\Modules\SMS\Http\Controllers\SMSController@updateSmsGroup', ['id' => $sms_group->id]), 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
            {{ method_field('POST') }}
            {{ csrf_field() }}
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('sms_group_name', __('sms::lang.sms_group_name') . ':') !!}
                        <div class="input-group">
                            {!! Form::text('group_name', $sms_group->group_name, ['class' => 'form-control', 'required']) !!}
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
