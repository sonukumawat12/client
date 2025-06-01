@extends('layouts.app')
@section('title', __('shipping::lang.recipients'))

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
                {!! Form::label('recipient_date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('recipient_date_range_filter', @format_date('first day of this month') . ' ~ ' .
                @format_date('last
                day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                'form-control date_range', 'id' => 'recipient_date_range_filter', 'readonly']); !!}
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

  @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.recipients')])
  @slot('tool')
  <div class="box-tools ">
    <button type="button" class="btn  btn-primary btn-modal pull-right"
      data-href="{{action('\Modules\Shipping\Http\Controllers\RecipientController@create')}}"
      data-container=".view_modal">
      <i class="fa fa-plus"></i> @lang('messages.add')</button>

  </div>
  @endslot
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="recipient_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('shipping::lang.added_date')</th>
          <th>@lang('shipping::lang.name')</th>
          <th>@lang('shipping::lang.address')</th>
          <th>@lang('shipping::lang.mobile_1')</th>
          <th>@lang('shipping::lang.mobile_2')</th>
          <th>@lang('shipping::lang.land_no')</th>
          <th>@lang('shipping::lang.pending_deliveries')</th>
          <th>@lang('shipping::lang.completed_deliveries')</th>
        </tr>
      </thead>
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->
@endsection


@section('javascript')

<script src="{{url('Modules/Shipping/Resources/assets/js/app.js')}}"></script>
    
<script>
    //driver tab script
        if ($('#recipient_date_range_filter').length == 1) {
            $('#recipient_date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#recipient_date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                recipient_table.ajax.reload();
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
        $(document).ready(function() {
            recipient_table = $('#recipient_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ action('\Modules\Shipping\Http\Controllers\RecipientController@index') }}',
                    data: function(d) {
                        d.name = $('#name').val();
                        d.address = $('#address').val();
                        d.mobile = $('#mobile').val();
                        var start_date = $('input#recipient_date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#recipient_date_range_filter')
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
                        data: 'pending_deliveries',
                        name: 'pending_deliveries'
                    },
                    {
                        data: 'completed_deliveries',
                        name: 'completed_deliveries'
                    },


                ],
                fnDrawCallback: function(oSettings) {

                },
            });

            

        })

        $('#recipient_date_range_filter, #mobile, #name').change(function() {
            recipient_table.ajax.reload();
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
                            recipient_table.ajax.reload();
                        },
                    });
                }
            });
        });

</script>

@endsection