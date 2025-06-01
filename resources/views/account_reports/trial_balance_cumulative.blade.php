@extends('layouts.app')
@section('title', __( 'account.trial_balance_cumulative' ))

@section('content')


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang( 'account.trial_balance_cumulative')</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Account Reports</a></li>
                    <li><span>@lang( 'account.trial_balance_cumulative')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="row no-print">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('account_type',  __('Account Type') . ':') !!}
                        {!! Form::select('account_type', $account_types_opts, null, ['id'=>'account_type','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('account_sub_type',  __('Account Sub Type') . ':') !!}
                        {!! Form::select('account_sub_type', $sub_acn_arr, null, ['id'=>'account_sub_type','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('account_group',  __('Account Group') . ':') !!}
                        {!! Form::select('account_group', $account_groups, null, ['id'=>'account_group','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('account_name',  __('Account Name') . ':') !!}
                        {!! Form::select('account_name', $accounts, null, ['id'=>'account_name','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
            </div>
            @endcomponent
        </div>

        <div class="col-sm-12">
            <div class="col-sm-3 col-xs-6 pull-left">
                <label for="business_location">@lang('account.business_locations'):</label>
                {!! Form::select('business_location', $business_locations, null, ['class' => 'form-control select2',
                'placeholder' =>__('lang_v1.all'), 'style' => 'width: 100%', 'id' => 'business_location']) !!}
            </div>
            <div class="col-sm-6 col-xs-6 text-center">
                <h3>{{request()->session()->get('business.name')}}</h3>
                <div class="clearfix"></div>
                <h5 style="margin:0px;" id="date_show"></h5>
            </div>
            <div class="col-sm-3 col-xs-6 pull-right">
                <label for="end_date">@lang('messages.filter_by_date'):</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="date" id="end_date" value="{{date('Y-m-d')}}" class="form-control" >
                </div>
            </div>
        </div>

    </div>
    <br>
    <div class="box box-solid">
        <div class="box-header print_section">
            <h3 class="box-title">{{session()->get('business.name')}} - @lang( 'account.trial_balance_cumulative') - <span
                    id="hidden_date">{{@format_date('now')}}</span></h3>
        </div>
        <div class="box-body">
            <table class="table table-striped table-bordered" id="trial_balance_table" style="width: 100%;">
                <thead>
                    <tr class="bg-gray">
                        <th></th>
                        <th>@lang('account.account_number')</th>
                        <th>@lang('account.account_type')</th>
                        <th>@lang('account.sub_account_type')</th>
                        <th>@lang('account.account_group')</th>
                        <th>@lang('account.account_name')</th>
                        <th>@lang('account.debit')</th>
                        <th>@lang('account.credit')</th>
                    </tr>
                </thead>
                @if($account_access)
                <tbody>
                 
                </tbody>
                @endif
                <tbody id="account_balances_details">
                    @if(!$account_access)
                    <tr class="text-center"
                        style="color: {{App\System::getProperty('not_enalbed_module_user_color')}}; font-size: {{App\System::getProperty('not_enalbed_module_user_font_size')}}px;">
                        <td colspan="8"> {{App\System::getProperty('not_enalbed_module_user_message')}}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-gray">
                        <th colspan="6" class="text-right">@lang('sale.total')</th>
                        <td>
                            <span class="remote-data display_currency " data-currency_symbol="true" id="total_debit">
                                @if($account_access)
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="remote-data display_currency" data-currency_symbol="true" id="total_credit">
                                @if($account_access)
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                                @endif
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
    $(document).ready( function(){
        // filter Data
        var filter = JSON.parse(`<?php echo json_encode($filterdata) ?>`);
        console.log(filter);
        $('#end_date, #business_location').change( function() {
            trial_balance_table.ajax.reload();
            $('#date_show').text($('#end_date').val());
            $('#hidden_date').text($(this).val());
        });
        $('#account_type').change(function(){
            let change_val ='subType_'+ $('#account_type').val();
            $('#account_sub_type').select2('destroy').empty().select2(filter[change_val]).change();
            loadNamesOtion();
            trial_balance_table.ajax.reload();
        });
        $('#account_sub_type').change(function(){
            let change_val ='groupType_'+ $('#account_sub_type').val();
           
            if(change_val == 'groupType_All'){
                change_val = 'groupType_';
            }
            if(change_val=="groupType_"){
                data = [{'id':'','text':'All'}];
                $('#account_sub_type option').each(function(){
                    if($(this).attr('value') != ''){
                        let newChangeVal ='groupType_'+ $(this).val();
                        if(filter[newChangeVal] && filter[newChangeVal]['data']){

                            for(var i in filter[newChangeVal]['data']) {
                                data.push(filter[newChangeVal]['data'][i]);
                            }

                        }
                    }
                    $('#account_group').select2('destroy').empty().select2({'data':data}).change();

                })

            }else{
               $('#account_group').select2('destroy').empty().select2(filter[change_val]).change();
            }
            loadNamesOtion();
            trial_balance_table.ajax.reload();
        });
        $('#account_group').change(function(){
            loadNamesOtion();
            trial_balance_table.ajax.reload();
        });
        $('#account_name').change(function(){
            trial_balance_table.ajax.reload();
        });
        $('#date_show').text($('#end_date').val());


        function loadNamesOtion(){
            postData = {"account_type_s" : $('#account_type').val(),
                "account_sub_type": $('#account_sub_type').val(),
                 "account_group" : $('#account_group').val(),
                 "_token": "{{ csrf_token() }}"
            };
            $.ajax({
                method: "post",
                url: '/accounting-module/check_account_names',
                dataType: "json",
                data: postData,
                success:function(result){
                    if(result.data){
                        $('#account_name').select2('destroy').empty().select2(result).change();
                    }
                }
            });
        }
    });

    @if($account_access)
    $(document).ready( function(){
        // trial_balance_table
        trial_balance_table = $('#trial_balance_table').DataTable({
            'processing': true,
            'serverSide': false,
            "autoWidth": false ,
            'ajax':{
                url: "{{action('AccountReportsController@trialBalanceCumulative')}}",
                data : function (d) {
                    d.end_date = new Date($('input#end_date').val()).toISOString().slice(0,10);
                    d.location_id = $('select#business_location').val();
                    d.account_type_s = $('#account_type').val();
                    d.account_sub_type = $('#account_sub_type').val();
                    d.account_group = $('#account_group').val();
                    d.account_name = $('#account_name').val();
                }
            },
            @include('layouts.partials.datatable_export_button')
            columnDefs: [
                { "width": "6%", "targets": 1 },
                { "width": "10%", "targets": 2 },

                { "width": "10%", "targets": 3 },

                { "width": "25%", "targets": 4 },

                { "width": "25%", "targets": 5 },

                { "width": "12%", "targets": 6 },

                { "width": "12%", "targets": 7 },

                {

                    "targets": 0,

                    "visible": true,

                },

                { "width": "auto", "targets": [6, 7] } 
            ],
            columns: [
                {data: 'hide', name: 'hide'},
                {data: 'account_number', name: 'accounts.account_number'},
                {data: 'parent_account_type_name', name: 'pat.name'},
                {data: 'account_type_name', name: 'ats.name'},
                {data: 'account_group', name: 'account_group'},
                {data: 'name', name: 'accounts.name'},
                {data: 'debit', name: 'debit'},
                {data: 'credit', name: 'credit'}
            ],
            "fnDrawCallback": function (oSettings) {
                $('#total_debit').text(sum_table_col($('#trial_balance_table'), 'debit'));
                $('#total_credit').text(sum_table_col($('#trial_balance_table'), 'credit'));

                __currency_convert_recursively($('#trial_balance_table'));
            }
        });
        trial_balance_table.columns(0).visible(false);
    });

    @endif

    function writeDebit(account_list){
        for (var key in account_list) {
            if(parseInt(account_list[key]['debit_balance']) !== 0){
                var accnt_bal = __currency_trans_from_en(account_list[key]['debit_balance']);
                var accnt_bal_with_sym = __currency_trans_from_en(account_list[key]['debit_balance'], true);
                var account_tr = '<tr><td class="pl-20-td">' + account_list[key]['name'] + ' <b>' + account_list[key]['account_number']  + '</b></td><td><input type="hidden" class="debit" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td><td>&nbsp;</td></tr>';
                $('table#trial_balance_table tbody#account_balances_details').append(account_tr);
            }
        }
    }
    function writeCredit(account_list){
        for (var key in account_list) {
            if(parseInt(account_list[key]['credit_balance']) !== 0){
                var accnt_bal = __currency_trans_from_en(account_list[key]['credit_balance']);
                var accnt_bal_with_sym = __currency_trans_from_en(account_list[key]['credit_balance'], true);
                var account_tr = '<tr><td class="pl-20-td">' + account_list[key]['name'] + ' <b>' + account_list[key]['account_number']  + '</b></td><td>&nbsp;</td><td><input type="hidden" class="credit" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td></tr>';
                $('table#trial_balance_table tbody#account_balances_details').append(account_tr);
                
            }
        }
    }
</script>

@endsection