@extends('layouts.app')
@section('content')
<style>
.break-word {
  word-wrap:break-word;
}
</style>
<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Performance Form</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      {{ Form::open(array('url'=>'#','id'=>'performance-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
      {{ Form::hidden('id', null) }}
      {{ Form::hidden('user_id',Auth::user()->id) }}
      {{ Form::hidden('employee_id',$user->id) }}
      <div class="modal-body">
        <div class="form-group row" style="margin-left:0px;" id="customer_id">
          <label for="customer" class="col-sm-3">Select Customer</label>
          <div class="col-sm-8">
            {!!Form::select('customer_id',[null=>'Please Select'] + $project_list,null, ['class' => 'form-control select2','id'=>'customer_id'])!!}
            <small class="help-block"></small>
          </div>
        </div>
        <div class="form-group row" style="margin-left:0px;" id="subject">
          <label for="subject" class="col-sm-3">Subject</label>
          <div class="col-sm-8">
            {{ Form::text('subject',null,array('class'=>'form-control')) }}
            <small class="help-block"></small>
          </div>
        </div>
        <div class="form-group row" style="margin-left:0px;">
          <label for="employee_rating_lookup_id" class="col-sm-3 control-label" style="vertical-align: top;">Rating</label>
          <div class="col-sm-8">
            {!!Form::select('employee_rating_lookup_id',[null=>'Please Select'] + $ratingLookups,null, ['class' => 'form-control','id'=>'employee_rating_lookup_id'])!!}
            <small class="help-block"></small>
          </div>
        </div>
        <div class="form-group row" style="margin-left:0px;">
          <label for="policy_id" class="col-sm-3 control-label">Policy</label>
          <div class="col-sm-8">
            {!!Form::select('policy_id',[null=>'Please Select'] ,null, ['class' => 'form-control','id' => 'policy_id'])!!}
            <small class="help-block"></small>
          </div>
        </div>

        <div class="form-group" style="display: none;" id="description-div">
          <label for="description" class="col-sm-5 control-label">Policy Description</label>
          <div class="col-sm-12">
            <div style="height: 120px;width:94%;border:1px solid #ced4dafc;overflow-y:auto;">
              <label id="description">
              </label>
            </div>
            <small class="help-block"></small>
          </div>
        </div>
        <div class="form-group row" style="margin-left:0px;margin-bottom:2px;">
          <label for="subject" class="col-sm-3">Notify Employee</label>
          <div class="col-sm-8">
            {{ Form::checkbox('notify_employee',null,'checked', array('class'=>'form-control','id'=>'notify_employee','style'=>'width:22px;height:30px;')) }}
            <small class="help-block"></small>
          </div>
        </div>
        <div class="form-group" id="supporting_facts">
          <label for="supporting_facts" class="col-sm-5 control-label">Supporting Facts</label>
          <div class="col-sm-12">
            {{ Form::textarea('supporting_facts',null,array('class'=>'form-control','rows' => 3, 'cols' => 42,'style' => 'width:94%')) }}
            <small class="help-block"></small>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist" id="profileTabs">
      <li class="nav-item success">
        <a class="nav-link " data-toggle="tab" href="#profile">
          <span>1. Profile
          </span>
        </a>
      </li>

      <li class="nav-item success">
        <a class="nav-link" data-toggle="tab" href="#performancelog">
          <span>2. Performance Log
          </span>
        </a>
      </li>

      <li class="nav-item success">
        <a class="nav-link" data-toggle="tab" href="#timelog">
          <span>3. Time Log
          </span>
        </a>
      </li>

      <li class="nav-item success">
        <a class="nav-link" data-toggle="tab" href="#training">
          <span>4. Training
          </span>
        </a>
      </li>
      @can('view_allocated_compliance_policy_in_employee_geomapping')
      <li class="nav-item success">
        <a class="nav-link" data-toggle="tab" href="#compaliance">
          <span>5. Compliance
          </span>
        </a>
      </li>
      @endcan


    </ul>
    <div class="tab-content">
      <div id="profile" class="tab-pane active candidate-screen">
        <br>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> {{$user->full_name}}
        </div>
        <section class="full-width">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 form-panel">
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="data-list-container">
                  <div class="data-list-body">
                    <div class="data-list-line row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Employee Number
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->employee->employee_no or '--'}}
                      </div>
                    </div>
                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        First Name
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->first_name or '--'}}
                      </div>
                    </div>
                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Last Name
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->last_name or '--'}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Address
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->employee->employee_address or '--'}}
                      </div>
                    </div>
                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        City
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->employee->employee_city or '--'}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Postal Code
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->employee->employee_postal_code or '--'}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Phone Number
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->employee->phone or '--'}}
                        {{isset($user->employee->phone_ext) ? ' x'.$user->employee->phone_ext : ''}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Work Email
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->employee->employee_work_email or '--'}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Project Number
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{!empty($user->allocation->last()->customer) ? ($user->allocation->last()->customer->project_number): '--'}}

                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Project Name
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{!empty($user->allocation->last()->customer) ? ($user->allocation->last()->customer->client_name): '--'}}

                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Current Wage
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->employee->current_project_wage or '--'}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Date of Birth
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{date('F j, Y', strtotime($user->employee->employee_dob))}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Age
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{!empty($user->employee->employee_dob) ? ($user->employee->age): '--'}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Start Date
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{date('F j, Y', strtotime($user->employee->employee_doj))}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Length of Service(Year)
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{!empty($user->employee->employee_doj) ? ($user->employee->service_length): '--'}}
                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Veteran Status
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{ $user->employee->employee_vet_status  === 1 ? "Yes" : ($user->employee->employee_vet_status ===0 ? "No" : "--") }}
                      </div>
                    </div>
                    @foreach($user->securityClearanceUser as $clearance)
                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Clearance
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{ $clearance->securityClearanceLookups->security_clearance or '--' }}

                      </div>
                    </div>
                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Clearance Expiry
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{date('F j, Y', strtotime($clearance->valid_until)) }}

                      </div>
                    </div>
                    @endforeach
                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Employee Rating
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->employee->employee_rating or '--'}}

                      </div>
                    </div>

                    <div class="data-list-line  row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Position
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$user->employee->employeePosition->position or '--'}}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <div class="candidate-screen display-inline print-view-btn" style="float:right;">

      </div>
      <div id="performancelog" class="container-fluid tab-pane fade">
        <div class="row">
          <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
            <li class="nav-item success">
              <a class="nav-link " data-toggle="tab" href="#byemployee">
                <span>By Manager
                </span>
              </a>
            </li>
            @canany(['view_feedback_by_client'])
            <li class="nav-item success">
              <a class="nav-link" data-toggle="tab" href="#byclient">
                <span>By Client
                </span>
              </a>
            </li>
            @endcan
            @canany(['view_all_performance_reports','view_allocated_performance_reports'])
            <li class="nav-item success">
              <a class="nav-link" data-toggle="tab" href="#hqstaff">
                <span>Project Rating
                </span>
              </a>
            </li>
            @endcan
            @canany(['view_timesheet_approval_compalince'])
            <li class="nav-item success">
              <a class="nav-link" data-toggle="tab" href="#ratingofEmployee">
                <span>Timesheet Approval Compliance
                </span>
              </a>
            </li>
            @endcan
          </ul>
        </div>
        <div class="tab-content">
          <div id="byemployee" class="tab-pane">
            {{-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Performance log by employee
                </div> --}}
            <section class="full-width">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1" style="margin-bottom: 60px">
                {{ Form::submit('Enter New Record', array('class' => 'btn submit add-new','onclick'=>'openModal()')) }}
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 title-header-align form-panel">
                <!-- Data List container One-->
                @foreach($employee_rating as $key=>$employee_rating)
                <div id="ers-{{$employee_rating->id}}">
                  <div class="data-list-line row">
                    <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                      Subject
                    </div>
                    <div class="col-md-3 col-xs-12 col-sm-12  data-list-disc break-word">
                      {{$employee_rating->subject}}
                    </div>
                    <div class="col-md-4 col-xs-12 col-sm-12  data-list-label">
                      Policy
                    </div>
                    <div class="col-md-3 col-xs-12 col-sm-12  data-list-label">
                      Employee Response
                    </div>
                  </div>

                  <div class="data-list-container">
                    <div class="data-list-head">
                      <h2></h2>
                    </div>
                    <div class="data-list-body">
                      <div>
                        <div class="row">
                          <div class="col-md-5">
                            <div class="data-list-line row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Date
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                @php
                                @endphp
                                {{date('F d, Y', strtotime(str_replace('-','/',$employee_rating->created_at)))}}

                              </div>
                            </div>
                            <div class="data-list-line  row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Time
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                {{date('h:i A', strtotime($employee_rating->created_at))}}
                              </div>
                            </div>
                            <div class="data-list-line  row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Manager Name
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                {{$employee_rating->user->full_name}}
                              </div>
                            </div>

                            <div class="data-list-line  row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Manager Employee ID
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                {{$employee_rating->user->trashedEmployee->employee_no}}
                              </div>
                            </div>

                            <div class="data-list-line  row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Supporting Facts
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                {{$employee_rating->supporting_facts}}
                              </div>
                            </div>
                            <div class="data-list-line row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Rating
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                {{(null!=$employee_rating->userRating)?$employee_rating->userRating->rating:''}}
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="col-md-12 col-xs-12 col-sm-12  data-list-disc" style="margin:0px -15px;">
                              {{ (isset($employee_rating->policyDetails)? $employee_rating->policyDetails->policy : '') }}
                            </div>
                            <br>
                            <div class="col-md-12 col-xs-12 col-sm-12  data-list-disc" style="margin:0px -15px;">
                              {{ (isset($employee_rating->policyDetails)? $employee_rating->policyDetails->description : '') }}
                            </div>
                          </div>

                          <div class="col-md-3">
                            <div class="col-md-12 col-xs-12 col-sm-12  data-list-disc" style="margin:0px -15px;">
                              {{$employee_rating->response}}
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                    @can('remove_manager_rating')
                    <div class="float-right">
                      <button class="btn btn-danger js-remove-rating" data-type="mng" data-id="{{$employee_rating->id}}"><i class="fas fa-trash-alt"></i> Remove</button>
                    </div>
                    @endcan
                  </div>
                </div>
                @endforeach
              </div>
            </section>
          </div>

          <div id="byclient" class="tab-pane">
            <div class="row">
              <section class="full-width">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 title-header-align form-panel" style="margin-top: 50px !important;">
                  <!-- Data List container One-->
                  @foreach($client_rating as $key=>$client_rating)
                  <div id="crs-{{$client_rating->id}}">
                    <div class="data-list-line row">
                      <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Rated By
                      </div>
                      <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$client_rating->createdUser->full_name}}
                      </div>
                    </div>

                    <div class="data-list-container">
                      <div class="data-list-head">
                        <h2></h2>
                      </div>
                      <div class="data-list-body">
                        <div class="data-list-line row">
                          <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Date
                          </div>
                          <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            @php
                            //dump($employee_rating->created_at);
                            @endphp
                            {{date('F d, Y', strtotime(str_replace('-','/',$client_rating->created_at)))}}

                          </div>
                        </div>

                        <div class="data-list-line  row">
                          <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Time
                          </div>
                          <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            {{date('h:i A', strtotime($client_rating->created_at))}}
                          </div>
                        </div>

                        {{-- <div class="data-list-line  row">
                              <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                                Manager Name
                              </div>
                              <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                              {{$client_rating->user->full_name}}
                      </div>
                    </div> --}}

                    <div>

                      <div class="data-list-line row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                          Rating
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                          {{(null!=$client_rating->userRating)?$client_rating->userRating->rating:'--'}}
                        </div>
                      </div>

                      <div class="data-list-line  row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                          Client Feedback
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                          {{$client_rating->client_feedback}}
                        </div>
                      </div>
                    </div>
                    @can('remove_client_rating')
                    <div class="float-right">
                      <button class="btn btn-danger js-remove-rating" data-type="clnt" data-id="{{$client_rating->id}}"><i class="fas fa-trash-alt"></i> Remove</button>
                    </div>
                    @endcan
                  </div>
                </div>
            </div>
            @endforeach
          </div>
          </section>
        </div>



      </div>
      @canany(['view_all_performance_reports','view_allocated_performance_reports'])
      <div id="hqstaff" class="container-fluid tab-pane fade">
        <div class="tab-content">
          <div id="leaveofabscence" class="candidate-screen tab-pane active">
            <div class="row">
              {{-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Leave of Absence
                </div> --}}
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"> <br>
              </div>
              <section class="full-width">
                <table class="table table-bordered auto-refresh" id="rating-table">
                  <thead>
                    <tr>
                      <th class="sorting">Project</th>
                      <th class="sorting">Group</th>
                      <th class="sorting">Task</th>
                      <th>Assignee</th>
                      <th class="sorting">Assign Date</th>
                      <th class="sorting">Due Date</th>
                      <th class="sorting">Rating Date</th>
                      <th class="sorting">DL</th>
                      <th class="sorting">VA</th>
                      <th class="sorting">IN</th>
                      <th class="sorting">CM</th>
                      <th class="sorting">CX</th>
                      <th class="sorting">EF</th>
                      <th class="sorting">Notes</th>
                      <th class="sorting">Score</th>
                      <th>Rating</th>

                    </tr>
                  </thead>
                </table>
              </section>
            </div>
          </div>


        </div>
        <!-- </div> -->
      </div>
      @endcan


      @canany(['view_timesheet_approval_compalince'])
      <div id="ratingofEmployee" class="container-fluid tab-pane fade">
        <div class="tab-content">
          <div id="leaveofabscence" class="candidate-screen tab-pane active">
            <div class="row">
              {{-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Leave of Absence
                </div> --}}
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"> <br>
              </div>
              @if(@isset($timeSheetApprovalRating) && ($timeSheetApprovalRating != null))
              <section class="full-width">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1" style="margin-bottom: 60px">
                  @include('hranalytics::partials.average-score')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 title-header-align form-panel">
                  <!-- Data List container One-->
                  <div class="data-list-line row">
                    <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                      Subject
                    </div>
                    <div class="col-md-3 col-xs-12 col-sm-12  data-list-disc">
                      Timesheet Approval {{(isset($timeSheetApprovalRating->payperiod->pay_period_name) ? " - ".$timeSheetApprovalRating->payperiod->pay_period_name : "")}}
                    </div>
                    <div class="col-md-4 col-xs-12 col-sm-12  data-list-label">
                      Policy
                    </div>
                    <div class="col-md-3 col-xs-12 col-sm-12  data-list-label">
                      Employee Response
                    </div>
                  </div>

                  <div class="data-list-container">
                    <div class="data-list-head">
                      <h2></h2>
                    </div>
                    <div class="data-list-body">
                      <div>
                        <div class="row">
                          <div class="col-md-5">
                            <div class="data-list-line row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Date
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                @php
                                //dump($employee_rating->created_at);
                                @endphp
                                {{isset($timeSheetApprovalRating->created_at) ? date('F d, Y', strtotime(str_replace('-','/',$timeSheetApprovalRating->created_at))) : ''}}

                              </div>
                            </div>
                            <div class="data-list-line  row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Time
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                {{isset($timeSheetApprovalRating->created_at) ? date('h:i A', strtotime($timeSheetApprovalRating->created_at)) : ''}}
                              </div>
                            </div>
                            <div class="data-list-line  row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Manager Name
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                System Generated
                              </div>
                            </div>

                            <div class="data-list-line row">
                              <div class="col-md-5 col-xs-12 col-sm-12 data-list-label">
                                Rating
                              </div>
                              <div class="col-md-7 col-xs-12 col-sm-12  data-list-disc">
                                {{(isset($employeeTimesheetApprovalScore->rating) ? $employeeTimesheetApprovalScore->rating : "")}}
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="col-md-12 col-xs-12 col-sm-12  data-list-disc" style="margin:0px -15px;">
                            {{isset($policyDeatils) ? $policyDeatils->policy : ''}}
                            </div>
                            <br>
                            <div class="col-md-12 col-xs-12 col-sm-12  data-list-disc" style="margin:0px -15px;">
                              {{isset($policyDeatils) ? $policyDeatils->description : ''}}
                            </div>
                          </div>

                          <div class="col-md-3">
                            <div class="col-md-12 col-xs-12 col-sm-12  data-list-disc" style="margin:0px -15px;">
                              {{(isset($timeSheetApprovalRating->timesheetApprovalPayPeriodRating->response) ? $timeSheetApprovalRating->timesheetApprovalPayPeriodRating->response : "")}}
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>
              </section>
              @endif
              <section class="full-width">
                <table class="table table-bordered auto-refresh" id="employee-approval-rating-table">
                  <thead>
                    <tr>
                      <th class="sorting"></th>
                      <th class="sorting">Pay Period</th>
                      <th class="sorting">Start Date</th>
                      <th class="sorting">End Date</th>
                      <th class="sorting">Average Rating</th>
                    </tr>
                  </thead>
                </table>
              </section>
            </div>
          </div>


        </div>
        <!-- </div> -->
      </div>
      @endcan


    </div>

  </div>

  <div id="timelog" class="container-fluid tab-pane fade">
    <!-- <br><div class="container"> -->
    <div class="row">
      <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
        <li class="nav-item success">
          <a class="nav-link " data-toggle="tab" href="#leaveofabscence">
            <span>Leave of Absence
            </span>
          </a>
        </li>
        <li class="nav-item success">
          <a class="nav-link" data-toggle="tab" href="#adhoc" id="leave">
            <span>Ad-Hoc
            </span>
          </a>
        </li>
        <li class="nav-item success">
          <a class="nav-link " data-toggle="tab" href="#vacation">
            <span> Vacation
            </span>
          </a>
        </li>
      </ul>
    </div>
    <div class="tab-content">
      <div id="leaveofabscence" class="candidate-screen tab-pane active">
        <div class="row">
          {{-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Leave of Absence
                </div> --}}
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"> <br>
          </div>
          <section class="full-width">
            <table class="table table-bordered timesheet " id="table-id">
              <thead>
                <tr>
                  <th class="sl-no">#</th>


                  <th class="ts-gen">Description</th>
                  <th class="ts-gen">Type</th>
                  <th class="ts-gen">Attachments</th>
                  <th class="ts-gen">Days Claimed</th>
                  <th class="ts-gen">Days Permitted</th>
                  <th class="ts-gen">Days Approved</th>
                  <th class="ts-gen">Days Rejected</th>
                  <th class="start-end-date">Request Date</th>
                  <th class="start-end-date">Request Time</th>
                  <th class="ts-gen">HR Associate</th>
                  <th class="ts-gen">Review Date</th>
                  <th class="ts-gen">Review Time</th>
                  <th class="ts-gen">Reviewed By</th>
                  <th class="note-header">Status</th>

                </tr>
              </thead>
            </table>
          </section>
        </div>
      </div>
      <div id="adhoc" class="candidate-screen tab-pane ">
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"> <br>
          </div>
          <section class="candidate full-width">
            <table class="table table-bordered" id="employees-table">
              <thead>
                <tr>
                  <th class="sorting">Date of Absenteeism</th>
                  <th class="sorting">Payperiod</th>
                  <th class="sorting">Hours booked off</th>
                  <th class="sorting">Reason</th>
                  <th class="sorting">Notes</th>
                  <th class="sorting">Project No/Name</th>
                  <th class="sorting">Supervisor Name/Emp No</th>
                </tr>
              </thead>
            </table>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"><b>Total Hours booked off={{$hours}}</b>
            </div>

          </section>
        </div>
      </div>
      <div id="vacation" class="candidate-screen tab-pane ">
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"> <br>
          </div>
          <section class="full-width">
            <table class="table table-bordered timesheet " id="vacation-table">
              <thead>
                <tr>
                  <th class="sl-no">#</th>


                  <th class="ts-gen">Description</th>
                  <th class="ts-gen">Type</th>
                  <th class="ts-gen">Vacation Pay Amount</th>
                  <th class="ts-gen">Pay Period</th>
                  <th class="ts-gen">Attachments</th>
                  <th class="ts-gen">Days Claimed</th>
                  <th class="ts-gen">Days Permitted</th>
                  <th class="ts-gen">Days Approved</th>
                  <th class="ts-gen">Days Rejected</th>
                  <th class="start-end-date">Request Date</th>
                  <th class="start-end-date">Request Time</th>
                  <th class="ts-gen">HR Associate</th>
                  <th class="ts-gen">Review Date</th>
                  <th class="ts-gen">Review Time</th>
                  <th class="ts-gen">Reviewed By</th>
                  <th class="note-header">Status</th>

                </tr>
              </thead>
            </table>
          </section>
        </div>
      </div>

    </div>
    <!-- </div> -->
  </div>

  {{--- START--  training Tab --}}
  <div id="training" class="container-fluid tab-pane fade">
    <div class="row">
      <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">

        <li class="nav-item success">
          <a class="nav-link " data-toggle="tab" href="#byTeams">
            <span> Teams </span>
          </a>
        </li>

        <li class="nav-item success">
          <a class="nav-link" data-toggle="tab" href="#byCourses">
            <span> Courses</span>
          </a>
        </li>

      </ul>
    </div>
    <div class="tab-content">
      <br><br>
      <div id="byTeams" class="tab-pane">
        <div class="col-xs-6 col-sm-6">

          <table class="table table-bordered" id="employees-team">
            <tr>
              <th style="color: #FFF;">Name</th>
              <th style="color: #FFF;">Parent Team</th>
              <th style="color: #FFF;">Allocated Date</th>
            </tr>
            @foreach($team_lists as $team)
            <tr>
              <td>{{$team->team->name}}</td>
              <td>@if($team->team->team){{$team->team->team->name}}@endif</td>
              <td>{{date('d-m-Y', strtotime($team->created_at))}}</td>
            </tr>

            @endforeach
          </table>

        </div>
      </div>

      <div id="byCourses" class="tab-pane">
        <table class="table table-bordered" id="employees-course-table">
          <thead>
            <tr>
              <th>#</th>
              <th class="sorting">Course Name</th>
              <th class="sorting">Course Type</th>
              <th class="sorting">Allocated On</th>
              <th class="sorting">Completed Date</th>
              <th>Completed Percentage</th>
              <th class="sorting">Status</th>
              <th class="sorting">Submitted Date</th>
              <th>Result</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
  {{--- END--  training Tab --}}
  @can('view_allocated_compliance_policy_in_employee_geomapping')
  <div id="compaliance" class="container-fluid tab-pane active hidden">
    <div class="tab-content">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"> <br>
      </div>
      <section class="full-width">
        {{-- <iframe id="iframe" src="{{ route('policy.dashboard') }}" width="100%" height="750px" frameborder="0"
        style="border:0"></iframe> --}}
        <div class="table_title">
          <h4>Compliance Module</h4>
        </div>
        <div class="row dashboard-row">
          {{-- <div class="col-sm-3 dashboard-box">
        <div class="progressbar-text">Compliance Module</div>
    </div> --}}
          <div class="col-sm-4 dashboard-box">
            <span class="chart"> {!! $policy_count_chart->render() !!}</span>
          </div>
          <div class="col-sm-4 dashboard-box">
            <span class="chart"> {!! $compliant_count_chart->render() !!}</span>
          </div>
          <div class="col-sm-4 dashboard-box">
            <span class="chart"> {!! $average->render() !!}</span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered" id="policy-table">
            <thead>
              <tr>
                <th class="sorting" width="10%">Policy Id</th>
                <th class="sorting" width="10%">Policy Name</th>
                <th class="sorting" width="20%">Policy Description</th>
                <th class="sorting" width="10%">Category</th>
                <th class="sorting" width="10%">Status</th>
                <th class="sorting" width="10%">Date Completed</th>
              </tr>
            </thead>
          </table>
        </div>
      </section>
    </div>
  </div>
  @endcan
