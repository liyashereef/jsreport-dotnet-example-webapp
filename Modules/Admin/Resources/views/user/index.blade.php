{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
<h1>Users</h1>
@stop

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">
<style>
.croppie-container {
    width: 23%;
    height: 28%;
}

.edit-image img {
    transition: transform .5s, filter 1.5s ease-in-out;
}

/* [3] Finally, transforming the image when container gets hovered */
.edit-image:hover img {
    z-index: 9999999;
    transform:scale(1.5);
    -ms-transform:scale(1.5); /* IE 9 */
    -moz-transform:scale(1.5); /* Firefox */
    -webkit-transform:scale(1.5); /* Safari and Chrome */
    -o-transform:scale(1.5); /* Opera */
    position: relative;
}

.upload-div{
    margin-top: 10px;
}
.vision-export{
    float: right;
}
.vision-export {
    float: right;
    width: 200px;
    background-color: #f26222;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 10px;
    text-align: center;
    border-radius: 5px;
    padding: 5px 0px;
    margin-left: 5px;
    cursor: pointer;
    border: 0px;
}
.modal-body{
   max-height: calc(100vh - 200px);
    overflow-y: auto;
}
.option-adjust {
    display: inline !important;
    width: 382px !important;
}
</style>

<div id="message"></div>
@include('filter.employee-filter')
<div class="add-new" data-title="Add New User">Add <span class="add-new-label">New</span></div>
<button class="vision-export " data-title="Vision Export">Vision Export</button>
@if(Session::has('user-updated'))
    <div id="import-success-alert" class="alert alert-info fade in alert-dismissible" role="alert" style="width:50%;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
        {{ Session::get('user-updated') }}
    </div>
@endif
<fieldset>
<div id="filter">
    <label><input type="radio" class="all" name="list_type" value="dummy" checked> All</label>
    <label><input type="radio" class="active" name="list_type" value="1"> Active</label>
    <label><input type="radio" class="inactive" name="list_type" value="0"> Inactive</label>
</div>
</fieldset>
<table class="table table-bordered" id="users-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Employee No</th>
            <th>Employee Contact</th>
            <!-- <th>Created Date</th>
            <th>Last Modified Date</th> -->
            <th>Action</th>
        </tr>
    </thead>
</table>
{!! Form::open(array('route' => 'user.import.process','method'=>'POST','files'=>'true')) !!}
    <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9" style="margin-top:30px;">
            <div class="col-md-12">
                {!! Form::label('import_file','Select File to Import:',['class'=>'col-md-4']) !!}

                    {!! Form::file('import_file', array('class' => 'col-md-4', 'accept'=>'.xls,.xlsx')) !!}

                    {!! Form::submit('Upload',['class'=>'btn btn-primary col-md-1']) !!}


            </div>
            {!! $errors->first('import_file', '<div class="col-md-12"><label class="col-md-10 align-center"><p class="help-block">:message</p></label></div>') !!}
            <div class="col-md-12">
                <a href="{{asset('excel_import_template/CGL360_User_Import_Template.xlsx')}}">User Import Template Format</a>
            </div>
        </div>
    </div>
{!! Form::close() !!}

<!-- User Master Modal - Start -->
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                  <h4 class="modal-title" id="myModalLabel">User Master</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'user-form','class'=>'form-horizontal', 'method'=> 'POST', 'novalidate'=>TRUE)) }}
            {{ Form::hidden('id', "") }}
            <div class="modal-body">

                <!-- Active Toggle button - Start -->
                <div class="form-group col-sm-12" id="active" style="display:none;">
                    <label class="switch" style="float:right;">
                      <input name="active" type="checkbox" class="actives">
                      <span class="slider round"></span>
                    </label>
                    <label style="float:right;padding-right: 5px;">Active</label>
                </div>
                <!-- Active Toggle button - End -->

                <!-- Tabs View - Start -->
                <div role="tabpanel">
                    <!-- Nav tabs - Start -->
                    <ul class="nav nav-tabs" id="userTabs">
                        <li role="presentation" class="active show"><a href="#userTab" aria-controls="userTab" role="tab" data-toggle="tab">User</a></li>
                        <li role="presentation"><a href="#employeeTab" aria-controls="employeeTab" role="tab" data-toggle="tab">Profile</a></li>
                        <li role="presentation"><a href="#securityClearanceTab" aria-controls="securityClearanceTab" role="tab" data-toggle="tab">Security Clearance</a></li>
                        <li role="presentation"><a href="#candidateTransitionTab" aria-controls="candidateTransitionTab" role="tab" data-toggle="tab">Candidate Transition</a></li>
                        <li role="presentation"><a href="#certificateTab" aria-controls="certificateTab" role="tab" data-toggle="tab">Certificates</a></li>
                        <li role="presentation"><a href="#expenseTab" aria-controls="expenseTab" role="tab" data-toggle="tab">Expense</a></li>
                        <li role="presentation"><a href="#dashboardTab" aria-controls="dashboardTab" role="tab" data-toggle="tab">Dashboard</a></li>
                        <li role="presentation"><a href="#bankingTab" aria-controls="bankingTab" role="tab" data-toggle="tab">Banking</a></li>
                        <li role="presentation"><a href="#taxTab" aria-controls="taxTab" role="tab" data-toggle="tab">Tax</a></li>
                        <li role="presentation"><a href="#benefitsTab" aria-controls="benefitsTab" role="tab" data-toggle="tab">Benefits</a></li>
                        <li role="presentation"><a href="#employmentTab" aria-controls="employmentTab" role="tab" data-toggle="tab">Employment</a></li>
                         <li role="presentation"><a href="#emergencyContactTab" aria-controls="emergencyContactTab" role="tab" data-toggle="tab">Emergency Contact</a></li>
                         <li role="presentation"><a href="#uniformTab" aria-controls="uniformTab" role="tab" data-toggle="tab">Uniform</a></li>
                        <li role="presentation"><a href="#skillTab" aria-controls="skillTab" role="tab" data-toggle="tab">Skills</a></li>
                        </ul>
                    <!-- Nav tabs - End -->

                    <!-- Tab panes - Start -->
                    <div class="tab-content tab-alignment">
                        <div role="tabpanel" class="tab-pane active" id="userTab">
                            <div class="form-group row" id="salutation_id">
                                <label for="salutation_id" class="col-sm-3 control-label"> Salutation <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                    {{Form::select('salutation_id',['' => 'Please Select']+$salutation,null,['class' => 'form-control'])}}
                                     <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="first_name">
                                <label for="name" class="col-sm-3 control-label">First Name <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('first_name',"",['class' => 'form-control has-error','placeholder' => 'First Name'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="last_name">
                                <label for="last_name" class="col-sm-3 control-label">Last Name</label>
                                <div class="col-sm-8">
                                    {{Form::text('last_name',"",['class' => 'form-control has-error','placeholder' => 'Last Name'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group active row" id="email">
                                <label for="email" class="col-sm-3 control-label">Commgl Email <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                    {{Form::email('email',"",['class' => 'form-control','placeholder' => 'Commgl Email'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="alternate_email">
                                <label for="alternate_email" class="col-sm-3 control-label">Alternate Email</label>
                                <div class="col-sm-8">
                                    {{Form::email('alternate_email',"",['class' => 'form-control','placeholder' => 'Alternate Email'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="username">
                                <label for="username" class="col-sm-3 control-label">Username <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('username',"",['class' => 'form-control has-error','placeholder' => 'Username'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="password">
                                <label for="password" class="col-sm-3 control-label">Password <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                    {{Form::password('password',['class' => 'form-control','placeholder' => '********'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="role_id">
                                <label for="role_id" class="col-sm-3 control-label">Role <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                    {{Form::select('role_id',$roles,null,['class' => 'form-control role select2','placeholder' => 'Choose the role','id'=>'role_id','style'=>"width: 100%;"])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="gender">
                                <label for="role_id" class="col-sm-3 control-label">Gender <span class="mandatory">*</span></label>
                                <div class="col-sm-8" style="padding-top:6px;">
                                      <span> <input type="radio" name="gender" value="0" id="0"> Male </span>
                                      <span> <input type="radio" name="gender" value="1" id="1"> Female </span>
                                 <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group disable_client row" id="marital_status_id">
                                <label for="role_id" class="col-sm-3 control-label">Marital Status</label>
                                <div class="col-sm-8">
                                     {{Form::select('marital_status_id',$marital_status,null,['class' => 'form-control','placeholder' => 'Choose marital status','id'=>'marital_status'])}}
                                 <span class="help-block"></span>
                                </div>
                            </div>

                        </div>
                        <div role="tabpanel" class="tab-pane" id="employeeTab">
                            <div class="form-group disable_client row" id="employee_no">
                                <label for="employee_no" class="col-sm-3 control-label">Employee No <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('employee_no',"",['class' => 'form-control','placeholder' => 'Employee No','maxlength' => '6'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="phone">
                                <label for="phone" class="col-sm-3 control-label">Phone <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('phone',"",['class' => 'form-control phone','placeholder' => 'Phone [ format (XXX)XXX-XXXX ]'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="phone_ext">
                                <label for="phone_ext" class="col-sm-3 control-label">Ext. </label>
                                <div class="col-sm-8">
                                    {{Form::text('phone_ext',"",['class' => 'form-control','placeholder' => 'Ext.'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="cell_no">
                                <label for="cell_no" class="col-sm-3 control-label">Cell</label>
                                <div class="col-sm-8">
                                    {{Form::text('cell_no',"",['class' => 'form-control phone','placeholder' => 'Cell No [ format (XXX)XXX-XXXX ]','pattern' => '[\(]\d{3}[\)]\d{3}[\-]\d{4}'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="disable_client">
                            <div class="form-group row" id="work_type_id">
                                <label for="work_type_id" class="col-sm-3 control-label">Work Type <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                    {{Form::select('work_type_id',$work_types,null,['class' => 'form-control','placeholder' => 'Choose the work type'])}}
                                    {!! $errors->first('work_type_id', '<small class="help-block">:message</small>') !!}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="employee_address">
                                <label for="employee_address" class="col-sm-3 control-label">Address</label>
                                <div class="col-sm-8">
                                    {{Form::text('employee_address',"",['class' => 'form-control','placeholder' => 'Address'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                        {{--<div class="form-group" id="employee_full_address">
                                <label for="employee_full_address" class="col-sm-3 control-label">Full Address</label>
                                <div class="col-sm-8">
                                    {{Form::text('employee_full_address',"",['class' => 'form-control','placeholder' => 'Full Address'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>--}}
                            <div class="form-group row" id="employee_city">
                                <label for="employee_city" class="col-sm-3 control-label">City</label>
                                <div class="col-sm-8">
                                    {{Form::text('employee_city',"",['class' => 'form-control','placeholder' => 'City'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="employee_postal_code">
                                <label for="employee_postal_code" class="col-sm-3 control-label">Postal Code <span class="mandatory"></span></label>
                                <div class="col-sm-8">
                                    {{Form::text('employee_postal_code',"",['class' => 'form-control postal-code','placeholder' => 'Postal Code','required'=>true])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="employee_work_email">
                                <label for="employee_work_email" class="col-sm-3 control-label">Work Email</label>
                                <div class="col-sm-8">
                                    {{Form::text('employee_work_email',"",['class' => 'form-control','placeholder' => 'Work Email'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="employee_doj">
                                <label for="employee_doj" class="col-sm-3 control-label">DOJ</label>
                                <div class="col-sm-8">
                                    {{Form::text('employee_doj',"",['class' => 'form-control datepicker','placeholder' => 'Date of Joining (Y-m-d)'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="employee_dob">
                                <label for="employee_dob" class="col-sm-3 control-label">DOB</label>
                                <div class="col-sm-8">
                                    {{Form::text('employee_dob',"",['class' => 'form-control datepicker' ,'placeholder' => 'Date of Birth (Y-m-d)'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="current_project_wage">
                                <label for="current_project_wage" class="col-sm-3 control-label">Current Project Wage</label>
                                <div class="col-sm-8">
                                    {{Form::text('current_project_wage',"",['class' => 'form-control','placeholder' => 'Current Project Wage'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="position_id">
                                <label for="position_id" class="col-sm-3 control-label">Position</label>
                                <div class="col-sm-8">
                                    {{Form::select('position_id',$positions,null,['class' => 'form-control','placeholder' => 'Choose the Position'])}}
                                    {!! $errors->first('position_id', '<small class="help-block">:message</small>') !!}
                                </div>
                            </div>
                            <div class="form-group row" id="years_of_security">
                                <label for="years_of_security"
                                class="col-sm-3 control-label">Years of Security</label>
                                <div class="col-sm-8">
                                    {{Form::number('years_of_security',"",['class' => 'form-control','placeholder' => 'Years of Security'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="being_canada_since">
                                <label for="being_canada_since" class="col-sm-3 control-label">Arrival In Canada</label>
                                <div class="col-sm-8">
                                    {{Form::text('being_canada_since',"",['id' => 'being_canada_since','class' => 'form-control datepicker' ,'placeholder' => ''])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="wage_expectations_from">
                                <label for="wage_expectations_from" class="col-sm-3 control-label">Wage Expectation From</label>
                                <div class="col-sm-8">
                                    {{Form::text('wage_expectations_from',"",['class' => 'form-control','placeholder' => 'Wage Expectation From'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="wage_expectations_to">
                                <label for="wage_expectations_to" class="col-sm-3 control-label">Wage Expectation To</label>
                                <div class="col-sm-8">
                                    {{Form::text('wage_expectations_to',"",['class' => 'form-control','placeholder' => 'Wage Expectation To'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="termination_date">
                                <label for="termination_date" class="col-sm-3 control-label"> Termination Date </label>
                                <div class="col-sm-8">
                                    {{Form::text('termination_date',"",['class' => 'form-control datepicker' ,'placeholder' => 'Termination Date (Y-m-d)'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>

                          {{--   <div class="form-group" id="employee_vet_status">
                                <label for="employee_vet_status" class="col-sm-3 control-label">@lang('Is Employee Veteran')</label>
                                <div class="col-sm-8">
                                    {{Form::select('employee_vet_status',[1=>"Yes",0=>"No"],null,['class' => 'form-control','placeholder' => 'Choose the Veteran Status'])}}
                                    {!! $errors->first('employee_vet_status', '<small class="help-block">:message</small>') !!}
                                </div>
                            </div> --}}
                        </div>

                        <div class="form-group row" id="image">
                                <label for="image" class="col-sm-3 control-label">Image</label>
                                <div class="col-sm-8">
                                    {{Form::file('profile_image', ['id' => 'image_input', 'style' => 'display: none;'])}}
                                    <div class="upload-image upload-div" style="display: none;"></div>
                                    <div class="edit-image upload-div" style="display: none;"></div>
                                    <small class="help-block"></small>
                                </div>
                            </div>
                    </div>
                        <div role="tabpanel" class="tab-pane" id="securityClearanceTab">
                            <div class="col-sm-12 table-responsive pop-in-table" id="user-security-clearance">
                            <table class="table table-bordered user-security-clearance-table">
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
                            <div class="form-group col-sm-12 row">
                                <label for="add-security-clearance" id="add-security-clearance" class="col-sm-1 btn btn-primary" style="margin-right:1%;">+</label>
                                <label for="remove-security-clearance" id="remove-security-clearance" class="col-sm-1 btn btn-primary">-</label>
                            </div>
                        </div>

                          <div role="tabpanel" class="tab-pane" id="candidateTransitionTab">
                            <div class="form-group row" id="updated_by">
                                <label for="updated_by" class="col-sm-3 control-label">Updated By</label>
                                <div class="col-sm-8">
                                    {{Form::text('updated_by',"",['class' => 'form-control','placeholder' => 'Updated By'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                             <div class="form-group row" id="employee_num">
                                <label for="employee_num" class="col-sm-3 control-label">Employee No</label>
                                <div class="col-sm-8">
                                    {{Form::text('employee_num',"",['class' => 'form-control','placeholder' => 'Employee Number'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="updated_time">
                                <label for="updated_time" class="col-sm-3 control-label">Date of Conversion</label>
                                <div class="col-sm-8">
                                    {{Form::text('updated_time','',['class' => 'form-control','placeholder' => 'Updated Time'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                        </div>

                         <div role="tabpanel" class="tab-pane" id="certificateTab">
                           <div class="form-group row" id="employee_vet_status">
                                <label for="employee_vet_status" class="col-sm-3 control-label">@lang('Is Employee Veteran')</label>
                                <div class="col-sm-8">
                                    {{Form::select('employee_vet_status',[0=>"No",1=>"Yes"],null,['class' => 'form-control','id'=>'veteran_status'])}}
                                    {!! $errors->first('employee_vet_status', '<small class="help-block">:message</small>') !!}
                                </div>
                            </div>

                            <div class=" veteran_status_qstn hide-this-block">
                            <div class="form-group row" id="vet_service_number">
                                <label for="vet_service_number" class="col-sm-3 control-label">@lang('Service Number?')<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('vet_service_number','',['class' => 'form-control','placeholder' => 'Service Number'])}}
                                     <small class="help-block"></small>
                                </div>
                            </div>
                                <div class="form-group row" id="vet_enrollment_date">
                                <label for="vet_enrollment_date" class="col-sm-3 control-label">@lang('Enrollment Date?')<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('vet_enrollment_date','',['class' => 'form-control datepicker','placeholder' => 'Enrollment Date'])}}
                                   <small class="help-block"></small>
                                </div>
                            </div>
                             <div class="form-group row" id="vet_release_date">
                                <label for="vet_release_date" class="col-sm-3 control-label">@lang('Release Date?')<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                    {{Form::text('vet_release_date','',['class' => 'form-control datepicker','placeholder' => 'Release Date'])}}
                                     <small class="help-block"></small>
                                </div>
                            </div>
                            </div>
                            <div class="col-sm-12 table-responsive pop-in-table" id="user-certificate">
                            <table class="table table-bordered user-certificate-table">
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
                          <div class="form-group col-sm-12 row">
                                <label for="add-certificate" id="add-certificate" class="col-sm-1 btn btn-primary" style="margin-right:1%;">+</label>
                                <label for="remove-certificate" id="remove-certificate" class="col-sm-1 btn btn-primary">-</label>
                            </div>
                        </div>

                    <!---Expense tab start--->
                        <div role="tabpanel" class="tab-pane" id="expenseTab">

                            <div class="form-group row" id="reporting_to_id">
                                <label for="reporting_to_id" class="col-sm-3 control-label">Reporting To (Approver)</label>
                                <div class="col-sm-8">
                        <select class="form-control reporting_to_approver" name="reporting_to_id" placeholder="Choose the Approver">
                            @foreach($approversList as $key => $approvers)
                                <option @if($key==2)selected @endif value="{{$key}}">{{$approvers}}</option>
                            @endforeach
                        </select>
                                    {!! $errors->first('reporting_to_id', '<small class="help-block">:message</small>') !!}
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group row" id="max_allowable_expense">
                                <label for="max_allowable_expense" class="col-sm-3 control-label">
                                    @lang('Max: Allowable Expense')</label>
                                <div class="col-sm-8">
                                    {{Form::text('max_allowable_expense','',['class' => 'form-control',
                                            'placeholder' => 'Max: Allowable Expense','maxlength' => 7])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                        </div>
                    <!---Expense tab End--->
                    <!---Dashboard tab start--->
                    <div role="tabpanel" class="tab-pane" id="dashboardTab">
                        @foreach ($employeecompliancereports as $compliancereport)
                            <div class="form-group row" >
                                <label for="dashboardreport_{{$compliancereport->id}}"
                                class="col-sm-3 control-label">
                                    {{$compliancereport->display_name}}
                                </label>
                                <div class="col-sm-8">
                                    <input id="dashboardreport_{{$compliancereport->id}}"
                                    class="form-check-input" type="checkbox"
                                    name="dashboardreport_{{$compliancereport->id}}" />
                                </div>
                            </div>
                        @endforeach

                    </div>
                    <!---Dashboard tab End--->
                    <!---Banking tab start--->
                    <div role="tabpanel" class="tab-pane" id="bankingTab">
                        <div class="form-group row" id="bankid">
                                <label for="bankname" class="col-sm-3 control-label">Bank Name <span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::select('bankid', ['' => 'Please Select']+ $banks,null,['class' => 'form-control','id'=>'bankname'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="bankcode">
                                <label for="bankcode" class="col-sm-3 control-label">Bank Code <span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::text('bankcode','',['class' => 'form-control','placeholder' => 'Bank Code','id'=>'bankcodes','readonly'=>true])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="transit">
                                <label for="transit" class="col-sm-3 control-label">Transit<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::text('transit','',['class' => 'form-control','placeholder' => 'Transit'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="account_no">
                                <label for="account_no" class="col-sm-3 control-label">Account Number<span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::text('account_no','',['class' => 'form-control','placeholder' => 'Account Number'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                             <div class="form-group row" id="payment_method_id">
                                <label for="payment_method_id" class="col-sm-3 control-label">Payment Method <span class="mandatory"> *</span></label>
                                <div class="col-sm-8">
                                     {{Form::select('payment_method_id',['' => 'Please Select']+$payment_methods,null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                         <div class="form-group row" id="sin">
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
                        <div class="form-group row" id="federal_td1_claim">
                                <label for="federal_td1_claim" class="col-sm-3 control-label">Federal TD1 Claim </label>
                                <div class="col-sm-8">
                                     {{Form::text('federal_td1_claim','',['class' => 'form-control','placeholder' => 'Federal TD1 Claim'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="provincial_td1_claim">
                                <label for="provincial_td1_claim" class="col-sm-3 control-label">Provincial TD1 Claim </label>
                                <div class="col-sm-8">
                                     {{Form::text('provincial_td1_claim','',['class' => 'form-control','placeholder' => 'Provincial TD1 Claim'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="is_cpp_exempt">
                                <label for="is_cpp_exempt" class="col-sm-3 control-label">CPP Exempt</label>
                                <div class="col-sm-8">
                                      {{Form::select('is_cpp_exempt',[''=>"Please select",0=>"No",1=>"Yes"],null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                              <div class="form-group row" id="is_uic_exempt">
                                <label for="is_uic_exempt" class="col-sm-3 control-label">UIC Exempt</label>
                                <div class="col-sm-8">
                                      {{Form::select('is_uic_exempt',[''=>"Please select",0=>"No",1=>"Yes"],null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="tax_province">
                                <label for="tax_province" class="col-sm-3 control-label">Tax Province </label>
                                <div class="col-sm-8">
                                     {{Form::text('tax_province','',['class' => 'form-control','placeholder' => 'Tax Province '])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                             <div class="form-group row" id="epaystub_email">
                                <label for="epaystub_email" class="col-sm-3 control-label">EPayStub Email </label>
                                <div class="col-sm-8">
                                      {{Form::text('epaystub_email','',['class' => 'form-control','placeholder' => 'EPayStub Email'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="is_epaystub_exempt">
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
                        <div class="form-group row" id="payroll_group_id">
                                <label for="payroll_group_id" class="col-sm-3 control-label">Payroll Group <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                     {{Form::select('payroll_group_id',['' => 'Please Select']+ $payroll_group,null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="vacation_level">
                                <label for="vacation_level" class="col-sm-3 control-label">Vacation Level (%) <span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                     {{Form::number('vacation_level','',['class' => 'form-control','placeholder' => 'Vacation Level In percentage','min'=>0,'max'=>100])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="green_sheild_no">
                                <label for="green_sheild_no" class="col-sm-3 control-label">Green shield No</label>
                                <div class="col-sm-8">
                                        {{Form::text('green_sheild_no','',['class' => 'form-control','placeholder' => 'Green shield No'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="is_lacapitale_life_insurance_enrolled">
                                <label for="is_lacapitale_life_insurance_enrolled" class="col-sm-3 control-label">LaCapitale Life Insurance enrolled </label>
                                <div class="col-sm-8">
                                    {{Form::select('is_lacapitale_life_insurance_enrolled',['' => 'Please Select',0=>"No",1=>"Yes"],null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            


                    </div>
                    <!---Employment tab End--->

                    <div role="tabpanel" class="tab-pane" id="employmentTab">

                            <div class="form-group row" id="continuous_seniority">
                                <label for="continuous_seniority" class="col-sm-3 control-label">Continuous Seniority </label>
                                <div class="col-sm-8">
                                     {{Form::text('continuous_seniority','',['class' => 'form-control datepicker','placeholder' => 'Continuous Seniority'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="pay_detach_customer_id">
                                <label for="pay_detach_customer_id" class="col-sm-3 control-label">Pay Detach<span class="mandatory">*</span></label>
                                <div class="col-sm-8">
                                       {{Form::select('pay_detach_customer_id',['' => 'Please Select']+$customers,null,['class' => 'form-control select2','id'=>'detach','style'=>"width: 100%;"])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>



                    </div>
                    <!---Employment tab End--->
                    <!---Emergency contact tab End--->

                    <div role="tabpanel" class="tab-pane" id="emergencyContactTab">

                            <div class="form-group row" id="name">
                                <label for="name" class="col-sm-3 control-label">Name</label>
                                <div class="col-sm-8">
                                     {{Form::text('name','',['class' => 'form-control','placeholder' => 'Name'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="relation_id">
                                <label for="relation_id" class="col-sm-3 control-label"> Relation</label>
                                <div class="col-sm-8">
                                       {{Form::select('relation_id',['' => 'Please Select']+$relation,null,['class' => 'form-control'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="full_address">
                                <label for="full_address" class="col-sm-3 control-label">Full Address</label>
                                <div class="col-sm-8">
                                     {{Form::text('full_address','',['class' => 'form-control','placeholder' => 'Full Address'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group row" id="primary_phoneno">
                                <label for="primary_phoneno" class="col-sm-3 control-label">Primary Phone No</label>
                                <div class="col-sm-8">
                                     {{Form::text('primary_phoneno','',['class' => 'form-control phone','placeholder' => 'Primary Phone No [ format (XXX)XXX-XXXX ]'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group row" id="alternate_phoneno">
                                <label for="alternate_phoneno" class="col-sm-3 control-label">Alternate Phone No</label>
                                <div class="col-sm-8">
                                     {{Form::text('alternate_phoneno','',['class' => 'form-control phone','placeholder' => 'Alternate Phone [ format (XXX)XXX-XXXX ]'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>



                    </div>
                    <!---Emergency contact tab End--->

                    <!---Uniform tab Start--->

                    <div role="tabpanel" class="tab-pane" id="uniformTab">
                        <div class="row">
                            <div class="col-sm-6" >
                            <div class="form-group row" id="ura_balance">
                                <label for="ura_balance" class="col-sm-5 control-label">URA Balance</label>
                                <div class="col-sm-7">
                                     {{Form::text('ura_balance','',['class' => 'form-control','placeholder' => 'URA Balance', 'readonly'=>true,'disabled'=>true])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="hours_worked_to_date">
                                <label for="hours_worked_to_date" class="col-sm-5 control-label">Hours worked to date</label>
                                <div class="col-sm-7">
                                     {{Form::text('hours_worked_to_date','',['class' => 'form-control','placeholder' => 'Hours worked to date','readonly'=>true,'disabled'=>true])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            </div>
                            <div class="col-sm-6" >
                            <div class="form-group row" id="ura_earned">
                                <label for="ura_earned" class="col-sm-4 control-label">URA Earned</label>
                                <div class="col-sm-7">
                                     {{Form::text('ura_earned','',['class' => 'form-control','placeholder' => 'URA Earned','readonly'=>true,'disabled'=>true])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row" id="rate_applied">
                                <label for="rate_applied" class="col-sm-4 control-label">Rate applied</label>
                                <div class="col-sm-7">
                                     {{Form::text('rate_applied','',['class' => 'form-control','placeholder' => 'Rate applied','readonly'=>true,'disabled'=>true])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            </div>
                        </div>
                    <br>

                    </div>
                    <!---Uniform tab End--->
                       <div role="tabpanel" class="tab-pane" id="skillTab">
                            <div class="col-sm-12 table-responsive pop-in-table" id="user-skill">
                            <table class="table table-bordered user-skill-table">
                                <thead>
                                    <tr>
                                        <th>Skill</th>
                                        <th>Skill Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="add-skill" id="add-skill" class="col-sm-1 btn btn-primary" style="margin-right:1%;">+</label>
                                <label for="remove-skill" id="remove-skill" class="col-sm-1 btn btn-primary">-</label>
                            </div>
                        </div>

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
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>
<script>
    var croppedImage;
    $(function () {

        function collectFilterData() {
            return {
                employee_id:$("#employee-name-filter").val(),
            }
        }

        $("#employee-name-filter").change(function(){
            table.ajax.reload();
        });

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            $('select#detach').select2({
            dropdownParent: $("#myModal"),
            placeholder :'Choose the customer'
            });
            $('select#role_id').select2({
            dropdownParent: $("#myModal"),
            placeholder :'Choose the role'
            });
            $('select#employee-name-filter').select2();
        var table = $('#users-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    },
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Users');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":"{{ route('user.list') }}",
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());
                        },
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[naodme="csrf-token"]').attr('content')
            },
            order: [[ 1, "asc" ]],
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'full_name', name: 'full_name'},
                {data: 'username', name: 'username'},
                {data: 'email', name: 'email'},
                {data: 'roles', name: 'roles'},
                {data: 'emp_no', name: 'emp_no'},
                {data: null, name: 'phone', render:function(data){
                    return data.phone_ext!=null?(data.phone+' x'+data.phone_ext):data.phone;
                }},
                /*{data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},*/
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete  {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                },
            ],
        });
        } catch(e){
            console.log(e.stack);
        }


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

        /*Hide tabs and fields on choosing Client Role-Start */
         $('.role').on('change', function () {
         populateClientDetils($(this).val());
         });


         function populateClientDetils(role)
         {
         if(role=='client')
            {
            $('[href="#certificateTab"]').closest('li').hide();
            $('[href="#securityClearanceTab"]').closest('li').hide();
            $('[href="#expenseTab"]').closest('li').hide();
            $('[href="#dashboardTab"]').closest('li').hide();
            $('[href="#bankingTab"]').closest('li').hide();
            $('[href="#taxTab"]').closest('li').hide();
            $('[href="#benefitsTab"]').closest('li').hide();
            $('[href="#employmentTab"]').closest('li').hide();
            $('[href="#emergencyContactTab"]').closest('li').hide();
            $('.disable_client').hide();
            }
            else
            {
            $('[href="#certificateTab"]').closest('li').show();
            $('[href="#securityClearanceTab"]').closest('li').show();
            $('[href="#expenseTab"]').closest('li').show();
            $('[href="#dashboardTab"]').closest('li').show();
            $('[href="#bankingTab"]').closest('li').show();
            $('[href="#taxTab"]').closest('li').show();
            $('[href="#benefitsTab"]').closest('li').show();
            $('[href="#employmentTab"]').closest('li').show();
            $('[href="#emergencyContactTab"]').closest('li').show();
            $('.disable_client').show();
            }
         }
        /*Hide tabs and fields on choosing Client Role-End */

        /*Filters for Active and Inactive users - Start*/
        $('#filter').on('click', 'input[type="radio"]', function () {
            if(isNaN($(this).val()))
            {
                var url = "{{ route('user.list') }}";
                table.ajax.url(url).load();
            }else{
                var url = "{{ route('user.list',':status') }}";
                url = url.replace(":status",$(this).val());
                table.ajax.url(url).load();
            }
        });
        /*Filters for Active and Inactive users - End*/

        /*User - Submit - Start*/
        $('#user-form').submit(function (e) {
            e.preventDefault();
            var imageValue = $('#image_input').val();
            $('#myModal #0,#1').attr('disabled',false)
            $('#myModal .select2').attr('disabled',false);
            $('#myModal .datepicker').attr('disabled',false);
            $('#myModal select').attr('disabled',false);
            $('#myModal input[name=termination_date]').attr('disabled',false);
            $('#myModal input[type=checkbox]:not(.actives)').attr('disabled',false);
            $('#myModal select[name="role_id"]').prop('disabled',false);
            $('#myModal select[name="work_type_id"]').prop('disabled',false);
            $('#myModal select[name="position_id"]').prop('disabled',false);
            $('#myModal select[name="employee_vet_status"]').prop('disabled',false);
            var $form = $(this);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            url = "{{ route('user.store') }}";
            var formData = new FormData($('#user-form')[0]);
            formData.append("image",croppedImage)
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
            //});
        });
        /*User - Submit - End*/

        $('select.reporting_to_approver').select2({
            dropdownParent: $("#myModal"),
            placeholder :'Choose the Approver',
            width: '100%'
        });

        /*User Master - Edit - Start*/
        var ura_rate={!!json_encode($ura_rate)!!};

        $("#users-table").on("click", ".edit", function (e) {
             $('#myModal').find('form').trigger('reset');

            $('[href="#candidateTransitionTab"]').closest('li').hide();
            var id = $(this).data('id');
            $('#myModal').find('#active').show();
            $('#userTabs li:first-child a').tab('show'); //To default tab to first
            $('#myModal #password').find('span').hide();
            $(".user-security-clearance-table tbody tr").remove();
            $(".user-certificate-table tbody tr").remove();
            $(".user-skill-table tbody tr").remove();
            $('select#detach').val(null).trigger('change');
            $('select#role_id').val(null).trigger('change');
            $('#myModal input[name="ura_balance"]').attr('readonly', 'true');
            $('#myModal input[name="hours_worked_to_date"]').attr('readonly', 'true');
            $('#myModal input[name="ura_earned"]').attr('readonly', 'true');
            $('#myModal input[name="rate_applied"]').attr('readonly', 'true');
            $('#user-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            var url = '{{ route("user.show", ["id"=>":id"]) }}';
            url = url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        // console.log(data)
                        var role_id=data.role_id;
                        var name = '';

                        populateClientDetils(data.roles[0].name);
                        $('#myModal').find('input[name="active"]').prop('checked',data.active)
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="first_name"]').val(data.first_name);
                        $('#myModal input[name="last_name"]').val(data.last_name);
                        $('#myModal input[name="email"]').val(data.email);
                        $('#myModal input[name="alternate_email"]').val(data.alternate_email);
                        $('#myModal input[name="employee_no"]').val(data.employee.employee_no);
                        $('#myModal input[name="phone"]').val(data.employee.phone);
                        $('#myModal input[name="phone_ext"]').val(data.employee.phone_ext);
                        $('#myModal input[name="cell_no"]').val(data.employee.cell_no);
                        $('#myModal input[name="employee_address"]').val(data.employee.employee_address);
                        if(data.gender){
                        $("#"+data.gender).prop("checked", true);
                         }
                         if(data.termination_date) {
                            $('#myModal input[name="termination_date"]').val(data.termination_date);
                         }
                        $('#myModal select[name="salutation_id"] option[value="'+data.salutation_id+'"]').prop('selected',true);
                        $('#myModal select[name="marital_status_id"] option[value="'+data.marital_status_id+'"]').prop('selected',true);
                        $('#myModal input[name="sin"]').val(data.sin);
                        if(data.user_bank){
                        $('#myModal select[name="bankid"] option[value="'+data.user_bank.bank_id+'"]').prop('selected',true).change();
                        $('#myModal input[name="transit"]').val(data.user_bank.transit);
                        $('#myModal input[name="account_no"]').val(data.user_bank.account_no);
                        $('#myModal select[name="payment_method_id"] option[value="'+data.user_bank.payment_method_id+'"]').prop('selected',true);
                        }
                        if(data.user_tax){
                        $('#myModal input[name="federal_td1_claim"]').val(data.user_tax.federal_td1_claim);
                        $('#myModal input[name="provincial_td1_claim"]').val(data.user_tax.provincial_td1_claim);
                        $('#myModal select[name="is_cpp_exempt"] option[value="'+data.user_tax.is_cpp_exempt+'"]').prop('selected',true);
                        $('#myModal select[name="is_uic_exempt"] option[value="'+data.user_tax.is_uic_exempt+'"]').prop('selected',true);

                        $('#myModal input[name="tax_province"]').val(data.user_tax.tax_province);
                        $('#myModal input[name="epaystub_email"]').val(data.user_tax.epaystub_email);

                        $('#myModal select[name="is_epaystub_exempt"] option[value="'+data.user_tax.is_epaystub_exempt+'"]').prop('selected',true);
                        }
                        if(data.user_benefits){
                        $('#myModal select[name="payroll_group_id"] option[value="'+data.user_benefits.payroll_group_id+'"]').prop('selected',true);
                        $('#myModal input[name="vacation_level"]').val(data.user_benefits.vacation_level);
                        $('#myModal input[name="green_sheild_no"]').val(data.user_benefits.green_sheild_no);
                        $('#myModal select[name="is_lacapitale_life_insurance_enrolled"] option[value="'+data.user_benefits.is_lacapitale_life_insurance_enrolled+'"]').prop('selected',true);
                        }
                        if(data.user_employments){
                        $('#myModal input[name="continuous_seniority"]').val(data.user_employments.continuous_seniority);
                        $('#myModal select[name="pay_detach_customer_id"] option[value="'+data.user_employments.pay_detach_customer_id+'"]').prop('selected',true).change();
                        }
                        if(data.user_emergency_contact){
                        $('#myModal input[name="name"]').val(data.user_emergency_contact.name);
                        $('#myModal select[name="relation_id"] option[value="'+data.user_emergency_contact.relation_id+'"]').prop('selected',true);
                        $('#myModal input[name="full_address"]').val(data.user_emergency_contact.full_address);
                        $('#myModal input[name="primary_phoneno"]').val(data.user_emergency_contact.primary_phoneno);
                        $('#myModal input[name="alternate_phoneno"]').val(data.user_emergency_contact.alternate_phoneno);
                        }

                        $('#myModal input[name="rate_applied"]').val('$'+ura_rate);
                        $('#myModal input[name="ura_balance"]').val('$'+data.ura_balance);
                        $('#myModal input[name="ura_earned"]').val('$'+data.ura_earned);
                        $('#myModal input[name="hours_worked_to_date"]').val(data.ura_hours);
                        /*$('#myModal text[name="employee_full_address"]').val(data.employee.employee_full_address);*/
                        $('#myModal input[name="employee_city"]').val(data.employee.employee_city);
                        $('#myModal input[name="employee_postal_code"]').val(data.employee.employee_postal_code);
                        $('#myModal input[name="employee_work_email"]').val(data.employee.employee_work_email);
                        $('#myModal input[name="employee_doj"]').val(data.employee.employee_doj);
                        $('#myModal input[name="employee_dob"]').val(data.employee.employee_dob);
                        $('#myModal input[name="current_project_wage"]').val(data.employee.current_project_wage);
                        $('#myModal input[name="vet_service_number"]').val(data.employee.vet_service_number);
                        $('#myModal input[name="vet_enrollment_date"]').val(data.employee.vet_enrollment_date);
                        $('#myModal input[name="vet_release_date"]').val(data.employee.vet_release_date);
                        $('#myModal input[name="years_of_security"]').val(data.employee.years_of_security);
                        $('#myModal input[name="being_canada_since"]').val(data.employee.being_canada_since);
                        $('#myModal input[name="wage_expectations_from"]').val(data.employee.wage_expectations_from);
                        $('#myModal input[name="wage_expectations_to"]').val(data.employee.wage_expectations_to);
                        $('#myModal select[name="position_id"] option[value="'+data.employee.position_id+'"]').prop('selected',true);
                        if(data.employee.employee_vet_status==1)
                        {
                           $(".veteran_status_qstn").removeClass('hide-this-block');
                        }
                        if(data.dashboard_compliancereports.length>0){
                            $.each(data.dashboard_compliancereports,function(i,value){
                                setTimeout(() => {
                                    $('#dashboardreport_'+value.report_id).prop('checked', false);

                                }, 500);
                            })
                        }
                         if(data.candidate_transition!=null)
                         {
                        $('[href="#candidateTransitionTab"]').closest('li').show();
                        $('#candidateTransitionTab input').attr('readonly', 'readonly');
                        const dateTime = data.candidate_transition.updated_at;
                        const parts = dateTime.split(/[- :]/);
                        const wanted = parts[0] + '-' + parts[1] + '-' + parts[2] ;
                        const full_name=data.candidate_transition.updated_user.first_name+' '+data.candidate_transition.updated_user.last_name
                        $('#myModal input[name="employee_num"]').val(data.candidate_transition.updated_user.employee.employee_no);
                         $('#myModal input[name="updated_by"]').val(full_name);
                          $('#myModal input[name="updated_time"]').val(wanted);
                      }
                        $('#myModal #bankcodes').prop('readonly',true);
                        $('#myModal select[name="employee_vet_status"] option[value="'+data.employee.employee_vet_status+'"]').prop('selected',true);

                        $.each(data.security_clearance_user, function (key, value) {
                            var select_box_values = [];
                            var user_security_clearance_edit_row =
                             "<tr><td><div class='form-group' id='security_clearance_"+key+"'><input type='text' name='row-no[]' class='row-no' value="+key+"><select class='form-control' name='security_clearance_"+key+"'><option value='' selected>Choose security clearance</option>@foreach($security_clearances as $id=>$security_clearance)<option value='{{$id}}'>{{$security_clearance}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='valid_until_"+key+"'><input type='text' class='form-control datepicker' name='valid_until_"+key+"' value="+value.valid_until+" placeholder='Valid Until(Y-m-d)'><small class='help-block'></small></div></td></tr>";
                            @foreach($security_clearances as $id=>$security_clearance)
                                select_box_values.push({{$id}});
                            @endforeach
                            if(select_box_values.includes(value.security_clearance_lookup_id)){
                                $(".user-security-clearance-table tbody").append(user_security_clearance_edit_row);
                                $('#myModal select[name="security_clearance_'+key+'"] option[value="'+value.security_clearance_lookup_id+'"]').prop('selected',true);
                                $("#valid_until_"+key+">input").datepicker({
                                    format: "yyyy-mm-dd", maxDate: "+900y"
                                });
                                $(".datepicker").mask("9999-99-99");
                            }
                        });

                        if(data.security_clearance_user.length >= 1){
                            $('#remove-security-clearance').show();
                        }

                        $.each(data.user_certificate, function (key, value) {
                            var select_box_value = [];
                            var user_certificate_edit_row =
                             "<tr><td><div class='form-group' id='certificate_"+key+"'><input type='text' name='certificate-row-no[]' class='row-no' value="+key+"><select class='form-control' name='certificate_"+key+"'><option value='' selected>Choose Certificates</option>@foreach($certificates as $id=>$certificate)<option value='{{$id}}'>{{$certificate}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='expiry_"+key+"'><input type='text' class='form-control datepicker' name='expiry_"+key+"' value="+value.expires_on+" placeholder='Valid Until(Y-m-d)'><small class='help-block'></small></div></td></tr>";
                            @foreach($certificates as $id=>$certificate)
                                select_box_value.push({{$id}});
                            @endforeach
                            if(select_box_value.includes(value.certificate_id)){
                                $(".user-certificate-table tbody").append(user_certificate_edit_row);
                                $('#myModal select[name="certificate_'+key+'"] option[value="'+value.certificate_id+'"]').prop('selected',true);
                                $("#expiry_"+key+">input").datepicker({
                                    format: "yyyy-mm-dd", maxDate: "+900y"
                                });
                                $(".datepicker").mask("9999-99-99");
                            }
                            else
                            {
                               // $(".user-certificate-table tbody").append(user_certificate_edit_row);
                               // $('#myModal select[name="certificate_'+key+'"]').append('<option value="'+value.certificate_id+'">'+value.trashed_certificate_master.certificate_name+'</option>');
                               //  $('#myModal select[name="certificate_'+key+'"] option[value="'+value.certificate_id+'"]').prop('selected',true);

                            }
                        });

                        if(data.user_certificate.length >= 1){
                            $('#remove-certificate').show();
                        }
                        $.each(data.user_skill_value, function (key, value) {
                            var select_box_value = [];
                            var user_skill_edit_row =
                             "<tr><td><div class='form-group' id='skill_"+key+"'><input type='hidden' name='skill-row-no[]' class='skill-row-no' value="+key+"><select class='form-control skills' name='skill_"+key+"' data-id='"+key+"'><option value='' selected>Choose User Skill</option>@foreach($user_skills as $id=>$each_user_skill)<option value='{{$id}}'>{{$each_user_skill}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group values' id='skillvalue_"+key+"'><select class='form-control skillValue_"+key+"' name='skillvalue_"+key+"'><option value='' selected>Choose value</option></select><small class='help-block'></small></div></td></tr>";
                            @foreach($user_skills as $id=>$each_user_skill)
                                select_box_value.push({{$id}});
                            @endforeach
                            console.log(select_box_value)
                            if(select_box_value.includes(value.option_allocation.user_skill_id)){
                                $(".user-skill-table tbody").append(user_skill_edit_row);
                                $('#myModal select[name="skill_'+key+'"] option[value="'+value.option_allocation.user_skill_id+'"]').prop('selected',true);
                                changeDropdown(value.option_allocation.user_skill_id,key,value.user_option_value_id);
                               
                               
                            }
                            else
                            {
                               // $(".user-skill-table tbody").append(user_skill_edit_row);
                               // $('#myModal select[name="skill_'+key+'"]').append('<option value="'+value.skill_id+'">'+value.trashed_certificate_master.certificate_name+'</option>');
                               //  $('#myModal select[name="certificate_'+key+'"] option[value="'+value.certificate_id+'"]').prop('selected',true);

                            }
                        });

                        if(data.user_skill_value.length >= 1){
                            $('#remove-skill').show();
                        }

                        //expense start
                        if (data.expense_allowed_for_user === null) {
                            $('#myModal input[name="max_allowable_expense"]').val(data.expense_allowed_for_user);
                            $('#myModal select[name="reporting_to_id"] option[value="'+data.expense_allowed_for_user+'"]').prop('selected',true);
                        } else {
                            $('#myModal input[name="max_allowable_expense"]').val(data.expense_allowed_for_user.max_allowable_expense);
                            $('#myModal select[name="reporting_to_id"] option[value="'+data.expense_allowed_for_user.reporting_to_id+'"]').prop('selected',true);
                        }
                       //expense end

                        if(null!=data.roles[0]){
                            $('#myModal select[name="role_id"]').val(data.roles[0].name).select2({ width: '100%',dropdownParent: $("#myModal") });
                        }

                        $('#myModal select[name="work_type_id"] option[value="'+data.employee.work_type_id+'"]').prop('selected',true)
                        $('#myModal input[name="username"]').val(data.username)

                        name = data.first_name+" "+((data.last_name == null) ? '' : data.last_name);
                        $('#myModal .modal-title').text("Edit User: " + name);

                        $('#myModal .edit-image').empty();
                        $('#myModal .edit-image').show();
                        $('#myModal .upload-image').hide();
                        if(data.employee.image != null && data.employee.image != "") {
                            $('#myModal .edit-image').html('<img style="border-radius: 50%;" height="100px" width="100px" src="{{asset("images/uploads/") }}/'+data.employee.image+'?'+new Date().getTime()+'">');
                        }else{
                            $('#myModal .edit-image').html('<img style="border-radius: 50%;" src="{{asset("images/uploads/")}}/{{config("globals.noAvatarImg") }}" height="100px" width="100px">');
                        }
                         $('#myModal input').attr('disabled',false);
                        $('#myModal select').attr('disabled',false);
                        $('#myModal .select2').attr('disabled',false);
                        $('#myModal input[type=checkbox]:not(.actives)').attr('disabled',false);
                        $(".form-check-input").prop("checked",true);
                        $('#myModal .datepicker').attr('disabled',false);
                        $('#myModal .gj-icon').show();
                        $("#myModal #add-security-clearance,#remove-security-clearance,#add-certificate,#remove-certificate").show();
                        if(data.active == 0){
                            $('#myModal #0,#1').attr('disabled',true)
                            $('#myModal input[name="active"]').prop('checked', false);
                            $('#myModal input').prop('readonly',true);
                            $('#myModal input[name="name"]').prop('readonly',true);
                            $('#myModal input[name="last_name"]').prop('readonly',true);
                            $('#myModal input[name="email"]').prop('readonly',true);
                            $('#myModal input[name="alternate_email"]').prop('readonly',true);
                            $('#myModal input[name="employee_no"]').prop('readonly',true);
                            $('#myModal input[name="phone"]').prop('readonly',true);
                            $('#myModal input[name="phone_ext"]').prop('readonly',true);
                            $('#myModal input[name="cell_no"]').prop('readonly',true);
                            $('#myModal select[name="role_id"]').prop('disabled',true);
                            $('#myModal select[name="work_type_id"]').prop('disabled',true);
                            $('#myModal input[name="username"]').prop('readonly',true);
                            $('#myModal input[name="password"]').prop('readonly',true);
                            $('#myModal input[name="employee_address"]').prop('readonly',true);
                            $('#myModal #bankcodes').prop('readonly',true);
                            /*$('#myModal text[name="employee_full_address"]').prop('readonly',true);*/
                            $('#myModal input[name="employee_city"]').prop('readonly',true);
                            $('#myModal input[name="employee_postal_code"]').prop('readonly',true);
                            $('#myModal input[name="employee_work_email"]').prop('readonly',true);
                            $('#myModal input[name="employee_doj"]').prop('readonly',true);
                            $('#myModal input[name="employee_dob"]').prop('readonly',true);
                            $('#myModal input[name="current_project_wage"]').prop('readonly',true);
                            $('#myModal select[name="position_id"]').prop('disabled',true);
                            $('#myModal select[name="employee_vet_status"]').prop('disabled',true);
                            $('#myModal input[name="years_of_security"]').prop('disabled',true);
                            $('#myModal input[name="being_canada_since"]').prop('disabled',true);
                            $('#myModal input[name="wage_expectations_from"]').prop('disabled',true);
                            $('#myModal input[name="wage_expectations_to"]').prop('disabled',true);
                            $('#myModal input[name="termination_date"]').prop('readonly',true);
                            $('#myModal select').attr('disabled',true);
                            $('#myModal .select2').attr('disabled',true);
                            $('#myModal .datepicker').attr('disabled',true);
                            $('#myModal input[type=checkbox]:not(.actives)').attr('disabled','disabled');
                            $('#myModal .gj-icon').hide();
                            $("#myModal #add-security-clearance,#remove-security-clearance,#add-certificate,#remove-certificate").hide();

                        }
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

        /*User - Delete - Start*/
        $('#users-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url ='{{ route('user.destroy',':id') }}';
            var url = base_url.replace(':id',id);
            var message = 'User has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /*User - Delete - End*/


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
             "<tr><td><div class='form-group' id='security_clearance_"+$next_row_no+"'><input type='text' name='row-no[]' class='row-no'><select class='form-control' name='security_clearance_"+$next_row_no+"'><option value='' selected>Choose security clearance</option>@foreach($security_clearances as $id=>$security_clearance)<option value='{{$id}}'>{{$security_clearance}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='valid_until_"+$next_row_no+"'><input type='text' class='form-control datepicker' name='valid_until_"+$next_row_no+"' placeholder='Valid Until(Y-m-d)'><small class='help-block'></small></div></td></tr>";
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
            $last_row_no = $(".user-certificate-table").find('tr:last .row-no').val();
            if($last_row_no != undefined){
                $next_row_no = ($last_row_no*1)+1;
            }else{
                $next_row_no = 0;
            }
             var user_certificate_new_row =
             "<tr><td><div class='form-group' id='certificate_"+$next_row_no+"'><input type='text' name='certificate-row-no[]' class='row-no'><select class='form-control' name='certificate_"+$next_row_no+"'><option value='' selected>Choose Certificates</option>@foreach($certificates as $id=>$certificate)<option value='{{$id}}'>{{$certificate}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='expiry_"+$next_row_no+"'><input type='text' class='form-control datepicker' name='expiry_"+$next_row_no+"' placeholder='Expiry Date(Y-m-d)'><small class='help-block'></small></div></td></tr>";
            $(".user-certificate-table tbody").append(user_certificate_new_row);
            $(".user-certificate-table").find('tr:last').find('.row-no').val($next_row_no);

            $("#expiry_"+$next_row_no+">input").datepicker({
                format: "yyyy-mm-dd", maxDate: "+900y"
            });

            $(".datepicker").mask("9999-99-99");

            if($last_row_no > 0 || $last_row_no == undefined){
                $('#remove-certificate').show();
            }
        });
             /* User Certificates - Add - End */


        /* Skill - Add - Start */

        $('#remove-skill').hide();
        $("#myModal").on("click", "#add-skill", function (e) {
            $last_row_no = $(".user-skill-table").find('tr:last .skill-row-no').val();
            if($last_row_no != undefined){
                $next_row_no = ($last_row_no*1)+1;
            }else{
                $next_row_no = 0;
            }

            var user_skill_new_row =
             "<tr><td><div class='form-group' id='skill_"+$next_row_no+"'><input type='hidden' name='skill-row-no[]' class='skill-row-no'><select class='form-control skills' name='skill_"+$next_row_no+"' data-id='"+$next_row_no+"'><option value='' selected>Choose User Skill</option>@foreach($user_skills as $id=>$each_user_skill)<option value='{{$id}}'>{{$each_user_skill}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group values' id='skillvalue_"+$next_row_no+"'><select class='form-control skillValue_"+$next_row_no+"' name='skillvalue_"+$next_row_no+"'><option value='' selected>Choose value</option></select><small class='help-block'></small></div></td></tr>";
            $(".user-skill-table tbody").append(user_skill_new_row);
            $(".user-skill-table").find('tr:last').find('.skill-row-no').val($next_row_no);


            if($last_row_no > 0 || $last_row_no == undefined){
                $('#remove-skill').show();
            }
        });
          $("#myModal").on("change", ".skills", function (e) {
          var id =this.value;
          var pos= $(this).attr('data-id');
          changeDropdown(id,pos);

        });

        /* Skill - Add - End */

        $("#myModal").on("change", "#bankname", function (e) {
             var bankcode = <?php echo json_encode($bank_code); ?>;
             $('#bankcodes').val(bankcode[$(this).val()])
        });


         $("#myModal input[name='active']").on("change", function (e) {
            if($('input[name="active"]:checked').length==1){
              $('#myModal input').attr('readonly',false);
              $('#myModal select').attr('readonly',false);
              $('#myModal input').prop('disabled',false);
              $('#myModal input[type=checkbox]:not(.actives)').attr('disabled',false);
              $('#myModal select').attr('disabled',false);
              $('#myModal .select2').attr('disabled',false);
              $('#myModal .datepicker').attr('disabled',false);
              $('#myModal .gj-icon').show();
              $("#myModal #add-security-clearance,#remove-security-clearance,#add-certificate,#remove-certificate").show();
              $('#myModal input[name="termination_date"]').val('');
              $('#myModal select[name="payment_method_id"]').val('');
            }
           else{
             $('#myModal input').prop('readonly',true);
             $('#myModal select').attr('readonly',true);
             $('#myModal input[type=checkbox]:not(.actives)').attr('disabled','disabled');
             $('#myModal select').attr('disabled',true);
             $('#myModal .select2').attr('disabled',true);
             $('#myModal .datepicker').attr('disabled',true);
             $('#myModal .gj-icon').hide();
             $("#myModal #add-security-clearance,#remove-security-clearance,#add-certificate,#remove-certificate").hide();
               var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            today = yyyy + '-' + mm + '-' + dd;
            $('#myModal input[name="termination_date"]').val(today);
            $('#myModal select[name="payment_method_id"]').val(2);
           }
             $('#myModal #bankcodes').prop('readonly',true);
             $('#myModal input[name="ura_balance"]').attr('readonly', 'true');
             $('#myModal input[name="hours_worked_to_date"]').attr('readonly', 'true');
             $('#myModal input[name="ura_earned"]').attr('readonly', 'true');
             $('#myModal input[name="rate_applied"]').attr('readonly', 'true');


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

        /*  User Certificates - Remove - Start */
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
        /*  User Certificates - Remove - End */

         /*  User Skills - Remove - Start */
        $("#myModal").on("click", "#remove-skill", function (e) {

           $last_row_no = $(".user-skill-table").find('tr:last .skill-row-no').val();
            if($last_row_no > -1){
                $(".user-skill-table").find('tr:last').remove();
                if($last_row_no == 0){
                    $('#remove-skill').hide();
                }
            }else{
                $('#remove-skill').hide();
            }
        });
        /*  User Skills - Remove - End */


    });

    

    /* Display single row on adding security clearance - Start */
    $('.add-new').click(function(){
        $('#myModal .upload-image').hide();
        $('#myModal .edit-image').html('<img style="border-radius: 50%;" src="{{asset("images/uploads/")}}/{{config("globals.noAvatarImg") }}" height="100px" width="100px">');
        $('[href="#certificateTab"]').closest('li').show();
        $('[href="#securityClearanceTab"]').closest('li').show();
        $('.disable_client').show();
        $(".veteran_status_qstn").addClass('hide-this-block');
        $('[href="#candidateTransitionTab"]').closest('li').hide();
        $('#remove-security-clearance').show();
        $('#remove-certificate').show();
        $('#user-security-clearance tbody').find('tr').remove();
        $('#user-certificate tbody').find('tr').remove
        $('select#detach').val(null).trigger('change');
        $('select#role_id').val(null).trigger('change');
        $('#myModal #bankcodes').prop('readonly',true);
        $('#myModal input').attr('disabled',false);
        $('#myModal select').attr('disabled',false);
        $('#myModal select').attr('readonly',false);
        $('#myModal .select2').attr('disabled',false);
        $('#myModal input[name="ura_balance"]').attr('readonly', 'true');
        $('#myModal input[name="hours_worked_to_date"]').attr('readonly', 'true');
        $('#myModal input[name="ura_earned"]').attr('readonly', 'true');
        $('#myModal input[name="rate_applied"]').attr('readonly', 'true');
        $('#myModal input[type=checkbox]:not(.actives)').attr('disabled',false);
        $(".form-check-input").prop("checked",true);
        $('#myModal .datepicker').attr('disabled',false);
        $('#myModal .gj-icon').show();
        $("#myModal #add-security-clearance,#remove-security-clearance,#add-certificate,#remove-certificate").show();
        $user_security_clearance_first_row = "<tr><td><div class='form-group' id='security_clearance_0'><input type='text' name='row-no[]' class='row-no' value='0'><select class='form-control' name='security_clearance_0'><option value='' selected>Choose security clearance</option>@foreach($security_clearances as $id=>$security_clearance)<option value='{{$id}}'>{{$security_clearance}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='valid_until_0'><input type='text' class='form-control datepicker' name='valid_until_0' placeholder='Valid Until(Y-m-d)'><small class='help-block'></small></div></td></tr>";
        $('#user-security-clearance tbody').append( $user_security_clearance_first_row);

         $user_certificate_first_row =
             "<tr><td><div class='form-group' id='certificate_0'><input type='text' name='certificate-row-no[]' class='row-no' value='0'><select class='form-control' name='certificate_0'><option value='' selected>Choose Certificates</option>@foreach($certificates as $id=>$certificate)<option value='{{$id}}'>{{$certificate}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='expiry_0'><input type='text' class='form-control datepicker' name='expiry_0' placeholder='Expiry Date(Y-m-d)'><small class='help-block'></small></div></td></tr>";
            $("#user-certificate tbody").append($user_certificate_first_row);
        $("#expiry_0>input").datepicker({
                format: "yyyy-mm-dd", maxDate: "+900y"
        });
        $("#valid_until_0>input").datepicker({
                format: "yyyy-mm-dd", maxDate: "+900y"
        });
        $(".datepicker").mask("9999-99-99");
        $('#myModal .edit-image').show();
    });
    /* Display single row on adding security clearance - End */

    //To reset the hidden value in the form
    $('#myModal').on('hidden.bs.modal', function () {
        $('#myModal').find('#active').hide();
        $('#myModal #password').find('span').show();
        $('#myModal input[name="active"]').prop('checked', true);
        $('#myModal input').prop('readonly',false);
        $('#myModal input[name="id"]').prop('readonly',false);
        $('#myModal input[name="name"]').prop('readonly',false);
        $('#myModal input[name="last_name"]').prop('readonly',false);
        $('#myModal input[name="email"]').prop('readonly',false);
        $('#myModal input[name="alternate_email"]').prop('readonly',false);
        $('#myModal input[name="employee_no"]').prop('readonly',false);
        $('#myModal input[name="phone"]').prop('readonly',false);
        $('#myModal input[name="cell_no"]').prop('readonly',false);
        $('#myModal select[name="role_id"]').prop('disabled',false);
        $('#myModal select[name="work_type_id"]').prop('disabled',false);
        $('#myModal input[name="username"]').prop('readonly',false);
        $('#myModal input[name="password"]').prop('readonly',false);
        $('#myModal input[name="employee_address"]').prop('readonly',false);
        /*$('#myModal text[name="employee_full_address"]').prop('readonly',false);*/
        $('#myModal input[name="employee_city"]').prop('readonly',false);
        $('#myModal input[name="employee_postal_code"]').prop('readonly',false);
        $('#myModal input[name="employee_work_email"]').prop('readonly',false);
        $('#myModal input[name="employee_doj"]').prop('readonly',false);
        $('#myModal input[name="employee_dob"]').prop('readonly',false);
        $('#myModal input[name="current_project_wage"]').prop('readonly',false);
        $('#myModal select[name="position_id"]').prop('disabled',false);
        $('#myModal select[name="employee_vet_status"]').prop('disabled',false);
    });


    function addnew() {
        $(".form-check-input").prop("checked",true);
        $("#myModal").modal();
        $('#user-form').trigger('reset');
        $('#myModal .modal-title').text("Add New User");
        $('#user-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        $('select#detach').val(null).trigger('change');

    }

    function changeDropdown(id,pos,user_option_value_id=null)
    {
          var url = '{{ route("user-skill-option-value.single",":id") }}';
          var url = url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                if (data) {
                    console.log(data)
                       $('.skillValue_'+pos).find('option').remove().end().append($("<option></option>").attr("value",'').text('Choose value')); 
                    $.each(data.result, function(key, val) {  
                        $.each(val.skill_option.skill_option_values, function(key, value) {  
         $('.skillValue_'+pos)
         .append($("<option></option>")
                    .attr("value", value.id)
                    .text(value.name)); 
         });


                         });
        if(user_option_value_id)
        {
         $('#myModal select[name="skillvalue_'+pos+'"] option[value="'+user_option_value_id+'"]').prop('selected',true);
         }
                                       
             } else {
                alert(data);
            }
        },
        error: function (xhr, textStatus, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        },
        contentType: false,
        processData: false,
    });
    }


     $("#myModal").on("input", "#email", function (e) {
          $('#myModal input[name="username"]').val($('#myModal input[name="email"]').val());
         $('#myModal input[name="epaystub_email"]').val($('#myModal input[name="email"]').val());
     });

     var resize = $('.upload-image').croppie({
        enableExif: true,
        enableOrientation: true,
        viewport: { // Default { width: 100, height: 100, type: 'square' }
            width: 110,
            height: 110,
            type: 'circle' //square
        },
        boundary: {
            width: 120,
            height: 120
        }
    });

    $('#image_input').on('change', function () {
        $('#myModal .edit-image').hide();
        $('#myModal .upload-image').show();

        var reader = new FileReader();
        reader.onload = function (e) {
            result = e.target.result;
            croppedImage=result;
            arrTarget = result.split(';');
            imageType = arrTarget[0];
            if (imageType == 'data:image/jpg' || imageType == 'data:image/jpeg' || imageType == 'data:image/png') {
                resize.croppie('bind',{
                    url: e.target.result
                }).then(function(){
                    console.log('jQuery bind complete');
                });
            } else {
                $('#myModal .edit-image').show();
                $('#myModal .upload-image').hide();
                $('#image_input').val('');
                swal("Error", "Accept only jpg or png images", "error");
            }
        }
$('.upload-image').on('click', function (ev) {
    var imageValue = $('#image_input').val();
    resize.croppie('result', {
                type: 'canvas',
                size: {width:512, height:512},
                quality: 1,
                circle: false
            }).then(function (img) {
                if(imageValue != "" && imageValue != null) {
                   croppedImage=img;


                }
            });
});
        if(this.files[0]) {
                reader.readAsDataURL(this.files[0]);
        }else{
            $('#image_input').val('');
        }
    });

    $('.upload-image').on('dblclick', function() {
        $('#image_input').trigger('click');
    });

    $('.edit-image').on('click', function() {
        $('#image_input').trigger('click');
    });

    $(document).on("click",".vision-export",function(e){
        e.preventDefault();
        window.open(
            '{{route("user.export.vision")}}',
            '_blank' // <- This is what makes it open in a new window.
        );

        // $.ajax({
        //     type: "post",
        //     url: '{{route("user.export.vision")}}',
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     },
        //     success: function (response) {

        //     }
        // });
    })

 

</script>

@stop
