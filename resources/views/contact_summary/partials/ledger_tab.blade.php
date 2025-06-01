
<div class="row">
    <div class="col-md-12">
    @component('components.widget', ['class' => 'box'])
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'ledger_date_range_new']); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('contact_type', __('lang_v1.contact_type') . ':') !!}
                    {!! Form::select('contact_type', $contact_types, null, ['placeholder' => __('lang_v1.all'), 'style' => 'width: 100%', 'class' => 'form-control select2', 'id' => 'contact_type']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('contact_id', __('lang_v1.contact') . ':') !!}
                    {!! Form::select('contact_id', [] ,null, ['placeholder' => __('lang_v1.all'), 'style' => 'width: 100%', 'class' => 'form-control select2', 'id' => 'contact_id']); !!}
                </div>
            </div>
        </div>
        @endcomponent
    </div>
    <div class="col-md-12">
        @component('components.widget', ['class' => 'box'])
        <div id="contact_ledger_div"></div>
        @endcomponent
    </div>
</div>
