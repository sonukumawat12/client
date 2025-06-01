
<li class="nav-item {{  in_array( $request->segment(1), ['essentials']) ? 'active active-sub' : '' }}">
    <a class="nav-link" href="{{action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'index'])}}">
        <i class="fas fa-check-circle"></i>
        <span>@lang('essentials::lang.essentials')</span></a>
</li>
