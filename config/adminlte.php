<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'AdminLTE 3',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Admin</b>LTE',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'AdminLTE',

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        ['header' => 'USER'],
        [
            'text' => 'User Management',
            'url' => 'admin/user',
            'icon' => 'users',
            'can' => 'manage-users',
        ],
        [
            'text' => 'User Allocation',
            'url' => 'admin/allocation',
            'icon' => 'check-square-o',
            'can' => 'employee-allocation',
        ],
        'CUSTOMER',
        [
            'text' => 'Customers',
            'url' => 'admin/customer',
            'icon' => 'male',
            'can' => 'manage-customers',
        ],
        [
            'text' => 'Customers Shift',
            'url' => 'admin/customers-shift',
            'icon' => 'male',
            'can' => 'manage-customers',
        ],
        [
            'text' => 'Customer Fences',
            'url' => 'admin/customerfences',
            'icon' => 'simplybuilt',
            'can' => 'manage-customers',
        ],
        [
            'text' => 'Email - Client Groups',
            'url' => 'admin/email-groups',
            'icon' => 'envelope',
            'can' => 'manage-customers',
        ],
        [
            'text' => 'Email Accounts',
            'url' => 'admin/email-accounts',
            'icon' => 'money',
            'can' => 'manage-customers',
        ],
        [
            'text' => 'Guard Routes',
            'url' => 'admin/guardroutes',
            'icon' => 'road',
            'can' => 'manage-customers',
        ],
        [
            'text' => 'Parent Customer',
            'url' => 'admin/parentcustomer',
            'icon' => 'male',
            'can' => 'manage-customers',
        ],

        [
            'text' => 'Customer Allocation',
            'url' => 'admin/customer-allocation',
            'icon' => 'calendar',
            'can' => 'customer-allocation',
        ],
        [
            'text' => 'Shift Module',
            'url' => 'admin/customer-shift-module',
            'icon' => 'calendar',
            'can' => 'manage-customers',
        ],
        [
            'text' => 'Shift Module Dropdown',
            'url' => 'admin/customer-shift-module-dropdown',
            'icon' => 'caret-square-o-down',
            'can' => 'manage-customers',
        ],
        [
            'text' => 'Shift Module Color Settings',
            'url' => 'admin/color_settings',
            'icon' => 'file-photo-o',
            'can' => 'manage-customers',
        ],
        [
            'text' => 'Customer Rooms',
            'url' => 'admin/customer-rooms',
            'icon' => 'th',
            'can' => 'manage-customers',
        ],
        'RECRUITMENT REVAMP',
        [
            'text' => 'Masters',
            'icon' => 'database',
            'can' =>  'recruitment_masters',
            'submenu' => [
                [
                    'text' => 'Process Steps',
                    'url' => 'admin/recruitment/process-steps',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Process Tab',
                    'url' => 'admin/recruitment/process-tab',
                    'icon' => 'list',

                ],
                [
                    'text' => 'Brand Awareness',
                    'url' => 'admin/recruitment/brand-awareness',
                    'icon' => 'list',
                ],
                [
                    'text' => 'English Ratings',
                    'url' => 'admin/recruitment/english-rating',
                    'icon' => 'list',
                ],
                [

                    'text' => 'Security Awareness',
                    'url' => 'admin/recruitment/security-awareness',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Experiences',
                    'url' => 'admin/recruitment/experience-lookups',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Experience Rating',
                    'url' => 'admin/recruitment/rate-experiences',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Understandings',
                    'url' => 'admin/recruitment/commissionaires-understanding',
                    'icon' => 'font-awesome',
                ],
                [
                    'text' => 'Competency Rating',
                    'url' => 'admin/recruitment/competency-matrix-rating',
                    'icon' => 'list',

                ],
                [

                    'text' => 'Competency Category',
                    'url' => 'admin/recruitment/competency-matrix-category',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Competency',
                    'url' => 'admin/recruitment/competency-matrix',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Criteria Requirements',
                    'url' => 'admin/recruitment/criteria-lookups',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Security Clearance',
                    'url' => 'admin/recruitment/security-clearance',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Uniform Items',
                    'url' => 'admin/recruitment/uniform-items',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Uniform Sizes',
                    'url' => 'admin/recruitment/uniform-sizes',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Uniform Measurements',
                    'url' => 'admin/recruitment/uniform-measurement-points',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Onboarding Documents',
                    'url' =>  'admin/recruitment/onboarding-documents',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Match Score Criteria',
                    'url' => 'admin/recruitment/match-score-criteria',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Job Requisition Reasons',
                    'url' => 'admin/recruitment/job-requisition-reason',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Assignment Types',
                    'url' => 'admin/recruitment/candidate-assignment-type',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Required Timings',
                    'url' => 'admin/recruitment/training-timing',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Required Trainings',
                    'url' => 'admin/recruitment/training',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Job Ticket Settings',
                    'url' => 'admin/recruitment/job-ticket-settings',
                    'icon' => 'ticket',
                ],
                [
                    'text' => 'Score Criteria',
                    'url' => 'admin/recruitment/score-criteria',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Candidate Feedback',
                    'url' => 'admin/recruitment/candidate-feedback-lookup',
                    'icon' => 'edit',
                ],
                [
                    'text' => 'Licence Threshold',
                    'url' => 'admin/recruitment/licence-threshold',
                    'icon' => 'font-awesome',
                ],
            ],
        ],
        [
            'text' => 'Document Allocation',
            'url' =>  'admin/recruitment/document-allocation',
            'icon' => 'file-text',
            'can' =>  'recruitment_masters',
        ],
        [
            'text' => 'Uniform Kit',
            'url' =>  'admin/recruitment/customer-uniform-kits',
            'icon' => 'black-tie',
            'can' =>  'recruitment_masters',
        ],
        'MASTERS',
        [
            'text' => 'General',
            'icon' => 'database',
            // 'can' => 'manage-masters',
            'can' => 'general_masters',
            'submenu' => [
                [
                    'text' => 'Site Settings',
                    'url' => 'admin/sitesettings',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Client Schedule Settings',
                    'url' => 'admin/threshold',
                    'icon' => 'cogs',
                    'can' => 'manage-schedule-threshold',
                ],
                [
                    'text' => 'Pay Periods',
                    'url' => 'admin/payperiod',
                    'icon' => 'hourglass',
                ],
                [
                    'text' => 'Holidays',
                    'url' => 'admin/holiday',
                    'icon' => 'plane',
                ],
                [
                    'text' => 'Work Types',
                    'url' => 'admin/worktype',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Positions',
                    'url' => 'admin/position',
                    'icon' => 'sort-amount-asc',
                ],
                [
                    'text' => 'Regions',
                    'url' => 'admin/region',
                    'icon' => 'globe',
                ],
                [
                    'text' => 'Industry Sectors',
                    'url' => 'admin/industry-sector',
                    'icon' => 'industry',
                ],
                [
                    'text' => 'Employee Ratings',
                    'url' => 'admin/employee-rating',
                    'icon' => 'star',
                ],
                [
                    'text' => 'Smart Phone Types',
                    'url' => 'admin/smart-phone-type',
                    'icon' => 'mobile',
                ],
                [
                    'text' => 'Role',
                    'url' => 'admin/rolelookup',
                    'icon' => 'book',
                ],
                [
                    'text' => 'User Certificates',
                    'url' => 'admin/user-certificate',
                    'icon' => 'book',
                ],
                [
                    'text' => 'Customer Types',
                    'url' => 'admin/customer-types',
                    'icon' => 'tasks',
                    // 'can' => 'manage-user-certificate-expiry-settings', //TODO:check later
                ],
                [
                    'text' => 'Document Expiry Settings',
                    'url' => 'admin/user-certificate-expiry-settings',
                    'icon' => 'cogs',
                    'can' => 'manage-user-certificate-expiry-settings',
                ],
                [
                    'text' => 'Permission Mapping',
                    'url' => 'admin/permission-mapping',
                    'icon' => 'cogs',
                ],
                [
                    'text' => 'User Payroll Groups',
                    'url' => 'admin/user-payroll-groups',
                    'icon' => 'cogs',
                    // 'can' => 'manage-schedule-threshold', TODO://add permission
                ],
                [
                    'text' => 'Marital Status',
                    'url' => 'admin/marital-status',
                    'icon' => 'cogs',
                    // 'can' => 'manage-schedule-threshold', TODO://add permission
                ],
                [
                    'text' => 'Banks',
                    'url' => 'admin/banks',
                    'icon' => 'university',
                ],
                [
                    'text' => 'Salutation',
                    'url' => 'admin/salutation',
                    'icon' => 'venus-mars',
                ],
                [
                    'text' => 'Contact Relation',
                    'url' => 'admin/user-emergency-contact-relation',
                    'icon' => 'male',
                ],
                [
                    'text' => 'Summary Dashboard',
                    'url' => 'admin/summary-dashboard',
                    'icon' => 'list',
                ],
            ],
        ],
        [
            'text' => 'Recruiting',
            'icon' => 'database',
            // 'can' => 'manage-masters',
            'can' => 'recruiting_masters',
            'submenu' => [
                [
                    'text' => 'Job Requisition Reasons',
                    'url' => 'admin/job-requisition-reason',
                    'icon' => 'black-tie',
                ],
                [
                    'text' => 'Policy and Procedure',
                    'url' => 'admin/rating-policy',
                    'icon' => 'shield',
                ],

                [
                    'text' => 'Assignment Types',
                    'url' => 'admin/candidate-assignment-type',
                    'icon' => 'calendar-check-o',
                ],
                [
                    'text' => 'Required Trainings',
                    'url' => 'admin/training',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Required Timings',
                    'url' => 'admin/training-timing',
                    'icon' => 'clock-o',
                ],
                [
                    'text' => 'Criteria Requirements',
                    'url' => 'admin/criteria',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Experiences',
                    'url' => 'admin/candidate-experience',
                    'icon' => 'shield',
                ],
                [
                    'text' => 'Candidate Feedback',
                    'url' => 'admin/candidate-feedback-lookup',
                    'icon' => 'edit',
                ],
                [
                    'text' => 'Tracking Process',
                    'url' => 'admin/tracking-lookup',
                    'icon' => 'exchange',
                ],
                [
                    'text' => 'Security Clearance',
                    'url' => 'admin/security-clearance',
                    'icon' => 'bullseye',
                ],
                [
                    'text' => 'Schedule Assignment Types',
                    'url' => 'admin/schedule-assignment-type',
                    'icon' => 'calendar-check-o',
                ],
                [
                    'text' => 'Termination Reasons',
                    'url' => 'admin/candidate-termination-reason',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Exit Termination Reasons',
                    'url' => 'admin/exit-termination-reason',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Exit Resignation Reasons',
                    'url' => 'admin/exit-resignation-reason',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Whistleblower Categories',
                    'url' => 'admin/employee-whistleblower-category',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Whistleblower Priorities',
                    'url' => 'admin/employee-whistleblower-priority',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Brand Awareness',
                    'url' => 'admin/candidate-brand-awareness',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Security Awareness',
                    'url' => 'admin/candidate-security-awareness',
                    'icon' => 'tasks',
                ],

                [
                    'text' => 'Candidate Password',
                    'url' => 'admin/settings/genericpwd',
                    'icon' => 'lock',
                ],
                [
                    'text' => 'Schedule Shift Timings',
                    'url' => 'admin/schedule-shift-timings',
                    'icon' => 'map-marker',
                ],
                [
                    'text' => 'Schedule Maximum Hours',
                    'url' => 'admin/schedule-maximum-hours',
                    'icon' => 'hourglass',
                ],
                [
                    'text' => 'Competency Category',
                    'url' => 'admin/competency-matrix-category',
                    'icon' => 'table',
                ],
                [
                    'text' => 'Competency',
                    'url' => 'admin/competency-matrix',
                    'icon' => 'table',
                ],
                [
                    'text' => 'English Ratings',
                    'url' => 'admin/english-rating',
                    'icon' => 'star',
                ],
                [
                    'text' => 'Competency Rating',
                    'url' => 'admin/competency-matrix-rating',
                    'icon' => 'table',
                ],
                [
                    'text' => 'Experience Rating',
                    'url' => 'admin/rate-experiences',
                    'icon' => 'star',
                ],
                [
                    'text' => 'Understandings',
                    'url' => 'admin/commissionaires-understanding',
                    'icon' => 'font-awesome',
                ],
                [
                    'text' => 'Licence Threshold',
                    'url' => 'admin/licence-threshold',
                    'icon' => 'font-awesome',
                ],
                [
                    'text' => 'Job Ticket Settings',
                    'url' => 'admin/job-ticket-settings',
                    'icon' => 'ticket',
                ],
                [
                    'text' => 'Dashboard Settings',
                    'url' => 'admin/recruitment_dashboard_index',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Job Post Finding',
                    'url' => 'admin/job-post-finding',
                    'icon' => 'bullseye',
                ],
                [
                    'text' => 'Whistleblower Master',
                    'url' => 'admin/whistleblower-master',
                    'icon' => 'star',
                ],
            ],
        ],
        [
            'text' => 'Employee Time Off',
            'icon' => 'database',
            // 'can' => 'manage-masters',
            'can' => 'employee_timeoff_masters',
            'submenu' => [
                [
                    'text' => 'Create ESA Category',
                    'url' => 'admin/time-off-category',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Manage Request Type',
                    'url' => 'admin/time-off-request-type',
                    'icon' => 'list',
                ],
                [
                    'text' => 'OC Email',
                    'url' => 'admin/operation-centre-email',
                    'icon' => 'list',
                ],
            ],
        ],

        /*[
        'text' => 'Time Tracker',
        'icon' => 'database',
        'can' => 'manage-masters',
        ],*/
        [
            'text' => 'Supervisor Panel',
            'icon' => 'database',
            // 'can' => 'manage-masters',
            'can' => 'supervisor_panel_masters',
            'submenu' => [
                [
                    'text' => 'Templates',
                    'url' => 'admin/templates',
                    'icon' => 'list',
                    //'can' => 'manage-blog',
                ],
                [
                    'text' => 'Template Settings',
                    'url' => 'admin/templatesettings',
                    'icon' => 'cogs',
                    //'can' => 'manage-blog',
                ],
                [
                    'text' => 'Questions Categories',
                    'url' => 'admin/templatequestioncategory',
                    'icon' => 'file-text',
                    //'can' => 'manage-masters-payperiods',
                ],
                [
                    'text' => 'Incident Categories',
                    'url' => 'admin/incident_categories',
                    'icon' => 'list-alt',
                    //'can' => 'manage-masters-payperiods',
                ],
                [
                    'text' => 'Incident Report Subjects',
                    'url' => 'admin/incidentreportsubjects',
                    'icon' => 'book',
                    //'can' => 'manage-masters-payperiods',
                ],
                [
                    'text' => 'Leave Reasons',
                    'url' => 'admin/leavereasons',
                    'icon' => 'black-tie',
                    //'can' => 'manage-masters-payperiods',
                ],
                [
                    'text' => 'Site Note Status',
                    'url' => 'admin/sitestatus',
                    'icon' => 'tasks',
                    //'can' => 'manage-masters-payperiods',
                ],
                [
                    'text' => 'STC Report Colors',
                    'url' => 'admin/stc-template-rule',
                    'icon' => 'tasks',
                    //'can' => 'manage-masters-payperiods',
                ],
                [
                    'text' => 'STC Threshold',
                    'url' => 'admin/stc_threshold_index',
                    'icon' => 'cogs',
                    //'can' => 'manage-masters-payperiods',
                ],
                [
                    'text' => 'Incident Priorities',
                    'url' => 'admin/incident-priority',
                    'icon' => 'list',

                ],

            ],
        ],
        [
            'text' => 'Timetracker',
            'icon' => 'database',
            // 'can' => 'manage-masters',
            'can' => 'time_tracker_masters',
            'submenu' => [
                [
                    'text' => 'Mobile App Settings',
                    'url' => 'admin/mobilesettings',
                    'icon' => 'hourglass',
                ],
                [
                    'text' => 'Spare Bonus Model Settings',
                    'url' => 'admin/spare-bonus-model-settings',
                    'icon' => 'hourglass',
                ],
                [
                    'text' => 'Security Patrol Subject',
                    'url' => 'admin/mobile-security-patrol-subject',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Satellite Tracking Settings',
                    'url' => 'timetracker/admin/satellite-tracking-settings',
                    'icon' => 'list',
                ],
                [
                    'text' => 'QR Patrol Widget Settings',
                    'url' => 'admin/qr-patrol-settings',
                    'icon' => 'cogs',
                ],
                [
                    'text' => 'Timesheet Configuration',
                    'url' => 'admin/timesheet-configuration',
                    'icon' => 'envelope',
                ],
                [
                    'text' => 'CPID',
                    'url' => 'admin/cp-id',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'CPID Functions',
                    'url' => 'admin/cpid-function',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Activity Type',
                    'url' => 'admin/work-hour-type',
                    'icon' => 'envelope',
                ],
                [
                    'text' => 'Activity Code Setup',
                    'url' => 'admin/work-hour-customer',
                    'icon' => 'envelope',
                ],
                [
                    'text' => 'Payroll Settings',
                    'url' => 'admin/payroll-settings',
                    'icon' => 'tasks',
                ],
            ],
        ],
        [
            'text' => 'Key Management',
            'icon' => 'database',
            'can' => 'key_management_lookups',

            'submenu' => [
                [
                    'text' => 'Identification Document',
                    'url' => 'admin/identification-document',
                    'icon' => 'id-card',
                ],
            ],
        ],
        [
            'text' => 'MST',
            'icon' => 'database',
            'can' => 'mst_lookups',
            'submenu' => [
                [
                    'text' => 'Dispatch Request Type',
                    'url' => 'admin/dispatch-request-types',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Push Notification',
                    'url' => 'admin/push-notification-role-settings',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Coordinate Idle',
                    'url' => 'admin/dispatch-coordinate-settings',
                    'icon' => 'list',
                ],
            ],
        ],
        /* [
        'text' => 'Training and Learning',
        'icon' => 'database',
        'can' => 'training_learn_lookups',
        'submenu' => [
        [
        'text' => 'Create Categories',
        'url' => 'admin/course-category',
        'icon' => 'tasks',
        ],
        [
        'text' => 'Create Course',
        'url' => 'admin/course',
        'icon' => 'tasks',
        ],
        [
        'text' => 'Define Employee Profile',
        'url' => 'admin/employee-profile',
        'icon' => 'users',
        ],
        [
        'text' => 'Define Site Profile',
        'url' => 'admin/site-profile',
        'icon' => 'users',
        ],
        ],
        ],*/
        [
            'text' => 'Compliance',
            'icon' => 'database',
            'can' => 'compliance_lookups',
            'submenu' => [
                [
                    'text' => 'Categories',
                    'url' => 'admin/compliance-policy-category',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Policy Dashboard',
                    'url' => 'admin/policy',
                    'icon' => 'tasks',
                ],
                //                [
                //                    'text' => 'Policy Analytics',
                //                    'url' => 'admin/policy/analytcis',
                //                    'icon' => 'tasks',
                //                ],
            ],
        ],
        [
            'text' => 'Capacity Tool',
            'icon' => 'database',
            'can' => 'capacity_tool_lookups',
            'submenu' => [
                [
                    'text' => 'Area',
                    'url' => 'admin/area',
                    'icon' => 'list',
                    //'can' => 'manage-blog',
                ],
                [
                    'text' => 'Task Frequency',
                    'url' => 'admin/task-frequency',
                    'icon' => 'tasks',
                    //'can' => 'manage-blog',
                ],
                [
                    'text' => 'Status',
                    'url' => 'admin/status',
                    'icon' => 'file-text',
                    //'can' => 'manage-masters-payperiods',
                ],
                [
                    'text' => 'Objective',
                    'url' => 'admin/objective',
                    'icon' => 'book',
                    //'can' => 'manage-masters-payperiods',
                ],
                [
                    'text' => 'Skill Type',
                    'url' => 'admin/skill-type',
                    'icon' => 'black-tie',
                    //'can' => 'manage-masters-payperiods',
                ],

            ],
        ],
        [
            'text' => 'Client',
            'icon' => 'database',
            'can' => 'client_lookups',
            'submenu' => [
                [
                    'text' => 'Feedback Types',
                    'url' => 'admin/client-feedback',
                    'icon' => 'star',
                ],
                [
                    'text' => 'Severity Level',
                    'url' => 'admin/severity',
                    'icon' => 'hourglass',
                ],
                [
                    'text' => 'Visitor Log Template',
                    'url' => 'admin/visitorlog-templates',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Template Allocation',
                    'url' => 'admin/template-allocation',
                    'icon' => 'check-square-o',
                ],
                [
                    'text' => 'Terms And Conditions',
                    'url' => 'admin/customer-terms-and-conditions',
                    'icon' => 'legal',
                ],
                [
                    'text' => 'Visitor Status',
                    'url' => 'admin/visitor-log-status-view',
                    'icon' => 'legal',
                ],
                [
                    'text' => 'Screening Templates',
                    'url' => 'admin/visitor-log/screening-templates',
                    'icon' => 'microchip',
                ],

            ],
        ],
        [
            'text' => 'Contracts',
            'icon' => 'database',
            'can' => 'contractsadmin',
            'submenu' => [
                [
                    'text' => 'List Reason for submission',
                    'url' => 'admin/contracts/view-submission-reason',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'List Business Segment',
                    'url' => 'admin/contracts/view-business-segment',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'List Line of Business',
                    'url' => 'admin/contracts/view-business-line',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Billing Rate Changes',
                    'url' => 'admin/contracts/view-billing-rate-changes',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Billing Frequency',
                    'url' => 'admin/contracts/view-billing-cycle',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Payment Methods',
                    'url' => 'admin/contracts/view-payment-methods',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Device Access',
                    'url' => 'admin/contracts/view-device-access',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Office Address',
                    'url' => 'admin/contracts/view-office-address',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Cellphone Provider',
                    'url' => 'admin/contracts/view-cellphone-provider',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Division Lookup',
                    'url' => 'admin/contracts/view-division-lookup',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Holiday Payment',
                    'url' => 'admin/contracts/view-holiday-payment',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'RFP Tracking Process Step',
                    'url' => 'admin/rfp/process-step',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'RFP Response Type',
                    'url' => 'admin/rfp/response-type',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Post Order Topics',
                    'url' => 'admin/contracts/post-order-topics',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Post Order Groups',
                    'url' => 'admin/contracts/post-order-groups',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'RFP Catalog Group',
                    'url' => 'admin/rfp-catalogue/group',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Onboarding Template',
                    'url' => 'admin/client-onboarding/template',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Onboarding Settings',
                    'url' => 'admin/client-onboarding/settings',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Contract Expiry Settings',
                    'url' => 'admin/contract-expiry/settings',
                    'icon' => 'file-text',
                ],
            ],
        ],
        [
            'text' => 'Documents',
            'icon' => 'database',
            'can' => 'document_lookups',
            'submenu' => [

                [
                    'text' => 'Document Category',
                    'url' => 'admin/document-category',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Document Names',
                    'url' => 'admin/document-name',
                    'icon' => 'file-text',
                ],

                [
                    'text' => 'Other Category',
                    'url' => 'admin/other-document-category',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Other Subcategory',
                    'url' => 'admin/other-category',
                    'icon' => 'file-text',
                ],

            ],
        ],
        [
            'text' => 'Vehicle',
            'icon' => 'database',
            'can' => 'vehicle',
            'submenu' => [
                [
                    'text' => 'Vehicle Lists',
                    'url' => 'admin/vehicle-list',
                    'icon' => 'car',
                ],
                [
                    'text' => 'Maintenance Category',
                    'url' => 'admin/vehicle-maintenance-category',
                    'icon' => 'car',
                ],
                [
                    'text' => 'Maintenance Type',
                    'url' => 'admin/vehicle-maintenance-type',
                    'icon' => 'car',
                ],
                [
                    'text' => 'Vehicle Vendor',
                    'url' => 'admin/vehicle-vendor-lookup',
                    'icon' => 'car',
                ],

            ],
        ],
        [
            'text' => 'Expense',
            'icon' => 'database',
            'can' => 'expense_masters',
            'submenu' => [
                [
                    'text' => 'Tax Master',
                    'url' => 'admin/tax-master',
                    'icon' => 'money',
                ],

                /*  [
                'text' => 'Expense Parent Category',
                'url' => 'admin/expense-parent-category',
                'icon' => 'file-text',
                ],*/
                [
                    'text' => 'Mode of Payment',
                    'url' => 'admin/expense-payment-mode',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Expense Category',
                    'url' => 'admin/expense-category',
                    'icon' => 'file-text',
                ],

                [
                    'text' => 'GL Code',
                    'url' => 'admin/view-gl-code',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Mileage Reimbursement',
                    'url' => 'admin/mileage-reimbursement',
                    'icon' => 'file-text',
                ],

                [
                    'text' => 'Cost Center',
                    'url' => 'admin/cost-center',
                    'icon' => 'file-text',
                ],
                [
                    'text' => 'Expense Settings',
                    'url' => 'admin/expense-settings',
                    'icon' => 'file-text',
                ],
            ],
        ],
        [
            'text' => 'Project',
            'icon' => 'database',
            'can' => 'project_settings',
            'submenu' => [
                [
                    'text' => 'Interval Settings',
                    'url' => 'admin/interval',
                    'icon' => 'tasks',
                ],
                [
                    'text' => 'Rating Tolerance',
                    'url' => 'admin/rating-tolerance',
                    'icon' => 'cogs',
                ],
            ],
        ],
        [
            'text' => 'IDS Scheduling',
            'icon' => 'database',
            'can' => 'ids_admin',
            'submenu' => [
                [
                    'text' => 'Services',
                    'url' => 'admin/idsServices',
                    'icon' => 'money',
                ],
                [
                    'text' => 'Offices',
                    'url' => 'admin/idsOffice',
                    'icon' => 'globe',
                ],
                [
                    'text' => 'Location Allocation',
                    'url' => 'admin/location-allocation',
                    'icon' => 'list-alt',
                ],
                [
                    'text' => 'Payment Methods',
                    'url' => 'admin/payment-methods',
                    'icon' => 'money',
                ],
                [
                    'text' => 'Payment Reasons',
                    'url' => 'admin/payment-reasons',
                    'icon' => 'hourglass',
                ],
                [
                    'text' => 'Custom Question',
                    'url' => 'admin/custom-question',
                    'icon' => 'question-circle',
                ],
                [
                    'text' => 'Settings',
                    'url' => 'admin/ids-noshow-settings',
                    'icon' => 'cogs',
                ],
                [
                    'text' => 'Passport Photo',
                    'url' => 'admin/ids-passport-photos',
                    'icon' => 'camera',
                ],

            ],
        ],
        [
            'text' => 'Training',
            'icon' => 'database',
            'can' => 'training_learn_lookups',
            'submenu' => [
                [
                    'text' => 'Training Settings',
                    'url' => 'admin/training-settings',
                    'icon' => 'graduation-cap',
                ],
            ],
        ],
        [
            'text' => 'KPI Master',
            'icon' => 'database',
            'can' => 'view_kpi_admin',
            'submenu' => [
                [
                    'text' => 'Groups',
                    'url' => 'admin/kpi/groups/view',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Headers',
                    'url' => 'admin/kpi/headers',
                    'icon' => 'list',
                ],
                [
                    'text' => 'Dictionary',
                    'url' => 'admin/kpi/view',
                    'icon' => 'microchip',
                ],

                // [
                //     'text' => 'Employee Groups',
                //     'url' => 'admin/kpi/groups/allocation',
                //     'icon' => 'list',
                // ],
                [
                    'text' => 'Header KPI Allocation',
                    'url' => 'admin/kpi/headers-kpi/allocation',
                    'icon' => 'list',
                ],

            ],
        ],
        [
            'text' => 'OSGC',
            'icon' => 'database',
            'can' => 'osgc_lookups',
            'submenu' => [
                [
                    'text' => 'OSGC Courses',
                    'url' => 'admin/osgc-course',
                    'icon' => 'graduation-cap',
                ],
                [
                    'text' => 'Registered Users',
                    'url' => 'admin/osgc-users',
                    'icon' => 'male',
                ],
            ],
        ],
        [
            'text' => 'Uniform Scheduling',
            'icon' => 'database',
            'can' => 'uniform_scheduling_admin',
            'submenu' => [
                [
                    'text' => 'Offices',
                    'url' => 'admin/uniform-scheduling/offices',
                    'icon' => 'graduation-cap',
                ],
                [
                    'text' => 'Custom Question',
                    'url' => 'admin/uniform-scheduling/custom-question',
                    'icon' => 'question-circle',
                ],
            ],
        ],
        [
            'text' => 'Sensors',
            'icon' => 'database',
            'can' => 'sensors_admin',
            'submenu' => [
                [
                    'text' => 'Sensor',
                    'url' => 'admin/sensors/view',
                    'icon' => 'microchip',
                ],
                [
                    'text' => 'Settings',
                    'url' => 'admin/sensors/settings',
                    'icon' => 'cog',
                ],

            ],
        ],
        [
            'text' => 'IP Camera',
            'icon' => 'database',
            'can' => 'view_ipcamera',
            'submenu' => [
                [
                    'text' => 'IP Camera',
                    'url' => 'admin/ip-camera/view',
                    'icon' => 'microchip',
                ],
                [
                    'text' => 'Dashboard Configuration',
                    'url' => 'admin/ip-camera/ip-camera-dashboard-index',
                    'icon' => 'list',
                ]
            ],
        ],
        [
            'text' => 'Content Manager',
            'icon' => 'database',
            'can' => 'content_manager_settings',
            'submenu' => [
                [
                    'text' => 'Add Content',
                    'url' => 'admin/content-manager/view',
                    'icon' => 'list-alt',
                ],
            ],
        ],
        [
            'text' => 'Employee Survey',
            'icon' => 'database',
            'can' => 'employee_survey_admin',
            'submenu' => [
                [
                    'text' => 'Template',
                    'url' => 'admin/employee-survey-template',
                    'icon' => 'microchip',
                ],

            ],
        ],
        [
            'text' => 'Departments',
            'icon' => 'database',
            'can' => 'department_master',
            'submenu' => [
                [
                    'text' => 'Department Master',
                    'url' => 'admin/department-master',
                    'icon' => 'graduation-cap',
                ],

            ],
        ],
        [
            'text' => 'Uniform',
            'icon' => 'database',
            'can' => 'uniform_settings',
            'submenu' => [
                [
                    'text' => 'Uniform Products',
                    'url' => 'admin/uniform-products',
                    'icon' => 'microchip',
                ],
                [
                    'text' => 'URA Rates',
                    'url' => 'admin/ura-rates',
                    'icon' => 'microchip',
                ],
                [
                    'text' => 'URA Settings',
                    'url' => 'admin/ura-settings',
                    'icon' => 'microchip',
                ],
            ],
        ],
        'SETTINGS',
        [
            'text' => 'Email',
            'icon' => 'envelope',
            'can' => 'settings-email',
            'submenu' => [
                [
                    'text' => 'Settings',
                    'url' => 'admin/settings/mail',
                    'icon' => 'money',
                    'can' => 'settings-email',
                ],
                [
                    'text' => 'Email Template',
                    'url' => 'admin/email-template',
                    'icon' => 'envelope',

                ],

                // [
                //     'text' => 'Customer Email Allocation',
                //     'url' => 'admin/email-template-allocation/data',
                //     'icon' => 'envelope',
                // ],
                [
                    'text' => 'Customer Email Allocation',
                    'url' => 'admin/email-template-allocation/list',
                    'icon' => 'envelope',
                ],
            ],

        ],
        [
            'text' => 'Roles & Permissions',
            'url' => 'admin/role',
            'icon' => 'users',
            'can' => 'manage-roles-permissions',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.2/b-2.0.0/b-colvis-2.0.0/b-html5-2.0.0/b-print-2.0.0/datatables.min.js',
                ],
             /*   [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],*/
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.2/b-2.0.0/b-colvis-2.0.0/b-html5-2.0.0/b-print-2.0.0/datatables.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
