<li class="nav-item {{ in_array($request->segment(1), ['DocManagement']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#DocManagement-menu" aria-expanded="true" aria-controls="DocManagement-menu">
        <i class="ti-id-badge"></i>
        <span>Doc Management</span>
    </a>
    <div id="DocManagement-menu" class="collapse" aria-labelledby="headingPages"data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Doc Management:</h6>
             <a class="collapse-item {{$request->segment(1) == 'DocManagement' && $request->segment(2) == 'list_doc_management' ? 'active' : '' }}"
                href="{{ action('\Modules\DocManagement\Http\Controllers\DocManagementController@index') }}">Upload Docs</a>
            <a class="collapse-item {{$request->segment(1) == 'DocManagement' && $request->segment(2) == 'add_doc_management' ? 'active' : '' }}"
                href="{{ action('\Modules\DocManagement\Http\Controllers\DocManagementController@show_status') }}">Doc Status</a>
            <a class="collapse-item {{$request->segment(1) == 'DocManagement' && $request->segment(2) == 'doc_settings' ? 'active' : '' }}"
                href="{{ action('\Modules\DocManagement\Http\Controllers\DocManagementSettingsController@index') }}">Doc Settings</a>
        </div>
    </div>
</li>