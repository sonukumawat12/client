@extends('layouts.app')
@section('title', 'Users')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'user.list' )
        <small>@lang( 'user.manage_user_list' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'user.all_user.list' )])
       
        @can('user.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="users_list_table">
                    <thead>
                        <tr>
                            <th>@lang( 'business.username' )</th>
                            <th>@lang( 'user.business_name' )</th>
                            <th>@lang( 'user.address' )</th>
                            <th>@lang( 'user.country' )</th>
                            <th>@lang( 'user.state' )</th>
                            <th>@lang( 'user.district' )</th>
                            <th>@lang( 'user.city' )</th>
                           
                         {{--   <th>@lang( 'messages.action' )</th>--}}
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade user_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        var users_table = $('#users_list_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/user/list',
                    "columns":[
                        {"data":"username"},
                        {"data":"business_name"},
                        {"data":"address"},
                        {"data":"country"},
                        {"data":"state"},
                        {"data":"district"},
                        {"data":"city"},
                      
                      //  {"data":"action", "orderable": false, 'searchable': false}
                    ]
                });
    });
    
    
</script>
@endsection
