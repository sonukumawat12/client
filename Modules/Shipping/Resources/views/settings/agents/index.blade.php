@extends('layouts.app')
@section('title', __('shipping::lang.agents'))

<style>
    .select2 {
        width: 100% !important;
    }
</style>
@section('content')
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('agent_date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('agent_date_range_filter', @format_date('first day of this month') . ' ~ ' .
                @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control date_range', 'id' => 'agent_date_range_filter', 'readonly']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('mobile', __( 'shipping::lang.mobile' )) !!}
                {!! Form::select('mobile', $mobile, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'shipping::lang.please_select' ), 'id' => 'mobile']);
                !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('name', __( 'shipping::lang.name' )) !!}
                {!! Form::select('name', $name, null, ['class' => 'form-control select2',
                'required',
                'placeholder' => __(
                'shipping::lang.please_select' ), 'id' => 'name']);
                !!}
            </div>
        </div>
       
        @endcomponent
    </div>
</div>

  @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.agents')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Shipping\Http\Controllers\AgentController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="agent_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('shipping::lang.added_date')</th>
          <th>@lang('shipping::lang.name')</th>
          <th>@lang('shipping::lang.current_due')</th>
          <th>@lang('shipping::lang.address')</th>
          <th>@lang('shipping::lang.mobile_1')</th>
          <th>@lang('shipping::lang.mobile_2')</th>
          <th>@lang('shipping::lang.land_no')</th>
          <th>@lang('shipping::lang.created_by')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->
@endsection


@section('javascript')

<script>

    
    //driver tab script
        if ($('#agent_date_range_filter').length == 1) {
            $('#agent_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#agent_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                agent_table.ajax.reload();
            });
            $('#agent_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#agent_date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#agent_date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        
        
        $(document).on('change','#pending_invoices', function() {
                let totalAmount = 0;

                // Iterate over selected options
                $('#pending_invoices option:selected').each(function() {
                    totalAmount += parseFloat($(this).data('string'));
                });

                // Assign the sum to the #amount element
                $('#amount').val(totalAmount);
            });
        
        $(document).ready(function() {
            
            agent_table = $('#agent_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\AgentController@index') }}',
                    data: function(d) {
                        d.name = $('#name').val();
                        //d.address = $('#address').val();
                        d.mobile = $('#mobile').val();
                        var start_date = $('input#agent_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#agent_date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'joined_date',
                        name: 'joined_date'
                    },
                    
                    {
                        data: 'name',
                        name: 'name'
                    },
                    
                    {
                        data: 'current_due',
                        name: 'current_due',
                        searchable: false
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'mobile_1',
                        name: 'mobile_1'
                    },
                    {
                        data: 'mobile_2',
                        name: 'mobile_2'
                    },
                    {
                        data: 'land_no',
                        name: 'land_no'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });

            

        })

        $('#agent_date_range_filter, #mobile, #name').change(function() {
            agent_table.ajax.reload();
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
                            agent_table.ajax.reload();
                        },
                    });
                }
            });
        });
        
</script>

@endsection