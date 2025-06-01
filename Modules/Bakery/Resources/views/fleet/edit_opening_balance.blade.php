
<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'fleet::lang.opening_balance' )</h4>
        </div>
        <div class="modal-body">
             {!! Form::open(['url' =>   action('\Modules\Fleet\Http\Controllers\FleetController@updateOpeningBalance'), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
            
                <input type="hidden" name="id" value="{{$fleets->id}}">
                <input type="hidden" name="transaction_id" value="{{$transactions->id}}">
                
                <div class="col-md-3">
                  <div class="form-group">
                    {!! Form::label('contact_id', __( 'fleet::lang.customer' )) !!} {!!
                    Form::select('contact_id', $customers, $fleets->contact_id, ['class' =>
                    'form-control select2 first_customer', 'required', 'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'customer']); !!}
                  </div>
                </div>
                
                <div class="col-md-3">
                  <div class="form-group">
                    {!! Form::label('opening_amount', __( 'fleet::lang.opening_amount'
                    )) !!} {!! Form::text('opening_amount', $fleets->opening_amount, ['class' =>
                    'form-control', 'placeholder' => __( 'fleet::lang.opening_amount' ),'required']); !!}
                  </div>
                </div>
                
                <div class="col-md-3">
                  <div class="form-group">
                    {!! Form::label('invoice_no', __( 'fleet::lang.invoice_no'
                    )) !!} {!! Form::text('invoice_no', $fleets->invoice_no, ['class' =>
                    'form-control', 'placeholder' => __( 'fleet::lang.invoice_no' ),'required']); !!}
                  </div>
                </div>
                
                <div class="col-md-3">
                  <div class="form-group">
                    {!! Form::label('invoice_date', __( 'fleet::lang.date'
                    )) !!} {!! Form::date('invoice_date', date('Y-m-d',strtotime($fleets->invoice_date)), ['class' =>
                    'form-control first_date', 'placeholder' => __( 'fleet::lang.date' ),'required']); !!}
                  </div>
                </div>
                
                <div class="col-md-3">
                  <div class="form-group">
                    {!! Form::label('notes', __( 'fleet::lang.notes' )) !!} {!!
                    Form::textarea('notes', $fleets->notes, ['class' => 'form-control',
                    'rows' => 3, 'placeholder' => __( 'fleet::lang.notes' )]); !!}
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    @lang( 'messages.save' )
                 </button>
                 
                {!! Form::close() !!}
        </div>
        <div class="clearfix"></div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->