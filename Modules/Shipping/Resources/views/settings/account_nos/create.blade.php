
@php
use Modules\HR\Entities\Employee;
$business_id = request()->session()->get('user.business_id');
$employees = Employee::where('business_id', $business_id)
    ->get(['employee_id', DB::raw('CONCAT(first_name, " ", last_name) as name')])
    ->pluck('name', 'employee_id');


@endphp


<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\FleetAccountNumberController@store'), 'method' =>
    'post', 'id' => 'account_nos_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.account_nos' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        
        <div class="form-group col-sm-12">
          {!! Form::label('invoice_name', __( 'shipping::lang.invoice_name' ) . ':*') !!}
          {!! Form::text('invoice_name', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.invoice_name')]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('account_number', __( 'shipping::lang.account_number' ) . ':*') !!}
          {!! Form::text('account_number', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.account_number')]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('dealer_name', __( 'shipping::lang.dealer_name' ) . ':*') !!}
          {!! Form::text('dealer_name', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.dealer_name')]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('dealer_account_number', __( 'shipping::lang.dealer_account_number' ) . ':*') !!}
          {!! Form::text('dealer_account_number', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.dealer_account_number')]); !!}
        </div> 
        <div class="form-group col-sm-12">
          {!! Form::label('bank_name', __( 'shipping::lang.bank_name' ) . ':*') !!}
          {!! Form::text('bank_name', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.bank_name')]); !!}
        </div> 
        <div class="form-group col-sm-12">
          {!! Form::label('branch', __( 'shipping::lang.branch' ) . ':*') !!}
          {!! Form::text('branch', null, ['class' => 'form-control', 'placeholder' => __( 'shipping::lang.branch')]); !!}
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
 
</script>