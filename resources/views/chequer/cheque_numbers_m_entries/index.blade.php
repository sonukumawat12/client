@extends('layouts.app')
@section('title', __('cheque.cheque_numbers_m_entries'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cheque.cheque_numbers_m_entries')</h1>
    <div class="box box-info">
        <div class="box-header">
            <i class="fa fa-filter" aria-hidden="true"></i>
            <h3 class="box-title">Filters</h3>
        </div>
        <div class="box-body">
            {{ Form::open(array('id' => 'filterForm')) }}
            <div class="row">
                <div class="col-md-3">
                    {!! Form::label('filter_bank_id',__('lang_v1.bank_account_no') . ':') !!}
                    {!! Form::select('filter_bank_id', $accounts, null, ['placeholder' =>__('cheque.select'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'filter_bank_id']); !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('cheque_no',__('cheque.cheque_number') . ':') !!}
                    {!! Form::select('cheque_no', $cheque_numbers, null, ['placeholder' =>__('cheque.select'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'cheque_no']); !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('new_cheque_number_to_print',__('cheque.cheque_numbers_m_entries') . ':') !!}
                    {!! Form::select('new_cheque_number_to_print', $cheque_numbers_m_entries, null, ['placeholder' =>__('cheque.select'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'new_cheque_number_to_print']); !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @date('m/01/Y').' - '.@date('m/t/Y') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>'form-control filter-control', 'id' => 'date_range', 'readonly']); !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('user_id',__('cheque.user') . ':') !!}
                    {!! Form::select('user_id', $cheque_numbers_m_entry_users, null, ['placeholder' =>__('cheque.select'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'user_id']); !!}
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'cheque.cheque_number_m_entries')])

    @slot('tool')
    <div class="box-tools pull-right">
        <div class="box-tools">
            <button type="button" class="btn  btn-primary"   id="cheque_number_m_entries_add" data-href="{{  action('Chequer\ChequeNumbersMEntryController@create') }}" >
                <i class="fa fa-plus"></i> @lang('messages.add')
            </button>
        </div>
    </div>
    <hr>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="cheque_number_m_entries_table">
            <thead>
                <tr>
                    <th>@lang('cheque.action')</th>
                    <th>@lang('cheque.data_tiime')</th>
                    <th>@lang('account.bank_account')</th>
                    <th>@lang('cheque.Cheque Book No')</th>
                    <th>@lang('cheque.Next_Check_Number_to_Print')</th>
                    <th>@lang('cheque.New_Cheque_Number_to_Print')</th>
                    <th>@lang('cheque.Next_Cheque_Number_to_Auto_print')</th>
                    <th>@lang('cheque.note')</th>
                    <th>@lang('cheque.user')</th>
                </tr>
            </thead>
        </table>
    </div>

    @endcomponent

    <div class="modal fade" id="cheque_number_m_entries_modal" tabindex="-1" role="dialog"></div>
    <div class="modal fade edit_modal" id="cheque_number_m_entries_edit_modal" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noteModalLabel">Note Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="noteContent"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        $('input#date_range').daterangepicker(dateRangeSettings);
        if ($('#date_range').length == 1) {
            $('#date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
                cheque_number_m_entries_table.ajax.reload();
            });
            $('#custom_date_apply_button').on('click', function() {
                if($('#target_custom_date_input').val() == "date_range"){
                    let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                    let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                    if (startDate.length === 10 && endDate.length === 10) {
                        let formattedStartDate = moment(startDate).format(moment_date_format);
                        let formattedEndDate = moment(endDate).format(moment_date_format);

                        $('#date_range').val(
                            formattedStartDate + ' - ' + formattedEndDate
                        );

                        $('#date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#date_range').data('daterangepicker').setEndDate(moment(endDate));
                        
                        cheque_number_m_entries_table.ajax.reload();

                        $('.custom_date_typing_modal').modal('hide');
                    } else {
                        alert("Please select both start and end dates.");
                    }
                }
            });
            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('#target_custom_date_input').val('date_range');
                    $('.custom_date_typing_modal').modal('show');
                }
            });
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
                cheque_number_m_entries_table.ajax.reload();
            });
            $('#date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }

        // Submit filter form on change
        $('.filter-control').on('change', function() {
            $('#filterForm').submit();
        });

        // Initialize the DataTable
        var cheque_number_m_entries_table = $('#cheque_number_m_entries_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ action("Chequer\ChequeNumbersMEntryController@index") }}',
                data: function(d) {
                    d.bank_id = $('#filter_bank_id').val();
                    d.cheque_no = $('#cheque_no').val();
                    d.date_range = $('#date_range').val();
                    d.new_cheque_number_to_print = $('#new_cheque_number_to_print').val();
                    d.user_id = $('#user_id').val();
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'date_time', name: 'date_time' },
                { data: 'bank_account', name: 'accounts.name' },
                { data: 'reference_no', name: 'cheque_numbers.reference_no' },
                { data: 'next_cheque_number_to_print', name: 'next_cheque_number_to_print' },
                { data: 'new_cheque_number_to_print', name: 'new_cheque_number_to_print' },
                { data: 'next_cheque_number_to_auto_print', name: 'next_cheque_number_to_auto_print' },
                { data: 'note', name: 'note' },
                { data: 'created_by_username', name: 'created_by_user.username' },
            ],
        });

        // Reload table data on filter change
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            cheque_number_m_entries_table.ajax.reload();
        });

        $(document).on('click', '#cheque_number_m_entries_add', function () {
            var url = $(this).data('href');
            
            $.ajax({
                method: 'GET',
                dataType: 'html',
                url: url,
                success: function (response) {
                    $("#cheque_number_m_entries_modal").html(response).modal('show');
                }
            });
        });

        $(document).on('click', '.cheque_number_m_entries_edit', function () {
            var url = $(this).data('href');
            
            $.ajax({
                method: 'GET',
                dataType: 'html',
                url: url,
                success: function (response) {
                    $("#cheque_number_m_entries_edit_modal").html(response).modal('show');
                }
            });
        });
        
        $(document).on('submit', '#frmAddNumber', function (evt) {
            evt.preventDefault();

            var formData = $(this).serialize(); // Serialize form data for submission
            
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                data: formData,
                success: function(response) {
                    $('#cheque_number_m_entries_modal').modal('hide'); // Hide the modal after submission
                    toastr.success('Cheque Numbers M. Entry added successfully!'); // Show success message
                    cheque_number_m_entries_table.ajax.reload(); // Reload the table after adding the data
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred. Please try again.'); // Show error message
                }
            });
        });

        $(document).on('submit', '#frmEditNumber', function (evt) {
            evt.preventDefault();

            var formData = $(this).serialize(); // Serialize form data for submission
            
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                data: formData,
                success: function(response) {
                    $('#cheque_number_m_entries_edit_modal').modal('hide'); // Hide the modal after submission
                    toastr.success('Cheque Numbers M. Entry updated successfully!'); // Show success message
                    cheque_number_m_entries_table.ajax.reload(); // Reload the table after adding the data
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred. Please try again.'); // Show error message
                }
            });
        });

        $(document).on('change', '#cheque_number_id', function () {
            if($('#bank_id').val() == '') {
                toastr.error('Select Bank Account.');
                return;
            }
            $.ajax({
                url: '/get-next-cheque-number',
                method: 'GET',
                data: {
                    account_id: $('#bank_id').val(),
                    cheque_number_id: $('#cheque_number_id').val(),
                },
                success: function(response) {
                    if (response.next_cheque_number) {
                        $('#next_cheque_number_to_print').val(response.next_cheque_number); // Populate the cheque number field
                    } else {
                        $('#next_cheque_number_to_print').val('');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    $('#next_cheque_number_to_print').val('');
                }
            });
        });
    });

    $(document).on('click', '.show-note', function(e) {
        e.preventDefault();
        var rawNote = $(this).data('raw-note');
        $('#noteContent').html(rawNote); // Set the raw HTML content in the modal
        $('#noteModal').modal('show'); // Show the modal
    });

    jQuery("#frmAddNumber").submit(function (evt) {
        return true;
    });

    jQuery("#frmEditNumber").submit(function (evt) {
        return true;
    });
</script>
@endsection