

<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.balance_details')</h4>
    </div>

    <div class="modal-body">
        <table class="table table-bordered table-striped" style="width: 100%" id="contact_table">
                <tr>
                    <td>@lang('contact.total_sale')</td>
                    <td>{{@num_format($balance_details['total_sale'])}}</td>
                </tr>
                <tr>
                    <td>@lang('contact.opening_balance')</td>
                    <td>{{@num_format($balance_details['opening_balance'])}}</td>
                </tr>
                <tr>
                    <td>@lang('contact.total_paid')</td>
                    <td>{{@num_format($balance_details['total_paid'])}}</td>
                </tr>
                <tr class="text-danger">
                    <th>@lang('contact.balance_due')</th>
                    <th>{{@num_format($balance_details['total_balance'])}}</th>
                </tr>
                
        </table>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>


  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
