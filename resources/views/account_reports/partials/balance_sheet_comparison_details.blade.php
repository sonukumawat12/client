<link media="print" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<style type="text/css">
.amount-text {
    text-align: right;
}
.table{
        width: 100% !important;
    }
    .select{
        width: 100% !important;
    }
    .dt-buttons{
        margin-left: 25% !important;
    }
    .dt-buttons .btn-sm {
      background-color: #8F3A84 !important;
        color: #fff !important;
        height: 30px !important;
        padding-top: 2px !important;
        padding-bottom: 2px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        
        <table class="table table-border table-striped" id="balance_sheet_comparison_table">
            <thead>
                <tr>
                    <th>{{$location_name}}</th>
                    <th class="amount-text">{{@format_date($end_date)}}</th>
                    <th class="amount-text">{{@format_date($end_date_2)}}</th>
                    <th class="amount-text">{{@format_date($end_date_3)}}</th>
                </tr>
                
            </thead>
           
            <tbody>
                <tr>
                    <td colspan="4">
                        <h3 class="text-left" style="color: #3577A3; border-bottom: 2px solid #3577A3">
                            @lang('account.assets')</h3>
                    </td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                </tr>
                 <tr>
                    <td colspan="4">@lang('account.current_assets')</td>
                     <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                </tr>
                
                @if($account_access)
                @foreach ($assets_accounts as $account_detail)
                <tr>
                    <td width="25%">{{$account_detail->name}}</td>
                    <td width="25%"  class="amount-text">{{@num_format(($account_detail->balance))}}</></td>
                    <td width="25%"  class="amount-text">{{@num_format(($account_detail->balance_2))}}</></td>
                    <td width="25%"  class="amount-text">{{@num_format(($account_detail->balance_3))}}</></td>
                </tr>
                @endforeach
                @php
                $assets_accounts_main_balances_total = 0;
                @endphp
                @foreach ($assets_accounts_main_balances as $assets_accounts_main_balance_key =>
                $assets_accounts_main_balance)
                <tr>
                    <td>{{$assets_accounts_main_balance_key}}</td>
                    <td class="amount-text">{{@num_format(($assets_accounts_main_balance))}}</td>
                    <td class="amount-text">{{@num_format(($assets_accounts_main_balance))}}</td>
                    <td class="amount-text">{{@num_format(($assets_accounts_main_balance))}}</td>
                    @php
                    $assets_accounts_main_balances_total += $assets_accounts_main_balance;
                    @endphp
                </tr>
                @endforeach
                <tr>
                    <th>@lang('account.total_current_assets')</th>
                    <th class="amount-text">{{@num_format(($assets_accounts->sum('balance') +  $assets_accounts_main_balances_total))}} </th>
                    <th class="amount-text">{{@num_format(($assets_accounts->sum('balance_2') +  $assets_accounts_main_balances_total))}} </th>
                    <th class="amount-text">{{@num_format(($assets_accounts->sum('balance_3') +  $assets_accounts_main_balances_total))}} </th>
                </tr>
                @endif
                
                <tr>
                    <td colspan="4">@lang('account.property_plant_equipment')</td>
                     <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                </tr>
                
                @if($account_access)
                @foreach ($fixed_assets_accounts as $account_detail)
                <tr>
                    <td>{{$account_detail->name}}</td>
                    <td  class="amount-text">{{@num_format(($account_detail->balance))}}</td>
                    <td  class="amount-text">{{@num_format(($account_detail->balance_2))}}</td>
                    <td  class="amount-text">{{@num_format(($account_detail->balance_3))}}</td>
               </tr>
                @endforeach
                @php
                $fixed_assets_accounts_main_balances_total = 0;
                @endphp
                @foreach ($fixed_assets_accounts_main_balances as $fixed_assets_accounts_main_balance_key =>
                $fixed_assets_accounts_main_balance)
                <tr>
                    <td>{{$fixed_assets_accounts_main_balance_key}}</td>
                    <td  class="amount-text">{{@num_format(($fixed_assets_accounts_main_balance))}}</td>
                    <td  class="amount-text">{{@num_format(($fixed_assets_accounts_main_balance))}}</td>
                    <td  class="amount-text">{{@num_format(($fixed_assets_accounts_main_balance))}}</td>
                    @php
                    $fixed_assets_accounts_main_balances_total += $fixed_assets_accounts_main_balance;
                    @endphp
                </tr>
                @endforeach
                <tr>
                    <th>@lang('account.property_plant_equipment_net')</th>
                    <th class="amount-text">{{@num_format(($fixed_assets_accounts->sum('balance') + $fixed_assets_accounts_main_balances_total))}} </th>
                    <th class="amount-text">{{@num_format(($fixed_assets_accounts->sum('balance_2') + $fixed_assets_accounts_main_balances_total))}} </th>
                    <th class="amount-text">{{@num_format(($fixed_assets_accounts->sum('balance_3') + $fixed_assets_accounts_main_balances_total))}} </th>
                </tr>
                @endif
                
                <tr>
                    <td colspan="4">
                        <h3 class="text-left" style="color: #3577A3; border-bottom: 2px solid #3577A3">
                            @lang('account.liabilities')</h3>
                    </td>
                     <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                </tr>
                <tr>
                    <th colspan="4">@lang('account.current_liabilities')</th>
                     <th style="display: none"></th>
                    <th style="display: none"></th>
                    <th style="display: none"></th>
                </tr>
                
                @if($account_access)
                @foreach ($liabilities_accounts as $account_detail)
                <tr>
                   <td>{{$account_detail->name}}</td>
                   <td  class="amount-text">{{@num_format(($account_detail->balance))}}</td>
                   <td  class="amount-text">{{@num_format(($account_detail->balance_2))}}</td>
                   <td  class="amount-text">{{@num_format(($account_detail->balance_3))}}</td>
                </tr>
                @endforeach
                @php
                $liabilities_accounts_main_balances_total = 0;
                @endphp
                @foreach ($liabilities_accounts_main_balances as $liabilities_accounts_main_balance_key =>
                $liabilities_accounts_main_balance)
                <tr>
                    <td>{{$liabilities_accounts_main_balance_key}}</td>
                    <td  class="amount-text">{{@num_format(($liabilities_accounts_main_balance))}}</td>
                    <td  class="amount-text">{{@num_format(($liabilities_accounts_main_balance))}}</td>
                    <td  class="amount-text">{{@num_format(($liabilities_accounts_main_balance))}}</td>
                    @php
                    $liabilities_accounts_main_balances_total += $liabilities_accounts_main_balance;
                    @endphp
                </tr>
                @endforeach
                <tr>
                    <th>@lang('account.total_current_liabilities')</th>
                    <th class="amount-text">{{@num_format(($liabilities_accounts->sum('balance') + $liabilities_accounts_main_balances_total))}} </th>
                    <th class="amount-text">{{@num_format(($liabilities_accounts->sum('balance_2') + $liabilities_accounts_main_balances_total))}} </th>
                    <th class="amount-text">{{@num_format(($liabilities_accounts->sum('balance_3') + $liabilities_accounts_main_balances_total))}} </th>
                    
                </tr>
                @endif
                
                <tr>
                    <td colspan="4">@lang('account.long_term_liabilities')</td>
                     <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                </tr>
                
                @if($account_access)
                @foreach ($lt_liabilities_accounts as $account_detail)
                <tr>
                    <td>{{$account_detail->name}}</td>
                    <td  class="amount-text">{{@num_format(($account_detail->balance))}}</td>
                    <td  class="amount-text">{{@num_format(($account_detail->balance_2))}}</td>
                    <td  class="amount-text">{{@num_format(($account_detail->balance_3))}}</td>
                </tr>
                @endforeach
                @php
                $lt_liabilities_accounts_main_balances_total = 0;
                @endphp
                @foreach ($lt_liabilities_accounts_main_balances as $lt_liabilities_accounts_main_balance_key =>
                $lt_liabilities_accounts_main_balance)
                <tr>
                    <td>{{$lt_liabilities_accounts_main_balance_key}}</td>
                    <td class="amount-text">{{@num_format(($lt_liabilities_accounts_main_balance))}}</td>
                    <td class="amount-text">{{@num_format(($lt_liabilities_accounts_main_balance))}}</td>
                    <td class="amount-text">{{@num_format(($lt_liabilities_accounts_main_balance))}}</td>
                    @php
                    $lt_liabilities_accounts_main_balances_total += $lt_liabilities_accounts_main_balance;
                    @endphp
                </tr>
                @endforeach
                <tr>
                    <th>@lang('account.total_liabilities')</th>
                    <th class="amount-text">{{@num_format(($lt_liabilities_accounts->sum('balance') +  $lt_liabilities_accounts_main_balances_total + $liabilities_accounts->sum('balance') + $liabilities_accounts_main_balances_total))}} </th>
                    <th class="amount-text">{{@num_format(($lt_liabilities_accounts->sum('balance_2') +  $lt_liabilities_accounts_main_balances_total + $liabilities_accounts->sum('balance_2') + $liabilities_accounts_main_balances_total))}} </th>
                    <th class="amount-text">{{@num_format(($lt_liabilities_accounts->sum('balance_3') +  $lt_liabilities_accounts_main_balances_total + $liabilities_accounts->sum('balance_3') + $liabilities_accounts_main_balances_total))}} </th>
                </tr>
                @endif
                
                <tr>
                    <td colspan="4">
                        <h3 class="text-left" style="color: #3577A3; border-bottom: 2px solid #3577A3">
                            @lang('account.intangible_assets')</h3>
                    </td>
                     <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                </tr>
                
                @if($account_access)
                <tr>
                    <td>@lang('account.good_will')</td>
                    <td  class="amount-text">000</td>
                    <td  class="amount-text">000</td>
                    <td  class="amount-text">000</td>
                </tr>
                <tr>
                    <td>@lang('account.trade_name')</td>
                    <td class="amount-text">000</td>
                    <td class="amount-text">000</td>
                    <td class="amount-text">000</td>
                </tr>
                <tr>
                    <td>@lang('account.total_intangible_assets')</td>
                    <td class="amount-text">000</td>
                    <td class="amount-text">000</td>
                    <td class="amount-text">000</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                     <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                </tr>
                <tr>
                    <td><b>@lang('account.total_assets')</b></td>
                    <td class="amount-text"><b>{{@num_format(($assets_accounts->sum('balance') +  $assets_accounts_main_balances_total + $fixed_assets_accounts->sum('balance') + $fixed_assets_accounts_main_balances_total))}}</b></td>
                    <td class="amount-text"><b>{{@num_format(($assets_accounts->sum('balance_2') +  $assets_accounts_main_balances_total + $fixed_assets_accounts->sum('balance_2') + $fixed_assets_accounts_main_balances_total))}}</b></td>
                    <td class="amount-text"><b>{{@num_format(($assets_accounts->sum('balance_3') +  $assets_accounts_main_balances_total + $fixed_assets_accounts->sum('balance_3') + $fixed_assets_accounts_main_balances_total))}}</b></td>
                </tr>
                @endif
                
                <tr>
                    <td colspan="4">
                         <h3 class="text-left" style="color: #3577A3; border-bottom: 2px solid #3577A3">
                            @lang('account.stake_holder_equity')</h3>
                    </td>
                     <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                </tr>
                
                @if($account_access)
                <!--<tr>
                    <td>@lang('account.owner_contribution')</td>
                    <td  class="amount-text">{{@num_format($equity_accounts->sum('balance'))}}</td>
                </tr>-->
                @foreach ($equity_accounts_main_balances as $name=>$account_detail)
                <tr>
                    <td>{{$name}}</td>
                    <td class="amount-text">{{@num_format(($account_detail))}}</td>
                    <td class="amount-text">{{@num_format(($account_detail))}}</td>
                    <td class="amount-text">{{@num_format(($account_detail))}}</td>
                   
                </tr>
                @endforeach
                 @php
                     $total_equity = 0; 
                  @endphp
                @foreach ($equity_accounts as $equity)
                <tr>
                    <td>{{$equity->name}}</td>
                    <td class="amount-text">{{@num_format(($equity->balance))}}</td>
                    <td class="amount-text">{{@num_format(($equity->balance_2))}}</td>
                    <td class="amount-text">{{@num_format(($equity->balance_3))}}</td>
                     @php
                     $total_equity += $equity->balance;  
                     @endphp
                </tr>
                @endforeach
                <tr>
                    <td>@lang('account.total_stake_holder_equity')</td>
                    <td class="amount-text">{{@num_format($tl_equity_accounts_main_balances)}}</td>
                    <td class="amount-text">{{@num_format($tl_equity_accounts_main_balances)}}</td>
                    <td class="amount-text">{{@num_format($tl_equity_accounts_main_balances)}}</td>
                </tr>

                <tr>
                    <td>@lang('account.cummulative_profit')</td>
                    <td class="amount-text">{{@num_format($cummulative_profit)}}</td>
                    <td class="amount-text">{{@num_format($cummulative_profit_2)}}</td>
                    <td class="amount-text">{{@num_format($cummulative_profit_3)}}</td>
                </tr>
                <tr>
                    <td><b>@lang('account.total_liabilities_and_stake_holder_equity')</b></td>
                    <td class="amount-text"><b>{{@num_format(@$lt_liabilities_accounts->sum('balance') +  @$lt_liabilities_accounts_main_balances_total + @$liabilities_accounts->sum('balance') + 
                    @$cummulative_profit + @$total_equity +
                    @$liabilities_accounts_main_balances_total + @$tl_equity_accounts_main_balances)}}</b></td>
                    
                     <td class="amount-text"><b>{{@num_format(@$lt_liabilities_accounts->sum('balance_2') +  @$lt_liabilities_accounts_main_balances_total + @$liabilities_accounts->sum('balance_2') + 
                    @$cummulative_profit_2 + @$total_equity +
                    @$liabilities_accounts_main_balances_total + @$tl_equity_accounts_main_balances)}}</b></td>
                    
                     <td class="amount-text"><b>{{@num_format(@$lt_liabilities_accounts->sum('balance_3') +  @$lt_liabilities_accounts_main_balances_total + @$liabilities_accounts->sum('balance_3') + 
                    @$cummulative_profit_3 + @$total_equity +
                    @$liabilities_accounts_main_balances_total + @$tl_equity_accounts_main_balances)}}</b></td>
                </tr>
                @endif
                
            </tbody>
           

        </table>
       
        
    </div>

</div>


