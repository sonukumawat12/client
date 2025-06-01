@extends('layouts.app')
@section('title', __('expense.expense_categories_code'))

@section('content')

<!-- Content Header (Page header) -->
<br/>
<ul class="nav nav-tabs">
    <li class="@if(empty(session('status.tab'))) active @endif">
        <a href="#category_settings" class="category_settings" data-toggle="tab">
            <i class="fa fa-file-text-o"></i>
            <strong>@lang('expense.category_settings')</strong>
        </a>
    </li>
    <!-- <li class="@if(session('status.tab') =='task') active @endif">
        <a href="#expense_settings" class="expense_settings" data-toggle="tab">
            <i class="fa fa-file-text-o"></i>
            <strong>@lang('expense.expense_settings')</strong>
        </a>
    </li> -->
</ul>

<!-- Main content -->
<section class="content">
    <div class="tab-content">

        <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="category_settings">
            @component('components.widget', ['class' => 'box-primary', 'title' => __( 'expense.category_settings' )])
                @slot('tool')
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-primary btn-modal" 
                        data-href="{{action('ExpenseCategoryCodeController@create')}}" 
                        data-container=".expense_category_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add')</button>
                    </div>
                @endslot

            @endcomponent
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="expense_category_code_table">
                    <thead>
                        <tr>
                            <th>@lang( 'expense.date' )</th>
                            <th>@lang( 'expense.prefix' )</th>
                            <th>@lang( 'expense.starting_no' )</th>
                            <th>@lang( 'expense.user' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        {{--<div class="tab-pane @if(session('status.tab') =='task') active @endif" id="expense_settings">
            @component('components.widget', ['class' => 'box-primary', 'title' => __( 'expense.expense_settings' )])
                @slot('tool')
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-primary btn-modal" 
                        data-href="{{action('ExpenseCategoryNumberController@create')}}" 
                        data-container=".expense_category_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add')</button>
                    </div>
                @endslot

            @endcomponent
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="expense_category_code_table1" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang( 'expense.date' )</th>
                            <th>@lang( 'expense.prefix' )</th>
                            <th>@lang( 'expense.starting_no' )</th>
                            <th>@lang( 'expense.user' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>--}}
    </div>
    <div class="modal fade expense_category_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>


<!-- /.content -->

@endsection
