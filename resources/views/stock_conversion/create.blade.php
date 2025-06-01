<div class="modal-dialog" role="document" style="width:45%">
    <div class="modal-content">

        {!! Form::open(['url' => action('StockConversionController@store'), 'method' => 'post', 'id' => 'contact_group_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Add Stock Conversion</h4>
        </div>
      
        <div class="modal-body">
        <div class="row">
            <div class="col-md-8">
            <div class="form-group">
               {!! Form::label('conversion_from_no', __( 'Form No' ) . ':*') !!}
                {!! Form::text('conversion_from_no', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'Form No' ) ]); !!}
            </div>
             </div>
             <div class="col-md-3">
            <div class="form-group">
               {!! Form::label('location', __( 'lang_v1.location' ) . ':') !!}
                {!! Form::select('location', $locations, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'location','placeholder'
              => __('messages.please_select'), 'required']); !!}
            </div>
             </div>
            
            <div class="col-md-8">
              <div class="form-group">
                {!! Form::label('product_conversion_from', __( 'Product Conversion From' ) . ':*') !!} 
               {!! Form::select('product_conversion_from', $product_convert_to, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'product_conversion_from','placeholder'
              => __('messages.please_select'), 'required']); !!}
                
                </div> 
            </div>
            
             <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('unit_convert_from',  __( 'Unit Convert From' ) . ':*') !!}
                  
                   {!! Form::select('unit_convert_from', $units, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'unit_convert_from','placeholder'
              => __('messages.please_select'), 'required']); !!}
                </div>
             </div>
            <div class="col-md-6">
            <div class="form-group">
                 {!! Form::label('qty_convert_from', __( 'Total Quantity Convert From' ) . ':*') !!}
                {!! Form::text('qty_convert_from', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'Total Quantity Convert From' ) ]); !!}
            </div>
            </div>
              <div class="col-md-8">
        
             <div class="form-group">
                {!! Form::label('product_convert_to', __( 'Product Convert to' ) . ':') !!} 
                 {!! Form::select('product_convert_to', $product_convert_to, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'product_convert_to','placeholder'
              => __('messages.please_select'), 'required']); !!}
                    
                </div>
            </div>
             <div class="col-md-6">
             <div class="form-group">
                {!! Form::label('unit_convert_to',  __( 'Unit Convert to' ) . ':*') !!}
            {!! Form::select('unit_convert_to', $units, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'unit_convert_to','placeholder'
              => __('messages.please_select'), 'required']); !!}
                
            </div>
            </div>
        <div class="col-md-6">
            <div class="form-group">
                 {!! Form::label('qty_convert_to', __( 'Quantity Convert to' ) . ':*') !!}
                {!! Form::text('qty_convert_to', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'Quantity Convert to' ) ]); !!}
            </div>
        </div>  

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 $(document).ready(function() {
    $('.select2').select2({
        width: '50%',
        placeholder: "{{ __('messages.please_select') }}",
        allowClear: true,
    });
});
   $('form').submit(function(event) {
       /*
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
          toastr.success('Stock Unit Conversion successful');
        } else {
          toastr.error('Unit Conversion Failed');
        }
        
    
        // Clear the form fields
        form[0].reset();
      },
      error: function(xhr, status, error) {
        // Handle the error response
        console.error(xhr.responseText);
        
        // Display error toast
        toastr.error('Stock Unit Conversion failed');
        
        // Optionally, you can display a more detailed error message
        // toastr.error('Supplier product mapping failed: ' + error);
      }
    });*/
  });
   $(document).on('change', '#unit_convert_to', function(){
    let product_id = $('#product_conversion_from').val();
    let qty_from = $('#qty_convert_from').val();
     console.log(product_id+qty_from);
    
    $.ajax({
      method: 'get',
      url: '/stock-conversion/get-stock-unit/'+product_id,
      data: {   
        product_id: product_id,
        qty_from: qty_from 
            },
      success: function(result) {
          
     $('#qty_convert_to').val(result.qty_to);
      },
    });
  });
    
 
</script>
