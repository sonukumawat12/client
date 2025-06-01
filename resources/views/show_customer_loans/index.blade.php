@extends('layouts.app')
@section('title', __('contact.all_customer_loans'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('contact.all_customer_loans')
            <small>@lang('contact.manage_customer_loan')</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => 'All  Customer Loans'])
            @slot('tool')
                {{-- <div class="box-tools pull-right">
                    <a class="btn btn-primary" href="{{ action('RoleController@create') }}">
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div> --}}
                <hr>
            @endslot

            <table class="table  table-bordered table-striped text-sm" id="show_customer_loans_table">
                <thead>
                    <tr class="row-border">
                        <th>Action</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Customer Name</th>
                        <th>Amount</th>
                        <th>Added Page</th>
                        <th>Note</th>
                        <th>Added User</th>
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <!-- /.content -->
@stop
@section('javascript')
    <script type="text/javascript">
        //Roles table
        $(document).ready(function() {
            var show_customer_loans_table = $('#show_customer_loans_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/get_loan_data',
                columns: [{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'transaction_date',
                        name: 'transaction_date',
                    },

                    {
                        data: 'ref_no',
                        name: 'ref_no'
                    },

                    {
                        data: 'contact.name',
                        name: 'contact.name'
                    },

                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
{
                    data: 'is_settlement',
                    name: 'is_settlement',
                    render: function(data, type, row) {
                        console.log(data);
                        return data ? 'settlement' : 'customer_ledger';
                    }
                },
                    {{-- {
                        data: 'type',
                        name: 'type'
                    }, --}}

                    {
                        data: 'transaction_note',
                        name: 'transaction_note'
                    },

                    {
                        data: 'approved_user',
                        name: 'approved_user'
                    }

                ],

            });
            $(document).on('click', 'button.delete_customer_loan_button', function() {
                swal({
                    title: LANG.sure,
                    text: "Delete Customer Loan?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).data('href');
                        var data = $(this).serialize();

                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            data: data,
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    show_customer_loans_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection