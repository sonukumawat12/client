@extends('layouts.app')
@section('title', __('cheque.cancel_cheque_menu'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cheque.cancel_cheque_menu')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
                </div>
            </div>
           
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('cheque_book', __( 'cheque.account_book_number' )) !!}
                    {!! Form::select('cheque_book', $cheque_books, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'airline::lang.please_select' ), 'id' => 'cheque_book']);
                    !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('cheque_number', __( 'cheque.cheque_number' )) !!}
                    {!! Form::select('cheque_number', $cheque_number, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'airline::lang.please_select' ), 'id' => 'cheque_number']);
                    !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('users', __( 'report.user' )) !!}
                    {!! Form::select('users', $users, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'airline::lang.please_select' ), 'id' => 'users']);
                    !!}
                </div>
            </div>

            </div>
            @endcomponent
        </div>
    </div>
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'cheque.list_cancel_cheque_menu')])

     @slot('tool')
    <div class="box-tools pull-right">
        <button type="button" class="btn  btn-primary"  id="cheque_stamp_add" data-href="{{action('Chequer\CancellChequeController@add')}}">
            <i class="fa fa-plus"></i> @lang('messages.add')
        </button>
    </div>
    <hr>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="cancelled_table">
            <thead>
                <tr>
                    <th>@lang('cheque.action')</th>
                    <th>@lang('cheque.data_tiime')</th>
                    <th>@lang('cheque.bank_account')</th>
                    <th>@lang('cheque.account_book_no')</th>
                    <th>@lang('cheque.cheque_no')</th>
                    <th>@lang('cheque.note')</th>
                    <th>@lang('cheque.added_user')</th>
                </tr>
            </thead>

        </table>
    </div>

    @endcomponent
</section>

<div class="modal fade" id="cancel_cheque_add_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>
    
<div class="modal fade" id="cancel_cheque_note_modal" tabindex="-1" role="dialog"></div>
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
					$("#cancel_cheque_add_modal").html(response).modal('show');
				}
			});
		});
	$(document).on('click', '.note-btn', function (event) {
    event.preventDefault(); // Prevent default anchor behavior

    var url = $(this).attr('href'); // Get the href value

    $.ajax({
        method: 'GET',
        dataType: 'html',
        url: url,
        success: function (response) {
            console.log(response); // Debugging
            $("#cancel_cheque_note_modal").html(response).modal('show');
        }
    });
});

    if ($('#date_range_filter').length == 1) {
            $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                cancelled_table.ajax.reload();
            });
            $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }

        $('#date_range_filter, #cheque_book, #cheque_number , #users').change(function() {
            cancelled_table.ajax.reload();
        })
        
</script>
<script>
   
    cancelled_table = $('#cancelled_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `{{ route('cancell_cheque_details.index')}}`,
            data: function(d) {
                d.user = $('#users').val();
                d.cheque_book = $('#cheque_book').val();
                d.cheque_number = $('#cheque_number').val();
                var start_date = $('input#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                var end_date = $('input#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                d.start_date = start_date;
                d.end_date = end_date;
            }
        },
        @include('layouts.partials.datatable_export_button')
        columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
            },
            {
                data: 'reg_datetime',
                name: 'reg_datetime'
            },
            {
                data: 'account.name',
                name: 'account_id'
            },
            {
                data: 'cheque_book_no.reference_no',
                name: 'cheque_bk_id'
            },
            
            {
                data: 'cheque_no',
                name: 'cheque_no'
            },
            {
                data: 'note',
                        searchable: false,
                        orderable: false
            },

            
            {
                data: 'user.username',
                name: 'user'
            },
        ],
        
        fnDrawCallback: function(oSettings) {

        },
    });


    $(document).on('click', 'a.delete_button', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
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
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        cancelled_table.ajax.reload();
                    },
                });
            }
        });
    });
    
</script>
<script>
    $(document).ready(function() {
    // Click event for "Add" button
    $('#addChequeButton').click(function() {
        // Load form into modal
        $.ajax({
            url: '{{ action('Chequer\CancellChequeController@create') }}',
            type: 'GET',
            success: function(response) {
                // Set the form content inside the modal
                $('#addChequeFormContent').html(response);
                // Show the modal
                $('#addChequeModal').modal('show');
            },
            error: function(xhr) {
                toastr.error('@lang("messages.something_went_wrong")');
            }
        });
    });

    // Click event for "Save" button inside the modal
    $('#saveChequeButton').click(function() {
        // Submit the form via AJAX
        var form = $('#addChequeFormContent').find('form');
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                // Close the modal
                $('#addChequeModal').modal('hide');
                // Reload the table
                cancelled_table.ajax.reload();
                // Show success message
                toastr.success(response.message);
            },
            error: function(xhr) {
                toastr.error('@lang("messages.something_went_wrong")');
            }
        });
    });
});

</script>
@endsection