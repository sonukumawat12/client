<section class="content no-print">
    <div class="row">
        {!! Form::open(['url' => action('ContactController@save_settings'), 'method' => 'POST']) !!}
        <div class="form-group col-sm-4">
          
            {!! Form::label('user_id', __('contact.customer_advance_payments'), ['class' => 'mr-2']) !!}: &nbsp;
            {!! Form::select('customer_advance', $liability_accounts, !empty($data) ? $data->customer_advance : null, ['class' => 'form-control select2','placeholder' => __('lang_v1.all'),'required']) !!}
          
        </div>
        
        <div class="form-group col-sm-4 ">
         
            {!! Form::label('user_id', __('contact.supplier_advance_payments'), ['class' => 'mr-2']) !!}: &nbsp;
            {!! Form::select('supplier_advance', $asset_accounts, !empty($data) ? $data->supplier_advance : null, ['class' => 'form-control select2','placeholder' => __('lang_v1.all'),'required']) !!}
         
        </div>
        
        <div class="form-group col-sm-4 ">
         
            {!! Form::label('user_id', __('contact.customer_deposit_refund_liability_account'), ['class' => 'mr-2']) !!}: &nbsp;
            {!! Form::select('customer_deposit_refund_liability_account', $liability_accounts, !empty($data) ? $data->customer_deposit_refund_liability_account : null, ['class' => 'form-control select2','placeholder' => __('lang_v1.all'),'required']) !!}
         
        </div>
        
        <div class="form-group col-sm-4 ">
         
            {!! Form::label('user_id', __('contact.customer_deposit_refund_asset_account'), ['class' => 'mr-2']) !!}: &nbsp;
            {!! Form::select('customer_deposit_refund_asset_account', $asset_accounts, !empty($data) ? $data->customer_deposit_refund_asset_account : null, ['class' => 'form-control select2','placeholder' => __('lang_v1.all'),'required']) !!}
         
        </div>
        
        <br>
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        
        {!! Form::close() !!}
        
    </div>
   <div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%" id="contact_table">
            <thead>
                
                <tr>
                    <th>@lang('contact.customer_advance_payments')</th>
                    <th>@lang('contact.supplier_advance_payments')</th>
                    <th>@lang('contact.customer_deposit_refund_liability_account')</th>
                    <th>@lang('contact.customer_deposit_refund_asset_account')</th>
                    <th>@lang('lang_v1.created_by')</th>
                    <th>@lang('lang_v1.date')</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($data))
                <tr>
                    <td>{{ $data->cust }}</td>
                    <td>{{ $data->sup }}</td>
                    <td>{{ $data->_customer_deposit_refund_liability_account }}</td>
                    <td>{{ $data->_customer_deposit_refund_asset_account }}</td>
                    
                    <td>{{ $data->username }}</td>
                    <td>{{ @format_date($data->created_at) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</section>