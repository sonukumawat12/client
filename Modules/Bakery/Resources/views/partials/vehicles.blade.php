

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
                    {!! Form::label('location_id', __( 'fleet::lang.location' )) !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'location_id']);
                    !!}
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
                    {!! Form::label('vehicle_type', __( 'fleet::lang.vehicle_type' )) !!}
                    {!! Form::select('vehicle_type', $vehicle_types, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'vehicle_type']);
                    !!}
                </div>
            </div>
            
            </div>
            <div class="row">
          
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_brand', __( 'fleet::lang.vehicle_brand' )) !!}
                    {!! Form::select('vehicle_brand', $vehicle_brands, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'vehicle_brand']);
                    !!}
                </div>
            </div>
          
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_model', __( 'fleet::lang.vehicle_model' )) !!}
                    {!! Form::select('vehicle_model', $vehicle_models, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'vehicle_model']);
                    !!}
                </div>
            </div>
          </div>
            @endcomponent
        
    

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'fleet::lang.all_your_fleets')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_fleet_btn"
                    data-href="{{action('\Modules\Fleet\Http\Controllers\FleetController@create')}}"
                    data-container=".fleet_model">
                    <i class="fa fa-plus"></i> @lang( 'fleet::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-11">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="fleet_table_bakery" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'messages.action' )</th>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>@lang( 'fleet::lang.location' )</th>
                                    <th>@lang( 'fleet::lang.code_vehicle' )</th>
                                    <th>@lang( 'fleet::lang.vehicle_number' )</th>
                                    <th>@lang( 'fleet::lang.starting_meter' )</th>
                                    <th>@lang( 'fleet::lang.ending_meter' )</th>
                                    <th>@lang( 'fleet::lang.vehicle_type' )</th>
                                    <th>@lang( 'fleet::lang.fuel_type' )</th>
                                    <th>@lang( 'fleet::lang.income' )</th>
                                    <th>@lang( 'fleet::lang.payment_received' )</th>
                                    <th>@lang( 'fleet::lang.payment_due' )</th>
                                    <th>@lang( 'fleet::lang.opening_amount' )</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
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

@section('javascript')
<script>
    $('#location_id option:eq(1)').attr('selected', true);

    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
            fleet_table_bakery.ajax.reload();
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


    // fleet_table_bakery
    $(document).ready(function(){
        
        @if (session('status'))
            @if(session('status')['success'] == false)
                    var msg = "{{ session('status')['msg'] }}"
                    toastr.error(msg);
            @endif
        @endif
        
        
        fleet_table_bakery = $('#fleet_table_bakery').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Fleet\Http\Controllers\FleetController@index')}}",
                    data: function(d){
                        d.location_id = $('#location_id').val();
                        d.vehicle_model = $('#vehicle_model').val();
                        d.vehicle_brand = $('#vehicle_brand').val();
                        d.vehicle_type = $('#vehicle_type').val();
                        d.vehicle_number = $('#vehicle_number').val();
                        d.start_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'action', name: 'action'},
                    {data: 'date', name: 'date'},
                    {data: 'location_name', name: 'location_name'},
                    {data: 'code_for_vehicle', name: 'code_for_vehicle'},
                    {data: 'vehicle_number', name: 'vehicle_number'},
                    {data: 'starting_meter', name: 'starting_meter'},
                    {data: 'ending_meter', name: 'ending_meter'},
                    {data: 'vehicle_type', name: 'vehicle_type'},
                    {data: 'fuel_type', name: 'fuel_type'},
                    {data: 'income', name: 'income'},
                    {data: 'payment_received', name: 'payment_received'},
                    {data: 'payment_due', name: 'payment_due'},
                    {data: 'opening_balance', name: 'opening_balance'}
                  
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#fleet_table_bakery'));
                }
            });
        });

        $('#date_range_filter, #location_id, #vehicle_model, #vehicle_brand, #vehicle_type, #vehicle_number').change(function () {
            fleet_table_bakery.ajax.reload();
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
                            fleet_table_bakery.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection