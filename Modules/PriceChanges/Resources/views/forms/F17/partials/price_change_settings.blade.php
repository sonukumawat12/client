<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'pricechanges::lang.price_change_settings')])
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('income_account_id', __('pricechanges::lang.price_gain_account') . ':') !!}
                {!! Form::select('income_account_id', $incomeAccounts, null, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'income_account_id', 'placeholder' => __('lang_v1.please_select')]); !!}
            </div>
        </div>
      
        <div class="col-md-3"> <!-- changed class from col-sm-3 to col-md-3 for uniform responsiveness-->
            <div class="form-group">
                {!! Form::label('expense_account_id', __('pricechanges::lang.price_loss_account') .  ':') !!}
                {!! Form::select('expense_account_id', $expenseAccounts, null, ['class' => 'form-control f17_filter select2', 'style' =>
                'width:100%', 'id' => 'expense_account_id', 'placeholder' => __('lang_v1.please_select')]); !!}
            </div>
        </div>
    </div>

    @endcomponent
    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    <div class="col-md-3 pull-right mb-12">
        <button type="submit" name="submit_type" id="f17_price_change" value="save" class="btn btn-primary pull-right"
            style="margin-left: 20px">@lang('pricechanges::lang.save')</button>
    </div>
    @endslot
    <!-- MPCS module f17 form should be full width -->
    <div class="">
       	
        <table class="table table-bordered table-striped" id="price_change_settings_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('pricechanges::lang.date_time')</th>
                    <th>@lang('pricechanges::lang.price_gain_account')</th>
                    <th>@lang('pricechanges::lang.price_loss_account')</th>
                    <th>@lang('pricechanges::lang.user')</th>
                    <th>@lang('pricechanges::lang.edit')</th>

                </tr>
            </thead>
        </table>
        
    </div>
    @endcomponent

    <div class="modal fade price_change_settings_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->