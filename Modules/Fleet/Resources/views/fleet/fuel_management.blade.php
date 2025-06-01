
@extends('layouts.app')

@section('title', __('fleet::lang.fleet'))

@section('content')
<!-- Main content -->
<section class="content">
    
        
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
                <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
                </div>
            </div>
           
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_number', __( 'fleet::lang.vehicle_number' )) !!}
                    {!! Form::select('vehicle_number', $vehicle_numbers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'vehicle_number']);
                    !!}
                </div>
            </div>
          
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('driver_id', __( 'fleet::lang.driver_id' )) !!}<br>
                    {!! Form::select('driver_id', $drivers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'driver_id']);
                    !!}
                </div>
            </div>
            
            </div>
            @endcomponent
        
    

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'fleet::lang.fuel_management')])
            
            <div class="row">
                <div class="col-md-11">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="fuel_management_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>@lang( 'fleet::lang.vehicle_number' )</th>
                                    <th>@lang( 'fleet::lang.driver' )</th>
                                    <th>@lang( 'fleet::lang.last_odometer' )</th>
                                    <th>@lang( 'fleet::lang.enter_odometer' )</th>
                                    <th>@lang( 'fleet::lang.fuel_type' )</th>
                                    <th>@lang( 'fleet::lang.liters' )</th>
                                    <th>@lang( 'fleet::lang.cost_per_liter' )</th>
                                    <th>@lang( 'fleet::lang.total_amount' )</th>
                                    <th>@lang( 'fleet::lang.fuel_cost' )</th>
                                    <th>@lang( 'fleet::lang.fuel_consumption' )</th>
                                    <th>@lang( 'fleet::lang.pumped_on' )</th>
                                    <th>@lang( 'fleet::lang.added_by' )</th>
                                    <th>@lang( 'messages.action' )</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            
                            <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="8"><strong>@lang('fleet::lang.average_consumption'):</strong></td>
                                    <td><span class="display_currency" id="average_consumption"
                                            data-currency_symbol="false"></span></td>
                                   <td colspan="3"></td>
                                </tr>
                                
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="8"><strong>@lang('fleet::lang.average_cost_per_km'):</strong></td>
                                    <td><span class="display_currency" id="average_cost_per_km"
                                            data-currency_symbol="true"></span></td>
                                   <td colspan="3"></td>
                                </tr>
                                
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                                    <td >
                                        <span class="display_currency" id="liters_pumped"
                                            data-currency_symbol="false"></span>
                                    </td>
                                    <td></td>
                                    <td><span class="display_currency" id="total_amount"
                                            data-currency_symbol="true"></span></td>
                                   <td colspan="3"></td>
                                </tr>
                            </tfoot>
                            
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade fleet_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $('#location_id option:eq(1)').attr('selected', true);

    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
            fuel_management_table.ajax.reload();
        });
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('year'));
        $('#date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('year'));
    }
    
    $(document).on('click', '#add_fleet_btn', function(){
        $('.fleet_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    })


    // fuel_management_table
    $(document).ready(function(){
        
        @if (session('status'))
            @if(session('status')['success'] == false)
                    var msg = "{{ session('status')['msg'] }}"
                    toastr.error(msg);
            @endif
        @endif
        
        
        fuel_management_table = $('#fuel_management_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Fleet\Http\Controllers\FleetController@fuelManagement')}}",
                    data: function(d){
                        d.driver_id = $('#driver_id').val();
                        d.vehicle_number = $('#vehicle_number').val();
                        d.start_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    },
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'date', name: 'date'},
                    {data: 'vehicle_number', name: 'vehicle_number'},
                    {data: 'driver', name: 'driver'},
                    {data: 'previous_odometer', name: 'previous_odometer'},
                    {data: 'current_odometer', name: 'current_odometer'},
                    {data: 'fuel_typen', name: 'fuel_typen'},
                    {data: 'liters', name: 'liters'},
                    {data: 'price_per_liter', name: 'price_per_liter'},
                    {data: 'total_amount', name: 'total_amount'},
                    // {data: 'fuel_cost', name: 'fuel_cost'},
                    {data: 'price_per_liter', name: 'price_per_liter'},
                    {data: 'fuel_consumption', name: 'fuel_consumption'},
                    {data: 'date', name: 'date'},
                    {data: 'added_by', name: 'added_by'},
                    {data: 'action', name: 'action'},
                  
                ],
                
                fnDrawCallback: function(oSettings) {
                    
                    if (oSettings.json && oSettings.json.avg) {
                        var avg = oSettings.json.avg;
                        $('#average_consumption').text(__number_f(avg));
                    } 
                    
                    if (oSettings.json && oSettings.json.avg_cost) {
                        var avg_cost = oSettings.json.avg_cost;
                        $('#average_cost_per_km').text(__number_f(avg_cost));
                    } 
                    
                    
                    
                    
                    var total_amount = sum_table_col($('#fuel_management_table'), 'total_amount');
                    $('#total_amount').text(total_amount);
                    
                    var liters_pumped = sum_table_col($('#fuel_management_table'), 'liters_pumped');
                    $('#liters_pumped').text(liters_pumped);
                    
            
                    __currency_convert_recursively($('#fuel_management_table'));
                }
            });
        });

        $('#date_range_filter, #location_id, #vehicle_model, #vehicle_brand, #driver_id, #vehicle_number').change(function () {
            fuel_management_table.ajax.reload();
        })
        $(document).on('click', 'a.delete-fleet', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            fuel_management_table.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection