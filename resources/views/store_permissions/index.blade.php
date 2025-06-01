@extends('layouts.app')
@section('title', __('store.store_permissions'))

@section('content')

<style>
    /* Background color for success state */
.bg-success {
  background-color: #28a745; /* Default Bootstrap success color */
  /* You can replace the color code with your desired color */
}

/* Background color for danger state */
.bg-danger {
  background-color: #dc3545; /* Default Bootstrap danger color */
  /* You can replace the color code with your desired color */
}

</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('store.store_permissions')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'store.store_permissions' )])
        @can('store.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-primary btn-modal pull-right"
                    data-href="{{action('StoreController@createStorePermission')}}" data-container=".store_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('store.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="store_permissions_table">
                    <thead>
                        <tr>
                            <th>@lang('store.date')</th>
                            <th>@lang('store.store')</th>
                            <th>@lang('store.user')</th>
                            <th>@lang('store.sell')</th>
                            <th>@lang('store.purchase')</th>
                            <th>@lang('store.stores_transfer')</th>
                            <th>@lang('store.stock_adjustment')</th>
                            <th>@lang('store.sell_return')</th>
                            <th>@lang('store.added_by')</th>
                            <th>@lang('store.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade store_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $('#location_id').change(function () {
        store_permissions_table.ajax.reload();
    });
    //employee list
    store_permissions_table = $('#store_permissions_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("StoreController@fetchUserStorePermissions")}}',
            data: function (d) {
                
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'store', name: 'store' },
            { data: 'username', name: 'username' },
            { data: 'sell', name: 'sell' },
            { data: 'purchase', name: 'purchase' },
            { data: 'stores_transfer', name: 'stores_transfer' },
            { data: 'stock_adjustment', name: 'stock_adjustment' },
            { data: 'sell_return', name: 'sell_return' },
            { data: 'created_by', name: 'created_by' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_store', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This store will be deleted.',
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
                        if (result.success === true) {
                            toastr.success(result.msg);
                            store_permissions_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#filter_business').select2();


    $(document).on('click', 'button.edit_store_button', function() {
        $('div.edit_modal').load($(this).data('href'), function() {
            $(this).modal('show');

            $('form#unit_edit_form').submit(function(e) {
                e.preventDefault();
                $(this)
                    .find('button[type="submit"]')
                    .attr('disabled', true);
                var data = $(this).serialize();

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div.edit_modal').modal('hide');
                            toastr.success(result.msg);
                            store_permissions_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });

    
</script>
@endsection