
<style>
    .notes-column {
        word-wrap: break-word;
        max-width: 100px !important; 
    }
</style>
<div class="modal-dialog" role="document" style="width: 75%;">
    <div class="modal-content">
        <div class="modal-header d-flex">
            <div style="width: 50%; justify-content:space-between" class="d-flex">
                <h4 class="modal-title">@lang( 'fleet::lang.opening_balance' )</h4>    
                <h2 class="text-danger" style="text-transform: capitalize;">{{$vehicle_number}}</h2>    
            </div>
            <div style="width: 50%">
                <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>    
            </div>
            

        </div>
        <div class="modal-body">
            <div class="text-center row card" style="background-color: #ffe600 !important; color: red;">
                <div class="col-sm-4">Opening balance:</b>&nbsp; {{number_format($opening_balance), 2}} </div>
                <div class="col-sm-4">Added: </b>&nbsp; {{number_format($total_received), 2}} </div>
                <div class="col-sm-4">Balance to Add:</b> &nbsp;{{number_format($to_be_added), 2}} </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped ajax_view" id="product_table">
                    <thead>
                        <tr>
                            <th class="notexport">Customer</th>
                            <th class="notexport">Opening Amount</th>
                            <th class="notexport">Invoice Date</th>
                            <th class="notexport">Invoice No</th>
                            <th class="notexport notes-column">Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fleets as $data)
                        
                        <tr>
                            <td>{{$data->contact->name}}</td>
                            <td>{{$data->opening_amount}}</td>
                            <td>{{\Carbon\Carbon::parse($data->invoice_date)->format('Y-m-d') }}</td>
                            <td>{{$data->invoice_no}}</td>
                            <td class="notes-column">{{$data->notes}}</td>
                            <td>
                                @can('edit.fleet_opening_balance')
                                    <a href="#" data-href="{{action('\Modules\Fleet\Http\Controllers\FleetController@editOpeningBalance', [$data->id]) }}" class="btn-modal" data-container=".fleet_model" style="pointer-events: none"><i class="glyphicon glyphicon-edit"></i>  {{__("messages.edit")}} </a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="clearfix"></div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->