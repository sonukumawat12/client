@extends('layouts.app')
@section('title', __('hms::lang.extras'))
@section('content')
   
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black"> @lang('hms::lang.extras')
        </h1>
        <p><i class="fa fa-info-circle"></i> @lang('hms::lang.extra_help_text') </p>
    </section>

    <!-- Main content -->
    <!-- style btn btn-primary pull-right all-p-btn //tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal-extra-->
    <section class="content">
        @component('components.widget')
               @slot('tool')
    <div class="box-tools pull-right">
        <button type="button" class="btn  btn-primary"  id="cheque_stamp_add" data-href="{{action('\Modules\Hms\Http\Controllers\ExtraController@create')}}">
            <i class="fa fa-plus"></i> @lang('messages.add')
        </button>
    </div>
    
    @endslot
            
            <table class="table table-bordered table-striped" id="extras_table">
                <thead>
                    <tr>
                        <th>
                            @lang('hms::lang.name')
                        </th>
                        <th>
                            @lang('hms::lang.price')
                        </th>
                        <th>
                            @lang('lang_v1.created_at')
                        </th>
                        <th>
                            @lang('messages.action')
                        </th>
                    </tr>
                </thead>
            </table>
        @endcomponent

        <!-- Add HMS Extra Modal -->
        <div class="modal fade" id="cancel_cheque_add_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    </section>
    <!-- /.content -->

@endsection

@section('javascript')

    <script type="text/javascript">
        $(document).ready(function() {
             $(document).on('click', '#cheque_stamp_add', function () {
			var url = $(this).data('href');
           
			$.ajax({
				method: 'GET',
				dataType: 'html',
				url: url,
				success: function (response) {
                    console.log(response);
					$("#cancel_cheque_add_modal").html(response).modal('show');
				}
			});
		});
        $(document).on('click', '#edit_extra', function () {
			var url = $(this).data('href');
           
			$.ajax({
				method: 'GET',
				dataType: 'html',
				url: url,
				success: function (response) {
                    console.log(response);
					$("#edit_modal").html(response).modal('show');
				}
			});
		});
            superadmin_business_table = $('#extras_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ action([\Modules\Hms\Http\Controllers\ExtraController::class, 'index']) }}",
                },
                aaSorting: [
                    [2, 'desc']
                ],
                columns: [{
                        data: 'name',
                        name: 'hms_extras.name'
                    },
                    {
                        data: 'price',
                        name: 'hms_extras.price'
                    },
                    {
                        data: 'created_at',
                        name: 'hms_extras.created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        sorting: false,
                    }
                ],
            });

            $(document).on('click', '.btn-modal-extra', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('href'),
                    dataType: 'html',
                    success: function(result) {
                        $('.view_modal_extra')
                            .html(result)
                            .modal('show');
                    },
                });
            });

            $(document).on('click', 'a.delete_extra_confirmation', function(e) {
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    text: "Once deleted, you will not be able to recover this Extra !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((confirmed) => {
                    if (confirmed) {
                        window.location.href = $(this).attr('href');
                    }
                });
            });
        });
    </script>
@endsection
