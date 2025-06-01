@extends('layouts.app')
@section('title', __('Supplier List'))

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
                <h4 class="page-title pull-left">Airline</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Airline</a></li>
                    <li><span>Manage Suppliers</span></li>
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
              <div class="form-group col-sm-4 form-inline">
                  
                  <div class="input-group">
                      {!! Form::label('user_id', __('lang_v1.assigned_to'), ['class' => 'mr-2']) !!}: &nbsp;
                    <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                    </span>
                    {!! Form::select('user_id', $user_groups, null, ['class' => 'form-control select2', 'id' => 'assigned_to']) !!}
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
    <input type="hidden" value="{{$type}}" id="contact_type">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'Supplier List', ['contacts' =>
    __('lang_v1.'.$type.'s') ])])
    
   <div class="box-tools pull-right">
        <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
        <button type="button" class="btn btn-primary btn-modal"
            data-href="{{action('ContactController@create', ['type' => $type,'module'=>'airline'])}}" data-container=".contact_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
        
    
    @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create'))
    @slot('tool')
    
    @endslot
    @endif
    @if(auth()->user()->can('supplier.view') || auth()->user()->can('customer.view'))
    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%" id="contact_table">
            <thead>
                <tr>
                    <td colspan="9">
                        <div class="row">
                            <div class="col-sm-2">
                                @if(auth()->user()->can('customer.delete') || auth()->user()->can('supplier.delete'))
                                    {!! Form::open(['url' => action('ContactController@massDestroy'), 'method' => 'post', 'id'
                                    => 'mass_delete_form' ]) !!}
                                    {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']); !!}
                                    {!! Form::submit(__('lang_v1.delete_selected'), array('class' => 'btn btn-xs btn-danger',
                                    'id' => 'delete-selected')) !!}
                                    {!! Form::close() !!}
                                @endif
                            </div>
                            <div class="col-sm-2">
                                {!! Form::open(['url' => action('ContactController@exportBalance'), 'method' => 'post', 'id'
                                => 'export_ob_form' ]) !!}
                                {!! Form::hidden('selected_rows', null, ['id' => 'ob_selected_rows']); !!}
                                {!! Form::submit(__('lang_v1.export'), array('class' => 'btn btn-xs btn-success',
                                'id' => 'export-selected')) !!}
                                {!! Form::close() !!}
                            </div>
                            </div>

                        </div>
                        
                    </td>
                    <td>
                        <table style="min-width: 23rem">
                                <tr>
                                    <th>Total Outstanding</th>
                                    <td>:</td>
                                    <td>
                                        <span id="total_outstanding" class="display_currency" style="margin-left: 0.5rem;">0</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Overpayment</th>
                                    <td>:</td>
                                    <td>
                                        <span id="total_overpayment" class="display_currency" style="margin-left: 0.5rem;">0</span>
                                    </td>
                                </tr>
                            </table>
                    </td>
                </tr>
                
                <tr>
                    <th><input type="checkbox" id="select-all-row"></th>
                    <th class="notexport">@lang('messages.action')</th>
                    <th >@lang('lang_v1.contact_id')</th>
                    @if($type == 'supplier')
                    <th>@lang('business.business_name')</th>
                    <th>@lang('contact.name')</th>
                    <th>@lang('contact.mobile')</th>
                    <th>@lang('lang_v1.supplier_group')</th>
                    <th>Assign To</th>
                    <th>@lang('contact.pay_term')</th>
                    <th>@lang('contact.total_purchase_due')</th>
                    <th>@lang('lang_v1.total_purchase_return_due')</th>
                    <!--<th html="true">@lang('contact.opening_bal_due')</th>-->
                    <th>@lang('account.opening_balance')</th>
                    <th>@lang('business.email')</th>
                    <th>@lang('contact.tax_no')</th>
                    <th>@lang('lang_v1.added_on')</th>
                    @elseif( $type == 'customer')
                        <th>@lang('user.name')</th>
                        <th>@lang('contact.mobile')</th>
                        <th>@lang('lang_v1.customer_group')</th>
                        <th>Assign To</th>
                        <th>@lang('lang_v1.credit_limit')</th>
                        <th style="color: #9D0606">@lang('contact.total_due')</th>
                        <!-- <th width="150" style="min-width: 100px"> @lang('contact.total_sale_due')</th> -->
                        <th> @lang('lang_v1.total_sell_return_due') </th>
                        <th>@lang('contact.pay_term')</th>
                        <!-- <th width="125">@lang('account.opening_balance')</th> -->

                        <!--
                        <th>@lang('contact.tax_no')</th>
                        <th>@lang('business.email')</th>
                        <th>@lang('business.address')</th>
                        -->
                        <th>
                            @lang('Photo')
                        </th>
                        <th>
                            @lang('lang_v1.signature')
                        </th>
                        <th>@lang('lang_v1.added_on')</th>
                    @if($reward_enabled)
                    <th id="rp_col">{{session('business.rp_name')}}</th>
                    @endif
                    @endif
                    <th class="contact_custom_field1 @if($is_property && !array_key_exists('property_customer_custom_field_1', $contact_fields)) hide @endif  @if($type=='customer' && !array_key_exists('customer_custom_field_1', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_1', $contact_fields)) hide @endif">
                        @lang('lang_v1.contact_custom_field1')
                    </th>

                    <th class="contact_custom_field2 @if($is_property && !array_key_exists('property_customer_custom_field_2', $contact_fields)) hide @endif  @if($type=='customer' && !array_key_exists('customer_custom_field_2', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_2', $contact_fields)) hide @endif">
                        @lang('lang_v1.contact_custom_field2')
                    </th>

                    <th class="contact_custom_field3 @if($is_property && !array_key_exists('property_customer_custom_field_3', $contact_fields)) hide @endif  @if($type=='customer' && !array_key_exists('customer_custom_field_3', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_3', $contact_fields)) hide @endif">
                        @lang('lang_v1.contact_custom_field3')
                    </th>

                    <th class="contact_custom_field4 @if($is_property && !array_key_exists('property_customer_custom_field_4', $contact_fields)) hide @endif  @if($type=='customer' && !array_key_exists('customer_custom_field_4', $contact_fields)) hide @endif @if($type=='supplier' && !array_key_exists('supplier_custom_field_4', $contact_fields)) hide @endif">
                        @lang('lang_v1.contact_custom_field4')
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr class="bg-gray font-17 text-center footer-total">
                    <td @if($type=='supplier' ) colspan="8" @elseif( $type=='customer' ) @if($reward_enabled)
                        colspan="7" @else colspan="7" @endif @endif>
                        <strong>
                            @lang('sale.total'):
                        </strong>
                    </td>
                    
                    @if($type == 'supplier')
                    <td><span class="display_currency" id="footer_pay_term" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_tot_due" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_contact_return_due" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_contact_opening_balance" data-currency_symbol="true"></span></td>
                    <td></td>
                    <td></td>
                    <td></td>
                     @endif
                    @if($type == 'customer')
                    <td><span class="display_currency" id="footer_tot_credit_limit" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_tot_due" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_contact_return_due" data-currency_symbol="true"></span></td>
                    <td><span class="display_currency" id="footer_pay_term" data-currency_symbol="true"></span></td>
                    <td></td>
                     @endif

                </tr>
            </tfoot>
        </table>
    </div>
    @endif
    @endcomponent

    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    
    <!-- Modal for Linked Supplier Account -->
    <div class="modal fade linked_account_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
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
{!! Form::hidden('module','airline', ['id'=>'module']) !!}

<script>
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
    $('.linked_account_modal').on('shown.bs.modal', function() {
        console.log("clicked")
        $('.linked_account_modal')
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
        contact_table.ajax.reload();

    });
    $(document).ready(function(){

    // Popup functionality
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

    // Open the modal and load existing linked accounts
    $('.linked_account_modal').on('shown.bs.modal', function () {
        console.log("Modal opened");

        // Attach the change event to account group dropdown
        $('#account_group').off('change').on('change', function() {
            console.log("Account Group changed");
            let accountGroupId = $(this).val();
            $.ajax({
                url: '{{ route("get.accounts.by.group", ":accountGroupId") }}'.replace(':accountGroupId', accountGroupId),
                method: 'GET',
                success: function(data) {
                    $('#account').empty().append('<option value="">{{ __("Select Account") }}</option>');
                    $.each(data.accounts, function(index, account) {
                        $('#account').append('<option value="' + account.id + '">' + account.name + '</option>');
                    });
                }
            });
        });

        $('#account_types').off('change').on('change', function() {
            let accountTypeId = $(this).val();
            let businessId = $('#business_id').val();
            $.ajax({
                url: '{{ route("get_account_sub_types", ["businessId" => ":businessId", "accountTypeId" => ":accountTypeId"]) }}'.replace(':accountTypeId', accountTypeId).replace(':businessId', businessId),
                method: 'GET',
                success: function(data) {
                    $('#sub_account_types').empty().append('<option value="">{{ __("Select Sub Account Type") }}</option>');
                    $.each(data.account_subs, function(index, account) {
                        $('#sub_account_types').append('<option value="' + account.id + '">' + account.name + '</option>');
                    });
                }
            });
        });

        $('#sub_account_types').off('change').on('change', function() {
            let accountTypeId = $(this).val();
            let businessId = $('#business_id').val();
            $.ajax({
                url: '{{ route("get_account_by_sub_type", ["businessId" => ":businessId", "accountTypeId" => ":accountTypeId"]) }}'.replace(':accountTypeId', accountTypeId).replace(':businessId', businessId),
                method: 'GET',
                success: function(data) {
                    $('#account').empty().append('<option value="">{{ __("Select Account") }}</option>');
                    $.each(data.accounts, function(index, account) {
                        $('#account').append('<option value="' + account.id + '">' + account.name + '</option>');
                    });
                }
            });
        });

        // Fetch existing linked supplier accounts and populate the table
        let supplier_id = $('#supplier_id').val();
        $.ajax({
            url: '{{ route("airline.get_linked_supplier_accounts") }}?supplier_id=' + supplier_id,
            method: 'GET',
            success: function(response) {
                console.log(response)
                $('#added_accounts_table tbody').empty(); // Clear existing rows

                $.each(response.accounts, function(index, account) {
                    let newRow = `<tr data-id="${account.id}">
                                    <td>${account.dateTime}</td>
                                    <td>${account.typeName}</td>
                                    <td>${account.subType}</td>
                                    <td>${account.user}</td>
                                    <td>${account.accountName}</td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-primary edit-account">Edit</button>
                                        <button type="button" class="btn btn-xs btn-danger delete-account">Delete</button>
                                    </td>
                                  </tr>`;
                    $('#added_accounts_table tbody').append(newRow);
                });
            },
            error: function() {
                alert('Error fetching existing linked supplier accounts.');
            }
        });

        // Saving the linked supplier account
        $('#save_linked_account').click(function() {
            let isValid = true;

            // Validate start
            $('#linked_supplier_account_form .form-group').each(function() {
                const input = $(this).find('input, select, textarea');
                if (input.val().trim() === "") {
                    isValid = false;
                    $(this).addClass('has-error');
                    if (!$(this).find('.error-message').length) {
                        $(this).append('<span class="error-message" style="color: red;">Required to Fill</span>');
                    }
                } else {
                    $(this).removeClass('has-error');
                    $(this).find('.error-message').remove();
                }
            });
            if (!isValid) {
                toastr.error('Please fill all required fields.');
                return;
            }
            $('#linked_supplier_account_form input, #linked_supplier_account_form select, #linked_supplier_account_form textarea').on('input change', function() {
                const formGroup = $(this).closest('.form-group');
                if ($(this).val().trim() !== "") {
                    formGroup.removeClass('has-error');
                    formGroup.find('.error-message').remove();
                }
            });
            // Validate end

            let id = $('#linked_account_id').val();
            let businessId = $('#business_id').val();
            let dateTime = $('#date_time').val();
            let accountGroup = $('#account_group option:selected').val(); // Get the selected account group id
            let account = $('#account option:selected').val(); // Get the selected account id
            let account_type = $('#account_types option:selected').val(); // Get the selected account id
            let sub_account_type = $('#sub_account_types option:selected').val(); // Get the selected account id
            let supplier_id = $('#supplier_id').val();
            
            console.log(accountGroup, account, account_type, sub_account_type)
            
            $.ajax({
                url: '{{ route("airline.submit_linked_supplier_account") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    date_time: dateTime,
                    account_group_id: accountGroup,
                    account_id: account,
                    business_id: businessId,
                    account_type: account_type,
                    sub_account_type: sub_account_type,
                    supplier_id: supplier_id,
                },
                success: function(response) {
                    console.log(response)
                    if (response.success) {
                        $('#added_accounts_table tbody').empty();
                        $.each(response.accounts, function(index, account) {
                            let newRow = `<tr data-id="${account.id}">
                                            <td>${account.dateTime}</td>
                                            <td>${account.typeName}</td>
                                            <td>${account.subType}</td>
                                            <td>${account.user}</td>
                                            <td>${account.accountName}</td>
                                            <td>
                                                <button type="button" class="btn btn-xs btn-primary edit-account">Edit</button>
                                                <button type="button" class="btn btn-xs btn-danger delete-account">Delete</button>
                                            </td>
                                        </tr>`;
                            $('#added_accounts_table tbody').append(newRow);
                        });
                        // Clear the form after saving
                        $('#linked_supplier_account_form')[0].reset();
                    } else {
                        toastr.error(response.msg);
                    }
                }
            });
        });
    });

    // Edit account
$('body').on('click', '.edit-account', function () {
    let row = $(this).closest('tr');
    console.log(row);
    let accountId = row.data('id'); // Ensure `data-id` exists on the row

    // Fetch the account details based on the ID
    $.ajax({
        url: '{{ route("airline.get_linked_supplier_account", ":id") }}'.replace(':id', accountId),
        method: 'GET',
        success: function (data) {
            $('#sub_account_types').val(data.account.accountTypeId);

            if (data.account.id) {
                $('#linked_account_id').val(data.account.id);
            } else {
                console.error("ID is missing.");
            }
            // Format the date properly
            if (data.account.date) {
                let formattedDate = new Date(data.account.date);
                if (!isNaN(formattedDate.getTime())) { // Check if the date is valid
                    $('#date_time').val(formattedDate.toISOString().slice(0, 16));
                } else {
                    console.error("Invalid date format: " + data.account.date);
                }
            }

            // Populate other form fields
            if (data.account.accountGroupId) {
                $('#account_group').val(data.account.accountGroupId);
            } else {
                console.error("Account group ID is missing.");
            }

            if (data.account.accountId) {
                $('#account').val(data.account.accountId);
            } else {
                console.error("Account ID is missing.");
            }

            // Open the modal for editing
            $('#linkedSupplierAccountModal').modal('show');
        },
        error: function (xhr, status, error) {
            console.error("Error fetching account details:", error);
        }
    });
});


    // Delete account
    $('body').on('click', '.delete-account', function() {
        let row = $(this).closest('tr');
        let id = row.data('id'); // Get the account ID from the row
        // console.log(accountId)

        // Confirm deletion
        if (confirm('Are you sure you want to delete this account?')) {
            $.ajax({
                url: '{{ route("airline.delete_linked_supplier_account", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        row.remove(); // Remove the row from the table
                        alert('Account deleted successfully');
                    } else {
                        alert('Error deleting account');
                    }
                }
            });
        }
    });

});



</script>
@endsection
