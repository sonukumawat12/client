<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => defaultSetting('app_url', '').'/storage',
            'visibility' => 'public',
        ],


        // Ticket attachments storage
        'ticket' => diskSettings(
            defaultSetting('disk_ticket_driver', 'local'), 
            defaultSetting('disk_ticket_root', storage_path('app/public/tickets'))
        ),

        // Ticket conversation attachments storage
        'ticket_conversation' => diskSettings(
            defaultSetting('disk_ticket_conversation_driver', 'local'), 
            defaultSetting('disk_ticket_conversation_root', 'tickets/conversation')
        ),

        // Article images storage
        'article' => diskSettings(
            defaultSetting('disk_article_driver', 'local'),
            defaultSetting('disk_article_root', storage_path('app/public/articles'))
        ),

        // User avatar storage
        'avatar' => diskSettings(
            defaultSetting('disk_avatar_driver', 'local'),
            defaultSetting('disk_avatar_root', storage_path('app/public/avatars'))
        ),

        // all other files storage
        'media' => diskSettings(
            defaultSetting('disk_media_driver', 'local'),
            defaultSetting('disk_media_root', storage_path('app/public/medias'))
        )
    ],

];
