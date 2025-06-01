<style>
  .justify-content-between{
    justify-content:space-between!important;
  }
  .main-modal {
  height:675px;
  overflow:auto;
}
  </style>
<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">


    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.incentives' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
       
        
        
        <div class="form-group col-sm-12">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="view_incentive_table" style="width: 100%;">
            <thead>
              <tr>
                <th>@lang('shipping::lang.added_date')</th>
                <th>@lang('shipping::lang.incentive_name')</th>
                <th>@lang('shipping::lang.incentive_type_th')</th>
                <th>@lang('shipping::lang.applicable_to')</th>
                <th>@lang('shipping::lang.fixed_amount')</th>
                <th>@lang('shipping::lang.percentage_amount')</th>
                <th>@lang('shipping::lang.based_on')</th>
                <th>@lang('shipping::lang.amount')</th>
                <th>@lang('shipping::lang.user')</th>
                
              </tr>
            </thead>
            <tbody>
              @foreach($incentives as $incentive)
              <tr>
              <td>{{date('Y-m-d',strtotime($incentive->created_at))}}</td>
              <td>{{$incentive->incentive_name}}</td>
              <td>{{ucfirst($incentive->incentive_type)}}</td>
              <td>{{ucfirst($incentive->applicable_to)}}</td>
              @if($incentive->incentive_type=='fixed')
              <td>{{$incentive->amount}}</td>
              @else
              <td>--</td>
              @endif
  <td>{{($incentive->percentage)?$incentive->percentage:"--"}}</td>
  <td>{{($incentive->based_on=='company_decision')?'Company Decision':'Trip Amount'}}</td>
  @if($incentive->incentive_type=='percentage')
              <td>{{$incentive->amount}}</td>
              @else
              <td>--</td>
              @endif
              <td>{{$incentive->username}}</td>
  
              </tr>
              @endforeach
          </tbody>
          </table>
        </div>
          </div>
        

      </div>

    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>


  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
 $('#view_incentive_table').dataTable();

</script>