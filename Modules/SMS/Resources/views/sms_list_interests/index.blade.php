<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'sms::lang.interest' )</h4>
    </div>

    <div class="modal-body">
        {!! Form::open(['url' => action('\Modules\SMS\Http\Controllers\SmsListInterestController@store'), 'method' =>
            'post', 'id' => 'sms_list_interest_form', 'enctype' => 'multipart/form-data' ])
            !!}
        <div class="row">
            <input type="hidden" name="business_id" value="{{request()->business_id}}">
            <input type="hidden" name="type" value="{{request()->type}}">
            <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('form_no', __( 'sms::lang.form_no' )) !!}
                  {!! Form::text('form_no', $form_no, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
                  'sms::lang.form_no' ),
                  'id' => 'form_no']);
                  !!}
                </div>
            </div>
            
            
            <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('date', __( 'sms::lang.sms' )) !!}
                  {!! Form::date('date', date('Y-m-d'), ['class' => 'form-control', 'required', 'placeholder' => __(
                  'sms::lang.date' ),
                  'id' => 'date']);
                  !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('amount', __( 'sms::lang.amount' )) !!}
                  {!! Form::text('amount', null, ['class' => 'form-control sms_list_fields', 'required', 'placeholder' => __(
                  'sms::lang.amount' ),
                  'id' => 'amount']);
                  !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                  <label for="note"> @lang( 'sms::lang.note' )</label>
                  {!! Form::textarea('note', null, ['class' => 'form-control sms_list_fields', 'placeholder' => __(
                  'sms::lang.note' ), 'style' => 'width: 100%', 'rows' => 3,
                  'id' => 'note']);
                  !!}
                </div>
            </div>
            
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.save' )</button>
            </div>
      
        </div>
        
        {!! Form::close() !!}
        
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="sms_list_interests_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>@lang( 'sms::lang.date' )</th>
                                        <th>@lang( 'sms::lang.amount' )</th>
                                        <th>@lang( 'sms::lang.note' )</th>
                                        <th>@lang( 'sms::lang.user_created' )</th>
                                    </tr>
                                </thead>
                                <tbody>
    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->



<script>
    
    sms_list_interests_table = $('#sms_list_interests_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url : "{!! action('\Modules\SMS\Http\Controllers\SmsListInterestController@listInterests', ['business_id' => request()->business_id,'type' => request()->type]) !!}",
            data: function(d){
                
            }
        },
        columnDefs:[{
                "targets": 1,
                "orderable": false,
                "searchable": false
            }],
        columns: [
            {data: 'date', name: 'date'},
            {data: 'amount', name: 'amount'},
            {data: 'note', name: 'note'},
            {data: 'user_added', name: 'user_added'},
        ]
    });
    

</script>