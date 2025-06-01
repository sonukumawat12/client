<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
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
    <form url="{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@store_passenger')}}" id="airline_passenger_contact_add_form" accept-charset="UTF-8" enctype="multipart/form-data" method="POST">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Passenger</h4>
      </div>
      {!! Form::hidden('airticket_no',null,['id'=>'airticket_no_hidden']) !!}
      {!! Form::hidden('id_row',null,['id'=>'id_row']) !!}
      <div class="modal-body" style="max-height: 400px; overflow-y: auto;  overflow-x: hidden;">
        <div class="row">
@if($fieldSettings->name == 1)
          <div class="form-group col-sm-6">
            {!! Form::label('passenger_name', __( 'airline::lang.passenger_name' ) . ':*') !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.passenger_name')}}">
                  <i class="fa fa-user"></i>
                </button>
              </div>
              {!! Form::select('passenger_name', [], null, [
              'class' => 'form-control select2',
              'id' => 'passenger_name_select',
              'placeholder' => __('airline::lang.passenger_name_placeholder'),
              'required',

              ]) !!}
              <span class="input-group-btn">
                <br>
                <button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-href="/contacts/create?type=customer&form_id=airline_passenger_contact_add_form" data-container=".passenger_contact_modal">
                  <i class="fa fa-plus-circle text-primary fa-lg"></i>
                </button>
              </span>
            </div>
          </div>
@endif

@if($fieldSettings->passport_number == 1)
          <div class="form-group col-sm-6">
            {!! Form::label('passport_number', __( 'airline::lang.passport_number' ) . '') !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.passport_number')}}">
                  <i class="fa fa-vcard"></i>
                </button>
              </div>
              {!! Form::text('passport_number', null, [
              'class' => 'form-control mousetrap',
              'id' =>'passport_number_text',
              'readonly',
              'placeholder' => __('airline::lang.passport_number_placeholder'),
              ]); !!}
              
               <span class="input-group-btn btn-modal-grp-create hide">
                <br>
                <button type="button" class="btn btn-default bg-white btn-flat btn-modal-popup" data-string="nic_number" data-href="" data-container=".passenger_contact_modal">
                  <i class="fa fa-plus-circle text-primary fa-lg"></i>
                </button>
              </span>
              
            </div>
          </div>
@endif

          <div class="clearfix"></div>

@if($fieldSettings->vat_number == 1)
          <div class="form-group col-sm-6">
            {!! Form::label('passenger_vat_number', __( 'airline::lang.passenger_vat_number' ) . '') !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.passenger_vat_number')}}">
                  <i class="fa fa-dot-circle-o"></i>
                </button>
              </div>
              {!! Form::text('passenger_vat_number', null, [
              'class' => 'form-control mousetrap',
              'id' =>'passenger_vat_number_text',
              // 'readonly',
              'placeholder' => __('airline::lang.passenger_vat_number'),
              'required']); !!}
              
              <span class="input-group-btn btn-modal-grp-create hide">
                <br>
                <button type="button" class="btn btn-default bg-white btn-flat btn-modal-popup" data-string="vat_number" data-href="" data-container=".passenger_contact_modal">
                  <i class="fa fa-plus-circle text-primary fa-lg"></i>
                </button>
              </span>
              
            </div>
          </div>
@endif
          
@if($fieldSettings->passenger_mobile_no == 1)
          <div class="form-group col-sm-6">
            {!! Form::label('passenger_mobile', __( 'airline::lang.passenger_mobile' ) . ':*') !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.passenger_mobile')}}">
                  <i class="fa fa-mobile"></i>
                </button>
              </div>
              {!! Form::text('passenger_mobile', null, [
              'class' => 'form-control mousetrap',
              'id' =>'passenger_mobile_text',
              'readonly',
              'placeholder' => __('airline::lang.passenger_mobile_placeholder'),
              'required']); !!}
              
              <span class="input-group-btn btn-modal-grp-create hide">
                <br>
                <button type="button" class="btn btn-default bg-white btn-flat btn-modal-popup" data-string="mobile" data-href="" data-container=".passenger_contact_modal">
                  <i class="fa fa-plus-circle text-primary fa-lg"></i>
                </button>
              </span>
              
            </div>
          </div>
@endif

          <div class="clearfix"></div>
@if($fieldSettings->select_passport_image == 1 )
          <div class="form-group col-sm-6">
            {!! Form::label('passport_image',__('airline::lang.passport_image')).'&nbsp; &nbsp;<i class="fa fa-cloud-upload"></i>' !!}
       

            <div class="input_container btn-modal-grp-create hide">
              {!! Form::file('passport_image', ['id' => 'passport_image', 'accept' => 'image/*','class' => 'file-input', 'required'=>'required' ]); !!}
            </div>
          </div>
          
         
          <input type="hidden" name="passport_image_link" value="" id="passport_image_link">
          <div class="form-group col-sm-6">
            <div class="">
              {!! Form::label('name', __('contact.should_notify') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-envelope"></i>
                </span>
                {!! Form::select('should_notify', ['1' => __('messages.yes'), '0' => __('messages.no')], null, ['placeholder'
                    => __( 'messages.please_select' ), 'required', 'class' => 'form-control','id' => 'message_notify']); !!}
              </div>
            </div>
          </div>
@endif

          <div class="clearfix"></div>
        

          {{-- Add By Sakhawat --}}
@if($fieldSettings->frequent_flyer_no == 1)
          <div class="form-group col-sm-6">
            {!! Form::label('frequent_flyer_no', __( 'airline::lang.frequent_flyer_no' ) . ':*') !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.frequent_flyer_no')}}">
                  <i class="fa fa-plane"></i>
                </button>
              </div>
              {!! Form::text('frequent_flyer_no', null, [
              'class' => 'form-control',
              'required' => 'required',
              'id' =>'frequent_flyer_no',
              'placeholder' => __('airline::lang.frequent_flyer_no')]); !!}
            </div>
          </div>
@endif

@if($fieldSettings->child == 1)
          <div class="form-group col-sm-2">
            {!! Form::label('child', __( 'airline::lang.child' ) . ':*') !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.passenger_mobile')}}">
                  <i class="fa fa-male"></i>
                </button>
              </div>
              {!! Form::select('child', ['yes'=>'Yes','no'=>'No'], null, ['class' => 'form-control','id'=>'child']); !!}
             
            </div>
          </div>
@endif

@if($fieldSettings->price == 1)
          <div class="form-group col-sm-4">
            {!! Form::label('price', __( 'airline::lang.price' ) . ':') !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.price')}}">
                  <i class="fa fa-dollar"></i>
                </button>
              </div>

              {!! Form::text('price', null, [
              'class' => 'form-control',
              'id' =>'price',
              'required'=>'required',
              'placeholder' => __('airline::lang.price')]); !!}
            </div>
          </div>
@endif
          <div class="clearfix"></div>

@if($fieldSettings->additional_service == 1)
          <div class="form-group col-sm-4">
            {!! Form::label('additional_service', __( 'airline::lang.additional_service' )  ) !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.additional_service')}}">
                  <i class="fa fa-server"></i>
                </button>
              </div>
              {!! Form::select('additional_service', [], null, [
              'class' => 'form-control select2',
              'id' => 'additional_service_select',
              'placeholder' => 'Additional Service',
              'required',

              ]) !!}
             
            </div>
          </div>
@endif

@if($fieldSettings->additional_service_amount == 1)
          <div class="form-group col-sm-4">
            Additional Service {!! Form::label('amount', __( 'airline::lang.amount' ) . ':') !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="Additional Service {{__('airline::lang.price')}}">
                  <i class="fa fa-dollar"></i>
                </button>
              </div>
              {!! Form::text('amount', null, [
              'class' => 'form-control',
              'required'=>'required',
              'id' =>'amount',
              'placeholder' => 'Additional Service '. __('airline::lang.amount')]); !!}
            </div>
          </div>
@endif

         
@if($fieldSettings->passenger_type == 1)
          <div class="form-group col-sm-4">
            {!! Form::label('passenger_type', __( 'airline::lang.passenger_type' )  ) !!}
            <div class="input-group">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.passenger_type')}}">
                  <i class="fa fa-user"></i>
                </button>
              </div>
              {!! Form::select('passenger_type', [], null, [
              'class' => 'form-control select2',
              'id' => 'passenger_type_select',
              'placeholder' => 'Passenger Type',
              'required',

              ]) !!}
             
            </div>
          </div>
@endif
          
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-save-submit">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<div class="modal fade passenger_contact_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
<script>
  $('#commision_amount').on('input', function() {
    var amountInput = document.getElementById('commision_amount');
    // Restrict the value to 2 decimal places
    var value = this.value;
    var parts = value.split('.');
    if (parts.length > 1 && parts[1].length > 2) {
      parts[1] = parts[1].slice(0, 2);
      this.value = parts.join('.');
    }
  });

  $('#commision_amount').on('keydown', function(e) {
    // Allow only numbers, period (.), and Backspace/Delete keys
    if (
      !/[\d.]/.test(e.key) &&
      e.key !== 'Backspace' &&
      e.key !== 'Delete' &&
      e.key !== 'ArrowLeft' &&
      e.key !== 'ArrowRight' &&
      e.key !== 'Home' &&
      e.key !== 'End'
    ) {
      e.preventDefault();
    }
  });
</script>
<script>
  $(document).ready(function() {
    // Make an Ajax request to fetch the commission types data from the server
    $.ajax({
      url: '/airline/get_commission_types',
      method: 'GET',
      success: function(response) {
        var commissionTypes = response.commissionTypes;
        var commissionTypeSelect = $('#commission_type');


        commissionTypeSelect.empty();


        commissionTypeSelect.append($('<option>', {
          value: '',
          text: 'Please Select'
        }));

        // Add options from the server data
        $.each(commissionTypes, function(value, label) {
          commissionTypeSelect.append($('<option>', {
            value: value,
            text: label
          }));
        });
      },
      error: function(xhr, status, error) {
        console.error('Error fetching commission types:', error);
      }
    });
  });

  $('#airline_passenger_contact_add_form').submit(function(e) {
    e.preventDefault();
    var data = new FormData(this);

    $.ajax({
      method: 'POST',
      url: $(this).attr('url'),
      dataType: 'json',
      contentType: false,
      processData: false,
      cache: false,
      encode: true,
      data: data,
      success: function(result) {
        
        if (result.success == true) {
          const {
            id_row,
            name,
            passport_number,
            passenger_vat_number,
            passenger_mobile,
            image,
            should_notify,
            frequent_flyer_no,
            airline_itinerary,
            airticket_no,
            child,
            price,
            additional_services,
            passenger_id,
            passenger_type,
            amount,
            final_amount,
            id
          } = result.data;

          $('div.passenger_modal').modal('hide');
          $('#id_row').val('');

          if (id_row) {
            $('#' + id_row).remove();
          }
          $("#passport_image_link").val(image);
        //   $("#airline_itinerary_link").val(airline_itinerary);

          var formDataArray = $('#airline_passenger_contact_add_form').serializeArray();

          // Create a new table row
            var newRowId = 'row_' + Date.now(); 
            var newRow = $('<tr>').attr('id', newRowId);

          newRow.append($('<td>').text(name));
          newRow.append($('<td>').text(passport_number));

          // Create a Passport Image cell
          var passportImageCell = $('<td>');

          // Create the Passport Image element
          var passportImage = $('<img>')
            .attr('style', 'width: 100px; height: 100px;')
            .attr('src', '{{ url("public/uploads/media") }}' + '/' + image) // Make sure passport_image has the correct file name
            .attr('alt', 'Passport Image'); // Add alt text for accessibility

          // Create the "View Passport Image" button
          var viewPassportImageBtn = $('<button type="button">')
            .addClass('btn btn-sm btn-info')
            .text('View Passport Image')
            .on('click', function() {
             $('div.passenger_modal').modal('hide');
              $('#id_row').val('');


              e.preventDefault(); // Prevent form submission
              var passportImageURL = passportImage.attr('src'); // Get the image source from the img element
              $('#passportImage').attr('src', passportImageURL);
              $('#passportImageModal').modal('show');
            });

          // Append the Passport Image and button to the cell
          //                    passportImageCell.append(passportImage);
          passportImageCell.append(viewPassportImageBtn);

          // Append the cell to the table row
          newRow.append(passportImageCell);

          
          newRow.append(
            `<input type="hidden" name="passengers[]" value='` + JSON.stringify(formDataArray) + `' >` + airticket_no
          );
          newRow.append($('<td>').text(frequent_flyer_no));
          newRow.append($('<td>').text(child ? 'YES' : 'NO'));

          newRow.append($('<td>').text(__number_f(price)));
          newRow.append($('<td>').text(passenger_type));
          newRow.append($('<td>').text(amount));
          newRow.append($('<td>').text(final_amount));

          // newRow.append(`<td><button type="button" class="btn btn-sm btn-success"><i class="fa fa-pencil"></i> Edit</button></td>`));

       

        newRow.append(`
          <td>
            <button type="button" class="btn btn-sm btn-success" 
              onclick="editRow('${newRowId}', '${name}', '${passport_number}', '${passenger_vat_number}', '${passenger_mobile}' , '${image}' , '${should_notify}', '${frequent_flyer_no}', '${airline_itinerary}', '${airticket_no}', '${child}', '${price}', '${additional_services}', '${passenger_id}', '${passenger_type}', '${amount}')">
              <i class="fa fa-pencil"></i> Edit
            </button>
          </td>
        `);

          // Append the new row to the table
          $('#passenger_table tbody').append(newRow);
          var tot_price = (parseInt(__number_uf($('#total_price').text()), 10) + parseInt(final_amount));

          $('#total_price').text(__number_f(tot_price));
          $('#tot_price').val(tot_price);
          calculate_balance_due();

        } else {
          toastr.error(result.msg);
        }
      },
    });
  });

  function calculate_balance_due() {
    var total_payable = parseInt(__number_uf($('#total_price').text()), 10);
    var total_paying = 0;
    $('.pmt-amount')
      .each(function() {
        if (parseFloat($(this).val())) {
          total_paying += __read_number($(this));
        }
      });
    var bal_due = total_payable - total_paying;


    if (bal_due != 0) {
      $("#save").hide();
    } else {
      $("#save").show();
    }


    $('#payment_due').text(__currency_trans_from_en(bal_due, false, false));
  }

  $(document).ready(function() {

    // Initialize Select2 on your select element
    $('#passenger_name_select').select2({
      minimumResultsForSearch: 0, // Set this to 0 to always display the search input
      placeholder: 'Select Customer',
      allowClear: true,
      width: '100%'
    });
    $('#additional_service_select').select2({
      minimumResultsForSearch: 0, 
      placeholder: 'Select Additional Service',
      allowClear: true,
      width: '100%'
    });
    $('#passenger_type_select').select2({
      minimumResultsForSearch: 0, 
      placeholder: 'Select Passenger Type',
      allowClear: true,
      width: '100%'
    });
    $('body').find('#airticket_no_hidden').val($('body').find('#airticket_no').val());
    // Fetch customer data via AJAX
    $.ajax({
      url: "{{route('get-customer')}}", // Replace with your actual endpoint
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        // Update the select options with the retrieved data
        var select = $('#passenger_name_select');
        select.empty();

        $.each(data, function(index, customer) {
          var option = $('<option>').text(customer.name).val(customer.id);
          option.attr('passport_id', customer.nic_number); // Add passport_id attribute
          option.attr('phone_no', customer.mobile);
          option.attr('vat_number', customer.vat_number); 
          option.attr('passport_image', customer.image); 
          
          select.append(option);
        });

        $('#passenger_name_select').trigger('change');
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });

    $.ajax({
      url: "{{url('airline/passenger_type_get')}}", 
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        var select = $('#passenger_type_select');
        select.empty();

        $.each(data, function(index, val) {
          var option = $('<option>').text(val.type_name).val(val.type_name);
      
          select.append(option);
        });

        $('#passenger_type_select').trigger('change');
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });

    $.ajax({
      url: "{{url('airline/additional_service_get')}}", 
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        var select = $('#additional_service_select');
        select.empty();

        $.each(data, function(index, val) {
          var option = $('<option>').text(val.name).val(val.id);
      
          select.append(option);
        });

        $('#additional_service_select').trigger('change');
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });



  });
  $('select#passenger_name_select').on('change', function() {
    $(".btn-modal-grp-create").removeClass('hide');
    
    $(".passport_image_div").empty();

    var selectedOption = $(this).find('option:selected');
    var passportId = selectedOption.attr('passport_id');
    
    var phone_no = selectedOption.attr('phone_no');
    var vat_number = selectedOption.attr('vat_number');
    var passport_image = selectedOption.attr('passport_image');
    
    $("#passport_image_link").val(passport_image);
    
    if(passport_image){
        var passportImage = $('<img>')
            .attr('style', 'width: 50px; height: 50px;')
            .attr('src', '{{ url("public/uploads/media") }}' + '/' + passport_image) // Make sure passport_image has the correct file name
            .attr('alt', 'Passport Image');
            
        $(".passport_image_div").append(passportImage);
    }
    
    
    $('#passenger_vat_number_text').val(vat_number);
    $('#passenger_mobile_text').val(phone_no);
    
    
    if (passportId) {
      $('#passport_number_text').val(passportId);
    } else {
      $('#passport_number_text').val('');
    }
  });
  $(document).on('click', '.closing_contact_modal', function() {
        $('.contact_modal_recipient').modal('hide');
        // $('.contact_modal').modal('hide');
    });
    
    $(document).on('click', '.close-passenger-modal', function() {
        $('.passenger_contact_modal').modal('hide');
    });
    
    
</script>