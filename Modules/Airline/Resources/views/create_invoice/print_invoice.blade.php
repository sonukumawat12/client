<!DOCTYPE html>   
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>TRAVEL CONSULTANT</title>

    <style>

@media print {


        @font-face {
            font-family: SourceSansPro;
            src: url(SourceSansPro-Regular.ttf);
        }
        
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        
        a {
            color: #0087C3;
            text-decoration: none;
        }
        
        body {
            position: relative;
            width: 100%;
            /* height: 29.7cm; */
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-family: SourceSansPro;
        }
        
        header {
            padding: 10px 0;
            margin-bottom: 20px;
        }
        
        #logo {
            float: left;
            margin-top: 8px;
        }
        
        #logo img {
            height: 100px;
        }
        
        #company {
            float: right;
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
        
        #client .to {
            color: #777777;
        }
        
        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }


        .grid-container {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                border: 1px solid #000;
                padding: 20px;
                margin-bottom: 20px;
            }
   
            .grid-item {
                padding: 10px;
                border: 1px solid #ccc;
                background-color: #f9f9f9;
            }

            .grid-container_2 {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 20px;
            }
            .grid-item_2 {
                padding: 10px;
                border: 1px solid #ccc;
                background-color: #f9f9f9;
            }

            .grid-item_3 {
                padding: 0px;
                border: 1px solid #ccc;
                min-height: 250px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            table, th, td {
                border: 1px solid #ccc;
            }

            th, td {
                padding: 8px;
                text-align: left;
            }
        
      
            .disclaimer {
                background-color: #ffffff !important; 
                color: #777777;
            }

            footer {
                background-color: #ffcc66 !important; 
                color: #000; 
                text-align: center;
                padding: 10px;
                font-size: 14px;
            }

            footer span.hotline {
                font-weight: bold;
            }

            footer span.copyright {
                font-style: italic;
            }

            .row {
                display: flex;
                flex-wrap: wrap;
                padding: 4px; 
            }
            
            .col {
                flex: 1;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
            }
            
            .col-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            
            .col-4 {
                flex: 0 0 33.33%;
                max-width: 33.33%;
                padding: 2px;
            }
            
            .col-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .col {
                border: 1px solid #ccc;
                padding: 20px;
                background-color: #f9f9f9;
                margin-bottom: 10px;
            }


        }
    </style>
</head>

