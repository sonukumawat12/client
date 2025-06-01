@extends('layouts.app')

@section('title', __('distribution::lang.edit'))

@section('content')
<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('\Modules\Distribution\Http\Controllers\RouteOperationController@update',$transaction->id), 'method' =>
    'put', 'id' => 'route_operation_form', 'enctype' => 'multipart/form-data' ])
    !!}
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'distribution::lang.route_operation')])
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_of_operation', __( 'distribution::lang.date_of_operation' )) !!}
                    {!! Form::text('date_of_operation', @format_datetime($transaction->route_operation->date_of_operation), ['class'
                    => 'form-control', 'required',
                    'placeholder' => __(
                    'distribution::lang.date_of_operation' ), 'readonly',
                    'id' => 'date_of_operation']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __( 'distribution::lang.location' )) !!}<br>
                    {!! Form::select('location_id', $business_locations, $transaction->route_operation->location_id, ['class' =>
                    'form-control select2',
                    'required',
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'location_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('contact_id', __( 'distribution::lang.customer' )) !!}
                    {!! Form::select('contact_id', $customers, $transaction->route_operation->contact_id, ['class' => 'form-control
                    select2',
                    'required',
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'customer']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('route_id', __( 'distribution::lang.route' )) !!}
                    {!! Form::select('route_id', $routes, $transaction->route_operation->route_id, ['class' => 'form-control
                    select2',
                    'required',
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'route_id']);
                    !!}
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('fleet_id', __( 'distribution::lang.vehicle_no' )) !!}
                    {!! Form::select('fleet_id', $fleets, $transaction->route_operation->fleet_id, ['class' => 'form-control
                    select2',
                    'required',
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'fleet_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('invoice_no', __( 'distribution::lang.invoice_no' )) !!}
                    {!! Form::text('invoice_no', $transaction->route_operation->invoice_no, ['class' => 'form-control', 'placeholder'
                    => __(
                    'distribution::lang.invoice_no' ), 'readonly',
                    'id' => 'invoice_no']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('order_number', __( 'distribution::lang.order_number' )) !!}
                    {!! Form::text('order_number', $transaction->route_operation->order_number, ['class' => 'form-control', 'placeholder'
                    => __('distribution::lang.order_number' ),
                    'id' => 'order_number']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('order_date', __( 'distribution::lang.order_date' )) !!}
                    {!! Form::text('order_date', $transaction->route_operation->order_date, ['class' => 'form-control', 'placeholder'
                    => __(
                    'distribution::lang.order_date' ),
                    'id' => 'order_date']);
                    !!}
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product_id', __( 'distribution::lang.product' )) !!}<br>
                    {!! Form::select('product_id', $products , $transaction->route_operation->product_id, ['class' => 'form-control
                    select2',
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'product_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('qty', __( 'distribution::lang.qty' )) !!}
                    {!! Form::text('qty', $transaction->route_operation->qty, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.qty' ),
                    'id' => 'qty']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('driver_id', __( 'distribution::lang.driver' )) !!}
                    <br>
                    {!! Form::select('driver_id', $drivers, $transaction->route_operation->driver_id, ['class' => 'form-control
                    select2',
                    'required',
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'driver_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('helper_id', __( 'distribution::lang.helper' )) !!}<br>
                    {!! Form::select('helper_id', $helpers, $transaction->route_operation->helper_id, ['class' => 'form-control
                    select2',
                    'required',
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'helper_id']);
                    !!}
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('distance', __( 'distribution::lang.distance_km' )) !!}
                    {!! Form::text('distance', $transaction->route_operation->distance, ['class' => 'form-control', 'placeholder' =>
                    __(
                    'distribution::lang.distance' ), 'readonly',
                    'id' => 'distance']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('amount', __( 'distribution::lang.amount' )) !!}
                    {!! Form::text('amount', $transaction->route_operation->amount, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.amount' ), 'readonly',
                    'id' => 'amount']);
                    !!}
                </div>
            </div> 
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('starting_meter', __( 'distribution::lang.starting_meter' )) !!}
                    {!! Form::text('starting_meter', $transaction->route_operation->starting_meter, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.starting_meter' ),
                    'id' => 'starting_meter','readonly','value'=>'00']);
                    !!}
                </div>
            </div>
             <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ending_meter', __( 'distribution::lang.ending_meter' )) !!}
                    {!! Form::text('ending_meter', $transaction->route_operation->ending_meter, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.ending_meter' ),
                    'id' => 'ending_meter','readonly','value'=> '00']);
                    !!}
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('driver_incentive', __( 'distribution::lang.driver_incentive' )) !!}
                    {!! Form::text('driver_incentive', $transaction->route_operation->driver_incentive, ['class' => 'form-control',
                    'placeholder' => __(
                    'distribution::lang.driver_incentive' ),'readonly',
                    'id' => 'driver_incentive']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('helper_incentive', __( 'distribution::lang.helper_incentive' )) !!}
                    {!! Form::text('helper_incentive', $transaction->route_operation->helper_incentive, ['class' => 'form-control',
                    'placeholder' => __(
                    'distribution::lang.helper_incentive' ),'readonly',
                    'id' => 'helper_incentive']);
                    !!}
                </div>
            </div>
            </div>
            
            
            <input type="hidden" name="grand_total_hidden" id="grand_total_hidden">
            <input type="hidden" name="final_total" id="final_total">

            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
            <div class="box-body payment_row" data-row_id="0">
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary pull-right"
                            id="add-payment-row">@lang('sale.add_payment_row')</button>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr>
                <div id="payment_rows_div">
                    @php
                        $j= 0;
                    @endphp
                    @if (!empty($transaction->payment_lines) && $transaction->payment_lines->count() > 0)
                    @foreach ($transaction->payment_lines as $pl)
                    @include('distribution::route_operations.partials.payment_row_edit', ['row_index' => $j, 'payment' => $pl, 'removable' =>true ])
                    @php
                        $j++;
                    @endphp
                    @endforeach
                    @else
                    @include('distribution::route_operations.partials.payment_row_edit', ['row_index' => 0, 'removable' =>true])
                    @endif
                </div>

                

                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span
                                id="payment_due">0.00</span>
                        </div>

                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" id="submit_route_operation_form"
                            class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade fleet_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
      $('#fleet_id').change(function () {
        
        let fleet_id = $(this).val();
         var actual_meter=0;
         var distance =0 ;
          $.ajax({
            method: 'GET',
            url: "{{ url('fleet-management/route-operation/get-by-fleet')}}/"+fleet_id,
            dataType: 'json',
            success: function (result) {
              if(result)
               {
                   actual_meter = result.actual_meter;
               }
            },
             complete: function (data) {
               if($("#distance").val())
                {
                    distance = parseInt($("#distance").val());
                }
                 __write_number($('#starting_meter'), actual_meter);
                __write_number($('#ending_meter'), actual_meter + distance);
               
            }
        });
       
    });
  
    $(document).ready( function(){

      $('#method_0').trigger('change');
    });
    $('#date_of_operation').datepicker('setDate', '{{@format_date($transaction->transaction_date)}}');
    $('#order_date').datepicker(@if(!empty($transaction->route_operation->order_date))'setDate', '{{@format_date($transaction->route_operation->order_date)}}'@endif);
    $('#route_id').change(function () {
        let id = $(this).val();

        $.ajax({
            method: 'get',
            url: '/fleet-management/routes/get-details/'+id,
            data: {  },
            success: function(result) {
                __write_number($('#distance'), result.distance);
                __write_number($('#amount'), result.route_amount);
                $('#fleet_id').trigger('change');

                $('#grand_total_hidden').val(result.route_amount);
                $('#final_total').val(result.route_amount);
                $('#payment_due').text(__currency_trans_from_en(result.route_amount, false, false));
                $('#amount_0').val(Number(result.route_amount).toFixed(2));
                __write_number($('#driver_incentive'), result.driver_incentive);
                __write_number($('#helper_incentive'), result.helper_incentive);
                $('vehicle_no')
            },
        });
    });

    $('button#add-payment-row').click(function () {
        var row_index = parseInt($('.payment_row_index').val()) + 1;
        var location_id = $('#location_id').val();

        $.ajax({
            method: 'POST',
            url: '/purchases/get_payment_row',
            data: { row_index: row_index, location_id: location_id },
            dataType: 'html',
            success: function (result) {
                if (result) {
                    var total_payable = __read_number($('input#grand_total_hidden'));
                    var total_paying = 0;
                    $('#payment_rows_div')
                        .find('.payment-amount')
                        .each(function () {
                            if (parseFloat($(this).val())) {
                                total_paying += __read_number($(this));
                            }
                        });
                    var b_due = total_payable - total_paying;
                    var appended = $('#payment_rows_div').append(result);
                    $(appended).find('input.payment-amount').focus();
                    $(appended).find('input.payment-amount').last().val(b_due).change().select();
                    __select2($(appended).find('.select2'));
                    $('#amount_' + row_index).trigger('change');
                    $('#cheque_date_' + row_index).datepicker('setDate', new Date());
                    $('.payment_row_index').val(parseInt(row_index));
                    let cash_account_id = $('#cash_account_id').val();
                    $(appended).find('select.payment_types_dropdown ').last().val(cash_account_id).change().select();
                }
            },
        });
    });

    $(document).on('click', '.remove_payment_row', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $(this).closest('.payment_row').remove();
                calculate_balance_due();
            }
        });
    });

    $(document).on('change', '.payment-amount', function() {
        calculate_balance_due();
    });

    
    function calculate_balance_due() {
        var total_payable = __read_number($('#final_total'));
        var total_paying = 0;
        $('#payment_rows_div')
            .find('.payment-amount')
            .each(function() {
                if (parseFloat($(this).val())) {
                    total_paying += __read_number($(this));
                }
            });
        var bal_due = total_payable - total_paying;
    

        $('#payment_due').text(__currency_trans_from_en(bal_due, false, false));
    }

    $('#submit_route_operation_form').click(function () {
        $('#route_operation_form').validate();
        if($('#route_operation_form').valid()){
            $('#route_operation_form').submit();
        }
    })
</script>
@endsection