@extends('layouts.app')
@section('title', __('All Stock Conversion'))

@section('content')

<!-- Content Header (Page header) -->

<style>
  
.popup{
   
    cursor: pointer
}
.popupshow{
    z-index: 99999;
    display: none;
}
.popupshow .overlay{
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.66);
    position: absolute;
    top: 0;
    left: 0;
}
.popupshow .img-show{
        width: 900px;
    height: 600px;
    background: #FFF;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    overflow: hidden;
}
.img-show span{
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 99;
    cursor: pointer;
}
.img-show img{
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
/*End style*/

</style>


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Product</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Product</a></li>
                    <li><span>Stock Conversion</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>



<!-- Main content -->
<section class="content main-content-inner">
    <div class="row">
      
            @component('components.filters', ['title' => __('report.filters')])
              <div class="col-md-2">
                <div class="form-group">
            {!! Form::label('date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
              </div>
            </div>
                <div class="col-md-2">
                <div class="form-group">
              {!! Form::label('location', __( 'lang_v1.location' ) . ':') !!}
                {!! Form::select('location', $locations, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'location','placeholder'
              => __('messages.please_select'), 'required']); !!}
              </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                
               
                {!! Form::label('type', __('Conversion Form No') . ':') !!}
           {!! Form::select('conversion_from_no', $stock_list, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'conversion_from_no','placeholder'
              => __('messages.please_select'), 'required']); !!}   
                </div>
            </div>
          <div class="col-md-2">
            <div class="form-group">
            {!! Form::label('type', __('Product Convert From') . ':') !!}
           {!! Form::select('product_convert_from', $product, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'product_convert_from','placeholder'
              => __('messages.please_select'), 'required']); !!}   
              </div>
        </div>   
         <div class="col-md-2">
                <div class="form-group">
                {!! Form::label('type', __('Unit Conver From') . ':') !!}
                 {!! Form::select('unit_convert_from', $units, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'unit_convert_from','placeholder'
              => __('messages.please_select'), 'required']); !!}
              </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                {!! Form::label('type', __('Product Convert to') . ':') !!}
              {!! Form::select('product_convert_to', $product, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'product_convert_to','placeholder'
              => __('messages.please_select'), 'required']); !!} 
              </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                {!! Form::label('type', __('Unit Convert to') . ':') !!}
                 {!! Form::select('unit_convert_to', $units, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'unit_convert_to','placeholder'
              => __('messages.please_select'), 'required']); !!}  </div>
            </div>
            
         
            @endcomponent
            
       
     
          
          
    </div>
    
   
    
    
   
    <input type="hidden" value="{{$type}}" id="contact_type">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'All Stock Conversion', ['contacts' =>
    __('lang_v1.'.$type.'s') ])])
    <div class="row">
   <div class="box-tools pull-right">
        <p class="text-muted">
                    
                        <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
                        <button type="button" class="btn btn-primary btn-modal"
                        data-href="{{action('StockConversionController@create', ['type' => 'customer'])}}" data-container=".contact_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')</button></p>
    </div>
     </div>
      
   
    @endcomponent
 <div class="table-responsive">
           <table class="table table-bordered table-striped"  id="Stock_conversion" style ="width:100%;">
            <thead>
              
                
                <tr>
                 <th>  Action</th>
                        <th >
                        @lang('lang_v1.location')
                    </th>
                    <th >
                        @lang('lang_v1.date')
                    </th>
                    <th >
                      Form Number
                    </th>

                    <th >
                       Product Convert From
                    </th>
                     <th >
                       Unit Convert From
                    </th> 
                    <th >
                       Quantity
                    </th>
                    <th >
                     Product Convert to
                    </th>
                    <th >
                        Unit Convert to
                    </th>
                    
                    <th >
                      Quantity Convert to
                    </th>
                    <th >
                        User
                    </th>
                     <!--  <th class="notexport">
                         @lang('lang_v1.action')
                    </th> -->
                </tr>
               
            </thead>
             
         
       
        </table>
    </div>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>

<div class="popupshow">
  <div class="overlay"></div>
  <div class="img-show">
    <span>X</span>
    <img src="">
  </div>
</div>
 
<!-- /.content -->

@endsection

@section('javascript')

