<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <div class="box box-primary" style="font-size: 12px !important">
        
        <div class="modal-header">
            <button type="button" class="close closing_contact_modal" aria-label="Close" onclick="closeScanCodeModal()"><span
            aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('messages.barandqrcode')</h4>
        </div>
        <div class="box-body">
            <div class="table-responsive">
            <div id="bar_qr_code_table_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                <div class="row margin-bottom-20 text-center">
                <div class="col-sm-2"></div>
                <div class="col-sm-7">
                    <div class="dt-buttons btn-group"></div>
                </div>
                <div class="col-sm-3"></div>
                <div id="bar_qr_code_table_processing" class="dataTables_processing panel panel-default" style="display: none;">Processing...</div>
                </div>
                <table class="table table-bordered table-striped dataTable no-footer" id="bar_qr_code_table" style="width: 100%;" role="grid">
                <thead>
                    <tr role="row">
                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 0px;">Details to Show</th>
                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 0px;">In Bar Code</th>
                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 0px;">In QR Code</th>
                    </tr>
                </thead>
                    <tbody>
                        @foreach($types as $data)
                            <tr>
                                <td>{{ $data['details'] }}</td>
                                <td>
                                    @if($data['bar_code'] == 1)
                                        <input type='checkbox' checked class="bar_code_{{$data['id'] }}" onchange="barcodeChanged('{{$data['details'] }}',{{ $id }},this.checked)"/>
                                    @elseif($data['bar_code'] == '')
                                        
                                    @else
                                        <input type='checkbox' class="bar_code_{{$data['id'] }}" onchange="barcodeChanged('{{$data['details'] }}',{{ $id }},this.checked)"/>
                                    @endif
                                </td>
                                <td>
                                    @if($data['qr_code'] == 1)
                                        <input type='checkbox' checked class="qr_code_{{$data['id'] }}" onchange="qrcodeChanged('{{$data['details'] }}',{{ $id }},this.checked)" />
                                    @elseif($data['qr_code'] == '')
                                        
                                    @else
                                        <input type='checkbox' class="qr_code_{{$data['id'] }}"  onchange="qrcodeChanged('{{$data['details'] }}',{{ $id }},this.checked)"  />
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr><td colspan="3"><b>User: </b>{{ $data['username'] }}</td></tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default closing_scancode_modal" onclick="closeScanCodeModal()">@lang( 'messages.close' )</button>
        </div>
        <!-- /.box-body -->
        </div>
    </div>
</div>