</div>
</div>
</div>

@stop

@section('scripts')

<script>
  {!!Charts::assets() !!}
</script>
<script>
  $('.select2').select2();
  @can('view_allocated_compliance_policy_in_employee_geomapping')
  $(document).ready(function() {
    setTimeout(() => {
      var textId = [1, 6, 11];
      for (const i of textId) {
        $($("svg text")[i]).attr('y', 130);
      }
    }, 5000);

    $('.nav-tabs a[href="#compaliance"]').on('click', function (e) {
       $('#compaliance').removeClass('hidden');
    });
    $('.nav-tabs a:not([href="#compaliance"])').on('click', function (e) {
       $('#compaliance').addClass('hidden');
    });


    var table = $('#policy-table').DataTable({
      fixedHeader: true,
      processing: false,
      serverSide: true,
      responsive: true,
      ajax: " {{route('policyTable.list',$user->id)}}",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      order: [
        [1, "desc"]
      ],
      lengthMenu: [
        [10, 25, 50, 100, 500, -1],
        [10, 25, 50, 100, 500, "All"]
      ],

      columns: [{
          data: 'reference_code',
          name: 'reference_code',

        },
        {
          data: 'policy_name',
          name: 'policy_name',
        },
        {
          data: 'policy_description',
          name: 'policy_description',
        },
        {
          data: 'compliance_policy_category',
          name: 'compliance_policy_category',
        },
        {
          data: 'status',
          name: 'status',
        },
        {
          data: 'updated_at',
          name: 'updated_at',
        },
      ]
    });
  });
  @endcan

  $(function() {
    @canany(['view_all_performance_reports','view_allocated_performance_reports'])
    $('.select2').select2();
    var url = "{{ route('project-report.rating.list',[':startdate',':enddate',':project_id',':group_id',':emp_id']) }}";
    var emp_id = "{{ $user->id }}";
    url = url.replace(':startdate', 0);
    url = url.replace(':enddate', 0);
    url = url.replace(':project_id', 0);
    url = url.replace(':group_id', 0);
    url = url.replace(':emp_id', emp_id);
    $.fn.dataTable.ext.errMode = 'throw';
    try {
      table = $('#rating-table').DataTable({
        processing: false,
        fixedHeader: false,
        serverSide: true,
        responsive: true,


        ajax: url,


        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        // order: [
        //     [1, "desc"]
        // ],
        lengthMenu: [
          [10, 25, 50, 100, 500, -1],
          [10, 25, 50, 100, 500, "All"]
        ],

        columnDefs: [{
            width: '10%',
            targets: 0
          },
          {
            width: '10%',
            targets: 1
          },
          {
            width: '10%',
            targets: 2
          },
          {
            width: '16%',
            targets: 3
          },
          {
            width: '7%',
            targets: 4
          },
          {
            width: '7%',
            targets: 5
          },
          {
            width: '7%',
            targets: 6
          },
          {
            width: '1%',
            targets: 7
          },
          {
            width: '1%',
            targets: 8
          },
          {
            width: '1%',
            targets: 9
          },
          {
            width: '1%',
            targets: 10
          },
          {
            width: '1%',
            targets: 11
          },
          {
            width: '1%',
            targets: 12
          },
          {
            width: '20%',
            targets: 13
          },
          {
            width: '5%',
            targets: 14
          },
          {
            width: '2%',
            targets: 14
          },
        ],
        columns: [{
            data: 'project',
            name: 'projects.name'
          },
          {
            data: 'group',
            name: 'groupDetails.name'
          },
          {
            data: 'task_name',
            name: 'name'
          },
          {
            data: 'assignee',
            name: 'assignee',
            sortable:false,
            searchable:false
          },
          {
            data: 'created_at',
            name: 'created_at'
          },
          {
            data: 'due_date',
            name: 'due_date'
          },
          {
            data: 'rating_date',
            name: 'rated_at'
          },
          {
            data: 'deadline_rating_id',
            name: 'deadline_rating_id'
          },
          {
            data: 'value_add_rating_id',
            name: 'value_add_rating_id'
          },
          {
            data: 'initiative_rating_id',
            name: 'initiative_rating_id'
          },
          {
            data: 'commitment_rating_id',
            name: 'commitment_rating_id'
          },
          {
            data: 'complexity_rating_id',
            name: 'complexity_rating_id'
          },
          {
            data: 'efficiency_rating_id',
            name: 'efficiency_rating_id'
          },
          {
            data: 'rating_notes',
            name: 'rating_notes'
          },
          {
            data: 'average',
            name: 'average_rating'
          },
          {
            data: 'rating',
            name: 'average_rating',
            sortable: false
          },
        ]
      });

    } catch (e) {
      console.log(e.stack);
    }
    @endcan


    @canany(['view_timesheet_approval_compalince'])

    var url = "{{ route('employee.timesheet-approval-rating.list',[':emp_id']) }}";
    var emp_id = "{{ $user->id }}";
    url = url.replace(':emp_id', emp_id);
    $.fn.dataTable.ext.errMode = 'throw';
    try {
      var employeeTimesheetApprovaltable = $('#employee-approval-rating-table').DataTable({
        processing: false,
        fixedHeader: false,
        serverSide: true,
        responsive: true,
        ajax: url,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        // order: [
        //     [1, "desc"]
        // ],
        lengthMenu: [
          [10, 25, 50, 100, 500, -1],
          [10, 25, 50, 100, 500, "All"]
        ],
        select: {
                    style: 'os',
                    selector: 'td:first-child'
                },
        columns: [
          {
            data: 'id',
                render: function(o) {
                    return '<button data-id="'+o.id+'" class="btn fa fa-plus-square buttons"></button>';
                },
            orderable: false,
            className: 'details-control',
            data: null,
            defaultContent: ''
          },
          {
            data: 'pay_period_name',
            name: 'pay_period_name'
          },
          {
            data: 'start_date',
            name: 'start_date'
          },
          {
            data: 'end_date',
            name: 'end_date'
          },
          {
            data: 'average_rating',
            name: 'average_rating'
          },
        ]
      });

    } catch (e) {
      console.log(e.stack);
    }
        //     // Add event listener for opening and closing details
        //     $('#employee-approval-rating-table tbody').on('click', 'td.details-control', function() {
        //     var tr = $(this).closest('tr');
        //     var row = employeeTimesheetApprovaltable.row(tr);
        //     if (row.child.isShown()) {
        //         // This row is already open - close it
        //         tr.find('td.details-control').html('<button  class="btn fa fa-plus-square "></button>');
        //         row.child.hide();
        //         tr.removeClass('shown');
        //         refreshSideMenu();
        //     } else {
        //         // Open this parentNode
        //         tr.find('td.details-control').html('<button  class="btn fa fa-minus-square "></button>');
        //         row.child(format(row.data())).show();
        //         tr.addClass('shown');
        //         refreshSideMenu();
        //     }
        // });

        $('#employee-approval-rating-table tbody').on('click', 'td.details-control', function() {
            var id=$(this).closest('tr').find('.buttons').data('id');
            var tr = $(this).closest('tr');
            var row = employeeTimesheetApprovaltable.row(tr);
            if (row.child.isShown()) {
                tr.find('td.details-control').html('<button  class="btn fa fa-plus-square buttons" data-id=' + id + '></button>');
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                var view_url = '{{ route("timesheetapprovalbypayperiod.details",[":payperiod_id",":emp_id"]) }}';
                view_url = view_url.replace(':emp_id', emp_id);
                view_url = view_url.replace(':payperiod_id', id);
                $.ajax({
                type: 'GET',
                url: view_url,
                dataType: 'json',
                success: function (data) {
                   tr.find('td.details-control').html('<button  class="btn fa fa-minus-square buttons"  data-id=' + id + '></button>');
                   row.child( format(data)).show();
                   tr.addClass('shown');
                  },
                error: function () {}
                });

            }
            refreshSideMenu();
        });


                /* Formatting function for row details - modify as you need */
            function format(d) {
            if (d.cpids.length <= 0) {
                return '';
            }
            var tbody = '';
            var distinctItems = d.cpids;

            distinctItems.forEach(function(cpid) {
                    c_row = '';
                    c_row += '<td>' + cpid.customer_name + '</td>';
                    c_row += '<td>' + cpid.customer_no + '</td>';
                    c_row += '<td>' + cpid.employee_name + '</td>';
                    c_row += '<td>' + cpid.employee_no + '</td>';
                    c_row += '<td>' + cpid.payperiod_week + '</td>';
                    c_row += '<td>' + cpid.deadline_date +" "+cpid.deadline_time+'</td>';
                    c_row += '<td>' + cpid.timesheet_submission_date +" "+  cpid.timesheet_submission_time + '</td>';
                    c_row += '<td>' +  cpid.rating + '</td>';
                    c_row += '<td><a style="background:#ffd9b3;" href="#" onclick="test('+cpid.id+');" id ="ratingDelete_'+cpid.id+'" class="delete-child-row fa fa-trash-o" data-id=' +  cpid.id + '></a></td>';
                    tbody += '<tr>' + c_row + '</tr>';
            });

            return '<table class="DataTable subtable dataTable" id="rating-subtable">' +
                    '<tr>' +
                    '<th>Customer Name</th> <th>Customer No</th><th>Employee Name</th> <th>Employee No</th> <th>Week</th> <th>Deadline Date Time</th> <th>Timesheet Approval Date Time</th><th>Rating</th><th>Action</th>' +
                    '</tr>' +
                    '<tbody>' + tbody + '</tbody>' +
                    '</table>'
        }




    @endcan

    var table = $('#employees-table').DataTable({
      processing: false,
      fixedHeader: true,
      serverSide: true,
      responsive: true,
      ajax: {
        url: '{{ route('employeetimeoff-list',$user->id) }}',
      },

      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      order: [
        [0, "desc"]
      ],
      lengthMenu: [
        [10, 25, 50, 100, 500, -1],
        [10, 25, 50, 100, 500, "All"]
      ],

      columns: [

        {
          data: 'date',
          name: 'date'
        },
        {
          data: 'payperiod',
          name: 'payperiod'
        },
        {
          data: 'hours_off',
          name: 'hours_off'
        },
        {
          data: 'reason',
          name: 'reason'
        },
        {
          data: 'notes',
          name: 'notes'
        },
        {
          data: 'project_number',
          name: 'project_number'
        },
        {
          data: 'supervisor',
          name: 'supervisor'
        },


      ]
    });

    var employees_course_table = $('#employees-course-table').DataTable({
      processing: false,
      fixedHeader: true,
      serverSide: true,
      responsive: true,
      ajax: {
        url: '{{ route('employee.course-list',$user->id) }}',
      },

      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      order: [
        [0, "desc"]
      ],
      lengthMenu: [
        [10, 25, 50, 100, 500, -1],
        [10, 25, 50, 100, 500, "All"]
      ],

      columns: [

        {
          data: 'id',
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          },
          orderable: false
        },
        {
          data: 'course_title',
          name: 'course_title'
        },
        {
          data: null,
          render: function(o) {
            if (o.course_type_flag == 1) {
              return 'Mandatory'
            } else if (o.course_type_flag == 2) {
              return 'Recommended'
            } else {
              return ''
            }
          },
          name: 'course_type',
          orderable: false,
        },
        {
          data: 'alloted_date',
          name: 'alloted_date'
        },
        {
          data: 'completed_date',
          name: 'completed_date',
        },
        {
          data: 'completed_percentage',
          name: 'completed_percentage',
          orderable: false
        },
        {
          data: null,
          name: 'is_exam_pass',
          render: function(o) {

            return (o.is_exam_pass != null) ? o.is_exam_pass : null;
          }


        },
        {
          data: null,
          name: 'submitted_date',
          render: function(o) {

            return (o.submitted_date != null) ? formatDate(o.submitted_date) : null;
          }
        },
        {
          data: null,
          name: 'submitted_date',
          render: function(o) {
            var actions = '';
            if (o.submitted_date != null) {
              actions += '<a href="#" class="popupShow fa fa-info-circle fa-3x" data-user-id=' + o.user_id + ' data-course-id=' + o.course_id + '></a>';

            }
            return actions;
          },

        }


      ]
    });

    $('#employees-course-table').on('click', '.popupShow', function(e) {
      var user_id = $(this).data('user-id');
      var course_id = $(this).data('course-id');
      var base_url = "{{route('user-test-results.show',[':user_id',':course_id'])}}";
      var url1 = base_url.replace(':user_id', user_id);
      var url = url1.replace(':course_id', course_id);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'GET',
        success: function(data) {
          if (data.success) {
            var swal_html = '<div class="panel"> <div class="panel-body"><table align="center" class="table alert-table">';
            swal_html += '<thead><tr><th>Score</th><th>&nbspPercentage</td><th style="text-align: center; vertical-align: middle;">&nbspSubmitted Date and Time</th></tr></thead>';
            $.each(data.data, function(index, value) {
              swal_html += '<tr><td style="text-align: center; vertical-align: middle;">' + value.total_exam_score + '</td><td style="text-align: center; vertical-align: middle;">&nbsp' + Math.round(value.score_percentage) + '%</td><td style="text-align: center; vertical-align: middle;">&nbsp' + formatDate(value.submitted_at) + '</td></tr>';
            });
            swal_html += '</table></div></div>';
            swal({
              title: "Result History",
              text: swal_html,
              html: true
            });
          }

        },
        fail: function(response) {
          swal("Oops", "Something went wrong", "warning");
        },
        error: function(xhr, textStatus, thrownError) {
          associate_errors(xhr.responseJSON.errors, $form, true);
        },

      })
    })

    function formatDate(date) {
      console.log(date)
      var d = new Date(date);
      var options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
      };
      var today = new Date(date);
      return today.toLocaleDateString("en-US", options);
    }


    $('#profileTabs li:first-child a').tab('show');
    /* Posting data to EmployeeAllocationController - Start*/
    $('#performance-form').submit(function(e) {
      e.preventDefault();
      var $form = $(this);
      url = "{{ route('employee.rating') }}";
      var formData = new FormData($('#performance-form')[0]);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data: formData,
        success: function(data) {
          if (data.success) {
            swal({
                title: "Saved",
                text: "The record has been saved",
                type: "success"
              },
              function() {
                $("#myModal").modal('hide');
                location.reload(true);
              });
          } else {
            console.log(data);
            swal("Oops", "The record has not been saved", "warning");
          }
        },
        fail: function(response) {
          console.log(response);
          swal("Oops", "Something went wrong", "warning");
        },
        error: function(xhr, textStatus, thrownError) {
          associate_errors(xhr.responseJSON.errors, $form);
        },
        contentType: false,
        processData: false,
      });
    });
  });
  /* Posting data to EmployeeAllocationController - End*/

 function test(timesheetRatingid){
    var table = $('#employee-approval-rating-table').DataTable();
    var id = timesheetRatingid;
    var base_url = "{{ route('employee.timesheet-approval-rating.destroy',':id') }}";
    var url = base_url.replace(':id', id);
    console.log(table,id,base_url,url);
    swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
          },
          function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal("Deleted", "Record has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "Cannot able to delete ", "warning");
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
 }




  $(function() {
    /******** Leave of Abscence Table ******************************/
    var abscence_table = '';
    $.fn.dataTable.ext.errMode = 'throw';
    try {
      abscence_table = $('#table-id').DataTable({
        //bProcessing: false,
        processing: false,
        //serverSide: true,
        //fixedHeader: true,
        responsive: true,
        pageLength: 10,
        //dom: 'Blfrtip',
        buttons: [{
            extend: 'pdfHtml5',
            pageSize: 'A2',
          },
          {
            extend: 'excelHtml5',
          },
          {
            extend: 'print',
            pageSize: 'A2',
          },
        ],
        "columnDefs": [{
            className: "nowrap",
          },
          {
            "width": "20%",
            //"targets": 16
          }

        ],
        ajax: {
          "url": "{{ route('time-off.listSingle',$user->id) }}",
          data: function(d) {
            d.type = {{LEAVE_OF_ABSENCE}};

          },
          "error": function(xhr, textStatus, thrownError) {
            if (xhr.status === 401) {
              window.location = "{{ route('login') }}";
            }
          }
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        order: [
          // [7, 'desc']
        ],
        columns: [{
            data: 'updated_at',
            name: 'updated_at',
            visible: false
          },

          {
            data: 'nature_of_request',
            name: 'description'
          },
          {
            //data: 'leave_reason.reason',
            data: null,
            render: function(o) {
              if (o.category) {
                return o.category.type;
              } else {
                return '';
              }
            },
            name: 'type'
          },


          {
            data: null,
            name: 'attachments',
            sortable: false,
            render: function(o) {
              if (o.attachments != "") {
                var link = '';
                for (var i = 0; i < o.attachments.length; i++) {
                  //     link += '<a title="Download" href="' + o.attachments[i].at_details2 + '">'+o.attachments[i].attachment.original_name+'</a>';
                  var view_url = '{{ route("filedownload", [":id",":module"]) }}';
                  view_url = view_url.replace(':id', o.attachments[i].attachment_id);
                  view_url = view_url.replace(':module', 'employeeTimeOff');
                  link += '<a class="leave_of_abscence" title="Download" href="' + view_url + '">' + o.attachments[i].attachment.original_name + '</a><br>';
                }
                return link;

              } else {
                return '';
              }
              return '';
            },
          },

          {
            data: 'days_requested',
            name: 'days_requested'
          },
          {
            //data: 'days_remaining',
            data: null,
            render: function(o) {
              if (o.category) {
                return o.category.allowed_days;
              } else {
                return '';
              }
            },
            name: 'days_permitted'
          },
          {
            data: 'days_approved',
            name: 'days_approved'
          },
          {
            data: 'days_rejected',
            name: 'days_rejected'
          },
          {
            data: null,
            render: function(data, type, row) {
              var datetime_str = "";
              datetime_str = datetimeformat(data.created_at, 2);
              return datetime_str;
            },
            name: 'request_date'
          },
          {
            data: null,
            render: function(data, type, row) {
              var datetime_str = "";
              datetime_str = datetimeformat(data.created_at, 3);
              return datetime_str;
            },
            name: 'request_time'
          },
          {
            //data: 'hr.user.first_name',
            data: null,
            render: function(data) {
              if (data.hr.trashed_user) {
                if (data.hr.trashed_user.last_name) {
                  return data.hr.trashed_user.first_name + ' ' + data.hr.trashed_user.last_name;
                } else {
                  return data.hr.trashed_user.first_name;
                }
              }
            },
            name: 'hr'
          },
          {
            data: null,
            render: function(data, type, row) {
              if (data.approved == 0 || data.approved == 1) {
                var datetime_str = "";
                datetime_str = datetimeformat(data.latest_log.created_at, 2);
                return datetime_str;
              } else {
                return '';
              }
            },
            name: 'review_date'
          },

          {
            data: null,
            render: function(data, type, row) {
              if (data.approved == 0 || data.approved == 1) {
                var datetime_str = "";
                datetime_str = datetimeformat(data.latest_log.created_at, 3);
                return datetime_str;
              } else {
                return '';
              }
            },
            name: 'review_time'
          },
          {
            data: null,
            render: function(data, type, row) {
              if (data.approved == 0 || data.approved == 1) {
                if (data.latest_log.created_by.trashed_user.last_name) {
                  return data.latest_log.created_by.trashed_user.first_name + ' ' + data.latest_log.created_by.trashed_user.last_name;
                } else {
                  return data.latest_log.created_by.trashed_user.first_name;
                }
              } else {
                return '';
              }
            },
            name: 'reviewed_by',
          },
          {
            data: null,
            name: 'status',
            orderable: false,
            sortable: false,
            render: function(o) {
              var actions = '';
              if (o.approved == 0) {
                return 'Rejected'

              } else if (o.approved == 1) {
                return 'Approved'
              } else {
                return 'Pending'
              }

              //actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
              @can('lookup-remove-entries') @endcan
              //return actions;
            },
          }
        ]
      });
      abscence_table.on('draw', function() {
        refreshSideMenu();
      });

    } catch (e) {
      console.log(e.stack);
    }


    ///////////// vacation Table ///////////////////////

    var vacation_table = '';
    $.fn.dataTable.ext.errMode = 'throw';
    try {
      vacation_table = $('#vacation-table').DataTable({
        //bProcessing: false,
        processing: false,
        //serverSide: true,
        //fixedHeader: true,
        responsive: true,
        pageLength: 10,
        //dom: 'Blfrtip',
        buttons: [{
            extend: 'pdfHtml5',
            pageSize: 'A2',
          },
          {
            extend: 'excelHtml5',
          },
          {
            extend: 'print',
            pageSize: 'A2',
          },
        ],
        "columnDefs": [{
            className: "nowrap",
          },
          {
            "width": "20%",
            //"targets": 16
          }

        ],
        ajax: {
          "url": "{{ route('time-off.listSingle',$user->id) }}",
          data: function(d) {
            d.type = {{VACATION}};

          },
          "error": function(xhr, textStatus, thrownError) {
            if (xhr.status === 401) {
              window.location = "{{ route('login') }}";
            }
          }
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        order: [
          // [7, 'desc']
        ],
        columns: [{
            data: 'updated_at',
            name: 'updated_at',
            visible: false
          },

          {
            data: 'nature_of_request',
            name: 'description'
          },
          {
            //data: 'leave_reason.reason',
            data: null,
            render: function(o) {
              if (o.category) {
                return o.category.type;
              } else {
                return '';
              }
            },
            name: 'type'
          },
          {
            data: 'vacation_pay_amount',
            name: 'vacation_pay_amount'
          },
          {
            //data: 'leave_reason.reason',
            data: null,
            render: function(o) {
              if (o.payperiod != null) {
                return o.payperiod.pay_period_name;
              } else {
                return '';
              }
            },
            name: 'pay_period_name'
          },

          {
            data: null,
            name: 'attachments',
            sortable: false,
            render: function(o) {
              if (o.attachments != "") {
                var link = '';
                for (var i = 0; i < o.attachments.length; i++) {
                  //     link += '<a title="Download" href="' + o.attachments[i].at_details2 + '">'+o.attachments[i].attachment.original_name+'</a>';
                  var view_url = '{{ route("filedownload", [":id",":module"]) }}';
                  view_url = view_url.replace(':id', o.attachments[i].attachment_id);
                  view_url = view_url.replace(':module', 'employeeTimeOff');
                  link += '<a class="leave_of_abscence" title="Download" href="' + view_url + '">' + o.attachments[i].attachment.original_name + '</a><br>';
                }
                return link;

              } else {
                return '';
              }
              return '';
            },
          },

          {
            data: 'days_requested',
            name: 'days_requested'
          },
          {
            //data: 'days_remaining',
            data: null,
            render: function(o) {
              if (o.category) {
                return o.category.allowed_days;
              } else {
                return '';
              }
            },
            name: 'days_permitted'
          },
          {
            data: 'days_approved',
            name: 'days_approved'
          },
          {
            data: 'days_rejected',
            name: 'days_rejected'
          },
          {
            data: null,
            render: function(data, type, row) {
              var datetime_str = "";
              datetime_str = datetimeformat(data.created_at, 2);
              return datetime_str;
            },
            name: 'request_date'
          },
          {
            data: null,
            render: function(data, type, row) {
              var datetime_str = "";
              datetime_str = datetimeformat(data.created_at, 3);
              return datetime_str;
            },
            name: 'request_time'
          },
          {
            //data: 'hr.user.first_name',
            data: null,
            render: function(data) {
              if (data.hr.trashed_user) {
                if (data.hr.trashed_user.last_name) {
                  return data.hr.trashed_user.first_name + ' ' + data.hr.trashed_user.last_name;
                } else {
                  return data.hr.trashed_user.first_name;
                }
              }
            },
            name: 'hr'
          },
          {
            data: null,
            render: function(data, type, row) {
              if (data.approved == 0 || data.approved == 1) {
                var datetime_str = "";
                datetime_str = datetimeformat(data.latest_log.created_at, 2);
                return datetime_str;
              } else {
                return '';
              }
            },
            name: 'review_date'
          },

          {
            data: null,
            render: function(data, type, row) {
              if (data.approved == 0 || data.approved == 1) {
                var datetime_str = "";
                datetime_str = datetimeformat(data.latest_log.created_at, 3);
                return datetime_str;
              } else {
                return '';
              }
            },
            name: 'review_time'
          },
          {
            data: null,
            render: function(data, type, row) {
              if (data.approved == 0 || data.approved == 1) {
                if (data.latest_log.created_by.trashed_user.last_name) {
                  return data.latest_log.created_by.trashed_user.first_name + ' ' + data.latest_log.created_by.trashed_user.last_name;
                } else {
                  return data.latest_log.created_by.trashed_user.first_name;
                }
              } else {
                return '';
              }
            },
            name: 'reviewed_by',
          },
          {
            data: null,
            name: 'status',
            orderable: false,
            sortable: false,
            render: function(o) {
              var actions = '';
              if (o.approved == 0) {
                return 'Rejected'

              } else if (o.approved == 1) {
                return 'Approved'
              } else {
                return 'Pending'
              }

              //actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
              @can('lookup-remove-entries') @endcan
              //return actions;
            },
          }
        ]
      });

      vacation_table.on('draw', function() {
        refreshSideMenu();
      });

    } catch (e) {
      console.log(e.stack);
    }

    // $("#table-id_wrapper").addClass("no-datatoolbar datatoolbar");

    // /*Payperiod dropdown change event - Start*/
    // $("#payperiod-filter").change(function(){
    //     table.ajax.reload();
    // });
    /*Payperiod dropdown change event - End*/



    $('#employees-team').DataTable({});

  });
  /*Function to format datetime - Start*/
  function datetimeformat(date_obj, onlytime) {
    if (onlytime == 1) {
      var hr_split_arr = date_obj.split(":");
      datetime_str = hr_split_arr[0] + ':' + hr_split_arr[1];
      return datetime_str;
    }
    if (onlytime == 2) {
      var hr_split_arr = date_obj.split(" ");
      datetime_str = hr_split_arr[0]; //+':'+hr_split_arr[1];
      return datetime_str;
    }
    if (onlytime == 3) {
      var hr_split_arr = date_obj.split(" ");
      datetime_str = hr_split_arr[1];
      return datetime_str;
    }
    if (date_str < 10) date_str = '0' + date_str;
    var month_str = (date_obj.getMonth()) + 1;
    if (month_str < 10) month_str = '0' + month_str;
    var year_str = date_obj.getFullYear();
    var hour_str = date_obj.getHours();
    if (hour_str < 10) hour_str = '0' + hour_str;
    var minute_str = date_obj.getMinutes();
    if (minute_str < 10) minute_str = '0' + minute_str;
    var datetime_str = year_str + '-' + month_str + '-' + date_str + ' ' + hour_str + ':' + minute_str;
    return datetime_str;
  }

  $('#employee_rating_lookup_id').on('change', function() {
    $('#description-div').hide();
    $('#policy_id').empty().append($("<option></option>").attr("value", 0).text('Please Select'));
    var id = $(this).val();
    var base_url = "{{route('employee.ratings-getPolicy',':id')}}";
    var url = base_url.replace(':id', id);
    $.ajax({
      url: url,
      type: 'GET',
      success: function(data) {
        policies = data;
        $.each(data, function(index, policy) {
          $('#policy_id')
            .append($("<option></option>")
              .attr("value", policy['id'])
              .text(policy['name']));
        });

      }
    });
  })

  $('#policy_id').on('change', function() {
    var id = $(this).val();
    $.each(policies, function(index, policy) {
      if (policy['id'] == id) {
        $('#description-div').show();
        $('#description').text(policy['description']);
      }
    });

  });

  $('#notify_employee').change(function() {
    if (!$(this).is(':checked')) {
      swal({
          title: "Are you sure you want to turn off notification?",
          text: "If turned off, employee will not be notified of this rating.",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-success",
          cancelButtonText: "Cancel",
          confirmButtonText: "Yes, turn off",
          showLoaderOnConfirm: true,
          closeOnConfirm: true
        },
        function() {
          $('#notify_employee').prop('checked', false);
        });
      $('#notify_employee').prop('checked', true);
    }

  });

  const rc = {
    init(){
      let root = this;
        $('.js-remove-rating').on('click',function(){
          console.log('clicked');
          root.onDeleteRating(this);
        });
    },
    onDeleteRating(el){
      let id = $(el).data('id');
      let type =  $(el).data('type');
      let url = "{{ route('manager-rating.delete',':id') }}";
      let ers = true;
      if(type === 'clnt'){
         url = "{{ route('client-rating.delete',':id') }}";
         ers = false;
      }
      url = url.replace(':id', id);

      $.ajax({
          url: url,
          type: 'DELETE',
          headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (data) {
              if (data.success) {
                  swal("Deleted", "Rating deleted successfully", "success");
                 let el = '#' + (ers ? 'ers-'+id : 'crs-'+id);
                 $(el).hide();
              }
            else {
                    swal("Warning", "Something went wrong.", "warning");
              }
          },
          contentType: false,
          processData: false,
      });
    }
  }
  
  // (function($){
    rc.init();
  // });




</script>
<style type="text/css">
  .table-bordered th {
    border: 1px solid #e9ecef !important;
  }

  .alert-table th,
  td {
    color: #343a40;
  }

  .candidate-screen-head1 {
    color: #000000;
    margin: 10px 0px;
    padding: 10px 5px;
  }

  .chart {
    display: inline-block;
  }

  .dashboard-row {
    background-color: #f26222;
    margin-bottom: 1%;
    margin-left: 0px;
    margin-right: 0px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)
  }

  .dashboard-box {
    border-right: 1px solid;
    padding-bottom: 1%;
    text-align: center;
  }

  div.hidden
  {
    display: hidden;
    opacity: 0
  }
</style>
@stop
