<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    |
    | In here you can define all the settings used in your app, it will be
    | available as a settings page where user can update it if needed
    | create sections of settings with a type of input.
    */

    'app' => [

        'title' => 'General',
        'key' => 'general',
        'desc' => 'General settings',
        'icon' => 'fas fa-sliders-h',

        'elements' => [
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'app_name',
                'label' => 'App Name',
                'rules' => 'required|min:2|max:50'
            ],
            [
                'type' => 'file',
                'data' => 'file',
                'name' => 'app_logo',
                'label' => 'App logo',
                'rules' => 'required'
            ],
            [
                'type' => 'file',
                'data' => 'file',
                'name' => 'favicon',
                'label' => 'Favicon',
                'rules' => 'required'
            ],
            [
                'type' => 'checkbox',
                'data' => 'integer',
                'name' => 'user_can_register',
                'label' => 'User can register',
                'rules' => '',
            ],
            [
                'type' => 'checkbox',
                'data' => 'boolean',
                'name' => 'verify_email',
                'label' => 'Users must verify their email address',
                'rules' => '',
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'admin_email',
                'label' => 'Admin email',
                'rules' => 'required'
            ],
            [
                'type' => 'select',
                'data' => 'string',
                'name' => 'default_lang',
                'label' => 'Language',
                'options' => [],
                'rules' => 'required'
            ]
        ]
    ],

    'Locale' => [

        'title' => 'Localization',
        'key' => 'localization',
        'desc' => 'Set your localization settings like format of Date and number etc.',
        'icon' => 'fas fa-globe',

        'elements' => [
            [
                'type' => 'select',
                'data' => 'string',
                'name' => 'date_format',
                'label' => 'Date format',
                'rules' => 'required',
                'class' => 'w-auto px-2',
                'options' => [
                    'm/d/Y' => date('m/d/Y'),
                    'm.d.y' => date("m.d.y"),
                    'j, n, Y' => date("j, n, Y"),
                    'M j, Y' => date("M j, Y"),
                    'D, M j, Y' => date('D, M j, Y')
                ],
                'value' => 'm/d/Y'
            ],
            [
                'type' => 'select',
                'data' => 'string',
                'name' => 'time_format',
                'label' => 'Time format',
                'rules' => 'string',
                'class' => 'w-auto px-2',
                'options' => [
                    'g:i a' => date('g:i a') . ' (12-hour format)',
                    'g:i:s A' => date('g:i A') . ' (12-hour format)',
                    'G:i' => date("G:i"). ' (24-hour format)',
                    'h:i:s a' => date("h:i:s a") . ' (12-hour with leading zero)',
                    'h:i:s A' => date("h:i:s A")
                ],
                'value' => 'g:i a'
            ],
            [
                'type' => 'select',
                'data' => 'string',
                'name' => 'timezone',
                'label' => 'Timezone',
                'class' => 'w-auto px-2',
                'rules' => 'string',
                'options' => array_combine(
                    DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                    DateTimeZone::listIdentifiers(DateTimeZone::ALL)
                ),
                'value' => config('app.timezone', 'UTC')
            ]
        ]
    ],

    'email' => [

        'title' => 'Email',
        'key' => 'email',
        'desc' => 'Email and SMPT settings',
        'icon' => 'fas fa-mail-bulk',

        'elements' => [
            [
                'type' => 'email',
                'data' => 'string',
                'name' => 'mail_from_address',
                'label' => 'From Email',
                'rules' => 'required|email'
            ],

            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'hutch_username',
                'label' => 'Hutch Username',
                'rules' => ''
            ],

            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'hutch_password',
                'label' => 'Hutch Password',
                'rules' => ''
            ],

            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'hutch_mask',
                'label' => 'Hutch Mask',
                'rules' => ''
            ],

            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'ultimate_token',
                'label' => 'Ultimate Token',
                'rules' => ''
            ],

            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'ultimate_sender_id',
                'label' => 'Ultimate Sender ID',
                'rules' => ''
            ],

            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'mail_from_name',
                'label' => 'From Name',
                'rules' => 'required|min:2|max:50'
            ],
            [
                'type' => 'select',
                'data' => 'string',
                'name' => 'mail_channel',
                'label' => 'Mail channel',
                'class' => 'w-auto px-2',
                'rules' => 'string',
                'options' => array_combine(
                    ['sendmail','smtp'],
                    ['PHP mail', 'SMTP']
                ),
                'value' => 'sendmail'
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'smtp_host',
                'label' => 'SMTP host',
                'rules' => 'required_if:mail_channel,smtp'
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'smtp_username',
                'label' => 'SMTP username',
                'rules' => 'required_if:mail_channel,smtp'
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'smtp_password',
                'label' => 'SMTP password',
                'rules' => 'required_if:mail_channel,smtp'
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'smtp_port',
                'label' => 'SMTP port',
                'rules' => 'required_if:mail_channel,smtp'
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'smtp_encryption',
                'label' => 'SMTP encryption',
                'rules' => 'required_if:mail_channel,smtp'
            ],
        ]
    ],

    // 'maintenance' => [

    //     'title' => 'maintenance mode',
    //     'key' => 'maintenance',
    //     'desc' => 'Enable / disable maintenance mode, maintenance message',
    //     'icon' => 'fas fa-wrench',

    //     'elements' => [
    //         [
    //             'type' => 'checkbox',
    //             'data' => 'integer',
    //             'name' => 'matenance_mode',
    //             'label' => 'Enable / disable maintenance mode'
    //         ],
    //         [
    //             'type' => 'textarea',
    //             'data' => 'text',
    //             'name' => 'maintenance_text',
    //             'label' => 'Maintenance message'
    //         ],
    //     ]
    // ]

    'oauth' => [
        'title' => 'oauth settings',
        'key' => 'oauth_settings',
        'desc' => '',
        'icon' => 'fas fa-cog',
        'hidden' => true,
        'elements' => [
            // Envato OAuth
            [
                'type' => 'checkbox',
                'data' => 'boolean',
                'name' => 'envato_oauth_enabled',
                'label' => 'envato_oauth_enabled',
                'rules' => ''
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'envato_oauth_app_id',
                'label' => 'envato_oauth_app_id',
                'rules' => ''
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'envato_oauth_app_secret',
                'label' => 'envato_oauth_app_secret',
                'rules' => ''
            ],

            // Facebook OAuth
            [
                'type' => 'checkbox',
                'data' => 'boolean',
                'name' => 'facebook_oauth_enabled',
                'label' => 'facebook_oauth_enabled',
                'rules' => ''
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'facebook_oauth_app_id',
                'label' => 'facebook_oauth_app_id',
                'rules' => ''
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'facebook_oauth_app_secret',
                'label' => 'facebook_oauth_app_secret',
                'rules' => ''
            ],

            // Google OAuth
            [
                'type' => 'checkbox',
                'data' => 'boolean',
                'name' => 'google_oauth_enabled',
                'label' => 'google_oauth_enabled',
                'rules' => ''
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'google_oauth_app_id',
                'label' => 'google_oauth_app_id',
                'rules' => ''
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'google_oauth_app_secret',
                'label' => 'google_oauth_app_secret',
                'rules' => ''
            ]
        ]
    ],
    'advanced_settings' => [

        'title' => 'Advanced settings',
        'key' => 'advanced_settings',
        'desc' => '',
        'icon' => 'fas fa-cog',
        'hidden' => true,

        'elements' => [
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'custom_js',
                'label' => 'Custom js',
                'rules' => ''
            ],
            [
                'type' => 'boolean',
                'data' => 'boolean',
                'name' => 'frontend_enabled',
                'label' => 'Enabled / Disable frontend',
                'rules' => ''
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'site_title',
                'label' => 'title',
                'rules' => ''
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'site_description',
                'label' => 'description',
                'rules' => ''
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'site_keywords',
                'label' => 'keywords',
                'rules' => ''
            ],
            [
                'type' => 'checkbox',
                'data' => 'string',
                'name' => 'app_debug',
                'label' => 'Debug mode',
                'rules' => ''
            ],
            [
                'type' => 'checkbox',
                'data' => 'string',
                'name' => 'enable_cache',
                'label' => 'Enable cache',
                'rules' => ''
            ]
        ]
    ],
    'ticket' => [
      'title' => 'Ticket settings',
      'key' => 'ticket_settings',
      'desc' => '',
      'icon' => 'bi bi-ticket',
      'hidden' => true,
      'elements' => [
        [
            'type' => 'text',
            'data' => 'number',
            'name' => 'ticket_default_agent',
            'label' => 'Ticket default agent',
            'rules' => ['required_if:ticket_auto_assign,false', 'nullable','exists:users,id']
        ],
        [
          'type' => 'text',
          'data' => 'number',
          'name' => 'ticket_auto_close',
          'label' => 'Ticket auto close',
          'rules' => ['in:0,7,15,30']
        ],
      ]
    ]
];