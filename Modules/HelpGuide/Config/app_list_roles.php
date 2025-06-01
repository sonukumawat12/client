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
            'delete_ticket',
            'delete_any_ticket',
            'permanently_delete_ticket',
            'manage_tickets',
            'reassign_ticket',
            'update_any_ticket',
    
            'create_ticket_reply',
            'create_any_ticket_reply',
            'update_ticket_reply',
            'delete_ticket_reply',
            'delete_any_ticket_reply',
    
            'manage_categories',
            'create_category',
            'edit_category',
            'delete_category',
            'view_category',
            'view_any_category',
            'delete_any_category',
            'update_any_category',
    
            'create_user',
            'update_user',
            'delete_user',
            'view_user',
            'viewany_user',
            'permanently_delete_user',
            
            'manage_customers',
            'create_customer',
            'update_customer',
    
            'manage_employees',
            'create_employee',
            'update_employee',
    
            'assign_role',
            'create_role',
            'edit_role',
            'delete_role',
            'view_role',
            'permanently_delete_role',
    
            'assign_permissions',
    
            'manage_articles',
            'create_article',
            'update_article',
            'delete_article',
            'permanently_delete_article',
            'unpublish_article',
            'publish_article',
            'view_any_article',
            'delete_any_article',
            'update_any_article',
    
            'create_saved_reply',
            'view_saved_reply',
            'edit_saved_reply',
            'delete_saved_reply',
    
            'view_error_logs',
    
            'update_settings',

            'view_settings',

            'update_application',
    
            'view_customer_purchase',
            'update_customer_purchase',

            'admin_only',
            'upload_module',
            'list_modules',
            'manage_modules',

            'statistics_view',
            'statistics_view_any'
        ],
        "agent" => [
            'edit_ticket',
            'view_ticket',
            'close_ticket',
            'reply_ticket',
            'view_category',
            'create_ticket_reply',

            'create_saved_reply',
            'edit_saved_reply',
            'delete_saved_reply',

            'statistics_view',
            'statistics_view_any'
        ],
        "non-restricted_agent" => [
            'edit_ticket',
            'view_ticket',
            'close_ticket',
            'reply_ticket',
            'view_category',
            'create_ticket_reply',

            'create_saved_reply',
            'view_saved_reply',
            'edit_saved_reply',
            'delete_saved_reply',
            'view_customer_purchase',

            'statistics_view',
            'statistics_view_any'
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