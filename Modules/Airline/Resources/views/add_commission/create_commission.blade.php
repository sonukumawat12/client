 @extends('layouts.app')
@section('title', 'Add Commission')

@section('content')
    @php
   
    @endphp
      <style>
          /* Override Select2's width */
          .my-modal .select2-container--default .select2-selection--single {
              width: 100% !important;
          }
          
       .input_container {
          border: 1px solid #e5e5e5;
        }
        
        input[type=file]::file-selector-button {
          background-color: #fff;
          color: #000;
          border: 0px;
          border-right: 1px solid #e5e5e5;
          padding: 10px 15px;
          margin-right: 20px;
          transition: .5s;
        }
        
        input[type=file]::file-selector-button:hover {
          background-color: #eee;
          border: 0px;
          border-right: 1px solid #e5e5e5;
        }
      </style>
  <!--   <form action="{{ action('\Modules\Airline\Http\Controllers\AirlineTicketingController@add_commision_store') }}" method="get" >  -->
   
<section class="content main-content-inner">
    <div class="row">
        <div class="col-md-12">
           <h4>Add Your Commission</h4>
             
            
            
</div>
            
</div>
        <div class="row">
          <div class="form-group col-sm-3">
            {!! Form::label('air_ticket_date', __( 'Date' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('Air Ticket Date')}}">
                        <i class="fa fa-money"></i>
                    </button>
                </div>
                {!! Form::text('date', null, [
                    'class' => 'form-control mousetrap',
                    'id' => 'date',
                    'placeholder' => __('Air Ticket Date'), 
                    'required' => 'required',
                    'readonly' => 'readonly'
                ]) !!}
            </div>
          </div>
          <div class="form-group col-sm-3">
            {!! Form::label('location', __( 'airline::lang.location' ) . ':*') !!}
          	<div class="form-group">
					 
						{!! Form::select('location', $business_locations, !empty($type) ? $type : null , ['class' => 'form-control', 'id' =>
                        'location','placeholder'
                        => __('messages.please_select'), 'required','closeOnSelect:false'  ]); !!}
					</div>
          </div>
          <div class="form-group col-sm-3">
            {!! Form::label('airline_agent', __( 'airline::lang.airline_agent' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-user-tie"></i>
                        </button>
                    </div>
                    {!! Form::select('airline_agent', $agent, null, ['class' => 'form-control', 'id' => 'airline_agent', 'required',
                    'placeholder' => __('airline::lang.airline_agent_placeholder')]); !!}
                    
                </div>
                
                    <div class="user_fin_info" style="display:none">
                       <!--<p>Credit Limit 2300</p>-->
                        
                    </div>
          </div>
        </div>
       
       
        <div class="row">
            
            <div class="form-group col-sm-3">
           {!! Form::label('invoice_no', __( 'airline::lang.invoice_no' ) . ':*') !!}
                    {!! Form::select('invoice_no', [], null, ['class' => 'form-control select2', 'id' => 'airline_invoice_no', 'required',
                        'placeholder' => __('airline::lang.invoice_no')]); !!}
                   
                    
          </div>
           <div class="form-group col-sm-3">
                      
                {!! Form::label('prt_ticket_no', __( 'airline::lang.prt_ticket_no' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline_invoice_no')}}">
                            <i class="fa fa-file-invoice"></i>
                        </button>
                    </div>
                        {!! Form::select('prt_ticket_no', [], null, ['class' => 'form-control select2', 'id' => 'airline_ticket_no', 'required',
                        'placeholder' => __('airline::lang.prt_ticket_no')]); !!}
                </div>
             
                </div>
          
          <div class="form-group col-sm-3">
            {!! Form::label('expiry', __( 'airline::lang.air_ticket_date' ) . ':*') !!}
            <div class="input-group">
              <div class="input-group-btn">
                  <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.expiry_date')}}">
                      <i class="fa fa-money"></i>
                  </button>
              </div>
              {!! Form::date('air_ticket_dates', null, [
                    'class' => 'form-control',
                    'id' => 'air_ticket_dates',
                    'placeholder' => __('airline::lang.expiry_date_placeholder'), 
                    'required',
                ]) !!}
            </div>
          </div>
         
       
          
        </div>
         <div class="row">
                 <div class="form-group col-sm-3">
            {!! Form::label('amount', 'Amount' . ':*') !!}
            <div class="input-group">
              <div class="input-group-btn">
                  <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.price')}}">
                      <i class="fa fa-money"></i>
                  </button>
              </div>
             {{ Form::text('amount', null, ['class' => 'form-control', 'id' => 'amount', 'readonly' => 'readonly']) }}
            </div>
          </div>
        </div>
         <div class="col-md-12 repeat_field">
                <button type="button" class="btn btn-danger add_ref_list pull-right">@lang('messages.add')</button>
            </div>
      
     
           
            <div class="clearfix"></div>
            <div class="col-md-12 repeat_field" style="margin-top: 10px;">
                <table class="table table-bordered table-striped" id="customer_reference_list_table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Agent</th>
                             <th>Invoice No</th>
                              <th>PTR / Ticket No</th>
                              <th>Air Ticket Date</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="input_value repeat_field">

            </div>
      <div class="modal-footer">
        <button type="submit" id="saveDataBtn" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
   <!--  </form> -->
 
  
</section>
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script>
    // Get the date input element
    var airTicketDate = document.getElementById('air_ticket_date');

    // Listen for changes in the date input
    airTicketDate.addEventListener('change', function() {
        // Get the selected date value
        var selectedDate = airTicketDate.value;

        // Show an alert message with the selected date value
        alert('You have selected ' + selectedDate);
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('.add_ref_list').click(function() {
      var airTicketDate = $('#air_ticket_dates').val();
      var amount = $('#amount').val();
      var invoice_no = $('#airline_invoice_no').val();
      var airline_agent = $('#airline_agent option:selected').text();
      var location = $('#location option:selected').text();
      var prt_ticket_no = $('#airline_ticket_no').val();
      var dates = $('#date').val();

      var newRow = '<tr>' +
        '<td>' + dates + '</td>' +
        '<td>' + location + '</td>' +
        '<td>' + airline_agent + '</td>' +
        '<td>' + invoice_no + '</td>' +
        '<td>' + prt_ticket_no + '</td>' +
        '<td>' + airTicketDate + '</td>' +
        '<td>' + amount + '</td>' +
        '<td><button class="btn btn-danger remove_row"><i class="fas fa-trash-alt"></i></button></td>' +
        '</tr>';

      $('#customer_reference_list_table tbody').append(newRow);

      // Remove selected option from the dropdown
      $('#airline_ticket_no').find('option:selected').remove();
    });

    // Event handler for removing a row
    $(document).on('click', '.remove_row', function() {
      // Get the value from the removed row
      var removedValue = $(this).closest('tr').find('td:nth-child(5)').text();

      // Add the removed value back to the dropdown
      $('#airline_ticket_no').append('<option value="' + removedValue + '">' + removedValue + '</option>');

      // Remove the row
      $(this).closest('tr').remove();
    });
    
    
        // Event handler for saving the table data
    $('#saveDataBtn').click(function() {
        console.log("save clicked");
      var tableData = [];

      $('#customer_reference_list_table tbody tr').each(function() {
        var rowData = {
          dates: $(this).find('td:nth-child(1)').text(),
          location: $(this).find('td:nth-child(2)').text(),
          airline_agent: $(this).find('td:nth-child(3)').text(),
          invoice_no: $(this).find('td:nth-child(4)').text(),
          prt_ticket_no: $(this).find('td:nth-child(5)').text(),
          airTicketDate: $(this).find('td:nth-child(6)').text(),
          amount: $(this).find('td:nth-child(7)').text(),
        };

        tableData.push(rowData);
      });

      // Send tableData to the server using AJAX or any other method
      console.log(tableData);
      // Send tableData to the server using AJAX
      $.ajax({
        url: '/airline/add_commision_store', // Replace with your server endpoint URL
        type: 'GET',
        data: { tableData: JSON.stringify(tableData) },
        success: function(response) {
           // alert(response.msg);
            toastr.success('Success');
          console.log('Data sent successfully');
          // Handle the server response here
        },
        error: function(xhr, status, error) {
              //toastr.success(response.msg);
          console.log('Error sending data:', error);
          // Handle the error here
        }
      });
    });
  });
</script>
<script>
    $(document).ready(function() {
        $('#airline_agent').on('change', function() {
            var selectedAgent = $(this).val();
            var invoiceNoSelect = $('#airline_invoice_no');

            // Make an AJAX request to the server
            $.ajax({
                url: '/airline/getInvoiceNumbers', // Replace with the actual URL to retrieve invoice numbers
                type: 'GET',
                data: {agent: selectedAgent},
                success: function(response) {
                    // Update the 'invoice_no' select element with the received options
                    invoiceNoSelect.empty();
                    $.each(response.invoiceNumbers, function(key, value) {
                        invoiceNoSelect.append($('<option></option>').attr('value', value).text(value));
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#airline_invoice_no').on('change', function() {
            
            var selectedInvoiceNo = $(this).val();
            var amountInput = $('#amount');
            var invoiceNoSelect= $('#airline_ticket_no');
            // Make an AJAX request to the server
            $.ajax({
                url: '/airline/getInvoiceno', // Retrieve the URL from 'data-url' attribute
                type: 'GET',
                data: {invoice_no: selectedInvoiceNo},
                success: function(response) {
                    // Update the 'amount' input field with the received data
                    //console.log(response.amount);
                    $('#amount').val(response.amount);
                    invoiceNoSelect.empty();
                    $.each(response.RTR_ticket_Number, function(key, value) {
                        invoiceNoSelect.append($('<option></option>').attr('value', value).text(value));
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });
</script>
 <script>
    // Get the current date and time
   var currentDate = new Date();
  
    // Format the date and time as a string
    var currentDateString = currentDate.toISOString().split('T')[0];
    var currentTimeString = currentDate.toTimeString().split(' ')[0];
 
    document.getElementById('date').value = currentDateString  + ' ' + currentTimeString;
    
  </script> 
  @endsection