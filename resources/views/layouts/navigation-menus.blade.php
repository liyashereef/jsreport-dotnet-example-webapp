@guest
<ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
        <a class="nav-link blue" href="{{ route('login') }}">
            <strong>Login </strong>
        </a>
    </li>
</ul>
@else
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav ml-auto">
        @canany(['create-job', 'edit-job', 'delete-job', 'archive-job', 'job-approval', 'hr-tracking', 'job-attachement-settings', 'list-jobs-from-all', 'job-tracking-summary', 'view_recruitinganalyticswidgets', 'view_admin', 'candidate-screening-summary', 'candidate-mapping', 'candidate-tracking-summary', 'employee-mapping-rating', 'supervisor-mapping-rating'])
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle blue" href="#" id="navbardrop">
                <strong>Recruiting</strong>
            </a>
            <ul class="dropdown-menu ">
                @canany(['create-job', 'edit-job', 'delete-job', 'archive-job', 'job-approval', 'hr-tracking', 'job-attachement-settings', 'list-jobs-from-all', 'job-tracking-summary', 'view_recruitinganalyticswidgets'])
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle dropdown-item" data-toggle="dropdown">Job Posting</a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-position dropdown-sub-menu">
                        @can('create-job')
                        <li><a class="dropdown-item" href="{{ route('job.create') }}" id="navbardrop">New Job Request</a></li>
                        @endcan
                        @canany(['create-job','edit-job','delete-job','archive-job','job-approval','hr-tracking','job-attachement-settings','list-jobs-from-all'])
                        <li><a class="dropdown-item" href="{{ route('job') }}" id="navbardrop">Summary Requisitions</a></li>
                        <li><a class="dropdown-item" href="{{ route('job.mapping') }}" id="navbardrop">Job Post
                                Geomapping</a></li>
                        @endcan
                        @can('job-tracking-summary')
                        <li><a class="dropdown-item" href="{{ route('job.hr-tracking-summary') }}" id="navbardrop">Job
                                Ticket Status</a></li>
                        @endcan
                        @can('view_recruitinganalyticswidgets')
                        <li><a class="dropdown-item" href="{{ route('recruitment-analytics.index') }}" id="navbardrop">Analytics</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @canany(['view_admin', 'candidate-screening-summary', 'candidate-mapping',
                'candidate-tracking-summary'])
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle dropdown-item" data-toggle="dropdown">Candidates</a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-position dropdown-sub-menu">
                        @can('view_admin')
                        <li><a class="dropdown-item" target="_blank" href="{{ route('applyjob') }}" id="navbardrop">Candidate
                                Entry</a></li>
                        @endcan
                        @can('candidate-screening-summary')
                        <li><a class="dropdown-item" href="{{ route('candidate') }}" id="navbardrop">Candidate Summary</a></li>
                        @endcan
                        @can('candidate-mapping')
                        <li><a class="dropdown-item" href="{{ route('candidate.mapping') }}" id="navbardrop">Candidate
                                Geomapping</a></li>
                        @endcan
                        @can('candidate-tracking-summary')
                        <li><a class="dropdown-item" href="{{ route('candidate.summary') }}" id="navbardrop">Candidate
                                Onboarding Status</a></li>
                        @endcan
                        @can('candidate_transition_process')
                         <li><a class="dropdown-item" href="{{ route('candidate.conversion') }}" id="navbardrop">Candidates Conversion</a></li>
                          @endcan
                    </ul>
                </li>
                @endcan
                @canany(['employee-mapping-rating', 'supervisor-mapping-rating'])
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle dropdown-item" data-toggle="dropdown">Employees</a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-position dropdown-sub-menu">
                        @can('employee-mapping-rating')
                        <li><a class="dropdown-item" href="{{ route('employee.mapping') }}" id="navbardrop">Employee
                                Geomapping</a></li>
                        @endcan
                        @can('supervisor-mapping-rating')
                        <li><a class="dropdown-item" href="{{ route('employee.mapping',["role "=>"supervisor"]) }}" id="navbardrop">Supervisor
                                Geomapping</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @canany(['view_supervisorpanel','candidate-schedule','candidate-schedule-summary','create-stc-customer','list-stc-customers'])
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle blue" href="#" id="navbardrop">
                <strong>Site</strong>
            </a>
            <ul class="dropdown-menu">
                @canany(['view-stc-customer','view-all-stc-customer','candidate-schedule','candidate-schedule-summary','create-stc-customer','list-stc-customers'])

                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle dropdown-item" data-toggle="dropdown">Short Term Contract</a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-position dropdown-sub-menu">
                        @canany(['view-stc-customer','view-all-stc-customer'])
                        <li><a class="dropdown-item" href="{{ route('customers.mapping',["stc"=>"stc"]) }}" id="navbardrop">STC
                                Client Geomapping </a></li>
                        @endcan
                        @can('create-stc-customer')
                        <li><a class="dropdown-item" href="{{ route('stc.create') }}" id="navbardrop">Create STC</a></li>
                        @endcan
                        @can('list-stc-customers')
                        <li><a class="dropdown-item" href="{{ route('stc') }}" id="navbardrop">View STC</a></li>
                        @endcan
                        @can('candidate-schedule')
                        <li><a class="dropdown-item" href="{{ route('candidate.schedule') }}" id="navbardrop">Candidate
                                Schedule</a></li>
                        @endcan
                        @can('candidate-schedule-summary')
                        <li><a class="dropdown-item" href="{{ route('stc.summary') }}" id="navbardrop">Schedule Summary</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @canany(['view_supervisorpanel'])
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle dropdown-item" data-toggle="dropdown">Permanent Contract</a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-position dropdown-sub-menu">
                        <li><a class="dropdown-item" href="{{ route('customers.mapping') }}" id="navbardrop">Site
                                Status Dashboard</a></li>
                        @canany(['view_guard_tour','view_all_guard_tour'])
                        <li><a class="dropdown-item" href="{{ route('customers.mappingGuardTour',["guard_tour"=>"guard_tour"]) }}"
                                id="navbardrop">Guard Tour</a></li>
                        @endcan
                        @canany(['view_all_incident_report','view_allocated_incident_report'])
                        <a class="dropdown-item" href="{{ route('incident.dashboard') }}" id="navbardrop">Incident
                            Updates</a>
                        @endcan
                    </ul>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('view_timetracker')
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle blue" href="#" id="navbardrop">
                <strong>Time</strong>
            </a>
            <ul class="dropdown-menu">
                @can('view_timesheet_approval')
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle dropdown-item" data-toggle="dropdown">Timesheet Entry</a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-position dropdown-sub-menu">
                        @can('view_timesheet_approval')
                        <li><a class="dropdown-item" href="{{ route('approval.timesheet') }}" id="navbardrop">Timesheet
                                Approval </a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @canany(['view_timesheet_by_employee','view_timesheet_detail_view','view_allocation_report','view_employee_summary','view_notification'])
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle dropdown-item" data-toggle="dropdown">Timesheet Reporting</a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-position dropdown-sub-menu">
                        @can('view_timesheet_by_employee')
                        <li><a class="dropdown-item" href="{{ route('timetracker.timesheet') }}" id="navbardrop">
                                Timesheet By Employee </a></li>
                        @endcan
                        @can('view_timesheet_detail_view')
                        <li><a class="dropdown-item" href="{{ route('timetracker.timesheet-detail') }}" id="navbardrop">
                                Timesheet Detail View</a></li>
                        @endcan
                        @can('view_allocation_report')
                        <li><a class="dropdown-item" href="{{ route('timetracker.allocation') }}" id="navbardrop">
                                Allocation Report</a></li>
                        @endcan
                        @can('view_employee_summary')
                        <li><a class="dropdown-item" href="{{ route('timetracker.employee-summary') }}" id="navbardrop">
                                Employee Summary</a></li>
                        @endcan
                        @can('view_notification')
                        <li><a class="dropdown-item" href="{{ route('notification.index') }}" id="navbardrop">
                                Notification</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle blue" href="#" id="navbardrop">
                <strong>Compliance</strong>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('policy.dashboard') }}">Dashboard</a>
            </div>
        </li>
        <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle blue" href="#" id="navbardrop">
                    <strong>Training</strong>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('training') }}">Dashboard</a>
                </div>
            </li>
        <li class="nav-item dropdown" style="min-width: 146px">
            <a class="nav-link dropdown-toggle blue" href="#" id="navbardrop" data-toggle="dropdown">
                    @if(Auth::user()->employee_profile->image)<img src="{{asset('images/uploads/') }}/{{ Auth::user()->employee_profile->image }}"
                    class="user-image" alt="">  @else <i class="fa fa-2x fa-user"></i> @endif
                <strong> {{ ucfirst(auth()->user()->full_name) }} </strong>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fa fa-fw fa-user "></i> Profile</a>
                @can('view_admin')
                <a class="dropdown-item" href="{{ route('admin') }}"><i class="fa fa-fw fa-cog "></i> Administration</a>
                @endcan
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa fa-fw fa-power-off"></i> {{ trans('adminlte::adminlte.log_out') }}
                </a>
                <form id="logout-form" action="{{ url(config('adminlte.logout_url ', 'logout ')) }}" method="POST"
                    style="display: none;">
                    @if(config('adminlte.logout_method '))
                    {{ method_field(config('adminlte.logout_method')) }}
                    @endif
                    {{ csrf_field() }}
                </form>
            </div>
        </li>
    </ul>
</div>
<style>
    .dropdown-position {
        /*position: relative !important;*/
        right: -150px !important;
        border-bottom: orange 1px solid;
        border-right: solid orange 1px;
        top: -10px;
    }
</style>
@endguest
