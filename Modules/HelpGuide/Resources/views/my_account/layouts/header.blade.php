<header id="app-header">
    <div class="px-3 py-2 mb-3 d-none d-md-block">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                {{-- <router-link to="/"
                    class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-decoration-none">
                    <img src="{{ asset(setting('app_logo')) }}" height="40" />
                </router-link> --}}

                <ul class="nav col-12 col-md-auto my-2 justify-content-center my-md-0 text-small">
                    @can('superadmin')
                    <li class="mx-2">
                        <a href="{{ route('dashboard') }}" class="nav-link text-center">
                            <i class="bi bi-speedometer2 d-block mx-auto mb-1 fs-3"></i>
                            {{__('Dashboard')}}
                        </a>
                    </li>
                    @endcan

                    <li class="mx-2">
                        <a class="nav-link text-center" href="{{ route('my_account') }}#/tickets">
                            <i class="bi bi-chat-left-text d-block mx-auto mb-1 fs-3"></i>
                            {{__('My Tickets')}}
                        </a>
                    </li>

                    @if (setting('frontend_enabled', true))
                    <li class="mx-2">
                        <a class="nav-link text-center" target="_blank" href="{{ route('frontend') }}">
                            <i class="bi bi-book d-block mx-auto mb-1 fs-3"></i>
                            {{__('knowledge_base')}}
                        </a>
                    </li>
                    @endif

                    <li class="mx-2">
                        <a href="#" class="nav-link text-center" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-bell d-block mx-auto mb-1 fs-3"></i>
                            {{__('notifications.notifications')}}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="header-notification">
                            <v-notifications></v-notifications>
                        </div>
                    </li>

                    <li class="mx-2">
                        <a href="{{ route('my_account.profile') }}" class="nav-link text-center">
                            <span class="d-block mx-auto mb-1 fs-3">
                                {{-- <img class="img-profile rounded-circle" width="32" src="{{ Auth::user()->avatar() }}"> --}}
                            </span>
                            {{ Auth::user()->name }}
                        </a>
                    </li>

                    {{-- <li class="mx-2">
                        <a href="#" class="nav-link text-center"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right d-block mx-auto mb-1 fs-3"></i>
                            {{ __('Logout') }}
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>
                        </a>
                    </li> --}}

                </ul>
            </div>
        </div>
    </div>

    <div class="px-3 py-2 mb-3 d-md-none d-sm-block ">
        <div class="container">

            <div class="clearfix">
                <div class="float-start">
                  <router-link to="/">
                    <img src="{{ asset(setting('app_logo')) }}" height="40" />
                  </router-link>
                </div>

                <div class="float-end">
                    <button class="btn btn-link fs-2  p-0 m-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#headerSidebar" aria-controls="headerSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="headerSidebar"
                aria-labelledby="headerSidebarLabel">
                <div class="offcanvas-header">
                    <div></div>
                    <button type="button" class="" data-bs-dismiss="offcanvas" aria-label="Close">
                        <i class="bi bi-x-lg fs-2"></i>
                    </button>
                </div>
                <div class="offcanvas-body">
                    <ul class="text-small">

                        <li class="mx-2">
                            <a href="{{ route('dashboard') }}" class="nav-link  text-center">
                                <i class="bi bi-speedometer2 d-block mx-auto mb-1 fs-3"></i>
                                {{__('Dashboard')}}
                            </a>
                        </li>
                        {{-- @if (Auth()->User()->isEmployee())
                        <li class="mx-2">
                            <a href="{{ route('dashboard') }}" class="nav-link  text-center">
                                <i class="bi bi-speedometer2 d-block mx-auto mb-1 fs-3"></i>
                                {{__('Dashboard')}}
                            </a>
                        </li>
                        @endif --}}

                        <li class="mx-2">
                            <router-link class="nav-link  text-center" to="/tickets">
                                <i class="bi bi-chat-left-text d-block mx-auto mb-1 fs-3"></i>
                                {{__('My Tickets')}}
                            </router-link>
                        </li>
                        <li class="mx-2">
                            <a class="nav-link  text-center" target="_blank" href="{{ route('frontend') }}">
                                <i class="bi bi-book d-block mx-auto mb-1 fs-3"></i>
                                {{__('knowledge_base')}}
                            </a>
                        </li>

                        <li class="mx-2">
                            <a href="#" class="nav-link  text-center" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="bi bi-bell d-block mx-auto mb-1 fs-3"></i>
                                {{__('notifications.notifications')}}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="header-notification">
                                <v-notifications></v-notifications>
                            </div>
                        </li>

                        <li class="mx-2">
                            <a href="{{ route('my_account.profile') }}" class="nav-link  text-center">
                                <span class="d-block mx-auto mb-1 fs-3">
                                    {{-- <img class="img-profile rounded-circle" width="32" src="{{ Auth::user()->avatar() }}"> --}}
                                </span>
                                {{ Auth::user()->name }}
                            </a>
                        </li>

                        <li class="mx-2">
                            <a href="#" class="nav-link  text-center"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right d-block mx-auto mb-1 fs-3"></i>
                                {{ __('Logout') }}
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>

</header>
