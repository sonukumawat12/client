@extends('layouts.app')
@section('title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('core.report', 2) }}
@endsection

@section('content')

    

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('slot')
                        <section class="content">
                            <div class="card">
                                <div class="card-body p-0">
                                    <table id="data-table" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ trans_choice('core.name', 1) }}</th>
                                                <th>{{ trans_choice('core.description', 1) }}</th>
                                                <th>{{ trans_choice('core.action', 1) }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reports as $report)
                                                <tr>
                                                    <td>
                                                        <a href="{{ $report->url }}">
                                                            {{ $report->title }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{ $report->description }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ $report->url }}"><i class="fa fa-search"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                                <!-- /.box-body -->
                            </div>
                        </section>
                    @endslot
                @endcomponent
            </div>
        @endcan
    </section>

@stop
@section('javascript')
    <script>
        $('#data-table').dataTable({
            "ordering": false
        });
    </script>
@endsection
