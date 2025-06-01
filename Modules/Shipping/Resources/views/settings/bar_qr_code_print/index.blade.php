<!-- Main content -->
<section class="content">
  

  @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.bar_qr_code_print')])
 
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="bar_qr_code_table" style="width: 100%;">
      <thead>
        <tr>
          <th>@lang('shipping::lang.details_to_show')</th>
          <th>@lang('shipping::lang.in_bar_code')</th>
          <th>@lang('shipping::lang.in_qr_code')</th>
        </tr>
      </thead>
      
    </table>
  </div>
  @endcomponent
</section>
<!-- /.content -->