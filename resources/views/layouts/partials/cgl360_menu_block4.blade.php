<li attr-block="4" class="pli">
    <a href="#" attr-block="4"  class="mainhead mhead">
        <div attr-block="4" class="plihidden ">P </div>
        <span attr-block="4"   >People </span>
    </a>

</li>
@canany(['rec-candidate-credential','rec-create-candidate-credential','rec-edit-candidate-credential','rec-delete-candidate-credential',
'rec-candidate-screening-summary','rec-view-allocated-candidates-summary',
'rec-candidate-tracking-summary','rec-view-allocated-candidates-tracking',
'rec-candidate-selection','rec-candidate-uniform-shipment','rec_candidate_transition_process',
'rec-view-candidate-training','rec-create-job', 'rec-edit-job', 'rec-archive-job', 'rec-job-approval', 'rec-hr-tracking',
         'rec-job-attachement-settings', 'rec-list-jobs-from-all', 'rec-job-tracking-summary','rec-view-allocated-job-requisitions',
         'rec-view-allocated-candidates-geomapping'])
<li class="block4 accordclass">
    
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <i title="Recruiting " class="iconclass  fa fa-university fa-fw" aria-hidden="true"></i>
        <span class="multilinespan">Recruiting </span>
    </a>
    <ul class="dropdown-menu first-menu-list" role="menu" aria-labelledby="menu1">
        @canany(['rec-create-job', 'rec-edit-job', 'rec-archive-job', 'rec-job-approval', 'rec-hr-tracking',
         'rec-job-attachement-settings', 'rec-list-jobs-from-all', 'rec-job-tracking-summary','rec-view-allocated-job-requisitions',
         'rec-view-allocated-candidates-geomapping'])
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
          @endcanany
          
          @canany(['rec-candidate-credential','rec-create-candidate-credential','rec-edit-candidate-credential','rec-delete-candidate-credential',
          'rec-candidate-screening-summary','rec-view-allocated-candidates-summary',
          'rec-candidate-tracking-summary','rec-view-allocated-candidates-tracking',
          'rec-candidate-selection','rec-candidate-uniform-shipment','rec_candidate_transition_process',
          'rec-view-candidate-training'])

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
@endcanany
@canany(['create-job', 'edit-job', 'delete-job', 'archive-job', 'job-approval', 'hr-tracking', 'job-attachement-settings', 'list-jobs-from-all', 'job-tracking-summary', 'view_recruitinganalyticswidgets', 'view_admin', 'candidate-screening-summary', 'candidate-mapping', 'candidate-tracking-summary', 'employee-mapping-rating', 'supervisor-mapping-rating','candidate_transition_process','view_all_whistleblower','create_employee_whistleblower','create_all_whistleblower','create_allocated_whistleblower','view_employee_whistleblower','view_allocated_whistleblower','create_exit_interview','create_all_exit_interview','view_all_exit_interview','view_exit_interview','view_all_candidates','view_all_candidates_candidate_geomapping','view_all_employee_surveys','view_allocated_employee_surveys','view_allocated_sites_in_employeefeedback','view_all_sites_in_employeefeedback','view_transaction_department_allocation'])
<li class="block4 accordclass">
    
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <i title="Recruiting " class="iconclass  fa fa-university fa-fw" aria-hidden="true"></i>
        <span class="multilinespan">Old Recruiting Process </span>
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
        @canany(['employee-mapping-rating','create_exit_interview','create_all_exit_interview','view_all_exit_interview','view_exit_interview','view_employee_whistleblower','view_all_whistleblower','create_all_whistleblower','create_employee_whistleblower','create_allocated_whistleblower','view_allocated_whistleblower','view_all_employee_availability','view_allocated_employee_availability','update_all_employee_availability','update_allocated_employee_availability','view_all_employee_unavailability','view_allocated_employee_unavailability','update_delete_all_employee_unavailability','update_delete_allocated_employee_unavailability','view_all_employee_surveys','view_allocated_employee_surveys','view_allocated_sites_in_employeefeedback','view_all_sites_in_employeefeedback','view_transaction_department_allocation'])
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
@endcanany

@canany(['employee-mapping-rating','create_exit_interview','create_all_exit_interview','view_all_exit_interview','view_exit_interview','view_employee_whistleblower','view_all_whistleblower','create_all_whistleblower','create_employee_whistleblower','create_allocated_whistleblower','view_allocated_whistleblower','view_all_employee_availability','view_allocated_employee_availability','update_all_employee_availability','update_allocated_employee_availability','view_all_employee_unavailability','view_allocated_employee_unavailability','update_delete_all_employee_unavailability','update_delete_allocated_employee_unavailability','view_all_employee_surveys','view_allocated_employee_surveys','view_allocated_sites_in_employeefeedback','view_all_sites_in_employeefeedback','view_transaction_department_allocation'])

<li class="block4 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Employee Management" class="iconclass fa fa-cog fa-fw" aria-hidden="true"></i>
        <span class="multilinespan">Employee Management </span>
    </a>
    <ul class="dropdown-menu transform-class">
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

    @canany(['view_all_employee_surveys','view_allocated_employee_surveys'])
        <li><a href="{{ route('employee.employeeSurveys') }}">Employee Survey</a></li>
    @endcanany
    @canany(['view_allocated_sites_in_employeefeedback','view_all_sites_in_employeefeedback','view_transaction_department_allocation'])
        <li><a href="{{ route('employee.employeeFeedback') }}">Employee Feedback</a></li>
    @endcanany

    </ul>
</li>
@endcanany
@canany(['rec-candidate-credential','rec-create-candidate-credential','rec-edit-candidate-credential','rec-delete-candidate-credential',
'rec-candidate-screening-summary','rec-view-allocated-candidates-summary','rec-candidate-tracking-summary','rec-view-allocated-candidates-tracking',
'rec-candidate-selection','rec-candidate-uniform-shipment','rec_candidate_transition_process','rec-view-candidate-training'
])
<li class="block4 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Candidate " class="iconclass fa fa-user fa-fw" style="color: #fff !important" aria-hidden="true"></i>
        <span class="singlelinespan">Candidate </span>
    </a>
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
@endcanany
@canany(['view_compliance_all', 'view_analytics', 'view_assigned_compliance'])
<li class="block4 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Compliance " class="iconclass fa fa-gavel fa-fw" aria-hidden="true"></i>
        <span class="singlelinespan">Compliance </span>
    </a>
        @canany(['view_compliance_all', 'view_analytics', 'view_assigned_compliance'])

            <ul class="dropdown-menu menu-list" role="menu">
                <li>
                    <a href="{{ route('policy.dashboard') }}"> Dashboard </a>
                </li>
            </ul>
        @endcan
</li>
@endcanany
@canany(['view_training', 'learner_view', 'learner_admin','view_video_post_summary','view_video_post_summary'])
<li class="block4 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Training " class="iconclass fa fa-graduation-cap fa-fw" aria-hidden="true"></i>
        <span class="singlelinespan">Training </span>
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
        @canany(['view_video_post_summary'])
        <li class="block4 accordclass">
            <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                 Video Post
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
    </ul>
</li>
@endcanany
