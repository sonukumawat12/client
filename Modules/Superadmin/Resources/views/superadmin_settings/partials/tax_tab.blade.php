<div
    class="pos-tab-content">
    <div class="row">
         
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('tax_label_1', __('business.tax_1_name') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('tax_label_1', !empty($settings['tax_label_1']) ? $settings['tax_label_1'] : null , ['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('tax_number_1', __('business.tax_1_no') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('tax_number_1', !empty($settings['tax_number_1']) ? $settings['tax_number_1'] : null, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('tax_label_2', __('business.tax_2_name') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('tax_label_2', !empty($settings['tax_label_2']) ? $settings['tax_label_2'] : null, ['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
                </div>
            </div>
        </div>
       
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('tax_number_2', __('business.tax_2_no') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::text('tax_number_2', !empty($settings['tax_number_2']) ? $settings['tax_number_2'] : null, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('enable_inline_tax', 1, !empty($settings['enable_inline_tax']) ? 1 : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_inline_tax' ) }}
                  </label>
                </div>
            </div>
        </div>
    </div>
</div>