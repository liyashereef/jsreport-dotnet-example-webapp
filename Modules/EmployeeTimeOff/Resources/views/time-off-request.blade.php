@extends('layouts.app')

@section('content')
<div class="table_title">
    <h4> Employee Time Off Request </h4>
</div>
<!-- Container - Start -->
<div class="container">
	<!-- Employee Summary - Start -->
	{{ Form::open(array('url'=>'timeoff/{module}/store','id'=>'time-off-form', 'method'=> 'POST')) }}
    {{ Form::hidden('id', isset($time_off_edit_details) ? old('id',$time_off_edit_details->id) : null) }}
    {{ Form::hidden('employee_role_id', isset($time_off_edit_details) ? old('employee_role_id',$time_off_edit_details->employee_role_id) : null, array('id'=>'employee_role_id')) }}
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Employee Summary</div>
		<div class="data-list-line row">
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">Enter Employee Number</div>
        	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 data-list-disc form-group" id="employee_id">
		        {{ Form::select('employee_id',[null=>'Please Select']+$employee_list, isset($time_off_edit_details) ? old('employee_id',$time_off_edit_details->employee_id) : null, array('class' => 'form-control','onchange'=>"findCalc($(this).val())", 'id'=>'employee_no','autocomplete' => 'off', 'required')) }}
		        <small class="help-block"></small>
		    </div>
        	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">Enter Primary Project Number</div>
        	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 data-list-disc form-group" id="customer_id">
		        {{ Form::select('customer_id',[null=>'Please Select']+$project_list, isset($time_off_edit_details) ? old('customer_id',$time_off_edit_details->customer_id) : null, array('class' => 'form-control', 'id'=>'project_no','autocomplete' => 'off', 'required')) }}
		        <small class="help-block"></small>
        	</div>
	  	</div>
	  	<!-- Row - Start -->
	  	<div class="row">
	   	<!-- Profile - Start -->
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-panel">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
				<div class="margin-bottom-20">
					<div id="profile" class="data-list-body">
						<div class="data-list-line row">
						<div class="data-list-label-time-off-request margin-top-1 margin-bottom-10">Profile</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">First Name</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="first_name">
								{{isset($employee) ? old('first_name',$employee['first_name']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Last Name</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="last_name">
								{{isset($employee) ? old('last_name',$employee['last_name']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Address</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="employee_address">
								{{isset($employee) ? old('employee_address',$employee['employee_address']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">City</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="employee_city">
								{{isset($employee) ? old('employee_city',$employee['employee_city']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Postal Code</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="employee_postal_code">
								{{isset($employee) ? old('employee_postal_code',$employee['employee_postal_code']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Phone Number</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="phone">
								{{isset($employee) ? old('phone',$employee['phone']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Work Email</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="employee_work_email">
								{{isset($employee) ? old('employee_work_email',$employee['employee_work_email']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Project Number</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="project_number">
								{{isset($employee) ? old('project_number',$employee['project_number']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Project Name</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="client_name">
								{{isset($employee) ? old('client_name',$employee['client_name']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Current Wage</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="current_project_wage">
								{{isset($employee) ? old('current_project_wage',$employee['current_project_wage']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Date of Birth</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="employee_dob">
								{{isset($employee) ? old('employee_dob',$employee['employee_dob']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Age</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="age">
								{{isset($employee) ? old('age',$employee['age']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Start Date</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="employee_doj">
								{{isset($employee) ? old('employee_doj',$employee['employee_doj']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Length of Service (Year)</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="service_length">
								{{isset($employee) ? old('service_length',$employee['service_length']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Veteran Status</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="employee_vet_status">
								{{isset($employee) ? old('employee_vet_status',$employee['employee_vet_status']) : null}}
							</div>
						</div>
						<div id="security_clearance_div">
							@if(isset($employee['all_security_clearance']))
								@foreach($employee['all_security_clearance'] as $clearance)
									<div class="data-list-line  row security_clearance">
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance</div>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="security_clearance">
											{{ $clearance->securityClearanceLookups->security_clearance or '--' }}
										</div>
									</div>
									<div class="data-list-line  row security_clearance">
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance Expiry</div>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="valid_until">
											{{ $clearance->valid_until or '--' }}
										</div>
									</div>
								@endforeach
							@else
								<div class="data-list-line  row security_clearance">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="security_clearance">
									</div>
								</div>
								<div class="data-list-line  row security_clearance">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance Expiry</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="valid_until">
									</div>
								</div>
							@endif
						</div>

						{{-- <div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="security_clearance">
								{{isset($employee) ? old('security_clearance',$employee['security_clearance']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance Expiry</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="valid_until">
								{{isset($employee) ? old('valid_until',$employee['valid_until']) : null}}
							</div>
						</div> --}}
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Employee Rating</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="employee_rating">
								{{isset($employee) ? old('employee_rating',$employee['employee_rating']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Position</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="position">
								{{isset($employee) ? old('position',$employee['position']) : null}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
        <!-- Profile - End -->

        <!-- Abscence Summary - Start -->
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-panel">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
				<div class="data-list-container">
					<div class="data-list-body">
						<div class="data-list-line row">
							<div class="data-list-label-time-off-request margin-top-1 margin-bottom-10">Absence Summary</div>
						</div>
						<table class="table table-bordered" id="abscence-summary">
		                  	<thead align="center">
		                      <tr>
		                          <th class="sorting" style='color:white'>Type</th>
		                          <th class="sorting" colspan="4" style='color:white'>YTD Summary</th>
		                      </tr>
		                 	</thead>
		                 	<tbody align="center" id="timeoff-data">
		                 		<tr><td></td><td>Claimed</td><td>Approved</td><td>Rejected</td><td>Remaining</td></tr>
		                 		@foreach($timeoff_data as $timeoff)
		                 		<tr><td>{{$timeoff['type']}}</td>
		                 			<td>{{$timeoff['days_requested']}}</td>
		                 			<td>{{$timeoff['days_approved']}}</td>
		                 			<td>{{$timeoff['days_rejected']}}</td>
		                 			<td>{{$timeoff['days_remaining']}}</td>
		                 			@endforeach
		                 	</tbody>
		                </table>
		                <div class="form-group" id="supervisor_id">
		                {{ Form::hidden('supervisor_id', isset($supervisor) ? old('supervisor_id',$supervisor['id']) : null, array('id'=>'super_visor_id')) }}
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Supervisor Name</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="supervisor_name">
								{{isset($supervisor) ? old('supervisor_name',$supervisor['full_name']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Supervisor Phone</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="supervisor_phone">
								{{isset($supervisor) ? old('supervisor_phone',$supervisor['phone']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Supervisor Email</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="supervisor_email">
								{{isset($supervisor) ? old('supervisor_email',$supervisor['email']) : null}}
							</div>
						</div>
						<small class="help-block"></small>
						</div>
						<div class="form-group" id="areamanager_id">
						{{ Form::hidden('areamanager_id', isset($area_manager) ? old('areamanager_id',$area_manager['id']) : null, array('id'=>'area_manager_id')) }}
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">RM Name</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="area_manager_name">
								{{isset($area_manager) ? old('area_manager_name',$area_manager['full_name']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">RM Phone</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="area_manager_phone">
								{{isset($area_manager) ? old('area_manager_phone',$area_manager['phone']) : null}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">RM Email</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="area_manager_email">
								{{isset($area_manager) ? old('area_manager_email',$area_manager['email']) : null}}
							</div>
						</div>
						<small class="help-block"></small>
						</div>
						<div class="form-group" id="">
						{{ Form::hidden('hr_id', isset($hr) ? old('hr_id',$hr['id']) : $logged_in_user['id'], array('id'=>'hr_id')) }}
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">HR Name</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="hr_name">
								{{isset($hr) ? old('hr_name',$hr['full_name']) : $logged_in_user['full_name']}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">HR Phone</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="hr_phone">
								{{isset($hr) ? old('hr_phone',$hr['phone']) : $logged_in_user['phone']}}
							</div>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">HR Email</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="hr_email">
								{{isset($hr) ? old('hr_email',$hr['email']) : $logged_in_user['email']}}
							</div>
						</div>
						<small class="help-block"></small>
						</div>
						<div class="data-list-line row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">OC Email</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="oc_email">
								@foreach ($oc_email as $oc_email_single)
									{{$oc_email_single}} <br>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
		<!-- Abscence Summary - End -->

	    </div>
	    <!-- Row - End -->
		<div class="row">
			<!-- Profile - Start -->
		 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-panel">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
					<div class="margin-bottom-20">
						<div  class="data-list-body">
							<div class="data-list-line row">
					
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Date</div>
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="date">{{isset($timestamp) ? $timestamp['date'] : '--'}}</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		 </div>
		 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-panel">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
						<div class="margin-bottom-20">
							<div  class="data-list-body">
								<div class="data-list-line row">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Time</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="time">{{isset($timestamp) ? $timestamp['time'] : '--'}}</div>
								
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	    <!-- Employee Summary - End -->

		<!-- Employee Request - Start -->
	    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head margin-top-10">Employee Request</div>
		    <div class="data-list-line form-group row" id="request_type_id">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Enter time off request type</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		            {{ Form::select('request_type_id',[null=>'Please Select']+$request_type, isset($time_off_edit_details) ? old('request_type_id',$time_off_edit_details->request_type_id) : null,array('class' => 'form-control', 'id'=> 'request_type')) }}
		            <small class="help-block"></small>
		        </div>
			</div>
			
		    <div class="" id="vacation_request" style="display:{{isset($time_off_edit_details) ? (($time_off_edit_details->vacation_pay_required === null) ? 'none' : 'block' ) : 'none'}};">
		    	<div class="data-list-line form-group row" id="vacation_pay_required">
			        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Is vacation pay required for leave period?</div>
			        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
			            {{ Form::select('vacation_pay_required', [null=>'Please Select',1=>'Yes', 0=>'No'], isset($time_off_edit_details) ? old('vacation_pay_required',$time_off_edit_details->vacation_pay_required) : null, array('class' => 'form-control', 'id'=> 'leave_period')) }}
			            <small class="help-block"></small>
			        </div>
		    	</div>
		    	<div id="vacation_pay_yes" style="display:{{isset($time_off_edit_details) ? ($time_off_edit_details->vacation_pay_required == 1 ? 'block' : 'none' ) : 'none'}};">
			    	<div class="data-list-line form-group row" id="vacation_pay_amount">
			        	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Indicate amount to be withdraw</div>
				        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
				        	{{ Form::number('vacation_pay_amount', isset($time_off_edit_details) ? old('vacation_pay_amount',$time_off_edit_details->vacation_pay_amount) : null, array('class'=>'form-control', 'placeholder'=>'Withdrawal Amount', 'id'=>'withdrawal_amount', 'min'=>'0')) }}
				        	<small class="help-block"></small>
				        </div>
			    	</div>
			    	<div class="data-list-line form-group row" id="vacation_payperiod_id">
			        	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Indicate the pay period/date you want your vacation pay</div>
				        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
				        	{{ Form::select('vacation_payperiod_id', [null=>'Please Select']+$pay_period, isset($time_off_edit_details) ? old('vacation_payperiod_id',$time_off_edit_details->vacation_payperiod_id) : null, array('class' => 'form-control', 'id'=> 'payperiod_date')) }}
				        	<small class="help-block"></small>
				        </div>
			    	</div>
		    	</div>
		    </div>
		    <div class="data-list-line form-group row" id="start_date">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">What is the start date of your request?</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		        	{{ Form::text('start_date', isset($time_off_edit_details) ? old('start_date',$time_off_edit_details->start_date) : null, array('class'=>'form-control datepicker', 'placeholder'=>'Start Date', 'id'=>'requested_start_date', 'required', 'readonly')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="end_date">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">What is your expected return date?</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		        	{{ Form::text('end_date', isset($time_off_edit_details) ? old('end_date',$time_off_edit_details->end_date) : null, array('class'=>'form-control datepicker', 'placeholder'=>'Return Date', 'id'=>'expected_return_date', 'required', 'readonly')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="no_of_shifts">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">How many total shifts will you be away?</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		        	{{ Form::number('no_of_shifts', isset($time_off_edit_details) ? old('no_of_shifts',$time_off_edit_details->no_of_shifts) : null, array('class'=>'form-control', 'id'=>'total_shifts' , 'placeholder'=>'Total Shifts Away', 'required', 'min'=>'0','max'=>'99')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="average_shift_length">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">What is the average shift length (hours)?</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		        	{{ Form::number('average_shift_length', isset($time_off_edit_details) ? old('average_shift_length',$time_off_edit_details->average_shift_length) : null, array('class'=>'form-control', 'id'=>'average_shifts', 'placeholder'=>'Average Shift Length', 'required', 'min'=>'0','max'=>'99')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="total_hours_away">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Total Hours Away</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		        	{{ Form::number('total_hours_away', isset($time_off_edit_details) ? old('total_hours_away',$time_off_edit_details->total_hours_away) : null, array('class'=>'form-control', 'id'=>'hours_away', 'placeholder'=>'Total Hours Away', 'required', 'readonly', 'min'=>'0')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="leave_reason_id">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">What is the reason for the request?</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		            {{ Form::select('leave_reason_id',[null=>'Please Select']+$leave_reason, isset($time_off_edit_details) ? old('leave_reason_id',$time_off_edit_details->leave_reason_id) : null, array('class' => 'form-control', 'id'=>'leave_reason')) }}
		            <small class="help-block"></small>
		        </div>
		    </div>
		<div class="data-list-line form-group row" id="other_reason" style="display:{{isset($time_off_edit_details->other_reason) ?  : 'none'}}">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Other Reason</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		        	{{ Form::text('other_reason', isset($time_off_edit_details) ? old('other_reason',$time_off_edit_details->other_reason) : null, array('class'=>'form-control', 'placeholder'=>'Other Reason')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="nature_of_request">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">What is the nature of the request?</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		        	{{ Form::textarea('nature_of_request', isset($time_off_edit_details) ? old('nature_of_request',$time_off_edit_details->nature_of_request) : null, array('class'=>'form-control', 'id'=>'nature', 'placeholder'=>'Nature of the Request', 'rows'=>5, 'cols'=>50, 'required')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="request_category_id">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">ESA Standard</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		            {{ Form::select('request_category_id',[null=>'Please Select']+$category, isset($time_off_edit_details) ? old('request_category_id',$time_off_edit_details->request_category_id) : null,array('class' => 'form-control', 'id'=>'esa_standard')) }}
		            <small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Overview</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		        	{{ Form::textarea('overview', isset($time_off_edit_details) ? old('overview',$time_off_edit_details->category->description) : null, array('class'=>'form-control', 'id'=>'overview', 'placeholder'=>'Overview', 'rows'=>5, 'cols'=>50, 'required', 'readonly')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="reference">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Reference</div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc">
		        	{{ Form::text('reference', isset($time_off_edit_details) ? old('reference',$time_off_edit_details->category->reference) : null, array('class'=>'form-control', 'id'=>'category_reference', 'placeholder'=>'Reference', 'required', 'readonly')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="days_permitted">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Days permitted under ESA or policy</div>
		        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 data-list-disc">
		        	{{ Form::Number('days_permitted', isset($time_off_edit_details) ? old('days_permitted',$time_off_edit_details->category->allowed_days) : null, array('class'=>'form-control', 'id'=>'permitted_days', 'placeholder'=>'Days Permitted', 'required', 'readonly', 'min'=>'0')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="days_requested">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Days Requested</div>
		        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 data-list-disc">
		        	{{ Form::Number('days_requested', isset($time_off_edit_details) ? old('days_requested',$time_off_edit_details->days_requested) : null, array('class'=>'form-control', 'id'=>'requested_days', 'placeholder'=>'Days Requested', 'required', 'min'=>'0')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="days_approved">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Days approved by HR</div>
		        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 data-list-disc">
		        	{{ Form::Number('days_approved', isset($time_off_edit_details) ? old('days_approved',$time_off_edit_details->days_approved) : null, array('class'=>'form-control', 'placeholder'=>'Days approved by HR', 'id'=>'approved_days', 'required', 'min'=>'0')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="days_rejected">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Days Rejected</div>
		        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 data-list-disc">
		        	{{ Form::Number('days_rejected', isset($time_off_edit_details) ? old('days_rejected',$time_off_edit_details->days_rejected) : null, array('class'=>'form-control', 'placeholder'=>'Days Rejected', 'id'=>'rejected_days', 'required', 'min'=>'0', 'readonly')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="days_remaining">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Remaining Balance</div>
		        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 data-list-disc">
		        	{{ Form::Number('days_remaining', isset($time_off_edit_details) ? old('days_remaining',$time_off_edit_details->days_remaining) : null, array('class'=>'form-control', 'placeholder'=>'Remaining Balance', 'required', 'min'=>'0')) }}
		        	<small class="help-block"></small>
		        </div>
		    </div>
		    <div class="data-list-line form-group row" id="upload-attachment">
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Upload Attachment (as required)</div>
		        <table class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 table table-bordered" id="upload-attachment-table">
                    <tbody>
					@if(isset($time_off_edit_details->attachments))
						@foreach ($time_off_edit_details->attachments as $key => $attach)
						<tr class="">
								@php $attachment_id = $attach->attachment_id @endphp
							<td><a title="Download" href=" {{ route("filedownload", [$attachment_id,'employeeTimeOff']) }} ">{{$attach->attachment->original_name}}</a></td>
						</tr>
						@endforeach
					@endif

                    <tr class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    	<td class="data-list-disc attachment"><input type="file" class="form-control" name="time_off_attachment[]"></td>
                    	<td class="data-list-disc attachment-button"><a title="Add" href="javascript:;" class="add_attachment"><i class="fa fa-plus size-adjust-icon" aria-hidden="true"></i> Add Attachment</a></td></tr></tbody>
                </table>
		    </div>
		    <div class="data-list-line form-group row">
		        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1">
		        	{{ Form::submit('Submit', array('class'=>'button btn btn-primary blue submit'))}}
		        </div>
		    </div>
		    {{ Form::close() }}
		</div>
		<!-- Employee Request - End -->
</div>
<!-- Container - End -->
@stop
@section('scripts')
@include('employeetimeoff::scripts')
@stop
