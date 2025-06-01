<aside class="page-sidebar">
    <div class="page-sidebar-container shadow-sm border-end">

        <div class="d-flex d-block d-sm-none py-2 border-bottom mx-2">
            <button class="btn sidebarToggleBtnMobile" aria-expanded="true" aria-label="collapse menu">
                <i class="bi bi-x-lg fs-3"></i>
            </button>
            <a class="d-flex align-items-center m-auto logo" href="{{ route('dashboard') }}">
                <img src="{{ asset(setting('app_logo')) }}" />
            </a>
        </div>

        <div class="page-sidebar-menu" data-simplebar>
            <nav>
                <ul class="admin-menu text-capitalize">

                    @foreach (backendMenu('sidebar') as $item)
                    @if(count($item['permissions']) != 0 )
                    @cannot($item['permissions'])
                    @continue
                    @endcan
                    @endif

                    <li @if($item['sub_items']) class="has-child collapse show" @endif>
{{-- 
                        @if($item['sub_items'])

                          <a class="collapsed text-truncate item-header" href="{{ $item['name'] }}"
                              title="{{ __($item['name']) }}"
                              data-toggle="collapse">
                              <i class="{{ $item['icon'] }} me-3"></i>
                              <span>{{ __($item['name']) }}</span>
                          </a>

                        @else --}}

                        @if( isset($item['vue_route']) )
                        <a href="{{ route('dashboard').$item['vue_route'] }}" title="{{ __($item['name']) }}">
                            <i class="{{ $item['icon'] }} me-3"></i>
                            <span>{{ __($item['name']) }}</span>
                        </a>
                        @else
                        <a href="@if($item['route']) {{route($item['route'])}} @else {{$item['route']}}@endif"
                            @if($item['target']) target="{{ $item['target'] }}" @endif title="{{ __($item['name']) }}">
                            <i class="{{ $item['icon'] }} me-3"></i>
                            <span>{{ __($item['name']) }}</span>
                        </a>
                        @endif

                        @if($item['sub_items'])

                        <ul class="collapse" id="item-{{ $item['name'] }}" aria-expanded="false">
                            <li>
                                <a href="@if($item['route']) {{route($item['route'])}} @else {{$item['route']}}@endif"
                                    @if($item['target']) target="{{ $item['target'] }}" @endif data-bs-toggle="tooltip"
                                    data-bs-placement="right" title="{{ __($item['name']) }}">
                                    <i class="{{ $item['icon'] }} me-3"></i>
                                    <span> @if(isset($item['label'])) {{ __($item['name']) }}@else {{ __($item['name']) }}
                                        @endif </span>
                                </a>
                            </li>

                            @foreach ($item['sub_items'] as $subitem)
                            @if(count($subitem['permissions']) != 0 )
                            @cannot($subitem['permissions'])
                            @continue
                            @endcan
                            @endif
                            <li>
                                <a href="@if($subitem['route']) {{route($subitem['route'])}} @else {{$subitem['route']}}@endif"
                                    @if($subitem['target']) target="{{ $subitem['target'] }}" @endif
                                     title="{{ __($subitem['name']) }}">
                                    <i class="{{ $subitem['icon'] }} me-3"></i>
                                    <span>{{ __($subitem['name']) }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                    </li>
                    @endforeach

                </ul>
            </nav>
        </div>
    </div>
</aside>