<li class="nav-item">
    <a class="nav-link collapsed {{ in_array($request->segment(1), ['crm']) && $request->segment(2) != 'issue-customer-bill'? 'active active-sub' : '' }}" href="#" data-toggle="collapse" data-target="#crm-menu-module"
        aria-expanded="true" aria-controls="crm-menu-module">
        <i class="fa fa-tint fa-lg"></i>
        <span>@lang('superadmin::lang.crm_module')</span>
    </a>
    <div id="crm-menu-module" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('superadmin::lang.crm_module') popua:</h6>
            @if(auth()->user()->can('crm.access_all_leads') || auth()->user()->can('crm.access_own_leads'))
                <a class="collapse-item @if(request()->segment(2) == 'leads') active @endif" href="{{action([\Modules\Crm\Http\Controllers\LeadController::class, 'index']). '?lead_view=list_view'}}">@lang('crm::lang.leads')</a>
            @endif
            <a class="collapse-item @if(request()->segment(2) == 'crm-dashboard') active @endif" href="{{ action([\Modules\Crm\Http\Controllers\CrmDashboardController::class, 'index']) }}">
      @lang('crm::lang.crm_dashboard')
       </a>
 
            @if(auth()->user()->can('crm.access_all_schedule') || auth()->user()->can('crm.access_own_schedule'))
                <a class="collapse-item @if(request()->segment(2) == 'follow-ups') active @endif" href="{{action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'index'])}}">@lang('crm::lang.follow_ups')</a>
            @endif
            
            @if(auth()->user()->can('crm.access_all_campaigns') || auth()->user()->can('crm.access_own_campaigns'))
                <a class="collapse-item @if(request()->segment(2) == 'campaigns') active @endif" href="{{action([\Modules\Crm\Http\Controllers\CampaignController::class, 'index'])}}">@lang('crm::lang.campaigns')</a>
            @endif 
            
            @can('crm.access_contact_login')
                <a class="collapse-item" href="{{action([\Modules\Crm\Http\Controllers\ContactLoginController::class, 'allContactsLoginList'])}}"> @lang('crm::lang.contacts_login')</a>
                <a class="collapse-item" href="{{action([\Modules\Crm\Http\Controllers\ContactLoginController::class, 'commissions'])}}">@lang('crm::lang.commissions')</a>
            @endcan
            
            @if((auth()->user()->can('crm.view_all_call_log') || auth()->user()->can('crm.view_own_call_log')) && config('constants.enable_crm_call_log'))
                <a class="collapse-item @if(request()->segment(2) == 'call-log') active @endif" href="{{action([\Modules\Crm\Http\Controllers\CallLogController::class, 'index'])}}">@lang('crm::lang.call_log')</a>
            @endif

            @can('crm.view_reports')
                <a class="collapse-item  @if(request()->segment(2) == 'reports') active @endif" href="{{action([\Modules\Crm\Http\Controllers\ReportController::class, 'index'])}}">@lang('report.reports')</a>
            @endcan
            
            <a class="collapse-item @if(request()->segment(2) == 'proposal-template') active @endif" href="{{action([\Modules\Crm\Http\Controllers\ProposalTemplateController::class, 'index'])}}">
                    @lang('crm::lang.proposal_template')
                </a>
            
            <a class="collapse-item @if(request()->segment(2) == 'proposals') active @endif" href="{{action([\Modules\Crm\Http\Controllers\ProposalController::class, 'index'])}}">
                    @lang('crm::lang.proposals')
                </a>
            
            @if(auth()->user()->can('crm.access_b2b_marketplace') && config('constants.enable_b2b_marketplace'))
            <a class="collapse-item @if(request()->segment(2) == 'b2b-marketplace') active @endif" href="{{action([\Modules\Crm\Http\Controllers\CrmMarketplaceController::class, 'index'])}}">
                    @lang('crm::lang.b2b_marketplace')
                </a>
            @endif

            @can('crm.access_sources')
                <a class="collapse-item @if(request()->get('type') == 'source') active @endif" href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=source'}}">@lang('crm::lang.sources')</a>
            @endcan

            @can('crm.access_life_stage')
                <a class="collapse-item @if(request()->get('type') == 'life_stage') active @endif" href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=life_stage'}}">@lang('crm::lang.life_stage')</a>

                <a class="collapse-item @if(request()->get('type') == 'followup_category') active @endif" href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=followup_category'}}">@lang('crm::lang.followup_category')</a>
            @endcan
            
            <a class="collapse-item @if(request()->segment(2) == 'settings') active @endif" href="{{action([\Modules\Crm\Http\Controllers\CrmSettingsController::class, 'index'])}}">
                @lang('business.settings')
            </a>
        
        </div>
    </div>
</li>
