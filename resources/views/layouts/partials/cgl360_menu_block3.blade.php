<li attr-block="3" class="pli">

            <a href="#" attr-block="3"  class="mainhead mhead">
                <div attr-block="3" class="plihidden ">O </div>
                <span attr-block="3" >Operations </span>
            </a>
        </li>
        @canany(['view-stc-customer','view-all-stc-customer','view-stc-geo-mapping',
        'candidate-schedule','candidate-schedule-summary','view_openshift','create-stc-customer',
        'list-stc-customers','manage_bonus_settings','view_stc_schedule_summary','view_all_stc_schedule_summary',
        'view_supervisorpanel','view_operational_dashboard','view_guard_tour','view_all_guard_tour','view_shift_journal','view_all_shift_journal','view_allocated_shift_journal',
                'view_all_shift_module_mapping','view_allocated_shift_module_mapping','view_all_incident_report','view_allocated_incident_report',
                'create_timeoff','approve_timeoff','view_employee_summary_all','view_employee_summary_allocated'])

        <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Documents " class="iconclass  fa fa-globe fa-fw" aria-hidden="true"></i>
                <span class="multilinespan">Site Management </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">


                @canany(['view-stc-customer','view-all-stc-customer','view-stc-geo-mapping',
                'candidate-schedule','candidate-schedule-summary','view_openshift','create-stc-customer',
                'list-stc-customers','manage_bonus_settings','view_stc_schedule_summary','view_all_stc_schedule_summary'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Short Term Contracts
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">
                        @canany(['view-stc-customer','view-all-stc-customer'])
                        <li>
                            <a href="{{ route('customers.mapping',["stc"=>"stc"]) }}"> STC Client Geomapping
                            </a>
                        </li>
                        @endcan
                        @can('view-stc-geo-mapping')
                        <li>
                            <a href="{{ route('stc-schedule.geo-mapping') }}"> STC Geomapping</a>
                        </li>
                        @endcan
                        @can('candidate-schedule')
                        <li>
                            <a href="{{ route('candidate.schedule') }}">Candidate Schedule</a>
                        </li>
                        @endcan
                        @can('candidate-schedule-summary')
                        <li><a href="{{ route('stc.summary') }}">Schedule Summary</a></li>
                         @endcan
                        @can('view_openshift')
                        <li><a href="{{ route('openshift') }}">Open Shift Approval</a></li>
                         @endcan
                        @can('create-stc-customer')
                        <li><a href="{{ route('stc.create') }}">Create STC</a></li>
                        @endcan
                        @can('list-stc-customers')
                        <li><a href="{{ route('stc') }}">View STC</a></li>
                        @endcan
                        @can('manage_bonus_settings')
                        <li><a href="{{ route('stc.bonuslist') }}">Bonus Programs</a></li>
                        @endcan
                        @canany(['view_stc_schedule_summary','view_all_stc_schedule_summary'])
                        <li><a href="{{ route('stc.employee-summary') }}">View Employee List</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                @canany([
                'view_supervisorpanel','view_operational_dashboard','view_guard_tour','view_all_guard_tour','view_shift_journal','view_all_shift_journal','view_allocated_shift_journal',
                'view_all_shift_module_mapping','view_allocated_shift_module_mapping','view_all_incident_report','view_allocated_incident_report',
                'create_timeoff','approve_timeoff','view_employee_summary_all','view_employee_summary_allocated'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Permanent Contracts
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('customers.mapping') }}"> Site Status Dashboard </a>
                        </li>

                        @can('view_operational_dashboard')
                        <li>
                            <a href="{{ route('operational-dashboard') }}"> Operational Dashboard </a>
                        </li>
                        @endcan

                        @canany(['view_guard_tour','view_all_guard_tour','view_shift_journal','view_all_shift_journal','view_allocated_shift_journal'])
                        <li>
                            <a href="{{ route('customers.mappingGuardTour',["guard_tour"=>"guard_tour"]) }}">Guard Tour / Shift Journal</a>
                        </li>

                        @endcan

                        @canany(['view_all_shift_module_mapping','view_allocated_shift_module_mapping'])
                        <li>
                            <a href="{{ route('shiftmodule.mapping') }}"> Activity Mapping </a>
                        </li>
                        @endcan

                        @canany(['view_all_incident_report','view_allocated_incident_report'])
                        <li>
                            <a href="{{ route('incident.dashboard') }}"> Incident Updates </a>
                        </li>
                        @endcan
                        {{-- @can('create_timeoff')
                         <li><a href="{{ route('timeoff.timeoffRequesForm') }}">Time Off Request </a></li>
                         @endcan
                         @can('approve_timeoff')
                            <li><a href="{{ route('timeoff.index') }}">Time Off Request Approval</a></li>
                        @endcan
                        @canany(['view_employee_summary_all','view_employee_summary_allocated'])
                         <li><a href="{{ route('timeoffstatus.index') }}">Time Off Request Summary </a></li>
                        @endcan --}}
                    </ul>
                </li>

                @endcanany



                {{-- <!-- Start new drop down menu adding for facility management dashboard-->
                        @can('view_fmdashboard')
                        <li class="dropdown-submenu">
                            <a href="{{route('facility-management-dashboard.index')}}">FM Dashboard</a>
                        </li>
                        @endcan
                <!-- End of new drop down menu adding for facility management dashboard-->
                @can('view_monitor_dashboard')
                <li class="dropdown-submenu">
                            <a href="{{route('monitor-dashboard')}}">Monitor Dashboard</a>
               </li>
               @endcan --}}
            </ul>
        </li>
        @endcanany
        @canany(['view_timesheet_approval', 'view_manual_timesheet_entry','view_timesheet_by_employee','view_timesheet_detail_view',
        'view_allocation_report','view_employee_summary','view_notification', 'view_manual_timesheet_report','view_all_qrcode_data','view_allocated_qrcode_data',
        'view_all_customer_qrcode_summary','view_allocated_customer_qrcode_summary'
        ])

        <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Time Management " class="iconclass fa fa-clock fa-fw" aria-hidden="true"></i>
                <span class="multilinespan">Time Management </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @canany(['view_timesheet_approval', 'view_manual_timesheet_entry'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Timesheet Entry
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">
                        @can('view_timesheet_approval')
                        <li>
                            <a href="{{ route('approval.timesheet') }}"> Timesheet Approval </a>
                        </li>
                        @endcan
                        @can('view_manual_timesheet_entry')
                        <li>
                            <a href="{{ route('timetracker.manualtimesheetentry') }}"> Manual Timesheet Entry</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @canany(['view_timesheet_by_employee','view_timesheet_detail_view',
                'view_allocation_report','view_employee_summary','view_notification', 'view_manual_timesheet_report'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Timesheet Report
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">
                        @can('view_timesheet_by_employee')
                        <li>
                            <a href="{{ route('timetracker.timesheet') }}"> Timesheet By Employee </a>
                        </li>
                        @endcan
                        @can('view_timesheet_detail_view')
                        <li>
                            <a href="{{ route('timetracker.timesheet-detail') }}"> Timesheet Detail View </a>
                        </li>
                        @endcan
                        @can('view_allocation_report')
                        <li>
                            <a href="{{ route('timetracker.allocation') }}"> Allocation Report </a>
                        </li>
                        @endcan
                        @can('view_employee_summary')
                        <li>
                            <a class="li-lv3" href="{{ route('timetracker.employee-summary') }}"> Employee Summary </a>
                        </li>
                        @endcan
                        {{-- @can('view_notification')
                        <li>
                            <a href="{{ route('notification.index') }}"> Notification </a>
                        </li>
                        @endcan --}}
                        @can('view_manual_timesheet_report')
                        <li>
                            <a href="{{ route('timetracker.manualtimesheetreport') }}"> Manual Timesheet Report </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                  @canany(['view_all_qrcode_data','view_allocated_qrcode_data'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('qrcodepatrol.list') }}">QR Patrol</a>
                </li>

                @endcan
                @canany(['view_all_customer_qrcode_summary','view_allocated_customer_qrcode_summary'])
                <li>
                    <a tabindex="-1" href="{{ route('customerqrcodeshift.summary') }}">QR Patrol Summary</a>
                </li>
                @endcan
            </ul>
        </li>
        @endcanany
        @canany(['create_schedule_allocated_customer','create_schedule_all_customer', 'view_all_employee_schedule_requests', 'view_allocated_employee_schedule_requests','view_reports_employee_schedules'])

        <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Documents " class="iconclass fa fa-calendar fa-fw" aria-hidden="true"></i>
                <span class="multilinespan">Schedule Management </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @canany(['create_schedule_allocated_customer','create_schedule_all_customer'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('scheduling.create') }}">Create </a>
                </li>
                @endcan

                @canany(['view_all_employee_schedule_requests', 'view_allocated_employee_schedule_requests'])
                <li class="dropdown-submenu">
                    @php
                    $schedule_approval_label = 'View Requests';
                    @endphp
                    @canany(['approve_all_employee_schedule_requests','approve_allocated_employee_schedule_requests'])
                    @php
                    $schedule_approval_label = 'View & Approve Requests';
                    @endphp
                    @endcan
                    <a tabindex="-1" href="{{ route('scheduling.approval-page') }}">{{$schedule_approval_label}}</a>
                </li>
                @endcan
                @can(['employee_schedule_inherit'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('inherit-schedule.index') }}">Inherit Schedule </a>
                </li>
                @endcan
                @can('view_reports_employee_schedules')
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Reports
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">

                        <li>
                            <a href="{{ route('scheduling.schedulegeneralreport') }}"> Scheduling - General
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scheduling.schedulepayperiodreport') }}"> Scheduling - Payperiod
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scheduling.scheduleaudit') }}"> Scheduling - Audit
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scheduling.report-non-compliance') }}"> Schedule Non-Compliance Report
                            </a>
                        </li>

                    </ul>
                </li>
                @endcan
            </ul>
        </li>
        @endcanany
        @canany(['ids_view_all_schedule','ids_view_allocated_locaion_schedule',
        'ids_refund_list','ids_refund_update_status','ids_view_report'])
            <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="IDS Schedule Management " class="iconclass fa fa-calendar fa-fw" aria-hidden="true"></i>
                <span class="multilinespan">IDS Schedule Management </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @canany(['ids_view_all_schedule','ids_view_allocated_locaion_schedule'])
                    <li class="dropdown-submenu">
                        <a href="{{ route('idsscheduling-admin') }}"> View Schedule</a>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="{{ route('idsscheduling-calendar-admin') }}"> Calendar View</a>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="{{ route('idsscheduling-admin.cancelled-schedule') }}"> Cancelled Schedule</a>
                    </li>
                @endcan
                @canany(['ids_refund_list','ids_refund_update_status'])
                    <li class="dropdown-submenu">
                        <a href="{{ route('idsscheduling-admin.refund') }}">Refund List</a>
                    </li>
                @endcan
                @canany(['ids_view_report'])

                    <li class="dropdown-submenu">
                        <a class="test" tabindex="-1" href="#">Reports</a>
                        <ul class="dropdown-menu">
                            <li>
                                <a tabindex="-1" href="{{ route('idsscheduling-admin.report') }}">IDS Forecast</a>
                            </li>
                            <li>
                                <a tabindex="-1" href="{{ route('idsscheduling-admin.analytics') }}">IDS Analytics </a>
                            </li>
                            <li>
                                <a tabindex="-1" href="{{ route('idsscheduling-admin.trends') }}">IDS Trends</a>
                            </li>
                            <li>
                                <a tabindex="-1" href="{{ route('idsscheduling-admin.revenue') }}">IDS Revenue </a>
                            </li>
                            <li>
                                <a tabindex="-1" href="{{ route('idsscheduling-admin.office-revenue') }}">Office Revenue </a>
                            </li>
                            <li>
                                <a tabindex="-1" href="{{ route('idsscheduling-admin.photo-revenue') }}">Photo Revenue</a>
                            </li>
                            <li>
                                <a tabindex="-1" href="{{ route('idsscheduling-admin.geomap') }}">IDS Geomap </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                </ul>
        
        </li>
        @endcanany
        @canany(['view_all_customer_facility','view_allocated_customer_facility',
        'manage_all_facility_users','manage_allocated_facility_users',
        'manage_user_allocation','view_facilityscheduleview'])
        <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Documents " class="iconclass fas fa-building" aria-hidden="true"></i>
                <span class="singlelinespan">Facility Signout </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @canany(['view_all_customer_facility','view_allocated_customer_facility'])
                    <li class="dropdown-submenu">
                        <a href="{{ route('cbs.facilities') }}"> Facility </a>
                    </li>
                @endcanany
                @canany(['manage_all_facility_users','manage_allocated_facility_users'])
                    <li class="dropdown-submenu">
                        <a href="{{ route('cbs.facilityusers') }}"> Facility User Management </a>
                    </li>
                @endcanany
                @canany(['manage_user_allocation'])
                    <li class="dropdown-submenu">
                        <a href="{{ route('cbs.facilityuserallocations') }}"> Facility User Allocation </a>
                    </li>
                @endcanany
                @canany(['view_facilityscheduleview'])
                    <li class="dropdown-submenu">
                        <a href="{{ route('cbs.booking-page') }}"> Scheduled View  </a>
                    </li>
                @endcanany
            </ul>
        </li>
        @endcanany

        @canany(['view_guard_tour','view_all_guard_tour','view_shift_journal','view_all_shift_journal','view_allocated_shift_journal'])
        <li class="block3 accordclass">
            <a href="{{ route('customers.mappingGuardTour',["guard_tour"=>"guard_tour"]) }}" data-toggle=" aria-expanded="false" class="dropdown-toggle">
                <i title="Documents " class="iconclass fa fa-car fa-fw" aria-hidden="true"></i>
                <span class="multilinespan">Guard Tour Management </span>
            </a>
        </li>
        @endcanany
        @canany(['view_all_live_location','view_allocated_live_location','view_all_mobile_security_patrol',
        'view_allocated_mobile_security_patrol','view_all_mobile_security_patrol_trips','view_allocated_mobile_security_patrol_trips',
        'view_all_satellite_tracking','view_allocated_satellite_tracking','view_dispatch_request_mst'])
        <li class="block3 accordclass">
            <a href="{{ route('qrcodepatrol.list') }}" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Documents " class="iconclass fas fa-user-secret" aria-hidden="true"></i>
                <span class="singlelinespan">Mobile Patrol </span>
            </a>
            <ul class="dropdown-menu menu-list mst-side-menu  transform-class" role="menu">
                @canany(['view_all_live_location','view_allocated_live_location'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('timetracker.shift-live-locations') }}">Employee Live Location</a>
                </li>
                @endcan
                @canany(['view_all_mobile_security_patrol_trips','view_allocated_mobile_security_patrol_trips'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('mobilesecuritypatrol.list') }}">Mobile Patrol Trips</a>

                </li>
                @endcan
                @canany(['view_all_mobile_security_patrol','view_allocated_mobile_security_patrol'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('mobilepatrol') }}">Mobile Security Patrol</a>

                </li>
                @endcan

                <!-- todo:change permission -->
                @canany(['view_all_satellite_tracking','view_allocated_satellite_tracking'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('msp.geofence.view') }}">Satellite Tracking</a>

                </li>
                @endcan
                @canany(['view_all_satellite_tracking','view_allocated_satellite_tracking'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('msp.geofence.dashboard.satellite-tracking') }}">Satellite Tracking Dashboard</a>
                </li>
                @endcan
                @can('view_mst_dashboard')
                <li>
                    <a href="{{ route('mst_dispatch.dashboard') }}"> MST-Dashboard</a>
                </li>
                @endcan
                @can('view_dispatch_request_mst')
                <li>
                    <a href="{{ route('dispatchrequest.index') }}"> Dispatch Request </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcanany
        @canany(['view_post_order','create_post_order','view_allocated_post_order','create_allocated_post_order'])
        <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Post Order Management " class="iconclass fa fa-tasks fa-fw" aria-hidden="true"></i>
                <span class="multilinespan">Post Order Management </span>
            </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('post-order.view') }}"> View </a>
                        </li>
                        @canany(['create_post_order','create_allocated_post_order'])
                        <li>
                            <a href="{{ route('post-order.create.view') }}"> Create </a>
                        </li>
                        @endcan
                    </ul>
        </li>
        @endcan
        @canany(['view_all_customers_in_visitor_screening','view_allocated_customers_in_visitor_screening'])
        <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Visitor Management " class="iconclass fa fa-user-plus" aria-hidden="true"></i>
                <span class="multilinespan">Visitor Management </span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('client-visitor') }}">Visitor List</a>
                </li>
                    @canany(['view_all_customers_in_visitor_screening','view_allocated_customers_in_visitor_screening'])
                <li>
                    <a href="{{ route('client-visitor.screening-submission') }}">Visitor Screening</a>
                </li>
                    @endcan
            </ul>
        </li>
        @endcanany
        @canany(['view_allocated_customers_keys','view_all_customers_keys','view_all_keylog_summary','view_allocated_keylog_summary'])
        <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Key Management " class="iconclass fa fa-key fa-fw" aria-hidden="true"></i>
                <span class="multilinespan">Key Management </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @canany(['view_all_customers_keys','view_allocated_customers_keys'])
                    <li class="dropdown-submenu">
                        <a href="{{ route('key-setting') }}">Keys</a>
                    </li>
                @endcan
                @canany(['view_all_keylog_summary','view_allocated_keylog_summary'])
                    <li class="dropdown-submenu">
                        <a href="{{ route('keysetting.keylog') }}">Key Log Summary</a>
                    </li>
                @endcan
                </ul>
        </li>
        @endcan
        @canany(['add_client_feedback','review_client_feedback','view_client_feedback','view_all_client_feedback','add_client_concern','review_client_concern','view_client_concern','view_all_client_concern',
        'view_allocated_clientsurvey','view_all_clientsurvey','view_all_customers_in_visitor_screening','view_allocated_customers_in_visitor_screening'])
        <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Client Management " class="iconclass fa fa-smile fa-fw" aria-hidden="true"></i>
                <span class="multilinespan">Client Management</span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @canany(['add_client_feedback','review_client_feedback','view_client_feedback','view_all_client_feedback'])
                <li class="dropdown-submenu">
                    <a href="{{ route('client.employee-rating') }}">Client Feedback</a>
                </li>
                @endcan
                @canany(['add_client_concern','review_client_concern','view_client_concern','view_all_client_concern'])
                <li class="dropdown-submenu">
                    <a href="{{ route('client.concern') }}">Client Concern</a>
                </li>
                @endcan
                @canany(['view_allocated_clientsurvey','view_all_clientsurvey'])
                        <li  class="dropdown-submenu">
                            <a href="{{ route('clientsurvey.index') }}"> Client Survey </a>
                        </li>
                @endcanany
                @canany(['view_all_customers_in_visitor_screening','view_allocated_customers_in_visitor_screening'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">
                        Visitors
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('client-visitor') }}">Visitor List</a>
                        </li>
                            @canany(['view_all_customers_in_visitor_screening','view_allocated_customers_in_visitor_screening'])
                        <li>
                            <a href="{{ route('client-visitor.screening-submission') }}">Visitor Screening</a>
                        </li>
                            @endcan
                    </ul>
                </li>
                @endcanany

            </ul>
        </li>
        @endcanany
        @canany(['view_video_post_summary'])
        <li class="block3 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Video Post " class="iconclass fa fa-video fa-fw" aria-hidden="true"></i>
                <span class="multilinespan">Video Post</span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @can(['view_video_post_summary'])
                    <li>
                        <a href="{{ route('videopost.summary') }}">View Video Post</a>
                    </li>
                @endcan
            </ul>
        </li>
        @endcanany

        {{-- <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Documents " class="fa fa-file fa-fw" aria-hidden="true"></i>
                <span class="">Fingerprinting </span>
            </a>
        </li> --}}
