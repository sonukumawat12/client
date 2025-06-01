@extends('layouts.app')
@section('title', __('lang_v1.'.$type.'s'))

@section('content')

<!-- Content Header (Page header) -->
<style>
  #supplier_mapping_filter {
   margin-top: -30px;
}
.dataTables_wrapper .dataTables_filter input {
  margin-top: 0px;
}
.dataTables_wrapper .dataTables_filter label {
    margin-top: -60px;
}
</style>
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
                <h4 class="page-title pull-left">Contacts</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Product</a></li>
                    <li><span>Product Bind contacts</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>



<!-- Main content -->
<section class="content main-content-inner">
    <div class="row">
        <div class="col-sm-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="form-group col-sm-2 form-inline">
                    <div class="form-group" style="margin-top: 30px;">
                    <button type="button" class="btn btn-primary" id="date_filter">
                    <span>
                    <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                    </span>
                    <i class="fa fa-caret-down"></i>
                    </button>
                    </div>
            </div>
 <div class="form-group col-sm-3 form-inline">
  <div class="form-group" style="width: 100%;">
    {!! Form::label('type', __('lang_v1.supplier')) !!}
    {!! Form::select('type', $name, !empty($type) ? $type : null, [
      'class' => 'form-control select2',
      'id' => 'type',
      'placeholder' => __('messages.please_select'),
      'required'
    ]) !!}
  </div>
