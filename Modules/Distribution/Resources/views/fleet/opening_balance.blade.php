<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'distribution::lang.opening_balance' )</h4>
        </div>
        <div class="modal-body">
            
            <div class="table-responsive">
    <table class="table table-bordered table-striped ajax_view" id="product_table">
        <thead>
            <tr>
                <th class="notexport">Customer</th>
                <th class="notexport">Opening Amount</th>
                <th class="notexport">Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fleets as $data)
            <tr>
                <td>{{$data->contact->name}}</td>
                <td>{{$data->opening_amount}}</td>
                <td>{{$data->notes}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
        </div>
        <div class="clearfix"></div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->