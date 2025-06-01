<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-3 text-red">
            <b>@lang('mpcs::lang.date_and_time'): <span class="9c_from_date">{{$date}}</span> </b>
        </div>
        <div class="col-md-3 text-red">
            <b>@lang('mpcs::lang.ref_previous_form_number'): <span class="9c_from_date">{{$form_number}}</span> </b>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <h5 style="font-weight: bold;">@lang('mpcs::lang.user_added'): {{$name}} <br>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="box-tools pull-right" style="margin: 14px 20px 14px 0;">
            <button type="button" class="btn btn-primary btn-modals" data-href="{{action('\Modules\MPCS\Http\Controllers\Form9CSettingsController@create')}}" data-container=".form_9_c_settings_modal">
                <i class="fa fa-plus"></i> @lang( 'mpcs::lang.add_form_9_c_settings' )</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="col-md-12">
                <div class="box-body" style="margin-top: 20px;">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div id="msg"></div>
                            <table id="form_9a_settings_table" class="table table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('mpcs::lang.action')</th>
                                        <th>@lang('mpcs::lang.form_starting_number')</th>
                                        <th>@lang('mpcs::lang.total_sale_up_to_previous_day')</th>
                                        <th>@lang('mpcs::lang.previous_day_cash_sale')</th>
                                        <th>@lang('mpcs::lang.previous_day_card_sale')</th>
                                        <th>@lang('mpcs::lang.previous_day_credit_sale')</th>
                                        <th>@lang('mpcs::lang.previous_day_cash')</th>
                                        <th>@lang('mpcs::lang.previous_day_cheques_cards')</th>
                                        <th>@lang('mpcs::lang.previous_day_total')</th>
                                        <th>@lang('mpcs::lang.previous_day_balance_in_hand')</th>
                                        <th>@lang('mpcs::lang.previous_day_grand_total')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->