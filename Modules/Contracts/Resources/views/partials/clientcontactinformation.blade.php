<div id="block-{{$clientcontactcount}}">
<div class="form-group row" style="border-top:solid 1px #000" >
        <div class="col-sm-11 ">
        </div>


</div>
<div class="form-group row">
        <div class="col-sm-4">Contact Name <span class="mandatory">*</span>
</div>
        <div class="col-sm-4">
        <select class="form-control primary_contact" countattr="{{$clientcontactcount}}" name="primary_contact_{{$clientcontactcount}}"  id="primary_contact_{{$clientcontactcount}}" placeholder="Select">
                                <option value="">Select</option>

                                @foreach ($lookUps['userlookuprepository'] as $key=>$value)
                                                <option
                                                                value="{{$value['id']}}">{{$value['full_name']}} </option>
                                @endforeach

                </select>
        </div>
        <div class="col-sm-4">
                        <label class="error text-danger" for="primary_contact_{{$clientcontactcount}}"></label>
        </div>
</div>
<div class="form-group row">
<div class="col-sm-4">Who is the primary client contact for this contract ? <span class="mandatory">*</span>
</div>
<div class="col-sm-4">
        <input type="text" required name="contact_name_{{$clientcontactcount}}" id="contact_name_{{$clientcontactcount}}" value=" " class="form-control contact_name" />
</div>
<div class="col-sm-4">
                <label class="error text-danger" for="contact_name_{{$clientcontactcount}}"></label>
</div>
</div>
<div class="form-group row">
<div class="col-sm-4">What is the person's job title ? <span class="mandatory">*</span>
</div>
<div class="col-sm-4">
        <input required class="form-control" name="contact_jobtitle_{{$clientcontactcount}}" required id="contact_jobtitle_{{$clientcontactcount}}" placeholder="Job Title" />
                {{-- <select required class="form-control" name="contact_jobtitle_{{$clientcontactcount}}" required id="contact_jobtitle_{{$clientcontactcount}}" placeholder="Select">
                                <option value="">Select</option>

                                @foreach ($lookUps['positionlookuprepository'] as $key=>$value)
                                                <option value="{{$value['id']}}">{{$value['position']}} </option>
                                @endforeach

                </select> --}}
</div>
<div class="col-sm-4">
                <label class="error text-danger" for="contact_jobtitle_{{$clientcontactcount}}"></label>
</div>
</div>
<div class="form-group row">
<div class="col-sm-4">What is the person's email address ? <span class="mandatory">*</span>
</div>
<div class="col-sm-4">
        <input  type="email"  required name="contact_emailaddress_{{$clientcontactcount}}" id="contact_emailaddress_{{$clientcontactcount}}"  value=" " class="form-control" />
</div>
<div class="col-sm-4">
                <label class="error text-danger" for="contact_emailaddress_{{$clientcontactcount}}"></label>
</div>
</div>
<div class="form-group row">
<div class="col-sm-4">What is the person's office number ?   <span class="mandatory">*</span>
</div>
<div class="col-sm-4">
        <input  type="text" required name="contact_phoneno_{{$clientcontactcount}}" placeholder="Office No [ format (XXX)XXX-XXXX ]" id="contact_phoneno_{{$clientcontactcount}}" value=" " pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" class="form-control" />
</div>
<div class="col-sm-4">
                <label class="error text-danger" for="contact_phoneno_{{$clientcontactcount}}"></label>
</div>
</div>
<div class="form-group row">
<div class="col-sm-4">What is the person's cell number ? <span class="mandatory">*</span>
</div>
<div class="col-sm-4">
        <input  type="text" required name="contact_cellno_{{$clientcontactcount}}" placeholder="Cell No [ format (XXX)XXX-XXXX ]" id="contact_cellno_{{$clientcontactcount}}" value=" " pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" class="form-control" />
</div>
<div class="col-sm-4">
                <label class="error text-danger" for="contact_cellno_{{$clientcontactcount}}"></label>
</div>
</div>
<div class="form-group row">
<div class="col-sm-4">What is the fax number associated with the individual ?
</div>
<div class="col-sm-4">
        <input type="text" name="contact_faxno_{{$clientcontactcount}}" placeholder="Fax No [ format (XXX)XXX-XXXX ]" id="contact_faxno_{{$clientcontactcount}}" value=" " class="form-control phone" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}"  />
</div>
<div class="col-sm-4">
                <label class="error text-danger" for="contact_faxno_{{$clientcontactcount}}"></label>
</div>
</div>
<div class="form-group row">
                <div class="col-sm-4">
                </div>
                <div class="col-sm-4">


        </div>
</div>
</div>
