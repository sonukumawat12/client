@extends('layouts.app')
@section('title', __('contact.view_contact'))
@section('content')


<div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#settings" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('contact.settings')</strong>
                        </a>
                    </li>
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#customer_sms_settings" class="" data-toggle="tab">
                            <i class="fa fa-list"></i>
                            <strong>@lang('contact.customer_sms_settings')</strong>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="settings">
            @include('contact.partials.settings')
        </div>
        <div class="tab-pane" id="customer_sms_settings">
            @include('contact.partials.customer_sms_settings')
        </div>
        
    </div>
@endsection

@section('javascript')

 <script>
    
    //CRM Group table
  
    customer_sms_settings_table = $('#customer_sms_settings_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("CustomerSmsSettingController@index")}}',
            data: function (d) {
              
            }
        },
        columns: [
            { data: 'lname', name: 'business_locations.name' },
            { data: 'date_time', name: 'date_time' },
            { data: 'show_customer', name: 'show_customer' },
            { data: 'show_supplier', name: 'show_supplier' },
            { data: 'username', name: 'users.name' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {
          
        },
    });

    $(document).on('submit', 'form#customer_sms_settings_form', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        
        $.ajax({
            method: 'POST',
            url: '{{action("CustomerSmsSettingController@store")}}',
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $('div.crm_groups_modal').modal('hide');
                    toastr.success(result.msg);
                    customer_sms_settings_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
        

       
    });
    

    $(document).on('click', 'button.crm_group_edit_button', function() {
        $('div.edit_crm_groups_modal').load($(this).data('href'), function() {
            $(this).modal('show');
            
            

            $('form#customer_sms_settings_edit_form').submit(function(e) {
                e.preventDefault();
                var data = $(this).serialize();
                var url = $(this).attr('action');
                
                console.log(data);
                
                $.ajax({
                    method: 'PUT',
                    url: url,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div.edit_crm_groups_modal').modal('hide');
                            toastr.success(result.msg);
                            customer_sms_settings_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
                
                
            });
        });
    });


    $(document).on('click', 'button.delete_crm_group_button', function() {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_customer_group,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            customer_sms_settings_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });


    </script>

@endsection