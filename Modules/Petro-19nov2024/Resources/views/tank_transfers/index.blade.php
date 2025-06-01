@extends('layouts.app')

@section('title', __('petro::lang.tank_management'))



@section('content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.petro')</a></li>
                    <li><span>@lang( 'petro::lang.list_tank_transfer')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<section class="content main-content-inner">
    @if(!empty($message)) {!! $message !!} @endif
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'date_range', 'readonly']); !!}
                    </div>
                </div>
                
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('petro::lang.location').':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
                
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('from_tank', __('petro::lang.from_tank').':') !!}
                        {!! Form::select('from_tank', $tank_numbers, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('to_tank', __('petro::lang.to_tank').':') !!}
                        {!! Form::select('to_tank', $tank_numbers, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                 <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('product_id', __('petro::lang.product_id').':') !!}
                        {!! Form::select('product_id', $products, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-primary btn-modal add_fuel_tank"
            data-href="{{action('\Modules\Petro\Http\Controllers\TankTransferController@create')}}"
            data-container=".fuel_tank_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="tank_transfers_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.transfer_no')</th>
                    <th>@lang('petro::lang.from_tank')</th>
                    <th>@lang('petro::lang.from_qty')</th>
                    <th>@lang('petro::lang.to_tank')</th>
                    <th>@lang('petro::lang.to_qty')</th>
                    <th>@lang('petro::lang.product')</th>
                    <th>@lang('petro::lang.transfer_qty')</th>
                    <th>@lang('petro::lang.user_added')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')

<script type="text/javascript">

    $(document).ready( function(){

    var columns = [

            { data: 'date', name: 'date' },
            
            { data: 'location_name', name: 'business_locations.name' },
            
            { data: 'transfer_no', name: 'transfer_no' },

            { data: 't_from_name', name: 't_from.fuel_tank_number' },
            
            { data: 'from_qty', name: 'from_qty',searchable: false },
            
            { data: 't_to_name', name: 't_to.fuel_tank_number' },
            
            { data: 'to_qty', name: 'to_qty',searchable: false },

            { data: 'product_name', name: 'products.name' },

            { data: 'quantity', name: 'quantity' },
            
            { data: 'user_created', name: 'users.username' }
        ];

  

    tank_transfers_table = $('#tank_transfers_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\TankTransferController@index')}}',
            data: function(d) {
                d.product_id = $('select#product_id').val();
                d.from_tank = $('select#from_tank').val();
                d.to_tank = $('select#to_tank').val();
                d.start_date = $('input#date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },

        @include('layouts.partials.datatable_export_button')

        columns: columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });
    
    tank_transfers_table.column(1).visible(false);

});

</script>



<script type="text/javascript">

    if ($('#date_range').length == 1) {

        $('#date_range').daterangepicker(dateRangeSettings, function(start, end) {

            $('#date_range').val(

                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)

            );
            
            tank_transfers_table.ajax.reload();

        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {

            $('#date_range').val('');

        });

        $('#date_range')

            .data('daterangepicker')

            .setStartDate(moment().startOf('month'));

        $('#date_range')

            .data('daterangepicker')

            .setEndDate(moment().endOf('month'));

    }


    $('#date_range, #from_tank, #to_tank, #product_id, #location_id').change(function(){

        tank_transfers_table.ajax.reload();

    });

</script>


@endsection