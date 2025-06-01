@extends('layouts.app')

@section('title', __('superadmin::lang.user_locations_page_title'))

@section('content')

<div class="content" id="user-locations-wrapper" >
    
    @component('components.filters', ['title' => __('report.filters')])
        <div class="row" >
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'), 
                        ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('country', __('superadmin::lang.country') . ':') !!}
                    {!! Form::select('country', $countries, null, ['class' => 'form-control select2', 
                        'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('state', __('superadmin::lang.state_province') . ':') !!}
                    {!! Form::select('state', $states, null, ['class' => 'form-control select2', 
                        'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('city', __('superadmin::lang.city') . ':') !!}
                    {!! Form::select('city', $cities, null, ['class' => 'form-control select2', 
                        'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('district', __('superadmin::lang.district') . ':') !!}
                    {!! Form::select('district', $districts, null, ['class' => 'form-control select2', 
                        'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('access_type', __('superadmin::lang.user_locations_type') . ':') !!}
                    {!! Form::select('access_type', $access_types, null, ['class' => 'form-control select2', 
                        'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
        </div>
    @endcomponent
    
    
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.user_locations_page_title' )])@endcomponent
    
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="user-locations-table" >
            <thead>
                <tr>
                    <th>@lang('superadmin::lang.date_and_time')</th>
                    <th>@lang('superadmin::lang.name')</th>
                    <th>@lang('superadmin::lang.country')</th>
                    <th>@lang('superadmin::lang.state_province')</th>
                    <th>@lang('superadmin::lang.city')</th>
                    <th>@lang('superadmin::lang.district')</th>
                    <th>@lang('superadmin::lang.address')</th>
                    <th>@lang('superadmin::lang.location_data_source')</th>
                    <th>@lang('superadmin::lang.page_accessed')</th>
                </tr>
            </thead>
            <tfoot></tfoot>
        </table>
    </div>
    
    
</div>


@endsection


@section('javascript')

<script>

(function(){
    
    // Define global variables
    const page_selectors = {
        dt_table: '#user-locations-wrapper #user-locations-table', 
        date_range: '#user-locations-wrapper input#date_range', 
        select2: '#user-locations-wrapper select.select2', 
        filters: '#user-locations-wrapper select.select2' 
    }
    
    // Function runs on ready 
    $(document).ready(function(){
        
        initDateRangePicker();
        initSelect2();
        initDataTable();
        
        $('body').on('cancel.daterangepicker', page_selectors.dt_table, function(ev, picker){
            $(this).val('');
            $(page_selectors.date_range).text("Date Range: - ");
            dataTableReload();
        });
        
        $('body').on('change', page_selectors.filters, function(){
            dataTableReload();
        });
        
    });
    
    
    // Define functions 
    function initDateRangePicker(){
        let date_range_inp = $('body').find(page_selectors.date_range);
        date_range_inp.daterangepicker(dateRangeSettings, function (start, end){
            date_range_inp.val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            dataTableReload();
        });
        
        date_range_inp.data('daterangepicker').setStartDate(moment().startOf('month'));
        date_range_inp.data('daterangepicker').setEndDate(moment().endOf('month'));
    }
    
    
    function initSelect2(){
            // console.log( $(page_selectors.select2) );
        $(page_selectors.select2).select2();
    }
    
    
    function initDataTable(){
        let table = $('body').find(page_selectors.dt_table);
        
        table.DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{ route('userlocations.index') }}",
                data: function(d){
                    let date_range_picker = $(page_selectors.date_range).data('daterangepicker');
                    d.start_date = date_range_picker.startDate.format('YYYY-MM-DD');
                    d.end_date = date_range_picker.endDate.format('YYYY-MM-DD');
                    
                    $(page_selectors.filters).each(function(){
                        let val = $(this).val();
                        if( val ){
                            d[ $(this).prop('name') ] = val;
                        }
                    });
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date_time', name: 'date_time'}, 
                {data: 'name', name: 'name'}, 
                {data: 'country', name: 'country'}, 
                {data: 'state', name: 'state_province'}, 
                {data: 'city', name: 'city'}, 
                {data: 'district', name: 'district'}, 
                {data: 'address', name: 'address'}, 
                {data: 'location_data_source', name: 'location_data_source'},
                {data: 'access_type', name: 'page_accessed'},
            ]
        });
    }
    
    
    function dataTableReload(){
        let table = $('body').find(page_selectors.dt_table);
        table.DataTable().ajax.reload();
    }
    
})();

</script>

@endsection

