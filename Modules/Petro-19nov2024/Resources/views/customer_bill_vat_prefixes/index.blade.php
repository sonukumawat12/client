@extends('layouts.app')

@section('title', __('petro::lang.prefix_and_starting_nos'))



@section('content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.petro')</a></li>
                    <li><span>@lang( 'petro::lang.prefix_and_starting_nos')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<section class="content main-content-inner">
    
    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-primary btn-modal add_fuel_tank"
            data-href="{{action('\Modules\Petro\Http\Controllers\CustomerBillVatPrefixController@create')}}"
            data-container=".fuel_tank_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="prefixes_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.prefix')</th>
                    <th>@lang('petro::lang.starting_no')</th>
                    <th>@lang('petro::lang.created_by')</th>
                    <th>@lang('lang_v1.action')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')

<script type="text/javascript">

    $(document).ready( function(){

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
            url: '{{action('\Modules\Petro\Http\Controllers\CustomerBillVatPrefixController@index')}}',
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
                    },
                });
            }
        });
    });

});

</script>



@endsection