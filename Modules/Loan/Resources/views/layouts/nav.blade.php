<section class="no-print">
    

<li class="nav-item {{ in_array(request()->segment(1), ['loan']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#loan-menu"
        aria-expanded="true" aria-controls="loan-menu">
        <i class="ti-settings"></i>
        <span>{{ __('loan::lang.loan') }}</span>
    </a>
    <div id="loan-menu" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">{{ __('loan::lang.loan') }}:</h6>
            
                    @if (auth()->user()->can('job_sheet.view_all'))
                        <a class="collapse-item" href="{{ url('contact_loan') }}">@lang('loan::lang.view_loans')</a>
                    @endif
                    
                    <a class="collapse-item" href="{{ url('contact_loan/create') }}">@lang('loan::lang.create_loan')</a>
                    
                    <a class="collapse-item" href="{{ url('contact_loan/repaymentbulk') }}">@lang('loan::lang.bulk_repayments')</a>
                    
                    @if (auth()->user()->can('edit_repair_settings'))
                        <a class="collapse-item" href="{{ url('contact_loan/import') }}">@lang('core.bulk') @lang('loan::lang.import_loan')</a>
                    @endif
                    
                    <a class="collapse-item" href="{{ url('contact_loan/calculator') }}">{{ trans_choice('loan::general.calculator', 1) }}</a>
'                   
                    <a class="collapse-item" href="{{ url('contact_loan/collateral_type') }}">@lang('core.settings')</a>

                    <a class="collapse-item" href="{{ url('report/contact_loan') }}">{{ trans_choice('core.report', 2) }}</a>

            
        </div>
    </div>
</li>
        

    
</section>
