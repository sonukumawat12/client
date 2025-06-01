@extends('layouts.app')
@section('title', __('cheque.cheque_number'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cheque.cheque_number')</h1>
     <div class="box box-info">
        <div class="box-header">
            <i class="fa fa-filter" aria-hidden="true"></i>
            <h3 class="box-title">Filters</h3>
        </div>
        <div class="box-body">
            {{ Form::open(array('id' => 'filterForm')) }}
            <div class="row">
                <div class="col-md-3">
                    {!! Form::label('bank_acount_no',__('lang_v1.bank_account_no') . ':') !!}
                    {!! Form::select('bank_acount_no', $accounts, ($defaultVal)?$defaultVal['bank_acount_no']:null, ['placeholder' =>__('report.all'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'bank_acount_no']); !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('cheque_no',__('cheque.cheque_number') . ':') !!}
                    {!! Form::select('cheque_no', $chequeNumbers, ($defaultVal)?$defaultVal['cheque_no']:null, ['placeholder' =>__('report.all'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'cheque_no']); !!}
                </div>
            
            <div class="row">
                <div class="col-md-3">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', 'All', ['placeholder' => __('All'), 'class' => 'form-control filter-control', 'id' => 'date_range', 'readonly']) !!}
                </div>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'cheque.cheque_number_list')])

    @slot('tool')
    <div class="box-tools pull-right">
        <div class="box-tools">
            <button type="button" class="btn  btn-primary"   id="cheque_number_add" data-href="{{  action('Chequer\ChequeNumberController@create') }}" >
                <i class="fa fa-plus"></i> @lang('messages.add')
            </button>
        </div>
    </div>
    <hr>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="cheque_number_table">
            <thead>
                <tr>
                    <th>@lang('cheque.action')</th>
                    <th>@lang('cheque.data_tiime')</th>
                    <th>@lang('account.bank_account')</th>
                    <th>@lang('cheque.Cheque Book No')</th>
                    <th>@lang('cheque.first_cheque_number')</th>
                    <th>@lang('cheque.last_cheque_number')</th>
                    <th>@lang('cheque.no_of_cheque_leaves')</th>
                    <th>@lang('cheque.user')</th>
                </tr>
            </thead>

        </table>
    </div>

    @endcomponent

    
    <div class="modal fade" id="cheque_number_modal" tabindex="-1" role="dialog"></div>
    <div class="modal fade edit_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    //$('input#date_range').daterangepicker(dateRangeSettings);
 if ($('#date_range').length == 1) {
    let dateRangeSettings = {
        showDropdowns: true,
        autoUpdateInput: false, // Prevents auto-filling dates
        locale: {
            format: moment_date_format,
            cancelLabel: 'Clear'
        },
        ranges: {
            'All': [null, null], // Represents no filtering (All)
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        'This Month Last Year': [moment().subtract(1, 'year').startOf('month'), moment().subtract(1, 'year').endOf('month')],
        'This Year': [moment().startOf('year'), moment().endOf('year')],
        'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
        'Current Financial Year': [moment().month(6).startOf('month'), moment().month(5).endOf('month').add(1, 'year')],
        'Last Financial Year': [moment().month(6).startOf('month').subtract(1, 'year'), moment().month(5).endOf('month')]
        }
    };

    $('#date_range').daterangepicker(dateRangeSettings, function(start, end, label) {
        if (label === 'All') {
            $('#date_range').val('All'); // Set "All" when selected
        } else {
            $('#date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        }
    });

    // Set default value as "All"
    $('#date_range').val('All');

    // Reset to "All" when the cancel button is clicked
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range').val('All');
    });

    // Ensure the picker defaults to "All"
    $('#date_range').data('daterangepicker').setStartDate(moment().subtract(10, 'years')); // Just a placeholder
    $('#date_range').data('daterangepicker').setEndDate(moment()); // Placeholder to show the picker
}
    
    
    // Initialize the DataTable
    var cheque_number_table = $('#cheque_number_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ action("Chequer\ChequeNumberController@index") }}',
            data: function(d) {
                d.bank_account_no = $('#bank_acount_no').val();
                d.cheque_no = $('#cheque_no').val();
                d.date_range = $('#date_range').val();
            }
        },
        columns: [
            { data: 'action', name: 'action' },
            { data: 'date_time', name: 'date_time' },
            { data: 'name', name: 'name' },
            { data: 'reference_no', name: 'reference_no' },
            { data: 'first_cheque_no', name: 'first_cheque_no' },
            { data: 'last_cheque_no', name: 'last_cheque_no' },
            { data: 'no_of_cheque_leaves', name: 'no_of_cheque_leaves' },
            { data: 'username', name: 'username' },
        ],
    });

      // Reload table data on filter change
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        cheque_number_table.ajax.reload();
    });

    $(document).on('click', '#cheque_number_add', function () {
        var url = $(this).data('href');
        
        $.ajax({
            method: 'GET',
            dataType: 'html',
            url: url,
            success: function (response) {
                $("#cheque_number_modal").html(response).modal('show');
            }
        });
    });
    
    // Validate and submit the form inside the modal
    $(document).on('submit', '#frmAddNumber', function (evt) {
        evt.preventDefault();
        if (check_cheque_number() === false) {
            return false;
        }

        var formData = $(this).serialize(); // Serialize form data for submission
        
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            data: formData,
            success: function(response) {
                cheque_number_table.ajax.reload(); // Reload the table after adding the data
                $('#cheque_number_modal').modal('hide'); // Hide the modal after submission
                toastr.success('Cheque Number added successfully!'); // Show success message
            },
            error: function(xhr, status, error) {
                toastr.error('An error occurred. Please try again.'); // Show error message
            }
        });
    });

  // Validate cheque numbers
    function check_cheque_number(){
        var frist,last,leaves;
        if($('#first_cheque_no').val()=='' || parseInt($('#first_cheque_no').val())<0){
            toastr.error('First Cheque Number is invalid.');
            return false;
        }
        if($('#last_cheque_no').val()=='' || parseInt($('#last_cheque_no').val())<0){
            toastr.error('Last Cheque Number is invalid.');
            return false;
        }
        frist = parseInt($('#first_cheque_no').val());
        last = parseInt($('#last_cheque_no').val());
        leaves = last-frist+1;
        if(leaves<1){
            toastr.error('Please check Cheque Number.');
            return false;
        }
        $('#no_of_cheque_leaves').val(leaves);
        return true;
    }
});



    jQuery("#frmAddNumber").submit(function (evt) {
        if (check_cheque_number()==false) {
             evt.preventDefault();
             return false;
        }
        return true;
        // alert("Values are correct!");
    });
    $(document).on('click', 'a.delete_employee', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This template will be deleted.',
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
                            cheque_number_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    // $('#account_number').select2();
</script>
@endsection