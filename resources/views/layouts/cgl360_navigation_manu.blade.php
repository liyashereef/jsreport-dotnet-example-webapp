<style>
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu .dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -165px;
        margin-left: -27px;
    }

    .dropdown-position {
        /*position: relative !important;*/
        left: 77% !important;
        border-bottom: orange 1px solid;
        border-right: solid orange 1px;
        top: -10px;
        width: 110%;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #f26321;
    }

</style>

<script>
    $(document).ready(function() {
        $('.dropdown-submenu a.test').on("click", function(e) {
            $('.dropdown-submenu .dropdown-menu').hide();
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#sidebar').find('.dropdown-menu').toggleClass('resp');
            $('.fa-caret-down').toggleClass('carat');
        });
        @if(!in_array(\Request::route()->getName(), [null, 'home'])) $('#sidebarCollapse').trigger('click');
        @endif
    });
</script>
<nav id="sidebar">
    <ul class="list-unstyled components">
        <li>
            <a id="sidebarCollapse" class="sidebarCollapseEl">
                <img class ="sidebarToggleImg" src="{{ asset('images/handburger.png') }}">
            </a>
        </li>
        @canany(['add_client_document','view_client_document','add_allocated_client_document','view_allocated_client_document','add_employee_document','view_employee_document','add_allocated_employee_document','view_allocated_employee_document','add_other_document','view_other_document'])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Documents " class="fa fa-file fa-fw" aria-hidden="true"></i>
                <span>Documents </span>
            </a>

            <ul class="dropdown-menu menu-list" role="menu">
                @canany(['add_client_document','view_client_document','add_allocated_client_document','view_allocated_client_document'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('documents.client-document') }}">Client</a>

                </li>
                @endcan
                @canany(['add_employee_document','view_employee_document','add_allocated_employee_document','view_allocated_employee_document'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('documents.employee-document') }}">Employee</a>

                </li>
                @endcan
                @canany(['add_other_document','view_other_document'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Others</a>
                    <?php $other_documents = Modules\Admin\Models\OtherCategoryLookup::get();?>
                    <ul class="dropdown-menu">
                        @foreach($other_documents as $doc)
                        <li>
                            <a tabindex="-1" href="{{ route('documents.other-vendor',['id'=>$doc['id']]) }}">{{$doc['category_name']}}</a>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @endcan
            </ul>
        </li>
        @endcan



        @canany(['create-job', 'edit-job', 'delete-job', 'archive-job', 'job-approval', 'hr-tracking', 'job-attachement-settings', 'list-jobs-from-all', 'job-tracking-summary', 'view_recruitinganalyticswidgets', 'view_admin', 'candidate-screening-summary', 'candidate-mapping', 'candidate-tracking-summary', 'employee-mapping-rating', 'supervisor-mapping-rating','candidate_transition_process','view_all_whistleblower','create_employee_whistleblower','create_all_whistleblower','create_allocated_whistleblower','view_employee_whistleblower','view_allocated_whistleblower','create_exit_interview','create_all_exit_interview','view_all_exit_interview','view_exit_interview','view_all_candidates','view_all_candidates_candidate_geomapping','view_all_employee_surveys','view_allocated_employee_surveys','view_allocated_sites_in_employeefeedback','view_all_sites_in_employeefeedback','view_transaction_department_allocation'])
        <li>
            <a href="homeSubmenu" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav1.png">-->
                <i title="Recruiting " class="fa fa-university fa-fw" aria-hidden="true"></i>
                <span>Recruiting </span>
            </a>
            <ul class="dropdown-menu first-menu-list" role="menu" aria-labelledby="menu1">
                @canany(['create-job', 'edit-job', 'delete-job', 'archive-job', 'job-approval', 'hr-tracking', 'job-attachement-settings', 'list-jobs-from-all', 'job-tracking-summary', 'view_recruitinganalyticswidgets'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Job Postings
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">
                        @can('create-job')
                        <li>
                            <a tabindex="-1" href="{{ route('job.create') }}">New Job Request</a>
                        </li>
                        @endcan
                        @canany(['create-job','edit-job','delete-job','archive-job','job-approval','hr-tracking','job-attachement-settings','list-jobs-from-all'])
                        <li>
                            <a tabindex="-1" href="{{ route('job') }}">Summary Requisitions</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="{{ route('job.mapping') }}">Job Post Geomapping</a>
                        </li>
                        @endcan
                        @can('job-tracking-summary')
                        <li>
                            <a tabindex="-1" href="{{ route('job.hr-tracking-summary') }}">Job Ticket Status</a>
                        </li>
                        @endcan
                        @can('view_recruitinganalyticswidgets')
                        <li><a tabindex="-1" href="{{ route('recruitment-analytics.index') }}">Analytics Dashboard</a></li>
                        @endcan

                    </ul>
                </li>
                @endcan
                @canany(['view_admin','candidate-screening-summary','candidate-mapping','candidate-tracking-summary','candidate_transition_process','view_all_candidates'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Candidates
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">
                        @can('view_admin')
                        <!-- based on sam's mail -->
                        <li>
                            <a tabindex="-1" target="_blank" href="{{ route('applyjob') }}">Candidate Entry</a>
                        </li>
                        @endcan
                        @can('candidate-screening-summary')
                        <li>
                            <a tabindex="-1" href="{{ route('candidate') }}">Candidate Summary</a>
                        </li>
                        @endcan
                        @can('candidate-mapping')
                        <li>
                            <a tabindex="-1" href="{{ route('candidate.mapping') }}">Candidate Geomapping</a>
                        </li>
                        @endcan
                        @can('candidate-tracking-summary')
                        <li>
                            <a tabindex="-1" href="{{ route('candidate.summary') }}">Candidates Onboarding Status</a>
                        </li>
                        @endcan
                        @can('candidate_transition_process')
                        <li>
                            <a tabindex="-1" href="{{ route('candidate.conversion') }}">Candidates Conversion</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @canany(['employee-mapping-rating','create_exit_interview','create_all_exit_interview','view_all_exit_interview','view_exit_interview','view_employee_whistleblower','view_all_whistleblower','create_all_whistleblower','create_employee_whistleblower','create_allocated_whistleblower','view_allocated_whistleblower','view_all_employee_availability','view_allocated_employee_availability','update_all_employee_availability','update_allocated_employee_availability','view_all_employee_unavailability','view_allocated_employee_unavailability','update_delete_all_employee_unavailability','update_delete_allocated_employee_unavailability','view_employee_timeoff_requests','view_all_employee_surveys','view_allocated_employee_surveys','view_allocated_sites_in_employeefeedback','view_all_sites_in_employeefeedback','view_transaction_department_allocation'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Employees
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">
                        @can('employee-mapping-rating')
                        <li>
                            <a href="{{ route('employee.mapping') }}">Employee Geomapping</a>
                        </li>
                        @endcan
                        @canany(['view_employee_whistleblower','view_all_whistleblower','create_employee_whistleblower','create_all_whistleblower','create_allocated_whistleblower','view_allocated_whistleblower'])
                        <li>
                            <a href="{{ route('employee.whistleblower') }}">Employee Whistleblower</a>
                        </li>
                        @endcan
                        @canany(['create_exit_interview','create_all_exit_interview','view_all_exit_interview','view_exit_interview'])
                        <li>
                            <a href="{{ route('employee.exitterminationsummary') }}">Employee Exit Interview</a>
                        </li>
                        @endcan
                        @canany(['view_all_employee_availability','view_allocated_employee_availability','update_all_employee_availability','update_allocated_employee_availability','view_all_employee_unavailability','view_allocated_employee_unavailability','update_delete_all_employee_unavailability','update_delete_allocated_employee_unavailability'])
                       <li class="dropdown-submenu">
                       <a tabindex="-1" href="{{ route('employee.scheduleEntry') }}">Employee Availability - Entry</a>
                      </li>
                      @endcan
                    @can('view_employee_timeoff_requests')
                        <li><a href="{{ route('timeoff.index') }}">Time Off Request Approval</a></li>
                    @endcan
                    @canany(['view_all_employee_surveys','view_allocated_employee_surveys'])
                        <li><a href="{{ route('employee.employeeSurveys') }}">Employee Survey</a></li>
                    @endcanany
                    @canany(['view_allocated_sites_in_employeefeedback','view_all_sites_in_employeefeedback','view_transaction_department_allocation'])
                        <li><a href="{{ route('employee.employeeFeedback') }}">Employee Feedback</a></li>
                    @endcanany

                    </ul>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

         @canany(['rec-create-job', 'rec-edit-job', 'rec-archive-job', 'rec-job-approval', 'rec-hr-tracking', 'rec-job-attachement-settings', 'rec-list-jobs-from-all', 'rec-job-tracking-summary', 'rec-candidate-screening-summary','rec_candidate_transition_process', 'rec-candidate-mapping','rec-candidate-credential', 'rec-candidate-tracking-summary','rec-candidate-credential','rec-create-candidate-credential','rec-edit-candidate-credential','rec-delete-candidate-credential','rec-view-allocated-job-requisitions','rec-view-allocated-candidates-summary','rec-view-allocated-candidates-geomapping','rec-view-allocated-candidates-tracking'])
        <li>

            <a href="homeSubmenu" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <i title="Recruiting " class="fa fa-university fa-fw" aria-hidden="true"></i>
                <span>Recruit Revamp </span>
            </a>
            <ul class="dropdown-menu first-menu-list" role="menu" aria-labelledby="menu1">
              @canany(['rec-create-job', 'rec-edit-job', 'rec-archive-job', 'rec-job-approval', 'rec-hr-tracking', 'rec-job-attachement-settings', 'rec-list-jobs-from-all', 'rec-job-tracking-summary','rec-view-allocated-job-requisitions','rec-view-allocated-candidates-geomapping'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Job Postings
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">
                       @can('rec-create-job')

                        <li>
                            <a tabindex="-1" href="{{ route('recruitment-job.create') }}">New Job Request</a>
                        </li>
                        @endcan
                         @canany(['rec-create-job','rec-edit-job','rec-archive-job','rec-job-approval','rec-hr-tracking','rec-job-attachement-settings','rec-list-jobs-from-all','rec-job-tracking-summary','rec-view-allocated-job-requisitions','rec-view-allocated-candidates-geomapping'])

                      <li>
                            <a tabindex="-1" href="{{ route('recruitment-job') }}">Summary Requisitions</a>
                        </li>
                          <li>
                            <a tabindex="-1" href="{{ route('recruitment-job.mapping') }}">Job Post Geomapping</a>
                        </li>
                        @endcan
                        @can('rec-job-tracking-summary')
                          <li>
                            <a tabindex="-1" href="{{ route('recruitment-job.hr-tracking-summary') }}">Job Ticket Status</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcan
                @canany(['rec-candidate-screening-summary', 'rec-candidate-tracking-summary','rec-candidate-mapping','rec-candidate-credential','rec_candidate_transition_process','rec-create-candidate-credential','rec-edit-candidate-credential','rec-delete-candidate-credential','rec-candidate-selection','rec-candidate-uniform-shipment','rec-view-allocated-candidates-summary','rec-view-allocated-candidates-tracking','rec-view-candidate-training'])

                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">Candidates
                        <!--<i class="fa fa-caret-right" aria-hidden="true"></i>--></a>
                    <ul class="dropdown-menu">
                        @canany(['rec-candidate-credential','rec-create-candidate-credential','rec-edit-candidate-credential','rec-delete-candidate-credential'])
                        <li>
                            <a tabindex="-1" href="{{ route('recruitment.candidate-credentials') }}">Candidate Credentials</a>
                        </li>
                        @endcan
                          @canany(['rec-candidate-screening-summary','rec-view-allocated-candidates-summary'])
                        <li>
                            <a tabindex="-1" href="{{ route('recruitment.candidate.summary') }}">Candidate Summary</a>
                        </li>
                        @endcan
                        @canany(['rec-candidate-tracking-summary','rec-view-allocated-candidates-tracking'])
                        <li>
                            <a tabindex="-1" href="{{ route('recruitment.candidate-process-step') }}">Candidates Onboarding Status</a>
                        </li>
                        @endcan
                        @can('rec-candidate-selection')
                        <li>
                            <a tabindex="-1" href="{{ route('recruitment.candidate.selection') }}">Candidate Selection</a>
                        </li>
                        @endcan
                        @can('rec-candidate-uniform-shipment')
                        <li>
                            <a tabindex="-1" href="{{ route('recruitment.uniform-shippment-detail') }}">Uniform Shipment</a>
                        </li>
                        @endcan
                        @can('rec_candidate_transition_process')
                        <li>
                            <a tabindex="-1" href="{{ route('recruitment.candidate.conversion') }}">Candidates Conversion</a>
                        </li>
                        @endcan
                        @can('rec-view-candidate-training')
                        <li>
                            <a tabindex="-1" href="{{ route('recruitment.candidate-training') }}">Candidates Training</a>
                        </li>
                        @endcan

                         {{-- @can('rec-candidate-mapping')
                        <li>
                            <a tabindex="-1" href="{{ route('recruitment.candidate.mapping') }}">Candidates Geomapping</a>
                        </li>
                        @endcan --}}
                    </ul>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @canany(['view_all_shift_module_mapping','view_allocated_shift_module_mapping','view_supervisorpanel','candidate-schedule','candidate-schedule-summary','create-stc-customer','list-stc-customers','view_guard_tour','view_all_guard_tour',
        'manage_bonus_settings','view_bonus_reports',
        'view_shift_journal','view_all_shift_journal','view_allocated_shift_journal','view_openshift','create_employee_timeoff_request'])
        <li>
            <a href="homeSubmenu3" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav2.png">-->
                <i title="Site " class="fa fa-globe fa-fw" aria-hidden="true"></i>
                <span>Site </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">


                @canany(['view-stc-customer','view-all-stc-customer',
                        'manage_bonus_settings','view_bonus_reports',
                        'candidate-schedule','candidate-schedule-summary','create-stc-customer','list-stc-customers','view_openshift','create_employee_timeoff_request'])
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
                @endcan

                @canany(['view_all_shift_module_mapping','view_allocated_shift_module_mapping','view_supervisorpanel','view_guard_tour','view_all_guard_tour','view_all_incident_report','view_allocated_incident_report','view_shift_journal','view_all_shift_journal','view_allocated_shift_journal','view_operational_dashboard','create_employee_timeoff_request'])
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
                        @can('create_employee_timeoff_request')
                         <li><a href="{{ route('timeoff.timeoffRequesForm') }}">Time Off Request </a></li>
                         @endcan

                    </ul>
                </li>

                @endcan



                <!-- Start new drop down menu adding for facility management dashboard-->
                        @can('view_fmdashboard')
                        <li class="dropdown-submenu">
                            <a href="{{route('facility-management-dashboard.index')}}">FM Dashboard</a>
                        </li>
                        @endcan
                <!-- End of new drop down menu adding for facility management dashboard-->
            </ul>
        </li>
        @endcan
        @canany(['view_timesheet_by_employee','view_timesheet_detail_view','view_employee_summary','view_notification','view_allocation_report','view_timesheet_approval','enable_mobile_security_patrol','view_all_mobile_security_patrol','view_allocated_mobile_security_patrol','view_all_customer_qrcode_summary','view_allocated_customer_qrcode_summary','view_all_qrcode_data','view_allocated_qrcode_data', 'view_manual_timesheet_entry', 'view_manual_timesheet_report'])

        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Time " class="fa fa-clock fa-fw" aria-hidden="true"></i>
                <span>Time </span>
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
                @canany(['view_timesheet_by_employee','view_timesheet_detail_view','view_allocation_report','view_employee_summary','view_notification', 'view_manual_timesheet_report'])
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
        @endcan

        @canany(['create_schedule_allocated_customer','create_schedule_all_customer', 'view_all_employee_schedule_requests', 'view_allocated_employee_schedule_requests','view_reports_employee_schedules'])
        <li>

            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="MST" class="fa fa-calendar fa-fw"  aria-hidden="true"></i><span>&nbsp;Scheduling

                </span>
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
        @endcan

        @canany(['view_all_live_location','view_allocated_live_location','view_all_mobile_security_patrol',
        'view_allocated_mobile_security_patrol','view_all_mobile_security_patrol_trips','view_allocated_mobile_security_patrol_trips',
        'view_all_satellite_tracking','view_allocated_satellite_tracking','view_dispatch_request_mst'])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="MST" class="fa fa-bell fa-fw"  aria-hidden="true"></i><span>&nbsp;MST

                </span>
            </a>
            <ul class="dropdown-menu menu-list mst-side-menu" role="menu">
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
        @endcan


        @can('view_capacitytool')
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Capacity Tool" class="fa fa-wrench fa-fw" aria-hidden="true"></i>
                <span>Capacity Tool</span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @can('create_entry')
                <li class="dropdown-submenu">
                    <a href="{{ route('capacitytool.create') }}"> Track Capacity </a>
                </li>
                @endcan
                <li class="dropdown-submenu">
                    <a href="{{ route('capacitytool') }}"> View Capacity </a>
                </li>
            </ul>
        </li>
        @endcan
        @canany(['view_compliance_all', 'view_analytics', 'view_assigned_compliance'])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Compliance " class="fa fa-book fa-fw" aria-hidden="true"></i>
                <span>Compliance </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                <li>
                    <a href="{{ route('policy.dashboard') }}"> Dashboard </a>
                </li>
            </ul>
        </li>
        @endcan
        @canany(['view_contracts'])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="CMUF" class="fa fa-briefcase fa-fw" aria-hidden="true"></i>
                <span>Contracts</span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @canany(['view_post_order','create_post_order','view_allocated_post_order','create_allocated_post_order'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">
                        Post Order
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
                @canany(['view_rfp_catalogue','create_rfp_catalogue'])
                <li class="dropdown-submenu">
                    <a class="test" tabindex="-1" href="#">
                        RFP Catalog
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('rfp-catalogue.view') }}"> View </a>
                        </li>
                         @can(['create_rfp_catalogue'])
                        <li>
                            <a href="{{ route('rfp-catalogue.create') }}"> Create </a>
                        </li>
                         @endcan
                    </ul>
                </li>
                @endcan
                    @canany(['view_contracts'])
                    <li class="dropdown-submenu">
                            <a tabindex="-1" href="{{ route('contracts.all-cmuf') }}">View All Contracts</a>
                    </li>
                    @endcan
                    <li class="dropdown-submenu">
                        <a class="test" tabindex="-1" href="#">RFP Tracking
                        </a>

                        <ul class="dropdown-menu">
                                @can(['create_rfp'])
                            <li>
                                <a href="{{ route('rfp.create') }}"> New RFP </a>
                            </li>
                            @endcan
                            <li>
                                <a href="{{ route('rfp.summary') }}"> RFP Summary </a>
                            </li>
                            {{-- <li>
                                <a href="{{route('rfp.rfplink')}}"> RFP link </a>
                            </li> --}}

                        </ul>

                    </li>
            </ul>
        </li>
    @endcan
        @can('view_training')
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <i title="Training&Development" class="fa fa-graduation-cap fa-fw" aria-hidden="true"></i>
                <span> Training </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @can('learner_view')
                <li>
                    <a href="{{ route('learning.dashboard') }}"> Learner View</a>
                </li>

                @endcan
                 @can('learner_admin')
                 <li>
                    {{-- <a href="{{ route('course.training') }}"> Admin View </a>--}}

                    <a href="{{ route('learningandtraining.dashboard') }}"> Admin View </a>
                </li>
                @endcan
                <li>
                    <a href="{{ route('content-manager.login') }}"> Content Manager</a>
                </li>
            </ul>
        </li>
        @endcan
        @can('view_client')
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="clients" class="fa fa-briefcase fa-fw" aria-hidden="true"></i>
                <span>Clients</span>
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

            </ul>
        </li>
        @endcan
        @canany(['view_all_expense_claim','view_allocated_expense_claim','view_all_mileage_claim','view_allocated_mileage_claim','expense_send_statements'])
        <li>

                    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                        <!--<img src="images/nav3.png">-->
                        <i title="expense" class="fas fa-credit-card fa-fw" aria-hidden="true"></i>
                        <span>Expense</span>
                    </a>
                    <ul class="dropdown-menu menu-list" role="menu">
                        @canany(['view_all_expense_claim','view_allocated_expense_claim','view_all_mileage_claim','view_allocated_mileage_claim'])
                        <li class="dropdown-submenu">
                        @if(auth()->user()->can('view_all_expense_claim') || auth()->user()->can('view_allocated_expense_claim'))
                            <a href="{{ route('expense-dashboard.index') }}">Dashboard</a>
                        @else
                            <a href="{{ route('mileage-dashboard.index') }}">Dashboard</a>
                        @endif
                        </li>
                        @endcan
                        @can('expense_send_statements')
                        <li class="dropdown-submenu">
                          <a href="{{ route('expense-statements.create') }}">Expense Statements</a>
                        </li>
                        @endcan
                    </ul>
        </li>
        @endcan
        @canany(['view_all_visitorlog','view_allocated_visitorlog','create_visitorlog'])
        <li>
            <a href="{{ route('visitor-log.dashboard') }}">
                <!--<img src="images/nav3.png">-->
                <i title="Visitor Log " class="fas fa-user-edit fa-fw" aria-hidden="true"></i>
                <span>Visitor Log </span>
            </a>
        </li>
        @endcan
        <li>
        @canany(['view_allocated_customers_keys','view_all_customers_keys','view_all_keylog_summary','view_allocated_keylog_summary'])
        <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Key Management" class="fas fa-key fa-fw" aria-hidden="true"></i>
                <span>Key Management </span>
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
        @canany(['initiate_vehicle','edit_initiated_vehicle','view_completed_maintenance','view_vehicle_cumilative_km','view_pending_maintenance'])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Vehicle" class="fas fa-taxi fa-fw" aria-hidden="true"></i>
                <span>Vehicle</span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                 @canany(['initiate_vehicle','edit_initiated_vehicle'])
                <li class="dropdown-submenu">
                    <a href="{{ route('vehicle.initiate') }}"> Initiate Vehicle </a>
                </li>
                @endcan
                @canany(['view_completed_maintenance','view_vehicle_cumilative_km','view_pending_maintenance'])
                <li class="dropdown-submenu">
                    @if (\Auth::user()->can('view_pending_maintenance'))
                    <a href="{{ route('vehicle.pending.maintenance') }}"> Maintenance </a>
                    @elseif(\Auth::user()->can('view_completed_maintenance'))
                    <a href="{{ route('vehicle.maintenance') }}"> Maintenance </a>
                    @elseif(\Auth::user()->can('view_vehicle_cumilative_km'))
                    <a href="{{ route('vehicle.cumilative_km') }}"> Maintenance </a>
                    @endif

                </li>
                 @endcan
            </ul>
        </li>
        @endcan
        @canany(['view_meeting_page','view_scheduled_meeting_page',"create_blastcom_all_customers","create_blastcom_allocated_customers","view_blastcom_reports"])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Facility" class="fas fa-calendar fa-fw" aria-hidden="true"></i>
                <span>CGL Meet</span>

            </a>

            <ul class="dropdown-menu menu-list" role="menu">
                    @can('view_meeting_page')
                        <li class="dropdown-submenu">
                                <a href="{{ route('jitsi.index') }}"> Meeting </a>
                        </li>
                    @endcan
                    @can('view_scheduled_meeting_page')
                        <li class="dropdown-submenu">
                            <a href="{{ route('jitsi.schedulemeeting') }}"> Schedule Meeting </a>
                        </li>
                    @endcan
                    @canany(["create_blastcom_all_customers","create_blastcom_allocated_customers"])
                    <li class="dropdown-submenu">
                        <a href="{{ route('mailblast.index') }}"> BlastCom </a>
                    </li>
                    @endcanany
                    @can('view_blastcom_reports')
                    <li class="dropdown-submenu">
                        <a href="{{ route('mailblast.reports') }}"> BlastCom - Reports </a>
                    </li>
                    @endcan

            </ul>
        </li>
        @endcanany
        @canany(['view_all_customer_facility','view_allocated_customer_facility'])
            <li>
                <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->
                    <i title="Facility" class="fas fa-calendar fa-fw" aria-hidden="true"></i>
                    <span>Facility Signout</span>

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
        @endcan

{{--        <li>--}}
{{--            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">--}}
{{--                <!--<img src="images/nav3.png">-->--}}
{{--                <i title="Fever" class="fas fa-heartbeat fa-fw" aria-hidden="true"></i>--}}
{{--                <span>Fever Scan</span>--}}
{{--            </a>--}}
{{--            <ul class="dropdown-menu menu-list" role="menu">--}}
{{--                    <li class="dropdown-submenu">--}}
{{--                        <a href="{{ route('fever.site-view') }}"> Customer View </a>--}}
{{--                    </li>--}}
{{--                    <li class="dropdown-submenu">--}}
{{--                        <a href="{{ route('fever.individual-view') }}"> Macro View </a>--}}
{{--                    </li>--}}
{{--                    <li class="dropdown-submenu">--}}
{{--                        <a href="{{ route('fever-reading-report-view') }}"> Report </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--        </li>--}}

        @can('view_idsscheduling')
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="IDS Scheduling" class="fas fa-table fa-fw" aria-hidden="true"></i>
                <span>IDS Scheduling</span>
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
        @endcan

        @canany(['view_uniformscheduling','view_uniform_orders','view_ura_transactions'])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="IDS Scheduling" class="fas fa-table fa-fw" aria-hidden="true"></i>
                <span>Uniform </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @can(['uniform_view_all_appointment'])
                    <li class="dropdown-submenu">
                        <a href="{{ route('uniform-admin') }}"> View Schedule</a>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="{{ route('uniform-admin.slot-booking.list-page') }}"> Appoinment Lists</a>
                    </li>
                @endcan
                @can(['view_ura_transactions'])
                <li class="dropdown-submenu">
                        <a href="{{route('ura.transactions') }}">URA Transactions</a>
                </li>
                @endcan
                @can(['view_uniform_orders'])
                <li class="dropdown-submenu">
                    <a href="{{route('uniform.orders') }}">Order Details</a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @canany(['view_covid_daily_transaction_report','view_covid_compliance_report','view_sitenote_reports','view_customersurvey_reports','view_visitor_log_report','view_all_site_document_report','view_allocated_site_document_report','view_recruiting_analytics_report','view_termination_report'])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <i title="Reports" class="fa fa-sticky-note fa-fw" aria-hidden="true"></i>
                <span> Reports </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @can('view_covid_daily_transaction_report')
                    <li>
                        <a href="{{ route('reports.dailytransactions') }}">Daily Transaction Report</a>
                    </li>
                @endcan
                @can('view_covid_compliance_report')
                    <li>
                        <a href="{{ route('reports.fevercompliancereport') }}">Compliance Report</a>
                    </li>
                @endcan
                @can('view_sitenote_reports')
                    <li>
                        <a href="{{ route('reports.sitenotes') }}">Site Notes Report</a>
                    </li>
                @endcan
                @can('view_customersurvey_reports')
                    <li>
                        <a href="{{ route('reports.surveryreport') }}">Survey Report</a>
                    </li>
                @endcan

                @can('view_visitor_log_report')
                    <li>
                        <a href="{{ route('reports.visitorLogReport') }}">Visitor Log Report</a>
                    </li>
                @endcan

              {{--   @can('view_certificate_expiry_report') --}}
                 @canany(['view_all_site_document_report','view_allocated_site_document_report','view_certificate_expiry_report'])
                    <li>
                        <a href="{{ route('reports.certificateExpiryReport') }}">Document Expiry</a>
                    </li>
                 {{-- @endcan --}}
                @endcan

                @can('view_recruiting_analytics_report')
                    <li>
                        <a href="{{ route('reports.recruitinganalyticsreport') }}">Recruiting Report</a>
                    </li>
                @endcan
                @can('view_termination_report')
                    <li>
                        <a href="{{ route('reports.terminationReport') }}">Termination Report</a>
                    </li>
                @endcan
            </ul>
        </li>
        @endcan
        @canany(['view_osgc_registered_users'])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <i title="Osgc" class="fa fa-briefcase fa-fw"  aria-hidden="true"></i>
                <span>OSGC</span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @can('view_osgc_registered_users')
                    <li>
                        <a href="{{ route('osgc.registered-users') }}">Registered Users</a>
                    </li>
                @endcan
            </ul>
        </li>
        @endcan
        @canany(['create_task_all_customer','create_task_allocated_customer','view_all_reports','view_allocated_customer_reports','view_assigned_reports','view_all_performance_reports','view_allocated_performance_reports'])
         <li>
            <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <i title="Project Management" class="fa fa-sticky-note fa-fw" aria-hidden="true"></i>
                <span> Project </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                 @canany(['create_task_all_customer','create_task_allocated_customer'])
                <li>
                    <a href="{{ route('project') }}">Projects</a>
                </li>
                @endcan
                @canany(['view_all_reports','view_allocated_customer_reports','view_assigned_reports'])
                <li>
                    <a href="{{ route('pm.report') }}">Reports</a>
                </li>
                @endcan
                 @canany(['view_all_performance_reports','view_allocated_performance_reports'])
                <li>
                    <a href="{{ route('pm.get-project-rating') }}">Performance Report</a>
                </li>
                 @endcan
            </ul>

        </li>
        @endcan

        @canany(['user_view','customer_view'])
        <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <i title="Management" class="fa fa-briefcase fa-fw"  aria-hidden="true"></i>
                <span>Management</span>
            </a>
            <ul class="dropdown-menu menu-list mst-side-menu" role="menu">
                @can(['user_view'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('management.userList') }}">User</a>
                </li>
                @endcan
                @can(['customer_view'])
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('management.customerList') }}">Customer</a>
                </li>
                @endcan
            </ul>

        </li>
        @endcan


        @canany(['motion_sensor_view'])
        <li>
            <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <i title="Project Management" class="fa fa-sticky-note fa-fw" aria-hidden="true"></i>
                <span> Sensors </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
            @canany(['motion_sensor_view'])
                <li>
                    <a href="{{ route('sesors.triggers') }}">Sensor Triggers</a>
                </li>
            @endcan
            </ul>
        </li>
        @endcan

        <li>
            <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <i title="Project Management" class="fa fa-camera fa-fw" aria-hidden="true"></i>
                <span> IP Camera </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                <li>
                    <a href="{{ route('ip_camera.widget_view') }}">IP Camera</a>
                </li>
            </ul>
        </li>

        @canany(['view_video_post_summary'])
        <li>
            <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <i title="Video Post" class="fas fa-play-circle fa-fw" aria-hidden="true"></i>
                <span> Video Post </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
            @can(['view_video_post_summary'])
                <li>
                    <a href="{{ route('videopost.summary') }}">View Video Post</a>
                </li>
            @endcan
            </ul>
        </li>
        @endcan


        @can('view_admin')
        <li>
            <a href="{{ route('admin') }}">
                <!--<img src="images/nav3.png">-->
                <i title="Administration " class="fa fa-cog fa-fw" aria-hidden="true"></i>
                <span>Administration   </span>
            </a>
        </li>
        @endcan
    </ul>
</nav>