</div>
                <div class="form-group col-sm-3 form-inline">
                <div class="form-group"  style="width: 100%;">
                {!! Form::label('type', __('lang_v1.products')  ) !!}
                
                {!! Form::select('names', $names, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
                'names','placeholder'
                => __('messages.please_select'), 'required']); !!}
                </div>
            </div>
            <div class="form-group col-sm-3 form-inline">
                <div class="form-group"  style="width: 100%;">
                {!! Form::label('type', __('lang_v1.mapping_status')) !!}
                {!! Form::select('names', ['mapped' => 'Mapped', 'unmapped' => 'Unmapped'], !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' => 'mapnames', 'placeholder' => __('messages.please_select'), 'required']) !!}
                </div>
            </div>
            @endcomponent
            
        </div>
     
          
          
    </div>
    
    
    
    
    @php
        if($type == 'customer'){
            $colspan = 19;

        }else{
            $colspan = 17;
        }

    @endphp
 
    <div class="row">
   <div class="box-tools pull-right">
        <p class="text-muted">
    <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
    <a href="{{ action('SupplierMappingController@addMapping') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i> @lang('messages.add')
    </a>
</p>
    </div>
     </div>
          
    @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create'))
    @slot('tool')
    
    @endslot
    @endif
    @if(auth()->user()->can('supplier.view') || auth()->user()->can('customer.view'))
    <div class="table-responsive">
 
  
           <table class="table table-bordered table-striped"  id="supplier_mapping">
            <thead>
              
                
                <tr>
                 
                    <th >
                    @lang('lang_v1.date')
                    </th>
                    <th >
                        @lang('lang_v1.supplier_list')
                    </th>

                    <th >
                        @lang('lang_v1.products')
                    </th>
                     
                       <th >
                        @lang('lang_v1.status')
                    </th>
                </tr>
               
            </thead>
            
            
                  
             
            @foreach($products_mappings as $mapping)
            
            <tr>
                 <td>{{ $mapping->date }}</td>
                <td>{{ $mapping->supplier_name }}</td>
                 <td>{{ $mapping->product_name }}</td>
              
                 <td style="color: {{ $mapping->status === 'Mapped' ? 'blue' : 'red' }}">
                    {{ $mapping->status }}
                </td>
            </tr>
         @endforeach
             
         
       
        </table>
    </div>
    @endif
 

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
<script src="{{ asset('js/reports.js?v=' . $asset_v) }}"></script>
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
    let table= $('#supplier_mapping').DataTable({
  "paging": true,
  "searching": true,
  "ordering": true,
  "info": true,
  "dom": '<"row"<"col-sm-3"l><"col-sm-6"B>>frtip',
  "buttons": [
    {
      extend: 'excel',
      text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
      className: 'btn btn-sm btn-default',
      exportOptions: {
        columns: ':visible:not(.notexport)'
      }
    },
    {
      extend: 'colvis',
      text: '<i class="fa fa-columns"></i> Column Visibility',
      className: 'btn btn-sm btn-default',
      exportOptions: {
        columns: ':visible:not(.notexport)'
      }
    },
    {
      extend: 'pdf',
      text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
      className: 'btn btn-sm btn-default',
      exportOptions: {
        columns: ':visible:not(.notexport)'
      }
    },
    {
      extend: 'print',
      text: '<i class="fa fa-print"></i> Print',
      className: 'btn btn-sm btn-default',
      exportOptions: {
        columns: ':visible:not(.notexport)'
      }
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
                 
    // Refilter the table
    // document.querySelectorAll('#min, #max').forEach((el) => {
    //     el.addEventListener('change', () => table.draw());
    // });
    $('#date_filter').daterangepicker({
                    singleDatePicker: false, // For selecting a single date
                    showDropdowns: true, // To show the dropdown for predefined date ranges
                    locale: {
                        format: 'YYYY-MM-DD', // Adjust the date format according to your needs
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Custom Date Range': [moment().startOf('month'), moment().endOf(
                            'month')], // Default custom date range (this can be modified)
                    }
                }, function(start, end, label) {
                    if (label === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else{
                        // Set the selected date in the input
                        $('#date_filter').val(start.format('YYYY-MM-DD'));

                        // Refresh DataTable with new date
                        table.draw();
                    }
                });

                $('#custom_date_apply_button').on('click', function () {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);
                    let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                    // === Update #9c_date_range if it exists ===
                    if ($('#date_filter').length) {
                        $('#date_filter').val(fullRange);
                        $('#date_filter').data('daterangepicker').setStartDate(moment(startDate));
                        $('#date_filter').data('daterangepicker').setEndDate(moment(endDate));
                        $("#report_date_range").text("Date Range: " + fullRange);
                        table.draw();
                    }
                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
//  if ($('#date_filter').length == 1) {
//         $('#date_filter').daterangepicker(
//             dateRangeSettings,
//             function (start, end) {
//                 $('#stock_adjustment_date_filter span').html(
//                     start.format(date_filter) + ' ~ ' + end.format(moment_date_format)
//                 );
//                let minDate, maxDate;
 
//                 // Custom filtering function which will search data in column four between two values
//                 DataTable.ext.search.push(function (settings, data, dataIndex) {
//                     let min = minDate.val();
//                     let max = maxDate.val();
//                     let date = new Date(data[4]);
                 
//                     if (
//                         (min === null && max === null) ||
//                         (min === null && date <= max) ||
//                         (min <= date && max === null) ||
//                         (min <= date && date <= max)
//                     ) {
//                         return true;
//                     }
//                     return false;
//                 });
                 
//                 // Create date inputs
//                 minDate = new DateTime('#min', {
//                     format: 'MMMM Do YYYY'
//                 });
//                 maxDate = new DateTime('#max', {
//                     format: 'MMMM Do YYYY'
//                 });
                 
//                 // DataTables initialisation
//                 let table = new DataTable('#supplier_mapping');
                 
//                 // Refilter the table
//                 document.querySelectorAll('#min, #max').forEach((el) => {
//                     el.addEventListener('change', () => table.draw());
//                 });
//             }
//         );
//         $('#date_filter').on('cancel.daterangepicker', function (ev, picker) {
//             $('#purchase_sell_date_filter').html(
//                 '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
//             );
//         });
//        // updateStockAdjustmentReport();
//     }
 $(document).ready(function() {
     
  $(document).ready(function() {
    

 $(document).ready(function() {

    $('#type').on('change', function() {
            //var selectedValue = $('#type').val();
             var selectedText = $('#type').find('option:selected').text();
            console.log(selectedText);
            $('#supplier_mapping').DataTable().search(selectedText).draw();
        });
          $('#names').on('change', function() {
            var selectedValue = $('#names').find('option:selected').text();
             console.log(selectedValue);
            $('#supplier_mapping').DataTable().search(selectedValue).draw();
        });
});

    $('#type, #names, #mapnames').on('change', function() {
        var selectedText = $(this).find('option:selected').text();
        supplierMappingTable.search(selectedText).draw();
    });
});
    $('#type').on('change', function() {
            //var selectedValue = $('#type').val();
             var selectedText = $('#type').find('option:selected').text();
            console.log(selectedText);
            $('#supplier_mapping').DataTable().search(selectedText).draw();
        });
          $('#names').on('change', function() {
            var selectedValue = $('#names').find('option:selected').text();
             console.log(selectedValue);
            $('#supplier_mapping').DataTable().search(selectedValue).draw();
        });
          $('#mapnames').on('change', function() {
            var selectedValue = $('#mapnames').find('option:selected').text();
             console.log(selectedValue);
            $('#supplier_mapping').DataTable().search(selectedValue).draw();
        });
});
    $('#contact_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#contact_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            contact_table.ajax.reload();
        }
    );
    $('#contact_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#contact_list_filter_date_range').val('');
        contact_table.ajax.reload();
    });
    $('.contact_modal').on('shown.bs.modal', function() {
        $('.contact_modal')
        .find('.select2')
        .each(function() {
            var $p = $(this).parent();
            $(this).select2({ 
                dropdownParent: $p
            });
        });

    });
    $(document).on('click', '#delete-selected', function(e){
        e.preventDefault();
        var selected_rows = getSelectedRows();

        if(selected_rows.length > 0){
        $('input#selected_rows').val(selected_rows);
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                $('form#mass_delete_form').submit();
                }
            });
        } else{
        $('input#selected_rows').val('');
            swal('@lang("lang_v1.no_row_selected")');
        }
    });
    
    
    $(document).on('click', '#export-selected', function(e){
        e.preventDefault();
        var selected_rows = getSelectedRows();

        if(selected_rows.length > 0){
        $('input#ob_selected_rows').val(selected_rows);
            $('form#export_ob_form').submit();
        } else{
        $('input#ob_selected_rows').val('');
            swal('@lang("lang_v1.no_row_selected")');
        }
    });
    
    
    function getSelectedRows() {
        var selected_rows = [];
        var i = 0;
        $('.row-select:checked').each(function () {
            selected_rows[i++] = $(this).val();
        });

        return selected_rows;
    }
    // document.addEventListener("DOMContentLoaded", function(){
    //     $.ajax({
    //         method: 'get',
    //         url: '/contacts/get_outstanding?type='+ "{{$type}}",
    //         success: function(result) {
    //             if (result && Object.keys(result).length > 0) {
    //                 $('#total_outstanding').text(result.total_outstanding);
    //                 $('#total_overpayment').text(result.total_overpayment);
    //             // $('#total_os').html(result);
    //             __currency_convert_recursively($('#contact_table'));
    //             }
    //         },
    //     });

    // });
    $(document).on('change','#assigned_to',function(){
        $('#contact_table').DataTable().ajax.reload();

    });
    
    $(document).ready(function(){

    
    $('body').on('click', '.popup', function () { 
        var $src = $(this).attr("src");
        $(".popupshow").fadeIn();
        $(".img-show img").attr("src", $src);
    });
    
   
        $('body').on('click', '.overlay', function () {

        $(".popupshow").fadeOut();
    });
    $('body').on('click', 'span', function () {
        $(".popupshow").fadeOut();
    });
});


</script>
@endsection
