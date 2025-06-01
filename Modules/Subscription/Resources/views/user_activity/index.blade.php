
@extends('layouts.app')

@section('title', __('subscription::lang.user_activity'))

@section('content')
<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'subscription::lang.user_activity')])
            

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="user_activity_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'subscription::lang.date_time' )</th>
                                    <th>@lang( 'subscription::lang.username' )</th>
                                    <th>@lang( 'subscription::lang.model' )</th>
                                    <th>@lang( 'subscription::lang.activity_type' )</th>
                                    <th>@lang( 'subscription::lang.description' )</th>
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
   
   

    $(document).ready(function(){
    
        user_activity_table = $('#user_activity_table').DataTable({
                processing: true,
                serverSide: false,
                ordering: false,
                ajax: {
                    url : "{{action('\Modules\Subscription\Http\Controllers\SubscriptionUserActivityController@index')}}",
                    data: function(d){
                        
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'created_at', name: 'created_at'},
                    {data: 'user', name: 'users.username'},
                    {data: 'model', name: 'model'},
                    {data: 'description', name: 'description'},
                    {data: 'properties', name: 'properties'},
                    
                  
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#user_activity_table'));
                }
            });
        });
</script>
@endsection