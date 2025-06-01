@extends('loan::settings.layout')
@section('tab-title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.status', 2) }}
@endsection

@section('tab-content')
    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('title')
                        {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.status', 2) }}
                    @endslot

                    @slot('header')
                        <div class="box-tools">
                            @can('loan.loans.statuses.create')
                                <a href="{{ url('contact_loan/status/create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> {{ trans_choice('core.add', 1) }}
                                </a>
                            @endcan
                        </div>
                    @endslot

                    @slot('slot')
                        <!-- Main content -->
                        <section class="content">
                            <div class="card">
                                <div class="card-body p-0">
                                    @if (!empty($loan_statuses) && count($loan_statuses) > 0)
                                        <table id="data-table" class="table table-striped table-condensed table-hover datatable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        {{ trans_choice('core.parent', 1) }}
                                                        {{ trans_choice('core.status', 1) }}
                                                    </th>
                                                    <th>
                                                        {{ trans_choice('core.name', 1) }}
                                                    </th>
                                                    <th>{{ trans_choice('core.action', 1) }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($loan_statuses as $key)
                                                    <tr>
                                                        <td>
                                                            <span>{{ $key->parent_status_label }}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{ $key->name }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button href="#" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown"
                                                                    aria-expanded="false">{{ trans_choice('core.action', 1) }}
                                                                    <span class="caret"></span><span class="sr-only"></span>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    @can('loan.loans.statuses.edit')
                                                                        <a href="{{ url('contact_loan/status/' . $key->id . '/edit') }}" class="dropdown-item">
                                                                            <i class="fas fa-edit"></i>
                                                                            <span>{{ trans_choice('core.edit', 1) }}
                                                                        </a>
                                                                    @endcan
                                                                    <div class="dropdown-divider"></div>
                                                                    @can('loan.loans.statuses.destroy')
                                                                        <a href="#" class="dropdown-item confirm-delete"
                                                                            form_id="delete-status-{{ $key->id }}-form"
                                                                            action="{{ url('contact_loan/status/' . $key->id . '/destroy') }}">
                                                                            <i class="fas fa-trash"></i>
                                                                            <span>{{ trans_choice('core.delete', 1) }}
                                                                        </a>
                                                                        <form id="delete-status-{{ $key->id }}-form" method="post">
                                                                            @csrf
                                                                            @method('delete')
                                                                        </form>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-info text-center">
                                            {{ trans('loan::general.no_data_available_in_table') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </section>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>
@endsection

@section('tab-javascript')
@endsection
