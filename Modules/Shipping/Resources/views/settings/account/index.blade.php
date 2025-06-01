<!-- Main content -->
<section class="content">
  
  @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.accounts')])
  {!! Form::open([
            'url' => action('\Modules\Shipping\Http\Controllers\ShippingAccountController@store'),
            'method' => 'post',
            'id' => 'types_add_form',
        ]) !!}
        
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense', __( 'shipping::lang.expense' )) !!}
                    {!! Form::select('expense', $expense_accounts,  null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'shipping::lang.please_select' ), 'id' => 'expense']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('income', __( 'shipping::lang.income' )) !!}
                    {!! Form::select('income', $income_accounts, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'shipping::lang.please_select' ), 'id' => 'income']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('shipping_mode', __( 'shipping::lang.shipping_mode' )) !!}
                    {!! Form::select('shipping_mode', $shipping_modes,  null, ['class' => 'form-control select2',
                    
                    'placeholder' => __(
                    'lang_v1.all' ), 'id' => 'shipping_mode']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('shipping_partner', __( 'shipping::lang.shipping_partner' )) !!}
                    {!! Form::select('shipping_partner', $shipping_partners,  null, ['class' => 'form-control select2',
                    
                    'placeholder' => __(
                    'lang_v1.all' ), 'id' => 'shipping_partner']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <br>
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                </div>
            </div>
        </div>
        
    {!! Form::close() !!}
        
    <div class="table-responsive">
    <table class="table table-bordered table-striped" id="accounts_table" style="width: 100%;">
      <thead>
        <tr>
          <th class="notexport">@lang('messages.action')</th>
          <th>@lang('shipping::lang.added_date')</th>
          <th>@lang('shipping::lang.income_account')</th>
          <th>@lang('shipping::lang.expense_account')</th>
          <th>@lang('shipping::lang.shipping_partner')</th>
          <th>@lang('shipping::lang.shipping_mode')</th>
          <th>@lang('shipping::lang.created_by')</th>
        </tr>
      </thead>
    </table>
  </div>
          
  @endcomponent
</section>
<!-- /.content -->