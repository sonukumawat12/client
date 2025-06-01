
@extends('layouts.app')

@section('title', __('subscription::lang.subscription_settings'))

@section('content')
<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'subscription::lang.subscription_settings')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_fleet_btn"
                    data-href="{{action('\Modules\Subscription\Http\Controllers\SubscriptionSettingController@create')}}"
                    data-container=".subscription_modal">
                    <i class="fa fa-plus"></i> @lang( 'subscription::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-11">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="subscription_settings_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'messages.action' )</th>
                                    <th>@lang( 'subscription::lang.date' )</th>
                                    <th>@lang( 'subscription::lang.product' )</th>
                                    <th>@lang( 'subscription::lang.base_amount' )</th>
                                    <th>@lang( 'subscription::lang.subscription_amount' )</th>
                                    <th>@lang( 'subscription::lang.subscription_cycle' )</th>
                                    <th>@lang( 'subscription::lang.created_by' )</th>
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
    <div class="modal fade subscription_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
   
    $(document).on('click', '#add_fleet_btn', function(){
        $('.subscription_modal').modal({
            backdrop: 'static',
            keyboard: false
        })
    })


    $(document).ready(function(){
    
        subscription_settings_table = $('#subscription_settings_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Subscription\Http\Controllers\SubscriptionSettingController@index')}}",
                    data: function(d){
                        
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'action', name: 'action'},
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'product', name: 'product'},
                    {data: 'base_amount', name: 'base_amount'},
                    {data: 'subscription_amount', name: 'subscription_amount'},
                    {data: 'subscription_cycle', name: 'subscription_cycle',searchable: false},
                    {data: 'user', name: 'users.username'}
                  
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#subscription_settings_table'));
                }
            });
        });

        // $('#date_range_filter, #location_id, #vehicle_model, #vehicle_brand, #vehicle_type, #vehicle_number').change(function () {
        //     subscription_settings_table.ajax.reload();
        // })
        $(document).on('click', 'a.delete-button', function(){
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
                            subscription_settings_table.ajax.reload();
                        },
                    });
                }
            });
        })
        
</script>
@endsection