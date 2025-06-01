<style>
.toggle-action-btn {
    padding: 5px 10px;
    background-color: #ccc;
    border: none;
    cursor: pointer;
    color: #fff;
    border-radius: 4px;
}
.toggle-action-btn[data-enabled="Enabled"] {
    background-color: green;
}
.toggle-action-btn[data-enabled="Disable"] {
    background-color: red;
}
</style>
<!-- Main content -->
<section class="content">
   
    <div class="row">
         {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\F22FormController@store_stock_taking'), 'method' =>
    'post', 'id' => 'store_stock_taking' ])
    !!}
        <div class="col-md-12">
           

            <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('stock_loss_account', __('mpcs::lang.stock_loss_account') . ':') !!}
                    {!! Form::select('stock_loss_account', $accounts, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
              <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('stock_gain_account', __('mpcs::lang.stock_gain_account') . ':') !!}
                    {!! Form::select('stock_gain_account', $accountType_gain, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>

            <div class="col-md-3" id="location_filter">
                <div class="form-group"  style="margin-top: 20px">
                  <button type="submit" name="submit_type" id="save_button" value="save"
                        class="btn btn-primary">@lang('mpcs::lang.save')</button>
                  </div> 
            </div>
            
        </div>
          {!! Form::close() !!}
    </div>
<div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
  
            <div class="col-md-12">
                <div class="row" style="margin-top: 20px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="form_f22_list_table_stock_taking" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang('mpcs::lang.action')</th>
                                    <th>@lang('mpcs::lang.date_and_time')</th>
                                    <th>@lang('mpcs::lang.stock_loss_account')</th>
                                     
                                    <th>@lang('mpcs::lang.stock_gain_account')</th>
                                    <th>@lang('mpcs::lang.status')</th>
                                     <th>@lang('mpcs::lang.user_added')</th>
                                </tr>
                            </thead>
                            
                        </table>
                    </div>
                </div>
              
            </div>

            @endcomponent
        </div>
    </div>
   
  
</section>
<!-- /.content -->