<body><style>
                table tr th,table tr td{
                    font-size:12px;
                }
                
                
           
            .headingblack-rightalign.col-md-10{
                width: 80%;
            }
            .headingblack,.headingblack-rightalign{
                width:100%;
                border: 1px solid black;
                border-collapse: collapse;
            }
            .headingblack th {
                background-color: #000;
                color: #fff;
                padding:5px;
                text-align:Center;
                border:1px solid #000;
            }
            .headingblack td{
                padding:5px;
                text-align:Center;
            }
            .bg_black{
                background-color: #000;
                color:#fff;
                margin-top:5px;
                padding-left:5px;
            }
            .border_black{
                border:1px solid #000;
            }
            .p-0{
                padding:0;
            }
            .headingblack-rightalign th{
                background-color: #000;
                color: #fff;
                padding:5px;
                text-align:left;
            }
            .headingblack-rightalign td{
                padding:5px;
                text-align:left;
            }
            .p-0{
                padding: 0 !important;
            }
            .mt-2{
                margin-top:5px;
            }
            </style>
                <div class="receipt">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 33%;">
                                <!-- Left table content -->
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td>{{ $data[0]->business_name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 33%;">
                                <!-- Right table content -->
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td>
                                            {!! $logo !!}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 33%;">
                                <!-- Right table content -->
                                <table style="width: 100%;margin-left:5%">
                                    <tbody>
                                        <tr>
                                            <td>Location Phone: {{ $data[0]->bl_name }}, {{ $data[0]->bl_mobile }}</td>
                                        </tr>
                                        <tr>
                                            <td>Businness Phone: {{ $data[0]->business_number }}<Br></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 27%;">
                                &nbsp;
                            </td>
                            <td style="width: 22%;">
                                <table style="width: 100%;" class="headingblack">
                                    <thead><tr><th>ACCOUNT NUMBER</th></tr></thead>
                                    <tbody>
                                        <tr><td>{{ $data[0]->agent }}</td></tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 0.1%;"></td>
                            <td style="width: 33%;">
                                <table style="width: 100%;" border="1" class="headingblack">
                                    <thead>
                                        <tr>
                                            <th>DESTINATION</th>
                                            <th>COUNTRY CODE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>DESTINATION</td><td>COUNTRY CODE</td></tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 18%;">
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td></b>No:</b>  {{ $title }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 49.1%;">
                                <table style="width: 100%;" class="headingblack-rightalign" border="1">
                                    <thead>
                                        <tr>
                                            <th colspan="2"> SHIPPER NAME AND ADDRESS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2"><b>NAME:</b> {{ $data[0]->sender }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b>COMPANY NAME:</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b>ADDRESS:</b> {{ $data[0]->address }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">&nbsp;{{ $data[0]->c_landmark }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>CITY:</b> {{ $data[0]->c_city }}</td><td><b>COUNTRY:</b> {{ $data[0]->c_country }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>ZIP CODE:</b> {{ $data[0]->c_state }}</td><td><b>TELEPHONE:</b> {{ $data[0]->mobile }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 0.1%;"></td>
                            <td style="width: 51%;">
                                <table style="width: 100%;" class="headingblack-rightalign" border="1">
                                    <thead>
                                        <tr>
                                            <th colspan="2"> RECEIVERS NAME AND ADDRESS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2"><b>NAME:</b> {{ $data[0]->recipient }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b>COMPANY NAME:</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b>ADDRESS:</b> {{ $data[0]->rec_address }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"> &nbsp;{{ $data[0]->rec_landmarks }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>CITY:</b></td><td><b>COUNTRY:</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>ZIP CODE:</b> {{ $data[0]->rec_postal_code }}</td><td><b>TELEPHONE:</b> {{ $data[0]->rec_mobile_1 }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 30%;">
                                <table style="width: 100%;" class="headingblack-rightalign" border="1">
                                    <thead>
                                        <tr>
                                            <th colspan="3">SHIPMENT DETAILS</th><th colspan="2">DIMENSIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 24%">No OF PIECES</td>
                                            <td style="width: 19%">WEIGHT</td>
                                            <td style="width: 19%">LENGTH(cm)</td>
                                            <td style="width: 19%">WIDTH(cm)</td>
                                            <td style="width: 19%">HEIGHT(cm)</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 24%">&nbsp;</td>
                                            <td style="width: 19%">{{ number_format($data[0]->weight,2) }}</td>
                                            <td style="width: 19%">{{ number_format($data[0]->length,2) }}</td>
                                            <td style="width: 19%">{{ number_format($data[0]->width,2) }}</td>
                                            <td style="width: 19%">{{ number_format($data[0]->height,2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table style="width: 100%;" class="headingblack-rightalign" border="1">
                                    <tr>
                                        <td style="width: 43.7%">Insurance</td>
                                        <td style="width: 46.3%">VALUE FOR CUSTOMS</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 43.7%">&nbsp;</td>
                                        <td style="width: 46.3%">&nbsp;</td>
                                        
                                    </tr>
                                </table>
                            </td>
                            <td style="width:1%;"></td>
                            <td style="width: 69.3%;">
                                <table style="width: 100%;" class="headingblack-rightalign">
                                    <thead>
                                        <tr>
                                            <th colspan="2">RECEIVED IN GOOD ORDER AND CONDITION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>RECEIVED BY</td>
                                            <td>DATE<br></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>SIGNATURE</td>
                                            <td>TIME<br></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%;">
                        <tr>                            
                            <td style="width: 49%;">
                                <table style="width: 100%;" >
                                    <tr>
                                        <td><input type="checkbox" /> PREPAID</td>
                                        <td><input type="checkbox" /> DOC</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><input type="checkbox" /> SHIPPER ACCOUNT</td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" /> CONSIGNMENT ACCOUNT</td>
                                        <td><input type="checkbox" /> NON DOC</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><input type="checkbox" /> CASH</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 0.1%;">
                            <td style="width: 50.9%;">
                                <table style="width: 100%;" border="1" class="headingblack">
                                    <thead>
                                        <tr><th>FULL DESCRIPTION OF GOODS (Profoma invoice required for all non doc)</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>&nbsp; {{ $data[0]->package_description }}</td></tr>
                                        <tr><th>NO CASH. CASH EQUIVALENT OR GOLD JEWELLERY ACCEPTED</th></tr>
                                    </tbody>
                                </table>
                            </td>                            
                        </tr>
                    </table>                    
                    <table style="width: 100%;">
                        <tr>                            
                            <td style="width: 49%;">
                                <table  class="headingblack-rightalign">
                                    <thead><tr><th colspan="2">SHIPPERS SIGNATURE & AUTHORIZATION</th></tr></thead>
                                    <tbody>
                                        <tr>
                                            <td>NAME</td>
                                            <td>TIME<br></td>
                                        </tr>
                                        <tr>
                                            <td>DATE</td>
                                            <td>SIGNATURE<br></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 0.1%"></td>
                            <td style="width: 50.9%;" rowspan="2">
                                <table class="headingblack-rightalign col-md-10" >
                                    <thead><tr><th>REMARKS / SPECIAL DELIVERY INSTRUCTION</th></tr></thead>
                                    <tbody>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                    </tbody>
                                </table>
                            </td>                        
                        </tr>
                        <tr>                            
                            <td style="width: 49%;">
                                <table  class="headingblack-rightalign">
                                    <thead><tr><th colspan="2">SHIPMENT RECEIVED BY CLO COURIER</th></tr></thead>
                                    <tbody>
                                        <tr>
                                            <td>NAME</td>
                                            <td>DATE<br></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>TIME<br></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </body>