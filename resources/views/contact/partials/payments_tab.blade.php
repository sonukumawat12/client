
<div class="row">
    <div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
        <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('payment_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('payment_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'payment_date_range_new']); !!}
            </div>
        </div>
       
        </div>
        
        @endcomponent
    </div>
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box'])
        <div id="contact_payment_div"></div>
        @endcomponent
    </div>
</div>
