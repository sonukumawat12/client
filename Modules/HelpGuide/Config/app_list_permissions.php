<?php

return [

    // Ticket
    'tickets' => [
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
        'reply_ticket',
        'create_ticket_reply',
        'create_any_ticket_reply',
        'update_ticket_reply',
        'delete_ticket_reply',
        'delete_any_ticket_reply',
        'update_any_ticket_reply'
    ],
    
    // Categories
    'categories' => [
        'manage_categories',
        'create_category',
        'edit_category',
        'delete_category',
        'view_category',
        'view_any_category',
        'delete_any_category',
        'update_any_category'
    ],

    // Users
    'users' => [
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
        'update_employee'
    ],

    // Roles and permissions
    'roles' => [
        'assign_role',
        'create_role',
        'edit_role',
        'delete_role',
        'view_role',
        'permanently_delete_role',
        'assign_permissions',
        'manage_acl'
    ],

    // Articles
    'articles' => [
        'manage_articles',
        'create_article',
        'update_article',
        'delete_article',
        'permanently_delete_article',
        'unpublish_article',
        'publish_article',
        'view_any_article',
        'delete_any_article',
        'update_any_article'
    ],

    // Saved reply
    'saved_reply' => [
        'create_saved_reply',
        'view_saved_reply',
        'edit_saved_reply',
        'delete_saved_reply'
    ],

    'modules' => [
        // modules
        'upload_module', // Upload module zip file
        'list_modules', // List modules
        'manage_modules' // Disable / Enable modules
    ],

    // Statistics
    'statistics' => [
      'statistics_view',
      'statistics_view_any'
    ],

    'other' => [
        // User settings
        'add_reply_signature',

        // Error logs
        'view_error_logs',
        'delete_error_log',

        // Settings
        'update_settings',
        'view_settings',

        'update_application',

        'view_customer_purchase',
        'update_customer_purchase',
        
        // Special permissions
        'admin_only'
    ]

];