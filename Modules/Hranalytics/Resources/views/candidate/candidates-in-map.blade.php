@extends('layouts.app')
@section('content')
<style>
    .profileImage {
        width: 12rem;
        height: 12rem;
        border-radius: 50%;
        font-size: 2.5rem;
        color: #fff;
        text-align: center;
        line-height: 12rem;
        margin: 1rem 0;
    }

    .popup-div-content {
        white-space: nowrap;
    }

    .user-image-div {
        text-align: center !important;
    }

    .candidate-filter-form {
        max-height: 65vh;
        overflow-x: hidden;
        overflow-y: scroll;
        padding: 5px;
    }

    #candidate_stage .select2-selection__rendered{
        color: white !important;
    }
</style>
<div id="candidate_map">
    <div class="table_title">
        <h4>Candidate Geomapping </h4>
    </div>
    <div class="table-responsive hide-scroll" id="candidate-geo-mapping-tbl">
        <table class=" table table-bordered" id="candidate-geo-mapping">
            <thead>
                <th>Job Id</th>
                <th>Area Manager</th>
                <th>Project Number</th>
                <th>Client</th>
                <th>Position Requested</th>
                <th>Posts</th>
                <th>Rationale</th>
                <th>Position Type</th>
                <th>Date Required</th>
                <th>Wage Low</th>
                <th>Wage High</th>
                <th>Status</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="{{ route('job.view',$job->id)}}">{{$job->unique_key}}</a>
                    </td>
                    <td>{{$job->area_manager}}</td>
                    <td>{{$job->customer->project_number}}</td>
                    <td>{{$job->customer->client_name}}</td>
                    <td>{{$job->positionBeeingHired->position}}</td>
                    <td>{{$job->no_of_vaccancies}}</td>
                    <td>{{$job->reason->reason}}</td>
                    <td>{{$job->assignmentType->type}}</td>
                    <td>{{date('F j, Y', strtotime($job->required_job_start_date))}}</td>
                    <td>${{ number_format($job->wage_low, 2) }}</td>
                    <td>${{ number_format($job->wage_high, 2) }}</td>
                    <td>{{ucfirst($job->status)}}</td>
                </tr>
            </tbody>
        </table>
        <div style="margin-top:-30px;"><strong>Showing <span id="candidate_list_count">0</span> records</strong></div>
    </div>
    <div id="wrapper" class="toggled siderbar-panel">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <div class="clearfix"></div>
                <input type="text" id="searchbox" class="form-control search-input" placeholder="Search">
                <div class="second-child"></div>
                <div id="candidate-data-left-panel">

                    @if(count($candidates)>0)
                    @foreach($candidates as $i=>$candidate)
                    <li class="candidate_names {{'candidate_li_'.$candidate->id}}" value="{{$candidate->id}}">
                            @php
                            $last_tracking_step = (null!=($candidate->lastTrack) && null!=($candidate->lastTrack->tracking_process))? $candidate->lastTrack->tracking_process->process_steps : '--'; //Candidate Transitioned
                            //dump($last_tracking_step);
                            @endphp
                        <i style="color:@if(null!=$candidate->termination) black @elseif(stripos($last_tracking_step,'transitioned')!=false) @if($candidate->availability->current_availability == "Part-Time (Less than 40 hours per week)") blue @else green @endif  @else red  @endif !important;" class="fa fa-map-marker float-right location-arrow" aria-hidden="true"></i>
                        <a target="_blank" onmouseover="openInfoWindow({{$i+1}});" href="{{ route('candidate.view', [$candidate->id,$candidate->latestJobApplied->job_id]) }}">{{ ucfirst($candidate->name) }}</a>
                    </li>
                    @endforeach
                    @else
                    <li> No records found </li>
                    @endif
                </div>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
        <div class="mapping mapping-ie">
            <a class="navbar-brand" href="#menu-toggle" id="menu-toggle">
                <i class="fa fa-caret-left fa-2x" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    <div id="view-details" class="toggled filter-details" style="display: none;">
        <div id="sidebar-view-details" class="hide-vertical-scroll">
            <h4 class="padding-top-20">Filter Criteria</h4>
            {{ Form::open(array('url'=>route("candidate.plot-in-map-with-customer",$job->id),'id'=>'filtering-form','method'=> 'GET', 'class' => 'candidate-filter-form')) }}
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Location</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::text('location',old('location',$request->get('location')),array('class' => 'form-control','placeholder'=>'City')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Availability</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('availability',[null=>'Please Select','Part-Time (Less than 40 hours per week)'=>'Part Time','Full-Time (Around 40 hours per week)'=>'Full Time'],old('availability',$request->get('availability')),array('class'=>'form-control availability')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Job Applied</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('job_applied',[null=>'Please Select'] + $lookups['client_name'],old('job_applied',$request->get('job_applied')),array('class'=>'form-control client-select')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Low Wage Expectation</label>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::select('wage_low_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('wage_low_condition',$request->get('wage_low_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('wage_low',old('wage_low',$request->get('wage_low')),array('class ' => 'form-control currency-input')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">High Wage Expectation</label>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::select('wage_high_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('wage_high_condition',$request->get('wage_high_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('wage_high',old('wage_high',$request->get('wage_high')),array('class ' => 'form-control currency-input')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Current Wage</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::select('current_wage_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('current_wage_condition',$request->get('current_wage_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('current_wage',old('current_wage',$request->get('current_wage')),array('class ' => 'form-control currency-input')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Case Study Score</label>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::select('candidate_score_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('candidate_score_condition',$request->get('candidate_score_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::text('candidate_score',old('candidate_score',$request->get('candidate_score')),array('class ' => 'form-control ')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Years of Experience</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::select('years_experience_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('years_experience_condition',$request->get('years_experience_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('years_experience',old('years_experience',$request->get('years_experience')),array('class ' => 'form-control ')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Age</label>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::number('age_from',old('age_from',$request->get('age_from')),array('class ' => 'form-control', 'min' => 0, 'max' => 100, 'placeholder' => 'Age From')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::number('age_to',old('age_to',$request->get('age_to')),array('class ' => 'form-control', 'min' => 0, 'max' => 100, 'placeholder' => 'Age To')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Date of Application</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::select('application_date_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('application_date_condition',$request->get('application_date_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('application_date',old('years_experience',$request->get('application_date')),array('class ' => 'form-control datepicker')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row " id="candidate_stage">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Candidate Stage</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                {{ Form::select('candidate_stage[]',$trackingProcess,old('candidate_stage',$request->get('candidate_stage')),array('class '=>'form-control candidate_stage client-select','multiple'=>"multiple")) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Attend Orientation</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('orientation',[null=>'Please Select','1' => 'Yes', '0' => 'No'],old('orientation',$request->get('orientation')),array('class'=>'form-control orientation')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Willing To Work As Spare</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('position_availibility', [null => 'Please Select', '1' => 'I\'m ok to start as a floater/spare until a full time position comes up', '2' => 'I\'m only interested in the position I\'ve applied for'],old('position_availibility',$request->get('position_availibility')),array('class'=>'form-control position_availibility')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Hour Per Week Willing To Work</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::select('floater_hours_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('floater_hours_condition',$request->get('floater_hours_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('floater_hours',old('floater_hours',$request->get('floater_hours')),array('class ' => 'form-control ')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">How Soon Can You Start ?</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('starting_time', [null => 'Please Select', "1" => "I could start as soon as possible (less than 1 days notice)", "2" => "I could start with 1-3 days notice", "3" => "I could start with 3 to 5 days notice", "4" => "I need to provide atleast 5 to 10 days notice"],old('starting_time',$request->get('starting_time')),array('class'=>'form-control starting_time')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Guard License Expiry</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::select('guard_license_expiry_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('guard_license_expiry_condition',$request->get('guard_license_expiry_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('guard_license_expiry',old('guard_license_expiry',$request->get('guard_license_expiry')),array('class ' => 'form-control datepicker')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">CPR Expiry</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::select('cpr_expiry_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('cpr_expiry_condition',$request->get('cpr_expiry_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('cpr_expiry',old('cpr_expiry',$request->get('cpr_expiry')),array('class ' => 'form-control datepicker')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">First Aid Expiry</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::select('first_aid_expiry_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('first_aid_expiry_condition',$request->get('first_aid_expiry_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('first_aid_expiry',old('first_aid_expiry',$request->get('first_aid_expiry')),array('class ' => 'form-control datepicker')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Work Status</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('work_status',[null=>'Please Select', 'Canadian Citizen'=>'Canadian Citizen', 'Landed Immigrant'=>'Landed Immigrant', 'Permanent Resident'=>'Permanent Resident'],old('work_status',$request->get('work_status')),array('class'=>'form-control work_status')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Years in Canada</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::select('years_canada_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('years_canada_condition',$request->get('years_canada_condition')),array('class '=>'form-control ')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('years_canada',old('years_canada',$request->get('years_canada')),array('class ' => 'form-control ')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Drivers License</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('drivers_license',[null=>'Please Select','I have a valid G1 license'=>'I have a valid G1 license','I have a valid G2 license'=>'I have a valid G2 license', 'I have a full class G license'=>'I have a full class G license'],old('drivers_license',$request->get('drivers_license')),array('class'=>'form-control drivers_license')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Use of Force</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('use_of_force',[null=>'Please Select', 'Yes'=>'Yes', 'No'=>'No'],old('use_of_force',$request->get('use_of_force')),array('class'=>'form-control use_of_force')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">English Speaking</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('english',[null=>'Please Select', 'A - Limited - I am just learning the language.'=>'A - Limited - I am just learning the language.','B - Functional - this is my second language but I can get by.'=>'B - Functional - this is my second language but I can get by.','C - Fluent - this is my native language.'=>'C - Fluent - this is my native language.','D - No Knowledge.'=>'D - No Knowledge.'],old('english',$request->get('english')),array('class'=>'form-control english')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">French Speaking</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('french',[null=>'Please Select', 'A - Limited - I am just learning the language.'=>'A - Limited - I am just learning the language.','B - Functional - this is my second language but I can get by.'=>'B - Functional - this is my second language but I can get by.','C - Fluent - this is my native language.'=>'C - Fluent - this is my native language.','D - No Knowledge.'=>'D - No Knowledge.'],old('french',$request->get('french')),array('class'=>'form-control french')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Vet</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('vet',[null=>'Please Select', 'Yes'=>'Yes', 'No'=>'No'],old('vet',$request->get('vet')),array('class'=>'form-control vet')) }}
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left ">Indigienous</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('indigienous',[null=>'Please Select', 'Yes'=>'Yes', 'No'=>'No'],old('indigienous',$request->get('indigienous')),array('class'=>'form-control indigienous')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Last Update</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::select('last_update_date_condition',['>='=>'>=','<='=>' <=','='=>' is '],old('last_update_date_condition',$request->get('last_update_date_condition')),array('class '=>'form-control last_update_date_condition')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('last_update_date',old('last_update_date',$request->get('last_update_date')),array('class ' => 'form-control datepicker last_update_date')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Personality Type</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('personality_type',[null=>'Please Select'] + $personalityTypes,old('personality_type',$request->get('personality_type')),array('class '=>'form-control client-select')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row ">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Career Interest</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('career_interest',[null=>'Please select',"1 - Commissionaires is a temporary stop in my career. I have no long term plans."=>"1 - Commissionaires is a temporary stop in my career. I have no long term plans.","2 - I would be interested in exploring a longer term career at Commissionaires."=>"2 - I would be interested in exploring a longer term career at Commissionaires.","3 - I am interested in a long term career with Commissionaires."=>"3 - I am interested in a long term career with Commissionaires.","4 - Commissionaires is strategic to my long term career in security."=>"4 - Commissionaires is strategic to my long term career in security."],old('career_interest',$request->get('career_interest')),array('class '=>'form-control career_interest')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="text-center margin-bottom-5">
                {{ Form::submit('Filter', array('class'=>'btn submit',))}}
                {{ Form::reset('Reset', array('class'=>'btn cancel reset',))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <!-- /#wrapper -->
    <div class="embed-responsive embed-responsive-4by3">
        <div id="map" class="embed-responsive-item">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d i n g . . . . . . </div>
    </div>
</div>

@stop
@section('scripts')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script type="text/javascript">
    var markers = [];
    var job = {!! json_encode($job) !!};

    $('#candidate-geo-mapping').dataTable({
        "bInfo" : false,
        dom: 'Blfrtip',
            buttons: [

                 {
                text: 'Excel',
                    action: function ( e, dt, node, config ) {
                        e.preventDefault();
                        getData();
                    }
                },
            ],
    });

    function getData(){
        var candidate_data= $('.candidate_names:not([style*="display: none"])');
        var data= $('.candidate_names:not([style*="display: none"])').length;
        var customerId=job.customer.id;
        var export_array=[];
        for(i=0;i<data;i++)
        {
            card_value = candidate_data.eq(i).val();
            export_array.push(card_value);
        }
        $.ajax({
                type: "POST",
                url: "{{route('candidate.candidateGeomapping-storeCandidateSessionalData')}}",
                data: {'export_array': export_array,'customerId':customerId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    url="{{ route('candidate.candidateGeomapping-export') }}"
                    window.location.href = url;
                }
            });
    }

    function initMap() {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        var customer = {!! json_encode($job->customer) !!};
        var candidates = {!! json_encode($candidates) !!};

        let candidatesArray = [];
        let last_update_date = $('.last_update_date').val();
        let last_update_date_condition = $('.last_update_date_condition').val();
        if(last_update_date != null && last_update_date != "" && last_update_date != undefined) {
            let filterDate = Date.parse(last_update_date);
            $.each(candidates, function(i, item) {
                if(item.last_track) {
                    let itemDate = Date.parse(item.last_track.completion_date);
                    let lastUpdateStatus = true;
                    if((last_update_date_condition == "<=") && (itemDate <= filterDate)) {
                        lastUpdateStatus = false;
                    }else if((last_update_date_condition == ">=") && (itemDate >= filterDate)) {
                        lastUpdateStatus = false;
                    }else if((last_update_date_condition == "=") && (itemDate == filterDate)){
                        lastUpdateStatus = false;
                    }

                    if(lastUpdateStatus) {
                        let candidate_id = parseInt(item.last_track.candidate_id);
                        candidatesArray.push(candidate_id);
                    }
                }else{
                    let candidate_id = parseInt(item.id);
                    candidatesArray.push(candidate_id);
                }
            });
        }

        var view_url = "{{ route('candidate.view',[':candidate_id',':job_id']) }}";
        var track_url = "{{ route('candidate.track',[':candidate_id',':job_id']) }}";
        var infowindow = new google.maps.InfoWindow();
        var marker, i, icon;
        var head = document.getElementsByTagName('head')[0];
        // Save the original method
        var insertBefore = head.insertBefore;
        // Replace it!
        head.insertBefore = function (newElement, referenceElement) {
            if (newElement.href && newElement.href.indexOf('//fonts.googleapis.com/css?family=Roboto') > -1) {
                console.info('Prevented Roboto from loading');
                return;
            }
            insertBefore.call(head, newElement, referenceElement);
        };
        if(customer.lat==null || customer.lng==null){
            mapCenter = getLocationCoordinate(customer.postal_code);
            if(mapCenter!=null){
                updateLatLong('cus',"{{route('location.store')}}",customer.id,mapCenter);
            }else{
                // console.log('Unable to locate customer now-default to canada');
                mapCenter = {lat: {{config('globals.map_default_center_lat')}}, lng: {{config('globals.map_default_center_lng')}}};
            }
        }else{
                mapCenter = {lat: Number(customer.lat), lng: Number(customer.lng)};
        }
        {!!\App\Services\HelperService::googleAPILog('map','Modules\Hranalytics\Resources\views\candidate\candidates-in-map')!!}
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: mapCenter,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl   : false,
            panControl       : false,
            gestureHandling  : "greedy",
        });
        marker = new google.maps.Marker({
                position: mapCenter,
                map: map,
                icon:'https://maps.google.com/mapfiles/ms/micons/blue-pushpin.png',
                /*content:'<div id="content" style="min-width:500px;">' +
                        '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;' + customer.client_name + '</h4>' +
                        '<div id="bodyContent">' +
                        '<label style="width:100%;"><div class="row"  style="flex-wrap: nowrap;width: 100%;">'+
                        '<span class="col-sm-4 col-xs-4 float-left p0">Client Name</span> <span class="float-left p0">'+customer.client_name+
                        '</span></div><div class="row"  style="flex-wrap: nowrap;width: 100%;">'+
                        '<span class="col-sm-4 float-left p0">Project Number</span><span class="float-left p0">'+customer.project_number+
                        '</span></div><div class="row" style="flex-wrap: nowrap;width: 100%;">'+
                        '<span class="col-sm-4 float-left p0">Client Address</span> <span class="float-left p0">'+customer.address+','+customer.city+','+customer.postal_code+'</span></div></label>' +
                        '</div>' +
                        '</div>',*/
                content: '<div id="content" style="min-width:500px;">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;' + customer.client_name + '</h4>' +
                            '<div id="bodyContent">' +
                            '<label style="width:100%;"><span class="col-sm-7 col-7 float-left p0 map-label">Full Name</span> <span class="col-sm-5 col-5 float-left p0 map-disc">'+customer.client_name+
                            '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">Address</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+customer.project_number+
                            '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">City</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+customer.address+','+customer.city+','+customer.postal_code+
                            '</span></label>' +
                            '</div>' +
                            '</div>',
            });
        markers.push(marker);
        $.each(candidates, function(i, item) {
            let candidate_stage = $('.candidate_stage').val();
            if(candidate_stage != "" && (item.last_track) && (item.last_track.tracking_process) && (!candidate_stage.includes((item.last_track.tracking_process.id).toString()))) {
                let candidate_id = parseInt(item.last_track.candidate_id);
                $('li.candidate_li_'+candidate_id).remove();
                return;
            }

            if(item.last_track) {
                let candidate_id = parseInt(item.last_track.candidate_id);
                if(last_update_date != null && last_update_date != "" && last_update_date != undefined && candidatesArray.includes(candidate_id)) {
                    $('li.candidate_li_'+candidate_id).remove();
                    return;
                }
            }else{
                let candidate_id = parseInt(item.id);
                if(last_update_date != null && last_update_date != "" && last_update_date != undefined && candidatesArray.includes(candidate_id)) {
                    $('li.candidate_li_'+candidate_id).remove();
                    return;
                }
            }

            if(item.geo_location_lat==null || item.geo_location_long==null)
            {
                position = getLocationCoordinate(item.postal_code);
                if(position != '' && position != null){
                    updateLatLong('cand',"{{route('location.store')}}",item.id,position);
                }
            }else{
                position = {lat: Number(item.geo_location_lat), lng: Number(item.geo_location_long)};
            }
            url = view_url.replace(':candidate_id', item.id);
            url = url.replace(':job_id', item.latest_job_applied.job_id);
            trackurl = track_url.replace(':candidate_id', item.id);
            trackurl = trackurl.replace(':job_id', item.latest_job_applied.job_id);
            last_tracking_step = (null!=(item.last_track) && null!=(item.last_track.tracking_process))? item.last_track.tracking_process.process_steps : '--'; //Candidate Transitioned
            last_tracking_step = last_tracking_step.toLowerCase();
            icon = "{{ asset('images/markers/red-dot.png') }}";
            if(null!=item.termination)
            {
                icon = "{{ asset('images/markers/black-dot.png') }}";
            }else if( last_tracking_step.indexOf('transitioned') >= 0){
                if(item.availability.current_availability == "Part-Time (Less than 40 hours per week)"){
                    icon = "{{ asset('images/markers/blue-dot.png') }}";
                }else{
                    icon = "{{ asset('images/markers/green-dot.png') }}";
                }
            }

            var image_html = '';
            if(item.profile_image != null && item.profile_image != "") {
                var image = "{{asset('images/uploads/') }}/" + item.profile_image;
                image_html = '<img name="image" src="'+image+'"  class="profileImage">';
            }else{
                var initial_characters = (item.first_name? item.first_name.charAt(0): '') + ((item.last_name != "")? item.last_name.charAt(0): camelcase(item.first_name.charAt((item.first_name.length - 1))));
                image_html = '<div class="profileImage" style="background: linear-gradient(to bottom, #F2351F, #F17437);">'+initial_characters+'</div>';
            }

            if(item.latest_job_applied.job.assignee!=null){
            // console.log(item.latest_job_applied.job.assignee.first_name);
            }
       track_step = '<a href="'+trackurl+'">'+((null==item.termination)?((item.last_track && item.last_track.tracking_process)?item.last_track.tracking_process.step_number+'.'+item.last_track.tracking_process.process_steps:"--"):"0. Application Terminated")+'</a>';
       var dateobj = new Date(item.created_at);
       function pad(n) {return n < 10 ? "0"+n : n;}
       //var applied_date = pad(dateobj.getDate())+"/"+pad(dateobj.getMonth()+1)+"/"+dateobj.getFullYear();
       var applied_date = pad(dateobj.getDate()+1)+"/"+pad(dateobj.getMonth())+"/"+dateobj.getFullYear();
            marker = new google.maps.Marker({
                position: position,
                map: map,
                icon:icon,
                content:'<div id="content" style="min-width:700px;">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;' +'<a style="color:#f26338;" target="_blank" href="'+url+'">'+item.name +'</a> </h4>' +
                            '<div id="bodyContent">' +
                            '<label class="col-md-12 col-12 scrollable popup-div-content">   <div class="row">      <div class="col-8">         '         +'         <div class="row">            <div class="col-6 p0">Full Name</div>            <div class="col-6 p0 map-disc popup-value">'+item.name+'</div>         </div>         <div class="row">            <div class="col-6 p0">Address</div>            <div class="col-6 p0 map-disc popup-value">'+item.address+'</div>         </div>         <div class="row">            <div class="col-6 p0">City</div>            <div class="col-6 p0 map-disc popup-value">'+item.city+'</div>         </div>         <div class="row">            <div class="col-6 p0">Postal Code</div>            <div class="col-6 p0 map-disc popup-value">'+item.postal_code+'</div>         </div>	<div class="row">            <div class="col-6 p0">Email Address</div>            <div class="col-6 p0 map-disc popup-value">'+item.email+'</div>         </div>         <div class="row">            <div class="col-6 p0">Phone</div>            <div class="col-6 p0 map-disc popup-value">'+(null!=item.phone_home?item.phone_home:'--')+'</div>         </div>	<div class="row">            <div class="col-6 p0">Cellular Phone</div>            <div class="col-6 p0 map-disc popup-value">'+(null!=item.phone_cellular?item.phone_cellular:'--')+'</div>         </div>         <div class="row">            <div class="col-6 p0">Security Experience</div>            <div class="col-6 p0 map-disc popup-value">'+((null!=item.guarding_experience && null!=item.guarding_experience.years_security_experience)?item.guarding_experience.years_security_experience:0)+'</div>         </div>         <div class="row">            <div class="col-6 p0">Date Applied</div>            <div class="col-6 p0 map-disc popup-value">'+formatDate(applied_date)+'</div>         </div>         '         @can("view_salary_in_candidate_mapping") + '         <div class="row">            <div class="col-6 p0">Low Wage</div>            <div class="col-6 p0 map-disc popup-value">$'+((null!=item.wage_expectation)?(Number(item.wage_expectation.wage_expectations_from).toFixed(2)):0)+'</div>         </div>	<div class="row">            <div class="col-6 p0">High Wage</div>            <div class="col-6 p0 map-disc popup-value">$'+((null!=item.wage_expectation)?(Number(item.wage_expectation.wage_expectations_to).toFixed(2)):0)+'</div>         </div>	<div class="row">            <div class="col-6 p0">Current Wage</div>            <div class="col-6 p0 map-disc popup-value">$'+((null!=item.wage_expectation)?(Number(item.wage_expectation.wage_last_hourly).toFixed(2)):0)+'</div>         </div>         '@endcan         +'         <div class="row">            <div class="col-6 p0">Current Employee</div>            <div class="col-6 p0 map-disc popup-value">'+((null!=item.experience)?item.experience.current_employee_commissionaries:0)+'</div>         </div>        '@can("view_candidate_score_in_candidate_geomapping")+' <div class="row">            <div class="col-6 p0">Scores</div>            <div class="col-6 p0 map-disc popup-value">'+Number(item.latest_job_applied.average_score)+'</div>         </div>  '@endcan         +'       <div class="row">            <div class="col-6 p0">HR Assigned</div>            <div class="col-6 p0 map-disc popup-value">'+((null!=item.latest_job_applied.job.assignee)?item.latest_job_applied.job.assignee.first_name+' '+((null!=item.latest_job_applied.job.assignee.last_name)?item.latest_job_applied.job.assignee.last_name:''):'')+'</div>         </div>         <div class="row">            <div class="col-6 p0">Last Tracking Step</div>            <div class="col-6 p0 map-disc popup-value">'+track_step+'</div>         </div>      </div><div class="col-4 user-image-div"><div class="row">'+image_html+'</div></div>   </div></label>' +
                            '</div>' +
                            '</div>'
            });
            markers.push(marker);
        });
        $.each(markers, function(i, marker) {
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(marker.content);
                    infowindow.open(map, marker);
                    map.setCenter(marker.getPosition());
                }
            })(marker, i));
        });

        google.maps.event.addDomListener(window, 'resize', function() {
            infowindow.open(map);
        });

        google.maps.event.addListenerOnce(map, 'idle', function(){
            $("#wrapper").height("66vh");
            $('.embed-responsive').height("85vh");
        });

        showNumberOfRecords();
    }

    function showNumberOfRecords() {
        let candidateCount = $('.candidate_names:not([style*="display: none"])').length;
        candidateCount = (candidateCount > 0) ? numberWithCommas(candidateCount): 0;
        $('#candidate_list_count').html(candidateCount);
    }

    function formatDate(date){
        var match = /(\d+)\/(\d+)\/(\d+)/.exec(date)
        var d = new Date(match[3], match[2], match[1]);

        var options = {
            timeZone:"UTC",month:"long", day:"2-digit", year:"numeric"
        };
        var formattedDate = d.toLocaleDateString("en-US", options);
        const sp = formattedDate.split(' ')
        return formattedDate;

    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function openInfoWindow(id) {
        google.maps.event.trigger(markers[id], 'click');
    }

    $(function () {
        $('.datepicker').datepicker();
        initMap();
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $("#view-details,.filter-details").css("display", "none");
        });
        $(".search-input").click(function () {
            $(".filter-details").toggleClass("toggled").css("display", "block");
        });
        $('.client-select').select2();
        @if(!empty($request->all()))
        $("#menu-toggle").click();
        $(".search-input").click();
        @endif
        $(".reset").click(function(e) {
            e.preventDefault();
            $(this).closest('form').find("input[type='text']").val("");
            $(this).closest('form').find("input[type='number']").val("");
            $('.client-select').val('').trigger('change');
            $('.career_interest').val('');
            $('.availability').val('');
            $('.drivers_license').val('');
            $('.english').val('');
            $('.french').val('');
            $('.vet').val('');
            $('.indigienous').val('');
            $('.work_status').val('');
            $('.orientation').val('');
            $('.position_availibility').val('');
            $('.starting_time').val('');
            $('.use_of_force').val('');
        });
        $.expr[':'].contains = function(a, i, m) {
            return jQuery(a).text().toUpperCase()
                .indexOf(m[3].toUpperCase()) >= 0;
        };
        $('#searchbox').on('keyup',function(){
            search = $(this).val();
            $('#candidate-data-left-panel li').show();
            $('#candidate-data-left-panel li:not(:contains('+search+'))').hide();
            showNumberOfRecords();
        });
    });

    $(window).bind("load", function() {
        $('#sidebar').css('height', "100vh");
        $('#content-div').css('height', "100vh");
        $('#content-div').css('overflow', "hidden");
    });
</script>
@stop
