<div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
             
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pumper_details_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('pumper_details_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class'
                    => 'form-control', 'readonly']); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    
    <div class="row">
        
        <div class="col-sm-12 pumper_report_div"></div>
    </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-primary" onclick="printPumperDocument()">@lang( 'messages.print' )</button>
     
    </div>