@if(session('status'))
    @if(session('status')['success'])
        <script>
            toastr.success('{{ session("status")["msg"] }}');
        </script>
    @else
        <script>
            toastr.error('{{ session("status")["msg"] }}');
        </script>
    @endif
@endif
 
<script>



    $('#expense_date_range').on('apply.daterangepicker', function(ev, picker) {
        if (picker.chosenLabel === 'Custom Date Range') {
            $('#target_custom_date_input').val('expense_date_range');
            $('.custom_date_typing_modal').modal('show');
        }
    });
    $('#custom_date_apply_button').on('click', function() {
        if($('#target_custom_date_input').val() == "expense_date_range"){
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStartDate = moment(startDate).format(moment_date_format);
                let formattedEndDate = moment(endDate).format(moment_date_format);

                $('#expense_date_range').val(
                    formattedStartDate + ' ~ ' + formattedEndDate
                );

                $('#expense_date_range').data('daterangepicker').setStartDate(moment(startDate));
                $('#expense_date_range').data('daterangepicker').setEndDate(moment(endDate));

                $('.custom_date_typing_modal').modal('hide');
            } else {
                alert("Please select both start and end dates.");
            }
        }
    });


     $('#product_convert_from').on('change', function() {
        var dataTable = $('#Stock_conversion').DataTable();
             dataTable.search('66').draw();
             var selectedText = $('#product_convert_from').find('option:selected').text();
            
            $('#Stock_conversion').DataTable().search(selectedText).draw();
        });
          $('#product_convert_to').on('change', function() {
              var dataTable = $('#Stock_conversion').DataTable();
                 dataTable.search('66').draw();
            var selectedValue = $('#product_convert_to').find('option:selected').text();
           
            $('#Stock_conversion').DataTable().search(selectedValue).draw();
        });
            $('#conversion_from_no').on('change', function() {
                var dataTable = $('#Stock_conversion').DataTable();
                  dataTable.search('').draw();
            var selectedValue = $('#conversion_from_no').find('option:selected').text();
             
            $('#Stock_conversion').DataTable().search(selectedValue).draw();
            
        });
           $('#unit_convert_from').on('change', function() {
               var dataTable = $('#Stock_conversion').DataTable();
                 dataTable.search('').draw();
            var selectedValue = $('#unit_convert_from').find('option:selected').text();
             
            $('#Stock_conversion').DataTable().search(selectedValue).draw();
        });
          $('#unit_convert_to').on('change', function() {
              var dataTable = $('#Stock_conversion').DataTable();
               dataTable.search('').draw();
            var selectedValue = $('#unit_convert_to').find('option:selected').text();
           
            $('#Stock_conversion').DataTable().search(selectedValue).draw();
        });
    
    

    $(document).ready(() => {
      
        
    $('#Stock_conversion').DataTable({
       
    processing: true,
    serverSide: true, // Set serverSide to true for server-side processing
    ajax: {
        url: "{{ action('StockConversionController@index') }}",
        data: function(d) {
          
            d.conversion_from_no = $("#conversion_from_no").val();
            d.product_convert_from = $("#product_convert_from").val();
            d.unit_convert_from = $("#unit_convert_from").val();
            d.product_convert_to = $("#product_convert_to").val();
            d.unit_convert_to = $("#unit_convert_to").val();
        }
    } ,
    columnDefs: [{
        targets: 1,
        orderable: false,
        searchable: false
    }],
    columns: [
        { data: 'action', name: 'action' },
        { data: 'location', name: 'location' },
        { data: 'created_at', name: 'stock_conversions.created_at' },
        { data: 'conversion_form_no', name: ' conversion_from_no' },
        // Add additional columns here
        { data: 'productname', name: 'productname' },
        { data: 'unit_convert_from', name: 'unit_convert_from' },
        { data: 'total_qty_convert_from', name: 'quantity' },
        { data: 'product_convert_to', name: 'product_convert_to' },
        { data: 'unit_convert_to', name: 'unit_convert_to' },
        { data: 'qty_convert_to', name: 'quantity_convert_to' },
        { data: 'user', name: 'user' },
    ],
    createdRow: function(row, data, dataIndex) {
        // Add any custom row creation logic here
    }
    
    
});
         
    });
    
    
</script>

@endsection
