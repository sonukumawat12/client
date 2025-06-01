<?php

return [
    "roles" => [
        "admin" => [
            'create_ticket',
            'edit_ticket',
            'update_ticket',
            'view_ticket',
            'view_any_ticket',
            'close_ticket',
            'reply_ticket',
            'update_ticket_reply',
            'update_any_ticket_reply',

            'manage_tickets',
            'reassign_ticket',
            'update_any_ticket',
    
            'create_ticket_reply',
            'create_any_ticket_reply',

            'manage_categories',
            'create_category',
            'view_category',
            'view_any_category',

            'view_user',
            'viewany_user',

            'manage_customers',
    
            'manage_employees',

            'manage_articles',
            'create_article',
            'view_any_article',

            'create_saved_reply',
            'view_saved_reply',

            'list_modules',

            'view_settings',

            'manage_acl',

            'statistics_view',
            'statistics_view_any',

            'view_customer_purchase',

            'update_any_ticket_reply',

            'publish_article',
            
        ],
        "agent" => [
            'create_ticket',
            'edit_ticket',
            'update_ticket',


            'view_ticket',
            'close_ticket',
            'reply_ticket',
            'view_category',
            'create_ticket_reply',
            'update_any_ticket_reply',

            'create_saved_reply',
        ],
        "non-restricted_agent" => [
            'edit_ticket',
            'create_ticket',
            'update_ticket',
            'view_ticket',
            'close_ticket',
            'reply_ticket',
            'view_category',
            'create_ticket_reply',

            'create_saved_reply',
            'view_saved_reply',
            'update_any_ticket_reply',
            'view_customer_purchase'

        ],
        "customer" => [
            'create_ticket',
            'view_ticket',
            'close_ticket',
            'reply_ticket', 
            'view_category',
            'create_ticket_reply'
        ]
    ]
];