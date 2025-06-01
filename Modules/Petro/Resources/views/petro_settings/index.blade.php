@extends('layouts.app')

@section('title', __('petro::lang.petro_settings'))



@section('content')

<!-- Content Header (Page header) -->

@php
                    
    $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp

<section class="content-header main-content-inner">
    <div class="row">

        <div class="col-md-12 dip_tab">

            <div class="settlement_tabs">

                <ul class="nav nav-tabs">

                    <li class="active" style="margin-left: 20px;">

                        <a style="font-size:13px;" href="#day_end_settlement" class="" data-toggle="tab">

                            <i class="fa fa-file-o"></i> <strong>@lang('petro::lang.day_end_settlement')</strong>

                        </a>

                    </li>


                   

                </ul>

            </div>

        </div>

    </div>

    <div class="tab-content">

        <div class="tab-pane active" id="petro_settings">

            @if(!empty($message)) {!! $message !!} @endif

            @include('petro::petro_settings.partials.day_end_settlement')

        </div>
       

    </div>



    <div class="modal fade dip_modal" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

</section>



@endsection

@section('javascript')

<script type="text/javascript">

    $(document).ready( function(){
       
        $(document).on('click', '.edit_dip', function (e) {
             e.preventDefault()
            var actionuRL = $(this).data('href');
            $('.dip_modal').load(actionuRL, function() {
                $(this).modal('show');
            });
        });
    
    
        
        $('.dip_modal').on('show.bs.modal', function () {
          $(this).data('bs.modal').options.backdrop = 'static';
          $(this).data('bs.modal').options.keyboard = false;
        });




    if ($('#date_range').length == 1) {

        $('#date_range').daterangepicker(dateRangeSettings, function(start, end) {

            $('#date_range').val(

                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)

            );
            day_end_settlement_table.ajax.reload();

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
        
        var day_end_settlement_table = $('#day_end_settlement_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{ action('\Modules\Petro\Http\Controllers\DayEndSettlementController@index') }}',
                data: function(d) {
                    d.start_date = $('input#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
            },
        
            
            columnDefs: [{
                "targets": 0,
                "orderable": true,
                "searchable": true
            }],
            columns: [
                { data: 'action', name: 'action' },
                { data: 'created_at', name: 'created_at' },
                { data: 'day_end_date', name: 'day_end_date' },
                { data: 'pumps', name: 'pumps' },
                { data: 'sold_pumps', name: 'sold_pumps' },
                { data: 'user_added', name: 'users.username' },
                { data: 'user_editted', name: 'users.username' },
            ]
        
    

    });
    
});

    @if(session('status'))
        @if(session('status')['success'] == false)
            toastr.error("{{session('status')['msg']}}");
        @endif 
    @endif

</script>

@endsection