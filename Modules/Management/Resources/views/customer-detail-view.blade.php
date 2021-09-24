@extends('layouts.app')
@section('content')
<div class="table_title">
@foreach($customerData as $data)
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head document-screen-head">
{{$data->client_name}} ({{$data->project_number}})</div>
@endforeach
<div class="data-list-line row">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 data-list-label document-list-label margin-top-1
margin-bottom-10" style="margin-top: -8px;"></div>
</div>
</div>
<style>
  /* Style the caret/arrow */
  .cpid-div{
    margin-left:21.5%;
  }
  .fa-lg {
    font-size: 1.1em !important;
}
#customer-cpid-allocation{
    margin-left:-0.9em;
}
.btn-class{
    margin-bottom:5%;
}
.fa-2x{
        font-size:1.22em !important;
    };
  .cpid-cncl-btn{
      margin-left:37px;
  }
  .table_height{
    height:60px;
  }
  #cust_save_change{
    margin-left:-0.01em;
  }
  .table_width::after{
    content: "\a";
    white-space: pre;
}
  .incident_sub_tab{
    margin-left:0px;
  }
  .left_shift{

    padding-left:3em;
  }
    .left_shifts{
    padding-top:0.1em;
  }
  .sub_left_shift{
    margin-right:1em;
    padding-left:3em;
  }
  .add_new_qr{
    margin-bottom:1em;
  }
  .caret {
            cursor: pointer;
            border: 0;
            margin: 0;
            display: inline;
            padding: 1rem;
            user-select: none; /* Prevent text selection */
        }

        .list-group-item {
            border: none;
        }

        .custom-list-group-item {
            border: none;
            position: relative;
            display: block;
            padding: 18px 15px;
            margin-bottom: -1px;
            background-color: #fff;
        }

        /* Create the caret/arrow with a unicode, and style it */
        .caret::before {
            content: "\25B6";
            color: black;
            display: inline-block;
            margin-right: 6px;
        }

        /* Rotate the caret/arrow icon when clicked on (using JavaScript) */
        .caret-down::before {
            transform: rotate(90deg);
        }

        /* Hide the nested list */
        .nested {
            display: none;
        }

        /* Show the nested list when the user clicks on the caret/arrow (with JavaScript) */
        .active {
            display: block;
        }
 .user_tab{
     margin-top:-6%;
 }
.preference_button{
    margin-bottom:5%;
    margin-left: 1em;
    margin-top:1%;
}
.divclass1{
    margin-top:1em;
    margin-left: 1em;
}
.divclass2{
    margin-left: -0.4em;
}
.checkpoint_loc{
    margin-left: -0.1em;
}
.divclass4{
    margin-left: -1em;
    margin-top:0.8em;
}
.nav-item{
    margin-left: 14px;
}
html, body {
max-width: 100%;
overflow-x: hidden;
}
#tabList {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
.fa-toggle-on{
    margin-top:-30px;
}
.table th {
    padding: .75rem;
    margin-left:150px;
    vertical-align: top;
    border-top: 1px solid #e9ecef;

}
.cpid_align{
    margin-top:-18px;
}
.editTab{
    margin-left:30px;
    margin-top:0px;
}
.editActiveTab{
    margin-left:0px;
    margin-top:-7px;
}
.landing_page_delete_icon{
    margin-left:0px;
}
.trash_icon{
    margin-left:1em;
}

.short_head{
    margin-left:30px;
}
.cpid_head{
    margin-left:-60px;
}
.pos_head{
    margin-left:30px;
}
.break {
  width: 150px;
  word-wrap: break-word;
}
.break_qr{
    margin-left:-17px;
}
.document-screen-head{
    width:100%;
}
.document-list-label{
    width:100%;
}
.data-list-label{
    height:40px;
}
#myModal {
  align: center;

}
#landingPage{
    margin-left:0.6em;
}
.fence{
    margin-left:33px;
}

#exp-id{
    margin-right:50px;
}
.sub_title_name{
    color: #f26222;
}

.locName{
    margin-left:0.35em;
}
.ActiveClass{
    margin-left:0.65em;
}
.picMandate{
    margin-left:2.2em;
}
.picName{
    margin-left:0.45em;
}
.tab-content{
    margin-top:15px;
}
#tab1{
  display:inline-block;
  padding:0px;
  margin:0px;
}
.leftAlignClass{
    margin-left:-0.8em;
}
#tab0{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:100%;
  margin-left:10px;
}
#tab{
    margin-top:-2em;
    margin-left:-2em;
}
#tab7{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:110%;
  margin-right:30px;
  margin-left:-15px;
}
.profileTabButtonAlign{
    margin-left:0em;
}
.checkPoint{
    margin-left:0.2em;
}
#tab2{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:110%;
  margin-right:50px;
  margin-left:20px;
}
#tab-last{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:115%;
  margin-right:60px;
  margin-left:-15px;
}
.preference_tab_align{
    margin-left:-15px;
    margin-top:-10px;
}
#tab-new{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:110%;
  margin-right:60px;
  margin-left:-10px;
}
.buttons_align{
    margin-left:1em;
}
.approver_align{
    margin-left:-1em;
    margin-top:1em;
}
.cpid{
    margin-left:-60px;
}
.act{
    width:75px;
}
.exp{
    width:75px;
}
.exp1{
    width:80px;
}
.act1{
    width:80px;
}
.check
{
    text-align: center;
    vertical-align: middle;
}
.btn-sm{
    margin-top: 14px;
}
.desc{
    margin-left:-30px;
    margin-bottom:15px;
}
.short{
    margin-left:-35px;
}
#locations{
    margin-left:0px;
}
.blue{
    font-size: 18px;
}

.preference{
    margin-left:-20px;

}
.attempt{
    margin-left:-20px;
}
.checkpoint{
    margin-left:-30px;
}
.picture{
    margin-left:-40px;
}
.acti{
    margin-left:-40px;
}
.qr-button{
    margin-top:-10px;
}
.mandator{
    margin-left:3em;
    margin-top:-5px;
}
.incident{
    margin-left:-55px;

}
.location{
    margin-left:-20px;
}
#tabIncid{
    margin-left:-55px;
}
#tabFence{
    margin-left:-90px;
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
.btn-align{
    margin-right:10px;
}
#preferenceTab{
    margin-left:-0.9em;
}
.qrcode-location-div{
    width:100%;
    display: inline-block;
    margin-left:0px;
    padding-left:0px;
}
.qrcode-location-div label {
    width: 60px;
    margin-left: 0px;
    margin-right: 1%;
}
.fencetab{
    margin-left:-80px;
    margin-top:30px;
}
.active_button_align{
    margin-top:-20px;
    margin-left: -500px;
}
.nav-link{
   width:100%;
}
.incident_priority_title{
    font-size: 18px;
}
.incident_tabs{
    margin-left:-5px;
}
.editbutton1, .editbutton4, .editbutton7{
     float: right;
     cursor: pointer;
        }
.editbutton2,.editbutton6{
    float: right;
    cursor: pointer;
        }
.editbutton3, .editbutton5{
    float: right;
    cursor: pointer;
        }
.position_cls{
    margin-left:580px;
}
.sname{
    margin-left:180px;
}
ul, #myUL {
  list-style-type: none;
}

#myUL {
  margin: 0;
  padding: 0;
}
.breadcrumb-arrow li {
    width: 13.93%;
}
.breadcrumbs li a {
    white-space: nowrap;
}
.add-button {
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
            width:175px;
            float:right;
        }
.profile_btn{
    margin-bottom:5%;
}

/* Toggle button in test settings creation - Start */
.switch {
    position: relative;
    display: inline-block;
    width: 47px;
    height: 20px;
}

.switch input {
    display: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 13px;
    width: 13px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
}

input:checked+.slider {
    background-color: #003A63;
}

input:focus+.slider {
    box-shadow: 0 0 1px #003A63;
}

input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}
</style>


