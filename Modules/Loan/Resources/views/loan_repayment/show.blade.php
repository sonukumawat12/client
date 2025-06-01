@extends('layouts.app')
@section('title')
    {{ trans_choice('core.view', 1) }} {{ trans_choice('core.transaction', 1) }}
@endsection

@section('content')

    
    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @component('title')
                        {{ trans_choice('core.transaction', 1) }} # {{ $data->first()->transaction_number }}
                    @endcomponent

                    @component('header')
                        <div class="box-tools">
                            @if ($data->first()->reversed == 0 && $data->first()->reversible == 1)
                                <a href="{{ url('journal_entry/' . $data->first()->transaction_number . '/reverse') }}"
                                    class="btn btn-danger btn-sm confirm">{{ trans_choice('general.reverse', 1) }}
                                </a>
                            @else
                                <span class="text-danger">{{ trans_choice('core.transaction', 1) }}
                                    {{ trans_choice('general.reversed', 1) }}
                                </span>
                            @endif
                        </div>
                    @endcomponent


                    @slot('slot')
                        <table class="table">
                            <tr>
                                <td>{{ trans_choice('core.location', 1) }}</td>
                                <td>
                                    @if (!empty($data->first()->location))
                                        {{ $data->first()->location->name }}
                                    @endif
                                </td>
                                <td>{{ trans_choice('core.transaction', 1) }} {{ trans_choice('core.date', 1) }}</td>
                                <td>
                                    {{ $data->first()->date }}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('core.created_by', 1) }}</td>
                                <td>
                                    @if (!empty($data->first()->created_by))
                                        {{ $data->first()->created_by->first_name }} {{ $data->first()->created_by->last_name }}
                                    @else
                                        {{ trans_choice('core.system', 1) }}
                                    @endif
                                </td>
                                <td>{{ trans_choice('core.created_on', 1) }}</td>
                                <td>
                                    {{ $data->first()->created_at }}
                                </td>
                            </tr>
                        </table>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th> {{ trans_choice('core.id', 1) }}</th>
                                    <th> {{ trans_choice('core.type', 1) }}</th>
                                    <th> {{ trans_choice('core.account', 1) }}</th>
                                    <th> {{ trans_choice('general.debit', 1) }}</th>
                                    <th> {{ trans_choice('general.credit', 1) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key)
                                    <tr>
                                        <td>{{ $key->id }}</td>
                                        <td>
                                            @if (!empty($key->chart_of_account))
                                                @if ($key->chart_of_account->account_type == 'asset')
                                                    {{ trans_choice('general.asset', 1) }}
                                                @elseif ($key->chart_of_account->account_type == 'expense')
                                                    {{ trans_choice('general.expense', 1) }}
                                                @elseif ($key->chart_of_account->account_type == 'equity')
                                                    {{ trans_choice('general.equity', 1) }}
                                                @elseif ($key->chart_of_account->account_type == 'liability')
                                                    {{ trans_choice('general.liability', 1) }}
                                                @elseif ($key->chart_of_account->account_type == 'income')
                                                    {{ trans_choice('general.income', 1) }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($key->chart_of_account))
                                                {{ $key->chart_of_account->name }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($key->debit))
                                                {{ number_format($key->debit, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($key->credit))
                                                {{ number_format($key->credit, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>

@stop
@section('javascript')
@endsection
