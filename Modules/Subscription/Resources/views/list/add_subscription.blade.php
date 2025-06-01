<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    
    <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'subscription::lang.add_subscription' )</h4>
    </div>

    <div class="modal-body">
        {!! Form::open(['url' =>
        action('\Modules\Subscription\Http\Controllers\SubscriptionSettingController@save_subscription', $id), 'method' =>
        'post', 'id' => 'save_subscription_form' ]) !!}
          <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('transaction_date', __( 'subscription::lang.date' )) !!} 
                  {!! Form::date('transaction_date', date('Y-m-d'), ['class' => 'form-control transaction_date',
                  'required', 'readonly', 'placeholder' => __( 'subscription::lang.date' )]); !!}
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('current_amount', __( 'subscription::lang.current_amount' )) !!} 
                  {!! Form::text('current_amount',$current_amount, ['class' => 'form-control', 'required','readonly', 'placeholder' => __(
                  'subscription::lang.current_amount' ), 'required']); !!}
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('new_amount', __( 'subscription::lang.new_amount' )) !!} 
                  {!! Form::text('new_amount',null, ['class' => 'form-control', 'required', 'placeholder' => __(
                  'subscription::lang.new_amount' ), 'required']); !!}
                </div>
              </div>
              
              <div class="col-md-3">
                  <div class="form-group">
                      <br>
                      <button type="submit" class="btn btn-primary" id="save_leads_btn">
                        @lang( 'messages.save' )
                      </button>
                  </div>
              </div>
              
          </div>
      {!! Form::close() !!}
      <hr>
      <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="subscription_prices_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'subscription::lang.date' )</th>
                                <th>@lang( 'subscription::lang.current_amount' )</th>
                                <th>@lang( 'subscription::lang.new_amount' )</th>
                                <th>@lang( 'subscription::lang.created_by' )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            
    </div>
    
    <div class="modal-footer">
        
      <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang( 'messages.close' )
      </button>
    </div>
    
    
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->

<script>
    $(".select2").select2();
    
    $(document).ready(function(){
    
        subscription_prices_table = $('#subscription_prices_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Subscription\Http\Controllers\SubscriptionSettingController@fetch_subscription',$id)}}",
                    data: function(d){
                        
                    }
                },
                columnDefs:[{
                        "targets": 0,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'current_amount', name: 'current_amount'},
                    {data: 'new_amount', name: 'new_amount'},
                    {data: 'user', name: 'users.username'}
                  
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#subscription_prices_table'));
                }
            });
        });
        
    
</script>