<body>
    <header class="clearfix">
        <div id="logo">
            <img src="{{ asset('img/print/logo_up.png') }}">
        </div>
        <div id="company">

        </div>
        <div id="company">
            <h2 class="name">TRAVEL CONSULTANT</h2>
            <div>Staff Name</div>
            <div>Staff Contact Number</div>
            <div><a >Branch Contact</a></div>
        </div>
        </div>
        <img src="{{ asset('img/print/line.png') }}" width="100%">

    </header>

    <div style=" border: 1px solid #ccc; padding: 20px;">
        <div class="row">
            <div class="col-6">
                <h3>Your booking is confirmed</h3>
                <h4>Thank you for booking through us.</h4>
            </div>
            <div class="col-6">
                <h3>{{ $invoice[0]->airline_invoice_no }}</h3>
                <h4>Galileo booking reference</h4>
            </div>
        </div>



        <div class="row">
            <div class="col-12">
                <p>Passenger(s) details</p>
            </div>

            @foreach($passenger as $passenger_val)
            <div class="col-6">
                <h3>{{ $passenger_val->name }} </h3>
                <small> {{ $passenger_val->passenger_type }}</small>
            </div>
            @endforeach
           
        </div>

    </div>





    <p style="float: right">Ticket Number: {{ $invoice[0]->airticket_no }}</p>
    
   
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Flight</th>
                <th>Class</th>
                <th>From</th>
                <th>To</th>
                <th>Depart</th>
                <th>Arrival</th>
                <th>Airline Reference : AAAA</th>
            </tr>
        </thead>
        <tbody>

          @foreach($invoice as $invoice_val)
            <tr>
                <td>{{ $invoice_val->departure_date }}</td>
                <td>{{ $invoice_val->airline }}</td>
                <td>{{ $invoice_val->airline }}</td>
                <td>{{ $invoice_val->airline }}</td>
                <td>{{ $invoice_val->airline }}</td>
                <td>{{ $invoice_val->airline }}</td>
                <td>{{ $invoice_val->airline }}</td>
                <td>
                    <p>Checked Baggage:</p>
                    <p>Hand Baggage:</p>
                    <p>Inf. Hand Baggage</p>
                    <p>Meal:</p>

                </td>
            </tr>

            @endforeach
        </tbody>
    </table>

    <div style=" padding: 10px;">


        <div class="row">
            <div class="col-6">
                <div class="grid-item_2">
                    <strong>Notes</strong>
                    <p>This Ticket is Non-Refundable.</p>
                </div>
                <div class="grid-item_3" style="padding: 4px;">
                    <strong>Passenger(s) Total <small style="float:right">KWD 200.500</small></strong>
                    <hr>
                    <div class="row">
                        <div class="col-4" style="padding:5px">Ticket Fare Adult</div>
                        <div class="col-4" style="padding:5px">x2</div>
                        <div class="col-4" style="padding:5px"><small style="float:right">KWD 200.500</small></div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="padding:5px">Ticket Fare Child</div>
                        <div class="col-4" style="padding:5px">x2</div>
                        <div class="col-4" style="padding:5px"><small style="float:right">KWD 200.500</small></div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="padding:5px">Ticket Fare Infant</div>
                        <div class="col-4" style="padding:5px">x1</div>
                        <div class="col-4" style="padding:5px"></div>
                    </div>
                    <div class="row">
                        <div class="col-4" style="padding:5px">Other Services</div>
                        <div class="col-4" style="padding:5px"></div>
                        <div class="col-4" style="padding:5px"><small style="float:right">KWD 200.500</small></div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="grid-item_2">
                    <strong>Payment Reference #000000 <small style="float:right">Invoice #{{ $invoice[0]->airline_invoice_no }}</small></strong>
                    <p>This Ticket is Non-Refundable.</p>
                </div>
               @if (!empty($supplier))
                   <div class="grid-item_2">
                        <strong>Supplier Info</strong>
                        <small style="float:right">{{ $supplier->name ?? 'N/A' }}</small>
                        <small style="float:right">{{ $supplier->contact_id ?? 'N/A' }}</small>
                    </div>
                @endif

                <div class="grid-item_3" >
                   @php
                        // Default payment amount is 0
                        $totalPaymentAmount = 0;
                    
                        // Check if the payment array exists and loop through it
                        if (!empty($payment)) {
                            foreach ($payment as $pay) {
                                // Access amount using array notation if $pay is an array
                                $amount = (float)($pay['amount'] ?? 0); // Cast to float for numeric operations
                    
                                // Add the amount to the total
                                $totalPaymentAmount += $amount;
                            }
                        }
                    
                        // Get transaction total or default to 0 if it doesn't exist
                        $transactionTotal = (float)($transaction->final_total ?? 0); // Ensure it's a float
                        
                        // Calculate total
                        $total = $totalPaymentAmount + $transactionTotal; // Add payment total and transaction total

                    @endphp



                        <div class="col-12"><strong>Total Booking Amount <small style="float:right">{{$total}}</small></strong> </div>
                        <div class="col-12"><strong>Passenger Payment <small style="float:right">{{$transactionTotal}}</small></strong> </div>
                        <div class="col-12"><strong>Supplier Payment<small style="float:right">{{$totalPaymentAmount}}</small></strong> </div>
                        <!--<div class="col-12"><strong>Amount Paid <small style="float:right">KWD 197.500</small></strong> </div>-->
                        <!--<div class="col-12"><strong>Balance <small style="float:right">KWD 0.000</small></strong> </div>-->
                
    
    
    
    
                </div>
            </div>
        </div>


    </div>

   <!-- <p>Disclaimer: We are not liable for fare fluctuations after ticket issuance, as fares are subject to changes due to airline policies and market conditions. By booking with us, you acknowledge and accept this condition</p>

    <footer>
        Careem Tours and Travels 2024. All rights reserved HOTLINE: +965 66619203 | +965 66963020
    </footer> -->

    <div class="disclaimer">
        Disclaimer: We are not liable for fare fluctuations after ticket issuance, as fares are subject to changes due to airline policies and market conditions. By booking with us, you acknowledge and accept this condition.
    </div>

    <!-- Footer section -->
    <footer>
        <span class="copyright">© Careem Tours and Travels 2024. All rights reserved.</span>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <span class="hotline">HOTLINE: +965 66619203 | +965 66963020</span>
    </footer>



</body>

</html>
