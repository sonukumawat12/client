@extends('layouts.app')
@section('title', __('vat::lang.vat_invoice'))

@section('content')
<!-- Main content -->

<section class="content">

<div class="row">
    @include('vat::vat_invoice.partials.nav')
    @include('vat::vat_prefixes.index')
</div>


<div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

</section>

@endsection


@section('javascript')
<script>
    var columns = [

            { data: 'prefix', name: 'prefix' },
            
            { data: 'starting_no', name: 'starting_no' },

            { data: 'user_created', name: 'users.username' },
            
            { data: 'action', name: 'action' }
        ];

    prefixes_table = $('#prefixes_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {
            url: '{{action('\Modules\Vat\Http\Controllers\VatPrefixController@index')}}',
            data: function(d) {
                
            },
        },

        @include('layouts.partials.datatable_export_button')

        columns: columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });
    
    $(document).on('click', 'a.delete_task', function(e) {
		e.preventDefault();
		var href = $(this).data('href');
        var data = $(this).serialize();
        console.log(href);
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
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
                        prefixes_table.ajax.reload();
                        
                        tank_transfers_table.ajax.reload();
                        userinvoice_prefixes_table.ajax.reload();
                        userinvoice_smstypes_table.ajax.reload();
                        vat_payable_to_table.ajax.reload();
                    },
                });
            }
        });
    });
</script>
@endsection