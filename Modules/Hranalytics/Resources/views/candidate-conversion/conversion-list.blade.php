@extends('layouts.app')
@section('content')
<style>
    .candidate-image-div img {
        transition: transform .5s, filter 1.5s ease-in-out;
    }

    /* [3] Finally, transforming the image when container gets hovered */
    .candidate-image-div:hover img {
        z-index: 9999999;
        transform:scale(1.8);
        -ms-transform:scale(1.8); /* IE 9 */
        -moz-transform:scale(1.8); /* Firefox */
        -webkit-transform:scale(1.8); /* Safari and Chrome */
        -o-transform:scale(1.8); /* Opera */
        position: relative;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">

<div class="table_title">
    <h4>Candidate Conversion</h4>
</div>
<table class="table table-bordered" id="conversion-table">
    <thead>
        <tr>
            <th class="sorting" width="5%">#</th>
            <th class="sorting" width="10%">Candidate Name</th>
            <th class="sorting" width="10%">Candidate Email</th>
            <th class="sorting" width="10%">Job Id</th>
            <th class="sorting" width="10%">Completion Date</th>

        </tr>
    </thead>
</table>
<input type="submit"  value="Convert to Employee" name="employee-convert" class="btn submit employee-conversion" data-id="" style="">
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myModalLabel">User Master</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

           </div>
           {{ Form::open(array('url'=>'#','id'=>'user-form','class'=>'form-horizontal', 'method'=> 'POST', 'novalidate'=>TRUE)) }}
           {{ Form::hidden('id', "") }}
           {{ Form::hidden('candidate_id', "",['id' => 'candidate_id']) }}
           {{ Form::hidden('document_type_id', $documentTypeID) }}
           {{ Form::hidden('document_category_id', $documentCategoryDetails->id) }}
            {{ Form::hidden('document_name_id', $documentNameDetails->id) }}
           <div class="modal-body">



            <!-- Tabs View - Start -->
            <div role="tablist">
                <!-- Nav tabs - Start -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active nav-item"><a href="#userTab" aria-controls="userTab" role="tab" data-toggle="tab" class="nav-link active">User</a></li>
                    <li role="presentation" class="nav-item"><a href="#employeeTab" aria-controls="employeeTab" role="tab" data-toggle="tab" class="nav-link">Profile</a></li>
                    <li role="presentation" class="nav-item"><a href="#securityClearanceTab" aria-controls="securityClearanceTab" role="tab" class="nav-link" data-toggle="tab">Security Clearance</a></li>
                    <li role="presentation" class="nav-item"><a href="#projectTab" aria-controls="projectTab" role="tab" class="nav-link" data-toggle="tab">Project Details</a></li>
                     <li role="presentation" class="nav-item"><a href="#certificateTab" aria-controls="certificateTab" role="tab"  class="nav-link" data-toggle="tab">Certificates</a></li>
                      <li role="presentation" class="nav-item">
                          <a href="#expenseTab" aria-controls="expenseTab" role="tab"  class="nav-link" data-toggle="tab">Expense</a></li>
                      <li role="presentation" class="nav-item"><a href="#bankingTab"  class="nav-link" aria-controls="bankingTab" role="tab" data-toggle="tab">Banking</a></li>
                      <li role="presentation" class="nav-item"><a href="#taxTab"  class="nav-link" aria-controls="taxTab" role="tab" data-toggle="tab">Tax</a></li>
                      <li role="presentation" class="nav-item"><a href="#benefitsTab"  class="nav-link" aria-controls="benefitsTab" role="tab" data-toggle="tab">Benefits</a></li>
                      <li role="presentation" class="nav-item"><a href="#employmentTab"  class="nav-link" aria-controls="employmentTab" role="tab" data-toggle="tab">Employment</a></li>
                       <li role="presentation" class="nav-item"><a href="#emergencyContactTab"  class="nav-link" aria-controls="emergencyContactTab" role="tab" data-toggle="tab">Emergency Contact</a></li>
                    </ul>
                <!-- Nav tabs - End -->

                <!-- Tab panes - Start -->
                <div class="tab-content tab-alignment">
                    <div role="tabpanel" class="tab-pane active" id="userTab">
                        <div class="form-group" id="first_name">
                            <label for="name" class="col-sm-3 control-label">First Name <span class="mandatory">*</span></label>
                            <div class="col-sm-8">
                                {{Form::text('first_name',"",['class' => 'form-control','placeholder' => 'First Name'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="last_name">
                            <label for="last_name" class="col-sm-3 control-label">Last Name</label>
                            <div class="col-sm-8">
                                {{Form::text('last_name',"",['class' => 'form-control','placeholder' => 'Last Name'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="email">
                            <label for="email" class="col-sm-3 control-label">COMMGL Email <span class="mandatory">*</span></label>
                            <div class="col-sm-8">
                                {{Form::email('email',"",['class' => 'form-control','placeholder' => 'Email','id'=>'email'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="alternate_email">
                            <label for="alternate_email" class="col-sm-3 control-label">Alternate Email</label>
                            <div class="col-sm-8">
                                {{Form::email('alternate_email',"",['class' => 'form-control','placeholder' => 'Alternate Email'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="username">
                            <label for="username" class="col-sm-3 control-label">Username <span class="mandatory">*</span></label>
                            <div class="col-sm-8">
                                {{Form::text('username',"",['class' => 'form-control','placeholder' => 'Username','id'=>'user_name','readonly'=>'true'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="password">
                            <label for="password" class="col-sm-3 control-label">Password <span class="mandatory">*</span></label>
                            <div class="col-sm-8">
                                {{Form::password('password',['class' => 'form-control','placeholder' => '********'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="role_id">
                            <label for="role_id" class="col-sm-3 control-label">Role <span class="mandatory">*</span></label>
                            <div class="col-sm-8">
                                {{Form::select('role_id',$roles,null,['class' => 'form-control','placeholder' => 'Choose the Role'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="employeeTab">
                        <div class="form-group" id="employee_no">
                            <label for="employee_no" class="col-sm-3 control-label">Employee No <span class="mandatory">*</span></label>
                            <div class="col-sm-8">
                                {{Form::text('employee_no',"",['class' => 'form-control','placeholder' => 'Employee No','maxlength' => '6'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="phone">
                            <label for="phone" class="col-sm-3 control-label">Phone <span class="mandatory">*</span></label>
                            <div class="col-sm-8">
                                {{Form::text('phone',"",['class' => 'form-control phone','placeholder' => 'Phone [ format (XXX)XXX-XXXX ]'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="phone_ext">
                            <label for="phone_ext" class="col-sm-3 control-label">Ext. </label>
                            <div class="col-sm-8">
                                {{Form::text('phone_ext',"",['class' => 'form-control','placeholder' => 'Ext.'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="cell_no">
                            <label for="cell_no" class="col-sm-3 control-label">Cell</label>
                            <div class="col-sm-8">
                                {{Form::text('cell_no',"",['class' => 'form-control phone','placeholder' => 'Cell No [ format (XXX)XXX-XXXX ]','pattern' => '[\(]\d{3}[\)]\d{3}[\-]\d{4}'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="work_type_id">
                            <label for="work_type_id" class="col-sm-3 control-label">Work Type <span class="mandatory">*</span></label>
                            <div class="col-sm-8">
                                {{Form::select('work_type_id',$work_types,null,['class' => 'form-control','placeholder' => 'Choose the work type'])}}
                                {!! $errors->first('work_type_id', '<small class="help-block">:message</small>') !!}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="employee_address">
                            <label for="employee_address" class="col-sm-3 control-label">Address</label>
                            <div class="col-sm-8">
                                {{Form::text('employee_address',"",['class' => 'form-control','placeholder' => 'Address'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group" id="employee_city">
                            <label for="employee_city" class="col-sm-3 control-label">City</label>
                            <div class="col-sm-8">
                                {{Form::text('employee_city',"",['class' => 'form-control','placeholder' => 'City'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="employee_postal_code">
                            <label for="employee_postal_code" class="col-sm-3 control-label">Postal Code <span class="mandatory"></span></label>
                            <div class="col-sm-8">
                                {{Form::text('employee_postal_code',"",['class' => 'form-control postal-code','placeholder' => 'Postal Code','required'=>true])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="employee_work_email">
                            <label for="employee_work_email" class="col-sm-3 control-label">Work Email</label>
                            <div class="col-sm-8">
                                {{Form::text('employee_work_email',"",['class' => 'form-control','placeholder' => 'Work Email'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="employee_doj">
                            <label for="employee_doj" class="col-sm-3 control-label">DOJ</label>
                            <div class="col-sm-8">
                                {{Form::text('employee_doj',$date,['class' => 'form-control datepicker','placeholder' => 'Date of Joining (Y-m-d)'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="employee_dob">
                            <label for="employee_dob" class="col-sm-3 control-label">DOB</label>
                            <div class="col-sm-8">
                                {{Form::text('employee_dob',"",['class' => 'form-control datepicker','placeholder' => 'Date of Birth (Y-m-d)'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="current_project_wage">
                            <label for="current_project_wage" class="col-sm-3 control-label">Current Project Wage</label>
                            <div class="col-sm-8">
                                {{Form::text('current_project_wage',"",['class' => 'form-control','placeholder' => 'Current Project Wage'])}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="position_id">
                            <label for="position_id" class="col-sm-3 control-label">Position</label>
                            <div class="col-sm-8">
                                {{Form::select('position_id',$positions,null,['class' => 'form-control','placeholder' => 'Choose the Position'])}}
                                {!! $errors->first('position_id', '<small class="help-block">:message</small>') !!}
                            </div>
                        </div>
                         <div class="form-group" id="years_of_security">
                                <label for="years_of_security"
                                class="col-sm-3 control-label">Years of Security</label>
                                <div class="col-sm-8">
                                    {{Form::number('years_of_security',"",['class' => 'form-control','placeholder' => 'Years of Security'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="being_canada_since">
                                <label for="being_canada_since" class="col-sm-3 control-label">Arrival In Canada</label>
                                <div class="col-sm-8">
                                    {{Form::text('being_canada_since',"",['id' => 'being_canada_since','class' => 'form-control datepicker' ,'placeholder' => ''])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="wage_expectations_from">
                                <label for="wage_expectations_from" class="col-sm-3 control-label">Wage Expectation From</label>
                                <div class="col-sm-8">
                                    {{Form::text('wage_expectations_from',"",['class' => 'form-control','placeholder' => 'Wage Expectation From'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="wage_expectations_to">
                                <label for="wage_expectations_to" class="col-sm-3 control-label">Wage Expectation To</label>
                                <div class="col-sm-8">
                                    {{Form::text('wage_expectations_to',"",['class' => 'form-control','placeholder' => 'Wage Expectation To'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="candidate-image">
                                <label for="image" class="col-sm-3 control-label">Image</label>
                                <div class="col-sm-8">
                                    {!! Form::file('profile_image', array('id' => 'candidate-image-element','class' => 'candidate-image-element hidden')) !!}
                                    <div class="candidate-image-div">
                                        <img style="border-radius: 50%;" src="{{asset('images/uploads/') }}/{{ $candidateJob->candidate->profile_image ?? config('globals.noAvatarImg') }}" height="100px" width="100px" name="image"/>
                                    </div>
                                    <div id="candidate-image-upload" class="candidate-image-upload" style="display: none;">
                                        <button type="button" style="float: right;" id="upload_profile_image" class="candidate-image-upload btn btn-primary btn-sm">Upload</button>
                                    </div>
                                </div>
                            </div>
                        {{-- <div class="form-group" id="employee_vet_status">
                            <label for="employee_vet_status" class="col-sm-3 control-label">@lang('Is Employee Veteran')</label>
                            <div class="col-sm-8">
                                {{Form::select('employee_vet_status',[1=>"Yes",0=>"No"],null,['class' => 'form-control','placeholder' => 'Choose the Veteran Status'])}}
                                {!! $errors->first('employee_vet_status', '<small class="help-block">:message</small>') !!}
                            </div>
                        </div> --}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="securityClearanceTab">
                        <div class="col-sm-12 table-responsive pop-in-table" id="user-security-clearance">
                            <table class="table user-security-clearance-table">
                                <thead>
                                    <tr>
                                        <th>Security Clearance</th>
                                        <th>Valid Until</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="add-security-clearance" id="add-security-clearance" class="col-sm-1 btn btn-primary" style="margin-right:1%;">+</label>
                            <label for="remove-security-clearance" id="remove-security-clearance" class="col-sm-1 btn btn-primary">-</label>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="projectTab">
                        <div class="form-group" id="project_no">
                            <label for="project_no" class="col-sm-5 control-label">Project No allocated<span class="mandatory">*</span></label>
                            <div class="col-sm-8">
                                {{Form::select('project_no',[''=>'Please Select']+$customer,null,['class' => 'form-control projects','id'=>'project_no'])}}
                                {!! $errors->first('project_no', '<small class="help-block">:message</small>') !!}
                            </div>
                        </div>
                        <div class="form-group" id="project_name">
                            <label for="project_name" class="col-sm-3 control-label">Project Name</label>
                            <div class="col-sm-8">
                             {{Form::text('project_name',"",['class' => 'form-control','placeholder' => 'Current Project','readonly'=>'true'])}}
                             {!! $errors->first('project_name', '<small class="help-block">:message</small>') !!}
                         </div>
                     </div>
                     <div class="form-group" id="project_deployed">
                        <label for="project_deployed" class="col-sm-5 control-label">Project Deployed Date<span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            {{Form::text('project_deployed',$date,['class' => 'form-control datepicker','placeholder' => 'Project Deployed (Y-m-d)'])}}
                            {!! $errors->first('project_deployed', '<small class="help-block">:message</small>') !!}
                        </div>
                    </div>
                    <div class="form-group" id="policy_file">
                        <label for="policy_file" class="col-sm-5 control-label">Upload Signed Employee Contract<span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            {{ Form::file('policy_file',array('id' => 'attachment')) }}

                            <small class="help-block"></small>

                        </div>
                    </div>
                </div>
                   <div role="tabpanel" class="tab-pane" id="certificateTab">
                           <div class="form-group" id="employee_vet_status">
                                <label for="employee_vet_status" class="col-sm-3 control-label">@lang('Is Employee Veteran')</label>
                                <div class="col-sm-8">
                                    {{Form::select('employee_vet_status',[0=>"No",1=>"Yes"],null,['class' => 'form-control','id'=>'veteran_status'])}}
                                    {!! $errors->first('employee_vet_status', '<small class="help-block">:message</small>') !!}
                                </div>
                            </div>

                            <div class=" veteran_status_qstn ">
                            <div class="form-group" id="vet_service_number">
                                <label for="vet_service_number" class="col-sm-3 control-label">@lang('Service number?')<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('vet_service_number','',['class' => 'form-control','placeholder' => 'Service Number'])}}
                                     <small class="help-block"></small>
                                </div>
                            </div>
                                <div class="form-group" id="vet_enrollment_date">
                                <label for="vet_enrollment_date" class="col-sm-3 control-label">@lang('Enrollment date?')<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('vet_enrollment_date','',['class' => 'form-control datepicker','placeholder' => 'Enrollment Date'])}}
                                   <small class="help-block"></small>
                                </div>
                            </div>
                             <div class="form-group" id="vet_release_date">
                                <label for="vet_release_date" class="col-sm-3 control-label">@lang('Release date?')<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('vet_release_date','',['class' => 'form-control datepicker','placeholder' => 'Release Date'])}}
                                     <small class="help-block"></small>
                                </div>
                            </div>
                            </div>
                            <div class="col-sm-12 table-responsive pop-in-table" id="user-certificate">
                            <table class="table user-certificate-table">
                                <thead>
                                    <tr>
                                        <th>Certificates</th>
                                        <th>Valid Until</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>
                          <div class="form-group col-sm-12">
                                <label for="add-certificate" id="add-certificate" class="col-sm-1 btn btn-primary" style="margin-right:1%;">+</label>
                                <label for="remove-certificate" id="remove-certificate" class="col-sm-1 btn btn-primary">-</label>
                            </div>
                        </div>

                          <div role="tabpanel" class="tab-pane" id="expenseTab">

                            <div class="form-group" id="reporting_to_id">
                                <label for="reporting_to_id" class="col-sm-5 control-label">Reporting To (Approver)<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                    {{Form::select('reporting_to_id',$approversList,null,['class' => 'form-control reporting_to_approver','placeholder' => 'Choose the Approver'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="max_allowable_expense">
                                <label for="max_allowable_expense" class="col-sm-5 control-label">
                                    @lang('Max: Allowable Expense')</label>
                                <div class="col-sm-8">
                                    {{Form::text('max_allowable_expense','',['class' => 'form-control',
                                            'placeholder' => 'Max: Allowable Expense','maxlength' => 7])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                        </div>
                         <!---Banking tab start--->
                    <div role="tabpanel" class="tab-pane" id="bankingTab">
                        <div class="form-group" id="bankid">
                                <label for="bankname" class="col-sm-3 control-label">Bank Name <span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::select('bankid', ['' => 'Please Select']+ $banks,null,['class' => 'form-control bankname-select2','id'=>'bankname'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="bankcode">
                                <label for="bankcode" class="col-sm-3 control-label">Bank Code <span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::text('bankcode','',['class' => 'form-control','placeholder' => 'Bank Code','id'=>'bankcodes'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="transit">
                                <label for="transit" class="col-sm-3 control-label">Transit<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::text('transit','',['class' => 'form-control','placeholder' => 'Transit'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="account_no">
                                <label for="account_no" class="col-sm-3 control-label">Account Number<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::text('account_no','',['class' => 'form-control','placeholder' => 'Account Number'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                             <div class="form-group" id="payment_method_id">
                                <label for="payment_method_id" class="col-sm-3 control-label">Payment Method <span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::select('payment_method_id',['' => 'Please Select']+$payment_methods,null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                         <div class="form-group" id="sin">
                                <label for="sin" class="col-sm-3 control-label">SIN<span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                     {{Form::text('sin','',['class' => 'form-control','placeholder' => 'SIN'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>


                    </div>
                    <!---Banking tab End--->

                     <!---Tax tab start--->
                    <div role="tabpanel" class="tab-pane" id="taxTab">
                        <div class="form-group" id="federal_td1_claim">
                                <label for="federal_td1_claim" class="col-sm-3 control-label">Federal TD1 Claim </label>
                                <div class="col-sm-8">
                                     {{Form::text('federal_td1_claim','',['class' => 'form-control','placeholder' => 'Federal TD1 Claim'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="provincial_td1_claim">
                                <label for="provincial_td1_claim" class="col-sm-3 control-label">Provincial TD1 Claim </label>
                                <div class="col-sm-8">
                                     {{Form::text('provincial_td1_claim','',['class' => 'form-control','placeholder' => 'Provincial TD1 Claim'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="is_cpp_exempt">
                                <label for="is_cpp_exempt" class="col-sm-3 control-label">CPP Exempt</label>
                                <div class="col-sm-8">
                                      {{Form::select('is_cpp_exempt',[''=>"Please select",0=>"No",1=>"Yes"],null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                              <div class="form-group" id="is_uic_exempt">
                                <label for="is_uic_exempt" class="col-sm-3 control-label">UIC Exempt</label>
                                <div class="col-sm-8">
                                      {{Form::select('is_uic_exempt',[''=>"Please select",0=>"No",1=>"Yes"],null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="tax_province">
                                <label for="tax_province" class="col-sm-3 control-label">Tax Province </label>
                                <div class="col-sm-8">
                                     {{Form::text('tax_province','',['class' => 'form-control','placeholder' => 'Tax Province '])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                             <div class="form-group" id="epaystub_email">
                                <label for="epaystub_email" class="col-sm-3 control-label">EPayStub Email </label>
                                <div class="col-sm-8">
                                      {{Form::text('epaystub_email','',['class' => 'form-control','placeholder' => 'EPayStub Email'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="is_epaystub_exempt">
                                <label for="is_epaystub_exempt" class="col-sm-3 control-label">EPayStub Exempt</label>
                                <div class="col-sm-8">
                                       {{Form::select('is_epaystub_exempt',[''=>"Please select",0=>"No",1=>"Yes"],null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>


                    </div>
                    <!---Tax tab End--->


                     <!---Benefits tab start--->
                    <div role="tabpanel" class="tab-pane" id="benefitsTab">
                        <div class="form-group" id="payroll_group_id">
                                <label for="payroll_group_id" class="col-sm-3 control-label">Payroll Group <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                     {{Form::select('payroll_group_id',['' => 'Please Select']+ $payroll_group,null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="vacation_level">
                                <label for="vacation_level" class="col-sm-3 control-label">Vacation Level (%) <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                     {{Form::number('vacation_level','',['class' => 'form-control','placeholder' => 'Vacation Level In percentage','min'=>0,'max'=>100])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="green_sheild_no">
                                <label for="green_sheild_no" class="col-sm-3 control-label">Green shield No</label>
                                <div class="col-sm-8">
                                        {{Form::text('green_sheild_no','',['class' => 'form-control','placeholder' => 'Green shield No'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="is_lacapitale_life_insurance_enrolled">
                                <label for="is_lacapitale_life_insurance_enrolled" class="col-sm-3 control-label">LaCapitale Life Insurance enrolled </label>
                                <div class="col-sm-8">
                                    {{Form::select('is_lacapitale_life_insurance_enrolled',['' => 'Please Select',0=>"No",1=>"Yes"],null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>



                    </div>
                    <!---Employment tab End--->

                    <div role="tabpanel" class="tab-pane" id="employmentTab">

                            <div class="form-group" id="continuous_seniority">
                                <label for="continuous_seniority" class="col-sm-3 control-label">Continuous Seniority </label>
                                <div class="col-sm-8">
                                     {{Form::text('continuous_seniority','',['class' => 'form-control datepicker','placeholder' => 'Continuous Seniority'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="pay_detach_customer_id">
                                <label for="pay_detach_customer_id" class="col-sm-3 control-label">Pay Detach<span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                       {{Form::select('pay_detach_customer_id',['' => 'Please Select']+$customers,null,['class' => 'form-control paydetach-select2','id'=>'detach','style'=>"width: 100%;"])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>



                    </div>
                    <!---Employment tab End--->
                    <!---Emergency contact tab End--->

                    <div role="tabpanel" class="tab-pane" id="emergencyContactTab">

                            <div class="form-group" id="name">
                                <label for="name" class="col-sm-3 control-label">Name</label>
                                <div class="col-sm-8">
                                     {{Form::text('name','',['class' => 'form-control','placeholder' => 'Name'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="relation_id">
                                <label for="relation_id" class="col-sm-3 control-label"> Relation</label>
                                <div class="col-sm-8">
                                       {{Form::select('relation_id',['' => 'Please Select']+$relation,null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group" id="full_address">
                                <label for="full_address" class="col-sm-3 control-label">Full Address</label>
                                <div class="col-sm-8">
                                     {{Form::text('full_address','',['class' => 'form-control','placeholder' => 'Full Address'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="primary_phoneno">
                                <label for="primary_phoneno" class="col-sm-3 control-label">Primary Phone No</label>
                                <div class="col-sm-8">
                                     {{Form::text('primary_phoneno','',['class' => 'form-control phone','placeholder' => 'Primary Phone No [ format (XXX)XXX-XXXX ]'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group" id="alternate_phoneno">
                                <label for="alternate_phoneno" class="col-sm-3 control-label">Alternate Phone No</label>
                                <div class="col-sm-8">
                                     {{Form::text('alternate_phoneno','',['class' => 'form-control phone','placeholder' => 'Alternate Phone [ format (XXX)XXX-XXXX ]'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>


                    </div>
                    <!---Emergency contact tab End--->
            </div>
            <!-- Tab panes - End -->
        </div>
        <!-- Tabs View - End -->
    </div>
    <div class="modal-footer">
        {{ Form::submit('Save', array('class'=>'button btn btn-primary','id'=>'mdl_save_change'))}}
        {{ Form::reset('Cancel', array('class'=>'btn btn-primary','data-dismiss'=>'modal','aria-hidden'=>true))}}
    </div>
    {{ Form::close() }}
</div>
</div>
</div>
<!-- User Master Modal - End -->
@stop
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>

<script>
    $(function () {
        $('select#project_no').select2({
            tags: true,
    dropdownParent: $("#myModal"),
    placeholder :'Choose the Customer'
        });

        $('select.reporting_to_approver').select2({
            dropdownParent: $("#myModal"),
            placeholder :'Choose the Approver'
        });
        
        var table = $('#conversion-table').DataTable({
            fixedHeader: true,
            processing: false,
            serverSide: true,
            responsive: false,
            ajax: "{{ route('conversion.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
            [0, "desc"]
            ],
            dom: 'Blfrtip',
             columnDefs: [
                {
                    targets: 0,
                    searchable: false,
                    orderable: false,
                    sortable: false,
                    // className: 'dt-body-center',
                    render: function (data, type, full, meta) {
                        return '<input type="checkbox" id="candidate_id" class="archive-button-trigger select-record" name="candidate_id" value="' +
                            $('<div/>').text(data).html() + '">';
                    }
                },
            ],
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

            }
            ],

            lengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
            {
                data: 'candidate_id',
                name: 'candidate_id',
                sortable:false,

            },
            {
                data: 'candidate_name',
                name: 'candidate_name',
                defaultContent: "--",
            },
            {
                data: 'candidate_email',
                name: 'candidate_email',
                defaultContent: "--",
            },
            {
                data: 'job_id',
                name: 'job_id',
                defaultContent: "--",
            },
            {
                data: 'completion_date',
                name: 'completion_date',
                defaultContent: "--",
            },

          ]
      });

    /* upload script start */
      var resize = $('#candidate-image-upload').croppie({
        enableExif: true,
        enableOrientation: true,
        viewport: { // Default { width: 100, height: 100, type: 'square' }
            width: 100,
            height: 100,
            type: 'circle' //square
        },
        boundary: {
            width: 130,
            height: 130
        }
    });

    $('#candidate-image-upload').on('dblclick', function() {
        $('.candidate-image-element').trigger('click');
    });

    $('.candidate-image-div').on('click', function() {
        $('.candidate-image-element').trigger('click');
    });
    /* upload script end */

          /*Show and Hide dependent block of question on choosing yes or no - Start*/
         $('#veteran_status').on('change', function () {
            if (this.value == '1')
            {
                $(".veteran_status_qstn").removeClass('hide-this-block');
            } else
            {
                $(".veteran_status_qstn").addClass('hide-this-block');
                $(".veteran_status_qstn").find('input').val('');
            }
        });
            /*Show and Hide dependent block of question on choosing yes or no - End*/



        $('.projects').on('change', function () {
           id = this.value;
           url = "{{ route('schedule.getCustomer') }}";
           $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            data: {
                id: id
            },
            success: function (data) {
                if (data.success) {
                    $('#project_name input[name="project_name"]').val(data.data.client_name);

                }
                else {
                    console.log(data);
                }
            },
            fail: function (response) {
                console.log(response);
            },
        });

       });

        /* Display single row on adding security clearance - End */

        /* Security Clearance - Add - Start */
        $('#remove-security-clearance').hide();
        $("#myModal").on("click", "#add-security-clearance", function (e) {
            $last_row_no = $(".user-security-clearance-table").find('tr:last .row-no').val();
            if($last_row_no != undefined){
                $next_row_no = ($last_row_no*1)+1;
            }else{
                $next_row_no = 0;
            }

            var user_security_clearance_new_row =
            "<tr><td><div class='form-group' id='security_clearance_"+$next_row_no+"'><input type='hidden' name='row-no[]' class='row-no'><select class='form-control' name='security_clearance_"+$next_row_no+"'><option value='' selected>Choose security clearance</option>@foreach($security_clearances as $id=>$security_clearance)<option value='{{$id}}'>{{$security_clearance}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='valid_until_"+$next_row_no+"'><input type='text' class='form-control datepicker' name='valid_until_"+$next_row_no+"' placeholder='Valid Until(Y-m-d)'><small class='help-block'></small></div></td></tr>";
            $(".user-security-clearance-table tbody").append(user_security_clearance_new_row);
            $(".user-security-clearance-table").find('tr:last').find('.row-no').val($next_row_no);

            $("#valid_until_"+$next_row_no+">input").datepicker({
                format: "yyyy-mm-dd", maxDate: "+900y"
            });

            $(".datepicker").mask("9999-99-99");

            if($last_row_no > 0 || $last_row_no == undefined){
                $('#remove-security-clearance').show();
            }
        });
          /* Security Clearance - Add - End */


            /* User Certificates - Add - Start */
            $('#remove-certificate').hide();
           $("#myModal").on("click", "#add-certificate", function (e) {
            addCertificateRow();
        });

        function addCertificateRow()
        {
            $last_row_no = $(".user-certificate-table").find('tr:last .row-no').val();
            if($last_row_no != undefined){
                $next_row_no = ($last_row_no*1)+1;
            }else{
                $next_row_no = 0;
            }
             var user_certificate_new_row =
             "<tr><td><div class='form-group' id='certificate_"+$next_row_no+"'><input type='hidden' name='certificate-row-no[]' class='row-no'><select class='form-control' name='certificate_"+$next_row_no+"'><option value='' selected>Choose Certificates</option>@foreach($certificates as $id=>$certificate)<option value='{{$id}}'>{{$certificate}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='expiry_"+$next_row_no+"'><input type='text' class='form-control datepicker' name='expiry_"+$next_row_no+"' placeholder='Expiry Date(Y-m-d)'><small class='help-block'></small></div></td></tr>";
            $(".user-certificate-table tbody").append(user_certificate_new_row);
            $(".user-certificate-table").find('tr:last').find('.row-no').val($next_row_no);

            $("#expiry_"+$next_row_no+">input").datepicker({
                format: "yyyy-mm-dd", maxDate: "+900y"
            });

            $(".datepicker").mask("9999-99-99");

            if($last_row_no > 0 || $last_row_no == undefined){
                $('#remove-certificate').show();
            }
        }
             /* User Certificates - Add - End */


 $('.dataTable').on('change', '.archive-button-trigger', function () {
  $('input.archive-button-trigger').not(this).prop('checked', false);
   $('.employee-conversion').data('id',this.value);
     });


        /*Typing email should be username */
        $('#email').keyup(function () {
           $('#user_name').val($(this).find(':input').val());
       });

        /* Security Clearance - Remove - Start */
        $("#myModal").on("click", "#remove-security-clearance", function (e) {
           $last_row_no = $(".user-security-clearance-table").find('tr:last .row-no').val();
           if($last_row_no > -1){
            $(".user-security-clearance-table").find('tr:last').remove();
            if($last_row_no == 0){
                $('#remove-security-clearance').hide();
            }
        }else{
            $('#remove-security-clearance').hide();
        }
    });
        /* Security Clearance - Remove - End */


         /* User Certificates - Remove - Start */
        $("#myModal").on("click", "#remove-certificate", function (e) {
           $last_row_no = $(".user-certificate-table").find('tr:last .row-no').val();
            if($last_row_no > -1){
                $(".user-certificate-table").find('tr:last').remove();
                if($last_row_no == 0){
                    $('#remove-certificate').hide();
                }
            }else{
                $('#remove-certificate').hide();
            }
        });
       /* User Certificates - Remove - End */

        /*User - Submit - Start*/
        $('#user-form').submit(function (e) {
            e.preventDefault();

            $('#myModal select[name="role_id"]').prop('disabled',false);
            $('#myModal select[name="work_type_id"]').prop('disabled',false);
            $('#myModal select[name="position_id"]').prop('disabled',false);
            $('#myModal select[name="employee_vet_status"]').prop('disabled',false);
            if ($('#attachment').val()) {
                if($('#attachment')[0].files[0].size/1024 > 31000){
                    swal("Oops", "Attachment must be less than 30 MB", "warning");
                    return;
                }
            }
            var $form = $(this);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            url = "{{ route('candidateEmployee.store',['module' => 'documents']) }}";
            var formData = new FormData($('#user-form')[0]);
            formData.append( 'document_attachment', $('#attachment')[0].files[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        if($('#user-form input[name="id"]').val())
                        {
                            swal("Updated", "User has been updated successfully", "success");
                        }else{
                            swal("Created", "User has been created successfully", "success");
                        }
                        $('#user-form').trigger('reset');
                        $("#myModal").modal('hide');
                        table.ajax.reload();
                        $('.modal-backdrop').remove();
                    } else if(data.tagged) {
                        swal("Tagged", "Please unallocate tagged Project(s) to proceed with this request", "warning");
                    } else {
                        console.log(data);
                        swal("Oops", "User updation was unsuccessful", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError) {
                    swal("Error", "Please check your inputs", "warning");
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
            });
        /*User - Submit - End*/
        $('.candidate-image-element').on('change', function () {
            $('.candidate-image-upload').hide();
            $('.candidate-image-div').show();

            var reader = new FileReader();
            reader.onload = function (e) {
                result = e.target.result;
                arrTarget = result.split(';');
                imageType = arrTarget[0];
                if (imageType == 'data:image/jpg' || imageType == 'data:image/jpeg' || imageType == 'data:image/png') {
                    resize.croppie('bind',{
                        url: e.target.result
                    }).then(function(){
                        console.log('jQuery bind complete');
                    });
                    $('.candidate-image-upload').show();
                    $('.candidate-image-div').hide();
                } else {
                    $('.candidate-image-upload').hide();
                    $('.candidate-image-div').show();
                    $('.candidate-image-element').val('');
                    swal("Error", "Accept only jpg or png images", "error");
                }
            }

            if(this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }else{
                $('.candidate-image-element').val('');
            }
        });

        /*User Master - Edit - Start*/
        $(".employee-conversion").on("click", function (e) {
            $('#user-form')[0].reset();
               $(".veteran_status_qstn").addClass('hide-this-block');
             if($('input.archive-button-trigger:checked').length==0)
             {
                  swal({
                        title: "Oops",
                        text: "Please select any checkbox",
                        type: "warning",
                        showCancelButton: false,
                        showLoaderOnConfirm: true,
                        closeOnConfirm: true
                    },
                    function () {

                    });
                    return;
             }
            var id = $(this).data('id');
            $('#myModal').find('#active').show();
            $('#remove-security-clearance').show();
             $('#remove-certificate').show();
            $('#user-security-clearance tbody').find('tr').remove();
              $('#user-certificate tbody').find('tr').remove();
            $user_security_clearance_first_row = "<tr><td><div class='form-group' id='security_clearance_0'><input type='hidden' name='row-no[]' class='row-no' value='0'><select class='form-control' name='security_clearance_0'><option value='' selected>Choose security clearance</option>@foreach($security_clearances as $id=>$security_clearance)<option value='{{$id}}'>{{$security_clearance}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='valid_until_0'><input type='text' class='form-control datepicker' name='valid_until_0' placeholder='Valid Until(Y-m-d)'><small class='help-block'></small></div></td></tr>";
            $('#user-security-clearance tbody').append($user_security_clearance_first_row);
            $("#valid_until_0>input").datepicker({
                format: "yyyy-mm-dd", maxDate: "+900y"
            });
            $user_certificate_first_row =
             "<tr><td><div class='form-group' id='certificate_0'><input type='hidden' name='certificate-row-no[]' class='row-no' value='0'><select class='form-control' name='certificate_0'><option value='' selected>Choose Certificates</option>@foreach($certificates as $id=>$certificate)<option value='{{$id}}'>{{$certificate}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='expiry_0'><input type='text' class='form-control datepicker' name='expiry_0' placeholder='Expiry Date(Y-m-d)'><small class='help-block'></small></div></td></tr>";
            $("#user-certificate tbody").append($user_certificate_first_row);
        $("#expiry_0>input").datepicker({
                format: "yyyy-mm-dd", maxDate: "+900y"
        });
            $(".datepicker").mask("9999-99-99");
            $('#user-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            var url = '{{ route("candidate.show", ["id"=>":id"]) }}';
            url = url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                      var name = '';
                      let urlImagePath = '';
                      if(data.user.candidate.profile_image != null && data.user.candidate.profile_image != "") {
                        urlImagePath = '{{asset("images/uploads/") }}/' + data.user.candidate.profile_image;
                      }else{
                        urlImagePath = '{{asset("images/uploads/") }}/{{config("globals.noAvatarImg")}}';
                      }

                    $(".candidate-image-div").html('<img style="border-radius: 50%;" src="' + urlImagePath + '" height="100px" width="100px" name="image"/>');
                    $('.candidate-image-upload').hide();
                    $('.candidate-image-div').show();
                    $('select.bankname-select2').select2({
                        dropdownParent: $("#myModal"),
                        placeholder :'Please Select'
                    });
                    $('select.paydetach-select2').select2({
                        dropdownParent: $("#myModal"),
                        placeholder :'Please Select'
                    });
                      $('#myModal input[name="candidate_id"]').val(data.user.candidate_id);
                      $('#myModal input[name="first_name"]').val(data.user.candidate.first_name);
                      $('#myModal input[name="last_name"]').val(data.user.candidate.last_name);
                      $('#myModal input[name="alternate_email"]').val(data.user.candidate.email);
                      $('#myModal input[name="phone"]').val(data.user.candidate.phone_home);
                      $('#myModal input[name="cell_no"]').val(data.user.candidate.phone_cellular);
                      $('#myModal input[name="employee_address"]').val(data.user.candidate.address);
                      $('#myModal input[name="years_of_security"]').val(data.employee_data.years_of_security);
                      $('#myModal input[name="being_canada_since"]').val(data.employee_data.being_canada_since);
                    $('#myModal input[name="wage_expectations_from"]').val(data.user.candidate.wage_expectation.wage_expectations_from);
                         $('#myModal input[name="wage_expectations_to"]').val(data.user.candidate.wage_expectation.wage_expectations_to);
                      $('#myModal input[name="employee_city"]').val(data.user.candidate.city);
                      $('#myModal input[name="employee_postal_code"]').val(data.user.candidate.postal_code);
                      $('#myModal input[name="employee_dob"]').val(data.user.candidate.dob);
                      $('#myModal select[name="role_id"] option[value="'+'guard'+'"]').prop('selected',true);

                      $('#myModal input[name="vet_service_number"]').val(data.user.candidate.miscellaneous.service_number);
                      $('#myModal input[name="vet_enrollment_date"]').val(data.user.candidate.miscellaneous.enrollment_date);
                      $('#myModal input[name="vet_release_date"]').val(data.user.candidate.miscellaneous.release_date);
                      if(data.user.candidate.miscellaneous.veteran_of_armedforce == 'Yes')
                      {
                        $('#myModal select[name="employee_vet_status"] option[value=1]').prop('selected',true);
                        $(".veteran_status_qstn").removeClass('hide-this-block');
                      }else{
                        $('#myModal select[name="employee_vet_status"] option[value=0]').prop('selected',true);
                        $(".veteran_status_qstn").addClass('hide-this-block');
                      }
                      if(data.user.candidate.guarding_experience.expiry_cpr)
                      {
                        $('#myModal select[name="certificate_0"] option[value=3]').prop('selected',true);
                        $('#myModal input[name="expiry_0"]').val(data.user.candidate.guarding_experience.expiry_cpr);
                      }
                      if(data.user.candidate.guarding_experience.expiry_first_aid)
                      {
                        addCertificateRow();
                        $('#myModal select[name="certificate_1"] option[value=2]').prop('selected',true);
                        $('#myModal input[name="expiry_1"]').val(data.user.candidate.guarding_experience.expiry_first_aid);
                      }
                      if(data.user.candidate.guarding_experience.expiry_guard_license)
                      {
                        addCertificateRow();
                        $('#myModal select[name="certificate_2"] option[value=1]').prop('selected',true);
                        $('#myModal input[name="expiry_2"]').val(data.user.candidate.guarding_experience.expiry_guard_license);
                      }
                      if((data.user.job_reassigned_id==0)||(data.user.job_reassigned_id==null))
                      {
                        //   $('#myModal select[name="project_no"] option[value="'+data.job.customer.id+'"]').prop('selected',true);
                        //   if ($('#myModal select[name="project_no"]').val() != "") {
                        //       $('#myModal input[name="project_name"]').val(data.job.customer.client_name);
                        //     }
                      }
                      else
                      {
                            // $('#myModal select[name="project_no"] option[value="'+data.job_reassigned.customer.id+'"]').prop('selected',true);
                            // if ($('#myModal select[name="project_no"]').val() != "") {
                            //     $('#myModal input[name="project_name"]').val(data.job_reassigned.customer.client_name);
                            // }
                       }
                    
                    name = data.user.candidate.name;
                    $('#myModal .modal-title').text("Edit User: " + name)
                    $("#myModal").modal();

                } else {
                    swal("Error", "Unable to edit", "warning");
                }
            },
            error: function (xhr, textStatus, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                swal("Oops", "Something went wrong", "error");
            },
            contentType: false,
            processData: false,
        });
        });
        /*User Master - Edit - End*/

        $('#upload_profile_image').on('click', function(){
            var candidate_id = $("#myModal input[name=candidate_id]").val();
            var imageValue = $('.candidate-image-element').val();
            if(imageValue == "" || imageValue == null) {
                swal('Oops','Please choose any valid image', 'error');
            }
            resize.croppie('result', {
                type: 'canvas',
                size: {width:512, height:512},
                quality: 1,
                circle: false
            }).then(function (img) {
                var url = '{{ route("candidate.profile-image-upload", ["candidate_id"=>":candidate_id"]) }}';
                url = url.replace(':candidate_id', candidate_id);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: {'image':img},
                    success: function (resp) {
                        if(resp.success) {
                            $(".candidate-image-div").html('<img style="border-radius: 50%;" src="{{asset("images/uploads/")}}/' + resp.image +'?'+new Date().getTime()+ '" height="100px" width="100px" name="image"/>');
                            $('.candidate-image-upload').hide();
                            $('.candidate-image-div').show();
                            swal('Success', resp.message, 'success');
                        }else{
                            swal('Oops', resp.message, 'error');
                        }
                    }
                });
            });
        });
    });
    $("#myModal").on("change", "#bankname", function (e) {
             var bankcode = <?php echo json_encode($bank_code); ?>;
             $('#bankcodes').val(bankcode[$(this).val()])
        });
</script>
<style type="text/css">
.nav-tabs .nav-link.active
{
  background: #003A63 !important;
}
.modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}

.croppie-container {
    width: 60%;
}

.cr-slider {
    width: 200px;
}
</style>
@stop
