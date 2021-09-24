<?php
define('TEMP_FILE', 0);
define('PERM_FILE', 1);

define('ACTIVE', 1);
define('INACTIVE', 0);

define('STC_CUSTOMER', 1);
define('PERMANENT_CUSTOMER', 0);
define('ALL_CUSTOMER', null);

define('SURVEY_DEFAULT_SCORE', -1);

define('PAST_PAYPERIOD', 0);
define('FUTURE_PAYPERIOD', 1);
define('PAST_FUTURE_PAYPERIOD', 2);

define('RESIGNATION', 1);
define('TERMINATION', 2);

define('EMPLOYEE', 1);
define('CLIENT', 2);
define('OTHER', 3);

define('CHECKEDIN', 1);
define('CHECKEDOUT', 0);

define('LEAVE_OF_ABSENCE', 1);
define('VACATION', 2);

define('AVAILABLE', 1);
define('MEETING', 2);
define('UNAVAILABLE', 3);
//MST default idle meeter distance
define('IDLE', 20);
define('SHIFT_TYPE_REGULER', 1);
define('SHIFT_TYPE_MSP', 2);

define('SHIFT_TYPE_REGULER_ARRAY', [1]);
define('SHIFT_TYPE_MSP_ARRAY', [2]);
define('ALL_SHIFT_TYPES', [1, 2]);
define('DISPATCH_PROGRESS_STATUS', [2, 3]);

define('VISITOR_LOG_TYPE', 1);
define('PUSH_MST', 1);
define('PUSH_EMPLOYEE_RATING', 2);
define('PUSH_EMPLOYEE_SURVEY', 3);
define('PUSH_MOTION_DETECTION', 4);
define('PUSH_CGL_MEET', 5);
define('PUSH_TIMESHEET_APPROVAL_RATING', 6);
define('HIGH_PRIORITY', 3);
define('MEDIUM_PRIORITY', 2);
define('LOW_PRIORITY', 1);

define('MOBILE', 1);
define('WEB', 2);

define('BLASTMAIL_START', 10);
define('BLASTMAIL_LOOP', 1);

define('REC_MATCHSCORE_SPEED', 65);

return [

    'temp_file' => TEMP_FILE,
    'perm_file' => PERM_FILE,
    'active' => ACTIVE,
    'inactive' => INACTIVE,
    'stc_customer' => STC_CUSTOMER,
    'permanent_customer' => PERMANENT_CUSTOMER,
    'all_customer' => ALL_CUSTOMER,
    'survey_default_score' => SURVEY_DEFAULT_SCORE,
    'past_payperiod' => PAST_PAYPERIOD,
    'future_payperiod' => FUTURE_PAYPERIOD,
    'past_future_payperiod' => PAST_FUTURE_PAYPERIOD,
    'resignation' => RESIGNATION,
    'termination' => TERMINATION,
    'employee' => EMPLOYEE,
    'client' => CLIENT,
    'other' => OTHER,
    'checkedin' => CHECKEDIN,
    'checkedout' => CHECKEDOUT,
    'Leave_of_Absence' => LEAVE_OF_ABSENCE,
    'Vacation' => VACATION,
    'available' => AVAILABLE,
    'meeting' => MEETING,
    'unavailable' => UNAVAILABLE,
    'idle' => IDLE,
    'shift_type_reguler' => SHIFT_TYPE_REGULER,
    'shift_type_msp' => SHIFT_TYPE_MSP,

    'blastmail_start' => BLASTMAIL_START,
    'blastmail_loop' => BLASTMAIL_LOOP,
    'rec_matchscore_speed' => REC_MATCHSCORE_SPEED,

    'shift_type_reguler_array' => SHIFT_TYPE_REGULER_ARRAY,
    'shift_type_msp_array' => SHIFT_TYPE_MSP_ARRAY,
    'all_shift_types' => ALL_SHIFT_TYPES,
    'dispatch_progress_status' => DISPATCH_PROGRESS_STATUS,
    'visitor_log_type' => VISITOR_LOG_TYPE,
    'push_mst' => PUSH_MST,
    'push_employee_rating' => PUSH_EMPLOYEE_RATING,
    'push_timesheet_approval_rating' => PUSH_TIMESHEET_APPROVAL_RATING,

    'high_priority' => HIGH_PRIORITY,
    'medium_priority' => MEDIUM_PRIORITY,
    'low_priority' => LOW_PRIORITY,
    'mobile' => MOBILE,
    'web' => WEB,

];
