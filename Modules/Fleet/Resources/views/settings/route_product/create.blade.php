<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\RouteProductController@store'), 'method' =>
    'post', 'id' => 'product_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.product' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'fleet::lang.date' ),'id'=>'product_date']); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('name', __( 'fleet::lang.name' ) . ':*') !!}
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.name'), 'id'
          => 'name']); !!}
        </div>
        <div class="form-group col-sm-4">
          <br><button type="button" class="btn  btn-primary" id="addProductBtn">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
        </div>

        <!--Add rows of the products here-->

        <!--End of the addition-->
      </div>

    </div>
    <hr>
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="add_product_table" style="width: 100%;">
        <thead>
        <tr>
          <th>@lang('fleet::lang.date')</th>
          <th>@lang('fleet::lang.name')</th>
          <th class="notexport">@lang('messages.action')</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
 $('#date').datepicker('setDate', new Date());
 function removeProduct(e){
   $(e).parent().parent().remove();
 }
</script>
<script>
  $(document).ready(function() {
    $('#addProductBtn').on('click', function() {
      saveProduct();
    });

  });

  function saveProduct(){
    var obj={
      name:$('#name').val(),
      date:$('#product_date').val(),

    };
    buildProductRow(obj);

    // Reset input fields to default values
    $('#name').val('');
    // $('#product_date').val('');
  }

  function buildProductRow(obj){
    console.log(obj);
    var tr=`
  <tr>
  <td>${obj.date}<input type="hidden" value="${obj.date}" name="date[]"></td>
  <td>${obj.name}<input type="hidden" value="${obj.name}" name="name[]"></td>
  <td><button type="button" onclick="removeProduct(this)" class="btn btn-danger" aria-label="Left Align">
                    <span aria-hidden="true">×</span>
                    </button></td>
  </tr>
  `;
    $('#add_product_table tbody').append(tr);

  }
</script>