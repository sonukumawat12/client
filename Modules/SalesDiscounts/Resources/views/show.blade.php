@extends('layouts.app')
@section('title', __('salesdiscounts::lang.salesdiscounts'))

@section('content')
<!-- Main content -->
<section class="content">
     <div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Sales Discounts </h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                       <li><a href="#">List Sales Discount</a></li>
                    <li><span>Show</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
    
 
  <div class="modal-body">
            {!! Form::open(['url' =>  action('\Modules\SalesDiscounts\Http\Controllers\SalesDiscountsController@update',$list->id), 'method' => 'post' ]) !!}
           
               <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        {!! Form::label('transaction_date', __('salesdiscounts::lang.transaction_date')) !!}
                        {!! Form::date('transaction_date', \Carbon\Carbon::parse($list->transaction_date)->format('Y-m-d'), ['class' => 'form-control transaction_date','readonly', 'required', 'placeholder' => __('salesdiscounts::lang.transaction_date')]) !!}
                    </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('customer', __('salesdiscounts::lang.customer') . ':') !!}
                           {!! Form::select('customer', $customers, $list->contact_id, ['class' => 'form-control ','disabled',  'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('location', __( 'salesdiscounts::lang.location' )) !!} 
                            {!! Form::select('location', $business_locations, $list->business_id, ['class' => 'form-control ','disabled',
                            'style' => 'width:100%']); !!}
                          
                        </div>
                    </div>
                    </div>
                      <div class="row">
                          <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('invoice_no', __('salesdiscounts::lang.invoice_no')) !!}
                            {!! Form::text('invoice_no', $list->invoice_no, ['class' => 'form-control','readonly',
                            'required'])
                            !!}
                        </div>
                    </div>   
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('discount_amount', __('salesdiscounts::lang.discount_amount')) !!}
                            {!! Form::text('discount_amount',number_format($list->discount_amount, $currency_precision)  , ['class' => 'form-control',
                            'required'])
                            !!}
                        </div>
                    </div>
                    
                 
                        
                   <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('discount_type', __('salesdiscounts::lang.discount_type')) !!}
                             {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], $list->discount_type, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'id' => 'discount_type','disabled', 'placeholder' => __('salesdiscounts::lang.all')]); !!}
                            
                        </div>
                    </div>
                </div>
                
              

                   <button type="button" class="btn btn-default" data-dismiss="modal" id="closeModal">@lang('messages.close')</button>
            
            {!! Form::close() !!}
            
            </div>

        </div><!-- /.modal-content -->
         
        
          
     
</section>
<!-- /.content -->
 
 <script>
    document.getElementById('closeModal').addEventListener('click', function() {
        window.location.href = "{{ route('salesdiscounts.index') }}";
    });
</script>
@endsection