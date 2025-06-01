@extends('loan::settings.layout')
@section('tab-title')
    {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.purpose', 2) }}
@endsection

@section('tab-content')
    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('title')
                        {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('loan::general.purpose', 2) }}
                        @show_tooltip(__('loan::lang.tooltip_loanpurposessettings'))
                    @endslot

                    @slot('header')
                        <div class="box-tools">
                            @can('loan.loans.purposes.create')
                                <a href="{{ url('contact_loan/purpose/create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> {{ trans_choice('core.add', 1) }}
                                    @show_tooltip(__('loan::lang.tooltip_loanpurposessettingsadd'))
                                </a>
                            @endcan
                        </div>
                    @endslot

                    @slot('slot')
                        <!-- Main content -->
                        <section class="content">
                            <div class="card">
                                <div class="card-body p-0">
                                    @if (!empty($data) && count($data) > 0)
                                        <table id="data-table" class="table table-striped table-condensed table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        {{ trans_choice('core.name', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanpurposessettingsname'))
                                                    </th>
                                                    <th>{{ trans_choice('core.action', 1) }}
                                                        @show_tooltip(__('loan::lang.tooltip_loanpurposessettingsaction'))
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $key)
                                                    <tr>
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
                                                                    @can('loan.loans.purposes.edit')
                                                                        <a href="{{ url('contact_loan/purpose/' . $key->id . '/edit') }}"
                                                                            class="dropdown-item">
                                                                            <i class="fas fa-edit"></i>
                                                                            <span>{{ trans_choice('core.edit', 1) }}
                                                                                @show_tooltip(__('loan::lang.tooltip_loanpurposessettingsedit'))</span>
                                                                        </a>
                                                                    @endcan
                                                                    <div class="dropdown-divider"></div>
                                                                    @can('loan.loans.purposes.destroy')
                                                                        <a href="{{ url('contact_loan/purpose/' . $key->id . '/destroy') }}"
                                                                            class="dropdown-item confirm">
                                                                            <i class="fas fa-trash"></i>
                                                                            <span>{{ trans_choice('core.delete', 1) }}
                                                                                @show_tooltip(__('loan::lang.tooltip_loanpurposessettingsdelete'))</span>
                                                                        </a>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                            {{ $data->links() }}
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
    <script>
        var app = new Vue({
            el: "#vue-app",
            data: {
                records: {!! json_encode($data) !!},
                selectAll: false,
                selectedRecords: []
            },
            methods: {
                selectAllRecords() {
                    this.selectedRecords = [];
                    if (this.selectAll) {
                        this.records.data.forEach(item => {
                            this.selectedRecords.push(item.id);
                        });
                    }
                },
            },
        })
    </script>
@endsection
