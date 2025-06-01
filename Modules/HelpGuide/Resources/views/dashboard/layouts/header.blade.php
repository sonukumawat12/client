<header class="bg-white py-0 page-header app-header border-bottom shadow-sm navbar navbar-expand-sm">
    <div class="container-fluid ps-0">
        {{-- <div>
            <button class="btn sidebarToggleBtn" aria-expanded="true" aria-label="collapse menu">
                <i class="bi bi-list fs-3"></i>
            </button>
        </div>
        <div class="mx-3">
            <a class="d-flex align-items-center justify-content-center app-logo" href="{{ route('dashboard') }}">
                <img src="{{ asset(setting('app_logo')) }}" />
            </a>
        </div> --}}
        <div class="mx-auto">
            <search-bar :searchtype="['articles','tickets', 'customers', 'categories']" placeholder="{{__('Search for')}}"></search-bar>
        </div>
        <div class="d-block d-sm-block d-md-none">
            <button class="btn HeaderMobileToggleBtn float-end" aria-expanded="true" aria-label="collapse menu">
                <i class="bi bi-list fs-3"></i>
            </button>
        </div>
        <div class="d-none d-md-block">
            <ul class="navbar-nav text-capitalize">
                <li class="nav-item dropdown ">
                  <button class="nav-link dropdown-toggle text-capitalize py-3" id="header-add-new" data-bs-toggle="dropdown" aria-expanded="false">
                    {{__('New')}}
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end text-start"  aria-labelledby="header-add-new">
                    @can('manage_articles')
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard') }}#/articles/new">
                            <i class="bi me-1 bi-newspaper"></i> {{__('article')}}
                        </a>
                    </li>
                    @endcan
                    {{-- @php
            dd(__('category'));
            @endphp --}}
                    @can('manage_categories')
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard') }}#/categories">
                            <i class="bi me-1 bi-folder"></i>  {{ "Categories" }}
                        </a>
                    </li>
                    @endcan
                    @can('manage_employees')
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard') }}#/employees" >
                            <i class="bi me-1 bi-person-check"></i>  {{__('employee')}}
                        </a>
                    </li>
                    @endcan
                    @can('manage_customers')
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard') }}#/customers" >
                            {{-- @php
            dd(__('customer'));
            @endphp --}}
                            <i class="bi me-1 bi-people"></i>  {{ "Customer" }}
                        </a>
                    </li>
                    @endcan
                </ul>
                </li>

                <div class="btn-group">
                    <button type="button" class="fs-4 border-0 btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="mx-2 bi bi-bell">
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="header-notification">
                        <v-notifications></v-notifications>
                    </div>
                </div>

                {{-- <li class="nav-item dropdown mt-1">
                    <a class="btn btn-light" type="button" aria-expanded="false" href="/home">
                        @if (optional(Auth::user())->first_name)
                            <span class="mx-2 d-none d-lg-inline text-gray-600 small">{{ "Home" }}</span>
                        @else
                            <script>
                                window.location.href = "{{ route('logout') }}";
                            </script>
                        @endif
                    </a>
                    <button class="btn btn-light dropdown-toggle" type="button" id="header-my-account" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        @if (optional(Auth::user())->first_name)
                            <span class="mx-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->first_name }}</span>
                        @else
                            <script>
                                window.location.href = "{{ route('logout') }}";
                            </script>
                        @endif
                        <img class="img-notification rounded-circle" width="24" src="{{ Auth::user()->avatar() }}">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="header-my-account">
                        <li>
                            <a class="dropdown-item" href="{{route('dashboard.profile')}}">
                                <i class="bi bi-person"></i> {{__('Profile')}}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-left"></i> {{ __('Logout') }}
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>
                            </a>
                        </li>
                    </ul>
                </li> --}}
            </ul>
        </div>
      </div>
  </header>
<div class="headerMobileMenu">
    <div class="headerMobileMenuContainer">
    <div class="clearfix">
        <button class="btn HeaderMobileToggleBtn float-end" aria-expanded="true" aria-label="collapse menu">
            <i class="bi bi-x-lg fs-3"></i>
        </button>
    </div>
    <div class="headerMobileMenuContent">
        <ul class="nav nav-pills mb-3 text-center" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-capitalize" id="pills-my-account-tab" data-bs-toggle="pill" data-bs-target="#pills-my-account" type="button" role="tab" aria-controls="pills-my-account" aria-selected="true">
                    {{__('My account')}}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-notification-tab text-capitalize" data-bs-toggle="pill" data-bs-target="#pills-notification" type="button" role="tab" aria-controls="pills-notification" aria-selected="false">
                    {{ __('notifications.notifications') }}
                </button>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-my-account" role="tabpanel" aria-labelledby="pills-my-account-tab">
                <ul class="dropdown" aria-labelledby="header-my-account">
                    <li>
                        <a class="dropdown-item" href="{{route('dashboard.profile')}}">
                            <i class="bi bi-person"></i> {{__('Profile')}}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-left"></i> {{ __('Logout') }}
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>

                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-pane fade" id="pills-notification" role="tabpanel" aria-labelledby="pills-notification-tab">
                <v-notifications></v-notifications>
            </div>
        </div>
    </div>
    </div>
</div>
