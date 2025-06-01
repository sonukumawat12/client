@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.repayment', 2) }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')

    
   

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @component('header')
                        <div class="box-tools">
                            @can('loan.loans.transactions.create')
                                <a href="{{ url('repayment/create') }}" class="btn btn-info btn-sm">{{ trans_choice('core.add', 1) }}
                                    {{ trans_choice('loan::general.repayment', 1) }}</a>
                            @endcan
                        </div>
                    @endcomponent


                    @slot('slot')
                        <table id="data-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans_choice('core.entry', 1) }} {{ trans_choice('core.id', 1) }}</th>
                                    <th>{{ trans_choice('core.location', 1) }}</th>
                                    <th>{{ trans_choice('core.transaction', 1) }} {{ trans_choice('core.date', 1) }}</th>
                                    <th>{{ trans_choice('core.transaction', 1) }}#</th>
                                    <th>{{ trans_choice('core.type', 1) }}</th>
                                    <th>{{ trans_choice('core.created_by', 1) }}</th>
                                    <th>{{ trans_choice('core.account', 1) }}</th>
                                    <th>{{ trans_choice('general.debit', 1) }}</th>
                                    <th>{{ trans_choice('general.credit', 1) }}</th>
                                    <th>{{ trans_choice('core.action', 1) }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>

@stop
@section('javascript')
    <script src="{{ asset('assets/plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>
        var url = '{!! url('journal_entry/get_journal_entries') !!}';
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'location',
                    name: 'business_locations.name'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'transaction_number',
                    name: 'transaction_number'
                },
                {
                    data: 'account_type',
                    name: 'chart_of_accounts.account_type'
                },
                {
                    data: 'created_by',
                    name: 'users.first_name'
                },
                {
                    data: 'account_name',
                    name: 'chart_of_accounts.name'
                },
                {
                    data: 'debit',
                    name: 'debit'
                },
                {
                    data: 'credit',
                    name: 'credit'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            "order": [
                [0, "desc"]
            ],
            "language": {
                "lengthMenu": "{{ trans('core.lengthMenu') }}",
                "zeroRecords": "{{ trans('core.zeroRecords') }}",
                "info": "{{ trans('core.info') }}",
                "infoEmpty": "{{ trans('core.infoEmpty') }}",
                "search": "{{ trans('core.search') }}",
                "infoFiltered": "{{ trans('core.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('core.first') }}",
                    "last": "{{ trans('core.last') }}",
                    "next": "{{ trans('core.next') }}",
                    "previous": "{{ trans('core.previous') }}"
                }
            },
            responsive: false,
            "autoWidth": false,
            "drawCallback": function(settings) {
                $('.confirm').on('click', function(e) {
                    e.preventDefault();
                    var href = $(this).attr('href');
                    swal({
                        title: 'Are you sure?',
                        text: '',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok',
                        cancelButtonText: 'Cancel'
                    }).then(function() {
                        window.location = href;
                    })
                });
            }
        });
    </script>
@endsection
