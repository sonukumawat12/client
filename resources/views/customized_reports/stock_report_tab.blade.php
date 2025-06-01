<div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
             
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('stock_details_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('stock_details_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class'
                    => 'form-control', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12 report_div"></div>
    </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-primary" onclick="printDocument()">@lang( 'messages.print' )</button>
     
    </div>
