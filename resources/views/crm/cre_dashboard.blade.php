@extends('layouts.app')

@section('title', __('crm::lang.cre_dashboard'))

@section('content')
<section class="content-header no-print">
    <h1>@lang('crm::lang.cre_dashboard')</h1>
</section>

<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('source', __('crm::lang.source') . ':') !!}
                    {!! Form::select('source', $sources, null, ['class' => 'form-control select2', 'id' => 'source', 'placeholder' => __('messages.all')]) !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('user_id', __('lang_v1.assigned_to') . ':') !!}
                    {!! Form::select('user_id', $users, null, ['class' => 'form-control select2', 'id' => 'user_id', 'placeholder' => __('messages.all')]) !!}
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('crm::lang.cre_dashboard_data')])
        <table class="table table-bordered table-striped" id="cre_dashboard_table">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('contact.name')</th>
                    <th>@lang('contact.mobile')</th>
                    <th>@lang('business.email')</th>
                    <th>@lang('lang_v1.assigned_to')</th>
                </tr>
            </thead>
        </table>
    @endcomponent
</section>
@endsection

@section('javascript')
    <script src="{{ url('Modules/Crm/Resources/assets/js/crm.js?v=' . $asset_v) }}"></script>
    <script>
        $(document).ready(function () {
            initializeCreDashboardTable(); // Initialize the DataTable specific to CRE Dashboard
        });
    </script>
@endsection
