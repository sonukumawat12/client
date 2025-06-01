@can('subscribe')
    <li class="nav-item {{ $request->segment(1) == 'subscription' ? 'active' : '' }}">
        <a class="nav-link" href="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index')}}">
            <i class="fa fa-money"></i>
            <span>@lang('superadmin::lang.subscription')</span></a>
    </li>
   
@endcan