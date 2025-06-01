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
                                <div class="col-sm-9"></div>
                                
                                <div class="col-sm-3 col-xs-12 d-inline-table">
                                    <b>@lang('sale.total_payable'):</b>
                                    <br />
                                    <input type="hidden" name="final_total" id="final_total" value=0>
                                    <span id="total_payable" class="text-success lead text-bold">0</span>
                                    
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                
                                <div class="col-sm-2 col-xs-12 col-2px-padding" style="width: 143px;
">
                                    
                                </div>


                                <div class="@if (!empty($pos_settings['show_credit_sale_button'])) col-sm-3 @else col-md-4 @endif col-xs-12 col-2px-padding multipay_btn_div"
                                    style="width: 139px;">
                                    
                                </div>

                                <div
                                    class="@if (!empty($pos_settings['show_credit_sale_button'])) col-sm-2 @else col-md-3 @endif  col-xs-12 col-2px-padding">
                                    <button type="button"
                                        id="pos-finalize"
                                        class="btn btn-success btn-block btn-flat btn-lg no-print  pos-express-btn"
                                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                                        <div class="text-center"
                                            style="font-size: @if (!empty($pos_settings['show_credit_sale_button'])) 15px @else 18px @endif;">
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                            <b>@lang('tpos.finalize')</b>
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