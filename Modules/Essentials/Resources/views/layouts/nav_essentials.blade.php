@php
                    
    $work_shift = 0;
    $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp


<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="settlement_tabs" id="bs-example-navbar-collapse-1">
                <ul class="nav nav-tabs">
                    
                    @if(!empty($pacakge_details['essentials_todo']))
                        <li @if(request()->segment(2) == 'todo') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'index'])}}">@lang('essentials::lang.todo')</a></li>
                    @endif
                    
                    @if(!empty($pacakge_details['essentials_document']))
                        <li @if(request()->segment(2) == 'document' && request()->get('type') != 'memos') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\DocumentController::class, 'index'])}}">@lang('essentials::lang.document')</a></li>
                    @endif
                    
                    
                    @if(!empty($pacakge_details['essentials_memos']))
                        <li @if(request()->segment(2) == 'document' && request()->get('type') == 'memos') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\DocumentController::class, 'index']) .'?type=memos'}}">@lang('essentials::lang.memos')</a></li>
                    @endif
                    
                    @if(!empty($pacakge_details['essentials_reminders']))
                        <li @if(request()->segment(2) == 'reminder') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\ReminderController::class, 'index'])}}">@lang('essentials::lang.reminders')</a></li>
                    @endif
                    
                    
                    @if(!empty($pacakge_details['essentials_messages']))
                        @if (auth()->user()->can('essentials.view_message') || auth()->user()->can('essentials.create_message'))
                            <li @if(request()->segment(2) == 'messages') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'index'])}}">@lang('essentials::lang.messages')</a></li>
                        @endif
                    @endif
                    
                    <li @if(request()->segment(2) == 'knowledge-base') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\KnowledgeBaseController::class, 'index'])}}">@lang('essentials::lang.knowledge_base')</a></li>
                    
                    @if(!empty($pacakge_details['essentials_settings']))
                        @if (auth()->user()->can('edit_essentials_settings'))
                            <li @if(request()->segment(2) == 'hrm' && request()->segment(2) == 'settings') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsSettingsController::class, 'editEssential'])}}">@lang('business.settings')</a></li>
                        @endif
                    @endif
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>