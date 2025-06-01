@extends('layouts.app')
@section('title', "Manual Bill")

@section('content')

<div class="container">
    <h4 class="mb-4">Customer Manual Bill</h4>
    
    <form method="POST" action="{{ route('manual-bill.store') }}" id="manual_bill_form">
        @csrf

        <div class="row">
            <!-- Left Form Panel -->
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th>Current Date & Time</th><td>{{ now() }}</td></tr>
                        <tr>
                            <th>Customer</th>
                            <td>
                                <div class="form-group" style="margin-bottom: 0;">
                                    {!! Form::select('customer', $customers, null, [
                                        'class' => 'form-control select2',
                                        'style' => 'width: 100%;',
                                        'id' => 'customer_dropdown'
                                    ]) !!}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Transaction Date</th>
                            <td>
                                <input type="text" id="transaction_date_range_bill" name="transaction_date_range" class="form-control" placeholder="Select date or range">

                                <!-- Hidden fields to hold values for form submission -->
                                <input type="hidden" name="transaction_date" id="transaction_date">
                                <input type="hidden" name="start_date" id="start_date">
                                <input type="hidden" name="end_date" id="end_date">
                            </td>
                        </tr>
                        <tr><th>Settlement No</th><td><div class="form-group" style="margin-bottom: 0;">
                                    {!! Form::select('settlement_no', [], null, [
                                        'class' => 'form-control select2',
                                        'style' => 'width: 100%;',
                                        'id' => 'settlement_dropdown'
                                    ]) !!}
                                </div></td></tr>
                        <tr><th>Order No</th><td><div class="form-group" style="margin-bottom: 0;">
                                    {!! Form::select('order_no', [], null, [
                                        'class' => 'form-control select2',
                                        'style' => 'width: 100%;',
                                        'id' => 'order_dropdown'
                                    ]) !!}
                                </div></td></tr>
                        <tr><th>Order Date</th><td><input type="date" name="order_date" class="form-control"></td></tr>
                        <tr><th>Vehicle No</th><td><div class="form-group" style="margin-bottom: 0;">
                                    {!! Form::select('vehicle_no', [], null, [
                                        'class' => 'form-control select2',
                                        'style' => 'width: 100%;',
                                        'id' => 'vehicle_dropdown'
                                    ]) !!}
                                </div></td></tr>
                        <tr><th>Product</th><td><div class="form-group" style="margin-bottom: 0;">
                                    {!! Form::select('products', [], null, [
                                        'class' => 'form-control select2',
                                        'style' => 'width: 100%;',
                                        'id' => 'product_dropdown'
                                    ]) !!}
                                </div></td></tr>
                        <tr><th>Qty</th><td><input type="number" name="qty" class="form-control"></td></tr>
                        <tr><th>Unit Price</th><td><input type="number" name="unit_price" step="0.01" class="form-control"></td></tr>
                        <tr><th>Total</th><td><input type="number" name="total" step="0.01" class="form-control"></td></tr>
                        <tr><th>Settlement Amount</th><td><input type="number" name="settlement_amount" step="0.01" class="form-control"></td></tr>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-warning">Save</button>
            </div>

            <!-- Right Table Panel -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('manual_bill_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('manual_bill_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                            'form-control','readonly']); !!}
                        </div>
                    </div>
                    <div>
                        <label>Customer</label>
                        <input type="text" class="form-control" name="filter_customer" id="customer_name_display" readonly>
                    </div>
                </div>

                <table class="table table-bordered table-sm" id="payment_data_table">
                    <thead class="table-light">
                        <tr>
                            <th>Transaction Date</th>
                            <th>Settlement No</th>
                            <th>Customer Order No</th>
                            <th><input type="checkbox" class="form-check-input"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@endsection