<div class="row">
<ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
            <li class="nav-item complete ">
                <a class="nav-link active profile_tab" data-toggle="tab" href="#profile"><span>1. Profile</span></a>
            </li>
            <li class="nav-item complete ">
                <a class="nav-link nav_tab cpid_tabs" data-toggle="tab" href="#cpid_allocation"><span>2. CPID Allocation</span></a>
            </li>
            <li class="nav-item complete ">
                <a class="nav-link nav_tab preference_tabs" data-toggle="tab" href="#preference"><span>3. Preferences</span></a>
            </li>
            <li class="nav-item complete ">
                <a class="nav-link nav_tab fence_tabs" data-toggle="tab" href="#fence"><span>4. Fences</span></a>
            </li>
            <li class="nav-item complete ">
                <a class="nav-link nav_tab location_tab" data-toggle="tab" href="#qr_code_location"><span>
                    5. QR Code </span></a>
            </li>
            <li class="nav-item complete ">
                <a class="nav-link nav_tab " data-toggle="tab" href="#incident_subjects" id="incid_tab"><span>
                    6. Incident Mapping </span></a>
            </li>


            <li class="nav-item complete landing_tab">
                <a class="nav-link nav_tab " data-toggle="tab" href="#landing_page" id="land-tab-caret"><span>
                    7. Landing Page </span></a>
            </li>


        </ul></div>

        <div class="tab-content">
            <div id="profile" class="tab-pane active candidate-screen">
            <div class="row">
    <div class="col-sm-12">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 align_header_tab candidate-screen-head">Profile
    @can(['customer_profile_tab_edit'])
    <span class="editbutton1 fas fa-edit" id="edit1">&nbsp;</span>
    @endcan
        </div>

        {{ Form::open(array('url'=>'#','id'=>'customer-profile-form','class'=>'form-horizontal', 'method'=> 'POST', 'novalidate'=>TRUE)) }}
        <div class="profile-form container-fluid">
                <div class="form-group row poinfoinput inputclass1" id="project_number">
                <div class="col-sm-2 sub_title_name">Project Number <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                {{ Form::text('project_number', null, array('class'=>'form-control project-number',
                  'placeholder'=>'Project Number')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="client_name">
                <div class="col-sm-2 sub_title_name">Client Name <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                {{ Form::text('client_name', null, array('class'=>'form-control','maxlength'=> 38,
                    'placeholder'=>'Client Name')) }}
                {{ Form::hidden('id', $id, array('class'=>'form-control','maxlength'=> 38,
                    'placeholder'=>'Client Name')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="contact_person_name">
                <div class="col-sm-2 sub_title_name">Contact Person Name</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('contact_person_name', null, array('class'=>'form-control',
                     'placeholder'=>'Contact Person Name')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="contact_person_email_id">
                <div class="col-sm-2 sub_title_name">Contact Person Email Id</div>
                <div class="col-sm-4 form_color">
                {{ Form::email('contact_person_email_id', null, array('class'=>'form-control',
                    'placeholder'=>'Contact Person Email Id')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="contact_person_phone">
                <div class="col-sm-2 sub_title_name">Contact Person Phone</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('contact_person_phone', null, array('class'=>'form-control phone',
                    'placeholder'=>'Contact Person Phone [ format (XXX)XXX-XXXX ]')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="contact_person_phone_ext">
                <div class="col-sm-2 sub_title_name">Ext.</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('contact_person_phone_ext', null, array('class'=>'form-control', 'placeholder'=>'Ext.',
                    'maxlength'=>255)) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="contact_person_cell_phone">
                <div class="col-sm-2 sub_title_name">Contact Person Cell Phone</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('contact_person_cell_phone', null, array('class'=>'form-control phone',
                    'placeholder'=>'Contact Person Cell Phone [ format (XXX)XXX-XXXX ]')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="contact_person_position">
                <div class="col-sm-2 sub_title_name">Contact Person Position</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('contact_person_position', null, array('class'=>'form-control',
                    'placeholder'=>'Contact Person Position')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="address">
                <div class="col-sm-2 sub_title_name">Address <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                {{ Form::text('address', null, array('class'=>'form-control address_details', 'placeholder'=>'Address')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="city">
                <div class="col-sm-2 sub_title_name">City <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                {{ Form::text('city', null, array('class'=>'form-control address_details', 'placeholder'=>'City')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="province">
                <div class="col-sm-2 sub_title_name">Province <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                {{ Form::text('province', null, array('class'=>'form-control address_details', 'placeholder'=>'Province')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="postal_code">
                <div class="col-sm-2 sub_title_name">Postal Code <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                {{ Form::text('postal_code', null, array('class'=>'postal-code form-control address_details',
                    'placeholder'=>'Postal Code')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="billing_address">
                <div class="col-sm-2 sub_title_name">Billing Address <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                <label for="same_address_check" class="control-label">@lang('Same as Site Address')&nbsp;</label>
                {{ Form::checkbox('same_address_check',null,null, array('id'=>'check_same_address')) }}<br>

                {{ Form::text('billing_address', null, array('class'=>'form-control', 'placeholder'=>'Billing Address')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="industry_sector_lookup_id">
                <div class="col-sm-2 sub_title_name">Industry Sector <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                {{ Form::select('industry_sector_lookup_id',[null=>'Select']+$lookups['industrySectorLookup'],
                    old('industry_sector_lookup_id'),array('class' => 'form-control')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="region_lookup_id">
                <div class="col-sm-2 sub_title_name">Region <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                {{ Form::select('region_lookup_id',[null=>'Select']+$lookups['regionLookup'], old('region_lookup_id'),
                    array('class' => 'form-control')) }}
                    </div><br>
            </div>
            <div class="form-group row poinfoinput inputclass1">
                <div class="col-sm-2 sub_title_name"></div>
                <div class="col-sm-4">
              <input class="region-description form-control" disabled />
            </div><br>
            </div>

            <div class="form-group row poinfoinput inputclass1" id="description">
                <div class="col-sm-2 sub_title_name">Description</div>
                <div class="col-sm-4 form_color">
                {{ Form::textArea('description', null, array('class'=>'form-control', 'placeholder'=>'Description',
                    'rows'=>5)) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="proj_open">
                <div class="col-sm-2 sub_title_name">Project Open Date</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('proj_open', null, array('class'=>'form-control datepicker',
                    'placeholder'=>'Project Open Date (Y-m-d)')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="proj_expiry">
                <div class="col-sm-2 sub_title_name">Project Expiry Date</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('proj_expiry', null, array('class'=>'form-control datepicker',
                     'placeholder'=>'Project Expiry Date (Y-m-d)')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="arpurchase_order_no">
                <div class="col-sm-2 sub_title_name">AR Purchase Order Number</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('arpurchase_order_no', null, array('class'=>'form-control',
                    'placeholder'=>'AR Purchase Order Number')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="arcust_type">
                <div class="col-sm-2 sub_title_name">AR Customer Type</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('arcust_type', null, array('class'=>'form-control', 'placeholder'=>'AR Customer Type')) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="requester_name">
                <div class="col-sm-2 sub_title_name">Requestor Name <span class="mandatory">*</span></div>
                <div class="col-sm-4 form_color">
                {{Form::select('requester_name',$lookups['requesterLookup'], old('requester_name'),['id'=>'requester_id',
                     'class' => 'form-control', 'placeholder' => 'Please Select','style'=>'width: 100%'])}}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="requester_position">
                <div class="col-sm-2 sub_title_name">Requestor Position</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('requester_position', null, array('class'=>'form-control',
                    'placeholder'=>'Requestor Position','readonly'=>true,'disabled'=>true)) }}
                <small class="help-block"></small>
                </div>
            </div>
            <div class="form-group row poinfoinput inputclass1" id="requester_empno">
                <div class="col-sm-2 sub_title_name">Requestor Employee Number</div>
                <div class="col-sm-4 form_color">
                {{ Form::text('requester_empno', null, array('class'=>'form-control',
                     'placeholder'=>'Requestor Employee Number','readonly'=>true,'disabled'=>true)) }}
                <small class="help-block"></small>
                </div>
            </div>


            <div class="form-group row poinfoinput inputclass1" id="master_customer">
                <div class="col-sm-2 sub_title_name">Master Customer</div>
                <div class="col-sm-4 form_color">
                <select name="master_customer" id="master_customer" style="width: 100%;" class="form-control">
                                    <option value=0 selected>Select All</option>
                                    @foreach($lookups['parentcustomerLookup'] as $key=>$value)
                                        <option value={{$key}}>{{$value}}</option>
                                    @endforeach
                                </select>
                <small class="help-block"></small>
                </div>
            </div>

            <div class="form-group row poinfoinput inputclass1" id="stc_div">
                <div class="col-sm-2 sub_title_name">Is this a STC Customer</div>
                <div class="col-sm-4 form_color">
                <select name="stc" id="stc" style="width: 100%;" class="form-control">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
                <small class="help-block"></small>
                </div>
            </div>

            <div class="form-group row poinfoinput inputclass1" id="is_nmso_account_div">
                <div class="col-sm-2 sub_title_name">Is this a NMSO Account</div>
                <div class="col-sm-4 form_color">
                <select name="is_nmso_account" id="is_nmso_account" style="width: 100%;" class="form-control">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
                <small class="help-block"></small>
                </div>
            </div>

            <div style="display:none;" class="form-group row poinfoinput inputclass1" id="security_clearance_lookup_id_div">
                <div class="col-sm-2 sub_title_name">Security clearance required for this post</div>
                <div class="col-sm-4 form_color">
                    {{ Form::select('security_clearance_lookup_id',[null=>'Select']+$lookups['securityClearanceLookup'], isset($customer_stc_details) ? old('security_clearance_lookup_id',$customer_stc_details->security_clearance_lookup_id) : null,
                        ['class' => 'form-control','style'=>'width: 100%']) }}
                    <small class="help-block"></small>
                </div>
            </div>

            <div class="form-group row poinfoinput inputclass1" id="incident_report_logo">
                <div class="col-md-2">
                    <label for="guard_tour_duration" class="control-label sub_title_name">@lang('Incident Report Logo')</label>
                </div>
                <div class="col-md-2">
                    <input type="file" name="incident_report_logo" id="incident_report_logo_el">
                        <!-- todo:remove logo -->
                        <div id="incident-logo-section" style="display:none;">
                            <span class="image-info"></span>
                            <a href="javascript:void(0)" id="incident_reset_btn" class="btn btn-primary btn-sm">Reset</a>
                        </div>
                        <small class="help-block"></small>
                </div>
            </div></div>
            <div class='col-sm-6 btn-class'>

            <button type="button"  name="cancelbutton" class="btn btn-primary cancelbutton1 inputclass1 profileTabButtonAlign">Cancel</button>
            {{ Form::submit('Save', array('class'=>' btn btn-primary profileTabButtonAlign inputclass1','id'=>'cust_save_change'))}}
            {{ Form::close()}}
            </div>


        <div class="col-sm-12 user_tab" >
            <div class="row">
                <div class="col-sm-12">
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Project Number</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->project_number ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Client Name</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->client_name ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Contact Person Name </div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->contact_person_name ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Contact Person Email Id</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->contact_person_email_id ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Contact Person Phone</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->contact_person_phone  ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Ext.</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->contact_person_phone_ext  ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Contact Person Cell Phone</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->contact_person_cell_phone ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Contact Person Position</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->contact_person_position ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Address</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->address ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">City</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->city ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Province</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->province ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Postal Code</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->postal_code ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Industry Sector</div>
                @foreach($industrySector as $data)
                <div class="col-md-5 break">{{ $data->industry_sector_name ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Region</div>
                @foreach($region as $data)
                <div class="col-md-5 break">{{ $data->region_name ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Region Description</div>

                <div class="col-md-5 break" id="region_desc"></div>

                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Project Open Date</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->proj_open ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Project Expiry Date</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->proj_expiry ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">AR Purchase Order Number</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->arpurchase_order_no ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">AR Customer Type </div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->arcust_type ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Requestor Name</div>
                @foreach($requestorName as $data)
                <div class="col-md-5 break">{{ $data->first_name.' '.$data->last_name ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Requestor Position</div>
                <div class="col-md-5 break" id="ReqPosition"></div>
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Requestor Employee Number</div>
                <div class="col-md-5 break" id="ReqEmpNo"></div>
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Billing Address</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->billing_address ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Master Customer</div>
                @foreach($parentCustomer as $data)
                <div class="col-md-5 break">{{ $data->client_name.' ('.$data->project_number.')' ?:"--"}}</div>
                @endforeach
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Is this a STC Customer</div>
                <div class="col-md-5 break">{{ $singleCustomer->stc ? "YES":"NO"}}</div>
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Is this a NMSO Account</div>
                <div class="col-md-5 break">{{ $singleCustomer->stcDetails ? strtoupper($singleCustomer->stcDetails->nmso_account):"--"}}</div>
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Security clearance required for this post</div>
                <div class="col-md-5 break">{{ ($singleCustomer->stcDetails && $singleCustomer->stcDetails->trashed_security_clearance) ? $singleCustomer->stcDetails->trashed_security_clearance->security_clearance:"--"}}</div>
                </div>
                <div class="form-group row">
                <div class="col-md-2 sub_title_name ">Description</div>
                @foreach($customerData as $data)
                <div class="col-md-5 break">{{ $data->description ?:"--"}}</div>
                @endforeach
                </div>

                </div>
            </div>
        </div>
    </div></div>
            </div>
            <div id="cpid_allocation" class="tab-pane candidate-screen">
            <div class="row">
            <div class="col-sm-12">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12  candidate-screen-head">CPID Allocation
            @can(['cpid_allocation_edit'])
            <span class="editbutton2 fas fa-edit" id="edit2">&nbsp;</span>
            @endcan
            </div>
        <div id="cpidTab ">
        {{ Form::open(array('url'=>'#','id'=>'cpid_form','class'=>'form-horizontal', 'method'=> 'POST',
             'novalidate'=>TRUE)) }}
        {{ Form::hidden('id', "") }}
            <div class="col-sm-4 table-responsive pop-in-table" id="customer-cpid-allocation">
                <table class="table customer-cpid-allocation-table inputclass2">
                    <thead>
                        <tr>
                            <th>CPID</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="form-group col-sm-6 cpid-div inputclass2">
                <label for="add-cpid-allocation" id="add-cpid-allocation" class="col-sm-1 btn btn-primary"
                 style="margin-right:1%;">+</label>
                <label for="remove-cpid-allocation" id="remove-cpid-allocation" class="col-sm-1 btn btn-primary">-
                </label>
            </div>
            <div class="buttons_align">
            {{ Form::reset('Cancel', array('class'=>'btn btn-primary inputclass2 cancelbutton cpid-cncl-btn',
                'id'=>'cpid_cancelbutton','aria-hidden'=>true))}}
            {{ Form::submit('Save', array('class'=>'button btn btn-primary inputclass2 ','id'=>'mdl_save_change '))}}
            {{ Form::close() }}
            </div>
        </div>

        <div class="row cpid_tab container-fluid">
        <div class="col-sm-8">
        <div class="row">
        <b class="col-md-3 break ">CPID</b>
        <b class="col-md-3 break ">Short Name</b>
        <b class="col-md-4 break ">Position</b>
        </div>
        </div></div>

        <div class="row cpid_tab container-fluid">
        <div class="col-sm-8 cpid_align">
        <div class="row">
        @foreach($cpid as $data)
        <div class="col-md-3 break ">{{$data->cpid_lookup['cpid']?:"--"}}</div>
        <div class="col-md-3 break ">{{ $data->cpid_lookup['short_name'] ?:"--"}}</div>
        <div class="col-md-4 break ">{{$data->cpid_lookup['position']['position']?:"--"}}</div><br>
        @endforeach
        </div></div></div></div>
        </div>
            </div>
            <div id="preference" class="tab-pane candidate-screen">
            <div class="row">
            <div class="col-sm-12">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12  candidate-screen-head">Preferences
            @can(['preference_tab_edit'])
            <span class="editbutton3 fas fa-edit" id="edit3">&nbsp;</span>
            @endcan
                </div>

            <div class="col-sm-6">
        <div role="tabpanel" class="tab-pane inputclass3" id="preferenceTab">
        {{ Form::open(array('url'=>'#','id'=>'preference_form','class'=>'form-horizontal', 'method'=> 'POST',
             'novalidate'=>TRUE)) }}
        {{ Form::hidden('id', "") }}
            <div class="form-group" id="show_in_sitedashboard">
                <label for="show_in_sitedashboard" class="col-lg-9 control-label">@lang('Show in Site Dashboard')</label>
                {{ Form::checkbox('show_in_sitedashboard', 1,'checked') }}
                <small class="help-block"></small>
            </div>
            <div class="form-group" id="facility_booking">
                <label for="facility_booking" class="col-lg-9 control-label">@lang('Facility Booking')</label>
                {{ Form::checkbox('facility_booking', 1) }}
                <small class="help-block"></small>
            </div>
            <div class="form-group" id="shift_journal">
                <label for="shift_journal" class="col-lg-9 control-label">@lang('Shift Journal')</label>
                {{ Form::checkbox('shift_journal_enabled', 1) }}
                <small class="help-block"></small>
            </div>
            <div class="form-group" id="time_shift_enabled">
                <label for="time_shift_enabled" class="col-lg-9 control-label">@lang('Enable Time shift')</label>
                {{ Form::checkbox('time_shift_enabled', 1) }}
                 <small class="help-block"></small>
            </div>
            <div class="form-group" id="guard_tour">
                <label for="guard_tour" class="col-lg-9 control-label">@lang('Guard Tour')</label>
                {{ Form::checkbox('guard_tour_enabled', 1) }}
                <small class="help-block"></small>
            </div>
            <div class="form-group sub_left_shift" id="interval_check" style="display:none">
                <label for="interval_check" class="col-lg-9 control-label">@lang('Interval Check-in Required')</label>
                {{ Form::checkbox('interval_check', 1,array('id'=>'interval_check')) }}
                <small class="help-block"></small>
            </div>
            <div class="form-group left_shift" id="guard_tour_duration" style="display:none">
                <div class="col-lg-9">
                    <label for="guard_tour_duration" class="control-label">@lang('Duration to set the interval (Hours)')
                    </label></div>
                    <div class="col-lg-3">
                    {{ Form::text('guard_tour_duration', null, array('class'=>'form-control','placeholder'=>'Duration (Hours)',
                         'id' => 'duration')) }}
                    </div>
                    <div class="col-lg-12">
                    <small class="help-block"></small>
                    </div>
                </div>
            <div class="form-group" id="overstay_enabled">
                <label for="overstay_enabled" class="col-lg-9 control-label">@lang('Overtime Enabled')</label>
                {{ Form::checkbox('overstay_enabled', 1) }}
                <small class="help-block"></small>
            </div>
            <div class="form-group left_shift" id="overstay_time" style="display:none">
                <div class="col-lg-9">
                    <label for="overstay_time" class="control-label">@lang('Overstay Time')</label></div>
                    <div class="col-lg-8">
                    {{ Form::text('overstay_time', null, array('class'=>'form-control','placeholder'=>'Duration',
                         'id' => 'overstay_timepicker')) }}
                    </div>
                    <div class="col-lg-12">
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group">
                            <label for="basement_mode" class="col-lg-9 control-label">@lang('Basement Mode')</label>
                            <input type="checkbox" id="basement_mode" name="basement_mode" />
                            <small class="help-block"></small>
                        </div>

                        <div class="form-group basement_mode left_shift" id="basement_interval" style="display:none">
                            <div class="col-lg-4">
                                <label for="basement_interval" class="control-label">@lang('Basement Interval')</label></div>
                            <div class="col-lg-8">
                                {{ Form::text('basement_interval', null, array('class'=>'form-control binterval','placeholder'=>'00:00', 'id' => 'basement_interval')) }}
                            </div>
                            <div class="col-lg-12">
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group basement_mode left_shift" id="basement_noofrounds" style="display:none">
                            <div class="col-lg-4">
                                <label for="basement_noofrounds" class="control-label">@lang('No of Rounds')</label></div>
                            <div class="col-lg-8">
                                <input type="number" id="basement_noofrounds" name="basement_noofrounds" class="form-control" placeholder='No of Rounds' />

                            </div>
                            <div class="col-lg-12">
                                <small class="help-block"></small>
                            </div>
                        </div>
                <div class="form-group" id="geo_fence">
                    <label for="geo_fence" class="col-lg-9 control-label">@lang('Geo Fence')</label>
                    {{ Form::checkbox('geo_fence', 1,'') }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="mobile_security_patrol_site">
                    <label for="mobile_security_patrol_site" class="col-lg-9 control-label">
                        @lang('Mobile Security Patrol Site')</label>
                    {{ Form::checkbox('mobile_security_patrol_site', 1,'') }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group sub_left_shift" id="geo_fence_satellite" style="display:none">
                    <label for="geo_fence_satellite" class="col-lg-9 control-label">
                        @lang('Geo Fence Satellite Tracking')</label>
                    {{ Form::checkbox('geo_fence_satellite', 1,'') }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="employee_rating_response">
                    <label for="employee_rating_response" class="col-lg-9 control-label">
                        @lang('Enable employee response for rating')</label>
                    {{ Form::checkbox('employee_rating_response', 1) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group left_shift" id="employee_rating_response_time" style="display:none">
                    <div class="col-lg-9">
                        <label for="employee_rating_response_time" class="control-label">
                            @lang('Response Time (Days)')</label>
                    </div>
                    <div class="col-lg-8">
                    {{ Form::number('employee_rating_response_time', null, array('class'=>'form-control',
                        'placeholder'=>'Duration (Days)','min'=>1, 'id' => 'employee_rating_timepicker')) }}
                    </div>
                    <div class="col-lg-12">
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="qr_patrol_enabled">
                    <label for="qr_patrol_enabled" class="col-lg-9 control-label">@lang('Enable QR Patrol')</label>
                    {{ Form::checkbox('qr_patrol_enabled', 1) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group left_shift" id="qr_picture_limit" style="display:none">
                    <div class="col-lg-4">
                        <label for="qr_picture_limit" class="control-label">@lang('Picture Limit')</label>
                    </div>
                    <div class="col-lg-8">
                        {{ Form::number('qr_picture_limit', null, array('class'=>'form-control',
                            'placeholder'=>'Maximum Pictures Count','min'=>1, 'id' => 'pic_limit')) }}
                    </div>
                    <div class="col-lg-12">
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group " id="qr_interval_check" style="display:none">
                    <label for="qr_interval_check" class="col-lg-9 control-label">&nbsp;&nbsp;
                        @lang('QR Interval Check-in')</label>
                    {{ Form::checkbox('qr_interval_check', 1) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group left_shift" id="qr_duration" style="display:none">
                    <div class="col-lg-9">
                        <label for="qr_duration" class="control-label">@lang('Duration to set the interval (Minutes)')
                        </label>
                    </div>
                    <div class="col-lg-8">
                        {{ Form::text('qr_duration', null, array('class'=>'form-control',
                            'placeholder'=>'Duration (Minutes)', 'id' => 'qrduration')) }}
                    </div>
                    <div class="col-lg-12">
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="key_management_enabled">
                    <label for="key_management_enabled" class="col-lg-9 control-label">@lang('Key Management')</label>
                    {{ Form::checkbox('key_management_enabled', 1) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group sub_left_shift" id="key_management_signature" style="display:none">
                    <label for="key_management_signature" class="col-lg-9 control-label">&nbsp;&nbsp;
                        @lang('Enable Signature')</label>
                    {{ Form::checkbox('key_management_signature', 1,array('class'=>'form-control shift_left')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group sub_left_shift" id="key_management_image_id" style="display:none">
                    <label for="key_management_image_id" class="col-lg-9 control-label">&nbsp;&nbsp;
                        @lang('Enable Image For ID')</label>
                    {{ Form::checkbox('key_management_image_id', 1,array('class'=>'form-control shift_left')) }}
                    <small class="help-block"></small>
                </div>
                {{Form::hidden('active',1)}}
                <div class="form-group" id="motion_sensor_enabled">
                            <label for="motion_sensor_enabled" class="col-lg-9 control-label">@lang('Enable Motion Sensor')</label>
                            {{ Form::checkbox('motion_sensor_enabled', 1) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group left_shift" id="motion_sensor_incident_subject" style="display:none">
                            <div class="col-lg-4" style="padding-left: 28px">
                                <label for="motion_sensor_incident_subject" class="control-label">@lang('Incident Subject')</label>
                            </div>
                            <div class="col-lg-8">
                                {{ Form::select(
                                        'motion_sensor_incident_subject',
                                         [null=>'Please Select']+$allocatedIncidentSubjects,
                                         null,
                                         array('class'=>'form-control', 'id' => 'motion_sensor_incident_subject_id')
                                  ) }}
                            </div>
                            <div class="col-lg-12">
                                <small class="help-block"></small>
                            </div>
                        </div>
                <div class="form-group" id="visitor_screening_enabled">
                    <label for="visitor_screening_enabled" class="col-lg-9 control-label">@lang('Visitor Screening')</label>
                    {{ Form::checkbox('visitor_screening_enabled', 1) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group row" id="time_sheet_approver_id">
                <div class="col-lg-12">
                <label for="time_sheet_approver_id" class="col-lg-9 control-label">@lang('Timesheet Approver')</label></div>
                <div class="col-lg-7">
                {{ Form::select('time_sheet_approver_id',[null=>'Please Select'] + $customerAllocattedUsers,null,array('class'=>'form-control', 'id' => 'time_sheet_approver_id')) }}
                </div>
                <small class="help-block"></small>
                </div>

                <div class="form-group left_shift" id="time_sheet_approver_email" style="display:none">
                <div class="col-lg-4" style="padding-left: 28px">
                <label for="time_sheet_approver_email" class="control-label">@lang('Timesheet Approver Email')</label>
                </div>
                <div class="col-lg-8">
                {{ Form::text('time_sheet_approver_email', null, array('class'=>'form-control','placeholder'=>'Email', 'id' => 'time_sheet_approver_email','readonly'=>true,'disabled'=>true)) }}
                <small class="help-block"></small>
                </div>
                </div>

      <div class="preference_button">
      {{ Form::reset('Cancel', array('class'=>'btn btn-primary inputclass3 cancelbutton','id'=>'preference_cancelbutton',
        'aria-hidden'=>true))}}
      {{ Form::submit('Save', array('class'=>'button btn btn-primary inputclass3 ','id'=>'save_preference'))}}
      {{ Form::close() }}
      </div>
            </div></div>


    <div class="row col-sm-6 preference_tab_align ">
    <div class="col-sm-12">
        @foreach($preference as $data)
        <div class="row">
            <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Show in Site Dashboard</div>
            <div class="col-sm-6">
            @if($data['show_in_sitedashboard']==0 )
            <button type="button" class="btn btn-danger btn-sm exp " >Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Facility Booking</div>
            <div class="col-sm-6">
            @if($data['facility_booking']==0 )
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
       </div>
       <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Shift Journal</div>
            <div class="col-sm-6">
            @if($data['shift_journal_enabled']==0 )
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Enable Time shift</div>
            <div class="col-sm-6">
            @if($data['time_shift_enabled']==0 )
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Guard Tour</div>
            <div class="col-sm-6">
            @if($data['guard_tour_enabled']==0 && $data['guard_tour_duration']==null)
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        @if($data['guard_tour_enabled']==1)
        @if($data['guard_tour_duration']!=null)
       <div class="row">
            <div class="col-sm-6 left_shift" style="padding-top:16px;"> Interval Check-in Required </div>
            <div class="col-sm-6"><button type="button" class="btn btn-success btn-sm exp ">Active</button></div><br>
        </div>
       <div class="row">
            <div class="col-sm-6 left_shift">Duration to set the interval (Hours)</div>
            <div class="col-sm-6 left_shifts " >{{$data['guard_tour_duration']}}</div><br>
       </div>

        @else
        <div class="row">
            <div class="col-sm-6 left_shift" style="padding-top:16px;"> Interval Check-in Required </div>
            <div class="col-sm-6"><button type="button" class="btn btn-danger btn-sm exp ">Inactive</button></div><br>
        </div>
        @endif
        @endif
        @if($data['overstay_time']!=null)
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Overtime Enabled</div>
        <div class="col-sm-6"><button type="button" class="btn btn-success btn-sm exp ">Active</button></div><br>
        </div>
        <div class="row">
            <div class="col-sm-6 left_shift">Overstay Time</div>
            <div class="col-sm-6 left_shifts ">{{$data['overstay_time']}}</div><br>
        </div>
        @else
       <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Overtime Enabled</div>
        <div class="col-sm-6"><button type="button" class="btn btn-danger btn-sm exp ">Inactive</button></div><br>
        </div>
        @endif
        @if($data['basement_mode']!=null)
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Basement Mode</div>
        <div class="col-sm-6"><button type="button" class="btn btn-success btn-sm exp ">Active</button></div><br>
        </div>
        <div class="row">
            <div class="col-sm-6 left_shift" >Basement Interval</div>
            <div class="col-sm-6 left_shifts ">{{$data['basement_interval']}}</div><br>
        </div>
        <div class="row">
            <div class="col-sm-6 left_shift">No of Rounds</div>
            <div class="col-sm-6 left_shifts ">{{$data['basement_noofrounds']}}</div><br>
        </div>
        @else
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Basement Mode</div>
        <div class="col-sm-6"><button type="button" class="btn btn-danger btn-sm exp ">Inactive</button></div><br>
        </div>
        @endif
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Geo Fence</div>
            <div class="col-sm-6">
            @if($data['geo_fence']==0 )
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Mobile Security Patrol Site</div>
            <div class="col-sm-6">
            @if($data['mobile_security_patrol_site']==0)
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        @if($data['mobile_security_patrol_site']==1)
        <div class="row">
        <div class="col-sm-6 left_shift" style="padding-top:16px;">Geo Fence Satellite Tracking</div>
            <div class="col-sm-6">
            @if($data['geo_fence_satellite']==0 )
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        @endif
        @if($data['employee_rating_response_time']!=null && $data['employee_rating_response']==1)
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Enable employee response for rating</div>
        <div class="col-sm-6"><button type="button" class="btn btn-success btn-sm exp ">Active</button></div><br>
        </div>
        <div class="row">
            <div class="col-sm-6 left_shift " >Response Time (Days)</div>
            <div class="col-sm-6 left_shifts ">{{$data['employee_rating_response_time']}}</div><br>
        </div>
        @else
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Enable employee response for rating</div>
        <div class="col-sm-6"><button type="button" class="btn btn-danger btn-sm exp ">Inactive</button></div><br>
        </div>
        @endif
        @if($data['qr_picture_limit']!=null)
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Enable QR Patrol</div>
        <div class="col-sm-6"><button type="button" class="btn btn-success btn-sm exp ">Active</button></div><br>
        </div>
        <div class="row">
            <div class="col-sm-6 left_shift">Picture Limit</div>
            <div class="col-sm-6 left_shifts ">{{$data['qr_picture_limit']}}</div><br>
        </div>
        @else
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Enable QR Patrol</div>
        <div class="col-sm-6"><button type="button" class="btn btn-danger btn-sm exp ">Inactive</button></div><br>
        </div>
        @endif
        @if($data['qr_patrol_enabled']==1)
        @if($data['qr_duration']!=null)
        <div class="row">
        <div class="col-sm-6 left_shift" style="padding-top:16px;"> QR Interval Check-in</div>
        <div class="col-sm-6"><button type="button" class="btn btn-success btn-sm exp ">Active</button></div><br>
        </div>
        <div class="row">
            <div class="col-sm-6 left_shift">Duration to set the interval (Minutes)</div>
            <div class="col-sm-6 left_shifts ">{{$data['qr_duration']}}</div><br>
        </div>
        @else
        <div class="row">
        <div class="col-sm-6 left_shift" style="padding-top:16px;"> QR Interval Check-in</div>
        <div class="col-sm-6"><button type="button" class="btn btn-danger btn-sm exp ">Inactive</button></div><br>
        </div>
        @endif
        @endif
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Key Management</div>
            <div class="col-sm-6">
            @if($data['key_management_enabled']==0 )
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        @if($data['key_management_enabled']==1 )
        <div class="row">
        <div class="col-sm-6 left_shift" style="padding-top:16px;">Key Management Signature</div>
            <div class="col-sm-6">
            @if($data['key_management_signature']==0 || $data['key_management_enabled']==0)
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        <div class="row">
        <div class="col-sm-6 left_shift" style="padding-top:16px;">Key Management Image Enabled</div>
            <div class="col-sm-6">
            @if($data['key_management_image_id']==0 || $data['key_management_enabled']==0 )
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        @endif
        @if($data['motion_sensor_incident_subject']!=null)
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Enable Motion Sensor</div>
        <div class="col-sm-6"><button type="button" class="btn btn-success btn-sm exp ">Active</button></div><br><br>
        </div>
        <div class="row">
            <div class="col-sm-6 left_shift">Incident Subject</div>
            @foreach($incidentSubjectArr as $incidentSubject)
            <div class="col-sm-6 left_shifts nowrap" >{{$incidentSubject->subject}}</div><br>
            @endforeach
        </div>
        @else
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;"> Enable Motion Sensor</div>
        <div class="col-sm-6"><button type="button" class="btn btn-danger btn-sm exp ">Inactive</button></div><br>
        </div>
        @endif
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Visitor Screening</div>
            <div class="col-sm-6">
            @if($data['visitor_screening_enabled']==0 )
            <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button><br>
            @else
            <button type="button" class="btn btn-success btn-sm act ">Active</button><br>
            @endif
            </div>
        </div>
        @if($data['time_sheet_approver_id'] != null )
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Timesheet Approver</div>
        <div class="col-sm-6">
        @foreach($approverDetails as $data)
        <div class="col-sm-6 nowrap left_shifts approver_align" >{{$data->name_with_emp_no}}</div><br>
        @endforeach
        </div><br>
        </div>
        <div class="row">
            <div class="col-sm-6 left_shift">Timesheet Approver Email</div>
            @foreach($approverDetails as $data)
            <div class="col-sm-6 nowrap left_shifts" >{{$data->email}}</div><br>
            @endforeach
        </div>
        @elseif($data['time_sheet_approver_id'] == null )
        <div class="row">
        <div class="col-sm-6 sub_title_name" style="padding-top:16px;">Timesheet Approver</div>
        <div class="col-sm-6">
        <button type="button" class="btn btn-danger btn-sm exp ">Inactive</button>
        </div><br><br>
        </div>
        @endif



        @endforeach
    </div></div>

            </div></div></div>
            <div id="fence" class="tab-pane candidate-screen">
            <div class="row">
            <div class="col-sm-12">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12  candidate-screen-head">Fences
            @can(['fence_tab_edit'])
            <span class="editbutton7 fas fa-edit" id="edit7">&nbsp;</span>
            @endcan
            </div>



           <div  class="inputclass7" id="fenceTab">
               <div class="form-group" id="fence_interval">
                   <div class="col-md-2 sub_title_name">
                       <label for="fence_interval"> Fence Interval (Minutes)</label>
                   </div>
                   <div class="col-md-3">
                       <input type="number" class="form-control" name="fence_interval"  id="fence_interval" >
                       <small class="help-block"></small>
                   </div>
               </div>
               <div class="form-group" id="contractual_visit_unit">
                   <div class="col-md-2 sub_title_name">
                       <label for="contractual_visit_unit"> Contractual Visit Unit </label>
                   </div>
                   <div class="col-md-3">
                       <select name="contractual_visit_unit" id="contractual_visit_unit" class="form-control">
                           <option value="">Select</option>
                           @foreach($lookups['contractualVisitLookup'] as $key=> $row)
                           <option value="{{$key}}">{{$row}}</option>
                           @endforeach
                       </select>
                       <small class="help-block"></small>
                   </div>
               </div>
               <input type="hidden" name="fencecount" id="fencecount" value="0" />
               <div class="row" id="fencenew" style="display:none;text-align:center">
                   <h2>Fence can be added after Customer creation</h2>
               </div>
               <div class="row" id="fencerowbottom"></div>
               <div class="col-sm-12 table-responsive "></div>
           </div>
           <div class='buttons_align'>
           {{ Form::reset('Cancel', array('class'=>'btn btn-primary inputclass7 cancelbutton','id'=>'cancelbutton',
            'aria-hidden'=>true))}}
           {{ Form::submit('Save', array('class'=>'button btn btn-primary inputclass7 ','id'=>'fence_save'))}}
           </div>
           <!--END--  Fence Tab   -->

           <div class="col-sm-12" >
            <div class="row fence_tab">
                <div class="col-sm-2 fences ">
                    <span class="sub_title_name"> Fence Interval (Minutes)</span>&nbsp; &nbsp;<br><br>

                    <span class="sub_title_name"> Contractual Visit Unit</span>&nbsp; &nbsp;

                </div>
                <div class="col-sm-6 fences ">

                    @foreach($customerData as $data)
                    <span>&nbsp;{{ $data->fence_interval ?:"--"}}</span><br><br>
                    @endforeach
                    @foreach($viewContractList as $data)
                    <span>{{ $data->value ?:"--"}}</span><br><br>
                    @endforeach
                </div>
            </div>
        </div>
            </div></div></div>
            <div id="qr_code_location" class="tab-pane candidate-screen">
            <div class="row">
            <div class="col-sm-12">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12  candidate-screen-head">QR Code
            @can(['qrcode_location_edit'])
            <span class="editbutton4 fas fa-edit" id="edit4">&nbsp;</span>
            @endcan
                 </div>

        <div class="row">
        <div class="col-sm-12">
            <div role="tabpanel" class="tab-pane inputclass4" id="qrcodeTab">
                <div class="add-new add_new_qr" data-title="Add New QR Code">Add
                    <span class="add-new-label">New</span>
                </div>
                <table class="table table-bordered" id="qrcode-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>QR Code</th>
                            <th>Checkpoint</th>
                            <th>No of Attempts (Weekday)</th>
                            <th>No of Attempts (Weekend)</th>
                            <th>Total no of Attempts (Weekday)</th>
                            <th>Total no of Attempts (Weekend)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
                <br>
                <div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title incident_priority_title" id="myModalLabel">Add New QR Code</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            {{ Form::open(array('url'=>'#','id'=>'qrcode-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                            {{ Form::hidden('qrcodeid', null) }}
                            {{ Form::hidden('customerids', null) }}
                            <div class="modal-body">
                            <div class="row">
                            <div class="form-group col-sm-12" id="qrcode_active" style="display: none;">
                                            <label class="switch" style="float:right;">
                                            <input name="active" type="checkbox">
                                            <span class="slider round"></span>
                                            </label>
                                        </div>
                            </div>

                                <div class="row">
                                    <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode'>
                                        <label for='qrcode' class='control-label'>QR Code <span class='mandatory'>*</span>
                                        </label>
                                        <input class='form-control qrcode' placeholder='QR Code' name='qrcode' id='qr_code'
                                        type='text'>
                                        <small class='help-block'></small>
                                    </div>
                                    <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location'>
                                        <label for='location' class='control-label'>Checkpoint<span class='mandatory'>*
                                        </span>
                                        </label>
                                        <input class='form-control location' placeholder='Checkpoint' name='location'
                                        id='locations' type='text'>
                                        <small class='help-block'></small>
                                    </div>
                                </div>
                                <div class="row">
                                <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='no_of_attempts'>
                                        <label for='no_of_attempts' class='control-label'>No of attempts per person per shift (weekday)
                                            <span class='mandatory'>*</span></label>
                                        <input class='form-control no_of_attempts' id='attempts_weekday'
                                         placeholder='Number of Attempts' name='no_of_attempts' type='text'>
                                        <small class='help-block'></small>
                                    </div>
                                    <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='no_of_attempts_week_ends'>
                                        <label for='no_of_attempts_week_ends' class='control-label'>No of attempts per person per shift (weekend)
                                            <span class='mandatory'>*</span></label>
                                        <input class='form-control no_of_attempts_week_ends' id='attempts_weekend'
                                         placeholder='Number of Attempts' name='no_of_attempts_week_ends' type='text'>
                                        <small class='help-block'></small>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='tot_no_of_attempts_week_day'>
                                        <label for='tot_no_of_attempts_week_day' class='control-label'>Total no of attempts (weekday)
                                            <span class='mandatory'>*</span></label>
                                        <input class='form-control tot_no_of_attempts_week_day' id='tot_attempts_weekday'
                                         placeholder='Number of Attempts' name='tot_no_of_attempts_week_day' type='text'>
                                        <small class='help-block'></small>
                                    </div>
                                    <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='tot_no_of_attempts_week_ends'>
                                        <label for='tot_no_of_attempts_week_ends' class='control-label'>Total no of attempts (weekend)
                                            <span class='mandatory'>*</span></label>
                                        <input class='form-control tot_no_of_attempts_week_ends' id='tot_attempts_weekend'
                                         placeholder='Number of Attempts' name='tot_no_of_attempts_week_ends' type='text'>
                                        <small class='help-block'></small>
                                    </div>
                                </div>
                                <div class="row">
                                <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location_enable_disable'>
                                        <label for='location_enable_disable' class='control-label'>Enable/Disable Location
                                            <span class='mandatory'>*</span></label>
                                        <select class='form-control' id='location_enabled' name='location_enable_disable'>
                                            <option value='' selected='selected'>Select</option>
                                            <option value='1'>Enable</option>
                                            <option value='0'>Disable</option>
                                        </select>
                                        <small class='help-block'></small>
                                    </div>
                                    <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='picture_enable_disable'>
                                        <label for='picture_enable_disable' class='control-label'>Enable/Disable Picture
                                            <span class='mandatory'>*</span></label>
                                        <select class='form-control' name='picture_enable_disable' id='picture_enabled'
                                        onchange='getval(this,0);'>
                                            <option value='' selected='selected'>Select</option>
                                            <option value='1'>Enable</option>
                                            <option value='0'>Disable</option>
                                        </select>
                                        <small class='help-block'></small>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class='form-group col-xs-12 col-sm-12 col-md-12 col-lg-12' id='picture_mandatory'
                                     style=display:none>
                                        <label for='picture_mandatory' class='control-label'>Picture Mandatory
                                            <span class='mandatory'>*</span></label>
                                        <select class='form-control' name='picture_mandatory' id='picture_mandatory_id'>
                                            <option value='' selected='selected'>Select</option>
                                            <option value='1'>Yes</option>
                                            <option value='0'>No</option>
                                        </select>
                                        <small class='help-block'></small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer pull-left">
                                {{ Form::button('Cancel', array('class'=>'btn btn-primary ','data-dismiss'=>'modal'))}}
                                {{ Form::button('Save', array('class'=>'button btn btn-primary ','id'=>'btnSubmit'))}}
                            </div>
                                {{ Form::close() }}
                        </div>
                    </div>
                </div>
                {{ Form::reset('Cancel', array('class'=>'btn btn-primary inputclass4 cancelbutton save_qrcode',
                    'id'=>'cancelbutton','aria-hidden'=>true))}}
            </div>

            <div class="row qrcode_tab container-fluid">
            <div class="col-sm-2 qrcode">
                <b>QR Code</b><br><br><br>
                @foreach($qrCodeData as $data)
                <span class="break_qr col-sm-2">&nbsp;{{ substr($data->qrcode,0,10) ?:"--"}}</span><br><br>
                @endforeach
            </div>
            <div class="col-sm-2 checkpoint">
                <b class="checkPoint">Checkpoints</b><br><br><br>
                @foreach($qrCodeData as $data)
                <span class="checkpoint_loc">&nbsp;{{ $data->location ?:"--"}}</span><br><br>
                @endforeach
            </div>
            <div class="col-sm-1 attempt">
                <b class="noOfAttmpt">Attempts (Weekday)</b><br><br>
                @foreach($qrCodeData as $data)
                <span>&nbsp;{{ $data->no_of_attempts ?:"--"}}</span><br><br>
                @endforeach
            </div>
            <div class="col-sm-1 attempt">
                <b class="noOfAttmpt">Attempts (Weekend)</b><br><br>
                @foreach($qrCodeData as $data)
                <span>&nbsp;{{ $data->no_of_attempts_week_ends ?:"--"}}</span><br><br>
                @endforeach
            </div>
            <div class="col-sm-1 attempt">
                <b class="noOfAttmpt">Total Attempts (Weekday)</b><br><br>
                @foreach($qrCodeData as $data)
                <span>&nbsp;{{ $data->tot_no_of_attempts_week_day ?:"--"}}</span><br><br>
                @endforeach
            </div>
            <div class="col-sm-1 attempt">
                <b class="noOfAttmpt">Total Attempts (Weekend)</b><br><br>
                @foreach($qrCodeData as $data)
                <span>&nbsp;{{ $data->tot_no_of_attempts_week_ends ?:"--"}}</span><br><br>
                @endforeach
            </div>
            <div class="col-sm-1 location">
                <b class="locName">Location </b><br><br><br>
                @foreach($qrCodeData as $data)
                @if($data->location_enable_disable==0 )
                <button type="button" class="btn btn-danger btn-sm qr-button exp1">Disabled</button><br><br>
                @else
                <button type="button" class="btn btn-success btn-sm qr-button act1">Enabled</button><br><br>
                @endif
                @endforeach
            </div>
            <div class="col-sm-1 picture">
                <b class="picName">Picture </b><br><br><br>
                @foreach($qrCodeData as $data)
                @if($data->picture_enable_disable==0 )
                <button type="button" class="btn btn-danger btn-sm qr-button exp1">Disabled</button><br><br>
                @else
                <button type="button" class="btn btn-success btn-sm qr-button act1">Enabled</button><br><br>
                @endif
                @endforeach
            </div>
            <div class="col-sm-1 acti">
                <b class="ActiveClass">Active </b><br><br><br>
                @foreach($qrCodeData as $data)
                @if($data->qrcode_active==0 )
                <button type="button" class="btn btn-danger btn-sm qr-button exp1">No</button><br><br>
                @else
                <button type="button" class="btn btn-success btn-sm qr-button act1">Yes</button><br><br>
                @endif
                @endforeach
            </div>
            <div class="col-sm-2">
                <b>Picture Mandatory </b><br><br><br>
                <div class="mandator">
                <table>
                @foreach($qrCodeData as $data)
                <tr>
                <td>{{ $data->picture_mandatory ?"Yes" :"No"}}<br><br></td>
                </tr>
                @endforeach
                </table>
                </div>
            </div>
        </div>
    </div> </div>


            </div> </div> </div>
            <div id="landing_page" class="tab-pane candidate-screen">
            <div class="row">
            <div class="col-sm-12">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12  candidate-screen-head">Landing Page</div>

            <div class="row landing_page_tab">
            <div class="col-sm-12 landing_page">

                <div id="landingPage">

                        <div class="container-fluid " style="margin-left: 30px;">
                        @can(['Landing_page_edit'])
                            <input type="button" onclick="open_new_configuration();" value="Add New" class="btn btn-primary"
                            id="newTabContainer" style="float: right; margin-right: 1%;margin-top: -1%;"/>
                        @endcan
                        </div>

                        <div class="row">
                            <div class="col-md-5" id="tab"></div>
                        </div>
                </div>

            </div>
            </div>
            </div></div></div>


            <div id="incident_subjects" class="tab-pane candidate-screen">
            <div class="row">
            <div class="col-sm-12">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12  candidate-screen-head">Incident Mapping
            @can(['incident_subject_edit'])
            <span class="editbutton6 fas fa-edit" id="edit6">&nbsp;</span>
            @endcan
                 </div>

            <div class="row">
            <div class="col-sm-12">
            <div role="tabpanel" class="tab-pane inputclass6" id="incidentSubjectTab">
            <div class="add-button" id="add-incident-subject">Add New</div>
            <div class="add-button" id="edit-priority">Set Incident Priority </div>
            <div class="add-button" id="incident-recipient">Incident Recipients </div>


                        <table class="table table-bordered " id="customer-incident-table" style="width: 100%">
                            <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Response Time</th>
                                <th>Priority</th>
                                <th>SOP</th>
                                <th>Actions</th>

                            </tr>
                            </thead>
                        </table>
<br>

                        <div class="modal fade" id="recepientModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h4 class="modal-title incident_priority_title" id="myModalLabel">Incident Recipient</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>

                                    </div>
                                    {{ Form::open(array('url'=>'#','id'=>'recipient-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                                    {{ Form::hidden('pid', null) }}
                                    <div class="modal-body">
                                    <div class="form-group row">
                                    <div class="col-sm-4"><strong>Email</strong></div>
                                     <div class="col-sm-2 check"><strong>High</strong></div>
                                     <div class="col-sm-2 check"><strong>Medium</strong></div>
                                     <div class="col-sm-2 check"><strong>Low</strong></div>
                                    </div>
                                    <div id="dynamic-rows">
                                    </div>
                                    </div>

                                    <div class="modal-footer">
                                        {{ Form::button('Save', array('class'=>'button btn btn-primary','id'=>'recipientSubmit'))}}
                                        {{ Form::button('Cancel', array('class'=>'btn btn-primary','data-dismiss'=>'modal'))}}
                                    </div>
                                    {{ Form::close() }}


                                </div>
                            </div>
                        </div>
                    <div class="modal fade" id="priorityModal" data-backdrop="static" tabindex="-1" role="dialog"
                    aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h4 class="modal-title incident_priority_title" id="myModalLabel">Set Incident Priority</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>

                                    </div>
                                    {{ Form::open(array('url'=>'#','id'=>'priority-form','class'=>'form-horizontal',
                                         'method'=> 'POST')) }}
                                    {{ Form::hidden('pid', null) }}
                                    <div class="modal-body">
                                    <table  class="table" id="priority-table">
                                    <tr>
                                    <td><strong>Priority</strong></td>
                                     <td colspan="2"><strong>Response Range (in Hrs)</strong></td>
                                    </tr>
                                    </table>
                                    </div>
                                    <div class="modal-footer pull-left">
                                        {{ Form::button('Save', array('class'=>'button btn btn-primary',
                                            'id'=>'prioritySubmit'))}}
                                        {{ Form::button('Cancel', array('class'=>'btn btn-primary',
                                            'data-dismiss'=>'modal'))}}
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="incidentPriorityModal" data-backdrop="static" tabindex="-1"
                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h4 class="modal-title incident_priority_title" id="myModalLabel">Incident Mapping</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>

                                    </div>
                                    {{ Form::open(array('url'=>'#','id'=>'incident-mapping-form','class'=>'form-horizontal',
                                        'method'=> 'POST')) }}
                                    {{ Form::hidden('sid', null) }}
                                    {{ Form::hidden('priority_id',null, array('id' => 'priority_id')) }}
                                    <div class="modal-body ">

                                      <div class="form-group row" id="subject_id">
                                         <label for="subject" class="col-sm-3 control-label">Subject
                                             <span class="mandatory">*</span></label>
                                         <div class="col-sm-6">
                                           {{ Form::select('subject_id',[null=>'Select Subject']+$lookups['subject'],
                                             old('subject_id'),array('class' => 'form-control select2',
                                             'style'=>"width: 100%;")) }}
                                             <small class="help-block"></small>
                                         </div>
                                     </div>
                                     <div class="form-group row" id="category_id">
                                         <label for="category" class="col-sm-3 control-label">Category
                                             <span class="mandatory">*</span></label>
                                         <div class="col-sm-6">
                                         {{ Form::select('category_id',[null=>'Select Category']+$lookups['incidentCategory'],
                                             old('category_id'),array('class' => 'form-control select2',
                                              'style'=>"width: 100%;")) }}
                                             <small class="help-block"></small>
                                         </div>
                                     </div>
                                     <div class="form-group row" id="incident_response_time" >
                                         <label for="name" class="col-sm-3 control-label">Response Time
                                              <span class="mandatory">*</span></label>
                                         <div class="col-sm-2">
                                      {{--   <input type="time" class="form-control" name="eta"> --}}
                                         <input class='form-control' placeholder='Hour' name='incident_response_time' min="1"
                                         id='incident_response_time' type='number'>
                                             <small class="help-block"></small>
                                         </div>
                                     </div>
                                     <div class="form-group row">
                                         <label for="name" class="col-sm-3 control-label">Priority</label>
                                         <div class="col-sm-2">
                                         <input class='form-control' readonly  name='priority' id='priority' type='text'>
                                             <small class="help-block"></small>
                                         </div>
                                     </div>
                                     <div class="form-group row" id="sop">
                                         <label for="name" class="col-sm-3 control-label">SOP
                                             <span class="mandatory">*</span></label>
                                         <div class="col-sm-6">
                                         {{ Form::textArea('sop', null, array('class'=>'form-control',
                                             'placeholder'=>'Please enter SOP details','rows'=>8)) }}
                                             <small class="help-block"></small>
                                         </div>
                                     </div>

                                    </div>
                                    <div class="modal-footer pull-left">
                                        {{ Form::button('Save', array('class'=>'button btn btn-primary',
                                            'id'=>'incidentSubjectMapping'))}}
                                        {{ Form::button('Cancel', array('class'=>'btn btn-primary',
                                            'data-dismiss'=>'modal'))}}
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                        {{ Form::reset('Cancel', array('class'=>'btn btn-primary inputclass6 cancelbutton save_qrcode',
                            'id'=>'cancelbutton','aria-hidden'=>true))}}
        </div>
        <div class="row incident_sub_tab container-fluid">
        <table class="col-sm-12">
<tr>
<th width="20%">Subject</th>
<th width="15%">Category</th>
<th width="15%">Response Time</th>
<th width="15%">Priority</th>
<th width="35%">SOP</th>
</tr>
<tr><td></td></tr>

@foreach($incidentData as $data)
@if($data->category || $data->incidentPriority)
<tr class="table_height">
<td>{{ $data->subject->subject ?:"--"}}</td>
<td>{{ $data->category?$data->category->name :"--"}}</td>
<td>{{ $data->incident_response_time ?$data->incident_response_time/60 :"--"}}&nbsp;Hour(s)</td>
<td>{{ $data->incidentPriority?$data->incidentPriority->value :"--"}}</td>
<td>{{ $data->sop ?:"--"}}</td>
</tr>
<tr class="table_width"></tr>
@endif
@endforeach


</table>
        </div>
        </div></div> </div>
        </div></div> </div>

        @include('management::partials.moreSteps')
        <script src="{{ asset('js/moreel.js') }}"></script>

        <script>

    function getCpidRow(key, create = false){
            let _value = (create == false) ? key : '';
            return `
                <tr>
                    <td>
                        <div class='form-group' id='cpid_allocation_${key}'>
                        <input type='hidden' name='row-no[]' class='row-no' value="${_value}">
                        <select class='form-control' name='cpid_${key}'>
                            <option value='' selected>Choose cpid</option>
                            @foreach($lookups['cpidLookup'] as $id=>$cpids)
                                <option value='{{$cpids->id}}'>{{$cpids->cpid}} 
                                @php if(!empty($cpids->position)) { @endphp
                                    ( {{$cpids->position->position }} )
                                    @php } @endphp
                                @php if(!empty($cpids->cpidFunction)) { @endphp
                                -{{$cpids->cpidFunction->name }}
                                @php } @endphp
                                </option>
                            @endforeach
                        </select>
                            <small class='help-block'></small>
                        </div>
                    </td>
                </tr>`;
    }

$(document).ready(function(){

    $("select[name=region_lookup_id").select2();
    var cusData={!!json_encode($singleCustomer)!!};
    var userId=cusData.requester_name;
        var requestorPosition={!!json_encode($requestorPosition)!!};
        var requestorEmpno={!!json_encode($requestorEmpno)!!};
        var reqPos=requestorPosition[userId][0];
        var empNo=requestorEmpno[userId];

            if( reqPos!=null && empNo!=null){
                $('#ReqPosition').text(requestorPosition[userId][0].position);
                 $('#ReqEmpNo').text(requestorEmpno[userId]);
            }else if(reqPos ==null && empNo!=null){
                 $('#ReqPosition').text("---");
                 $('#ReqEmpNo').text(requestorEmpno[userId]);
            }else if(reqPos !=null && empNo==null){
                 $('#ReqPosition').text(requestorPosition[userId][0].position);
                 $('#ReqEmpNo').text("---");
            }else{
                $('#ReqPosition').text("---");
                $('#ReqEmpNo').text("---");
            }

    });



            var data={!!json_encode($singleCustomer)!!};
            var id=data.region_lookup_id;
            var regionDetails={!!json_encode($regionDetails)!!};
            $('.region-description').val("");
            $('#region_desc').text(regionDetails[id]);



    $('select[name="region_lookup_id"]').on('change', function() {
            var id = $(this).val();
            var regionDetails={!!json_encode($regionDetails)!!};
            console.log(regionDetails[id]);
            $('.region-description').val("");
            $('.region-description').val(regionDetails[id]);

        });

$(document).ready(function() {
        $("#landing_page").on("click", function() {
            var toggler = document.getElementsByClassName("caret");
            var i;

            for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function() {
                this.parentElement.querySelector(".nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
            }
        });

        $("#landingPage #tabList #editTab").on("click", function(e) {
            console.log(e);
            console.log('clicked tab edit');
        });

        $("#landingPage #tabList #editActiveTab").on("click", function(e) {
            console.log(e);
            console.log('active tab');
        });

    });


    $("#check_same_address").click(function() {
            if ($("input[name=address]").val().length <= 0 || $("input[name=city]").val().length <= 0 || $("input[name=province]").val().length <= 0 || $("input[name=postal_code]").val().length <= 0) {
                swal("Warning", "Please enter address details", "warning");
                $(this).prop('checked', false);
            }
            if (this.checked) {
                var address = '';
                var city = '';
                var province = '';
                var postal_code = '';
                if ($("input[name=address]").val().length > 0)
                    var address = $("input[name=address]").val() + ', ';
                if ($("input[name=city]").val().length > 0)
                    var city = $("input[name=city]").val() + ', ';
                if ($("input[name=province]").val().length > 0)
                    var province = $("input[name=province]").val() + ', ';
                var postal_code = $("input[name=postal_code]").val();
                var full_addr = address + city + province + postal_code;
                $('input:text[name="billing_address"]').val(full_addr);
                $('input:text[name="billing_address"]').prop('readonly', true);
            } else {
                $('input:text[name="billing_address"]').val('');
                $('input:text[name="billing_address"]').prop('readonly', false);
            }
        });

        $(function() {
        $('#incident_reset_btn').on('click', function(e) {
            e.preventDefault();
            let customerId = $('input[name="id"]').val();
            swal({
                title: 'Are you sure?',
                text: "It will be permanently deleted",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it'
            }, function() {
                $.ajax({
                    url: "{{ route('managementCustomer.reset_incident_logo') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'customer_id': customerId
                    },
                    success: function(data) {
                        $('#incident_report_logo_el').val('');
                        if (data.success) {
                            $('#incident-logo-section').hide();
                            swal('Deleted', 'Your file has been deleted.', 'success');
                        } else {
                             swal("Oops", "Something went wrong", "warning");
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        swal("Oops", "Something went wrong", "warning");
                    },
                });

            });

        });

    });


        function hasValidIncidentLogo() {
            let fileEl = $('#incident_report_logo_el');
            let file = fileEl[0].files[0];
            if (!file) {
                return true; //allow empty logo
            }
            //check valid image
            if (!file.type.match('image.*')) {
                return false;
            }
            //todo:check file dimensions
            return true;
        }


$('document').ready(function(event){

    var id = $('input[name="id"]').val()
    var customerStatus=$('input[name="active"]').val();
                $.ajax({
                    type: "get",
                    url : "{{route('managementCustomer.getLandingPageDetails')}}",
                    data: {
                        "customerid": id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        var prevTabId = null;
                        var prevWidget = null;
                        var widgetNum = 0;
                        $('#tabList').remove();
                        $('#tab').append('<ul class="list-group" style="padding-bottom: 2rem;white-space: nowrap;" id="tabList"></ul>');
                        if (customerStatus == 1) {
                            $('#newTabContainer').html('<input type="button" onclick="open_new_configuration();" value="Add New" class="btn btn-primary" style="float: right; margin-right: 1%;"/>');
                            $.each(data, function(tabKey, tabValue) {
                                if (prevTabId != tabValue.id) {
                                    prevTabId = tabValue.id;
                                    $('#tabList').append(
                                        '<li style="list-style-type: none;" class="custom-list-group-item" id=tabName'+prevTabId+'>'
                                        +'<span class="caret"></span>'+tabValue.tab_name
                                        @can(['Landing_page_edit'])
                                        +'<span onclick="edit_activeTab('+prevTabId+')"><a href="#" class="editActiveTab fa fa-toggle-on fa-2x" style="float: right; padding-right: 1rem;" value="'+tabValue.active+'"></a></span>'
                                        +'<span onclick="delete_tab('+tabValue.id+')"><a href="#" class="editTab fa fa-trash fa-lg landing_page_delete_icon" style="float: right; padding-right: 1rem;padding-top: 0.3em;" data-id="'+tabValue.id+'"></a></span>'
                                        +'<span onclick="edit_tab('+tabValue.id+')"><a href="#" class="editTab fa fa-pencil fa-lg" style="float: right; padding-right: 1rem;padding-top: 0.3em;" data-id="'+tabValue.id+'"></a></span>'
                                        @endcan
                                        +'</li>');

                                    if (tabValue.active == 0) {
                                        $('#tabList #tabName'+prevTabId+' a.editActiveTab').removeClass("fa-toggle-on fa-2x").addClass("fa-toggle-off fa-2x");
                                    }

                                    $('#tabName'+prevTabId).append('<ul class="nested" style="margin-top:3% !important;" id="nested'+prevTabId+'"></ul>');
                                }
                                $.each(tabValue.tabDetails, function(widgetkey, widgetvalue) {
                                    if (prevWidget != widgetkey) {
                                        widgetNum++;
                                        $('#nested'+prevTabId).append('<li class="custom-list-group-item nest1" id="nest'+widgetNum+'"><span class="caret"></span>'+widgetkey+'</li>');
                                        $('#nest'+widgetNum).append('<ul class="nested" id="colName'+widgetNum+'"></ul>');
                                        $('#colName'+widgetNum).append('<table class="table" id="table'+widgetNum+'">'
                                                                        +'<thead>'
                                                                        +'<tr>'
                                                                        +'<th scope="col">Filed Display Name</th>'
                                                                        +'<th scope="col"><center>Sort By<center></th>'
                                                                        +'</tr>'
                                                                        +'</thead>'
                                                                        +'<tbody>'
                                                                        +'</tbody>'
                                                                        +'</table>');
                                    }
                                    $.each(widgetvalue, function(key, value) {
                                        $.each(value, function(k,v){
                                            console.log(v);
                                            $('#table'+widgetNum).append('<tr>'
                                                                    +'<td >'+v.field_display_name+'</td>'
                                                                    +'<td class="text-center">'+[(v.default_sort == 1)? '<span class="fa fa-check" style="color:green;"></span>':'<span class="fa fa-times" style="color:red;"></span>']+'</td>'
                                                                    +'</tr>');

                                        });
                                    });
                                })
                            });
                        } else {
                            $('#newTabContainer').html('<h4 style="text-align: center; padding-bottom:6rem;">Please activate customer to edit landing page</h4>');
                        }

                    }//success function end
                });
            });

function edit_tab(tab_id) {
    $(".close").trigger('click');
    let url = "{{ route('landing_page.new_configuration_window',['tab_id' => ''])}}" + tab_id + '';
    window.open(url);
}

function delete_tab(tab_id) {
    swal({
             title: "Are you sure?",
            text: "You will not be able to undo this action",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, remove",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },function() {
            $.ajax({
                type: "POST",
                url: "{{route('landing_page.removeTab')}}",
                data: {'tabid': tab_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.status == "success") {
                        $('#tabName' + tab_id).remove();
                    }
                    swal(response.status_msg,response.msg, response.status);
                }
            });
    });
}

function edit_activeTab(prevTabId) {
   name = 'li'+'#tabName' + prevTabId + ' a.editActiveTab';
   status = document.querySelector(name).getAttribute("value");
   var customer_id = $('input[name="id"]').val()
   $.ajax({
            type: "POST",
            url: "{{route('landing_page.saveTabActiveStatus')}}",
            data: {
                'customerid': customer_id,
                'tabid': prevTabId,
                'status': (status==1)?0:1,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.status === "success") {
                    swal({
                        title: "Success",
                        text: response.msg,
                        type: response.status,
                        confirmButtonText: "OK"
                    });

                    if (status == 1) {
                    $(name).removeClass("fa-toggle-on fa-2x").addClass("fa-toggle-off fa-2x");
                    document.querySelector(name).setAttribute("value", "0");
                    } else {
                    $(name).removeClass("fa-toggle-off fa-2x").addClass("fa-toggle-on fa-2x");
                    document.querySelector(name).setAttribute("value", "1");
                }
                }else {
                    swal(response.status_msg, response.msg, response.status);
                }
            }
        });

}

function open_new_configuration() {
    $(".close").trigger('click');
    var customer_id = $('input[name="id"]').val();
    let url = "{{ route('landing_page.new_configuration_window',['customer_id' => ''])}}" + customer_id + '';
    window.open(url);
}

$('.editbutton6').on('click', function(e) {
$.fn.dataTable.ext.errMode = 'throw';
          var custid=$('input[name="id"]').val();
          var url = '{{ route("management.customerIncidentMappingList",":id") }}';
          var url = url.replace(':id', custid);

            prioritytable = $('#customer-incident-table').DataTable({
             bProcessing: false,
            responsive: true,

            processing: false,
            serverSide: true,
            responsive: true,
            ajax: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {
                    data: 'subject_with_trashed.subject',
                    name: 'subject_with_trashed.subject'
                },
                   {
                    data: 'category_with_trashed.name',
                    name: 'category_with_trashed.name'
                },

                {data: 'incident_response_time', name: 'incident_response_time',
                     render: function (incident_response_time) {return incident_response_time / 60 + ' Hour(s)'  }},
                {
                    data: 'incident_priority.value',
                    name: 'incident_priority.value'
                },
                {
                    data: 'sop',
                    name: 'sop'
                },


                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil " data-id=' + o.id + '></a>'
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o trash_icon" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
          });

/* Incident Mapping Tab - Start*/
$('[aria-controls="incidentSubjectTab"], #edit-priority').on('click', function(e){
           if(this.href !=null){
               var clicked =1;
           }
          var custid=$('input[name="id"]').val();
          var base_url = "{{route('management.customerIncidentPriorityCheck',':id')}}";
          var url = base_url.replace(':id', custid);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        console.log(data.status);
                      if(((clicked ==1)  && (data.status == 2)) || ((clicked ==null))){
                        $('#priorityModal').modal();
                        $('#priorityModal #priority-table tr').remove('.tr-priority');
                        var  priority_html= '';
                        $.each(data.response, function(key, value) {
                            var isLastElement = data.response.length -1;
                            if(key==0){
                                priority_html +='<tr class="tr-priority"><td>'+value.value+'</td><td class="nowrap">Less than or equal to</td><td><input type="number" class="form-control" style="display: inline;width:12%;" id="high" onchange="updateResponseTime(this)"  min="1"  value="'+value.response_time+'"  name="response_time[]" /><input type="hidden" value="'+value.priority_id+'" name="priority_id[]" /><input type="hidden" value="'+value.id+'" name="id[]" />&nbsp;Hours</td></tr>';

                            }else if(key == isLastElement){
                                priority_html +='<tr class="tr-priority"><td>'+value.value+'</td><td>Greater than</td><td><input type="number" class="form-control" style="display: inline;width:12%;" id="low" readonly min="1" value="'+value.response_time+'" name="response_time[]" /><input type="hidden" value="'+value.priority_id+'" name="priority_id[]" /><input type="hidden" value="'+value.id+'" name="id[]" />&nbsp;Hours</td></tr>';

                            }else{
                              //  priority_html +='<tr class="tr-priority"><td>'+value.value+'</td><td>Less than</td><td><input type="number" class="form-control" style="display: inline;width:10%;" id="medium" onchange="updateResponseTime(this)" min="1" value="'+value.response_time+'" name="response_time[]" /><input type="hidden" value="'+value.priority_id+'" name="priority_id[]" /><input type="hidden" value="'+value.id+'" name="id[]" />&nbsp;and Greater than <input type="number" class="form-control" style="display: inline;width:10%;" id="medium_range" disabled />&nbsp;</td></tr>';
                                priority_html +='<tr class="tr-priority"><td>'+value.value+'</td><td>Greater than </td><td><input type="number" class="form-control" style="display: inline;width:12%;" id="medium_range" disabled />&nbsp;Less than or equal to &nbsp;&nbsp;&nbsp;&nbsp;<input type="number" class="form-control" style="display: inline;width:12%;" id="medium" onchange="updateResponseTime(this)" min="1" value="'+value.response_time+'" name="response_time[]" /><input type="hidden" value="'+value.priority_id+'" name="priority_id[]" /><input type="hidden" value="'+value.id+'" name="id[]" /></td></tr>';

                            }

                        });
                        $('#priorityModal #priority-table').append(priority_html);
                        $('#high').trigger('onchange');
                      }
                    }
                });
        });


        $('#incident_response_time').on('input', function(e) {
           customer_details = {!! json_encode($single_customer_details); !!};
           customer_details.customer_priority.sort( function ( a, b ) { return  a.response_time - b.response_time; } );
           if(customer_details.customer_priority.length > 0){
            var res_hr = $('#incident-mapping-form input[name="incident_response_time"]').val();
            if(res_hr > 0){
            $.each(customer_details.customer_priority, function(key, value) {
             if((res_hr * 60 ) <= value.response_time ){
                $('#priority').val(value.priority.value);
                $('#priority_id').val(value.priority.id);
                return false;
             }else{
                $('#priority').val(value.priority.value);
                $('#priority_id').val(value.priority.id);
             }
            });
            }else{
                $('#priority').val('');
            }
           }else{
                 swal({title: "Alert", text: "Please set incident priority", type: "warning"} );
           }

        });

        $('#prioritySubmit').on('click', function(e) {
        var customer_id = $('input[name="id"]').val();
        e.preventDefault();
        var $form = $('#priority-form');
        var formData = new FormData($('#priority-form')[0]);
        formData.append('customer_id', customer_id);
        if(/\D/.test($('#high').val()) || /\D/.test($('#medium').val())){
            swal("Warning","Please add a valid response time","warning");
        }else if($('#high').val() =='' || $('#low').val() =='' || $('#medium').val() =='' ){
            swal("Warning", "Please add response time", "warning");
        }else if($('#medium').val() <= $('#high').val()){
            swal("Warning", "Medium priority reponse time should be grater than high priority reponse time", "warning");
        }else{
         $('.form-group').removeClass('has-error').find('.help-block').text('');
         url = "{{ route('management.customerIncidentPriorityStore') }}";
         $.ajax({
             headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                 'accept': 'application/json',
             },
             url: url,
             type: 'POST',
             data: formData,
             success: function (data) {
                 if (data.success) {
                     swal({title: "Saved", text: "Customer incident priority has been saved", type: "success"},
                         function () {
                             location.reload();
                         }
                     );
                 } else {
                     console.log(data.success);
                 }
             },
             fail: function (response) {
                console.log(response);
             },
             error: function (xhr, textStatus, thrownError) {
                 associate_errors(xhr.responseJSON.errors, $form, true);
             },
             contentType: false,
             processData: false,
         });
        }
       });


       $('#incidentSubjectMapping').on('click', function(e) {
        var id = $('input[name="sid"]').val();
        var customer_id = $('input[name="id"]').val();
        e.preventDefault();
        var $form = $('#incident-mapping-form');
        var formData = new FormData($('#incident-mapping-form')[0]);
        formData.append('id', id);
        formData.append('customer_id', customer_id);
         $('.form-group').removeClass('has-error').find('.help-block').text('');
         url = "{{ route('management.customerIncidentMappingStore') }}";
         $.ajax({
             headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                 'accept': 'application/json',
             },
             url: url,
             type: 'POST',
             data: formData,
             success: function (data) {
                 if (data.success) {
                     swal({title: "Saved", text: "Customer incident subject has been saved", type: "success"});
                     $('#incidentPriorityModal').modal('hide');
                     prioritytable.ajax.reload();
                 } else {
                     console.log(data.success);
                 }
             },
             fail: function (response) {
                console.log(response);
             },
             error: function (xhr, textStatus, thrownError) {
                 associate_errors(xhr.responseJSON.errors, $form, true);
             },
             contentType: false,
             processData: false,
         });

       });



       function updateResponseTime(result){
    if(result.id == 'medium'){
        $('#priorityModal #low').val(result.value);
    }else if(result.id == 'high'){
        $('#priorityModal #medium_range').val(result.value);
    }

}

    $('#add-incident-subject').click(function(){
        $("#incidentPriorityModal").modal();
        $('#incidentPriorityModal input[name="sid"]').val('');
        $('#incidentPriorityModal').find('input,select').val('').change();
        $('#incidentPriorityModal textarea[name="sop"]').val('');
        $('.select2').select2();
        $('#incidentPriorityModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
    });

    $("#customer-incident-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            console.log(id);
            var url = '{{ route("management.customerIncidentMappingSingle",":id") }}';
            var url = url.replace(':id', id);
            $('#incidentPriorityModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#incidentPriorityModal input[name="sid"]').val(data.id)
                        $('#incidentPriorityModal input[name="priority_id"]').val(data.priority_id)
                        $('#incidentPriorityModal select[name="subject_id"]').val(data.subject_id);
                        $('#incidentPriorityModal select[name="category_id"]').val(data.category_id);
                        $('#incidentPriorityModal input[name="incident_response_time"]').val(data.incident_response_time/60)
                        $('#incidentPriorityModal input[name="priority"]').val(data.incident_priority.value)
                        $('#incidentPriorityModal textarea[name="sop"]').val(data.sop)
                        $("#incidentPriorityModal").modal();
                        $('.select2').select2();
                        $('#incidentPriorityModal .modal-title').text("Edit Incident Subject")
                    } else {

                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });

        $('#customer-incident-table').on('click', '.delete', function (e) {
                var id = $(this).data('id');
                var base_url = "{{ route('management.customerIncidentMappingDestroy',':id') }}";
                var url = base_url.replace(':id', id);
                var message = 'Incident Subject deleted successfully';
                deleteRecord(url, prioritytable, message);
            });

        $('#incident-recipient').on('click', function(e){
            var custid= $('input[name="id"]').val();
            var base_url = "{{route('management.customerIncidentRecipientList',':id')}}";
            var url = base_url.replace(':id', custid);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {

                         let divParam = {
                           containerDiv: '#dynamic-rows',
                           addButton: '.add_button',
                           form: '#recipient-form',
                         };
                           let moreSteps = new MoreEl('step', divParam);
                           if(data.data.length!=0){
                           moreSteps.initElDiv(true);
                            }
                            else
                            {
                            moreSteps.initElDiv();
                            }
                            $("#recepientModal").modal();
                            for(let i = 0; i < data.data.length; i++) {
                            let emailSelector = 'input[name="email['+i+']"]';
                            let highSelector = 'input[name="high['+i+']"]';
                            let mediumSelector = 'input[name="medium['+i+']"]';
                            let lowSelector = 'input[name="low['+i+']"]';
                                let email = data.data[i].email;
                                let high = data.data[i].High?true:false;
                                let low = data.data[i].Low?true:false;
                                let medium = data.data[i].Medium?true:false;
                                let newSteps = moreSteps.addRow();
                                $(newSteps).find(emailSelector).val(email);
                                $(newSteps).find(highSelector).prop('checked', high);
                                $(newSteps).find(mediumSelector).prop('checked', medium);
                                $(newSteps).find(lowSelector).prop('checked', low);
                            }


                      }
                });
        });
        $('#recipientSubmit').on('click', function(e) {
        var customer_id = $('input[name="id"]').val();
        e.preventDefault();
        var $form = $('#recipient-form');
        var formData = new FormData($('#recipient-form')[0]);
        formData.append('customer_id', customer_id);
         $('.form-group').removeClass('has-error').find('.help-block').text('');
         url = "{{ route('management.customerIncidentRecipientStore') }}";
         $.ajax({
             headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                 'accept': 'application/json',
             },
             url: url,
             type: 'POST',
             data: formData,
             success: function (data) {
                 if (data.success) {
                     swal({title: "Saved", text: "Customer incident recipient has been saved", type: "success"},
                         function () {
                             location.reload();
                         }
                     );
                 } else {
                     console.log(data.success);
                 }
             },
             fail: function (response) {
                console.log(response);
             },
             error: function (xhr, textStatus, thrownError) {
                 associate_errors(xhr.responseJSON.errors, $form, true);
             },
             contentType: false,
             processData: false,
         });
       });


    $('#incident-recipient').click(function(){
        $("#recepientModal").modal();
        $('#recepientModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
    });


$('.save_qrcode').on('click', function(e) {
                location.reload();
            });

$(".inputclass1").hide();
$(".inputclass2").hide();
$(".inputclass3").hide();
$(".inputclass4").hide();
$(".inputclass5").hide();
$(".inputclass6").hide();
$(".inputclass7").hide();

$(".cancelbutton1").on("click",function(e){
    $(".inputclass1").hide();
    $(".user_tab").show();
});

$(".cancelbutton").on("click",function(e){

    $(".inputclass1").hide();
    $(".inputclass2").hide();
    $(".inputclass3").hide();
    $(".inputclass4").hide();
    $(".inputclass5").hide();
    $(".inputclass6").hide();
    $(".inputclass7").hide();
    $(".user_tab").show();
    $(".cpid_tab").show();
    $(".preference_tab_align").show();
    $('.new_pref_cls').show();
    $(".qrcode_tab").show();
    $(".landing_page_tab").show();
    $(".incident_sub_tab").show();
    $(".fence_tab").show();

       });


   $(".editbutton4").on("click",function(e){

   $(".inputclass4").show();
   $(".qrcode_tab").hide();

       })


        $("#basement_mode").on("click", function(event) {
        var isChecked = $('#basement_mode').is(':checked');
        if ($("#basement_mode").is(':checked')) {
            $(".basement_mode").show();
        } else {
            $(".basement_mode").hide();
            $('input[name="basement_interval"]').val("");
            $('input[name="basement_noofrounds"]').val("");
        }

    });

    $('#motion_sensor_incident_subject_id').select2();
        $('input[name="motion_sensor_enabled"]').on("click", function(event) {
            if (this.checked == true) {
                $("#motion_sensor_incident_subject").show();
                //$("#geo_fence_satellite").prop("checked", true);
            } else {
                $("#motion_sensor_incident_subject").hide();
                $("#motion_sensor_incident_subject_id").val(null).trigger('change');
            }
        });


       $(function() {

        $('.editbutton4').on('click', function(e) {
          $.fn.dataTable.ext.errMode = 'throw';
          var custid=$('input[name="id"]').val();

          console.log(custid);
          var url = '{{ route("management.qrcodeGetAll",":id") }}';
          var url = url.replace(':id', custid);

        var table = $('#qrcode-table').DataTable({
             bProcessing: false,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: 'qrcode',
                    name: 'qrcode'
                },
                   {
                    data: 'location',
                    name: 'location'
                },
                   {
                    data: 'no_of_attempts',
                    name: 'no_of_attempts'
                },
                {
                    data: 'no_of_attempts_week_ends',
                    name: 'no_of_attempts_week_ends'
                },
                {
                    data: 'tot_no_of_attempts_week_day',
                    name: 'tot_no_of_attempts_week_day'
                },
                {
                    data: 'tot_no_of_attempts_week_ends',
                    name: 'tot_no_of_attempts_week_ends'
                },

                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" data-qid=' + o.id + '></a>'
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o trash_icon" data-qid=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        }); });


        $("#qrcode-table").on("click", ".edit", function (e) {
            var table = $('#qrcode-table').DataTable();
            var qid = $(this).data('qid');
            var url = '{{ route("management.qrcodeSingle",":id") }}';
            var url = url.replace(':id', qid);
            $('#myModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#myModal').find('#qrcode_active').show();
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="qrcodeid"]').val(data.id)
                        $('#myModal input[name="customerids"]').val($('input[name="id"]').val());
                        $('#myModal input[name="qrcode"]').val(data.qrcode)
                        $('#myModal input[name="no_of_attempts"]').val(data.no_of_attempts)
                        $('#myModal input[name="no_of_attempts_week_ends"]').val(data.no_of_attempts_week_ends)
                        $('#myModal input[name="tot_no_of_attempts_week_day"]').val(data.tot_no_of_attempts_week_day)
                        $('#myModal input[name="tot_no_of_attempts_week_ends"]').val(data.tot_no_of_attempts_week_ends)
                        $('#myModal input[name="location"]').val(data.location)
                        $("#myModal #location_enabled").val(data.location_enable_disable).trigger('change');
                        $("#myModal #picture_enabled").val(data.picture_enable_disable).trigger('change');
                        // $("#myModal #qrcodeactive").val(data.qrcode_active).trigger('change');
                        $('#myModal').find('input[name="active"]').prop('checked',data.qrcode_active);
                        $("#myModal #picture_mandatory_id").val(data.picture_mandatory).trigger('change');
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit QR code: " + data.qrcode)
                    } else {

                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });

        $('.add-new').click(function(){
        $('#myModal').find('#qrcode_active').show();
        $('#myModal').find('input[name="active"]').prop('checked',true);
        var customer_id =$('input[name="id"]').val();
        $('#myModal input[name="customerids"]').val(customer_id);
        $("#myModal").modal();
        $('#myModal').find('input,select').val('').change();
        $('#myModal input[name="customerids"]').val($('input[name="id"]').val());
        $('#myModal').find('.form-group').removeClass('has-error').find('.help-block').text('');

        });

            $('#qrcode-table').on('click', '.delete', function (e) {
                var table = $('#qrcode-table').DataTable();
                var id = $(this).data('qid');
                var base_url = "{{ route('management.qrcodeDestroy',':id') }}";
                var url = base_url.replace(':id', id);
                var message = 'QR code location deleted successfully';
                deleteRecord(url, table, message);
            });

     $('#btnSubmit').on('click', function(e) {
        e.preventDefault();
        var $form = $(this).parents('form:first');;
        var qrcode = $("#qr_code").val();
        var  location = $("#locations").val();
        var  attemptWeekday = $("#attempts_weekday").val();
        var  attemptWeekend = $("#attempts_weekend").val();
        var  totalAttemptWeekday = $("#tot_attempts_weekday").val();
        var  totalAttemptWeekend = $("#tot_attempts_weekend").val();
        var  customerid =  $('#myModal input[name="customerids"]').val();
        var  qrcodeid =  $('#myModal input[name="qrcodeid"]').val();
        var location_enable_disable = $("#location_enabled").val();
        var  picture_enable_disable = $("#picture_enabled").val();
        var  picture_mandatory = $("#picture_mandatory_id").val();
        var active_status = $('#myModal input[name="active"]').prop("checked");
        var  qrcode_active = (active_status)? 1: 0;
        var table = $('#qrcode-table').DataTable();
       $.ajax({
                  url: "{{route('management.qrcodeStore')}}",
                        type: 'POST',
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                        data: {
                        'customerid':customerid,
                        'qrcode': qrcode,
                        'location': location,
                        'no_of_attempts': attemptWeekday,
                        'no_of_attempts_week_ends': attemptWeekend,
                        'tot_no_of_attempts_week_day': totalAttemptWeekday,
                        'tot_no_of_attempts_week_ends': totalAttemptWeekend,
                        'location_enable_disable': location_enable_disable,
                        'picture_enable_disable': picture_enable_disable,
                        'qrcode_active': qrcode_active,
                        'picture_mandatory':picture_mandatory,
                        'qrcodeid':qrcodeid
                    },
                success: function (data) {
                    if (data.success) {
                        $('#myModal').find('#qrcode_active').hide();
                        swal("Success", "QR code saved successfully", "success");
                            $('#myModal').modal('hide');
                            table.ajax.reload();
                    } else {
                           swal("Warning", "Something went wrong", "warning");

                    }
                },
                error: function (xhr, textStatus, thrownError) {
                     associate_errors(xhr.responseJSON.errors, $form,true);
                },
            });
    });
    $('#subjects').select2({  width: '100%' });
    });

function getval(sel, key) {

    if (sel.value == 1) {
        $('#picture_mandatory').show();
    } else {
        $('#picture_mandatory').hide();
    }
}

function deleteRecord(url, table, message) {
        var url = url;
        var table = table;
        swal({
            title: "Are you sure?",
            text: "You will not be able to undo this action. Proceed?",
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
                        swal("Deleted", message, "success");
                        // if (table) {
                            location.reload();
                        // }
                    }else if(data.success == false){
                        if(Object.prototype.hasOwnProperty.call(data,'message') && data.message){
                            swal("Warning", data.message, "warning");
                        }else{
                            swal("Warning", 'Data exists', "warning");
                        }
                    } else {
                        console.log(data);
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


$('input[name="mobile_security_patrol_site"]').on("click", function(event) {
            if (this.checked == true) {
                $("#geo_fence_satellite").show();
            } else {
                $("#geo_fence_satellite").hide();
                $('input[name="geo_fence_satellite"]').prop('checked', false);
            }
        });

       //To reset the hidden value in the form
       $('#myModal').on('hidden.bs.modal', function() {

           $('#interval_check').hide();
           $('#guard_tour_duration').hide();
           $('input:text[name="billing_address"]').prop('readonly', false);
       });


       $('#guard_tour').find('input').change(function() {
           if ($(this).is(":checked")) {
               $('#interval_check').show();
           } else {
               $('#interval_check').hide();
               $('#guard_tour_duration').hide();
               $('#interval_check').find('input').prop('checked', false);
               $('#duration').val('');
           }
       });
       $('#interval_check').find('input').change(function() {
           if ($(this).is(":checked")) {
               $('input[name="guard_tour_enabled"]').prop("checked", true);
               $('#guard_tour').show();
               $('#guard_tour_duration').show();
           } else {
               $('#guard_tour_duration').hide();
               $('#guard_tour_duration').find('input').prop('checked', false);
               $('#duration').val('');
           }
       });
       $('#overstay_enabled').find('input').change(function() {
           if ($(this).is(":checked")) {
               $('#overstay_time').show();

           } else {
               $('#overstay_time').hide();
               $('#overstay_timepicker').val('');

           }
       });

       $('#employee_rating_response').find('input').change(function() {
           if ($(this).is(":checked")) {
               $('#employee_rating_response_time').show();

           } else {
               $('#employee_rating_response_time').hide();
               $('#employee_rating_timepicker').val('');

           }
       });
        $('#qr_patrol_enabled').find('input').change(function() {
           if ($(this).is(":checked")) {
               $('#qr_picture_limit').show();
               $('#qr_interval_check').show();


           } else {
               $('#qr_picture_limit').hide();
               $('#qr_interval_check').hide();
               $('#qr_duration').hide();
                $('#pic_limit').val('');
               $('#qrduration').val('');
                $('#qr_interval_check').find('input').prop('checked', false);

           }
       });
         $('#qr_interval_check').find('input').change(function() {
           if ($(this).is(":checked")) {
               $('#qr_duration').show();

           } else {
               $('#qr_duration').hide();
               $('#qrduration').val('');

           }
       });
       $('#key_management_enabled').find('input').change(function() {
           if ($(this).is(":checked")) {

               $('#key_management_signature').show();
               $('#key_management_image_id').show();

           } else {
               $('#key_management_signature').hide();
               $('#key_management_image_id').hide();
           }
       });

       $('#requester_id').on('change', function() {
            var id = $(this).val();
            var requestorPosition={!!json_encode($requestorPosition)!!};
            var requestorEmpno={!!json_encode($requestorEmpno)!!};
            if ($(this).val() == '') {
                $('input:text[name="requester_position"]').val('');
                $('input:text[name="requester_empno"]').val('');
            }
            var reqPos=requestorPosition[id][0];
            var empNo=requestorEmpno[id];
            if( reqPos!=null && empNo!=null){
                $('input:text[name="requester_position"]').val(requestorPosition[id][0].position).prop('readonly', 'true');
                $('input:text[name="requester_empno"]').val(requestorEmpno[id]).prop('readonly', 'true');
            }else if(reqPos ==null && empNo!=null){
                $('input:text[name="requester_position"]').val("---").prop('readonly', 'true');
                $('input:text[name="requester_empno"]').val(requestorEmpno[id]).prop('readonly', 'true');
            }else if(reqPos !=null && empNo==null){
                $('input:text[name="requester_position"]').val(requestorPosition[id][0].position).prop('readonly', 'true');
                $('input:text[name="requester_empno"]').val("---").prop('readonly', 'true');
            }else{
                $('input:text[name="requester_position"]').val("---").prop('readonly', 'true');
                $('input:text[name="requester_empno"]').val("---").prop('readonly', 'true');
            }


            });


       function customerProfile(data){
        console.log(data[0].id);
        if (data) {

        $('input[name="id"]').val(data[0].id)
        $('input[name="customer_type"]').val("1")
        $('input[name="project_number"]').val(data[0].project_number)
        $('input[name="client_name"]').val(data[0].client_name)
        $('input[name="contact_person_name"]').val(data[0].contact_person_name)
        $('input[name="contact_person_email_id"]').val(data[0].contact_person_email_id)
        $('input[name="contact_person_phone"]').val(data[0].contact_person_phone)
        $('input[name="contact_person_phone_ext"]').val(data[0].contact_person_phone_ext)
        $('input[name="contact_person_cell_phone"]').val(data[0].contact_person_cell_phone)
        $('input[name="contact_person_position"]').val(data[0].contact_person_position)
        $('select[name="requester_name"]').val(data[0].requester_name);
        $('input[name="status"').val(data[0].status);
        $('input[name="city"]').val(data[0].city)
        $('input[name="postal_code"]').val(data[0].postal_code)
        $('input[name="province"]').val(data[0].province)
        $('input[name="address"]').val(data[0].address)
        $('textarea[name="description"]').val(data[0].description);
        $('input[name="proj_open"]').val(data[0].proj_open);
        $('input[name="proj_expiry"]').val(data[0].proj_expiry);
        $('input[name="arpurchase_order_no"]').val(data[0].arpurchase_order_no);
        $('input[name="arcust_type"]').val(data[0].arcust_type);
        $('select[name="industry_sector_lookup_id"]').val(data[0].industry_sector_lookup_id);
        $('select[name="region_lookup_id"]').val(data[0].region_lookup_id);
        $('select[name="region_lookup_id"]').trigger('change');
        $('input[name="billing_address"]').val(data[0].billing_address);
        $('input[name="same_address_check"]').prop("checked", false);
        $('select[name="requester_name"]').val(data[0].requester_name);
        $('select[name="master_customer"]').val(data[0].master_customer);
        $("select[name=master_customer").select2();
        $("select[name=industry_sector_lookup_id").select2();
        $("select[name=requester_name").select2();

        $('select[name="is_nmso_account"]').val(0);
        $('#security_clearance_lookup_id_div').css('display', 'none');
        if(data[0].stc_details != null) {
            if(data[0].stc_details.nmso_account == "yes") {
                $('#security_clearance_lookup_id_div').css('display', '');
                $('select[name="is_nmso_account"]').val(1);
                $('select[name="security_clearance_lookup_id"]').val(data[0].stc_details.security_clearance_lookup_id);
            }
        }

        if(data[0].stc) {
            $('select[name="stc"]').val(1);
        }else{
            $('select[name="stc"]').val(0);
        }

        var userId=$('select[name="requester_name"]').val();
        var requestorPosition={!!json_encode($requestorPosition)!!};
        var requestorEmpno={!!json_encode($requestorEmpno)!!};
        var reqPos=requestorPosition[userId][0];
        var empNo=requestorEmpno[userId];

            if( reqPos!=null && empNo!=null){
                $('input:text[name="requester_position"]').val(requestorPosition[userId][0].position).prop('readonly', 'true');
                $('input:text[name="requester_empno"]').val(requestorEmpno[userId]).prop('readonly', 'true');
            }else if(reqPos ==null && empNo!=null){
                $('input:text[name="requester_position"]').val("---").prop('readonly', 'true');
                $('input:text[name="requester_empno"]').val(requestorEmpno[userId]).prop('readonly', 'true');
            }else if(reqPos !=null && empNo==null){
                $('input:text[name="requester_position"]').val(requestorPosition[userId][0].position).prop('readonly', 'true');
                $('input:text[name="requester_empno"]').val("---").prop('readonly', 'true');
            }else{
                $('input:text[name="requester_position"]').val("---").prop('readonly', 'true');
                $('input:text[name="requester_empno"]').val("---").prop('readonly', 'true');
            }


        var full_address = data[0].address + ', ' + data[0].city + ', ' + data[0].province + ', ' + data[0].postal_code;

        if (data[0].incident_report_logo && data[0].incident_report_logo.length > 0) {
                            $('#incident-logo-section').show();
                            let baseName = data[0].incident_report_logo.split('/').reverse()[0];
                            $('#incident-logo-section .image-info').text(baseName)
                        }

        if (data[0].billing_address != null) {
            if (full_address.trim() === data[0].billing_address.trim()) {
                $('input[name="same_address_check"]').prop("checked", true);
            }
        }

        } else {
        console.log(data[0]);
        swal("Oops", "Could not save data", "warning");
        }
        }

       $(".editbutton1").on("click", function () {
           $(".inputclass1").show();
           $(".user_tab").hide();
           var id=$('input[name="id"]').val();
           var data={!!json_encode($customerData)!!};
           customerProfile(data);

       });

       $('#customer-profile-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var id=$('input[name="id"]').val();
            var requester_position= $('input[name="requester_position"]').val();
            var requester_empno= $('input[name="requester_empno"]').val();
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

            var formData = new FormData($('#customer-profile-form')[0]);
             formData.append('requester_position', requester_position);
             formData.append('requester_empno', requester_empno);
            var url= "{{route('management.customerProfileStore',':id')}}";
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
                          text: "Customer profile has been updated successfully",
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });

                    } else {
                        console.log(data);
                        swal("Oops", "Customer profile updation was unsuccessful", "warning");
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




        function customerCpid(data){
                // let _selCType = data.customer_type != null ? data.customer_type.id:null;
                // $('select[name="customer_type_id"] option[value="'+_selCType +'"]').prop('selected', true);

            if (data) {
                            $(".customer-cpid-allocation-table tbody").empty();
                            $.each(data.cpids, function(key, value) {
                                var customer_cpid_allocation_edit_row = '';
                                customer_cpid_allocation_edit_row = getCpidRow(key);
                                $(".customer-cpid-allocation-table tbody").append(customer_cpid_allocation_edit_row);
                                $('select[name="position_' + key + '"] option[value="' + value.position_id + '"]').prop('selected', true);
                                $('select[name="cpid_' + key + '"] option[value="' + value.cpid + '"]').prop('selected', true);
                                });

                            if (data.cpids.length >= 1) {
                                $('#remove-cpid-allocation').show();
                            }

                        } else {
                            console.log(data);
                            swal("Oops", "Could not save data", "warning");
                        }
         }

        $(".editbutton2").on("click", function () {
                $(".inputclass2").show();
                $(".cpid_tab").hide();
                var id=$('input[name="id"]').val();
                var data={!!json_encode($singleCustomer)!!};
                console.log(data);
                customerCpid(data);

            });


       /* CPID Allocation - Add - Start */

       $('#remove-cpid-allocation').hide();
        $("#add-cpid-allocation").on("click", function(e) {
            $last_row_no = $(".customer-cpid-allocation-table").find('tr:last .row-no').val();
            if ($last_row_no != undefined) {
                $next_row_no = ($last_row_no * 1) + 1;
            } else {
                $next_row_no = 0;
            }

            var customer_cpid_allocation_new_row = getCpidRow($next_row_no,true);
            $(".customer-cpid-allocation-table tbody").append(customer_cpid_allocation_new_row);
            $(".customer-cpid-allocation-table").find('tr:last').find('.row-no').val($next_row_no);

            $("#valid_until_" + $next_row_no + ">input").datepicker({
                format: "yyyy-mm-dd",
                maxDate: "+900y"
            });

            $(".datepicker").mask("9999-99-99");

            if ($last_row_no > 0 || $last_row_no == undefined) {
                $('#remove-cpid-allocation').show();
            }
        });
        /* CPID Allocation - Add - End */

        /* CPID Allocation - Remove - Start */
        $("#remove-cpid-allocation").on("click", function(e) {
            $last_row_no = $(".customer-cpid-allocation-table").find('tr:last .row-no').val();
            if ($last_row_no > -1) {
                $(".customer-cpid-allocation-table").find('tr:last').remove();
                if ($last_row_no == 0) {
                    $('#remove-cpid-allocation').hide();
                }
            } else {
                $('#remove-cpid-allocation').hide();
            }
        });
        /*CPID Allocation - Remove - End */

        $('#cpid_form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var id=$('input[name="id"]').val();
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

            var formData = new FormData($('#cpid_form')[0]);
            var url= "{{route('management.customerCpidStore',':id')}}";
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
                          text: "CPID details has been updated successfully",
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });

                    } else {
                        console.log(data);
                        swal("Oops", "CPID updation was unsuccessful", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },

                contentType: false,
                processData: false,

            });
        });

        function customerPreference(data){
        if (data) {

        if (data.show_in_sitedashboard) {
            $('input[name="show_in_sitedashboard"]').prop("checked", true);
        } else {
            $('input[name="show_in_sitedashboard"]').prop("checked", false);
        }
        if (data.guard_tour_enabled) {
            $('input[name="guard_tour_enabled"]').prop("checked", true);

        } else {
            $('input[name="guard_tour_enabled"]').prop("checked", false);
        }
        if (data.guard_tour_duration) {
            $('#guard_tour').show();
            $('#interval_check').show();
            $('input[name="guard_tour_enabled"]').prop("checked", true);
            $('input[name="interval_check"]').prop("checked", true);
            $('#guard_tour_duration').show();
            $('input[name="guard_tour_duration"]').val(data.guard_tour_duration);
        } else {
            $('input[name="interval_check"]').prop("checked", false);
            $('#interval_check').hide();
            $('#guard_tour_duration').hide();
        }
        if (data.basement_mode) {
            $('input[name="basement_mode"]').prop("checked", true);
            $('input[name="basement_interval"]').val(data.basement_interval);
            $('input[name="basement_noofrounds"]').val(data.basement_noofrounds);
            $(".basement_mode").show();
        } else {
            $('input[name="basement_mode"]').prop("checked", false);
            $(".basement_mode").hide();
        }
        if (data.shift_journal_enabled) {
            $('input[name="shift_journal_enabled"]').prop("checked", true);
        } else {
            $('input[name="shift_journal_enabled"]').prop("checked", false);
        }
        if (data.facility_booking) {
            $('input[name="facility_booking"]').prop("checked", true);
        } else {
            $('input[name="facility_booking"]').prop("checked", false);
        }
        customerStatus = data.active;
        if (data.active) {
            $('input[name="active"]').prop("checked", true);
        } else {
            $('input[name="active"]').prop("checked", false);
        }

        if (data.time_shift_enabled) {
            $('input[name="time_shift_enabled"]').prop("checked", true);
            // $('#time_shift_enabled').show();

        } else {
            $('input[name="time_shift_enabled"]').prop("checked", false);
            // $('#time_shift_enabled').hide();

        }
        if (data.overstay_enabled) {
            $('input[name="overstay_enabled"]').prop("checked", true);
            $('#overstay_time').show();
            $('input[name="overstay_time"]').val(data.overstay_time);
        } else {
            $('input[name="overstay_enabled"]').prop("checked", false);
            $('#overstay_time').hide();
            $('input[name="overstay_time"]').val(data.overstay_time);
        }

        if (data.employee_rating_response) {
            $('input[name="employee_rating_response"]').prop("checked", true);
            $('#employee_rating_response_time').show();
            $('input[name="employee_rating_response_time"]').val(data.employee_rating_response_time);
        } else {
            $('input[name="employee_rating_response"]').prop("checked", false);
            $('#employee_rating_response_time').hide();
            $('input[name="employee_rating_response_time"]').val(data.employee_rating_response_time);
        }
        if (data.qr_patrol_enabled) {
            $('input[name="qr_patrol_enabled"]').prop("checked", true);
            $('#qr_picture_limit').show();
            $('#qr_interval_check').show();
            $('input[name="qr_picture_limit"]').val(data.qr_picture_limit);

        } else {
            $('input[name="qr_patrol_enabled"]').prop("checked", false);
            $('#qr_picture_limit').hide();
            $('#qr_interval_check').hide();

        }
        if (data.qr_interval_check) {
            $('input[name="qr_interval_check"]').prop("checked", true);
            $('#qr_duration').show();
            $('input[name="qr_duration"]').val(data.qr_duration);
        } else {
            $('input[name="qr_interval_check"]').prop("checked", false);
            $('#qr_duration').hide();

        }
        if (data.time_sheet_approver_id) {
        $('select[name="time_sheet_approver_id"]').val(data.time_sheet_approver_id).trigger('change');
        $('#time_sheet_approver_email').show();
        }
        else{
        $('#time_sheet_approver_email').hide();
        }
        if (data.key_management_enabled) {
            $('input[name="key_management_enabled"]').prop("checked", true);
            $('#key_management_signature').show();
            $('#key_management_image_id').show();
            if(data.key_management_signature){
                $('input[name="key_management_signature"]').prop("checked", true);
            }else{
                $('input[name="key_management_signature"]').prop("checked", false);
            }
            if(data.key_management_image_id){
                $('input[name="key_management_image_id"]').prop("checked", true);
            }else{
                $('input[name="key_management_image_id"]').prop("checked", false);
            }

        } else {
            $('input[name="key_management_enabled"]').prop("checked", false);
            $('#key_management_signature').hide();
            $('#key_management_image_id').hide();

        }
        if (data.motion_sensor_enabled) {
                            $('input[name="motion_sensor_enabled"]').prop("checked", true);
                            $('#motion_sensor_incident_subject').show();
                            if(data.motion_sensor_incident_subject){
                                $('#motion_sensor_incident_subject_id')
                                    .val(data.motion_sensor_incident_subject).trigger('change');
                            }
                        } else {
                            $('input[name="motion_sensor_enabled"]').prop("checked", false);
                            $('#motion_sensor_incident_subject').hide();
                        }
        if (data.visitor_screening_enabled) {
                $('input[name="visitor_screening_enabled"]').prop("checked", true);
            } else {
                 $('input[name="visitor_screening_enabled"]').prop("checked", false);
            }

        if (data.geo_fence == 1) {
            $('input[name="geo_fence"]').trigger("click");
        }
        if (data.mobile_security_patrol_site == 1) {
            $('input[name="mobile_security_patrol_site"]').trigger("click");
        }
        if (data.geo_fence_satellite == 1) {
            $('input[name="geo_fence_satellite"]').prop("checked", true);
        } else {
            $('input[name="geo_fence_satellite"]').prop("checked", false);
        }

        } else {
        console.log(data);
        swal("Oops", "Could not save data", "warning");
        }
        }

        $(".editbutton3").on("click", function () {
            $(".inputclass3").show();
            $('.new_pref_cls').hide();
            $(".preference_tab_align").hide();
           var id=$('input[name="id"]').val();
           var data={!!json_encode($singleCustomer)!!};
           console.log(data);
           customerPreference(data);

       });


 $('#preference_form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var id=$('input[name="id"]').val();
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

            var formData = new FormData($('#preference_form')[0]);
            var url= "{{route('management.customerPreferenceStore',':id')}}";
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
                          text: "Preference details has been updated successfully",
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });

                    } else {
                        console.log(data);
                        swal("Oops", "Preference updation was unsuccessful", "warning");
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



$(".editbutton6").on("click", function () {
          $(".inputclass6").show();
          $(".incident_sub_tab").hide();

      });


      function customerFence(data){
        $('select[name="contractual_visit_unit"] option[value="'+data.contractual_visit_unit+'"]').prop('selected',true)
        $('input[name="fence_interval"]').val(data.fence_interval)
      }

      $(".editbutton7").on("click", function () {
           $(".inputclass7").show();
           $(".fence_tab").hide();
           var id=$('input[name="id"]').val();
           var data={!!json_encode($singleCustomer)!!};
           console.log(data);
           customerFence(data);

       });

       $('select[name="time_sheet_approver_id"]').select2();
            $('select[name="time_sheet_approver_id"]').on('change', function(e){
            $('#time_sheet_approver_email').show();
            url = "{{route('managementCustomer.allocatteduseremail',':userid')}}";
            var userId = $(this).val();
            // userid = $('#time_sheet_entry_notification_user_id').val();
            if($(this).val()){
                url = url.replace(':userid', userId);
            }
            $.ajax({
                url:url,
                method: 'GET',
                success: function (data) {
                    $('input:text[name="time_sheet_approver_email"]').val(data.email);
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
        });


$('#fence_save').on('click', function(e) {
        e.preventDefault();
         var id=$('input[name="id"]').val();
         var contractual_visit_unit=   $('select[name="contractual_visit_unit"] ').val();
         var fence_interval= $('input[name="fence_interval"]').val()

                  var url= "{{route('management.customerFenceStore',':id')}}";
                  var url = url.replace(':id', id);
                  $.ajax({
                        url: url,

                        type: 'POST',
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                        data: {
                        'contractual_visit_unit':contractual_visit_unit,
                        'fence_interval':fence_interval,
                         'id':id,
                    },
                success: function (data) {

                    if (data.success) {

                        $(".inputclass1").hide();
                        $(".inputclass2").hide();
                        $(".inputclass3").hide();
                        $(".inputclass4").hide();
                        $(".inputclass5").hide();
                        $(".inputclass6").hide();
                        $(".inputclass7").hide();
                        $(".user_tab").show();
                        $(".cpid_tab").show();
                        $(".preference_tab").show();
                        $(".qrcode_tab").show();
                        $(".landing_page_tab").show();
                        $(".incident_tab").show();
                        $(".fence_tab").show();
                        swal({
                          title: "Updated",
                          text: "Customer details has been updated successfully",
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

    $('#is_nmso_account').on('change', function(){
        if($(this).val() == 1) {
            $('#security_clearance_lookup_id_div').css('display', '');
        }else{
            $('#security_clearance_lookup_id_div').css('display', 'none');
        }
    });
</script>
<style>

.fa-check {
            color: green !important;
        }

span.fa-times {
            color: red !important;
        }
.candidate-screen a {
    background: white;
    border-radius: 5px;
    color: #ffffff;
    padding: 10px;
}

</style>
@stop
