@extends('layouts.app')
@section('title', __('account.fixed_assets'))

@section('content')


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('account.fixed_assets')</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('account.fixed_assets')</a></li>
                    <li><span>@lang('account.fixed_assets')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
    
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'),
            'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('location_id', __('account.asset_location') . ':') !!}
            {!! Form::select('location_id', $locations, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
    </div>
     <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('asset_name', __('account.asset_name') . ':') !!}
            {!! Form::select('asset_name', $names, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('created_by', __('account.created_by') . ':') !!}
            {!! Form::select('created_by', $users, null, ['class' => 'form-control select2',
            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
        </div>
    </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'account.fixed_assets')])

    @slot('tool')
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-primary btn-modal"
            data-href="{{action('FixedAssetController@create')}}" data-container=".add_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="fixed_assets_table">
            <thead>
                <tr>
                    <th>@lang('account.date')</th>
                    <th>@lang('account.account')</th>
                    <th>@lang('account.asset_name')</th>
                    <th>@lang('account.asset_location')</th>
                    <th>@lang('account.account_no')</th>
                    <th>@lang('account.amount')</th>
                    <th>@lang('account.action')</th>
                </tr>
            </thead>

        </table>
    </div>
    <div class="modal fade add_modal" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade edit_modal" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    @endcomponent
</section>
@endsection

@if(!$account_access)
<style>
  .dataTables_empty{
        color: {{App\System::getProperty('not_enalbed_module_user_color')}};
        font-size: {{App\System::getProperty('not_enalbed_module_user_font_size')}}px;
    }
</style>
@endif

@section('javascript')
<script>
    
    if ($('#sell_list_filter_date_range').length == 1) {
            $('#sell_list_filter_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#sell_list_filter_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
                fixed_assets_table.ajax.reload();
            });
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#sell_list_filter_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#sell_list_filter_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }

  
  
    //employee list
    fixed_assets_table = $('#fixed_assets_table').DataTable({
        language: {
            "emptyTable": "@if(!$account_access) {{App\System::getProperty('not_enalbed_module_user_message')}} @else @lang('account.no_data_available_in_table') @endif"
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("FixedAssetController@index")}}',
            data: function (d) {
                if($('#sell_list_filter_date_range').val()) {
                    var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                    d.location_id = $("#location_id").val();
                    d.asset_name = $("#asset_name").val();
                    d.created_by = $("#created_by").val();
                }
            }
        },
        
        columns: [
            
            { data: 'date_of_operation', name: 'date_of_operation' },
            { data: 'account_name', name: 'accounts.name' },
            { data: 'asset_name', name: 'asset_name' },
            { data: 'asset_location', name: 'asset_location' },
            { data: 'account_no', name: 'accounts.account_number' },
            { data: 'amount', name: 'amount' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    
    $('#sell_list_filter_date_range,#location_id,#asset_name,#created_by').change(function(){
        fixed_assets_table.ajax.reload();
    })

    $(document).on('click', 'a.delete_journal', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This template will be deleted.',
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
                        if (result.success == 1) {
                            toastr.success(result.msg);
                            fixed_assets_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $(document).on('click', '.journal_edit', function(e) {
        e.preventDefault();
        $('div.edit_modal').load($(this).attr('href'), function() {
            $(this).modal('show');
        });
    });

    $('.add_modal').on('hidden.bs.modal', function () {
        $('.journal_rows').remove();
        console.log('asdf');
        
    })
</script>
@endsection