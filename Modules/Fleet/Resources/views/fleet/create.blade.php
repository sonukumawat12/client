@php

    $vehicle_category = ['Cat A' => '' ];

@endphp


<div class="modal-dialog modal-xl" role="document" style="height: 95vh; overflow-y: auto">
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' =>
    action('\Modules\Fleet\Http\Controllers\FleetController@store'), 'method' =>
    'post', 'id' => 'fleet_form', 'enctype' => 'multipart/form-data' ]) !!}
    <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'fleet::lang.fleet' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="card" style="background-color: #F7BCF1 !important">
                <div class="col-md-3">
                    <h5><span style="font-weight: bold; color: red">Allowed no of vehicles: <span id="allowed"> {{ $max_vehicles == 0 ? "Infinite" : $max_vehicles }} </span></span></h5>
                </div>
                <div class="col-md-3">
                    <h5><span style="font-weight: bold; color: red">Current no of vehicles:  <span id="current">{{ $vehicles_added }}</span></span></h5>
                </div>
                <div class="col-md-3">
                    <h5><span style="font-weight: bold; color: red">Vehicles you can add:  <span id="remaining">{{ $max_vehicles-$vehicles_added }}</span></span></h5>
                </div>
            </div>
            
        </div>
        @if($max_vehicles != 0 && $max_vehicles <= $vehicles_added)
            <div class="alert alert-danger">
               @lang('messages.max_vehicles_reached')
            </div>
        @endif
        <hr>
      <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('date', __( 'fleet::lang.date' )) !!} {!!
          Form::text('date', date('m/d/Y'), ['class' => 'form-control',
          'required', 'placeholder' => __( 'fleet::lang.date' ), 'id' =>
          'leads_date']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('code_for_vehicle', __( 'fleet::lang.code_for_vehicle'
          )) !!} {!! Form::text('code_for_vehicle', $code_for_vehicle, ['class'
          => 'form-control', 'readonly', 'placeholder' => __(
          'fleet::lang.code_for_vehicle' ), 'id' => 'code_for_vehicle']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('location_id', __( 'fleet::lang.location' )) !!} {!!
          Form::select('location_id', $business_locations, null, ['class' =>
          'form-control select2', 'required', 'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'modal_location_id']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('vehicle_number', __( 'fleet::lang.vehicle_number' ))
          !!} {!! Form::text('vehicle_number', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.vehicle_number' ), 'id' =>
          'vehicle_number']); !!}
        </div>
      </div>
      <div class="col-md-3">
       <div class="form-group">
            {!! Form::label('vehicle_type', __( 'fleet::lang.vehicle_category' ) . ':*') !!}
              {!! Form::select('vehicle_category', $vehicle_category, null, ['class' => 'form-control select2', 'placeholder' =>
              __('fleet::lang.select_vehicle_category')]); !!}
        </div>
    </div>
      </div><div class="row">
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('vehicle_type', __( 'fleet::lang.vehicle_type' )) !!}
          {!! Form::text('vehicle_type', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.vehicle_type' ), 'id' =>
          'vehicle_type']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('vehicle_brand', __( 'fleet::lang.vehicle_brand' ))
          !!} {!! Form::text('vehicle_brand', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.vehicle_brand' ), 'id' =>
          'vehicle_brand']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('vehicle_model', __( 'fleet::lang.vehicle_model' ))
          !!} {!! Form::text('vehicle_model', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.vehicle_model' ), 'id' =>
          'vehicle_model']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('chassis_number', __( 'fleet::lang.chassis_number' ))
          !!} {!! Form::text('chassis_number', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.chassis_number' ), 'id' =>
          'chassis_number']); !!}
        </div>
      </div>
      </div>
      <div class="row">
          <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('engine_number', __( 'fleet::lang.engine_number' ))
          !!} {!! Form::text('engine_number', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.engine_number' ), 'id' =>
          'engine_number']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('battery_detail', __( 'fleet::lang.battery_detail' ))
          !!} {!! Form::text('battery_detail', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.battery_detail' ), 'id' =>
          'battery_detail']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('tyre_detail', __( 'fleet::lang.tyre_detail' )) !!}
          {!! Form::text('tyre_detail', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.tyre_detail' ), 'id' =>
          'tyre_detail']); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('opening_balance', __( 'fleet::lang.opening_balance'
          )) !!} {!! Form::text('opening_balance', 0, ['class' =>
          'form-control input-number', 'placeholder' => __(
          'fleet::lang.opening_balance' ), 'id' => 'opening_balance','step' => '0.001']); !!}
        </div>
        <label style="color: red">Balance: <span id="ob_bal">0</span> &nbsp <a href="#" class="badge" style="background-color: #8F3A84; color: #fff" onClick="launchBalancesModal()">Edit</a></label>
        
      </div>
      </div>
      <div class="row">
        
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('income_account_id', __( 'fleet::lang.income_account'
          )) !!} {!! Form::select('income_account_id', $income_accounts, null,
          ['class' => 'form-control select2', 'required', 'disabled' =>
          empty($access_account) ? true : false, 'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'income_account_id']); !!}
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('starting_meter', __( 'fleet::lang.starting_meter' ))
          !!} {!! Form::text('starting_meter', null, ['class' => 'form-control',
          'placeholder' => __( 'fleet::lang.starting_meter' ), 'id' =>
          'starting_meter']); !!}
        </div>
      </div>
          <div class="col-md-3">
              <div class="form-group">
                  {!! Form::label('fuel_type_id', __( 'fleet::lang.fuel_type' )) !!} {!!
          Form::select('fuel_type_id', $fuelTypes, null, ['class' =>
          'form-control select2', 'required', 'placeholder' => __(
          'fleet::lang.please_select' ), 'id' => 'modal_fuel_type_id']); !!}
              </div>
          </div>
      <div class="col-md-3">
        <div class="form-group">
          {!! Form::label('notes', __( 'fleet::lang.notes' )) !!} {!!
          Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3,
          'placeholder' => __( 'fleet::lang.notes' ), 'id' => 'notes']); !!}
        </div>
      </div>
      
    </div>
    <input type="hidden" name="new_contact_id" id="new_contact_id">
    <input type="hidden" name="new_opening_amount" id="new_opening_amount">
    <input type="hidden" name="new_notes" id="new_notes">
    <input type="hidden" name="new_dates" id="new_dates">
    <input type="hidden" name="new_invoices" id="new_invoices">
    <div class="clearfix"></div>
    <div class="modal-footer">
        @if($max_vehicles != 0 && $max_vehicles <= $vehicles_added)
            <div class="alert alert-danger">
               @lang('messages.max_vehicles_reached')
            </div>
        @else
          <button type="submit" class="btn btn-primary" id="save_leads_btn">
            @lang( 'messages.save' )
          </button>
        @endif
      <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang( 'messages.close' )
      </button>
    </div>
    
    {!! Form::close() !!}
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->

