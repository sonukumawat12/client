@inject('request', 'Illuminate\Http\Request')
<div class="col-md-12 no-print pos-header mb-12">
    <input type="hidden" id="pos_redirect_url" value="{{action('SellPosController@create')}}" />
    <div class="row">
        <div class="col-md-10 mt-5">
            <div class="btn-toolbar mb-15 mt-10 pull-right" role="toolbar" aria-label="Toolbar with button groups">
                <div class="btn-group mr-2" role="group" aria-label="First group">
                    @can('pos_page_return')
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#verify_password_modal">
                        @lang('lang_v1.return')
                    </button>
                    @endcan  
                </div>
                <div class="btn-group mr-2" role="group" aria-label="Second group">
                    @if(!empty($pos_settings['price_later']))
                    <button style="background: rgb(202, 132, 2); color: #fff;" type="button" class="btn btn-price-later btn-secondary">
                        @lang('lang_v1.price_later')
                    </button>
                    @endif
                    <button id="btnLock" title="@lang('lang_v1.lock_screen')" type="button" class="btn btn-success hidden-xs popover-default" data-placement="bottom">
                        <strong><i class="fa fa-lock fa-lg" aria-hidden="true"></i></strong>
                    </button>
                    <button
                        id="btnKeyboard"
                        type="button"
                        class="btn hidden-xs btn-secondary"
                        style="background: yellow;"
                        data-container="body"
                        data-toggle="popover"
                        data-placement="bottom"
                        data-trigger="click"
                        data-content="@include('sale_pos.partials.keyboard_shortcuts_details')"
                        data-html="true"
                    >
                        <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true"></i>
                    </button>
                    @if($request->segment(1) == 'purchase-pos')
                    <button
                        type="button"
                        id="view_suspended_sales"
                        title="{{ __('lang_v1.view_suspended_sales') }}"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        class="btn btn-default bg-yellow btn-modal "
                        data-container=".view_modal"
                        data-href="{{ action('PurchaseController@index')}}?suspended=1"
                    >
                        <strong><i class="fa fa-pause-circle-o fa-lg"></i></strong>
                    </button>
                    @else
                    <button
                        type="button"
                        id="view_suspended_sales"
                        title="{{ __('lang_v1.view_suspended_purchases') }}"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        class="btn btn-default bg-yellow btn-modal"
                        data-container=".view_modal"
                        data-href="{{ action('SellController@index')}}?suspended=1"
                    >
                        <strong><i class="fa fa-pause-circle-o fa-lg"></i></strong>
                    </button>
                    @endif
                    <button
                        type="button"
                        title="{{ __('lang_v1.hide_show_products') }}"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        class="btn btn-default btn-info hidden-xs"
                        id="hide_show_products"
                        style="background: pink;"
                    >
                        <strong><i class="fa fa-eye fa-lg"></i></strong>
                    </button>
                    <button type="button" title="{{ __('lang_v1.full_screen') }}" data-toggle="tooltip" data-placement="bottom" class="btn btn-primary hidden-xs" id="full_screen">
                        <strong><i class="fa fa-window-maximize fa-lg"></i></strong>
                    </button>
                    <button type="button" title="{{ __('lang_v1.hide_pos_popup') }}" data-toggle="tooltip" data-placement="bottom" class="btn btn-info  btn-default" id="toggle_popup">
                        <strong><i class="fa fa-window-close-o fa-lg"></i></strong>
                    </button>
                </div>
                <div class="btn-group" role="group" aria-label="Third group">
                    <button
                        title="@lang('lang_v1.calculator')"
                        id="btnCalculator"
                        type="button"
                        class="btn btn-success popover-default"
                        data-toggle="popover"
                        data-trigger="click"
                        data-content='@include("layouts.partials.calculator")'
                        data-html="true"
                        data-placement="bottom"
                    >
                        <strong><i class="fa fa-calculator fa-lg" aria-hidden="true"></i></strong>
                    </button>
                    <button
                        type="button"
                        id="register_details"
                        title="{{ __('cash_register.register_details') }}"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        class="btn btn-success btn-modal"
                        data-container=".register_details_modal"
                        data-href="{{ action('CashRegisterController@getRegisterDetails')}}"
                    >
                        <strong><i class="fa fa-briefcase fa-lg" aria-hidden="true"></i></strong>
                    </button>
                    <button
                        type="button"
                        id="close_register"
                        title="{{ __('cash_register.close_register') }}"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        class="btn btn-danger btn-modal"
                        data-container=".close_register_modal"
                        data-href="{{ action('CashRegisterController@getCloseRegister')}}"
                    >
                        <strong><i class="fa fa-window-close fa-lg"></i></strong>
                    </button>
                    <a href="{{ action('SellPosController@index')}}" title="{{ __('lang_v1.go_back') }}" data-toggle="tooltip" data-placement="bottom" class="btn btn-info">
                        <strong><i class="fa fa-backward fa-lg"></i></strong>
                    </a>
                </div>
            </div>
            {{-- @if(Module::has('Repair')) @include('repair::layouts.partials.pos_header') @endif --}}
        </div>
        <div class="col-md-2">
            <div class="m-6 pull-right mt-15 hidden-xs">
                <div class="input-group">
                    <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Current Date" aria-label="Input group example" aria-describedby="btnGroupAddon" readonly value="{{ @format_date('now') }}">
                </div>
            
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="verify_password_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('lang_v1.enter_password')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="password" id="verify_password" name="verify_password" placeholder="@lang('lang_v1.enter_password')" style="margin-auto;" class="form-control" />
            </div>
            <div class="modal-footer">
                <button type="button" id="verify_password_btn" class="btn btn-primary">@lang('lang_v1.verify')</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
