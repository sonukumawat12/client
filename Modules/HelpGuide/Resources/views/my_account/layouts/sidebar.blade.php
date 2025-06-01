<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="bi bi-three-dots-vertical"></i>
                </span>
            </button>
        </span>
    </div> 
    <div class="scrollbar-sidebar pt-2">
        <div class="app-sidebar__inner scrollbar-container">
            <ul class="vertical-nav-menu">
                <li>
                    <a target="_blank" href="{{ route('frontend') }}" >
                        <i class="bi bi-eye"></i>
                        <span>{{__('Home page')}}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard') }}">
                    <i class="bi bi-list-ul"></i>
                    <span>{{__('My tickets')}}</span></a>
                </li>
                <li>
                    <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2 text-gray-400"></i>
                        {{ __('Logout') }}
                    </a>
                </li>
                
            </ul>
        </div>
    </div>
</div>