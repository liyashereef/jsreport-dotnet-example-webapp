{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', ' Add New Customer')

@section('content_header')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<h3> @if(!empty($single_customer_details))
    Edit Customer: {{$single_customer_details->client_name}}
    @else
    Add New Customer
    @endif
</h3>

@stop
@section('content')
<div id="message"></div>
@if(Session::has('customer-updated'))
<div id="import-success-alert" class="alert alert-info fade in alert-dismissible" role="alert" style="width:50%;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
    {{ Session::get('customer-updated') }}
</div>
@endif
<div class="container-fluid container-wrap">
    <!-- Main content -->
    <section class="content">
        {{ Form::open(array('url'=>'customer/store','id'=>'customer-form', 'method'=> 'POST')) }}
        @if(!empty($single_customer_details))
        {{ Form::hidden('id', $single_customer_details->id, array('id' => 'id')) }}
        @endif
        <div class="form-group" id="active" style="padding-left: 92%; margin-top: 12px;">
            <label class="switch">
                <input type="checkbox" name="active" checked>
                <span class="slider round"></span>@lang('active')
            </label>
        </div>
        <!-- Tabs View - Start -->
        <div role="tabpanel">
            <!-- Nav tabs - Start -->
            <ul class="nav nav-tabs" role="tablist" id="userTabs">
                <li role="presentation" class="active show" id="user_tab"><a href="#userTab" aria-controls="userTab" role="tab" data-toggle="tab">Profile</a></li>
                <li role="presentation"><a href="#cpidTab" aria-controls="cpidTab" role="tab" data-toggle="tab">CPID Allocation</a></li>
                <li role="presentation"><a href="#configTab" aria-controls="configTab" role="tab" data-toggle="tab">Preferences</a></li>
                <li style="display:none" role="presentation"><a href="#fenceTab" aria-controls="fenceTab" role="tab" data-toggle="tab">Fences</a></li>
                <li role="presentation"><a href="#fenceTab" aria-controls="fenceTab" role="tab" data-toggle="tab">Fences</a></li>
                <li role="presentation" class="qrcodeTab"><a href="#qrcodeTab" aria-controls="qrcodeTab" role="tab" data-toggle="tab">QR Code</a></li>
                <li role="presentation" class="incidentSubjectTab"><a href="#incidentSubjectTab" aria-controls="incidentSubjectTab" role="tab" data-toggle="tab">Incident Mapping</a></li>
                <li role="presentation" class="customer-tab-li landingPageTab"><a href="#landingPage" aria-controls="landingPage" role="tab" data-toggle="tab">Landing Page</a></li>
                <li role="presentation" class="customer-tab-li tab-kpi"><a href="#kpi-page" aria-controls="kpi-page" role="tab" data-toggle="tab">KPI</a></li>
            </ul>
            <!-- Nav tabs - End -->

            <!-- Tab panes - Start -->
            <div class="tab-content tab-alignment">
                @include('admin::customer.partials.kpi.kpi-tab')

                <div role="tabpanel" class="tab-pane active" id="userTab">

                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="project_number">
                            <label for="project_number" class="control-label">@lang('Project Number <span class="mandatory">*</span>')</label>
                            {{ Form::text('project_number', null, array('class'=>'form-control project-number', 'placeholder'=>'Project Number')) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="client_name">
                            <label for="client_name" class="col-lg-4 control-label">@lang('Client Name <span class="mandatory">*</span>')</label>
                            {{ Form::text('client_name', null, array('class'=>'form-control','maxlength'=> 38,'placeholder'=>'Client Name')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group  col-xs-12 col-sm-12 col-md-6 col-lg-6" id="contact_person_name">
                            <label for="contact_person_name" class="col-lg-4 control-label">@lang('Contact Person Name')</label>
                            {{ Form::text('contact_person_name', null, array('class'=>'form-control', 'placeholder'=>'Contact Person Name')) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-6 col-lg-6" id="contact_person_email_id">
                            <label for="contact_person_email_id" class="control-label">@lang('Contact Person Email Id')</label>
                            {{ Form::email('contact_person_email_id', null, array('class'=>'form-control', 'placeholder'=>'Contact Person Email Id')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group  col-xs-12 col-sm-12 col-md-6 col-lg-6" id="contact_person_phone">
                            <label for="contact_person_phone" class="control-label">@lang('Contact Person Phone')</label>
                            {{ Form::text('contact_person_phone', null, array('class'=>'form-control phone', 'placeholder'=>'Contact Person Phone [ format (XXX)XXX-XXXX ]')) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-6 col-lg-6" id="contact_person_phone_ext">
                            <label for="contact_person_phone_ext" class="control-label">@lang('Ext.')</label>
                            {{ Form::text('contact_person_phone_ext', null, array('class'=>'form-control', 'placeholder'=>'Ext.','maxlength'=>255)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group  col-xs-12 col-sm-12 col-md-6 col-lg-6" id="contact_person_cell_phone">
                            <label for="contact_person_cell_phone" class="control-label">@lang('Contact Person Cell Phone')</label>
                            {{ Form::text('contact_person_cell_phone', null, array('class'=>'form-control phone', 'placeholder'=>'Contact Person Cell Phone [ format (XXX)XXX-XXXX ]')) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-6 col-lg-6" id="contact_person_position">
                            <label for="contact_person_position" class="control-label">@lang('Contact Person Position')</label>
                            {{ Form::text('contact_person_position', null, array('class'=>'form-control', 'placeholder'=>'Contact Person Position')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group  col-xs-12 col-sm-12 col-md-6 col-lg-6" id="address">
                            <label for="address" class="control-label">@lang('Address <span class="mandatory">*</span>')</label>
                            {{ Form::text('address', null, array('class'=>'form-control address_details', 'placeholder'=>'Address')) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-6 col-lg-6" id="city">
                            <label for="city" class="control-label">@lang('City <span class="mandatory">*</span>')</label>
                            {{ Form::text('city', null, array('class'=>'form-control address_details', 'placeholder'=>'City')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group  col-xs-12 col-sm-12 col-md-6 col-lg-6" id="province">
                            <label for="Province" class="control-label">@lang('Province <span class="mandatory">*</span>')</label>
                            {{ Form::text('province', null, array('class'=>'form-control address_details', 'placeholder'=>'Province')) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="postal_code">
                            <label for="postal_code" class="control-label">@lang('Postal Code <span class="mandatory">*</span>')</label>
                            {{ Form::text('postal_code', null, array('class'=>'postal-code form-control address_details','placeholder'=>'Postal Code')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" id="billing_address">
                            <label for="billing_address" class="col-lg-3 control-label">@lang('Billing Address <span class="mandatory">*</span>')</label>
                            <label for="same_address_check" class="control-label">@lang('Same as Site Address')</label>
                            {{ Form::checkbox('same_address_check',null,null, array('id'=>'check_same_address')) }}
                            {{ Form::text('billing_address', null, array('class'=>'form-control', 'placeholder'=>'Billing Address')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4" id="industry_sector_lookup_id">
                            <label for="industry_sector_lookup_id" class="col-lg-4 control-label">@lang('Industry Sector<span class="mandatory">*</span>')</label>
                            {{ Form::select('industry_sector_lookup_id',[null=>'Select']+$lookups['industrySectorLookup'], old('industry_sector_lookup_id'),array('class' => 'form-control')) }}
                            <small class="help-block"></small>
                        </div>

                        <div class="form-group col-xs-12 col-sm-12 col-md-8 col-lg-8" id="region_lookup_id">
                            <div class="row">
                                <label for="region_lookup_id" class="control-label col-sm-12">@lang('Region <span class="mandatory">*</span>')</label>
                                <div class="col-md-6 col-lg-6">
                                    {{ Form::select('region_lookup_id',[null=>'Select']+$lookups['regionLookup'], old('region_lookup_id'),array('class' => 'form-control')) }}
                                </div>

                                <div class="col-md-6 col-lg-6">
                                    <input class="region-description form-control col-md-4 col-lg-4" disabled />
                                </div>

                                <small class="help-block"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="description">
                            <label for="description" class="control-label">@lang('Description')</label>
                            {{ Form::textArea('description', null, array('class'=>'form-control', 'placeholder'=>'Description','rows'=>5)) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="proj_open">
                            <label for="proj_open" class="control-label">@lang('Project Open Date')</label>
                            {{ Form::text('proj_open', null, array('class'=>'form-control datepicker', 'placeholder'=>'Project Open Date (Y-m-d)')) }}
                            <small class="help-block"></small>
                        </div>
                        <!--Expiry column-->
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="proj_open">
                            <label for="proj_open" class="control-label">@lang('Project Expiry Date')</label>
                            {{ Form::text('proj_expiry', null, array('class'=>'form-control datepicker', 'placeholder'=>'Project Expiry Date (Y-m-d)')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="arpurchase_order_no">
                            <label for="arpurchase_order_no" class="control-label">@lang('AR Purchase Order Number')</label>
                            {{ Form::text('arpurchase_order_no', null, array('class'=>'form-control', 'placeholder'=>'AR Purchase Order Number')) }}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="arcust_type">
                            <label for="arcust_type" class="control-label">@lang('AR Customer Type')</label>
                            {{ Form::text('arcust_type', null, array('class'=>'form-control', 'placeholder'=>'AR Customer Type')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="requester_name">
                            <label for="requester_name" class="control-label">@lang('Requestor Name <span class="mandatory">*</span>')</label>
                            {{Form::select('requester_name',$lookups['requesterLookup'], old('requester_name'),['id'=>'requester_id', 'class' => 'form-control', 'placeholder' => 'Please Select','style'=>'width: 100%'])}}
                            <small class="help-block"></small>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="requester_position">
                            <label for="requester_position" class="control-label">@lang('Requestor Position')</label>
                            {{ Form::text('requester_position', null, array('class'=>'form-control', 'placeholder'=>'Requestor Position','readonly'=>true,'disabled'=>true)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" id="requester_empno">
                            <label for="requester_empno" class="control-label">@lang('Requestor Employee Number')</label>
                            {{ Form::text('requester_empno', null, array('class'=>'form-control', 'placeholder'=>'Requestor Employee Number','readonly'=>true,'disabled'=>true)) }}
                            <small class="help-block"></small>
                        </div>
                        <!-- <div class="form-group">
                            </div> -->
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <label for="master_customer" class="control-label">@lang('Master Customer')</label>
                            <select name="master_customer" id="master_customer" style="width: 100%;" class="form-control">
                                <option value=0 selected>Select All</option>
                                @foreach($lookups['parentcustomerLookup'] as $key=>$value)
                                <option value={{$key}}>{{$value}}</option>
                                @endforeach
                            </select>

                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group  col-lg-12" style="margin:0px;" id="incident_report_logo">
                            <div class="col-md-2">
                                <label for="guard_tour_duration" class="control-label">@lang('Incident Report Logo')</label>
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
                            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2" id="stc">
                                <label for="stc" class="control-label">@lang('Is this a STC Customer')</label>
                                {{ Form::checkbox('stc', 1) }}
                                <small class="help-block"></small>
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2" id="is_nmso_account">
                                <label for="is_nmso_account" class="control-label">@lang('Is this a NMSO Account')</label>
                                {{ Form::checkbox('is_nmso_account', 1) }}
                                <small class="help-block"></small>
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4" id="security_clearance_lookup_id" style="display: none;">
                                {{ Form::select('security_clearance_lookup_id',[null=>'Security clearance required for this post']+$lookups['securityClearanceLookup'], isset($customer_stc_details) ? old('security_clearance_lookup_id',$customer_stc_details->security_clearance_lookup_id) : null, array('class' => 'form-control')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="cpidTab">
                    <div class="col-sm-12 table-responsive pop-in-table" id="customer-cpid-allocation">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="customer_type_id" class="control-label">Site Type</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="customer_type_id">
                                    {{Form::select('customer_type_id',$customerTypes,null,['class' => 'form-control','placeholder' => 'Choose Site Type','id'=>'customer_type','style'=>'width:100%;'])}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                        </div>

                        <table class="table table-bordered customer-cpid-allocation-table">
                            <thead>
                                <tr>
                                    <th>CPID</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="form-group col-sm-12 cpid-div">
                        <label for="add-cpid-allocation" id="add-cpid-allocation" class="col-sm-1 btn btn-primary" style="margin-right:1%;">+</label>
                        <label for="remove-cpid-allocation" id="remove-cpid-allocation" class="col-sm-1 btn btn-primary">-</label>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="configTab">
                    <div class="form-group" id="show_in_sitedashboard">
                        <label for="show_in_sitedashboard" class="col-lg-4 control-label">@lang('Show in Site Dashboard')</label>
                        {{ Form::checkbox('show_in_sitedashboard', 1,'checked') }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="facility_booking">
                        <label for="facility_booking" class="col-lg-4 control-label">@lang('Facility Booking')</label>
                        {{ Form::checkbox('facility_booking', 1) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="shift_journal">
                        <label for="shift_journal" class="col-lg-4 control-label">@lang('Shift Journal')</label>
                        {{ Form::checkbox('shift_journal_enabled', 1) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="time_shift_enabled">
                        <label for="time_shift_enabled" class="col-lg-4 control-label">@lang('Enable Time shift')</label>
                        {{ Form::checkbox('time_shift_enabled', 1) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="guard_tour">
                        <label for="guard_tour" class="col-lg-4 control-label">@lang('Guard Tour')</label>
                        {{ Form::checkbox('guard_tour_enabled', 1) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="interval_check" style="display:none">
                        <label for="interval_check" class="col-lg-4 control-label">@lang('Interval Check-in Required')</label>
                        {{ Form::checkbox('interval_check', 1) }}
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group" id="guard_tour_duration" style="display:none">
                        <div class="col-lg-4">
                            <label for="guard_tour_duration" class="control-label">@lang('Duration to set the interval (Hours)')</label>
                        </div>
                        <div class="col-lg-8">
                            {{ Form::text('guard_tour_duration', null, array('class'=>'form-control','placeholder'=>'Duration (Hours)', 'id' => 'duration')) }}
                        </div>
                        <div class="col-lg-12">
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="overstay_enabled">
                        <label for="overstay_enabled" class="col-lg-4 control-label">@lang('Overtime Enabled')</label>
                        {{ Form::checkbox('overstay_enabled', 1) }}
                        <small class="help-block"></small>
                    </div>


                    <div class="form-group" id="overstay_time" style="display:none">
                        <div class="col-lg-4">
                            <label for="overstay_time" class="control-label">@lang('Overstay Time')</label>
                        </div>
                        <div class="col-lg-8">
                            {{ Form::text('overstay_time', null, array('class'=>'form-control','placeholder'=>'Duration', 'id' => 'timepicker')) }}
                        </div>
                        <div class="col-lg-12">
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="basement_mode" class="col-lg-4 control-label">@lang('Basement Mode')</label>
                        <input type="checkbox" id="basement_mode" name="basement_mode" />
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group basement_mode" id="basement_interval" style="display:none">
                        <div class="col-lg-4">
                            <label for="basement_interval" class="control-label">@lang('Basement Interval')</label>
                        </div>
                        <div class="col-lg-8">
                            {{ Form::text('basement_interval', null, array('class'=>'form-control binterval','placeholder'=>'00:00', 'id' => 'basement_interval')) }}
                        </div>
                        <div class="col-lg-12">
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group basement_mode" id="basement_noofrounds" style="display:none">
                        <div class="col-lg-4">
                            <label for="basement_noofrounds" class="control-label">@lang('No of Rounds')</label>
                        </div>
                        <div class="col-lg-8">
                            <input type="number" id="basement_noofrounds" name="basement_noofrounds" class="form-control" placeholder='No of Rounds' />

                        </div>
                        <div class="col-lg-12">
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="geo_fence">
                        <label for="geo_fence" class="col-lg-4 control-label">@lang('Geo Fence')</label>
                        {{ Form::checkbox('geo_fence', 1,'') }}
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group" id="mobile_security_patrol_site">
                        <label for="mobile_security_patrol_site" class="col-lg-4 control-label">@lang('Mobile Security Patrol Site')</label>
                        {{ Form::checkbox('mobile_security_patrol_site', 0,'') }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="geo_fence_satellite" style="display:none">
                        <label for="geo_fence_satellite" class="col-lg-4 control-label">@lang('Geo Fence Satellite Tracking')</label>
                        {{ Form::checkbox('geo_fence_satellite', 1,'') }}
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group" id="employee_rating_response">
                        <label for="employee_rating_response" class="col-lg-4 control-label">@lang('Enable employee response for rating')</label>
                        {{ Form::checkbox('employee_rating_response', 1) }}
                        <small class="help-block"></small>
                    </div>


                    <div class="form-group" id="employee_rating_response_time" style="display:none">
                        <div class="col-lg-4">
                            <label for="employee_rating_response_time" class="control-label">@lang('Response Time (Days)')</label>
                        </div>
                        <div class="col-lg-8">
                            {{ Form::number('employee_rating_response_time', null, array('class'=>'form-control','placeholder'=>'Duration (Days)','min'=>1, 'id' => 'timepicker')) }}
                        </div>
                        <div class="col-lg-12">
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="qr_patrol_enabled">
                        <label for="qr_patrol_enabled" class="col-lg-4 control-label">@lang('Enable QR Patrol')</label>
                        {{ Form::checkbox('qr_patrol_enabled', 1) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="qr_picture_limit" style="display:none">
                        <div class="col-lg-4">
                            <label for="qr_picture_limit" class="control-label">@lang('Picture Limit')</label>
                        </div>
                        <div class="col-lg-8">
                            {{ Form::number('qr_picture_limit', null, array('class'=>'form-control','placeholder'=>'Maximum Pictures Count','min'=>1, 'id' => 'pic_limit')) }}
                        </div>
                        <div class="col-lg-12">
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="qr_interval_check" style="display:none">
                        <label for="qr_interval_check" class="col-lg-4 control-label">&nbsp;&nbsp;@lang('QR Interval Check-in')</label>
                        {{ Form::checkbox('qr_interval_check', 1) }}
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group" id="qr_duration" style="display:none">
                        <div class="col-lg-4">
                            <label for="qr_duration" class="control-label">@lang('Duration to set the interval (Minutes)')</label>
                        </div>
                        <div class="col-lg-8">
                            {{ Form::text('qr_duration', null, array('class'=>'form-control','placeholder'=>'Duration (Minutes)', 'id' => 'qrduration')) }}
                        </div>
                        <div class="col-lg-12">
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="qr_daily_activity_report" style="display:none;">
                        <label for="qr_daily_activity_report" class="col-lg-4 control-label">&nbsp;&nbsp;@lang('Daily Activity Report')</label>
                        {{ Form::checkbox('qr_daily_activity_report', 1) }}
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group" id="qr_recipient_email" style="display:none;padding-left:8px;">
                        <div class="col-lg-4">
                            <label for="qr_recipient_email" class="control-label">@lang('Email Id (Separated by comma)')</label>
                        </div>
                        <div class="col-lg-8">
                            {{ Form::text('qr_recipient_email', null, array('class'=>'form-control','placeholder'=>'Email Id(Separated by comma)','title'=>'', 'id' => 'qr_recipient')) }}
                        </div>
                        <div class="col-lg-12">
                            <small class="help-block"></small>
                        </div>
                    </div>
                    
                    
                    <div class="form-group" id="key_management_enabled">
                        <label for="key_management_enabled" class="col-lg-4 control-label">@lang('Key Management')</label>
                        {{ Form::checkbox('key_management_enabled', 1) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="key_management_signature" style="display:none">
                        <label for="key_management_signature" class="col-lg-4 control-label">&nbsp;&nbsp;@lang('Enable Signature')</label>
                        {{ Form::checkbox('key_management_signature', 1) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="key_management_image_id" style="display:none">
                        <label for="key_management_image_id" class="col-lg-4 control-label">&nbsp;&nbsp;@lang('Enable Image For ID')</label>
                        {{ Form::checkbox('key_management_image_id', 1) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="motion_sensor_enabled">
                        <label for="motion_sensor_enabled" class="col-lg-4 control-label">@lang('Enable Motion Sensor')</label>
                        {{ Form::checkbox('motion_sensor_enabled', 1) }}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="motion_sensor_incident_subject" style="display:none">
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

                    <div class="form-group" id="recruiting_match_score_for_sending_mail">
                            <div class="col-lg-4">
                                <label  class="control-label">@lang('Recruiting match score for sending mail')</label></div>
                            <div class="col-lg-8">
                                {{ Form::number('recruiting_match_score_for_sending_mail', null, array('class'=>'form-control','placeholder'=>'Match Score', 'step'=>0.01)) }}
                            </div>
                            <div class="col-lg-12">
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group" id="rec_onboarding_threshold_days">
                            <div class="col-lg-4">
                                <label for="rec_onboarding_threshold_days" class="control-label">@lang('Recruiting Onboarding Threshold (Days)')</label></div>
                            <div class="col-lg-8">
                                {{ Form::number('rec_onboarding_threshold_days', null, array('class'=>'form-control','placeholder'=>'Duration (Days)','min'=>1)) }}
                            </div>
                            <div class="col-lg-12">
                                <small class="help-block"></small>
                            </div>
                        </div>
                           {{--  <div class="col-lg-12"> --}}
                        <div class="form-group" id="time_sheet_approver_id">
                             <div class="col-lg-4">
                            <label for="time_sheet_approver_id" class="control-label">@lang('Timesheet Approver')</label>
                        </div>
                         <div class="col-lg-8">
                            {{ Form::select(
                                'time_sheet_approver_id',
                                 [null=>'Please Select'] + $customerAllocattedUsers,
                                 null,
                                 array('class'=>'form-control', 'id' => 'time_sheet_approver_id')
                          ) }}
                      </div>
                             <div class="col-lg-12">
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group" id="time_sheet_approver_email" style="display:none">
                            <div class="col-lg-4" style="padding-left: 28px">
                                <label for="time_sheet_approver_email" class="control-label">@lang('Timesheet Approver Email')</label>
                            </div>
                            <div class="col-lg-8">
                                {{ Form::text('time_sheet_approver_email', null, array('class'=>'form-control','placeholder'=>'Email', 'id' => 'time_sheet_approver_email','readonly'=>true,'disabled'=>true)) }}
                                <small class="help-block"></small>
                            </div>
                        </div>

                    <div class="form-group" id="visitor_screening_enabled">
                        <label for="visitor_screening_enabled" class="col-lg-4 control-label">@lang('Visitor Screening')</label>
                        {{ Form::checkbox('visitor_screening_enabled', 1) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="fenceTab">
                    {{-- <div class="row">
                            <div class="col-sm-12 table-responsive "></div>
                    </div> --}}
                    <div class="form-group" id="fence_interval">
                        <div class="col-md-2">
                            <label for="fence_interval"> Fence Interval (Minutes)</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="fence_interval" id="fence_interval">
                            <small class="help-block"></small>
                        </div>
                    </div>


                    <div class="form-group" id="contractual_visit_unit">
                        <div class="col-md-2">
                            <label for="contractual_visit_unit"> Contractual Visit Unit </label>
                        </div>
                        <div class="col-md-4">
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

                    <div class="row" id="fencerowbottom">
                    </div>
                    <div class="col-sm-12 table-responsive "></div>
                </div>

                <div role="tabpanel" class="tab-pane" id="qrcodeTab">
                    <div class="add-new" id="add-qrcode" data-title="Add New QR Code">Add
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
                    <div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel"></h4>
                                </div>
                                {{ Form::open(array('url'=>'#','id'=>'qrcode-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                                {{ Form::hidden('qrcodeid', null) }}
                                {{ Form::hidden('customerids', null) }}
                                <div class="modal-body">

                                    <div class="form-group col-sm-12" id="qrcode_active" style="display: none;">
                                        <label class="switch" style="float:right;">
                                            <input name="qrcode_active" type="checkbox">
                                            <span class="slider round"></span>
                                        </label>
                                        <label style="float:right;padding-right: 5px;">Active</label>
                                    </div>

                                    <div class="row">
                                        <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode'>
                                            <!-- <input type='text' name='qr-location-row' class='row-no' value='" + next_row + "'> -->
                                            <label for='qrcode' class='control-label'>QR Code <span class='mandatory'>*</span></label>
                                            <input class='form-control qrcode' placeholder='QR Code' name='qrcode' id='qr_code' type='text'>
                                            <small class='help-block'></small>
                                        </div>
                                        <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location'>
                                            <label for='location' class='control-label'>Checkpoint<span class='mandatory'>*</span></label>
                                            <input class='form-control location' placeholder='Checkpoint' name='location' id='locations' type='text'>
                                            <small class='help-block'></small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='no_of_attempts'>
                                            <label for='no_of_attempts' class='control-label'>No of attempts per person per shift (weekday) <span class='mandatory'>*</span></label>
                                            <input class='form-control no_of_attempts' id='attempts_week_day' placeholder='Number of Attempts' name='no_of_attempts' type='text'>
                                            <small class='help-block'></small>
                                        </div>
                                        <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='no_of_attempts_week_ends'>
                                            <label for='no_of_attempts_week_ends' class='control-label'>No of attempts per person per shift (weekend) <span class='mandatory'>*</span></label>
                                            <input class='form-control no_of_attempts_week_ends' id='attempts_week_ends' placeholder='Number of Attempts' name='no_of_attempts_week_ends' type='text'>
                                            <small class='help-block'></small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='tot_no_of_attempts_week_day'>
                                            <label for='tot_no_of_attempts_week_day' class='control-label'>Total no of attempts (weekday) <span class='mandatory'>*</span></label>
                                            <input class='form-control tot_no_of_attempts_week_day' id='tot_attempts_week_day' placeholder='Number of Attempts' name='tot_no_of_attempts_week_day' type='text'>
                                            <small class='help-block'></small>
                                        </div>

                                        <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='tot_no_of_attempts_week_ends'>
                                            <label for='tot_no_of_attempts_week_ends' class='control-label'>Total no of attempts (weekend) <span class='mandatory'>*</span></label>
                                            <input class='form-control tot_no_of_attempts_week_ends' id='tot_attempts_week_ends' placeholder='Number of Attempts' name='tot_no_of_attempts_week_ends' type='text'>
                                            <small class='help-block'></small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='picture_enable_disable'>
                                            <label for='picture_enable_disable' class='control-label'>Enable/Disable Picture<span class='mandatory'>*</span></label>
                                            <select class='form-control' name='picture_enable_disable' id='picture_enabled' onchange='getval(this,0);'>
                                                <option value='' selected='selected'>Select</option>
                                                <option value='1'>Enable</option>
                                                <option value='0'>Disable</option>
                                            </select>
                                            <small class='help-block'></small>
                                        </div>
                                        <div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='picture_mandatory'>
                                            <label for='picture_mandatory' class='control-label'>Picture Mandatory<span class='mandatory'>*</span></label>
                                            <select class='form-control' name='picture_mandatory' id='picture_mandatory_id'>
                                                <option value='' selected='selected'>Select</option>
                                                <option value='1'>Yes</option>
                                                <option value='0'>No</option>
                                            </select>
                                            <small class='help-block'></small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class='form-group col-xs-12 col-sm-12 col-md-12 col-lg-12' id='location_enable_disable'>
                                            <label for='location_enable_disable' class='control-label'>Enable/Disable Location<span class='mandatory'>*</span></label>
                                            <select class='form-control' id='location_enabled' name='location_enable_disable'>
                                                <option value='' selected='selected'>Select</option>
                                                <option value='1'>Enable</option>
                                                <option value='0'>Disable</option>
                                            </select>
                                            <small class='help-block'></small>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    {{ Form::button('Save', array('class'=>'button btn btn-primary blue','id'=>'btnSubmit'))}}
                                    {{ Form::button('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    <!--  <div class="col-sm-12 table-responsive pop-in-table" id="customer-qrcode-location">
                             <ul id="fieldList" class="customer-qrcode-location-list">
                             </ul>
                         </div>
                         <div class="form-group col-sm-12 qrcode-location-div">
                             <label for="add-qrcode-location" id="addMore" class="col-sm-1 btn btn-primary" style="margin-right:1%;">+</label>
                             <label for="remove-qrcode-location" id="remove-qrcode-location" class="col-sm-1 btn btn-primary">-</label>
                         </div> -->
                </div>

                <!-- START-- Incident Mapping Tab -->
                <div role="tabpanel" class="tab-pane" id="incidentSubjectTab">
                    <div class="add-button" id="add-incident-subject">Add New</div>
                    <div class="add-button" id="edit-priority">Set Incident Priority </div>
                    <div class="add-button" id="incident-recipient">Incident Recipients </div>
                    <table class="table table-bordered" id="customer-incident-table" style="width: 100%">
                        <thead>
                            <tr>
                                <th width="25%">Subject</th>
                                <th width="25%">Category</th>
                                <th width="12%">Response Time</th>
                                <th width="8%">Priority</th>
                                <th width="25%">SOP</th>
                                <th width="10%">Actions</th>

                            </tr>
                        </thead>
                    </table>
                    <br>
                    {{-- <div class="row" id="fencenew" style="display:none;text-align:center">
                        </div>

                        <div class="row" id="fencerowbottom">
                        </div>--}}
                    <div class="col-sm-12 table-responsive "></div>
                </div>

                <div class="modal fade" id="recepientModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Incident Recipient</h4>
                            </div>
                            {{ Form::open(array('url'=>'#','id'=>'recipient-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                            {{ Form::hidden('pid', null) }}
                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-sm-3"><strong>Email</strong></div>
                                    <div class="col-sm-2 check"><strong>High</strong></div>
                                    <div class="col-sm-2 check"><strong>Medium</strong></div>
                                    <div class="col-sm-2 check"><strong>Low</strong></div>
                                     <div class="col-sm-2 check"><strong>Amendment</strong></div>
                                </div>
                                <div id="dynamic-rows">
                                </div>
                            </div>

                            <div class="modal-footer">
                                {{ Form::button('Save', array('class'=>'button btn btn-primary blue','id'=>'recipientSubmit'))}}
                                {{ Form::button('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                            </div>
                            {{ Form::close() }}


                        </div>
                    </div>
                </div>
                <div class="modal fade" id="priorityModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Set Incident Priority</h4>
                            </div>
                            {{ Form::open(array('url'=>'#','id'=>'priority-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                            {{ Form::hidden('pid', null) }}
                            <div class="modal-body">
                                <table class="table" id="priority-table">
                                    <tr>
                                        <td><strong>Priority</strong></td>
                                        <td colspan="2"><strong>Response Range (in Hrs)</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-footer">
                                {{ Form::button('Save', array('class'=>'button btn btn-primary blue','id'=>'prioritySubmit'))}}
                                {{ Form::button('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="incidentPriorityModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Incident Mapping</h4>
                            </div>
                            {{ Form::open(array('url'=>'#','id'=>'incident-mapping-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                            {{ Form::hidden('sid', null) }}
                            {{ Form::hidden('priority_id',null, array('id' => 'priority_id')) }}
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group" id="subject_id">
                                        <label for="subject" class="col-sm-3 control-label">Subject <span class="mandatory">*</span></label>
                                        <div class="col-sm-6">
                                            {{ Form::select('subject_id',[null=>'Select Subject']+$lookups['subject'], old('subject_id'),array('class' => 'form-control select2', 'style'=>"width: 100%;")) }}
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                    <div class="form-group" id="category_id">
                                        <label for="category" class="col-sm-3 control-label">Category <span class="mandatory">*</span></label>
                                        <div class="col-sm-6">
                                            {{ Form::select('category_id',[null=>'Select Category']+$lookups['incidentCategory'], old('category_id'),array('class' => 'form-control select2', 'style'=>"width: 100%;")) }}
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                    <div class="form-group" id="incident_response_time">
                                        <label for="name" class="col-sm-3 control-label">Response Time <span class="mandatory">*</span></label>
                                        <div class="col-sm-2">
                                            {{-- <input type="time" class="form-control" name="eta"> --}}
                                            <input class='form-control' placeholder='Hour' name='incident_response_time' min="1" id='incident_response_time' type='number'>
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="col-sm-3 control-label">Priority</label>
                                        <div class="col-sm-2">
                                            <input class='form-control' readonly name='priority' id='priority' type='text'>
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                    <div class="form-group" id="sop">
                                        <label for="name" class="col-sm-3 control-label">SOP <span class="mandatory">*</span></label>
                                        <div class="col-sm-6">
                                            {{ Form::textArea('sop', null, array('class'=>'form-control', 'placeholder'=>'Please enter SOP details','rows'=>8)) }}
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                {{ Form::button('Save', array('class'=>'button btn btn-primary blue','id'=>'incidentSubjectMapping'))}}
                                {{ Form::button('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <!-- END-- Incident Mapping Tab -->
                <!-- START-- Landing Page Tab -->
                <div role="tabpanel" class="tab-pane" id="landingPage">
                    <div class="container-fluid" id="newTabContainer">
                        <input type="button" onclick="open_new_configuration();" value="Add New" class="btn btn-primary" style="float: right; margin-right: 1%;" />
                    </div>

                    <div class="row">
                        <div class="col-md-8" id="tab"></div>
                    </div>
                </div>
                <!-- END-- Landing Page Tab -->
            </div>


            <div class="customer-form-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::reset('Cancel', array('class'=>'btn btn-primary blue','id'=>'form_cancel'))}}
            </div>


        </div>

</div>


{{ Form::close() }}
</section>

</div>
@include('admin::customer.partials.moreSteps')
@stop
@section('js')
<style>
    ul li {
        list-style: none;
    }

    hr {
        border: none;
        height: 10px;
        /* Set the hr color */
        color: #333;
        /* old IE */
        background-color: #333;
        /* Modern Browsers */
    }

    .cpid-div {
        display: block;
        margin-left: 0px;
        padding-left: 0px;

    }

    .cpid-div label {
        width: 60px;
        margin-left: 0px;
        margin-right: 1%;

    }

    #customer-cpid-allocation {
        margin-left: 0px;
        padding-left: 0px;
    }

    #customer-cpid-allocation select {
        width: 35%;

    }

    #configTab {
        width: 1505px;
        padding-bottom: 10px;
        margin-left: -9px;
        padding-left: 0px;
    }

    #configTab .form-control {
        width: 225px;
    }

    #guard_tour_duration,
    #overstay_time,
    #basement_interval,
    #basement_noofrounds,
    #employee_rating_response_time,
    #qr_picture_limit,
    #qr_duration {
        margin-left: 8px;
    }

    #configTab .timepicker_wrap {
        width: 308px !important;
    }

    #fenceTab {
        width: 838px;
        margin-left: -11px;
        padding-left: 0px;
    }

    #customer-qrcode-location {
        width: 100%;
        margin-left: -35px;
        padding-left: 0px;
    }

    .qrcode-location-div {
        width: 100%;
        display: inline-block;
        margin-left: 0px;
        padding-left: 0px;

    }

    .qrcode-location-div label {
        width: 60px;
        margin-left: 0px;
        margin-right: 1%;

    }

    #incidentSubjectTab {
        width: 100%;
        margin-left: -10px;
        padding-left: 0px;
    }

    .customer-form-footer {
        display: inline-block;
        ;
        padding-top: 10px;
    }

    #tabList {
        width: 507px;
    }

    #remove-cpid-allocation,
    #remove-qrcode-location {
        margin-left: -13px;
    }

    #landingPage {
        margin-left: -22px;
        padding-left: 1px;
    }

    #check_same_address {
        margin-left: 10px;
    }




</style>


<style>
    .pac-container {
        z-index: 10000 !important;
    }

    ul li {
        list-style: none;
    }

    hr {
        border: none;
        height: 10px;
        /* Set the hr color */
        color: #333;
        /* old IE */
        background-color: #333;
        /* Modern Browsers */
    }

    /* Remove margins and padding from the parent ul */
    #tabList {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    .fa-toggle-on {
        color: #003A63;
    }

    .fa-toggle-off {
        color: #003A63;
    }

    .fa-check {
        color: green;
    }

    .fa-times {
        color: red;
    }



    /* Style the caret/arrow */
    .caret {
        cursor: pointer;
        border: 0;
        margin: 0;
        display: inline;
        padding: 1rem;
        user-select: none;
        /* Prevent text selection */
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
        width: 175px;
        float: right;
    }

    input.largerCheckbox {
        width: 20px;
        height: 20px;
    }

    .check {
        text-align: center;
        /* center checkbox horizontally */
        vertical-align: middle;
        /* center checkbox vertically */
    }
</style>
@include('admin::customer.partials.add-customer-scripts')
@include('admin::customer.partials.kpi.kpi-scripts')
<script>
    $("ul.nav-tabs a").click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
</script>
@stop