<div
  class="modal fade OpeningBalanceModal"
  role="dialog"
  aria-labelledby="gridSystemModalLabel"
>
  <div class="modal-dialog modal-lg " role="document" style="position: relative;
  top: 50%;
  transform: translateY(-50%);">
    <div class="modal-content">
      <style>
        .select2 {
          width: 100% !important;
        }
      </style>
      {!! Form::open(['url' =>
      action('\Modules\Fleet\Http\Controllers\FleetController@store'), 'method'
      => 'post', 'id' => 'fleet_forms', 'enctype' => 'multipart/form-data' ])
      !!}
      <div class="modal-header">
          <div class="d-flex" style="justify-content: space-between">
                <h4 class="modal-title">@lang( 'fleet::lang.opening_balance' )</h4>
                <div class="d-flex" style="justify-content: end;">
                    <button type="button" class="btn btn-default bg-white btn-flat btn-modal" style="margin-right: 20px"
                            data-href="/contacts/create?type=customer&mode=opening-balance" data-container=".contact_modal">
                        @lang( 'fleet::lang.add_new_customer' )
                    </button>
                    <button
                      type="button"
                      class="close"
                      aria-label="Close"
                      id="close_opening_balance"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
              </div>
          </div>
        
      </div>

      <div class="modal-body">
        <div class="text-center row card" style="background-color: #ffe600 !important; color: red; margin-bottom: 20px">
            <div class="col-sm-4" id="opening_balance_value">Opening balance:</b>&nbsp; </div>
            <div class="col-sm-4" id="added_value">Added: </b>&nbsp; </div>
            <div class="col-sm-4" id="balance_to_add_value">Balance to Add:</b> &nbsp; </div>
        </div>
        

        <div class="col-md-3">
          <div class="input-group">
            {!! Form::label('contact_id', __( 'fleet::lang.customer' )) !!} {!!
            Form::select('contact_id[]', $customers, null, ['class' =>
            'form-control select2 first_customer', 'required', 'placeholder' => __(
            'fleet::lang.please_select' ), 'id' => 'customer']); !!}
          </div>
        </div>
        <div class="col-md-2" id="forms">
          <div class="form-group">
            {!! Form::label('opening_amount', __( 'fleet::lang.opening_amount'
            )) !!} {!! Form::text('opening_amount[]', null, ['class' =>
            'form-control first_amount', 'placeholder' => __( 'fleet::lang.opening_amount' ),
            'id' => 'opening_amount','onkeyup'=>'amountvalidation(this)']); !!}
          </div>
        </div>
        
        <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('invoice_no', __( 'fleet::lang.invoice_no'
            )) !!} {!! Form::text('invoice_no[]', null, ['class' =>
            'form-control first_invoice', 'placeholder' => __( 'fleet::lang.invoice_no' ),
            'id' => 'invoice_no']); !!}
          </div>
        </div>
        
        <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('invoice_date', __( 'fleet::lang.date'
            )) !!} {!! Form::date('invoice_date[]', null, ['class' =>
            'form-control first_date', 'placeholder' => __( 'fleet::lang.date' ),
            'id' => 'invoice_date']); !!}
          </div>
        </div>
        
        <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('notes', __( 'fleet::lang.notes' )) !!} {!!
            Form::textarea('not[]', null, ['class' => 'form-control first_notes notes',
            'rows' => 3, 'placeholder' => __( 'fleet::lang.notes' ), 'id' =>
            'not']); !!}
          </div>
        </div>
        <div class="col-md-1">
          <button
            type="button"
            class="btn btn-primary w-10"
            id="add_opening_balance"
          >
            +
          </button>
        </div>
        
        <div id="opening_balance_values"></div>
        
      </div>
      
      <div class="clearfix"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="save_opening_balance">
          @lang( 'messages.save' )
        </button>
      </div>

      {!! Form::close() !!}
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>

