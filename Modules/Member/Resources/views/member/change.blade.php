<div class="modal-dialog" role="document" style="width: 45%;">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\MemberController@member_update',
    $member->id), 'method' => 'PUT', 'id' => 'member_form' ])
    !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'member::lang.change_member_group' ) ({{$member->username}})</h4>
    </div>

    <div class="modal-body">
      <div class="box box-widget">
      
      <div class="box-body">
        {!! Form::hidden('username', $member->username,['id'=>'username']); !!}
        
               <div class="col-md-12">
          <div class="col-md-6">
              <div class="form-group">
                    {!! Form::label('member_group', __('member::lang.transfer_from_member_group') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('member_group', $member_groups, $member_group,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required', 'disabled=>true']); !!}
                    </div>
                </div>
          </div>
          <div class="col-md-6">
             <div class="form-group">
                    {!! Form::label('member_group', __('member::lang.transfer_to_member_group') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('transferto_member_group', $member_groups, null,
                        ['class' => 'form-control select2','placeholder' => __('lang_v1.all'), 'style' => 'margin:0px',
                        'required']); !!}
                    </div>
                </div>
    
          </div>
        </div>
        <div class="col-md-12">
        
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('date_of_birth_filter', __('member::lang.date') . ':') !!}
                    {!! Form::text('date_of_birth_filters',date('m/d/Y'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'date_of_birth_filters']); !!}
                </div>
            </div>
              <div class="col-md-6">
          <div class="form-group">
                    {!! Form::label('member_group', __('member::lang.add_by') . ':') !!}
                    
                    {!! Form::text('opening_meter_user', auth()->user()->username, ['placeholder' => __('lang_v1.user'), 'class' =>
                    'form-control', 'id' => 'opening_meter_user', 'readonly']); !!}
                </div>
          </div>
            </div>
            
            
            <!-- Transfer history table -->
                    <div class="col-md-12">
                        <h4>@lang('member::lang.transfer_history')</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('member::lang.date_time')</th>
                                    <th>@lang('member::lang.transferred_from')</th>
                                    <th>@lang('member::lang.transferred_to')</th>
                                    <th>@lang('member::lang.transferred_by')</th>
                                </tr>
                            </thead>
                           <tbody>
            @forelse ($transfer_histories as $history)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($history->transferred_at)->format('m/d/Y H:i:s') }}</td>
                    <td>{{ optional($history->transferredFromGroup)->member_group ?? __('lang_v1.none') }}</td>
                    <td>{{ optional($history->transferredToGroup)->member_group ?? __('lang_v1.none') }}</td>
                    <td>{{ optional($history->transferredBy)->username ?? __('lang_v1.unknown') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">@lang('member::lang.no_transfer_history_found')</td>
                </tr>
            @endforelse
        </tbody>
                        </table>
                    </div>
        
        
    </div>
  <!-- /.box-body -->
</div>





<div class="clearfix"></div>

    </div>

    <div class="clearfix"></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    
      <button type="submit" class="btn btn-primary" id="save_member_btn">@lang( 'member::lang.update'
        )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 if ($('#date_of_birth_filters').length == 1) {
    $('#date_of_birth_filters').datepicker({
        format: 'mm/dd/yyyy', // Adjust the format as needed
        autoclose: true,
        todayHighlight: true,
    }).datepicker('setDate', moment('{{ date('m/d/Y', strtotime("-80 year")) }}').startOf('year')); // Set default date to 80 years ago

    $('#date_of_birth_filters').on('changeDate', function(e) {
        // Handle the date change event
        // For example, reload your table or perform other actions
        // member_table.ajax.reload();
    });
}

$('.date-field').autotab('number');

$('#electrorate_select').select2({
    width: '100%'
});
</script>