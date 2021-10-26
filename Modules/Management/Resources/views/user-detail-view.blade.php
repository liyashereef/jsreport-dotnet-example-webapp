@extends('layouts.app')
@section('content')

<div class="table_title">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head document-screen-head">
    {{$userDetails['first_name']}} {{$userDetails['last_name']}} ({{$userDetails->employee->employee_no }})</div>
<div class="data-list-line row">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 data-list-label document-list-label margin-top-1
margin-bottom-10" style="margin-top: -8px;"></div>
</div>
</div>
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
</style>
<style>
    .security-tab{
        margin-top:-30px;
        margin-left:-0.5em;
    }
    .certificate-tab{
        margin-top:-30px;
        margin-left:0.5em;
    }
.break {
  width: 150px;
  word-wrap: break-word;
}
#userProfileForm{
    margin-left:-0.5em;
}
.document-screen-head{
    width:100%;
}
.certificate_btn{
    margin-left:0.3em;
}
.data-list-label{
    width:100%;
}
.inc_button{
    margin-left:39%;
}
.btn-class{
    margin-left:0.3em;
}
.align_button{
    margin-left:20px;
}
#myModal {
  align: center;

}
#exp-id{
    margin-right:50px;
}
.sub_title_name{
    color: #f26222;
}
.tab-content{
    margin-top:15px;
}
.data-list-label{
    height:40px;
}
#security_clearance_form{
    margin-left:-1em;
}
.nav-row{
    padding-left:10px;
}
#updated_time{
    margin-top: -20px;
    margin-left: 0px;
}
.blue{
    font-size: 18px;
    color:#003A63 !important;
}
.nav-tabs .nav-item {
    margin-left: -9px;
    margin-right:-10px;
}
.expense{
    margin-left: -45px;

}
.profile{
    margin-left: -25px;

}
.sec-status{
    margin-left: -45px;
}
.expiry{
    margin-left: -75px;
}
.certificate{
    margin-left: 25px;
    }
.management_expense_tab{
    margin-left:-0.5em;
}
.btn-sm{
    margin-top: 5px;
}
.user-screen-head {
    background: #0e3b5e;
    color: #ffffff;
    margin: 8px 0px;
    padding: 10px 5px;
    margin-left: 0px;
}
.user-screen {
    background: #0e3b5e;
    color: #ffffff;
    margin: 8px 0px;
    padding: 10px 5px;
}
#user-form{
    margin-left:-1em;
}
html, body {
max-width: 100%;
overflow-x: hidden;
}
.security_clr_btn{
    margin-left:0.3em;
}
#ponumber{
    margin-left:-150px;
}
.container-fluid{
    margin-left:-50px;
}
#ponumber{
    margin-left:0px;
}
.btn-align{
    margin-right:10px;
}
.expense-tab{
    margin-left:50px;
}
.form_fileld{
        color:#000000 !important;
}
.align-tab{
    margin-top:-30px;
}
.container-fluid{
    margin-left:-7px;
}

.nav-link{
    margin-left:26px;
}
.editbutton1{
                float: right;
                cursor: pointer;
        }
.editbutton2, .editbutton6,.editbutton7{
    float: right;
    cursor: pointer;
        }
.editbutton3, .editbutton5{
    float: right;
    cursor: pointer;
        }
.breadcrumb-arrow li {
    width: 16.8%;
}
.profile_btn{
    margin-bottom:5%;
}
.profile_tab_new{
    margin-top:-80px;
}
.userexpiry {

        height: max-content;
        width: 100px;
        margin-bottom: 0.9em;
    }
.profile-tab{
    margin-top:-10px;
}
.expense_class{
    margin-left:2.7em;
}
.candidate_tab_class{
    margin-left:-1em;
}
.user_management_tab{
    margin-left:-0.5em;
}
</style>

<!-- User Detail page- Start -->


