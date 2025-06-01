@extends('layouts.app')
@section('title', __('lang_v1.customer_loans'))

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
                <h4 class="page-title pull-left">@lang('lang_v1.contacts)</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('lang_v1.contacts)</a></li>
                    <li><span>@lang('lang_v1.customer_loans')</span></li>
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
                
               
                {!! Form::label('type', __('lang_v1.customer') . ':') !!}
           {!! Form::select('customer', $customer, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'customer','placeholder'
              => __('messages.please_select'), 'required']); !!}   
                </div>
            </div>
          <div class="col-md-2">
            <div class="form-group">
            {!! Form::label('type', __('lang_v1.loan') . ':') !!}
           {!! Form::select('loan', [], !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'loan','placeholder'
              => __('messages.please_select'), 'required']); !!}   
              </div>
        </div>   
         <div class="col-md-2">
                <div class="form-group">
                {!! Form::label('type', __('lang_v1.user') . ':') !!}
                 {!! Form::select('user', $username, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
              'user','placeholder'
              => __('messages.please_select'), 'required']); !!}
              </div>
            </div>
              

            @endcomponent
            
        </div>
       
          
          
    </div>
    
    
    
   
    <input type="hidden" value="{{$type}}" id="contact_type">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_customer_loans', ['contacts' =>
    __('lang_v1.'.$type.'s') ])])
    
  
        
    
    @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create'))
    @slot('tool')
    
    @endslot
    @endif
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%" id="contact_tables">
            <thead>
                 
                
                <tr>
                    
                    <th class="notexport">@lang('messages.action')</th>
                    <th >@lang('lang_v1.date')</th>
                     
                        <th>@lang('lang_v1.location')</th>
                        <th>@lang('lang_v1.customer')</th>
                        <th>@lang('lang_v1.loan_given') </th>
                        <th>@lang('lang_v1.loan_amount')</th>
                        <th>@lang('lang_v1.added_user')</th>
                </tr>
            </thead>
             
        </table>
    </div>
  
    @endcomponent

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
   $(document).ready(function() {
 
  $.ajax({
    url: '/contacts/customer_loan',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
        if (data.length === 0) {
            $('#contact_tables').append('<tr><td colspan="7">No results found.</td></tr>');
        } else {
            $.each(data, function(index, operation) {
                var row = [
                    '<div class="btn-group">' +
                    '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                    'data-toggle="dropdown" aria-expanded="false">' +
                    'Actions' +
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown' +
                    '</span>' +
                    '</button>' +
                    '<ul class="dropdown-menu dropdown-menu-left" role="menu">'+
                    '<li><a data-href="#" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-eye-open"></i>View</a></li>'+
                    '<li><a data-href="#" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i>Edit</a></li>'+
                    '</div>',
                    operation.created_at,
                    operation.location_name,
                    operation.contact_name,
                    '[Loan Given]',
                    operation.transaction_amount,
                    operation.usernames
                ];
                $('#contact_tables').DataTable().row.add(row).draw();
            });
        }
    },
    error: function(xhr, status, error) {
        console.log("error"); // Handle any error that occurs during the AJAX request
    }
});

  $('#customer').on('change', function() {
       
     var selectedCustomer = $('#customer').val();  
  $.ajax({
    url: '/contacts/customer_loans_list',
    type: 'GET',
    data:{
        customer:selectedCustomer
    },
    dataType: 'json',
    success: function(data) {
          $('#contact_tables').DataTable().clear().draw();
        if (data.length === 0) {
            $('#contact_tables').append('<tr><td colspan="7">No results found.</td></tr>');
        } else {
            $.each(data, function(index, operation) {
                var row = [
                    '<div class="btn-group">' +
                    '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                    'data-toggle="dropdown" aria-expanded="false">' +
                    'Actions' +
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown' +
                    '</span>' +
                    '</button>' +
                    '<ul class="dropdown-menu dropdown-menu-left" role="menu">'+
                    '<li><a data-href="#" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-eye-open"></i>View</a></li>'+
                    '<li><a data-href="#" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i>Edit</a></li>'+
                    '</div>',
                    operation.created_at,
                    operation.location_name,
                    operation.contact_name,
                    '[Loan Given]',
                    operation.transaction_amount,
                    operation.usernames
                ];
                $('#contact_tables').DataTable().row.add(row).draw();
            });
        }
    },
    error: function(xhr, status, error) {
        console.log("error"); // Handle any error that occurs during the AJAX request
    }
});  
    });
    
    //user
    $('#user').on('change', function() {
       
     var selecteduser = $('#user').val();  
  $.ajax({
    url: '/contacts/customer_loans_list',
    type: 'GET',
    data:{
        user:selecteduser
    },
    dataType: 'json',
    success: function(data) {
          $('#contact_tables').DataTable().clear().draw();
        if (data.length === 0) {
            $('#contact_tables').append('<tr><td colspan="7">No results found.</td></tr>');
        } else {
            $.each(data, function(index, operation) {
                var row = [
                    '<div class="btn-group">' +
                    '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                    'data-toggle="dropdown" aria-expanded="false">' +
                    'Actions' +
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown' +
                    '</span>' +
                    '</button>' +
                    '<ul class="dropdown-menu dropdown-menu-left" role="menu">'+
                    '<li><a data-href="/contacts/customer_loan_view?customer_id=' + operation.customer_id + '" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-eye-open"></i>View</a></li>' +
                    '<li><a data-href="#" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i>Edit</a></li>'+
                    '</div>',
                    operation.created_at,
                    operation.location_name,
                    operation.contact_name,
                    '[Loan Given]',
                    operation.transaction_amount,
                    operation.usernames
                ];
                $('#contact_tables').DataTable().row.add(row).draw();
            });
        }
    },
    error: function(xhr, status, error) {
        console.log("error"); // Handle any error that occurs during the AJAX request
    }
});  
    });
    
    //Date Range
     $('#expense_date_range').on('change', function() {
       console.log("hihi");
     var selecteddate = $('#expense_date_range').val();  
  $.ajax({
    url: '/contacts/customer_loans_list',
    type: 'GET',
    data:{
        date:selecteddate
    },
    dataType: 'json',
    success: function(data) {
          $('#contact_tables').DataTable().clear().draw();
        if (data.length === 0) {
            $('#contact_tables').append('<tr><td colspan="7">No results found.</td></tr>');
        } else {
            $.each(data, function(index, operation) {
                var row = [
                    '<div class="btn-group">' +
                    '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                    'data-toggle="dropdown" aria-expanded="false">' +
                    'Actions' +
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown' +
                    '</span>' +
                    '</button>' +
                    '<ul class="dropdown-menu dropdown-menu-left" role="menu">'+
                    '<li><a data-href="/contacts/customer_loan_view?customer_id=' + operation.customer_id + '" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-eye-open"></i>View</a></li>' +
                    '<li><a data-href="#" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i>Edit</a></li>'+
                    '</div>',
                    operation.created_at,
                    operation.location_name,
                    operation.contact_name,
                    '[Loan Given]',
                    operation.transaction_amount,
                    operation.usernames
                ];
                $('#contact_tables').DataTable().row.add(row).draw();
            });
        }
    },
    error: function(xhr, status, error) {
        console.log("error"); // Handle any error that occurs during the AJAX request
    }
});  
    });
});
    
</script>
@endsection
