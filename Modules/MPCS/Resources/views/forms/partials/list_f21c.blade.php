@if (empty($is_ajax))
    <!-- Main content -->
    <section class="content">
@endif
   <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
         
             <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('f21c_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('f21c_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('form_21c_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'form_21c_date_range_list', 'readonly']); !!}
                </div>
            </div>

           
             
        
           
          
         
           
          
  
           
            @endcomponent
        </div>
    </div>
     <div id="21c_details_section">
     </div>
@if (empty($is_ajax))              
    </section>
@endif
    <script>
     function printDiv_list() {
		var w = window.open('', '_self');
		var html ='<html><body class="col-print-12">'  +document.getElementById("21c_details_section").innerHTML + '</body></html>'  ;
		$(w.document.body).html(html);
		w.print();
		w.close();
		window.location.href = "{{URL::to('/')}}/mpcs/F21";
	}
</script>