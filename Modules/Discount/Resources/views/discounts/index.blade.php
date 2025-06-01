@extends('layouts.app')
@section('title', 'Discount Level')
@section('content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('discount::lang.discount')</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('discount::lang.discount')</a></li>
                    <li><span>@lang('discount::lang.manage_discount')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<section class="content main-content-inner">
    <div class="row">
        <div class="col-md-12">
        
            <!-- general form elements -->
            <div class="box box-primary">
        <div class="box-header">
        	<h3 class="box-title">@lang('discount::lang.discount_list')</h3>
            <div class="row">
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary btn-modal adddiscount">
                    <i class="fa fa-plus"></i> @lang('discount::lang.add_discount')</button>
                </div>
            </div>
        </div>
        </div>
        @include('discount::discounts.create_discount')
            </div>
            </div>
</section>
<!-- /.content -->
@stop
@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script type="text/javascript">

 
$(document).ready(function(){ 
  /* BEGIN: Save button */
        $('#savebutton').click(function() {
            $('#savebutton').text('Saving...');
            $.ajax({
                url: '{{action("\Modules\Discount\Http\Controllers\NewdiscountController@store")}}',

                success: function(data) {
                    if (data.errors) {
                        lert('sdfklsdkfhs')
                    } else if (data.success) {
                        if (parseFloat(optype) == 1) {
                            $('#savebutton').text('Save');
                            $('#savebutton').prop("disabled", false);
                        } else if (parseFloat(optype) == 2) {
                            $('#savebutton').text('Update');
                            $('#savebutton').prop("disabled", false);
                        }
                        toastrMessage('success', "<span style='color: white;'>Successful</span>",
                            "Success");
                        closeRegisterModal();
                        $("#discountModal").modal('hide');
                    }
                }
            });
        });
});
 // Get the input element
    var discountInput = document.getElementById('discount');

    // Add an event listener to the input event
    discountInput.addEventListener('input', function() {
        // Remove any % symbols from the input value
        discountInput.value = discountInput.value.replace('%', '');
    });
$('.select2').select2({
            placeholder: "Please select here",
            width: "100%"
        });
document.addEventListener('DOMContentLoaded', function () {
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        const hours = String(currentDate.getHours()).padStart(2, '0');
        const minutes = String(currentDate.getMinutes()).padStart(2, '0');
        
        const currentDatetime = `${year}-${month}-${day}T${hours}:${minutes}`;
        
        document.getElementById('date_and_time').value = currentDatetime;
    });
    $('#adddiscount').click(function() {
            $("#discountModal").modal('show');
        });
$(document).ready(function(){
   $('.adddiscount').click(function() {
            $("#discountModal").modal('show');
        });
});
    $(document).on('click', '#deactivate-selected', function(e){
        e.preventDefault();
        var selected_rows = [];
        var i = 0;
        $('.row-select:checked').each(function () {
            selected_rows[i++] = $(this).val();
        }); 
        
        if(selected_rows.length > 0){
            $('input#selected_discounts').val(selected_rows);
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $('form#mass_deactivate_form').submit();
                }
            });
        } else{
            $('input#selected_discounts').val('');
            swal('@lang("lang_v1.no_row_selected")');
        }    
    });

    $('table#discounts_table tbody').on('click', '.activate-discount', function(e){
        e.preventDefault();
        var href = $(this).data('href');
        $.ajax({
            method: "get",
            url: href,
            dataType: "json",
            success: function(result){
                if(result.success == true){
                    toastr.success(result.msg);
                    discounts_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });
</script>
@endsection
