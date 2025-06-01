<table class="table table-bordered">
    <thead>
        <tr>
            <th>Settlement Date</th>
            <th>Description</th>
            <th>Order No</th>
            <th>Balance Qty</th>
            <th>Unit Price</th>
            <th>Final Total</th>
            <th>Customer</th>
            <th>Location</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($credit_sales as $sale)
        <tr>
            <td>{{ $sale->settlement_date }}</td>
            <td>{{ $sale->description }}</td>
            <td>{{ $sale->order_no }}</td>
            <td>{{ $sale->balance_qty }}</td>
            <td>{{ $sale->unit_price }}</td>
            <td>{{ $sale->final_total }}</td>
            <td>{{ $sale->customer }}</td>
            <td>{{ $sale->location }}</td>
        </tr>
        @endforeach
    </tbody>
</table>