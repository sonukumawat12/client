@extends('layouts.app')
@section('title', 'View Customer Loan')

@section('content')

@php
$business_id = request()->session()->get('user.business_id');
$type = !empty($type) ? $type : 'customer';
$customer_groups = App\ContactGroup::where('business_id',$business_id)
                        ->where(function ($query) use ($type) {
                            $query->where('contact_groups.type', 'customer')
                                ->orWhere('contact_groups.type', 'both');
                        })->pluck('name','id');
@endphp

<style>
    .select2{
        width: 100% !important;
    }
</style>
<style>
    @media print {
        @page {
            size: A5;
            margin: 0;
        }
        
        body {
            margin: 1cm;
        }
    }
</style>
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
               <h5 class="page-title pull-center">@lang('lang_v1.view_customer_loan')</h5>
                
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content main-content-inner">

    <div class="row">
         <div class="col-md-3">
            
                 <p>@lang('lang_v1.customer'):{{$name}}</p>
                 
                
        </div>
       
    </div>
      <div class="row">
         <div class="col-md-3">
             
                
                <p>@lang('lang_v1.location'): {{$location}}</p>
          </div>
         
         
         
    </div>
    <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="airline_tickets_table" style="width: 80%;">
                            <thead>
                                <tr>
                                    
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    
                                    <th>{{__( 'airline::lang.airline_invoice_no' ) }}</th>
                                    <th>{{__( 'airline::lang.prt_ticket_no' )}}</th>
                                    <th>{{__( 'airline::lang.air_ticket_date' )}}</th>
                                     <th>@lang('lang_v1.amount')</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($add_commission as $commission)
                                <tr>
                        
                                    <td>{{ $commission->date }}</td>
                                   
                                    <td>{{ $commission->invoice_no }}</td>
                                    <td>{{ $commission->ticket_no }}</td>
                                    <td>{{ $commission->air_ticket_date }}</td>
                                      <td>{{ $commission->commision_amount }}</td> 
                                </tr>
                                @endforeach
                               
                            </tbody>
                        </table>
                    </div>
                </div>
                  
                  <div class="row">
                    <div class="col-md-3" style=margin-left:20px;>
                    
                    <p>@lang('lang_v1.authorised_officer'): </p>
                    <p>@lang('lang_v1.approved_by'): </p>
                    <p>@lang('lang_v1.signature'): </p>
                </div>
                
                
                </div>
                 
                <div class="col-md-9">
                <button type="button" id="commission_print"  class="btn btn-primary btn-modal pull-right">@lang('messages.print')</button>
            </div>
            </div>
            
           
             
        </div>
           
         
     
     
     
 
 
</section>
<script>
    // Print button click event handler
    document.getElementById('commission_print').addEventListener('click', function() {
        // Open print preview
        window.print();
    });
</script>
<!-- /.content -->
<style>
  .nav-tabs-custom>.nav-tabs>li.active a{
    color:#3c8dbc;
  }
  .nav-tabs-custom>.nav-tabs>li.active a:hover{
    color:#3c8dbc;
  }
</style>
@endsection

@section('javascript')
 

@endsection