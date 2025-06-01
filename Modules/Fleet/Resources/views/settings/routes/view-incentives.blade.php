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
      <h4 class="modal-title">@lang( 'fleet::lang.incentives' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="form-group col-sm-6">
                <h4 class="text-danger">@lang('fleet::lang.main_driver_incentive'):  <strong>{{@num_format($route->driver_incentive)}}</strong></h4>
            </div>
            <div class="form-group col-sm-6">
                <h4 class="text-danger">@lang('fleet::lang.main_helper_incentive'):  <strong>{{@num_format($route->helper_incentive)}}</strong></h4>
            </div>
        </div>
      <div class="row">
        
        
        <div class="form-group col-sm-12">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="view_incentive_table" style="width: 100%;">
            <thead>
              <tr>
    <th style="width: 120px;">@lang('fleet::lang.added_date')</th>
    <th style="width: 200px;">@lang('fleet::lang.incentive_name')</th>
    <th>@lang('fleet::lang.incentive_type_th')</th>
    <th>@lang('fleet::lang.applicable_to')</th>
    <th style="width: 150px;">@lang('fleet::lang.fixed_amount')</th>
    <th>@lang('fleet::lang.percentage')</th>
    <th style="width: 120px;">@lang('fleet::lang.based_on')</th>
    <th style="width: 150px;">@lang('fleet::lang.percentage_amount')</th>
    <th style="width: 150px;">@lang('fleet::lang.incentive_amount')</th>
    <th>@lang('fleet::lang.user')</th>   
  </tr>
            </thead>
            <tbody>
              @foreach($incentives as $incentive)
              <tr>
              <td>{{date('Y-m-d',strtotime($incentive->created_at))}}</td>
              <td>{{$incentive->incentive_name}}</td>
              <td>{{ucfirst($incentive->incentive_type)}}</td>
              <td>{{ucfirst($incentive->applicable_to)}}</td>
              <td>{{@num_format($incentive->amount)}}</td>
              
              <td>{{($incentive->percentage)?@num_format($incentive->percentage):"0.00"}}%</td>
              <td>{{($incentive->based_on=='company_decision')?'Company Decision':'Trip Amount'}}</td>
              <td>{{@num_format($incentive->percentage_amount)}}</td>
              <td>{{@num_format($incentive->percentage_amount + $incentive->amount)}}</td>
              <td>{{$incentive->username}}</td>
  
              </tr>
              @endforeach
          </tbody>
          <tfoot>
        <tr>
            <!-- Empty cells for columns before 'incentive_amount' -->
            <td colspan="8" style="text-align: right;color:red;">
                @lang('fleet::lang.total_incentives')
            </td>
            <!-- Total incentive amount -->
            <td style="color: red;">
                <!-- Total incentive amount value -->
                {{ @num_format($incentiveTotalAmount) }} <!-- Replace $totalIncentiveAmount with your actual variable holding the total incentive amount -->
            </td>
            <!-- Empty cell for 'user' column -->
            <td></td>
        </tr>
    </tfoot>
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