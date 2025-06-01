<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hos S</title>
    <link rel="stylesheet" media="print" href="{{ asset('v2/css/print.css') }}" />
    {{-- @include('layouts.partials.css') --}}
    <style>
        div#container {
            font: normal 12px Arial, Helvetica, Sans-serif;
            background: white;
            display: inline-flex;
            width: 100%; 
        }
        table {    
            border-collapse: collapse;
            width: 99%;
            border: 1px solid #b3b1b4;
            margin: auto;
        }

        table tbody td {
            color: #6f6f6e;
            font-size: 11px;
            padding-top: 7px;
            font-weight: 500;
            height: 15px;
            border-right: 1px solid #80808061;
            border-bottom: .5px solid #80808061;
        }
        table tfoot th {
            color: #6f6f6e;
            font-size: 11px;
            padding-top: 7px;
            font-weight: 500;
            height: 15px;
            text-align: right;
            border-right: 1px solid #80808061;
            border-bottom: .5px solid #80808061;
        }
        table tfoot td{
            color: #6f6f6e;
            font-size: 11px;
            padding-top: 7px;
            font-weight: 500;
            height: 15px;
            border-right: 1px solid #80808061;
            border-bottom: .5px solid #80808061;
        }
        td.val{
            background: #9896960f;
            
        }
        td.total{
            text-align: right;
            font-weight: 600;
        }

        table thead th {
            border: .5px solid black;
            background-color: #969698;
            color: #e5e5e6;
            text-align: left;
            padding-left: 22px;
            text-transform: uppercase;
            height: 28px;

        }

        tfoot {
            page-break-after: always !important;
        }
        div.row{
            width: 100%;
            display: inline-flex;
        }
        
        div.col-6{
            width:50%;
        }
        div#bar-code-box{
            float: right;
            margin-top: 10px;
            margin-bottom: 10px;
            margin-right: 5px;
            border: .5px solid #80808070;
        }

        @media print {

            .no-print,
            .no-print * {
                display: none !important;
            }
        }

        .close_btn {
            border-radius: 0px !important;
            float: left;
            margin-bottom: 10px;
            background-color: #28a745;
            color: #fff;
            border: 1px solid #28a745;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-weight: 400;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border-radius: 0.25rem;
            margin-left: 50px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
   <div class="row">
        <div class="col-6">
            <a style=" border-radius: 0px !important; float: left; margin-bottom: 10px;" class="btn btn-success btn-sm btn-flat pull-left close_btn no-print" href="{{ action('\Modules\Shipping\Http\Controllers\ShippingController@index') }}">@lang('lang_v1.close')</a>
        </div>
        <div class="col-6">
            <div id="bar-code-box">
            {!! $shipment_barcode !!}
            <div style="text-align: center;font: message-box;">
                {{$shipment->tracking_no}}
            </div>
        </div>
        </div>
    </div>
    <div  id="container">
        <div class="col-6">
           <table>
            <thead>
                <tr data-iterate="item">
                <th>
                    1.
                    From (Shipping)
                </th>
                </tr>
            </thead>
            <tbody>
                <tr data-iterate="item">
                    <td>
                        Sender / Customer:
                    </td>
                </tr>
                <tr data-iterate="item">
                    <td class="val">
                        {{ $shipment->sender->name}}
                    </td>
                </tr>
                <tr data-iterate="item">
                    <td>
                        Mobile:
                    </td>
                </tr>
                <tr data-iterate="item">
                    <td class="val">
                        {{ $shipment->sender->mobile }}
                    </td>
                </tr>
                <tr data-iterate="item">
                    <td>
                        Address:
                    </td>
                </tr>
                <tr data-iterate="item">
                    <td class="val">
                        {{ $shipment->sender->address}}
                    </td>
                </tr>
                <tr data-iterate="item">
                    <td class="val">
                        {{ $shipment->sender->address_2}}
                    </td>
                </tr>
                <tr data-iterate="item">
                    <td class="val">
                        {{ $shipment->sender->address_3}}
                    </td>
                </tr>
                
                
                
            </tbody>
           </table>
            
            <table>
                <thead>
                    <tr data-iterate="item">
                        <th colspan="6">
                            4.
                            Shipment Details
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shipment->lineitem as $item)
                    <tr data-iterate="item">
                        <td colspan="2">
                            Package name
                        </td>
                        <td>
                            Length(cm)
                        </td>
                        <td>
                            Width(cm)
                        </td>
                        <td>
                            Height(cm) 
                        </td>
                        <td>
                            Weight(kg)
                        </td>
                    </tr>
                        
                    <tr data-iterate="item">
                        <td class="val" colspan="2">
                            {{$item->package_name }}
                        </td>
                        <td class="val">
                            {{numberFormate($item->length) }}
                        </td>
                        <td class="val">
                            {{numberFormate($item->width) }}
                        </td>
                        <td class="val">
                            {{numberFormate($item->height) }}
                        </td>
                        <td class="val">
                            {{numberFormate($item->weight) }}
                        </td>
                    </tr>
                    <tr data-iterate="item">
                    <td colspan="2">
                            Package Description
                    </td>
                        <td>
                            Shipping Charge
                        </td>
                        <td>
                            Service Fee
                        </td>
                        <td>
                            Declared Value
                        </td>
                        <td>
                            Total
                        </td>
                    </tr>
                    <tr data-iterate="item">
                        <td class="val" colspan="2">
                            {{ $item->package_description}}
                        </td>
                        <td class="val" >
                           {{ $shipment->location->currency->symbol.numberFormate($item->shipping_charge)}} 
                        </td>
                        <td class="val">
                            {{ $shipment->location->currency->symbol.numberFormate($item->service_fee) }} 
                        
                        </td>
                        <td class="val">
                            {{ $shipment->location->currency->symbol.numberFormate($item->declared_value)}}
                        </td>
                        <td class="val">
                            {{ $shipment->location->currency->symbol.numberFormate($item->total) }}
                        </td>
                    </tr>
                    @endforeach
                   
                </tbody>
            
                <tfoot>
                    <tr data-iterate="item">
                    <th colspan="3">
                        No. Of Packages
                        
                    </th>
                    <td class="val">
                        {{ $shipment->lineitem->count()}}
                    </td> 
                    <th>
                        Grand Total:
                    </th>  
                    <td class="val">
                        {{ $shipment->location->currency->symbol.numberFormate($shipment->total)}}
                    </td> 

                    
                    </tr>
                </tfoot>
            </table>
         

        </div>
        <div class="col-6">
            
            <table>
                <thead>
                    <tr data-iterate="item">
                    <th colspan="2">
                        2.
                        Shipment Collected By
                    </th>
                    <th colspan="4">
                        Delevery
                    </th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-iterate="item">
                        <td>
                            Location:
                        </td>
                        <td>
                            Agent:
                        </td>
                        <td>
                            Shipping Mode:
                        </td>
                        <td>
                            Package:
                        </td>
                        <td>
                            Shipping Partner:
                        </td>
                        <td>
                            Driver:
                        </td>
                    </tr>
                    <tr data-iterate="item">
                        <td class="val">
                            {{ $shipment->location->name}}
                        </td>
                        <td class="val">
                            {{ $shipment->agent->name}}
                        </td>
                        <td class="val">
                            {{ $shipment->mode->shipping_mode}}
                        </td>
                        <td class="val">
                            {{ $shipment->package->package_name}}
                        </td>
                        <td class="val">
                            {{ $shipment->partner->name}}
                           
                        </td>
                        <td class="val">
                            {{ $shipment->driver->driver_name}}
                             
                        </td>
                        
                    </tr>
                    
                </tbody>
            </table>

            <table>
                <thead>
                    <tr data-iterate="item">
                    <th colspan="2">
                        3.To (Receiver)
                        
                    </th>
                    
                    </tr>
                </thead>
                <tbody>
                    <tr data-iterate="item">
                        <td colspan="2">
                           Name:
                        </td>
                        
                    </tr>
                    <tr data-iterate="item">
                       <td class="val" colspan="2">
                           {{ $shipment->receiver->name}}
                       </td>
                    </tr>
                    <tr data-iterate="item">
                        <td colspan="2">
                            Address:
                        </td>
                        
                    </tr>
                    <tr data-iterate="item">
                       <td class="val" colspan="2">
                        {{ $shipment->receiver->address}}
                       
                       </td>
                    </tr>
                    <tr data-iterate="item">
                        <td>
                            Mobile No 1:
                        </td>
                        <td>
                            Mobile No 2:
                        </td>
                        
                    </tr>
                    <tr data-iterate="item">
                       <td class="val">
                        {{ $shipment->receiver->mobile_1}}
                       </td>
                       <td class="val">
                        {{ $shipment->receiver->mobile_2}}
                       </td>
                    </tr>

                    <tr data-iterate="item">
                        <td>
                            Postal Code:
                        </td>
                        <td>
                            Land Number:
                        </td>
                    </tr>

                    <tr data-iterate="item">
                        <td class="val">
                        {{ $shipment->receiver->postal_code}}
                            
                        </td>
                        <td class="val">
                        {{ $shipment->receiver->land_no}}
                            
                        </td>
                    </tr>

                    <tr data-iterate="item">
                        <td colspan="2">
                            Landmarks:
                        </td>
                       
                        
                    </tr>
                    <tr data-iterate="item">
                        <td class="val" colspan="2">
                        {{ $shipment->receiver->landmarks}}
                        </td>
                       
                    </tr>
                </tbody>
            </table>
            
            <table>
                <thead>
                    <tr data-iterate="item">
                        <th colspan="3">
                            5. Note
                        </th>
                        <th>
                            6. Payment Method
                            
                        </th>
                        <th>
                            7. Payment
                        </th>
                    </tr>
                </thead>
                <tbody>
                    
                    <tr data-iterate="item">
                        <td class="val" colspan="3">
                            {{ $shipment->transection->additional_notes }}
                   
                        </td>
                        
                        <td>
                            {{ $shipment->transection->is_credit_sale == 0 ? 'Cash': 'Credit' }}
                        </td>
                        <td>
                            {{ $shipment->location->currency->symbol.numberFormate($shipment->transection->final_total) }}
                        </td>
                        
                        
                    </tr>
                    
                   
                </tbody>
            </table>

        </div>
       
    </div>
    
<script>
    // window.print();
    // window.onafterprint = function(event) {
    // document.location.href = "{{route('print_terms_condition')}}";
    // } 
</script>
</body>

</html>
