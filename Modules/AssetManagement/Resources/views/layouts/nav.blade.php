<li @if(request()->segment(2) == 'assets') class="nav-item active active-sub" @else class="nav-item" @endif>
    <a 
        class="nav-link collapsed" 
        href="{{action([\Modules\AssetManagement\Http\Controllers\AssetController::class, 'dashboard'])}}"
        data-toggle="collapse"
        data-target="#assetmanagement-menu"
        aria-expanded="true"
        aria-controls="assetmanagement-menu"
    >
    	<i class="fas fa fa-boxes"></i>
    	<span>@lang('assetmanagement::lang.asset_management')</span>
    </a>

    <div id="assetmanagement-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('assetmanagement::lang.asset_management'):</h6>
                @can('asset.view')
                    <a @if(request()->segment(2) == 'assets') class="collapse-item active" @else class="collapse-item" @endif  href="{{action([\Modules\AssetManagement\Http\Controllers\AssetController::class, 'index'])}}">
                        @lang('assetmanagement::lang.assets')
                    </a>
                    <a @if(request()->segment(2) == 'allocation') class="collapse-item active" @else class="collapse-item" @endif href="{{action([\Modules\AssetManagement\Http\Controllers\AssetAllocationController::class, 'index'])}}">
                        @lang('assetmanagement::lang.asset_allocated')
                    </a>
                    <a @if(request()->segment(2) == 'revocation') class="collapse-item active" @else class="collapse-item" @endif href="{{action([\Modules\AssetManagement\Http\Controllers\RevokeAllocatedAssetController::class, 'index'])}}">
                        @lang('assetmanagement::lang.revoked_asset')
                    </a>
                @endcan
                @if(auth()->user()->can('asset.view_all_maintenance') || auth()->user()->can('asset.view_own_maintenance'))
                    <a  @if(request()->segment(2) == 'maintenance') class="collapse-item active" @else class="collapse-item" @endif href="{{action([\Modules\AssetManagement\Http\Controllers\AssetMaitenanceController::class, 'index'])}}">
                        @lang('assetmanagement::lang.asset_maintenance')
                    </a>
                @endif
                @can('only_admin')
                	<a @if(request()->get('type') == 'asset') class="collapse-item active" @else class="collapse-item" @endif href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=asset'}}">
                		@lang('assetmanagement::lang.asset_categories')
                	</a>
                    <a @if(request()->segment(2) == 'settings') class="collapse-item active' @else class="collapse-item" @endif href="{{action([\Modules\AssetManagement\Http\Controllers\AssetSettingsController::class, 'index'])}}">
                        @lang('role.settings')
                    </a>
                @endcan

        </div>
    </div>
</li>