<script>
    var allcontacts = [];
    var allamounts = [];
    var allnotes = [];
    var alldates = [];
    var allinvoices = [];
    var index = 0;
  
  $(document).on("click", "#add_opening_balance", function () {
    var html =
      `<div class="adddiv"><div class="col-md-3">
                        <div class="form-group">
                            ` +
      ` {!!
            Form::select('contact_id[]', $customers, null, ['class' =>
            'form-control select2_` +
      index +
      `', 'required', 'placeholder' => __(
            'fleet::lang.please_select' ), 'id' => 'customer']); !!}` +
      `
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                       ` +
      ` {!! Form::text('opening_amount[]', null, ['class' =>
            'form-control', 'placeholder' => __( 'fleet::lang.opening_amount' ),
            'id' => 'opening_amount','onkeyup'=>'amountvalidation(this)']); !!}` +
      `
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                       ` +
      ` {!! Form::text('invoice_no[]', null, ['class' =>
            'form-control', 'placeholder' => __( 'fleet::lang.invoice_no' ),
            'id' => 'invoice_no']); !!}` +
      `
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                       ` +
      ` {!! Form::date('invoice_date[]', null, ['class' =>
            'form-control', 'placeholder' => __( 'fleet::lang.date' ),
            'id' => 'invoice_date']); !!}` +
      `
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            ` +
      `{!! Form::textarea('not[]', null, ['class' => 'form-control notes', 'rows' =>
                            3, 'placeholder' => __( 'fleet::lang.notes' ), 'id' => 'not']);
                            !!}` +
      `                        
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-danger delete_opening_balance">-</button>
                    </div></div>`;

    $("#opening_balance_values").prepend(html);
    $(".select2_" + index).select2();
    index++;
  });

  $("#save_opening_balance").on("click", function (e) {
    var openingbalance = $("#opening_balance").val();
    let open_amount = 0;
    $('input[name^="opening_amount"]').each(function () {
        if($(this).val() == '')
        {
          toastr.error("please enter amount");
          return false;
        }
    });
    
    
    $('input[name^="invoice_date"]').each(function () {
        if($(this).val() == '')
        {
          toastr.error("please enter invoice dates");
          return false;
        }
    });
    
    $('input[name^="invoice_no"]').each(function () {
        if($(this).val() == '')
        {
          toastr.error("please enter invoice numbers");
          return false;
        }
    });
    
    
    $('select[name^="contact_id"]').each(function () { 
        if(parseInt($(this).val()) > 0)
        {
        }
        else
        {
             toastr.error("please select customer");
             return false;
        }
    });
    $('input[name^="opening_amount"]').each(function () {
        open_amount += parseInt($(this).val());
    });

    if (parseInt(open_amount) < parseInt(openingbalance)) {
        toastr.error("Enter amount is less then opening balance");
        return false;
    }
    var form = new FormData($("form#fleet_forms")[0]);

    //customer_id array
    $('select[name^="contact_id"]').each(function () {
      form.append("contact_id", $(this).val());
    });

    //opening amount array
    $('input[name^="opening_amount"]').each(function () {
      form.append("opening_amount", $(this).val());
    });

    //notes array
    $("textarea.notes").each(function () {
      form.append("notes", $(this).val());
    });
    
    //invoice np.s
    $('input[name^="invoice_no"]').each(function () {
      form.append("invoice_no", $(this).val());
    });
    
    //invoice np.s
    $('input[name^="invoice_date"]').each(function () {
      form.append("invoice_date", $(this).val());
    });
    
    
    allcontacts = form.getAll("contact_id");
    allamounts = form.getAll("opening_amount");
    allnotes = form.getAll("notes");
    alldates = form.getAll("invoice_date");
    allinvoices = form.getAll("invoice_no");
    
    
    $("#new_contact_id").val(form.getAll("contact_id"));
    $("#new_opening_amount").val(form.getAll("opening_amount"));
    $("#new_notes").val(form.getAll("notes"));
    $("#new_dates").val(form.getAll("invoice_date"));
    $("#new_invoices").val(form.getAll("invoice_no"));
    
    $(".OpeningBalanceModal").modal("hide");
    
  });
  
  
    // Add a click event listener to the delete_opening_balance button
    $(document).on('click', '.delete_opening_balance', function() {
        $(this).parent().parent().remove(); // Remove the parent element (i.e., the entire row)
    });


  $("#modal_location_id option:eq(1)").attr("selected", true);
  $("#leads_date").datepicker({
    format: "mm/dd/yyyy",
  });
  $(".select2").select2();

  $("input[name=vehicle_number]").change(function () {
    var vehicle_number = $(this).val();
    $.ajax({
      type: "POST",
      url: "{{ route('vehicle_check') }}",
      data: {
        vehicle_number: vehicle_number,
        _token: "{{ csrf_token() }}",
      },
      success: function (data) {
        if (data.success == false) {
          toastr.error(data.msg);
          $("input[name=vehicle_number]").val("");
        }
      },
    });
  });
  $("#opening_balance").change(function () {
      launchBalancesModal();
  });
  
  
  function launchBalancesModal(){
      var openingbalance = $("#opening_balance").val();
      
      var totamts = allamounts.reduce((a, b) => parseInt(a) + parseInt(b), 0);
      
      var bal = parseInt(openingbalance) - parseInt(totamts);
      $("#opening_balance_value").html("Opening balance: " + openingbalance);
      $("#added_value").html("Added: " + totamts);
      $("#balance_to_add_value").html("Balance to Add: " + bal);
      
      $("#ob_bal").html(bal);
      
      if(bal > 0 || bal < 0){
        //   disable submit
        $("#save_leads_btn").attr("disabled", true);
      }
      
      
      let pattern = /^[0-9.]+$/;
      if (openingbalance.match(pattern)) {
        //   $("#opening_balance_values").empty();
          
          for(var i =0; i< allcontacts.length; i++){
              if(i== 0){
                    $(".first_customer").val(allcontacts[i]).change();
                    $(".first_amount").val(allamounts[i]);
                    $(".first_notes ").val(allnotes[i]);
                    $(".first_invoice ").val(allinvoices[i]);
                    $(".first_date ").val(alldates[i]);
                    
              }else{
                  
                  var html =
                  `<div class="adddiv"><div class="col-md-3">
                                    <div class="form-group">
                                        ` +
                  ` {!!
                        Form::select('contact_id[]', $customers, `+ allcontacts[i]+`, ['class' =>
                        'form-control select2_` +
                  i +
                  `', 'required', 'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 'customer']); !!}` +
                  `
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                   ` +
                  ` {!! Form::text('opening_amount[]', `+ allamounts[i]+`, ['class' =>
                        'form-control', 'placeholder' => __( 'fleet::lang.opening_amount' ),
                        'id' => 'opening_amount','onkeyup'=>'amountvalidation(this)']); !!}` +
                  `
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                   ` +
                  ` {!! Form::text('invoice_no[]', `+ allinvoices[i]+`, ['class' =>
                        'form-control', 'placeholder' => __( 'fleet::lang.invoice_no' ),
                        'id' => 'invoice_no']); !!}` +
                  `
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                   ` +
                  ` {!! Form::date('invoice_date[]', `+ alldates[i]+`, ['class' =>
                        'form-control', 'placeholder' => __( 'fleet::lang.date' ),
                        'id' => 'invoice_date']); !!}` +
                  `
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        ` +
                  `{!! Form::textarea('not[]', `+ allnotes[i]+`, ['class' => 'form-control notes', 'rows' =>
                                        3, 'placeholder' => __( 'fleet::lang.notes' ), 'id' => 'not']);
                                        !!}` +
                  `                        
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <button type="button" class="btn btn-danger delete_opening_balance">-</button>
                                </div></div>`;
            
                $("#opening_balance_values").append(html);
                $(".select2_" + i).select2();
                            
              }
          }
          
          index = allcontacts.length + 1;
          
          $(".OpeningBalanceModal").modal("show");
      }
      else
      {
          toastr.error("Please enter valid amount!");
      }
  }
  
  
  $("#close_opening_balance").click(function () {
    $(".OpeningBalanceModal").modal("hide");
  });
  
  $('.OpeningBalanceModal').on('hidden.bs.modal', function () {
      
      var openingbalance = $("#opening_balance").val();
      
      var totamts = allamounts.reduce((a, b) => parseInt(a) + parseInt(b), 0);
      
      var bal = parseInt(openingbalance) - totamts;
      
      $("#opening_balance_value").html("Opening balance: " + openingbalance);
      $("#added_value").html("Added: " + totamts);
      $("#balance_to_add_value").html("Balance to Add: " + bal);
      $("#ob_bal").html(bal);
      
      if(bal > 0 || bal < 0){
        //   disable submit
        $("#save_leads_btn").attr("disabled", true);
      }
      
      if(bal == 0){
           $("#save_leads_btn").attr("disabled", false);
      }
      
    })

  function amountvalidation(amounts) {
    var openingbalance = $("#opening_balance").val();
    let amount = $(amounts).val();
    let open_amount = 0;
    let pattern = /^[0-9]+$/;

    if (amount.match(pattern)) {
      $('input[name^="opening_amount"]').each(function () {
        open_amount += parseInt($(this).val());
      });

      if (parseInt(open_amount) > parseInt(openingbalance)) {
          $(amounts).val("");
        toastr.error("You Can Not add amount more than your opening Balance!");
      } else {
           $("#added_value").html("Added: " + parseInt(open_amount));
           var bal = parseInt(openingbalance)-parseInt(open_amount);
           $("#balance_to_add_value").html("Balance to Add: " + bal);
      }
    } else {
      toastr.error("Please enter valid amount!");
    }
  }
</script>
