<style>
    button.btn.btn-primary.user {
        margin-top: 23px !important;
    }
</style>
<!-- Main content -->
<section class="content" id="dsr_opening_meter">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
          'dsr::lang.accumulative_sale_and_purchase')])
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::open(['url' => action('\Modules\Dsr\Http\Controllers\DsrSettingsController@addAccumulativeSalePurchase'), 'method' => 'post', 'id' => 'accumulative_purchase' ]) !!}
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('date_time', __('dsr::lang.date_time')) !!}
                                {!! Form::date('date_time', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.date_time'), 'id' => 'date_time','required']) !!}
                                <div class="text-danger" id="date_time-error"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('product_id', __('dsr::lang.product') . ':') !!}
                                {!! Form::select('product_id', $fuel_products, null, ['class' => 'form-control
                                select2',
                                'placeholder' => __('dsr::lang.all'), 'id' => 'product_id', 'style' => 'width:100%']); !!}
                            </div>
                            <div class="text-danger" id="product_id-error"></div>

                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('accumulative_sale', __('dsr::lang.accumulative_sale')) !!}
                                {!! Form::text('accumulative_sale', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.accumulative_sale'), 'id' => 'accumulative_sale']) !!}
                             <div class="text-danger" id="accumulative_sale-error"></div>
                        </div>
                        </div>
                        <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('accumulative_purchase', __('dsr::lang.accumulative_purchase')) !!}
                                    {!! Form::text('accumulative_purchase', null, ['class' => 'form-control', 'placeholder' => __('dsr::lang.accumulative_purchase'), 'id' => 'accumulative_sale']) !!}
                                    <div class="text-danger" id="accumulative_purchase-error"></div>
                                </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-bottom justify-content-start">
                                <button id="save_purchase_sale" class="btn btn-primary user">save</button>
                        </div>
                        {!! Form::close() !!}
                        
                    </div>
                    
                        <table class="table table-striped table-bordered" id="dsr_openingmeter" style="width: 100%;">
                            <thead>
                            <tr>
                                <th>@lang('lang_v1.action')</th>
                                <th>@lang('dsr::lang.date')</th>
                                <th>@lang('dsr::lang.product')</th>
                                <th>@lang('dsr::lang.accumulative_sale')</th>
                                <th>@lang('dsr::lang.accumulative_purchase')</th>
                                <th>@lang('dsr::lang.user')</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            @endcomponent
        </div>
    <div class="modal fade dsr_opening_meter_modal" size="lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

@push('javascript')
    <script type="text/javascript">
        $(() =>{
            $('.select2').select2();
        })
        $('#save_purchase_sale').click(function(e) {
            e.preventDefault();
            //add validation all field should not be empty or null
            $('#accumulative_purchase').submit();
        });
        function fieldValidation() {
            var product_id = $('#product_id').val();
            var accumulative_sale = $('#accumulative_sale').val();
            var accumulative_purchase = $('#accumulative_purchase').val();
            if (product_id == null || product_id == '') {
                $('#product_id-error').text('Product field is required');
                return false;
            }
            if (accumulative_sale == null || accumulative_sale == '') {
                $('#accumulative_sale-error').text('Accumulative sale field is required');
                return false;
            }
            if (accumulative_purchase == null || accumulative_purchase == '') {
                $('#accumulative_purchase-error').text('Accumulative purchase field is required');
            }
            $('#accumulative_sale-error').text('');
            $('#accumulative_purchase-error').text('');
            $('#product_id-error').text('');
            return true;
        }
    </script>
@endpush
