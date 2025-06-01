<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body bg-gray disabled" style="margin-bottom: 0px !important">
                
                <table class="table table-condensed" style="margin-bottom: 0px !important">
                    <tbody>
                        @php
                            $col = in_array('types_of_service', $enabled_modules) ? 'col-sm-2' : 'col-sm-3';
                        @endphp
                        
                        <tr>
                            <td>
                                
                                <div
                                    class="@if (!empty($pos_settings['show_credit_sale_button'])) col-sm-2 @else col-md-3 @endif  col-xs-12 col-2px-padding">
                                    <button type="button"
                                        class="btn btn-success btn-block btn-flat btn-lg no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif pos-express-btn pos-express-finalize"
                                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                                        <div class="text-center"
                                            style="font-size: @if (!empty($pos_settings['show_credit_sale_button'])) 15px @else 18px @endif;">
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                            <b>@lang('lang_v1.save')</b>
                                        </div>
                                    </button>
                                </div>

                                <div class="div-overlay pos-processing"></div>
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

