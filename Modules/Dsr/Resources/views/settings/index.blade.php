@extends('layouts.app')
@section('title', __('dsr::lang.settings') . ' | Dsr Settings')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('dsr::lang.settings')<small>@lang('dsr::lang.edit_dsr_settings')</small>
        </h1>
    </section>
    <style>
        .wrapper {
            overflow: hidden;
        }
    </style>
    <!-- Main content -->
    <section class="content">
        <div class="settlement_tabs">
            <ul class="nav nav-tabs">
                <li class="dsrSettings active tab_settings" id="tab_settings">
                    <a href="#dsrSettings" class="dsrSettings" data-toggle="tab">
                        <i class="fa fa-users"></i>
                        <strong>@lang('dsr::lang.dsr_settings')</strong>
                    </a>
                </li>
                <li class="dsr_opening_meter opening_meter" id="opening_meter">
                    <a href="#dsr_opening_meter" class="dsr_opening_meter" data-toggle="tab">
                        <i class="fa fa-users"></i>
                        <strong>@lang('dsr::lang.accumulative_sale_and_purchase')</strong>
                    </a>
                </li>
            </ul>
            <hr>
       <div class="tab-content">
            <div class="tab-pane active dsrSettings" id="dsrSettings">
                @include('dsr::settings.dsrSettings')
            </div>
            <div class="tab-pane dsr_opening_meter" id="dsr_opening_meter">
                @include('dsr::settings.dsr_opening_meter.index')
            </div>
        </div>
      </div>
    </section>
    <div class="modal fade dsr_settings_model" size="lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
@endsection
@push('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            // Check if URL contains the parameter
            if (window.location.href.indexOf('tab=dsr_opening_meter') !== -1) {
                // Remove active class from dsrSettings tab
                $('.dsrSettings').removeClass('active');
                $('.dsrSettings').removeClass('active');

                // Add active class to dsr_opening_meter tab
                $('.dsr_opening_meter').addClass('active');
                $('.dsr_opening_meter').addClass('active');

                // Trigger click event on the dsr_opening_meter tab link
                setTimeout(()=>{
                    $('.dsr_opening_meter a').click();
                }, 1000);
            }
        });
        $(()=>{
            $('.select2').select2();
        })
        $('#dsr_settings_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ action('\Modules\Dsr\Http\Controllers\DsrSettingsController@index') }}",
            columnDefs: [{
                "targets": 1,
                "orderable": false,
                "searchable": false
            }],
            columns: [
                { data: 'dealer_name', name: 'dealer_name' },
                { data: 'country', name: 'country' },
                { data: 'province', name: 'province' },
                { data: 'action', name: 'action' }
            ],
            "fnDrawCallback": function(oSettings) {
            }
        });
        $('#dsr_openingmeter').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ action('\Modules\Dsr\Http\Controllers\DsrSettingsController@listAccumulativeSalePurchase') }}",
            columnDefs: [{
                "targets": 1,
                "orderable": false,
                "searchable": false
            }],
            columns: [
                { data: 'action'},
                { data: 'date', name: 'date' },
                { data: 'product', name: 'product' },
                { data: 'accumulative_sale', name: 'accumulative_sale' },
                { data: 'accumulative_purchase', name: 'accumulative_purchase' },
                { data: 'user', name: 'user' }
            ],
            "fnDrawCallback": function(oSettings) {
            }
        });
        $(document).on('click', 'button.dsr_officer_del', function(){
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
                            priority_table.ajax.reload();
                        },
                    });
                }
            });
        })
    </script>
@endpush
