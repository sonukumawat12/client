@extends('layouts.app')
@section('title', __('shipping::lang.shipment'))

<style>
    .select2 {
        width: 100% !important;
    }
    .delivery_status{
        display:none;
    }
</style>
@section('content')
<!-- Main content -->
<section class="content">
  
  @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.shipment')])
  
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="shipment_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('shipping::lang.added_date')</th>
          <th>@lang('shipping::lang.tracking_no')</th>
          <th>@lang('shipping::lang.shipper_tracking_no')</th>
          <th>@lang('shipping::lang.agent')</th>
          <th>@lang('shipping::lang.sender')</th>
          <th>@lang('shipping::lang.recipient')</th>
          <th>@lang('shipping::lang.shipping_mode')</th>
          <th>@lang('shipping::lang.package_type')</th>
          <th>@lang('shipping::lang.schedule')</th>
          <th>@lang('shipping::lang.delivery_date')</th>
          <th>@lang('shipping::lang.shipping_partner')</th>
          <th>@lang('shipping::lang.delivery_status')</th>
          <th>@lang('shipping::lang.total')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\ShippingController@index'), 'method' => 'post']) !!}

            <div class="modal-header">
            <button type="button" class="close closing_contact_modal" aria-label="Close" onclick="closeShipperTrackingModal()"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('shipping::lang.shipper_tracking_no')</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="shipment_id_hidden"  value="" />
                    <div class="col-md-12 ">
                        <div class="form-group">
                            {!! Form::label('shipper_tracking_no', __('shipping::lang.shipper_tracking_no') . ':') !!}
                            <div class="input-group">
                            
                            {!! Form::text('shipper_tracking_no', 0, ['class' => 'form-control shipper_tracking_no_input']); !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default closing_contact_modal" onclick="closeShipperTrackingModal()">@lang( 'messages.close' )</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>



<div class="modal fade add_agent_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\ShippingController@addAgent'), 'method' => 'post', 'id' => 'add_agent_form']) !!}

            <div class="modal-header">
            <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('shipping::lang.add_agent')</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="shipment_id" id="shipment_id"  value="" required/>
                    <div class="form-group col-sm-12">
                          {!! Form::label('agent_no', __( 'shipping::lang.shipping_agents' ) .":") !!}
                          {!! Form::select('agent_no', $shipping_agents, null, ['class' => 'form-control select2','required', 'placeholder' =>
                          __('messages.please_select')]); !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<div class="modal fade add_partner_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\ShippingController@addPartner'), 'method' => 'post', 'id' => 'add_partner_form']) !!}

            <div class="modal-header">
            <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('shipping::lang.add_partner')</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="shipment_id" id="partner_shipment_id"  value="" required/>
                    <div class="form-group col-sm-12">
                          {!! Form::label('shipping_partner', __( 'shipping::lang.shipping_partner' ) .":") !!}
                          {!! Form::select('shipping_partner', $shipping_partners, null, ['class' => 'form-control select2','required', 'placeholder' =>
                          __('messages.please_select')]); !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<div class="modal fade invoice_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
            <button type="button" class="close closing_contact_modal" aria-label="Close" onclick="closeSendInvoiceModal()"><span
                aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('messages.send_invoice')</h4>
            </div>
            <input type="hidden" name="invoice_id_hidden"  value="" />
            <div class="modal-body">                
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary " onclick="whatsapp_form()"><i class="fa fa-whatsapp" aria-hidden="true" style="margin-right:5px;"></i>@lang('shipping::lang.send_by_whatsapp' )</button>
                        </div>                    
                  
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary " onclick="email_form()"><i class="fa fa-share" aria-hidden="true" style="margin-right:5px;"></i>@lang('shipping::lang.send_by_email' )</button>
                        </div>
                    </div>                   
                </div>

                <hr />
                {!! Form::open(['class' => 'form-inline']) !!}
                <div class="row whatsapp-form" style="display:none;margin-top:15px;">
                    <div class="col-md-12 ">
                        <div class="form-group">
                            {!! Form::label('whatsapp_number', __('shipping::lang.enter_whatsapp_number').": " ) !!}
                            <div class="input-group">
                            
                            {!! Form::text('whatsapp_number', '', ['class' => 'form-control whatsapp_number']); !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary whatsapp_send " >@lang( 'messages.send' )</button>
                        </div>
                    </div>
                </div>
                <div class="row email-form" style="display:none;margin-top:15px;">
                    <div class="col-md-12 ">
                        <div class="form-group emailadd">
                            {!! Form::label('email_id', __('shipping::lang.enter_email').": " ) !!}
                            <div class="input-group">
                            
                            {!! Form::email('email_id[]', '&nbsp;', ['class' => 'form-control email_id']); !!}
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" id="addItem"> + </button>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary email_send" >@lang( 'messages.send' )</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <hr />
                <div class="table-responsive send_invoice_table" style="height:50%">
                    <table class="table table-bordered table-striped" id="price_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="notexport">@lang('messages.datetime')</th>
                                <th>@lang('shipping::lang.whatsapp_number')</th>
                                <th>@lang('shipping::lang.email_ids')</th>
                                <th>@lang('shipping::lang.sent_by')</th>
                            </tr>
                        </thead>    
                        <tbody></tbody>                   
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default closing_contact_modal" onclick="closeSendInvoiceModal()">@lang( 'messages.close' )</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade changestatus_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
            <button type="button" class="close closing_contact_modal" aria-label="Close" onclick="closeChangeStatusModal()"><span
                aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('messages.send_invoice')</h4>
            </div>
            <input type="hidden" name="changestatus_id_hidden"  value="" />
            <input type="hidden" name="changestatus_status_hidden"  value="" />
            <input type="hidden" name="changestatus_delivery_date_hidden"  value="" />
            <div class="modal-body">    
                    <div class="row">
                        <div class="form-group col-sm-4">
                            {!! Form::label('datetime', __('messages.datetime') .":") !!}
                            <input type="text" class="form-control" name="datetime" required value="{{ now()->format('m/d/Y H:i:s') }}" readonly>
                        </div>
                        <div class="form-group col-sm-4">
                            {!! Form::label('user_changed', __( 'shipping::lang.user_changed' ) .":") !!}
                            <input type="text" class="form-control" name="user_changed" required value="{{ auth()->user()->username }}" readonly>
                        </div>
                        
                        
                        
                    </div>
                    <div class="row row0">
                        <div class="form-group col-sm-4">
                            {!! Form::label('type_of_change', __( 'shipping::lang.type_of_change' ) .":") !!}
                            {!! Form::select('type_of_change', ['Scheduled for Delivery','Delivery Date','Status of Delivery'], null, [
                                'id' => 'type_of_change',
                                'class' => 'form-control select2',
                                'placeholder' => __('messages.please_select'),
                            ]); !!}
                        </div> 
                        <input type="hidden" value="0" id="change_row" /> 
                                            
                        <div class="form-group col-sm-1">
                            <br/>
                            <button type="button" class="btn btn-success" id="addTypeChange"> + </button>  
                        </div>  
                        <div class="form-group col-sm-7 type_of_row_0"></div>
                        
                    </div>
                    <div class="row row_1"></div>
                    <div class="row row_2"></div>
                <hr />
                <div class="table-responsive change_status_table" style="height:50%">
                    <table class="table table-bordered table-striped" id="price_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="notexport">@lang('messages.datetime')</th>
                                <th>@lang('shipping::lang.type_of_change')</th>
                                <th>@lang('shipping::lang.delivery_time')</th>
                                <th>@lang('shipping::lang.previous_status')</th>
                                <th>@lang('shipping::lang.new_status')</th>
                                <th>@lang('shipping::lang.changed_by')</th>
                            </tr>
                        </thead>    
                        <tbody></tbody>                   
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary closing_contact_modal save_change_status" onclick="saveChangeStatusModal()">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default closing_contact_modal" onclick="closeChangeStatusModal()">@lang( 'messages.close' )</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade scancode_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade barqrcode_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="">
            <input type="hidden" name="invoice_id_hidden"  value="" />
            <div class="modal-body"></div>
        </div>
    </div>
</div>


 <div class="modal fade" id="noteModal" role="dialog" 
      aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog">
          <div class="modal-content">
    
            <!-- Modal Header -->
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
    
            <!-- Modal Body -->
            <div class="modal-body">
              <p id="noteContent" class="text-center"></p>
            </div>
    
          </div>
        </div>
      </div>

</section>
<!-- /.content -->
@endsection


@section('javascript')
<script>

    $(document).on('click', '.note_btn', function(e){
      let note = $(this).data('string');
      // Replace newline characters with <br>
      note = note.replace(/\n/g, '<br>');
      $("#noteContent").html(note);
      $("#noteModal").modal('show');
    }); 


    $(document).on('submit', "#add_agent_form", function(e) {
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
                shipment_table.ajax.reload();

                $('.modal').modal('hide');

            },
            error: function(xhr, status, error) {
               
            }
        });

    });
    
    $(document).on('submit', "#add_partner_form", function(e) {
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
                shipment_table.ajax.reload();

                $('.modal').modal('hide');

            },
            error: function(xhr, status, error) {
               
            }
        });

    });






    @if($flag_scan==1)
        window.onload = function() {
            openChangeStatusModal({{ $id }},'{{ $delivery_scan }}','{{ $delivery_time }}');
        };
    @endif
    // Function to print the barcode three times
    function printBarcode(shipment_id,detail_id) {
        var width       = $('.width_size').val();
        var height      = $('.height_size').val();
        var print_time  = $('.print_time').val();
        if(print_time == '' || print_time <=0){
            print_time =1;
        }
        $.ajax({
            method: 'POST',
            url: '{{ action('\Modules\Shipping\Http\Controllers\BarQrCodeController@createShipmentCode') }}',
            data: { shipment_id : shipment_id,detail_id : detail_id,width : width, height : height, type : 'bar'},
            success: function(result) {
                $('.barqrcode_modal').modal('hide');   
                var barcodeHtml = result;
        
                var printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Print Barcode</title></head><body>');

                // Print the barcode three times
                for (var i = 0; i < print_time; i++) {
                    printWindow.document.write(barcodeHtml);
                    printWindow.document.write("<BR><BR>");
                }

                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();                 
                
            },
            error: function(result) {
                alert("Some Error Occured. Please try again");
            }
        });        
    }

    function printQrcode(shipment_id,detail_id) {
        var width       = $('.qr_width_size').val();
        var height      = $('.qr_height_size').val();
        var print_time  = $('.qr_print_time').val();
        if(print_time == '' || print_time <=0){
            print_time =1;
        }
        $.ajax({
            method: 'POST',
            url: '{{ action('\Modules\Shipping\Http\Controllers\BarQrCodeController@createShipmentCode') }}',
            data: { shipment_id : shipment_id,detail_id : detail_id,width : width, height : height, type : 'qr'},
            success: function(result) {
                $('.barqrcode_modal').modal('hide');    
                var barcodeHtml = result;
        
                var printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Print Qrcode</title></head><body>');

                // Print the barcode three times
                for (var i = 0; i < print_time; i++) {
                    printWindow.document.write(barcodeHtml);
                    printWindow.document.write("<BR><BR>");
                }

                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
                
            },
            error: function(result) {
                alert("Some Error Occured. Please try again");
            }
        });        
    }
    
    
    
    function whatsapp_form(){
        $('.whatsapp-form').show();
        $('.email-form').hide();
    }
    function email_form(){
        $('.whatsapp-form').hide();
        $('.email-form').show();
    }

   
    function closeScanCodeModal(){       
        $('.scancode_modal').modal('hide');            
    }

    function closeBarQrCodeModal(){       
        $('.barqrcode_modal').modal('hide');            
    }

    function qrcodeChanged(detail_id,shipment_id,checkbox) {
        if (checkbox) {
            $.ajax({
                method: 'POST',
                url: '{{ action('\Modules\Shipping\Http\Controllers\BarQrCodeController@createShipmentCode') }}',
                data: { shipment_id : shipment_id,detail_id : detail_id,width : 3, height : 3, type : 'showqr'},
                success: function(result) {
                    $(".barqrcode_modal .modal-body").html(result);
                    $(".barqrcode_modal").modal('show');
                    
                },
                error: function(result) {
                    alert("Some Error Occured. Please try again");
                }
            });
        } else {
            console.log("Checkbox is not checked");
            // Do something when checkbox is not checked
        }
    }

    function barcodeChanged(detail_id,shipment_id,checkbox) {
        if (checkbox) {
            $.ajax({
                method: 'POST',
                url: '{{ action('\Modules\Shipping\Http\Controllers\BarQrCodeController@createShipmentCode') }}',
                data: { shipment_id : shipment_id,detail_id : detail_id,width : 2, height : 35, type : 'showbar'},
                success: function(result) {
                    $(".barqrcode_modal .modal-body").html(result);
                    $(".barqrcode_modal").modal('show');
                    
                },
                error: function(result) {
                    alert("Some Error Occured. Please try again");
                }
            });
        } else {
            console.log("Checkbox is not checked");
            // Do something when checkbox is not checked
        }
    }

    function openShipperTrackingModal(trackingno,shipment_id_hidden){
        $('.contact_modal').modal('show');
        $(".shipper_tracking_no_input").val(trackingno);
        $('input[name="shipment_id_hidden"]').val(shipment_id_hidden);
            
    }
    function closeShipperTrackingModal(){       
        $('.contact_modal').modal('hide');            
    }
    function openChangeStatusModal(shipment_id,status,delivery_date){
        $('input[name="changestatus_status_hidden"]').val(status);
        $('input[name="changestatus_delivery_date_hidden"]').val(delivery_date);
        $('input[name="changestatus_id_hidden"]').val(shipment_id);
        //var fetch_data=1;var whatsapp_number='';
        $.ajax({
                method: 'POST',
                url: '{{ action('\Modules\Shipping\Http\Controllers\ShippingController@getShippingChangeDetails') }}',
                dataType: 'json',
                data: {shipment_id},
                success: function(result) {
                    //4b task done updated by dushyant
                    var shipping_change_status = result.shipping_change_status;
                    var row='';
                    shipping_change_status.forEach(function(item){
                        if(item.formatted_delivery_time === null){
                            item.formatted_delivery_time ='';
                        }
                        if(item.shipping_status === null){
                            item.shipping_status ='';
                        }
                        if(item.prev_shipping_status === null){
                            item.prev_shipping_status ='';
                        }
                        let replacedString ='';
                        replacedString = item.type_change.replace('0', 'Scheduled for Delivery');
                        // Replace '1' with 'Delivery Date'
                        replacedString = replacedString.replace('1', 'Delivery Date');
                        // Replace '2' with 'Status of Delivery'
                        replacedString = replacedString.replace('2', 'Status of Delivery');
                        row += '<tr>' +
                                    '<td>' + item.formatted_created_at + '</td>' +
                                    '<td>' + replacedString + '</td>' +
                                    '<td>' + item.formatted_delivery_time + '</td>' +
                                    '<td>' + item.prev_shipping_status + '</td>' +
                                    '<td>' + item.shipping_status + '</td>' +
                                    '<td>' + item.username + '</td>' +
                                '</tr>';
                                console.log(row);
                    });
                    $('.change_status_table tbody').html(row);
                },
        });
        $('.changestatus_modal').modal('show');
    }
    function closeChangeStatusModal(){
        $('.changestatus_modal').modal('hide'); 
        $("#change_row").val(0);
        $('#type_of_change').prop('disabled', false);
        $('#type_of_change').val('').trigger('change');;
        $('.type_of_row_0,.row_1,.row_2').html('');
        $(".save_change_status").text("Save");
    }
    function openSendInvoiceModal(shipment_id){
        $('input[name="invoice_id_hidden"]').val(shipment_id);
        var fetch_data=1;var whatsapp_number='';
        $.ajax({
                method: 'POST',
                url: '{{ action('\Modules\Shipping\Http\Controllers\ShippingController@sendWhatsappInvoice') }}',
                dataType: 'json',
                data: {whatsapp_number,shipment_id,fetch_data},
                success: function(result) {
                    //4b task done updated by dushyant
                    var shippingInvoiceSend = result.shippingInvoiceSend;
                    console.log(shippingInvoiceSend);
                    var row='';
                    shippingInvoiceSend.forEach(function(item){
                        row += '<tr>' +
                                    '<td>' + item.formatted_created_at + '</td>' +
                                    '<td>' + item.whatsapp_number + '</td>' +
                                    '<td>' + item.email_id + '</td>' +
                                    '<td>' + item.username + '</td>' +
                                '</tr>';
                                console.log(row);
                    });
                    $('.send_invoice_table tbody').html(row);
                },
        });
        $('.invoice_modal').modal('show');
    }
    function closeSendInvoiceModal(){
        $('.send_invoice_table tbody').html();
        $("#whatsapp_number").val('');
        var packageArray = [];
        packageArray.push('<input type="email" name="email_id[]" class="form-control email_id">');

        // Create a new row with the values
        var newRow = '<label for="email_id">Enter Email: </label><div class="input-group">'+packageArray.join('') +'</div>';

        // Append the new row to the table
        $('.emailadd').html(newRow);
        $('.invoice_modal').modal('hide');            
    }
    function whatsappPdf(file){
        var whatsappUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(file);
        window.open(whatsappUrl, '_blank');
    }
    //driver tab script
        if ($('#recipient_date_range_filter').length == 1) {
            $('#recipient_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#recipient_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                shipment_table.ajax.reload();
            });
            $('#recipient_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#recipient_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#recipient_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }

        function schedule_of_delivery(){
            return '<div class="form-group col-sm-8"><label>Schedule for Delivery: </label><select name="shipping_delivery" required  id="shipping_delivery"  class= "shipping_delivery form-control select2" ><option value=""> Please Select </option>@foreach($shipping_delivery as $id => $delivery) <option value="{{ $id }}">{{ $delivery }}</option>  @endforeach</select></div>';
        }
        function delivery_of_date(){
            var deliveryStatusText = $('input[name="changestatus_delivery_date_hidden"]').val();
            return '<div class="form-group col-sm-8"><label>Delivery Date: </label><input type="date" class="form-control" required  name="delivery_time" required="" value="'+deliveryStatusText+'"></div>';
        }
        function delivery_status(){
            var deliveryStatusText = $('input[name="changestatus_status_hidden"]').val();
            return '<div class="form-group col-sm-6"><label>Current Status: </label><input type="text" readonly value="'+deliveryStatusText+'" class="form-control " /></div><div class="form-group col-sm-6"><label>New Status: </label><select required  name="new_status" id="new_status"  class= "new_status form-control select2" ><option value=""> Please Select </option>@foreach($shipping_status as $id => $delivery_status) <option value="{{ $id }}">{{ $delivery_status }}</option>  @endforeach</select></div>';
        }

        function onChangeTypeOfChange(type_of_change){
            var packageArray = [];
            if(type_of_change == 0){
                packageArray.push(schedule_of_delivery());
            }else if(type_of_change == 1){
                packageArray.push(delivery_of_date());
            }else if(type_of_change == 2){
                
                packageArray.push(delivery_status());
            }else{
                packageArray.push('');
            }
            return newRow =  packageArray.join('');
        }

        function showToastError(type_of_change){
            if(type_of_change == '0')  {              
                var shipping_delivery = $('#shipping_delivery').val();
                if(shipping_delivery == ''){
                    toastr.error("Please select scheduled delivery!");
                    return false;
                }
            }else if(type_of_change == '1')  {              
                var delivery_time = $('input[name="delivery_time"]').val();
                if(delivery_time == ''){
                    toastr.error("Please select delivery date!");
                    return false;
                }
            }else if(type_of_change == '2') {               
                var new_status = $('#new_status').val();
                if(new_status == ''){
                    toastr.error("Please select delivery status!");
                    return false;
                }
            }else{
                toastr.error("Please select type of change!");
                return false;
            }
            return true;
        }

        function saveChangeStatusModal(){
            var type_of_change = $('#type_of_change').val();
                
            var change_row = $('#change_row').val();
            var id= parseInt(change_row)+1;
            if(type_of_change == ''){
                toastr.error("Please select type of change!");
                return false;
            }

            if(change_row=='0'){
                if(!showToastError(type_of_change)) return false;
                type_of_changes = [type_of_change];
            }else if(change_row=='1'){
                var type_of_change_1 = $('#type_of_change_1').val();
                type_of_changes = [type_of_change, type_of_change_1];
                if(!showToastError(type_of_change)) return false;
                if(!showToastError(type_of_change_1)) return false;
            }else if(change_row=='2'){
                var type_of_change_1 = $('#type_of_change_1').val();
                var type_of_change_2 = $('#type_of_change_2').val();
                type_of_changes = [type_of_change, type_of_change_1 ,type_of_change_2];
                if(!showToastError(type_of_change)) return false;
                if(!showToastError(type_of_change_1)) return false;
                if(!showToastError(type_of_change_2)) return false;               
            }
            console.log(type_of_changes);

            $(".save_change_status").text("Loading...");

            var shipping_delivery='';var delivery_time='';var new_status='';
            shipping_delivery= $('#shipping_delivery').val();
            delivery_time   = $('input[name="delivery_time"]').val();
            new_status      = $('#new_status').val();
            shipping_id     = $('input[name="changestatus_id_hidden"]').val();
            $.ajax({
                method: 'POST',
                url: '{{ action('\Modules\Shipping\Http\Controllers\ShippingController@saveShippingDetails') }}',
                data: {shipping_delivery,delivery_time,new_status,shipping_id,type_of_changes},
                success: function(result) {
                    $(".save_change_status").text("Save");
                    toastr.success("Update Done Successfully!");
                    window.location.href='{{ action('\Modules\Shipping\Http\Controllers\ShippingController@index') }}';
                },
                error: function(result) {
                    $(".save_change_status").text("Save");
                    toastr.error("Please try again!");
                    return false;
                },
        });
            
        }

        $(document).ready(function() {
            $('#type_of_change').on( "change", function() {
                var type_of_change = $(this).val();
                var newRow =  onChangeTypeOfChange(type_of_change);
                $('.type_of_row_0').html(newRow);
            });
            $('body').on('change', '#type_of_change_1', function() {
                var type_of_change = $(this).val();
                console.log(type_of_change);
                var newRow =  onChangeTypeOfChange(type_of_change);
                console.log(newRow);
                $('.type_of_row_1').html(newRow);
            });
            $('body').on('change', '#type_of_change_2', function() {
                var type_of_change = $(this).val();
                console.log(type_of_change);
                var newRow =  onChangeTypeOfChange(type_of_change);
                console.log(newRow);
                $('.type_of_row_2').html(newRow);
            });

            $('#addTypeChange').click(function() {
                // Get values from the input fields
                var type_of_change = $('#type_of_change').val();
                
                var change_row = $('#change_row').val();
                var id= parseInt(change_row)+1;
                if(type_of_change == ''){
                    toastr.error("Please select type of change!");
                    return false;
                }
               
                if(change_row == 1){
                    var type_of_change_1 = $('#type_of_change_1').val();
                    if(type_of_change_1 == 'Please Select'){
                        toastr.error("Please select type of change!");
                        return false;
                    }
                    $('#type_of_change_1').prop('disabled', true);
                }else if(change_row == 2){
                    toastr.error("Maximum 3 can be added!");
                    return false;
                }else{
                    $('#type_of_change').prop('disabled', true);
                }
                $('#change_row').val(id);
                
                
                var packageArray = [];
                if(change_row=='0'){
                    if(type_of_change == '0')                
                        packageArray.push('<select name="type_of_change[]" id="type_of_change_'+id+'" class= "type_of_change form-control select2" ><option>Please Select</option><option value="1">Delivery Date</option><option value="2">Status of Delivery</option></select>');
                    else if(type_of_change == '1')                
                        packageArray.push('<select name="type_of_change[]" id="type_of_change_'+id+'" class= "type_of_change form-control select2" ><option>Please Select</option><option value="0">Scheduled for Delivery</option><option value="2">Status of Delivery</option></select>');
                    else if(type_of_change == '2')                
                        packageArray.push('<select name="type_of_change[]" id="type_of_change_'+id+'" class= "type_of_change form-control select2" ><option>Please Select</option><option value="0">Scheduled for Delivery</option><option value="1">Delivery Date</option></select>');
                }else if(change_row=='1'){
                    if(type_of_change == '0' && type_of_change_1=='1')                
                        packageArray.push('<select name="type_of_change[]" id="type_of_change_'+id+'" class= "type_of_change form-control select2" ><option>Please Select</option><option value="2">Status of Delivery</option></select>');
                    else if(type_of_change == '0' && type_of_change_1=='2')               
                        packageArray.push('<select name="type_of_change[]" id="type_of_change_'+id+'" class= "type_of_change form-control select2" ><option>Please Select</option><option value="1">Delivery Date</option></select>');
                    else if(type_of_change == '1' && type_of_change_1=='2')               
                        packageArray.push('<select name="type_of_change[]" id="type_of_change_'+id+'" class= "type_of_change form-control select2" ><option>Please Select</option><option value="0">Scheduled for Delivery</option></select>');
                    else if(type_of_change == '1' && type_of_change_1=='0')               
                        packageArray.push('<select name="type_of_change[]" id="type_of_change_'+id+'" class= "type_of_change form-control select2" ><option>Please Select</option><option value="2">Status of Delivery</option></select>');
                    else if(type_of_change == '2' && type_of_change_1=='0')               
                        packageArray.push('<select name="type_of_change[]" id="type_of_change_'+id+'" class= "type_of_change form-control select2" ><option>Please Select</option><option value="1">Delivery Date</option></select>');
                    else if(type_of_change == '2' && type_of_change_1=='1')               
                        packageArray.push('<select name="type_of_change[]" id="type_of_change_'+id+'" class= "type_of_change form-control select2" ><option>Please Select</option><option value="0">Scheduled for Delivery</option></select>');
                }

                var newRow =  '<div class="form-group col-sm-4"><label>Type of Change '+id+'</label>'+packageArray.join('')+'</div><div class="form-group col-sm-7 type_of_row_'+id+'"></div>';
        
                // Append the new row to the table
                $('.row_'+id).html(newRow);
        
                
            });

            $('#addItem').click(function() {
                var packageArray = [];
                packageArray.push('<input type="email" name="email_id[]" class="form-control email_id">');
        
                // Create a new row with the values
                var newRow = '<div class="input-group">'+packageArray.join('') +'</div>';
        
                // Append the new row to the table
                $('.emailadd').append(newRow);
            });
            
            
            $(document).on('click','.add-agent-btn-modal',function(){
                var id = $(this).data('id');
                $("#shipment_id").val(id);
                $(".add_agent_modal").modal('show');
            });
            
            
            $(document).on('click','.add-partner-btn-modal',function(){
                var id = $(this).data('id');
                $("#partner_shipment_id").val(id);
                $(".add_partner_modal").modal('show');
            });
            
            

            $('.whatsapp_send').click(function() {
                var whatsapp_number = $("#whatsapp_number").val();
                var shipment_id =  $('input[name="invoice_id_hidden"]').val();
                var fetch_data=0;
                
                if(whatsapp_number != "" && shipment_id != ""){
                    $.ajax({
                            method: 'POST',
                            url: '{{ action('\Modules\Shipping\Http\Controllers\ShippingController@sendWhatsappInvoice') }}',
                            dataType: 'json',
                            data: {whatsapp_number,shipment_id,fetch_data},
                            success: function(result) {
                                //4b task done updated by dushyant
                                var shippingInvoiceSend = result.shippingInvoiceSend;
                                var downloadUrl = result.path;
                                var row='';
                                shippingInvoiceSend.forEach(function(item){
                                    row += '<tr>' +
                                                '<td>' + item.formatted_created_at + '</td>' +
                                                '<td>' + item.whatsapp_number + '</td>' +
                                                '<td>' + item.email_id + '</td>' +
                                                '<td>' + item.username + '</td>' +
                                            '</tr>';
                                            console.log(row);
                                });
                                $('.send_invoice_table tbody').html(row);
                                whatsappPdf(downloadUrl);
                            },
                    });
                }else{
                    swal({
                        title: "Please Enter Whatsapp Number",
                        text: "",
                        icon: "error",
                        button: "OK!",
                    });
                }
               
            });


            $('.email_send').click(function() {
                const emailInputs = document.querySelectorAll('.email_id'); // Select all elements with class 'email_id'
                let emailList = [];

                emailInputs.forEach(input => {
                    emailList.push(input.value.trim()); // Push each email value into the emailList array
                });

                const emailids = emailList.join(', '); // Join the emails with a comma and space

                console.log(emailids);
                var shipment_id =  $('input[name="invoice_id_hidden"]').val();
                var fetch_data=0;
                
                if(emailids != "" && shipment_id != ""){
                    $.ajax({
                            method: 'POST',
                            url: '{{ action('\Modules\Shipping\Http\Controllers\ShippingController@sendEmailIdsInvoice') }}',
                            dataType: 'json',
                            data: {emailids,shipment_id,fetch_data},
                            success: function(result) {
                                //4b task done updated by dushyant
                                var shippingInvoiceSend = result.shippingInvoiceSend;
                                console.log(shippingInvoiceSend);
                                var row='';
                                shippingInvoiceSend.forEach(function(item){
                                    row += '<tr>' +
                                                '<td>' + item.formatted_created_at + '</td>' +
                                                '<td>' + item.whatsapp_number + '</td>' +
                                                '<td>' + item.email_id + '</td>' +
                                                '<td>' + item.username + '</td>' +
                                            '</tr>';
                                            console.log(row);
                                });
                                $('.send_invoice_table tbody').html(row);
                            },
                    });
                }else{
                    swal({
                        title: "Please Enter Email Ids",
                        text: "",
                        icon: "error",
                        button: "OK!",
                    });
                }
               
            });
           
            shipment_table = $('#shipment_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\ShippingController@index') }}',
                    data: function(d) {
                        // d.name = $('#name').val();
                        // d.address = $('#address').val();
                        // d.mobile = $('#mobile').val();
                        // var start_date = $('input#recipient_date_range_filter')
                        //     .data('daterangepicker')
                        //     .startDate.format('YYYY-MM-DD');
                        // var end_date = $('input#recipient_date_range_filter')
                        //     .data('daterangepicker')
                        //     .endDate.format('YYYY-MM-DD');
                        // d.start_date = start_date;
                        // d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                "columnDefs": [
                    { 
                        "targets": 12, // Index of the column (zero-based index)
                        "className": "delivery-status" 
                    }
                ],
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'operation_date',
                        name: 'operation_date'
                    },
                    
                    {
                        data: 'tracking_no',
                        name: 'tracking_no'
                    },

                    {
                        data: 'shipper_tracking_no',
                        name: 'shipper_tracking_no'
                    },
                    
                    {
                        data: 'agent',
                        name: 'agent'
                    },
                    {
                        data: 'sender',
                        name: 'sender'
                    },
                    {
                        data: 'recipient',
                        name: 'recipient'
                    },
                    {
                        data: 'mode',
                        name: 'mode'
                    },
                    {
                        data: 'package',
                        name: 'package'
                    },
                    {
                        data: 'delivery',
                        name: 'delivery'
                    },
                    {
                        data: 'delivery_time',
                        name: 'delivery_time'
                    },
                    {
                        data: 'partner',
                        name: 'partner'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });

            

        })

        $('#recipient_date_range_filter, #mobile, #name').change(function() {
            shipment_table.ajax.reload();
        })
        
        $(document).on('click', 'a.delete_button', function(e) {
            var page_details = $(this).closest('div.page_details')
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                            shipment_table.ajax.reload();
                        },
                    });
                }
            });
        });

</script>

@endsection