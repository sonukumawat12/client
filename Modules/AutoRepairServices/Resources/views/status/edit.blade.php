<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\AutoRepairServices\Http\Controllers\RepairStatusController@update', [$status->id]), 'method' => 'put', 'id' => 'status_form']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'repair::lang.edit_status' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('name', __( 'repair::lang.status_name' ) . ':*') !!}
                    {!! Form::text('name', $status->name, ['class' => 'form-control', 'readonly']) !!}
                    </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('color', __( 'repair::lang.color' ) . ':') !!}
                    <input type="color" name="color" value="{{ $status->color }}" class="form-control" disabled>
                    </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('sort_order', __( 'repair::lang.sort_order' ) . ':') !!}
                    {!! Form::number('sort_order', $status->sort_order, ['class' => 'form-control', 'placeholder' => __( 'repair::lang.sort_order' ) ]); !!}
                </div>
            </div>
            <div class="col-md-6 mt-15">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                        <input type="checkbox" name="is_completed_status" value="1" 
                        {{ $status->is_completed_status ? 'checked' : '' }} disabled>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('sms_template', __( 'repair::lang.sms_template' ) . ':') !!}
                    {!! Form::textarea('sms_template', $status->sms_template, ['class' => 'form-control', 'readonly', 'placeholder' => __( 'repair::lang.sms_template' ), 'rows' => 4, 'id' => 'sms_template']); !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('email_subject', __( 'lang_v1.email_subject' ) . ':') !!}
                    {!! Form::text('email_subject', $status->email_subject, ['class' => 'form-control', 'readonly', 'placeholder' => __( 'lang_v1.email_subject' ), 'id' => 'email_subject']); !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('email_body', __( 'lang_v1.email_body' ) . ':') !!}
                    {!! Form::textarea('email_body', $status->email_body, ['class' => 'form-control', 'readonly', 'placeholder' => __( 'lang_v1.email_body' ), 'rows' => 5, 'id' => 'email_body']); !!}
                    <p class="help-block">
                        <label>{{$status_template_tags['help_text']}}:</label><br>
                        {{implode(', ', $status_template_tags['tags'])}}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->