<div class="row">
<ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100 col-sm-12" role="tablist">
            <li class="nav-item complete ">
                <a class="nav-link active profile_tab" data-toggle="tab" href="#user"><span>&nbsp;User</span></a>
            </li>
            <li class="nav-item complete ">
                <a class="nav-link " data-toggle="tab" href="#profile" id="userProfTab"><span>&nbsp;Profile</span></a>
            </li>
            <li class="nav-item complete ">
                <a class="nav-link " data-toggle="tab" href="#expense" id="expense_tab_id"><span>&nbsp;Expense</span></a>
            </li>
            <li class="nav-item complete candidate-li">
                <a class="nav-link " data-toggle="tab" href="#candidate" id="candidate_tab_id"><span>&nbsp;Candidate
                </span></a>
            </li>

            <li class="nav-item complete ">
                <a class="nav-link " data-toggle="tab" href="#security_clearance" id="sec_clr_tab_id"><span>&nbsp;
                    Security Clearance </span></a>
            </li>
            <li class="nav-item complete ">
                <a class="nav-link " data-toggle="tab" href="#certificates" id="certificate_tab_id"><span>&nbsp;
                     Certificates </span></a>
            </li>
            <li class="nav-item complete ">
                <a class="nav-link " data-toggle="tab" href="#skills" id="skill_tab_id"><span>&nbsp;
                     Skills </span></a>
            </li>
        </ul></div>



        <div class="tab-content">
            <div id="user" class="tab-pane active candidate-screen">
            <div class="user-form">
            <div class="row">

                <div class="col-sm-12">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 align_header_tab candidate-screen-head">User
                @can(['user_tab_edit'])
                <span class="editbutton1 fas fa-edit" id="edit1">&nbsp;</span>
                @endcan
                     </div>
                     <div class="user_management_tab">
                    <div class="container-fluid form-group row poinfoinput inputclass1">
                        <div class="col-sm-2 sub_title_name">First Name<span class="mandatory">*</span>
                        </div>
                        <div class="col-sm-4">
                        {{Form::text('first_name',$userDetails['first_name'] ?:"",['class' => 'form-control has-error
                            form_fileld','placeholder' => 'First Name'])}}
                        <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="container-fluid form-group row poinfoinput inputclass1">
                        <div class="col-sm-2 sub_title_name">Last Name</div>
                        <div class="col-sm-4 form_color">
                        {{Form::text('last_name',$userDetails['last_name'] ?:"",
                            ['class' => 'form-control has-error form_fileld','placeholder' => 'Last Name'])}}
                        <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="container-fluid form-group row poinfoinput inputclass1">
                        <div class="col-sm-2 sub_title_name">Commgl Email<span class="mandatory">*</span></div>
                        <div class="col-sm-4">
                        {{Form::email('email',$userDetails['email']?:"",['class' => 'form-control form_fileld',
                            'placeholder' => 'Commgl Email','id'=>'email'])}}
                        <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="container-fluid form-group row poinfoinput inputclass1">
                        <div class="col-sm-2 sub_title_name">Alternate Email</div>
                        <div class="col-sm-4">
                        {{Form::email('alternate_email',$userDetails['alternate_email']?:"",
                            ['class' => 'form-control form_fileld','placeholder' => 'Alternate Email'])}}
                        <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="container-fluid form-group row poinfoinput inputclass1">
                        <div class="col-sm-2 sub_title_name control-label">Username<span class="mandatory">*</span></div>
                        <div class="col-sm-4">
                        {{Form::text('username',$userDetails['username']?:"",
                            ['class' => 'form-control has-error form_fileld','placeholder' => 'Username'])}}
                        <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="container-fluid form-group row poinfoinput inputclass1">
                        <div class="col-sm-2 sub_title_name">Password</div>
                        <div class="col-sm-4">
                        {{Form::password('password',['class' => 'form-control','placeholder' => '********'])}}
                        <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row poinfoinput inputclass1">
                        <div class="col-sm-4">
                        {{Form::hidden('pass',$userId,['class' => 'form-control user_id','placeholder' => '********'])}}
                        {{Form::hidden('pwd',$userDetails['password']?:"--",['class' => 'form-control pwd',
                            'placeholder' => '********'])}}
                        <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="container-fluid form-group row poinfoinput inputclass1">
                        <div class="col-sm-2 sub_title_name">Role<span class="mandatory">*</span></div>
                        <div class="col-sm-4">
                        {{Form::select('role_id',$roles,old('role_id',$viewRole),['class' => 'form-control role form_fileld',
                            'placeholder' => 'Choose the role'])}}
                        <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="col-sm-6 btn-class ">

                        <button type="button"  name="cancelbutton" id="usertab_cancelbutton" class="button btn submit
                        cancelbutton inputclass1">Cancel</button>
                        <button type="button" name="savepoinfo" id="savepoinfo_user" class="button btn submit inputclass1"
                         >Save</button>
                    </div>
                     </div>
                <div class="col-sm-12 user_tab">
                    <div class="row">
                        <div class="col-sm-12">
        <div class="form-group row">
        <div class="col-md-2 sub_title_name">First Name</div>
        <div class="col-md-6 break">{{ $userDetails['first_name']  ?:"--"}}</div>
        </div>
        <div class="form-group row">
        <div class="col-md-2 sub_title_name">Last Name</div>
        <div class="col-md-6 break">{{ $userDetails['last_name']  ?:"--"}}</div>
        </div>
        <div class="form-group row">
        <div class="col-md-2 sub_title_name">Commgl Email</div>
        <div class="col-md-6 break">{{$userDetails['email'] ?:"--"}}</div>
        </div>
        <div class="form-group row">
        <div class="col-md-2 sub_title_name">Alternate Email</div>
        <div class="col-md-6 break">{{ $userDetails['alternate_email'] ?:"--"}}
        </div>
        </div>
        <div class="form-group row">
        <div class="col-md-2 sub_title_name">Username</div>
        <div class="col-md-6 break">{{ $userDetails['username'] ?:"--"}}</div>
        </div>
        <div class="form-group row">
        <div class="col-md-2 sub_title_name">Role</div>
        <div class="col-md-6 break">{{ $viewRole ?:"--"}}</div>
        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>

        <div id="profile" class=" tab-pane fade">
        <div class="row">

        <div class="col-sm-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 align_header_tab candidate-screen-head">Profile
        @can(['user_profile_edit'])
        <span class="editbutton2 fas fa-edit" id="edit2">&nbsp;</span>
        @endcan
        </div>
        {{ Form::open(array('url'=>'#','id'=>'userProfileForm','class'=>'form-horizontal', 'method'=> 'POST', 'novalidate'=>TRUE)) }}
        <div class="disable_client">
            <div class="form-group container-fluid row poinfoinput inputclass2">
                <div class="col-sm-2 sub_title_name">Employee Number<span class="mandatory">*</span></div>
                <div class="col-sm-4">
                {{Form::text('employee_no',$userDetails->employee->employee_no ?:"",['class' => 'form-control',
                'placeholder' => 'Employee No','maxlength' => '6'])}}
                <small class="help-block"></small>
                </div>
            </div>
        </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Phone Number<span class="mandatory">*</span>
        </div>
        <div class="col-sm-4">
        {{Form::text('phone',$userDetails->employee->phone ?:"",['class' => 'form-control phone',
            'placeholder' => 'Phone [ format (XXX)XXX-XXXX ]'])}}
         <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Ext.</div>
        <div class="col-sm-4">
        {{Form::text('phone_ext',$userDetails->employee->phone_ext ?:"",['class' => 'form-control',
            'placeholder' => 'Ext.'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Cell</div>
        <div class="col-sm-4">
        {{Form::text('cell_no',$userDetails->employee->cell_no ?:"",['class' => 'form-control phone',
            'placeholder' => 'Cell No [ format (XXX)XXX-XXXX ]','pattern' => '[\(]\d{3}[\)]\d{3}[\-]\d{4}'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="disable_client">
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Work Type <span class="mandatory">*</span>
        </div>
        <div class="col-sm-4">
        {{Form::select('work_type_id',$work_types,null,['class' => 'form-control',
            'placeholder' => 'Choose the work type'])}}
        {!! $errors->first('work_type_id', '<small class="help-block">:message</small>') !!}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Address</div>
        <div class="col-sm-4">
        {{Form::text('employee_address',$userDetails->employee->employee_address ?:"",['class' => 'form-control',
            'placeholder' => 'Address'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">City</div>
        <div class="col-sm-4">
        {{Form::text('employee_city',$userDetails->employee->employee_city ?:"",['class' => 'form-control',
            'placeholder' => 'City'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Postal Code  <span class="mandatory">*</span>
       </div>
        <div class="col-sm-4">
        {{Form::text('employee_postal_code',$userDetails->employee->employee_postal_code ?:"",
            ['class' => 'form-control postal-code','placeholder' => 'Postal Code','required'=>true])}}
         <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Work Email</div>
        <div class="col-sm-4">
        {{Form::text('employee_work_email',$userDetails->employee->employee_work_email ?:"",['class' => 'form-control',
            'placeholder' => 'Work Email'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">DOJ</div>
        <div class="col-sm-4">
        {{Form::text('employee_doj',$userDetails->employee->employee_doj ?:"",['class' => 'form-control datepicker',
            'placeholder' => 'Date of Joining (Y-m-d)'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">DOB</div>
        <div class="col-sm-4">
        {{Form::text('employee_dob',$userDetails->employee->employee_dob ?:"",['class' => 'form-control datepicker' ,
            'placeholder' => 'Date of Birth (Y-m-d)'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Current Project Wage</div>
        <div class="col-sm-4">
        {{Form::text('current_project_wage',$userDetails->employee->current_project_wage ?:"",
            ['class' => 'form-control','placeholder' => 'Current Project Wage'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Position
        </div>
        <div class="col-sm-4">
        {{Form::select('position_id',$positions,null,['class' => 'form-control position','placeholder' => 'Choose the Position'])}}
         {!! $errors->first('position_id', '<small class="help-block">:message</small>') !!}
        </div>
    </div>

    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Years of Security</div>
        <div class="col-sm-4">
        {{Form::number('years_of_security',$userDetails->employee->years_of_security ?:"",
            ['class' => 'form-control','placeholder' => 'Years of Security'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Arrival In Canada</div>
        <div class="col-sm-4">
        {{Form::text('being_canada_since',$userDetails->employee->being_canada_since ?:"",
            ['id' => 'being_canada_since','class' => 'form-control datepicker' ,'placeholder' => 'Arrival In Canada'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Wage Expectation From </div>
        <div class="col-sm-4">
        {{Form::text('wage_expectations_from',$userDetails->employee->wage_expectations_from ?:"",
            ['class' => 'form-control','placeholder' => 'Wage Expectation From'])}}
        <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Wage Expectation To</div>
        <div class="col-sm-4">
        {{Form::text('wage_expectations_to',$userDetails->employee->wage_expectations_to ?:"",
            ['class' => 'form-control','placeholder' => 'Wage Expectation To'])}}
        <small class="help-block"></small>
        </div>
    </div>

    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Veteran Status<span class="mandatory">*</span>
        </div>
        <div class="col-sm-4">
        {{Form::select('employee_vet_status',[1=>"Yes",0=>"No"],null,['class' => 'form-control','id'=>'veteran_status',
            'placeholder' => 'Choose the Veteran Status'])}}
        {!! $errors->first('employee_vet_status', '<small class="help-block">:message</small>') !!}
        </div>
    </div>
    <div class=" veteran_status_qstn hide-this-block">
        <div class="form-group row container-fluid inputclass2" id="vet_service_number">
            <label for="vet_service_number" class="col-sm-2 control-label sub_title_name">@lang('Service Number?')<span class="mandatory"> *</span></label>
            <div class="col-sm-4">
            {{Form::text('vet_service_number','',['class' => 'form-control','placeholder' => 'Service Number'])}}
            <small class="help-block"></small>
            </div>
        </div>
        <div class="form-group row container-fluid inputclass2" id="vet_enrollment_date">
            <label for="vet_enrollment_date" class="col-sm-2 control-label sub_title_name">@lang('Enrollment Date?')<span class="mandatory"> *</span></label>
            <div class="col-sm-4">
            {{Form::text('vet_enrollment_date','',['class' => 'form-control datepicker','placeholder' => 'Enrollment Date'])}}
            <small class="help-block"></small>
            </div>
        </div>
        <div class="form-group row container-fluid inputclass2" id="vet_release_date">
            <label for="vet_release_date" class="col-sm-2 control-label sub_title_name">@lang('Release Date?')<span class="mandatory"> *</span></label>
            <div class="col-sm-4">
            {{Form::text('vet_release_date','',['class' => 'form-control datepicker','placeholder' => 'Release Date'])}}
            <small class="help-block"></small>
            </div>
        </div>
    </div>
    </div>
    <div class="form-group container-fluid row poinfoinput inputclass2">
        <div class="col-sm-2 sub_title_name">Image</div>
        <div class="col-sm-4">
        {{Form::file('profile_image', ['id' => 'image_input', 'style' => 'display: none;'])}}
        <div class="upload-image upload-div" style="display: none;"></div>
        <div class="edit-image upload-div" style="display: none;"></div>
        </div>
    </div>
    <div class="col-sm-6 btn-class profile_btn">
        <button type="button"  name="cancelbutton " id="profiletab_cancelbutton" class="button btn submit cancelbutton cancelbtnProfile inputclass2">Cancel</button>

        {{ Form::submit('Save', array('class'=>' btn btn-primary profileTabButtonAlign inputclass2','id'=>'savepoinfo_profile'))}}
    {{ Form::close()}}
    </div>

    <div class="col-sm-12 profile-tab " >
        <div class="row">
        <div class="col-sm-12 profile profile_tab_new">

        <div class="form-group row container-fluid disable_client">
        <div class="col-md-2 sub_title_name">Employee No</div>
        <div class="col-md-6 break">{{ $userDetails->employee->employee_no ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name"> Phone </div>
        <div class="col-md-6 break">{{ $userDetails->employee->phone ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Ext.</div>
        <div class="col-md-6 break">{{ $userDetails->employee->phone_ext ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Cell</div>
        <div class="col-md-6 break">{{ $userDetails->employee->cell_no ?:"--"}}</div>
        </div>
        <div class="disable_client">
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Work Type</div>
        <div class="col-md-6 break">{{ $userDetails->employee->work_type_id == 1 ? "Permanent":"Contract" }}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name ">Address</div>
        <div class="col-md-6 break">{{ $userDetails->employee->employee_address ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">City</div>
        <div class="col-md-6 break">{{ $userDetails->employee->employee_city ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Postal Code </div>
        <div class="col-md-6 break">{{ $userDetails->employee->employee_postal_code ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Work Email</div>
        <div class="col-md-6 break">{{ $userDetails->employee->employee_work_email ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">DOJ</div>
        <div class="col-md-6 break">{{ $userDetails->employee->employee_doj?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">DOB</div>
        <div class="col-md-6 break">{{ $userDetails->employee->employee_dob ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Current Project Wage</div>
        <div class="col-md-6 break">{{ $userDetails->employee->current_project_wage ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Position</div>
        @foreach($posId as $data)
        <div class="col-md-6 break">{{$data->employee->employeePosition?$data->employee->employeePosition->position:"--"}}
        </div>
        @endforeach
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Years of Security </div>
        <div class="col-md-6 break">{{ $userDetails->employee->years_of_security ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Arrival In Canada </div>
        <div class="col-md-6 break">{{ $userDetails->employee->being_canada_since ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Wage Expectation From </div>
        <div class="col-md-6 break">{{ $userDetails->employee->wage_expectations_from  ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Wage Expectation To </div>
        <div class="col-md-6 break">{{ $userDetails->employee->wage_expectations_to  ?:"--"}}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Veteran Status </div>
        <div class="col-md-6 break">{{ $userDetails->employee->employee_vet_status ? "Yes" :"No" }}</div>
        </div>
        @if($userDetails->employee->employee_vet_status==1 )
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Service Number </div>
        <div class="col-md-6 break">{{ $userDetails->employee->vet_service_number ?:"--" }}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Enrollment Date </div>
        <div class="col-md-6 break">{{ $userDetails->employee->vet_enrollment_date ?:"--" }}</div>
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Release Date </div>
        <div class="col-md-6 break">{{ $userDetails->employee->vet_release_date ?:"--" }}</div>
        </div>
        @endif
        </div>
        <div class="form-group row container-fluid">
        <div class="col-md-2 sub_title_name">Image </div>
        <div class="edit-images" id='myFile' disabled></div>
    </div>

        </div></div></div></div>
    </div>
        </div>
        <div id="expense" class="tab-pane fade">
        <div class="row">
        <div class="col-sm-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Expense
        @can(['user_expense_edit'])
        <span class="editbutton3 fas fa-edit" id="edit3">&nbsp;</span>
        @endcan
             </div>
        <div class="management_expense_tab">
        <div class="form-group row poinfoinput container-fluid inputclass3">
        <div class="col-sm-2 sub_title_name">Reporting To (Approver)<span class="mandatory">*</span></div>
            <div class="col-sm-4">
                <select class="form-control reporting_to" name="reporting_to_id" placeholder="Choose the Approver">
                    @foreach($approversList as $key => $approvers)
                    <option @if($key==2)selected @endif value="{{$key}}">{{$approvers}}</option>
                    @endforeach
                </select>
                {!! $errors->first('reporting_to_id', '<small class="help-block">:message</small>') !!}
                <small class="help-block"></small>
            </div>
        </div>
        <div class="form-group row poinfoinput container-fluid inputclass3">
            <div class="col-sm-2 sub_title_name">Max Allowable Expense</div>
                <div class="col-sm-4">
                {{Form::text('max_allowable_expense',$viewExpense == "null" ? "" : "$viewExpense",
                    ['class' => 'form-control','placeholder' => 'Max: Allowable Expense','maxlength' => 7])}}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="col-sm-6 btn-class">
            <button type="button"  name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton
                inputclass3 ">Cancel</button>
                <button type="button" name="savepoinfo_expense" id="savepoinfo_expense" class="button btn submit
                inputclass3 ">Save</button>

            </div>
        </div>
        <div class="col-sm-12 expense-tab expense_class" >
            <div class="row">
                <div class="col-sm-12 expense">

                <div class="form-group row">
                <div class="col-md-2 sub_title_name break">Reporting To (Approver)</div>
                @foreach ($viewList as $user)
                <div class="col-md-6 break">{{ $user ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name break">Max Allowable Expense</div>
                <div class="col-md-6 break">{{ $viewExpense == "null" ? "---" : $viewExpense}}</div>
                </div>

            </div></div></div>
        </div>
    </div>
        </div>
        <div id="candidate" class="tab-pane fade">
        <div class="row">
        <div class="col-sm-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Candidate</div>
        <div class="col-sm-12 candidate-tab candidate_tab_class" >
        <div class="form-group row col-sm-12">
            <span class="sub_title_name col-sm-2">Candidate Converted by</span>
            <span class="col-sm-6">
           <div id="updated_by"></div>
            </span>
        </div>
        <div class="form-group row col-sm-12">
            <span class="sub_title_name col-sm-2">Employee No</span>
            <span class="col-sm-6">
           <div id="employee_num"></div>
            </span>
        </div>
        <div class="form-group row col-sm-12">
        <span class="sub_title_name col-sm-2">Date </span>
            <span class="col-sm-6">&nbsp;&nbsp;
            <div id="updated_time"></div>
            </span>
        </div></div></div>
        </div></div>


        <div id="security_clearance" class=" tab-pane fade">

       <div class="row">
        <div class="col-sm-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Security Clearance
        @can(['security_clearance_edit'])
        <span class="editbutton5 fas fa-edit" id="edit5">&nbsp;</span>
        @endcan
        </div>
    <div class="col-sm-12 container-fluid">
      {{ Form::open(array('url'=>'#','id'=>'security_clearance_form','class'=>'form-horizontal',
        'method'=> 'POST', 'novalidate'=>TRUE)) }}
      {{ Form::hidden('id', "") }}
        <div class="col-sm-6 table-responsive pop-in-table" id="user-security-clearance">
            <table class="table user-security-clearance-table">
                <thead>
                    <tr class='inputclass5'>
                    <th>Security Clearance</th>
                    <th>Valid Until</th>
                    </tr>
                </thead>
                <tbody class='inputclass5'>
                </tbody>
            </table>
        </div>
        <div class="form-group col-sm-6 inc_button">
            <label for="add-security-clearance" id="add-security-clearance" class="col-sm-1 btn btn-primary inputclass5"
             style="margin-right:1%;">+</label>
            <label for="remove-security-clearance" id="remove-security-clearance" class="col-sm-1 btn btn-primary
            inputclass5">-</label>
        </div>
        <div class="security_clr_btn">
        {{ Form::reset('Cancel', array('class'=>'btn btn-primary inputclass5 cancelbutton align_button',
            'id'=>'cancelbutton_sec','aria-hidden'=>true))}}
        {{ Form::submit('Save', array('class'=>'button btn btn-primary inputclass5',
            'id'=>'mdl_save_change save_security_clearance'))}}
        {{ Form::close() }}
        </div>

<div class="col-sm-10 security-tab">
    <div class="row">
        <div class="col-sm-3 ">
            <b>Security Clearance</b><br><br>
            @foreach ($securityDataLookup as $user)
            <span class="sub_title_name">{{ $user->securityClearanceLookups->security_clearance ?:"--" }}</span><br><br>
            @endforeach
        </div>
        <div class="col-sm-2 expiry">
            <b>Expiry Date</b><br><br>
            @foreach ($securityDataLookup as $user)
            <span>{{  $user->valid_until ?:"--" }}</span><br><br>
            @endforeach
        </div>
        <div class="col-sm-2 sec-status">
            <b>Status</b><br><br>
            @foreach ($securityDataLookup as $user)
            @if($user->valid_until < \Carbon\Carbon::today() )
            <button type="button" class="btn btn-danger userexpiry ">Expired</button><br>
            @else
            <button type="button" class="btn btn-success userexpiry">Active</button><br>
            @endif
            @endforeach
        </div>
    </div>
</div>
</div></div></div>
        </div>
        <div id="certificates" class=" tab-pane fade">
        <div class="row">
        <div class="col-sm-12 ">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Certificates
        @can(['user_certificates_edit'])
        <span class="editbutton6 fas fa-edit" id="edit6">&nbsp;</span>
        @endcan
        </div>
    <div class="col-sm-12 container-fluid">
    {{ Form::open(array('url'=>'#','id'=>'user-form','class'=>'form-horizontal', 'method'=> 'POST', 'novalidate'=>TRUE)) }}
    {{ Form::hidden('id', "") }}
    <div class="col-sm-6 table-responsive pop-in-table" id="user-certificate">
        <table class="table user-certificate-table">
            <thead>
                <tr class="inputclass6">
                    <th>Certificates</th>
                    <th>Valid Until</th>
                </tr>
            </thead>
            <tbody class="inputclass6">
            </tbody>
        </table>
    </div>
    <div class="form-group col-sm-6 inc_button">
        <label for="add-certificate" id="add-certificate" class="col-sm-1 btn btn-primary inputclass6"
         style="margin-right:1%;">+</label>
        <label for="remove-certificate" id="remove-certificate" class="col-sm-1 btn btn-primary inputclass6">-</label>
    </div>
    <div class="certificate_btn">
    {{ Form::reset('Cancel', array('class'=>'btn btn-primary inputclass6 cancelbutton align_button','id'=>'cancelbutton',
        'aria-hidden'=>true))}}
    {{ Form::submit('Save', array('class'=>'button btn btn-primary inputclass6','id'=>'mdl_save_change save_certificate'))}}
      {{ Form::close() }}
    </div>
    <div class="col-sm-10 skills-tab" >
        <div class="row">
            <div class="col-sm-2 ">
                <b>Certificate Name</b><br><br>
                @foreach ($user_certificate as $user)
                    @if($user->certificateMaster)
                        <span class="sub_title_name">{{ $user->certificateMaster->certificate_name ?:"--" }}</span><br><br>
                    @endif
                @endforeach
            </div>
            <div class="col-sm-2">
            <b>Expiry Date</b><br><br>
            @foreach ($user_certificate as $user)
            @if($user->certificateMaster)
            <span>{{$user->expires_on ?:"--" }}</span><br><br>
            @endif
            @endforeach
            </div>
            <div class="col-sm-2">
            <b>Status</b><br><br>
            @foreach ($user_certificate as $user)
            @if($user->certificateMaster)
            @if($user->expires_on < \Carbon\Carbon::today() )
            <button type="button" class="btn btn-danger userexpiry">Expired</button><br>
            @else
            <button type="button" class="btn btn-success userexpiry ">Active</button><br>
            @endif
            @endif
            @endforeach
            </div>
        </div>
    </div>
</div>
</div> </div> </div>

  <div id="skills" class=" tab-pane fade">
        <div class="row">
        <div class="col-sm-12 ">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Skills
        @can(['user_skill_edit'])
        <span class="editbutton7 fas fa-edit" id="edit7">&nbsp;</span>
        @endcan
        </div>
    <div class="col-sm-12 container-fluid">
    {{ Form::open(array('url'=>'#','id'=>'skill-form','class'=>'form-horizontal', 'method'=> 'POST', 'novalidate'=>TRUE)) }}
    {{ Form::hidden('id', "") }}
    <div class="col-sm-6 table-responsive pop-in-table" id="user-skills">
        <table class="table user-skill-table">
            <thead>
                <tr class="inputclass7">
                    <th>Skills</th>
                    <th>Skill Option</th>
                </tr>
            </thead>
            <tbody class="inputclass7">
            </tbody>
        </table>
    </div>
    <div class="form-group col-sm-6 inc_button">
        <label for="add-skills" id="add-skills" class="col-sm-1 btn btn-primary inputclass7"
         style="margin-right:1%;">+</label>
        <label for="remove-skills" id="remove-skills" class="col-sm-1 btn btn-primary inputclass7">-</label>
    </div>
    <div class="skill_btn">
    {{ Form::reset('Cancel', array('class'=>'btn btn-primary inputclass7 cancelbutton align_button','id'=>'cancelbutton',
        'aria-hidden'=>true))}}
    {{ Form::submit('Save', array('class'=>'button btn btn-primary inputclass7','id'=>'mdl_save_change save_skill'))}}
    </div>
    <div class="col-sm-10 skills-tab" >
        <div class="row">
            <div class="col-sm-2 ">
                <b>Skill Name</b><br><br>
                @foreach ($userDetails->user_skill_value as $userSkill)
                     @if($userSkill->optionAllocation->skill) 
                        <span class="sub_title_name">{{$userSkill->optionAllocation->skill?$userSkill->optionAllocation->skill->name:"--" }}</span><br><br>
                  
                    @endif
                @endforeach
            </div>
            <div class="col-sm-2">
            <b>Skill Value</b><br><br>
            @foreach ($userDetails->user_skill_value  as $userSkill)
          @if($userSkill->optionAllocation->skill) 
            <span>{{ $userSkill->userOptionValue?$userSkill->userOptionValue->name:"--" }}</span><br><br>
            @endif
            @endforeach
            </div>
           
        </div>
    </div>
</div>
</div> </div> </div>



        </div>





@stop

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>
<script>
var rolePermission={!! json_encode($rolePermission) !!};
var data={!!json_encode($userDetails)!!};
var id=$('.user_id').val();


if(rolePermission==true){
    $("#certificate_tab_id").show();
    $("#sec_clr_tab_id").show();
    $('.disable_client').show();
     $("#expense_tab_id").show();
   }
else{
    $("#certificate_tab_id").hide();
    $("#sec_clr_tab_id").hide();
    $('.disable_client').hide();
     $("#expense_tab_id").hide();
   }


$(".inputclass1").hide();
$(".inputclass2").hide();
$(".inputclass3").hide();
$(".inputclass4").hide();
$(".inputclass5").hide();
$(".inputclass6").hide();
$(".inputclass7").hide();
$(".expense-tab").show();

$(".cancelbutton").on("click",function(e){
    $(".inputclass1").hide();
    $(".inputclass2").hide();
    $(".inputclass3").hide();
    $(".inputclass4").hide();
    $(".inputclass5").hide();
    $(".inputclass6").hide();
    $(".inputclass7").hide();
    $(".user_tab").show();
    $(".profile-tab").show();
    $(".expense-tab").show();
    $(".candidate-tab").show();
    $(".security-tab").show();
    $(".certificate-tab").show();
    $(".skills-tab").show();
       })


     $("#email").on("input", function (e) {
          $('input[name="username"]').val($('input[name="email"]').val());
     });

      $(document).on('click', '.btn_remove', function(){
           var button_id = $(this).attr("id");
           $('#row'+button_id+'').remove();
      });




      $(".editbutton1").on("click", function () {
            $(".inputclass1").show();
            $(".user_tab").hide();

        });

        $(".editbutton2").on("click", function () {

        $(".inputclass2").show();
        $(".profile-tab").hide();
         $('.edit-image').empty();
            $('.edit-images').empty();
            $('.edit-image').show();
            $('.edit-images').show();
            $('.upload-image').hide();
            if(data.employee.image != null && data.employee.image != "") {
                $('.edit-image').html('<img style="border-radius: 50%;" height="100px" width="100px" src="{{asset("images/uploads/") }}/'+data.employee.image+'?'+new Date().getTime()+'">');
                $('.edit-images').html('<img style="border-radius: 50%;" height="100px" width="100px" src="{{asset("images/uploads/") }}/'+data.employee.image+'?'+new Date().getTime()+'">');
            }else{
                $('.edit-image').html('<img style="border-radius: 50%;" src="{{asset("images/uploads/")}}/{{config("globals.noAvatarImg") }}" height="100px" width="100px">');
                $('.edit-images').html('<img style="border-radius: 50%;" src="{{asset("images/uploads/")}}/{{config("globals.noAvatarImg") }}" height="100px" width="100px">');
            }
            if(data.employee.employee_vet_status==1)
                        {
                           $(".veteran_status_qstn").removeClass('hide-this-block');
                        }
            $('select[name="employee_vet_status"] option[value="'+data.employee.employee_vet_status+'"]').prop('selected',true);
            $('input[name="vet_service_number"]').val(data.employee.vet_service_number);
            $('input[name="vet_enrollment_date"]').val(data.employee.vet_enrollment_date);
            $('input[name="vet_release_date"]').val(data.employee.vet_release_date);
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

        });

        $(".editbutton3").on("click", function () {

        $(".inputclass3").show();
        $(".expense-tab").hide();

        });

        $('#image_input').on('change', function () {
        $('.edit-image').hide();
        $('.edit-images').hide();
        $('.upload-image').show();

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
            } else {
                $('.edit-image').show();
                $('.edit-images').show();
                $('.upload-image').hide();
                $('#image_input').val('');
                swal("Error", "Accept only jpg or png images", "error");
            }
        }

        if(this.files[0]) {
                reader.readAsDataURL(this.files[0]);
        }else{
            $('#image_input').val('');
        }
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

        $('.upload-image').on('dblclick', function() {
            $('#image_input').trigger('click');
        });

        $('.edit-images').on('click', function() {
            $("#myFile").prop('disabled', true);
        });

        $('.edit-image').on('click', function() {
            $('#image_input').trigger('click');
        });

        if(data.candidate_transition!=null)
        {
          $('.candidate-li').show();
        //   $('[href="#candidate"]').closest('li').show();
            const dateTime = data.candidate_transition.updated_at;
            const parts = dateTime.split(/[- :]/);
            const wanted = parts[0] + '-' + parts[1] + '-' + parts[2] ;
            const full_name=data.candidate_transition.updated_user.first_name+' '+data.candidate_transition.updated_user.last_name
            $('#updated_by').text(full_name);
            $('#employee_num').text(data.candidate_transition.updated_user.employee.employee_no);
            $('#updated_time').text(wanted);
        }else{
            $('.candidate-li').hide();
        }

        $(document).ready(function(){
            $('select[name="role_id"] option[value="'+data.roles[0].name+'"]').prop('selected',true);
            $(".role").select2();
            $('select[name="position_id"] option[value="'+data.employee.position_id+'"]').prop('selected',true);
            $(".position").select2();
            $('select[name="work_type_id"] option[value="'+data.employee.work_type_id+'"]').prop('selected',true);
            $('select[name="employee_vet_status"] option[value="'+data.employee.employee_vet_status+'"]').prop('selected',true);
            $('select[name="reporting_to_id"] option[value="'+data.expense_allowed_for_user.reporting_to_id+'"]').prop('selected',true);
            $(".reporting_to").select2();
            $('.edit-image').empty();
            $('.edit-images').empty();
            $('.edit-image').show();
            $('.edit-images').show();
            $('.upload-image').hide();
            if(data.employee.image != null && data.employee.image != "") {
                $('.edit-image').html('<img style="border-radius: 50%;" height="100px" width="100px" src="{{asset("images/uploads/") }}/'+data.employee.image+'?'+new Date().getTime()+'">');
                $('.edit-images').html('<img style="border-radius: 50%;" height="100px" width="100px" src="{{asset("images/uploads/") }}/'+data.employee.image+'?'+new Date().getTime()+'">');
            }else{
                $('.edit-image').html('<img style="border-radius: 50%;" src="{{asset("images/uploads/")}}/{{config("globals.noAvatarImg") }}" height="100px" width="100px">');
                $('.edit-images').html('<img style="border-radius: 50%;" src="{{asset("images/uploads/")}}/{{config("globals.noAvatarImg") }}" height="100px" width="100px">');
            }
        });

         $('#user-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var id=$('.user_id').val();
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

            var formData = new FormData($('#user-form')[0]);
            var url= "{{route('management.userCertificateStore',':id')}}";
                  var url = url.replace(':id', id);
                  $.ajax({
                        url: url,

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,

                success: function (data) {
                    if (data.success) {

                        swal({
                          title: "Updated",
                          text: "User certificates has been updated successfully",
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });

                    } else {
                        console.log(data);
                        swal("Oops", "Certificates updation was unsuccessful", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError,err) {
                    console.log(xhr.status);
                    console.log(xhr.responseJSON.errors);
                    var obj = xhr.responseJSON.errors;
                    console.log(obj[Object.keys(obj)[0]]);
                    swal("Oops",obj[Object.keys(obj)[0]], "error");
                },
                contentType: false,
                processData: false,
            });
        });
          $('#skill-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var id=$('.user_id').val();
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

            var formData = new FormData($('#skill-form')[0]);
            var url= "{{route('management.userSkillStore',':id')}}";
                  var url = url.replace(':id', id);
                  $.ajax({
                        url: url,

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,

                success: function (data) {
                    if (data.success) {

                        swal({
                          title: "Updated",
                          text: "User Skills has been updated successfully",
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });

                    } else {
                        console.log(data);
                        swal("Oops", "Skill updation was unsuccessful", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError,err) {
                    console.log(xhr.status);
                    console.log(xhr.responseJSON.errors);
                    var obj = xhr.responseJSON.errors;
                    console.log(obj[Object.keys(obj)[0]]);
                    swal("Oops",obj[Object.keys(obj)[0]], "error");
                },
                contentType: false,
                processData: false,
            });
        });


        $('#security_clearance_form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var id=$('.user_id').val();
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

            var formData = new FormData($('#security_clearance_form')[0]);
            var url= "{{route('management.securityClearanceTabStore',':id')}}";
                  var url = url.replace(':id', id);
                  $.ajax({
                        url: url,

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,

                success: function (data) {
                    if (data.success) {
                        swal({
                          title: "Updated",
                          text: "Security Cleaeance has been updated successfully",
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });

                    } else {
                        console.log(data);
                        swal("Oops", "User updation was unsuccessful", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError,err) {
                    console.log(xhr.status);
                    console.log(xhr.responseJSON.errors);
                    var obj = xhr.responseJSON.errors;
                    console.log(obj[Object.keys(obj)[0]]);
                    swal("Oops",obj[Object.keys(obj)[0]], "error");
                },
                contentType: false,
                processData: false,

            });
        });


        $('#savepoinfo_user').on('click', function(e) {
        e.preventDefault();
        var id=$('.user_id').val();
        var first_name =$('input[name="first_name"]').val();
        var  last_name =$('input[name="last_name"]').val();
        var  email =$('input[name="email"]').val();
        var  alternate_email =  $('input[name="alternate_email"]').val();
        var  username =  $('input[name="username"]').val();
        var  password =  $('input[name="password"]').val();
        var  role = $(".role").val();
        var userinputid = 1;
                  var url= "{{route('management.userTabStore',':id')}}";
                  var url = url.replace(':id', id);
                  $.ajax({
                        url: url,

                        type: 'POST',
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                        data: {
                        'first_name':first_name,
                        'last_name': last_name,
                        'email': email,
                        'alternate_email': alternate_email,
                        'username': username,
                        'password': password,
                         'id':id,
                         'role':role,

                    },
                success: function (data) {

                    if (data.success) {
                        swal({
                          title: "Updated",
                          text: "User details has been updated successfully",
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });

                    } else {
                           swal("Warning", "Something went wrong", "warning");
                           console.log(data);
                    }
                },
                error: function (xhr, textStatus, thrownError,err) {
                    console.log(xhr.status);
                    console.log(xhr.responseJSON.errors);
                    var obj = xhr.responseJSON.errors;
                    console.log(obj[Object.keys(obj)[0]]);
                    swal("Oops",obj[Object.keys(obj)[0]], "error");
                },
            });
    });

function securityClearanceEdit(sec_data){
    if (sec_data) {
                            $('.user-security-clearance-table tbody').empty();
                            $.each(sec_data.security_clearance_user, function (key, value) {
                            var select_box_values = [];
                            var user_security_clearance_edit_row =
                             "<tr><td><div class='form-group' id='security_clearance_"+key+"'><input type='hidden' name='row-no[]' class='row-no' value="+key+"><select class='form-control' name='security_clearance_"+key+"'><option value='' selected>Choose security clearance</option>@foreach($security_clearances as $id=>$security_clearance)<option value='{{$id}}'>{{$security_clearance}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='valid_until_"+key+"'><input type='text' class='form-control datepicker' name='valid_until_"+key+"' value="+value.valid_until+" placeholder='Valid Until(Y-m-d)'><small class='help-block'></small></div></td></tr>";
                            @foreach($security_clearances as $id=>$security_clearance)
                                select_box_values.push({{$id}});
                            @endforeach
                            if(select_box_values.includes(value.security_clearance_lookup_id)){
                                $(".user-security-clearance-table tbody").append(user_security_clearance_edit_row);
                                $('select[name="security_clearance_'+key+'"] option[value="'+value.security_clearance_lookup_id+'"]').prop('selected',true);
                                $("#valid_until_"+key+">input").datepicker({
                                    format: "yyyy-mm-dd", maxDate: "+900y"
                                });
                                $(".datepicker").mask("9999-99-99");
                            }
                        });


                        if(sec_data.security_clearance_user.length >= 1){
                            $('#remove-security-clearance').show();
                        }

                   } else {
                       console.log(sec_data);
                       swal("Oops", "Could not save data", "warning");
                   }
}


       $(".editbutton5").on("click", function () {
           $(".inputclass5").show();
           $(".security-tab").hide();
           id=$('.user_id').val();
           var sec_data={!!json_encode($userDetails)!!};
           securityClearanceEdit(sec_data);

       });


       $("#userProfTab").on("click", function () {
        $('.edit-image').empty();
            $('.edit-images').empty();
            $('.edit-image').show();
            $('.edit-images').show();
            $('.upload-image').hide();
            if(data.employee.image != null && data.employee.image != "") {
                $('.edit-image').html('<img style="border-radius: 50%;" height="100px" width="100px" src="{{asset("images/uploads/") }}/'+data.employee.image+'?'+new Date().getTime()+'">');
                $('.edit-images').html('<img style="border-radius: 50%;" height="100px" width="100px" src="{{asset("images/uploads/") }}/'+data.employee.image+'?'+new Date().getTime()+'">');
            }else{
                $('.edit-image').html('<img style="border-radius: 50%;" src="{{asset("images/uploads/")}}/{{config("globals.noAvatarImg") }}" height="100px" width="100px">');
                $('.edit-images').html('<img style="border-radius: 50%;" src="{{asset("images/uploads/")}}/{{config("globals.noAvatarImg") }}" height="100px" width="100px">');
            }

       });



       $('#remove-security-clearance').hide();
        $("#add-security-clearance").on("click", function (e) {
            $last_row_no = $(".user-security-clearance-table").find('tr:last .row-no').val();
            if($last_row_no != undefined){
                $next_row_no = ($last_row_no*1)+1;
            }else{
                $next_row_no = 0;
            }

            var user_security_clearance_new_row =
             "<tr><td><div class='form-group' id='security_clearance_"+$next_row_no+"'><input type='hidden' name='row-no[]' class='row-no'><select class='form-control' name='security_clearance_"+$next_row_no+"'><option value='' selected>Choose security clearance</option>@foreach($security_clearances as $id=>$security_clearance)<option value='{{$id}}'>{{$security_clearance}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group' id='valid_until_"+$next_row_no+"'><input type='text' class='form-control datepicker' name='valid_until_"+$next_row_no+"' placeholder='Valid Until(Y-m-d)' required><small class='help-block'></small></div></td></tr>";
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

        $("#remove-security-clearance").on("click", function (e) {
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

function userCertificateTable(data){
    if (data) {
                    $(".user-certificate-table tbody").empty();
                    $.each(data.user_certificate, function (key, value) {

                            var select_box_value = [];
                            var user_certificate_edit_row =
                             "<tr><td><div class='form-group inputclass6' id='certificate_"+key+"'><input type='hidden' name='certificate-row-no[]' class='row-no inputclass6' value="+key+"><select class='form-control inputclass6' name='certificate_"+key+"'><option value='' selected>Choose Certificates</option>@foreach($certificates as $id=>$certificate)<option value='{{$id}}'>{{$certificate}}</option>@endforeach</select><small class='help-block inputclass6'></small></div></td><td><div class='form-group inputclass6' id='expiry_"+key+"'><input type='text' class='form-control datepicker inputclass6' name='expiry_"+key+"' value="+value.expires_on+" placeholder='Valid Until(Y-m-d)'><small class='help-block'></small></div></td></tr>";
                            @foreach($certificates as $id=>$certificate)
                                select_box_value.push({{$id}});
                            @endforeach
                            if(select_box_value.includes(value.certificate_id)){
                                $(".user-certificate-table tbody").append(user_certificate_edit_row);
                                $('select[name="certificate_'+key+'"] option[value="'+value.certificate_id+'"]').prop('selected',true);
                                $("#expiry_"+key+">input").datepicker({
                                    format: "yyyy-mm-dd", maxDate: "+900y"
                                });
                                $(".datepicker").mask("9999-99-99");
                            }

                        });

                        if(data.user_certificate.length >= 1){
                            $('#remove-certificate').show();
                        }



                   }
}
       $(".editbutton6").on("click", function () {
           $(".inputclass6").show();
           $(".certificate-tab").hide();
           id=$('.user_id').val();
           var data={!!json_encode($userDetails)!!};
           userCertificateTable(data);

       });

       $('#remove-certificate').hide();
           $("#add-certificate").on("click", function (e) {
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
        });

        $("#remove-certificate").on("click", function (e) {
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
         $(".editbutton7").on("click", function () {
           $(".inputclass7").show();
           $(".skills-tab").hide();
           id=$('.user_id').val();
           var skill_data={!!json_encode($userDetails)!!};
           skillEdit(skill_data);

       });
         function skillEdit(data){
                if (data) {
                    $(".user-skill-table tbody").empty();
                      $.each(data.user_skill_value, function (key, value) {
                            var select_box_value = [];
                            var user_skill_edit_row =
                             "<tr><td><div class='form-group' id='skill_"+key+"'><input type='hidden' name='skill-row-no[]' class='skill-row-no' value="+key+"><select class='form-control skills' name='skill_"+key+"' data-id='"+key+"'><option value='' selected>Choose User Skill</option>@foreach($user_skills as $id=>$each_user_skill)<option value='{{$id}}'>{{$each_user_skill}}</option>@endforeach</select><small class='help-block'></small></div></td><td><div class='form-group values' id='skillvalue_"+key+"'><select class='form-control skillValue_"+key+"' name='skillvalue_"+key+"'><option value='' selected>Choose value</option></select><small class='help-block'></small></div></td></tr>";
                            @foreach($user_skills as $id=>$each_user_skill)
                                select_box_value.push({{$id}});
                            @endforeach
                            if(select_box_value.includes(value.option_allocation.user_skill_id)){
                                $(".user-skill-table tbody").append(user_skill_edit_row);
                                $('select[name="skill_'+key+'"] option[value="'+value.option_allocation.user_skill_id+'"]').prop('selected',true);
                                changeDropdown(value.option_allocation.user_skill_id,key,value.user_option_value_id);
                               
                               
                            }
                

                        if(data.user_skill_value.length >= 1){
                            $('#remove-skills').show();
                        }



                   });
}
}
           $('#remove-skills').hide();
           $("#add-skills").on("click", function (e) {
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
                $('#remove-skills').show();
            }
        });
            $(".user-skill-table").on("change", ".skills", function (e) {
          var id =this.value;
          var pos= $(this).attr('data-id');
          changeDropdown(id,pos);

        });
             $("#remove-skills").on("click", function (e) {

           $last_row_no = $(".user-skill-table").find('tr:last .skill-row-no').val();
            if($last_row_no > -1){
                $(".user-skill-table").find('tr:last').remove();
                if($last_row_no == 0){
                    $('#remove-skills').hide();
                }
            }else{
                $('#remove-skills').hide();
            }
        });

        $("#remove-certificate").on("click", function (e) {
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
        $('#userProfileForm').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
             var id=$('.user_id').val();
             var imageValue = $('#image_input').val();
             var  role = $(".role").val();
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

            var formData = new FormData($('#userProfileForm')[0]);
            formData.append('role', role);
            var url= "{{route('management.profileTabStore',':id')}}";
                  var url = url.replace(':id', id);
            resize.croppie('result', {
                type: 'canvas',
                size: {width:512, height:512},
                quality: 1,
                circle: false
            }).then(function (img) {
                if(imageValue != "" && imageValue != null) {
                    formData.append("image", img);
                }

                  $.ajax({
                        url: url,

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,

                success: function (data) {
                    if (data.success) {
                        swal({
                          title: "Updated",
                          text: "User profile has been updated successfully",
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });

                    } else {
                        console.log(data);
                        swal("Oops", "User profile updation was unsuccessful", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError,err) {
                    console.log(xhr.status);
                    console.log(xhr.responseJSON.errors);
                    var obj = xhr.responseJSON.errors;
                    console.log(obj[Object.keys(obj)[0]]);
                    swal("Oops",obj[Object.keys(obj)[0]], "error");
                },
                contentType: false,
                processData: false,

            });});
        });


$('#savepoinfo_expense').on('click', function(e) {
        e.preventDefault();
        var id=$('.user_id').val();
        var max_allowable_expense =$('input[name="max_allowable_expense"]').val();
        var reporting_to_id=$('select[name="reporting_to_id"]').val();

                  var url= "{{route('management.expenseTabStore',':id')}}";
                  var url = url.replace(':id', id);
                  $.ajax({
                        url: url,

                        type: 'POST',
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                        data: {
                        'max_allowable_expense':max_allowable_expense,
                        'reporting_to_id': reporting_to_id,
                        'id':id,


                    },
                success: function (data) {

                    if (data.success) {

                        swal({
                          title: "Updated",
                          text: "Expense details has been updated successfully",
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });
                    } else {
                           swal("Warning", "Something went wrong", "warning");
                           console.log(data);
                    }
                },

            });

});

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
         $('select[name="skillvalue_'+pos+'"] option[value="'+user_option_value_id+'"]').prop('selected',true);
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


</script>
@stop
