<div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Leads\Http\Controllers\LeadsController@clientResponse'), 'method' => 'POST', 'id' => 'client_resp_form' ])
        !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Add Client Response</h4>
        </div>

        <div class="modal-body">
            <input type="hidden" value="{{$leads->id}}" name="leads_id">
            <div class="col">
                <div class="form-group d-block">
                    {!! Form::label('date', __( 'leads::lang.date' )) !!}
                    {!! Form::text('date', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                    'leads::lang.date' ),
                    'id' => 'resp_date']);
                    !!}
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    {!! Form::label('follow_up_date', __( 'leads::lang.follow_up_date' )) !!}
                    {!! Form::text('follow_up_date', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                    'leads::lang.follow_up_date' ),
                    'id' => 'resp_follow_up_date']);
                    !!}
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    {!! Form::label('response', __( 'leads::lang.client_resp' )) !!}
                    {!! Form::textarea('response', null, ['class' => 'form-control', 'placeholder' => __(
                    'leads::lang.client_resp' ), 'required',
                    'id' => 'resp_text']);
                    !!}
                </div>
            </div>
            
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save_resp_btn" >@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script>
$('#resp_follow_up_date').datepicker({
    format: 'mm/dd/yyyy'
});
$('#resp_date').datepicker({
    format: 'mm/dd/yyyy'
});
</script>