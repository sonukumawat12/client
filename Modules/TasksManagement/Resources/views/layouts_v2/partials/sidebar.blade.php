<li class="nav-item {{ in_array($request->segment(1), ['tasks-management']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#task-menu"
        aria-expanded="true" aria-controls="task-menu">
        <i class="fa fa-sticky-note"></i>
        <span>@lang('tasksmanagement::lang.tasks_management')</span>
    </a>
    <div id="task-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Tasks Management:</h6>
            @if($notes_page)
                <a class="collapse-item {{ $request->segment(1) == 'tasks-management' && $request->segment(2) == 'notes' ? 'active' : '' }}" href="{{action('\Modules\TasksManagement\Http\Controllers\NoteController@index')}}">@lang('tasksmanagement::lang.notes')</a>
            @endif
            
            @if($tasks_page)
            @can('tasks_management.tasks')
                <a class="collapse-item {{ $request->segment(1) == 'tasks-management' && $request->segment(2) == 'tasks' ? 'active' : '' }}" href="{{action('\Modules\TasksManagement\Http\Controllers\TaskController@index')}}">@lang('tasksmanagement::lang.list_tasks')</a>
            @endcan
            @endif
            
            @if($reminder_page)
            @can('tasks_management.reminder')
                <a class="collapse-item {{ $request->segment(1) == 'tasks-management' && $request->segment(2) == 'reminders' ? 'active' : '' }}" href="{{action('\Modules\TasksManagement\Http\Controllers\ReminderController@index')}}">@lang('tasksmanagement::lang.reminders')</a>
            @endcan
            @endif
            <a class="collapse-item {{ $request->segment(1) == 'tasks-management' && $request->segment(2) == 'settings' ? 'active' : '' }}" href="{{action('\Modules\TasksManagement\Http\Controllers\SettingsController@index')}}">@lang('tasksmanagement::lang.settings')</a>
        </div>
    </div>
</li>