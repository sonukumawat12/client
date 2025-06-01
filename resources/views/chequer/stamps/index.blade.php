@extends('layouts.app')
@section('title', __('cheque.stamps'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cheque.stamps')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'cheque.stamps_list')])

    @slot('tool')
    <div class="box-tools pull-right">
        <button type="button" class="btn  btn-primary"  id="cheque_stamp_add" data-href="{{action('Chequer\ChequerStampController@create')}}">
            <i class="fa fa-plus"></i> @lang('messages.add')
        </button>
    </div>
    <hr>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="stamps_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('cheque.stamp_image')</th>
                    <th>@lang('cheque.stamp_name')</th>
                    <th>@lang('cheque.active')</th>
                    <th>@lang('cheque.last_changed_date')</th>
                    <th>@lang('cheque.action')</th>
                </tr>
            </thead>

        </table>
    </div>

    @endcomponent
</section>

<!-- Modal -->

<div class="modal fade" id="stamp_add_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>
@endsection

@section('javascript')
<script>
      $(document).on('click', '#cheque_stamp_add', function () {
			var url = $(this).data('href');
           
			$.ajax({
				method: 'GET',
				dataType: 'html',
				url: url,
				success: function (response) {
                    console.log(response);
					$("#stamp_add_modal").html(response).modal('show');
				}
			});
		});
    //employee list
    stamps_table = $('#stamps_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("Chequer\ChequerStampController@index")}}',
            data: {}
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'stamp_image', name: 'stamp_image' },
            { data: 'stamp_name', name: 'stamp_name' },
            { data: 'active', name: 'active' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('click', 'a.delete_stamps', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This stamp will be deleted.',
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
                            stamps_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $('#filter_business').select2();
</script>
@endsection