<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Travel Consultant Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #555555;
            background: #FFFFFF;
            font-size: 14px;
        }
        header {
            padding: 10px 0;
            margin-bottom: 20px;
        }
        #logo img {
            height: 100px;
        }
        #company {
            text-align: right;
        }
        #details {
            margin-bottom: 50px;
        }
        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        .col {
            float: left;
            width: 48%;
        }
        .col-6 {
            width: 50%;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <header class="clearfix">
        <div id="logo">
            <img src="{{ asset('img/print/logo_up.png') }}">
        </div>
        <div id="company">
            <h2 class="name">TRAVEL CONSULTANT</h2>
            <div>Staff Name</div>
            <div>Staff Contact</div>
            <div>Branch Contact</div>
        </div>
    </header>

    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <h3>Customer Details</h3>
                <p><strong>Customer:</strong> {{ $customer->name }}</p>
                <p><strong>Customer Email:</strong> {{ $customer->email }}</p>
                 <p><strong>Customer Phone:</strong> {{ $customer->mobile }}</p>
            </div>
            <div id="invoice">
                <h3>Invoice No: {{ $invoiceData['airline_invoice_no'] }}</h3>
                <p><strong>Date:</strong> {{ $invoiceData['departure_date'] }}</p>
            </div>
        </div>

        <div>
            <h3>Passenger Details</h3>
           @foreach($passengers_new as $passenger)
    <p><strong>Name:</strong> {{ $passenger['name'] }}</p>
    <p><strong>Passenger Type:</strong> {{ $passenger['passenger_type'] }}</p>
    <p><strong>Price:</strong> {{ $passenger['price'] }}</p>
    <hr>
@endforeach

        </div>

        <h3>Flight Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Flight</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Class</th>
                    <th>Airline</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoiceData['airticket_no'] }}</td>
                    <td>{{ $invoiceData['departure_airport'] }} ({{ $invoiceData['departure_country'] }})</td>
                    <td>{{ $invoiceData['arrival_airport'] }} ({{ $invoiceData['arrival_country'] }})</td>
                    <td>{{ $invoiceData['departure_date'] }} {{ $invoiceData['arrival_time'] }}</td>
                    <td>{{ $invoiceData['arrival_date'] }} {{ $invoiceData['arrival_time'] }}</td>
                    <td>{{ $invoiceData['travel_mode'] }}</td>
                    <td>{{ $invoiceData['airline'] }}</td>
                </tr>
            </tbody>
        </table>

        <h3>Payment Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
              @foreach($invoiceData['formatted_payment_methods_data'] as $key => $value)
    <tr>
        <td>{{ $key ?? 'N/A' }}</td>  <!-- Display the payment method name -->
        <td>{{ $value ?? 'N/A' }}</td> <!-- Display the corresponding value -->
    </tr>
@endforeach

            </tbody>
        </table>

        <h3>Total Price: {{ $invoiceData['tot_price'] }}</h3>
    </main>

    <footer>
        <p>Disclaimer: We are not liable for fare fluctuations after ticket issuance, as fares are subject to changes due to airline policies and market conditions.</p>
        <p>© 2024 Careem Tours and Travels. Hotline: +965 66619203 | +965 66963020</p>
    </footer>
</body>
</html>
