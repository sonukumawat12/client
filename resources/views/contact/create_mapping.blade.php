<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    @php
    $form_id = 'contact_add_forms';
    
    if(isset($quick_add)){
    $form_id = 'quick_add_contact';
    }
@endphp 

     

    {!! Form::open(['url' => action('SupplierMappingController@store'), 'method' => 'post', 'id' => $form_id,'enctype'=>"multipart/form-data",'files' => true ]) !!}

    <div class="modal-header" id="supplier-form">
      <button type="button" class="close closing_contact_modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.add_supplier_product_mapping')</h4>
    </div>

    <div class="modal-body">
      <div class="row">

        <div class="col-md-4 contact_type_div">
          <div class="form-group">
            {!! Form::label('type', __('lang_v1.supplier_list') . ':*' ) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::select('type', $name, !empty($type) ? $type : null , ['class' => 'form-control', 'id' =>
              'type','placeholder'
              => __('messages.please_select'), 'required']); !!}
            </div>
          </div>
        </div>
     
        
         

          
          <table class="table table-bordered table-striped" style="width: 80%" id="numbers_table">
            <thead>
                <tr>
                    <th >@lang('lang_v1.name')</th>
                     
                <!--    <th>*</th> -->
                </tr>
            </thead>
            
            <tbody>
               
                    <tr>
                        <td >
                               {!! Form::select('names', $names, !empty($type) ? $type : null , ['class' => 'form-control', 'id' =>
              'names','placeholder'
              => __('messages.please_select'), 'required']); !!}
                        </td>
                        
                       <!--  <td>
                            <button type="button" id="add_number_row" class="btn btn-success">+</button>
                        </td> -->
                    </tr>
                
                
            </tbody>
            
        </table>
        </div>
         

       
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default closing_contact_modal">@lang( 'messages.close' )</button>
    </div>
  <div class="modal-header">
      
      <h4 class="modal-title" style="Align:center">@lang('lang_v1.list_supplier_product_mapping')</h4>
    </div>
    
    <div class="row " style="width: 70%;margin-left:40px;">
     <div class="table-responsive">
        <table class="table table-bordered table-striped"  id="supplier_mapping">
            <thead>
              
                
                <tr>
                 

                    <th >
                        @lang('lang_v1.supplier_list')
                    </th>

                    <th >
                        @lang('lang_v1.name')
                    </th>
                </tr>
               
            </thead>
            
            
                  
              @foreach($mappings as $mapping)
            
            <tr>
                <td>{{ $mapping->suppliername }}</td>
                 <td>{{ $mapping->productname }}</td>
            </tr>
        @endforeach
         
       
        </table>
    </div>
</div>

</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
 $(document).ready(function() {
    $('#supplier_mapping').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "dom": 'lBfrtip',
        "buttons": [
               {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'colvis',
                text: '<i class="fa fa-columns"></i> Column Visibility',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
              {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            }
        ],
        "language": {
            "lengthMenu": "Show _MENU_ entries",
            "search": "Search",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });
});
 
$(document).ready(function() {
  // ...

  $('form').submit(function(event) {
    event.preventDefault(); // Prevent form submission

    var form = $(this);
    var formData = new FormData(form[0]);

    $.ajax({
      url: form.attr('action'),
      method: form.attr('method'),
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        // Handle the success response
        console.log(response);
        
        // Display success toast
        
        if (response.success) {
          toastr.success('Product bind to supplier successful');
        } else {
          toastr.error('Product bind to supplier exist');
        }
        
    
        // Clear the form fields
        form[0].reset();
      },
      error: function(xhr, status, error) {
        // Handle the error response
        console.error(xhr.responseText);
        
        // Display error toast
        toastr.error('Supplier product mapping failed');
        
        // Optionally, you can display a more detailed error message
        // toastr.error('Supplier product mapping failed: ' + error);
      }
    });
  });

  
});
 
    $(document).ready(function() {
       // $("#numbers_table").hide();

    // Add row when add_number_row button is clicked

        $('#numbers_table').on('click', '#add_number_row', function() {
            var lastRow = $('#numbers_table tbody tr:last');
            var newRow = lastRow.clone(); // Clone the last row
            newRow.find('input[type="text"]').val(''); // Clear the input value
            lastRow.after(newRow); // Append the new row after the last row
    
            // Add remove button to the new row
            var removeButton = $('<button>', {
                'type': 'button',
                'class': 'btn btn-danger remove-number-row',
                'text': '-'
            });
            newRow.find('td:last').html(removeButton); // Add the remove button to the last cell
        });

        // Remove row when remove-number-row button is clicked
        $('#numbers_table').on('click', '.remove-number-row', function() {
            $(this).closest('tr').remove(); // Remove the row
        });
        $(document).on('click', '.closing_contact_modal', function() {
            $('.contact_modal_recipient').modal('hide');
            $('.contact_modal').modal('hide');
        })
        $(document).on('change', '.add_more_nos', function() {
            if($(this).is(':checked')){
                
                $("#numbers_table").show();
                $("#notification_fields").attr('required',true);
            }else{
                
                $("#numbers_table").hide();
                $("#notification_fields").attr('required',false);
            }
        });
    
        $(document).on('change', '.toggler', function() {
            var formData = [];
            
            $('#numbers_table tbody tr').each(function() {
                var phoneNumber = $(this).find('input[name="phone_number[]"]').val(); // Get phone number value
                var phoneName = $(this).find('input[name="phone_name[]"]').val(); // Get phone number value
                var checkboxes = $(this).find('.toggler'); // Get checkboxes
                var rowValues = {
                  'phone_number': phoneNumber,
                  'notifications': {} ,
                  'phone_name' : phoneName
                };
                
                checkboxes.each(function() {
                  var checkboxName = $(this).attr('name').replace('[]', ''); // Get the checkbox name
                  var checkboxValue = $(this).is(':checked') ? 1 : 0; // Determine checkbox value (1 if checked, 0 if not)
                  rowValues['notifications'][checkboxName] = checkboxValue; // Add checkbox name and value to rowValues object
                });
                
                formData.push(rowValues); // Add rowValues to formData array
              });
              
            $("#notification_parameters").val(JSON.stringify(formData));
        });
    
          $('#transaction_date_contact').datepicker({
              autoclose: true,
              format: datepicker_date_format,
          });
    })
 
  
    document.addEventListener('DOMContentLoaded', function () {
        // Get the form element
        var form = document.getElementById('supplier-form');

        // Submit event listener
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the form from submitting

            // Make an AJAX request to submit the form
            var xhr = new XMLHttpRequest();
            xhr.open(form.method, form.action, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Reload the page after successful submission
                    location.reload();
                }
            };
            xhr.send(new FormData(form));
        });
    });
 
      

        $(document).on('click', 'a.print_bill', function(){
            let href = $(this).data('href');

            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    html = result;
                    console.log(html);
                    var w = window.open('', '_self');
                    $(w.document.body).html(html);
                    w.print();
                    w.close();
                    location.reload();
                },
            });


        });


        $(document).on('click', '#add_issue_bill_customer_btn', function(){
            $('.issue_bill_customer_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })
</script>
 