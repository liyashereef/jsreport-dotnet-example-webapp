
<div class="form-group row" id="project_number">
    <label for="project_number" class="col-sm-5 col-form-label">Project Number</label>
    <div class="col-sm-6">
        {{ Form::text('project_number', isset($customer) ? old('project_number',$customer->project_number) : null, array('class'=>'form-control edge-validation project-number', 'placeholder'=>'Project Number', 'required')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="client_name">
    <label for="client_name" class="col-sm-5 col-form-label">Client Name</label>
    <div class="col-sm-6">
        {{ Form::text('client_name', isset($customer) ? old('client_name',$customer->client_name) : null, array('class'=>'form-control edge-validation', 'placeholder'=>'Client Name', 'required')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="contact_person_name">
    <label for="contact_person_name" class="col-sm-5 col-form-label">Contact Person Name</label>
    <div class="col-sm-6">
        {{ Form::text('contact_person_name', isset($customer) ? old('contact_person_name',$customer->contact_person_name) : null, array('class'=>'form-control', 'placeholder'=>'Contact Person Name')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="contact_person_email_id">
    <label for="contact_person_email_id" class="col-sm-5 col-form-label">Contact Person Email Id</label>
    <div class="col-sm-6">
        {{ Form::email('contact_person_email_id', isset($customer) ? old('contact_person_email_id',$customer->contact_person_email_id) : null, array('class'=>'form-control', 'placeholder'=>'Contact Person Email Id')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="contact_person_phone">
    <label for="contact_person_phone" class="col-sm-5 col-form-label">Contact Person Phone</label>
    <div class="col-sm-6">
        {{ Form::text('contact_person_phone', isset($customer) ? old('contact_person_phone',$customer->contact_person_phone) : null, array('class'=>'form-control phone', 'placeholder'=>'Contact Person Phone [ format (XXX)XXX-XXXX ]', 'pattern'=>'[\(]\d{3}[\)]\d{3}[\-]\d{4}')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="contact_person_phone_ext">
    <label for="contact_person_phone_ext" class="col-sm-5 col-form-label">Ext</label>
    <div class="col-sm-6">
       {{ Form::text('contact_person_phone_ext', isset($customer) ? old('contact_person_phone_ext',$customer->contact_person_phone_ext) : null, array('class'=>'form-control', 'placeholder'=>'Ext.')) }}
       <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
   </div>
</div>
<div class="form-group row" id="contact_person_cell_phone">
    <label for="contact_person_cell_phone" class="col-sm-5 col-form-label">Contact Person Cell Phone</label>
    <div class="col-sm-6">
      {{ Form::text('contact_person_cell_phone', isset($customer) ? old('contact_person_cell_phone',$customer->contact_person_cell_phone) : null, array('class'=>'form-control phone', 'placeholder'=>'Contact Person Cell Phone [ format (XXX)XXX-XXXX ]')) }}

      <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
  </div>
</div>
<div class="form-group row" id="contact_person_position">
    <label for="contact_person_position" class="col-sm-5 col-form-label">Contact Person Position</label>
    <div class="col-sm-6">
      {{ Form::text('contact_person_position', isset($customer) ? old('contact_person_position',$customer->contact_person_position) : null, array('class'=>'form-control', 'placeholder'=>'Contact Person Position')) }}

      <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
  </div>
</div>

<div class="form-group row" id="address">
    <label for="address" class="col-sm-5 col-form-label">Address</label>
    <div class="col-sm-6">
        {{ Form::text('address', isset($customer) ? old('address',$customer->address) : null, array('class'=>'form-control edge-validation', 'placeholder'=>'Address', 'required')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="city">
    <label for="city" class="col-sm-5 col-form-label">City</label>
    <div class="col-sm-6">
        {{ Form::text('city', isset($customer) ? old('city',$customer->city) : null, array('class'=>'form-control edge-validation', 'placeholder'=>'City', 'required')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="province">
    <label for="Province" class="col-sm-5 col-form-label">Province</label>
    <div class="col-sm-6">
        {{ Form::text('province', isset($customer) ? old('province',$customer->province) : null, array('class'=>'form-control edge-validation', 'placeholder'=>'Province', 'required')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="postal_code">
    <label for="postal_code" class="col-sm-5 col-form-label">Postal Code</label>
    <div class="col-sm-6">
        {{ Form::text('postal_code', isset($customer) ? old('postal_code',$customer->postal_code) : null, array('class'=>' postal-code form-control edge-validation','min'=>6, 'max'=>6, 'placeholder'=>'Postal Code', 'required')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

  <div class="form-group row" id="billing_address">
<label for="billing_address" class="col-sm-5 col-form-label">Billing Address <span class="mandatory">*</span></label>
<div class="col-sm-6">
        {{ Form::text('billing_address', isset($customer) ? old('billing_address',$customer->billing_address) : null, array('class'=>'form-control', 'placeholder'=>'Billing Address')) }}
 <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
         </div>
    </div>

    <div class="form-group row" id="same_address_check">
    <label for="same_address_check" class="col-sm-5"></label>
    <div class="col-sm-6">
        <label for="same_address_check" class="col-sm-5 col-form-label">Same as Site Address</label>
    {{ Form::checkbox('same_address_check',null,null, array('id'=>'check_same_address')) }}
    </div>
</div>

<div class="form-group row" id="industry_sector_lookup_id">
    <label for="industry_sector_lookup_id" class="col-sm-5 col-form-label">Industry Sector</label>
    <div class="col-sm-6">
        {{ Form::select('industry_sector_lookup_id',[null=>'Select']+$lookups['industrySectorLookup'], isset($customer) ? old('industry_sector_lookup_id',$customer->industry_sector_lookup_id) : null,array('class' => 'form-control edge-validation select2', 'required')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="region_lookup_id">
    <label for="region_lookup_id" class="col-sm-5 col-form-label">Region</label>
    <div class="col-sm-6">
        {{ Form::select('region_lookup_id',[null=>'Select']+$lookups['regionLookup'], isset($customer) ? old('region_lookup_id',$customer->region_lookup_id) : null, array('class' => 'form-control edge-validation', 'required')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="description">
    <label for="description" class="col-sm-5 col-form-label">Description</label>
    <div class="col-sm-6">
        {{ Form::text('description', isset($customer) ? old('description',$customer->description) : null, array('class'=>'form-control', 'placeholder'=>'Description')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="proj_open">
    <label for="proj_open" class="col-sm-5 col-form-label">Project Open Date</label>
    <div class="col-sm-6">
        {{ Form::text('proj_open', isset($customer) ? old('proj_open',$customer->proj_open) : null, array('class'=>'form-control datepicker', 'placeholder'=>'Project Open Date')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="arpurchase_order_no">
    <label for="arpurchase_order_no" class="col-sm-5 col-form-label">AR Purchase Order Number</label>
    <div class="col-sm-6">
        {{ Form::text('arpurchase_order_no', isset($customer) ? old('arpurchase_order_no',$customer->arpurchase_order_no) : null, array('class'=>'form-control', 'placeholder'=>'AR Purchase Order Number')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="arcust_type">
    <label for="arcust_type" class="col-sm-5 col-form-label">AR Customer Type</label>
    <div class="col-sm-6">
        {{ Form::text('arcust_type', isset($customer) ? old('arcust_type',$customer->arcust_type) : null, array('class'=>'form-control', 'placeholder'=>'AR Customer Type')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="requester_name">
    <label for="requester_name" class="col-sm-5 col-form-label">Requestor Name</label>
    <div class="col-sm-6">
        {{ Form::select('requester_name', $lookups['requesterLookup'],isset($requester_name) ? old('requester_name',$requester_name) : null, array('id'=>'requester_id','class'=>'form-control edge-validation', 'placeholder'=>'Please Select', 'required')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="requester_position">
    <label for="requester_position" class="col-sm-5 col-form-label">Requestor Position</label>
    <div class="col-sm-6">
        @if(isset($customer) && $customer->requesterDetails!=null)
        {{ Form::text('requester_position', isset($customer->requesterDetails->employee) ? old('requester_position',$customer->requesterDetails->employee->employeePosition->position ?? null) : null, array('class'=>'form-control', 'placeholder'=>'Requestor Position','readonly'=>true)) }}
        @else
       {{ Form::text('requester_position', isset($customer) ? old('requester_position',$customer->requester_position) : null, array('class'=>'form-control', 'placeholder'=>'Requestor Position','readonly'=>true)) }}
        @endif
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="requester_empno">
    <label for="requester_empno" class="col-sm-5 col-form-label">Requestor Employee Number</label>
    <div class="col-sm-6">
         @if(isset($customer) && $customer->requesterDetails!=null)
        {{ Form::text('requester_empno', isset($customer->requesterDetails->employee) ? old('requester_empno',$customer->requesterDetails->employee->employee_no) : null , array('class'=>'form-control', 'placeholder'=>'Requestor Employee Number' ,'readonly'=>true)) }}
        @else
        {{ Form::text('requester_empno', isset($customer) ? old('requester_empno',$customer->requester_empno) : null , array('class'=>'form-control', 'placeholder'=>'Requestor Employee Number' ,'readonly'=>true)) }}
          @endif
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="job_description">
    <label for="job_description" class="col-sm-5 col-form-label">Site Notes</label>
    <div class="col-sm-6">
        {{ Form::textarea('job_description', isset($customer_stc_details) ? old('job_description',$customer_stc_details->job_description) : null , array('class'=>'form-control', 'placeholder'=>'Site Notes','required'=>true)) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>

<div class="form-group row" id="nmso_account">
    <label for="nmso_account" class="col-sm-5 col-form-label">Is this a NMSO Account?</label>
    <div class="col-sm-6">
       {{ Form::select('nmso_account',['no'=>'No','yes'=>'Yes',], isset($customer_stc_details) ? old('nmso_account',$customer_stc_details->nmso_account) : null,array('class' => 'form-control', 'required')) }}
       <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
   </div>
</div>

<div class="form-group row" id="security_clearance_lookup_id" style="display:{{old('nmso_account', isset($customer_stc_details) ? $customer_stc_details->nmso_account : 'nmso_account') == "yes" ? '' : 'none' }}">
    <label for="security_clearance_lookup_id" class="col-sm-5 col-form-label edge-validation">What is the security clearance required for this post?</label>
    <div class="col-sm-6">
       {{ Form::select('security_clearance_lookup_id',[null=>'Select']+$lookups['securityClearanceLookup'], isset($customer_stc_details) ? old('security_clearance_lookup_id',$customer_stc_details->security_clearance_lookup_id) : null, array('class' => 'form-control')) }}
       <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
   </div>
</div>

<div class="form-group row">
    <div class="col-sm-5"></div>
    <div class="col-sm-6">
        {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'save'))}}
        {{ Form::button('Cancel', array('class'=>'btn cancel', 'type'=>'reset','onClick'=>'window.history.back();'))}}
    </div>
</div>
