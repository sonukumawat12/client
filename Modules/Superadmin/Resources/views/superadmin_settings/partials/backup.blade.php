<div class="row">
    <div class="col-xs-4">
        <div class="form-group">
            {!! Form::label('BACKUP_DISK', __('superadmin::lang.backup_disk') . ':') !!}
            {!! Form::select('BACKUP_DISK', $backup_disk, $default_values['BACKUP_DISK'], ['class' => 'form-control']); !!}
        </div>
    </div>
</div>

<div class="row @if(env('BACKUP_DISK') != 'dropbox') hide @endif" id="dropbox_access_token_div">
    <div class="form-group col-sm-6">
            {!! Form::label('DROPBOX_ACCESS_TOKEN', __('superadmin::lang.dropbox_access_token') . ':') !!}
            {!! Form::text('DROPBOX_ACCESS_TOKEN', $default_values['DROPBOX_ACCESS_TOKEN'], ['class' => 'form-control','placeholder' => __('superadmin::lang.dropbox_access_token')]); !!}
    </div>
    <br>
    <p class="help-block">{!! __('superadmin::lang.dropbox_help', ['link' => 'https://www.dropbox.com/developers/apps/create']) !!}</p>
</div>

<div class="row @if(env('BACKUP_DISK') != 'google') hide @endif" id="google_access_token_div">
    <div class="form-group col-sm-4">
            {!! Form::label('GOOGLE_DRIVE_CLIENT_ID', __('superadmin::lang.google_drive_client') . ':') !!}
            {!! Form::text('GOOGLE_DRIVE_CLIENT_ID', $default_values['GOOGLE_DRIVE_CLIENT_ID'], ['class' => 'form-control','placeholder' => __('superadmin::lang.google_drive_client')]); !!}
    </div>
    
     <div class="form-group col-sm-4">
            {!! Form::label('GOOGLE_DRIVE_CLIENT_SECRET', __('superadmin::lang.google_drive_secret') . ':') !!}
            {!! Form::text('GOOGLE_DRIVE_CLIENT_SECRET', $default_values['GOOGLE_DRIVE_CLIENT_SECRET'], ['class' => 'form-control','placeholder' => __('superadmin::lang.google_drive_secret')]); !!}
    </div>
    
     <div class="form-group col-sm-4">
            {!! Form::label('GOOGLE_DRIVE_REFRESH_TOKEN', __('superadmin::lang.google_drive_refresh') . ':') !!}
            {!! Form::text('GOOGLE_DRIVE_REFRESH_TOKEN', $default_values['GOOGLE_DRIVE_REFRESH_TOKEN'], ['class' => 'form-control','placeholder' => __('superadmin::lang.google_drive_refresh')]); !!}
    </div>
    
    <div class="form-group col-sm-4">
            {!! Form::label('GOOGLE_FOLDER_NAME', __('superadmin::lang.google_folder_name') . ':') !!}
            {!! Form::text('GOOGLE_FOLDER_NAME', $default_values['GOOGLE_FOLDER_NAME'], ['class' => 'form-control','placeholder' => __('superadmin::lang.google_folder_name')]); !!}
    </div>

    <div class="form-group col-sm-4">
        {!! Form::label('BACKUP_RETENTION_DAYS', __('superadmin::lang.backup_retention_days') . ':') !!}
        {!! Form::number('BACKUP_RETENTION_DAYS', $default_values['BACKUP_RETENTION_DAYS'], ['class' => 'form-control','placeholder' => __('superadmin::lang.backup_retention_days')]); !!}
</div>
    
</div>    
    
    