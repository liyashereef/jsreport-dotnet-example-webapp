@section('css')
   <style>
           radio{
                   width:10px;
           }
           ul, li {

                list-style:none
                }
                li {

}
.btnwidthhalf{
        width:100px !important;
}
.btnwidthfull{
        width:203px !important;
}
.error li:nth-child(1):before {
   content: "*"
}

.error li:nth-child(2):before {
   content: ""
}
.yesnolabel{
        margin-left:20px;
}
   </style>
@endsection
<form method="POST"  id="uploadform-data" name="uploadform-data" enctype="multipart/form-data">
<input type="hidden" name="savemode" id="savemode" value="0" />
<input type="hidden" name="ongoingupload" id="ongoingupload" value="0" />
<div class="container-fluid">
                <div class="form-group row">
                                <div class="col-sm-12 candidate-screen-head" > Prerequisites

                                </div>

                        </div>
</div>
<div class="form-group row" id="formfill">
                <div class="col-sm-4">Do we have a signed contract with client </div>
                <div class="col-sm-4">
                        <select class="form-control" name="yes_no" id="yes_no" placeholder="Select">
                        <option value="0">Select</option>
                        <option value="1">Yes</option>
                        <option value="2">No</option>
                        </select>
                        <span id="yes" style="display:none">Yes</span>

                </div>
                <div class="col-sm-4">
                        <label  for="yes_no" class="error text-danger"></label>
                </div>
                </div>
                <div class="form-group row" id="yesbar" style="display:none">
                        <div class="col-sm-4">Upload Contract <small>(doc,docx,pdf,xls,xlsx,ods,ppt,pptx)</small><span class="mandatory">*</span></div>
                        <div class="col-sm-4">
                                <input  type="file" name="cmuf_contract_document" id="cmuf_contract_document" class="form-control" />
                                <input type="hidden" name="contract_document_attachment" id="contract_document_attachment" value="0" />
                                <span id="fname" style="display:none"></span>
                        </div>
                        <div class="col-sm-4">

                                <label  for="cmuf_contract_document" class="error text-danger"></label>
                        </div>
                </div>
                <div class="form-group row">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-4">
                                <input type="button" id="uploadcontract" attr_file="cmuf_contract_document" attr_hidden="contract_document_attachment" class="button btn submit" value="Enter" />
                                <input style="display:none" type="button" id="swap" value="Swap" />
                        </div>
                        <div class="col-sm-4">

                        </div>
                </div>
        <div class="container-fluid" id="uploadform" style="display:none">
        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head" >Contract Information</div>

        </div>
        <div class="form-group row">
                <div class="col-sm-4">Contract Name<span class="mandatory">*</span></div>
                <div class="col-sm-4">
                                <select class="form-control" name="customer_client" required id="customer_client" placeholder="Select">
                                        <option value="">Select</option>
                                        @foreach ($lookUps['customerLookup'] as $customers)
                                                <option value="{{$customers->id}}">{{$customers->project_number}} - {{$customers->client_name}}</option>
                                        @endforeach
                                </select>


                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="customer_client"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Contract Number<span class="mandatory">*</span></div>
                <div class="col-sm-4"><input readonly type="text" class="form-control" id="contract_number" name="contract_number" /></div>
                <div  class="col-sm-4"><label class="error text-danger" for="contract_number"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Submission Date<span class="mandatory">*</span></div>
                <div class="col-sm-4">{{date("M-d-Y")}}</div>
                <div  class="col-sm-4">   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Regional Manager<span class="mandatory">*</span></div>
                <div class="col-sm-4">
                        <input readonly type="text"  class="form-control" id="area_manager_text" name="area_manager_text" />
                        <input type="hidden" id="area_manager_id" name="area_manager_id" value="" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="area_manager_text"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Reason for Submission<span class="mandatory">*</span></div>
                <div class="col-sm-4">
                        <select class="form-control" name="reason_for_submission" required id="reason_for_submission" placeholder="Select">
                                        <option value="">Select</option>
                                        @foreach ($lookUps['reasonforsubmissionLookup'] as $reasonforsubmission)
                                                <option value="{{$reasonforsubmission->id}}">{{$reasonforsubmission->reason}}</option>
                                        @endforeach

                        </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="reason_for_submission"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head" >Business Information</div>

        </div>
        <div class="form-group row">
                <div class="col-sm-4">What business segment does the contract fall under  <span class="mandatory">*</span></div>
                <div class="col-sm-4">
                                <select class="form-control" name="business_segment" required id="business_segment" placeholder="Select">
                                                <option value="">Select</option>
                                                @foreach ($lookUps['businessSegmentLookup'] as $businesssegment)
                                                        <option value="{{$businesssegment->id}}">{{$businesssegment->segmenttitle}}</option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="business_segment"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What line of business does the contract fall under  <span class="mandatory">*</span></div>
                <div class="col-sm-4">
                                <select class="form-control" name="line_of_business" required id="line_of_business" placeholder="Select">
                                                <option value="">Select</option>
                                                @foreach ($lookUps['lineofBusinessLookup'] as $lineofbusiness)
                                                        <option value="{{$lineofbusiness->id}}">{{$lineofbusiness->lineofbusinesstitle}}</option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="line_of_business"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Is this a multi division contract  <span class="mandatory">*</span> </div>
                <div class="col-sm-1">
                <input type="radio" name="multidivision" id="multidivision" value="1"  />&nbsp;Yes<label class="error text-danger yesnolabel" for="multidivision">Yes</label>
                </div><div class="col-sm-1"><input type="radio" name="multidivision" id="multidivision" value="0" checked />&nbsp;No<label class="error text-danger yesnolabel" for="multidivision">No</label></div>
                <div  class="col-sm-4"><label class="error text-danger" for="multidivision"></label>   </div>
        </div>
        <div class="form-group row" id="leaddiv" style="display:none">
                <div class="col-sm-4">Who is the lead division  </div>
                <div class="col-sm-4">
                                <select disabled class="form-control" name="division_lookup" required id="division_lookup" placeholder="Select">
                                                <option value="">Select</option>
                                                @foreach ($lookUps['divisionlookuprepository'] as $divisionlookup)
                                                        <option value="{{$divisionlookup->id}}">{{$divisionlookup->division_name}}</option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="division_lookup"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Is there a master entity  <span class="mandatory">*</span></div>
                <div class="col-sm-1">
                        <input type="radio" name="masterentity" id="masterentity"  value="1" checked />&nbsp;Yes <label class="error text-danger yesnolabel" for="masterentity"></label>
                </div><div class="col-sm-1"> <input type="radio" name="masterentity" id="masterentity" value="0" checked />&nbsp;No<label class="error text-danger yesnolabel" for="masterentity"></label>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="masterentity"></label>   </div>
                </div>

        <div class="form-group row"  id="parentdiv" style="display:none">
                <div class="col-sm-4">Parent Project Number</div>
                <div class="col-sm-4">

                                <select disabled class="form-control" name="master_customer" required id="master_customer" placeholder="Select">
                                                <option value="">Select</option>
                                                @foreach ($lookUps['parentcustomerlookuprepository'] as $key=>$value)
                                                        <option value="{{$key}}">{{$value}}</option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="master_customer"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head" > Enter Regional Manager Information
 </div>

        </div>

        <div class="form-group row">
                        <div class="col-sm-4">Who is the Regional Manager assigned to the account  <span class="mandatory">*</span> </div>
                        <div class="col-sm-4">
                                <input readonly type="text" class="form-control" id="area_manager" name="area_manager" value="" />
                                <input type="hidden" name="rmanagerid" id="rmanagerid" value="" />
                        </div>
                        <div  class="col-sm-4"><label class="error text-danger" for="area_manager"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the person's job title  <span class="mandatory">*</span> </div>
                <div class="col-sm-4">
                         <!--
                        <input type="text" class="form-control" id="area_manager_position_text" name="area_manager_position_text" value="" />
                         -->
                         <select class="form-control" name="area_manager_position_text" required id="area_manager_position_text" placeholder="Select">
                                        <option value="">Select</option>

                                        @foreach ($lookUps['positionlookuprepository'] as $key=>$value)
                                                        <option id-value="{{$value['id']}}" value="{{$value['position']}}">{{$value['position']}} </option>
                                        @endforeach

                        </select>

                        <input type="hidden"  id="area_manager_position_id" name="area_manager_position_id" value="" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="area_manager_position_text"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the person's email address <span class="mandatory">*</span> </div>
                <div class="col-sm-4">
                        <input  type="email" class="form-control" placeholder="Email Address" id="area_manager_email_address" name="area_manager_email_address" value="" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="area_manager_email_address"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the person's office number <span class="mandatory">*</span>
                </div>
                <div class="col-sm-4">
                        <input  type="text" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" placeholder="Office No [ format (XXX)XXX-XXXX ]" class="form-control phone" id="area_manager_office_number" name="area_manager_office_number" value="" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="area_manager_office_number"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the person's cell number  <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                        <input  type="text" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" placeholder="Cell No [ format (XXX)XXX-XXXX ]" class="form-control phone" id="area_manager_cell_number" name="area_manager_cell_number" value="" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="area_manager_cell_number"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the fax number associated with the individual
 </div>
                <div class="col-sm-4">
                        <input  type="text" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" placeholder="Fax No [ format (XXX)XXX-XXXX ]"  class="form-control phone" id="area_manager_fax_number" name="area_manager_fax_number" value="0" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="area_manager_fax_number"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Where is the contact's primary office located <span class="mandatory">*</span>

 </div>
                <div class="col-sm-4">
                                <select class="form-control" name="office_address" required id="office_address" placeholder="Select">
                                                <option value="">Select</option>
                                                @foreach ($lookUps['officeaddresslookuprepository'] as $officeaddress)
                                                        <option value="{{$officeaddress->id}}">{{$officeaddress->addresstitle}}</option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="office_address"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head" > Sales Information</div>

        </div>
        <div class="form-group row">
                <div class="col-sm-4">Who won the contract  <span class="mandatory">*</span>
                </div>
                <div class="col-sm-4">
                                <select class="form-control" name="sales_employee_id" required id="sales_employee_id" placeholder="Select">
                                                <option value="">Select</option>

                                                @foreach ($lookUps['userlookuprepository'] as $key=>$value)
                                                         <option data-empno="{{$value["emp_no"]}}"
                                                                                value="{{$value['id']}}">{{$value['full_name']}} </option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="sales_employee_id"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the person's job title <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                                <select class="form-control" name="sales_contact_job_title" required id="sales_contact_job_title" placeholder="Select">
                                                <option value="">Select</option>

                                                @foreach ($lookUps['positionlookuprepository'] as $key=>$value)
                                                                <option data-empno="{{$value["id"]}}"
                                                                                value="{{$value['id']}}">{{$value['position']}} </option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="sales_contact_job_title"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the person's email address <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                                <input type="email"  name="sales_contact_emailaddress" id="sales_contact_emailaddress" value=" " class="form-control">
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="sales_contact_emailaddress"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the person's office number <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                                <input  type="text"  id="sales_contact_office_number" placeholder="Office No [ format (XXX)XXX-XXXX ]" name="sales_contact_office_number" value="" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="sales_contact_office_number"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the person's cell number <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                                <input type="text"   id="sales_contact_cell_number" name="sales_contact_cell_number" placeholder="Cell No [ format (XXX)XXX-XXXX ]" value="" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone">
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="sales_contact_cell_number"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the fax number associated with the individual
 </div>
                <div class="col-sm-4">
                                <input type="text" name="sales_contact_faxno" id="sales_contact_faxno" value=" " placeholder="Fax No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone">
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="sales_contact_faxno"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Which division won the bid <span class="mandatory">*</span>

 </div>
                <div class="col-sm-4">
                                <select class="form-control" name="sales_contact_division" required id="sales_contact_division" placeholder="Select">
                                                <option value="">Select</option>
                                                @foreach ($lookUps['divisionlookuprepository'] as $divisionlookup)
                                                        <option value="{{$divisionlookup->id}}">{{$divisionlookup->division_name}}</option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="sales_contact_division"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Where is the contact's primary office located <span class="mandatory">*</span>

 </div>
                <div class="col-sm-4">

                                <select class="form-control" name="sales_contact_office_address" required id="sales_contact_office_address" placeholder="Select">
                                                <option value="">Select</option>
                                                @foreach ($lookUps['officeaddresslookuprepository'] as $officeaddress)
                                                        <option value="{{$officeaddress->id}}">{{$officeaddress->addresstitle}}</option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="sales_contact_office_address"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head" > Client Contact Information
                        <input type="hidden" name="clientcontactcount" id="clientcontactcount" value="1" />
                </div>

        </div>
        <div class="container-fluid" id="clientcontactinformation" style="padding:0">
                <div class="form-group row">
                        <div class="col-sm-4">Contact Name <span class="mandatory">*</span>
                </div>
                        <div class="col-sm-4">
                                <select class="form-control" name="primary_contact" required id="primary_contact" placeholder="Select">
                                                <option value="">Select</option>

                                                @foreach ($lookUps['userlookuprepository'] as $key=>$value)
                                                                <option data-empno="{{$value["emp_no"]}}"
                                                                                value="{{$value['id']}}">{{$value['full_name']}} </option>
                                                @endforeach

                                </select>
                        </div>
                        <div  class="col-sm-4"><label class="error text-danger" for="primary_contact"></label>   </div>
                </div>
                <div class="form-group row">
                <div class="col-sm-4">Who is the primary client contact for this contract <span class="mandatory">*</span>
                </div>
                <div class="col-sm-4">
                        <input readonly type="text" name="contact_name" id="contact_name" value=" " class="form-control" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contact_name"></label>   </div>
                </div>
                <div class="form-group row">
                <div class="col-sm-4">What is the person's job title <span class="mandatory">*</span>
                </div>
                <div class="col-sm-4">
                        <input class="form-control" name="contact_jobtitle" required id="contact_jobtitle" placeholder="Job Title">
                                {{-- <select class="form-control" name="contact_jobtitle" required id="contact_jobtitle" placeholder="Select">
                                                <option value="">Select</option>

                                                @foreach ($lookUps['positionlookuprepository'] as $key=>$value)
                                                                <option data-empno="{{$value["id"]}}"
                                                                                value="{{$value['id']}}">{{$value['position']}} </option>
                                                @endforeach

                                </select> --}}
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contact_jobtitle"></label>   </div>
                </div>
                <div class="form-group row">
                <div class="col-sm-4">What is the person's email address <span class="mandatory">*</span>
                </div>
                <div class="col-sm-4">
                        <input  type="email" name="contact_emailaddress" id="contact_emailaddress" value=" " class="form-control" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contact_emailaddress"></label>   </div>
                </div>
                <div class="form-group row">
                <div class="col-sm-4">What is the person's office number <span class="mandatory">*</span>
                </div>
                <div class="col-sm-4">
                        <input  type="text" name="contact_phoneno" id="contact_phoneno" placeholder="Office No [ format (XXX)XXX-XXXX ]" value=" " pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contact_phoneno"></label>   </div>
                </div>
                <div class="form-group row">
                <div class="col-sm-4">What is the person's cell number <span class="mandatory">*</span>
                </div>
                <div class="col-sm-4">
                        <input  type="text" name="contact_cellno" id="contact_cellno" placeholder="Cell No [ format (XXX)XXX-XXXX ]" value=" " pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contact_cellno"></label>   </div>
                </div>
                <div class="form-group row">
                <div class="col-sm-4">What is the fax number associated with the individual
                </div>
                <div class="col-sm-4">
                        <input   type="text" name="contact_faxno" id="contact_faxno" placeholder="Fax No [ format (XXX)XXX-XXXX ]" value="0" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" />
                </div>
                <div  class="col-sm-4">
                        <label class="error text-danger" for="contact_faxno"></label>   </div>
                </div>
        </div>
        <div class="form-group row" id="addmoreclientblock">
                <div class="col-sm-4" style="text-align:center"></div>
                <div class="col-xs-1" ><button type="button" id="addmoreclient" class="btn btn-primary">+</button></div>
                <div class="col-xs-1">&nbsp;&nbsp;<a class="btn submit remClientblock" id="remclientblock" style="display:none">-</a></div>
        </div>
        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head" > Contract Terms</div>

        </div>
        <div class="form-group row">
                <div class="col-sm-4">Contract Start Date <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                        <input type="text"  name="contract_startdate" id="contract_startdate" placeholder='Project Open Date (Y-m-d)' value="" class="form-control  datepicker" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contract_startdate"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Contract Length (Years) <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4"><input min="0" type="number"  name="contract_length" id="contract_length" value="" class="form-control" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contract_length"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Contract Expiry   <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">

                        <input type="text"  name="contract_enddate" id="contract_enddate" placeholder='Project End Date (Y-m-d)' value="" class="form-control  datepicker" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contract_enddate"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Is there a renewal option <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                        <select name="renewable_contract" id="renewable_contract" class="form-control" >
                                <option value=" ">select</option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                        </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="renewable_contract"></label>   </div>
        </div>
        <div class="form-group row" id="renewableyears" style="display: none">
                <div class="col-sm-4">How long is the renewal option (Years)
 </div>
                <div class="col-sm-4"><input readonly min="0" type="number" min="0" value="0" class="form-control" name="contract_length_renewal_years" id="contract_length_renewal_years" value="0" /></div>
                <div  class="col-sm-3"><label class="error text-danger" for="contract_length_renewal_years"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Does the client have a termination clause <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                        <select name="termination_clause_client" id="termination_clause_client" class="form-control" >
                                <option value=" ">select</option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                        </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="termination_clause_client"></label>   </div>
        </div>
        <div class="form-group row" style="display: none" id="terminationnoticeperiodclientdiv">
                <div class="col-sm-4">Termination notice period (Days)
 </div>
                <div class="col-sm-4"><input readonly min="0" type="number" min="0" value="0" class="form-control" name="terminationnoticeperiodclient" id="terminationnoticeperiodclient" value="0" /></div>
                <div  class="col-sm-3"><label class="error text-danger" for="terminationnoticeperiodclient"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Does service provider have a termination clause <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                        <select name="termination_clause" id="termination_clause" class="form-control" >
                                <option value=" ">select</option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                        </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="termination_clause"></label>   </div>
        </div>
        <div class="form-group row" style="display: none" id="terminationnoticeperioddiv">
                <div class="col-sm-4">Termination notice period (Days)
 </div>
                <div class="col-sm-4"><input readonly min="0" type="number" min="0" value="0" class="form-control" name="terminationnoticeperiod" id="terminationnoticeperiod" value="0" /></div>
                <div  class="col-sm-3"><label class="error text-danger" for="terminationnoticeperiod"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">How often will the pay/bill rate change <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                                <select class="form-control" name="billing_ratechange" required id="billing_ratechange" placeholder="Select">
                                                <option value="">Select</option>

                                                @foreach ($lookUps['billingratechangerepository'] as $billingratechange)
                                                                <option value="{{$billingratechange->id}}">{{$billingratechange->ratechangetitle}} </option>
                                                @endforeach

                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="billing_ratechange"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">What is the annual increase allowable <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                        <input type="text" name="contract_annualincrease_allowed" id="contract_annualincrease_allowed" class="form-control" placeholder="Annual Increase Allowed" />

                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contract_annualincrease_allowed"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Contract written template <span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                                <select class="form-control" name="contractonourtemplate" required id="contractonourtemplate" placeholder="Select">
                                                <option value="">Select</option>
                                                @foreach ($lookUps['contractprovidertemplate'] as $contractprovidertemplate)
                                                    <option value="{{$contractprovidertemplate->id}}">{{$contractprovidertemplate->templateparty}}</option>
                                                @endforeach
                                </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="contractonourtemplate"></label>   </div>
        </div>

        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head" > Pricing Definition</div>

        </div>
        <div class="form-group row">
                <div class="col-sm-4">Load RFP Pricing Template (doc,docx,pdf,xls,xlsx,ods,ppt,pptx)<span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                        <input type="file" required name="rfc_pricing_template" id="rfc_pricing_template" class="form-control">
                        <input type="hidden" name="rfc_document_attachment" id="rfc_document_attachment" value="" />
                        <p id="rfcuploadlabel" style="display:none"></p>
                </div>
                <div  class="col-sm-1">
                        <button type="button" id="uploadrfc" attr_file="rfc_pricing_template" attr_hidden="rfc_document_attachment" class="button btn submit">Upload</button>

                </div>
                <div  class="col-sm-3">
                                <label class="error text-danger" for="rfc_pricing_template"></label>
                                <label class="error text-danger" for="rfc_document_attachment"></label>
                </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Total Annual Contract Billing
 </div>
                <div class="col-sm-4">
                      <input type="text" class="form-control dollar" pattern="^\d*(\.\d{0,2})?$" step=".01" name="total_annual_contract_billing" id="total_annual_contract_billing" placeholder="$" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="total_annual_contract_billing"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Total Annual Contract Wages & Benefits
 </div>
                <div class="col-sm-4">
                        <input type="text" class="form-control dollar" pattern="^\d*(\.\d{0,2})?$" name="total_annual_contract_wages_benifits" id="total_annual_contract_wages_benifits" placeholder="$" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="total_annual_contract_wages_benifits"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Total Annual (Expected) Contribution Margin
 </div>
                <div class="col-sm-4">
                        <input type="text" class="form-control dollar" pattern="^\d*(\.\d{0,2})?$" name="total_annual_expected_contribution_margin" id="total_annual_expected_contribution_margin" placeholder="$" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="total_annual_expected_contribution_margin"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4"  style="display:inline-block">Total Hours per Week

 </div>
                <div class="col-sm-2" style="display:inline-block">
                        <input type="number" class="form-control notdecimal"
                        name="total_hours_perweek" id="total_hours_perweek" placeholder="Hour" />
                </div>
                <div class="col-sm-1" style="display:inline-block">
                     Minutes
                </div>
                <div class="col-sm-1" style="display:inline-block">
                        <select class="form-control"
                                class="form-control notdecimal"
                                name="total_hours_perweek_minutes" id="total_hours_perweek_minutes">
                                @for($i=0;$i<60;$i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                @endfor

                        </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="total_hours_perweek"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Average Bill Rate

 </div>
                <div class="col-sm-4">
                        <input type="text" class="form-control dollar markupval" name="average_billrate" pattern="^\d*(\.\d{0,2})?$" id="average_billrate" placeholder="$" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="average_billrate"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Average Wage Rate

 </div>
                <div class="col-sm-4">
                        <input type="text" class="form-control dollar markupval" name="average_wagerate" pattern="^\d*(\.\d{0,2})?$" id="average_wagerate" placeholder="$" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="average_wagerate"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Average Markup


 </div>
                <div class="col-sm-4">
                        <input type="text" class="form-control dollar" readonly name="average_markup" pattern="^\d*(\.\d{0,2})?$" id="average_markup" placeholder="%" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="average_markup"></label>   </div>
        </div>
        <div class="form-group row">
                        <div class="col-sm-12 candidate-screen-head" > Pricing details</div>

        </div>
        <div class="form-group row">
                        <div class="col-sm-4">Billing Frequency<span class="mandatory">*</span>
                        </div>
                        <div class="col-sm-4">
                                <select class="form-control" required id="contract_billing_cycle" name="contract_billing_cycle" required>
                                                <option value="">Select</option>
                                                @foreach  ($lookUps['contractbillingcyclerepository'] as $contractbillingcycle)
                                                        <option value="{{$contractbillingcycle->id}}">{{$contractbillingcycle->title}}</option>
                                                @endforeach
                                </select>
                        </div>
                        <div  class="col-sm-4"><label class="error text-danger" for="contract_billing_cycle"></label>   </div>
        </div>
        <div class="form-group row">
                        <div class="col-sm-4">Payment Method<span class="mandatory">*</span>
                        </div>
                        <div class="col-sm-4">
                                <select class="form-control" required id="contract_payment_method" name="contract_payment_method" required>
                                                <option value="">Select</option>
                                                @foreach  ($lookUps['contractpaymentmethodrepository'] as $contractpaymentmethod)
                                                        <option value="{{$contractpaymentmethod->id}}">{{$contractpaymentmethod->paymentmethod}}</option>
                                                @endforeach
                                </select>
                        </div>
                        <div  class="col-sm-4"><label class="error text-danger" for="contract_payment_method"></label>   </div>
        </div>
        <div class="form-group row statholidayhead">
                <div class="col-sm-12 candidate-screen-head" > Stat Holidays</div>

        </div>
        @foreach ($lookUps['holidayrepository'] as $holidays)
                @if($holidays->id > 0)
                <div class="form-group row">
                        <div class="col-sm-4">{{$holidays->holiday}}
                        </div>
                        <div class="col-sm-4">
                                <select class="form-control holidaypayment" required id="holiday-payment-{{$holidays->id}}" name="holiday-payment-{{$holidays->id}}">
                                       <option value="0">Select</option>
                                        @foreach  ($lookUps['holidaypaymentallocationrepository'] as $holidaypayments)
                                        <option value="{{$holidaypayments->id}}">{{$holidaypayments->paymentstatus}}</option>
                                        @endforeach
                               </select>
                        </div>
                        <div  class="col-sm-4">   </div>
                </div>
                @endif
        @endforeach

        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head" > PO Information</div>

        </div>
        <div class="form-group row">
                <div class="col-sm-4">Purchase Order (PO) Number<span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                        <input type="text" name="ponumber" id="ponumber" placeholder="Purchase Order" class="form-control" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="ponumber"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Company Name<span class="mandatory">*</span>
 </div>
                <div class="col-sm-4">
                        <input type="text" name="pocompanyname" id="pocompanyname" placeholder="Company Name" class="form-control" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="pocompanyname"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Attention Name

 </div>
                <div class="col-sm-4"><input type="text" name="poattentionto" id="poattentionto" placeholder="Attention Name" class="form-control" /></div>
                <div  class="col-sm-4"><label class="error text-danger" for="poattentionto"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Title

        </div>
                <div class="col-sm-4"><input type="text" name="potitle" id="potitle" placeholder="Title" class="form-control" /></div>
                <div  class="col-sm-4"><label class="error text-danger" for="potitle"></label>   </div>
        </div>
        <div class="form-group row">
                        <div class="col-sm-4">Mailing Address

         </div>
                        <div class="col-sm-4">
                                <textarea type="text" name="pomailingaddress" id="pomailingaddress"  class="form-control">
                                </textarea>
                                </div>
                        <div  class="col-sm-4">
                                   </div>
        </div>

        <div class="form-group row">
                <div class="col-sm-4">City

 </div>
                <div class="col-sm-4">
                        <input type="text" name="pocity" id="pocity" placeholder="City" class="form-control" />
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="pocity"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Postal Code

 </div>
                <div class="col-sm-4"><input type="text" name="popostalcode" id="popostalcode" placeholder="Postal Code" class="form-control postal-code" /></div>
                <div  class="col-sm-4"><label class="error text-danger" for="popostalcode"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Phone Number

 </div>
                <div class="col-sm-4"><input type="text" name="pophone" id="pophone" placeholder="Phone No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" /></div>
                <div  class="col-sm-4"><label class="error text-danger" for="pophone"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Email

 </div>
                <div class="col-sm-4"><input type="email" name="poemail" id="poemail" placeholder="Email Address" class="form-control" /></div>
                <div  class="col-sm-4"><label class="error text-danger" for="poemail"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Cell


 </div>
                <div class="col-sm-4"><input type="text" name="pocellno" id="pocellno" placeholder="Cell No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" /></div>
                <div  class="col-sm-4"><label class="error text-danger" for="pocellno"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Fax Number


 </div>
                <div class="col-sm-4"><input type="text" name="pofax" id="pofax" placeholder="Fax No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" /></div>
                <div  class="col-sm-4"><label class="error text-danger" for="pofax"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">Billing Notes
 </div>
                <div class="col-sm-4"><input type="text" name="ponotes" id="ponotes" placeholder="Billing Notes" class="form-control" /></div>
                <div  class="col-sm-4"><label class="error text-danger" for="ponotes"></label>   </div>
        </div>
        <div class="form-group row">
                <div class="col-sm-4">PO-Upload (Supports doc,docx,pdf,xls,xlsx,ods,ppt,pptx)</div>
                <div class="col-sm-4">
                        <input type="file" name="po_upload" id="po_upload" value="" class="form-control" />
                        <input type="hidden" name="po_document_attachment" id="po_document_attachment" value="0" />
                        <p id="pouploadlabel" style="display:none"></p>
                </div>
                <div  class="col-sm-1">
                        <button type="button" id="poupload" attr_file="po_upload" attr_hidden="po_document_attachment" class="button btn submit">Upload</button>
                           </div>
                           <div class="col-sm-3"><label class="error text-danger" for="po_upload"></label></div>
        </div>
        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head"> Supervisor Information

 </div>

        </div>
        <div class="form-group row">
                <div class="col-sm-4">Is there a supervisor assigned to site
        </div>
                <div class="col-sm-4">
                        <select class="form-control" id="supervisorassigned" name="supervisorassigned">
                                <option>Select any</option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                        </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="supervisorassigned"></label>   </div>
        </div>
        <div class="container-fluid" id="supervisorornot" style="display:none;padding:0">
                        <div class="form-group row">
                                        <div class="col-sm-4">Employee
                                </div>
                                        <div class="col-sm-4">

                                                        <select class="form-control" name="supervisoremployeenumber" required id="supervisoremployeenumber">
                                                                        <option value="">Select</option>

                                                                        @foreach ($lookUps['userlookuprepository'] as $key=>$value)
                                                                        @if($value['emp_no']!="")
                                                                                        <option data-empno="{{$value["emp_no"]}}"
                                                                                                        value="{{$value['id']}}">{{$value['emp_no']}} - {{$value['full_name']}} </option>
                                                                        @endif
                                                                        @endforeach

                                                        </select>
                                        </div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="supervisoremployeenumber"></label>   </div>
                                </div>
                                <div class="form-group row" style="display:none">
                                        <div class="col-sm-4">Employee Name
                                </div>
                                <div class="col-sm-4">
                                        <input type="text" readonly name="employeename" id="employeename" placeholder="Employee Name" class="form-control">
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="employeename"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">View Training/Performance/Profile, etc.
                                </div>
                                        <div class="col-sm-4">

                                                <select class="form-control" id="viewtrainingperformance" name="viewtrainingperformance">
                                                                <option>Select Any</option>
                                                                <option value="1">Yes</option>
                                                                <option value="0" selected>No</option>
                                                </select>
                                                </div>
                                                <div  class="col-sm-4"><label class="error text-danger" for="viewtrainingperformance"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Cell Phone
                         </div>
                                        <div class="col-sm-4">
                                                <input  type="text" name="employeecellphone" id="employeecellphone" placeholder="Cell No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone">
                                        </div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="employeecellphone"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Email Address</div>
                                        <div class="col-sm-4">
                                                <input  type="email" name="employeeemailaddress" id="employeeemailaddress" placeholder="Email Address" class="form-control">
                                        </div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="employeeemailaddress"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Telephone</div>
                                        <div class="col-sm-4">
                                                <input  type="text" name="employeetelephone" id="employeetelephone" placeholder="Phone No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone"></div>
                                                <div  class="col-sm-4"><label class="error text-danger" for="employeetelephone"></label>   </div>
                                        </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Fax Number
                                </div>
                                        <div class="col-sm-4"><input type="text" name="employeefaxno" id="employeefaxno" placeholder="Fax No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone    "></div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="employeefaxno"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Who provides the cell phone
                                </div>
                                <div class="col-sm-4">
                                        <select class="form-control" name="contractcellphoneprovider" required id="contractcellphoneprovider" placeholder="Select">
                                                <option value="">Select</option>

                                                @foreach ($lookUps['contractcellphoneproviderrepository'] as $cellphoneprovider)
                                                                <option   value="{{$cellphoneprovider->id}}">{{$cellphoneprovider->providername}} </option>
                                                @endforeach

                                        </select>
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="contractcellphoneprovider"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Tablet Required </div>
                                        <div class="col-sm-4">
                                                <select class="form-control" id="supervisortabletrequired" name="supervisortabletrequired">
                                                                <option value="0">Select Any</option>
                                                                <option value="1">Yes</option>
                                                                <option value="0" selected="">No</option>
                                                </select>
                                        </div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="supervisortabletrequired"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">CGL 360 in Use</div>
                                        <div class="col-sm-4">
                                                <select class="form-control" id="supervisorcgluser" name="supervisorcgluser">
                                                                <option>Select Any</option>
                                                                <option value="1">Yes</option>
                                                                <option value="0" selected="">No</option>
                                                </select>
                                        </div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="supervisorcgluser"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Public Transport or Car Required </div>
                                        <div class="col-sm-4">
                                                <select class="form-control" id="supervisorpublictransportrequired" name="supervisorpublictransportrequired">
                                                        <option>Select Any</option>
                                                        <option value="1">Yes</option>
                                                        <option value="0" selected="">No</option>
                                                </select>
                                        </div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="supervisorpublictransportrequired"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Directions or Nearest Intersection</div>
                                        <div class="col-sm-4"><input type="text" name="direction_nearest_intersection" id="direction_nearest_intersection" placeholder="Nearest Intersection" class="form-control"></div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="direction_nearest_intersection"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Department at Site</div>
                                        <div class="col-sm-4"><input type="text" name="department_at_site" id="department_at_site" placeholder="Department at Site" class="form-control"></div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="department_at_site"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Delivery Hours</div>
                                        <div class="col-sm-4"><input type="number" name="delivery_hours" id="delivery_hours" placeholder="Delivery Hours" class="form-control"></div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="delivery_hours"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Can Mail be Sent  </div>
                                        <div class="col-sm-4">
                                                        <select class="form-control" id="supervisorcanmailbesent" name="supervisorcanmailbesent">
                                                                        <option>Select Any</option>
                                                                        <option value="1">Yes</option>
                                                                        <option value="0" selected="">No</option>
                                                        </select>
                                        </div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="supervisorcanmailbesent"></label>   </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Computer/Internet Access
                                </div>
                                        <div class="col-sm-4">
                                                        <select class="form-control" name="contractdeviceaccess" required id="contractdeviceaccess" placeholder="Select">
                                                                        <option value="">Select</option>

                                                                        @foreach ($lookUps['contractdeviceaccessrepository'] as $contractdeviceaccess)
                                                                                        <option   value="{{$contractdeviceaccess->id}}">{{$contractdeviceaccess->DeviceType}} </option>
                                                                        @endforeach

                                                        </select>
                                        </div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="contractdeviceaccess"></label>   </div>
                                </div>


                </div>
                <div class="container-fluid" style="padding:0">
                                <div class="form-group row">
                                                <div class="col-sm-12 candidate-screen-head">Scope of Work
                                </div></div>
                                <div class="form-group row">
                                <div class="col-sm-4">Scope of Work<span class="mandatory">*</span></div>
                                <div class="col-sm-4">
                                        <textarea maxlength="3000" class="form-control" name="scopeofwork" id="scopeofwork" rows="7" style="resize:none"></textarea>
                                        <small>(maximum 3000 chars)</small>
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="scopeofwork"></label>   </div>

                        </div>
                <div class="container-fluid" id="contractamendments0" style="padding:0">
                        <div class="form-group row">
                                <div class="col-sm-12 candidate-screen-head"> Amendments
                                        <input type="hidden" name="amendmentcount" id="amendmentcount" value="1" />
                                </div>
                        </div>
                                <div class="form-group row">
                                    <div class="col-sm-4">Description</div>
                                    <div class="col-sm-4">
                                            <textarea id="amendment_description" name="amendment_description" class="form-control"  rows="7" style="resize:none" maxlength="3000"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Attachment (Supports doc,docx,pdf,xls,xlsx,ods,ppt,pptx)</div>
                                        <div class="col-sm-4">
                                                <input type="file"  name="amendment_attachment_id" id="amendment_attachment_id" class="form-control" />
                                                <input type="hidden" name="amendment_document_attachment" id="amendment_document_attachment" value="">
                                                <p id="amendmentuploadlabel" style="display:none"></p>
                                        </div>
                                        <div class="col-sm-1">
                                        <button type="button" id="amendment_attachment_button_id"
                                        attr_file="amendment_attachment_id"
                                        attr_hidden="amendment_document_attachment"
                                        attr_file_input_val="amendment_document_attachment"
                                        class="button btn submit uploadamend">Upload</button>
                                        </div>
                                        <div class="col-sm-3">
                                        <label class="error text-danger" for="amendment_attachment_id"></label>
                                        </div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4"></div>
                                        <div class="col-sm-4">
                                                <button type="button" attr-descid="amendment_description"
                                                attr-attachid="amendment_attachment_id"
                                                attr-attachremoveid="amendment_attachment_button_id"
                                                attr-blockid="contractamendments" class="button btn submit removeprimarycontent">-</button>
                                        </div>
                                </div>
                </div>

                        <div class="form-group row" id="addmoreamendmentblock">
                                <div class="col-sm-5"></div>
                                <div class="col-sm-7" >
                                        <button type="button" id="addmoreammendments" class="btn btn-primary  btnwidthfull">+Add More Amendments</button></div>

                        </div>




                <div class="form-group row">
                        <div class="col-sm-5"></div>
                        <div class="col-sm-7" >
                                {{ Form::submit('Submit', array('class'=>'button btn submit btnwidthhalf','id'=>'save'))}}
                                {{ Form::button('Cancel', array('class'=>'btn cancel btnwidthhalf', 'type'=>'reset','onClick'=>'window.history.back();'))}}
                        </div>

                    </div>
                    <div class="form-group row">
                            <div class="col-sm-12">   </div>
                    </div>
         </div>
        </div>


</form>

<div id="cmufcontract" style="display:none">

</div>
@section('scripts')
<script>
        $(window).on('load',function(){

                $("#customer_client").select2();
                $("#reason_for_submission").select2();
                $("#line_of_business").select2();
                $("#business_segment").select2();
                $("#master_customer").select2();
                $("#customer_client_regional_manager").select2();
                $("#office_address").select2();
                $("#office_address_sales").select2();
                $("#division_bid_lookup").select2();
                $("#division_lookup").select2();

                $("#sales_employee_id").select2();

                $("#sales_contact_division").select2();
                $("#sales_contact_office_address").select2();
                $("#primary_contact").select2();

                $("#billing_ratechange").select2();
                $("#contract_billing_cycle").select2();
                $("#contract_payment_method").select2();

                $("#supervisoremployeenumber").select2();
                $("#contractcellphoneprovider").select2();
                $("#contractdeviceaccess").select2();
                $(".holidaypayment").select2();
                //$("#contact_jobtitle").select2();
                $("#area_manager_position_text").select2();
                $("#sales_contact_job_title").select2();

        });




        $("#uploadcontract").on('click',function(evt){
            $('label').html("");
            evt.preventDefault();
            var selection = $("#yes_no").val();

            var hiddeninput = $(this).attr("attr_hidden");
            if(selection == 1 && $("#cmuf_contract_document").val()!="")
            {
                    var formulario = $('#uploadform-data')[0];
                    var formData = new FormData(formulario);
                    var self =this;

                    formData.append("upload_file","cmuf_contract_document");
                    $.ajax({
                            type: "post",
                            url: "{{route('contracts.attachfile')}}",
                            data: formData,
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                    if(parseInt(response)>0)
                                    {
                                        $("#"+hiddeninput).val(response);
                                        $("#uploadform").show();

                                        refreshSideMenu();
                                        //$(self).off('click');
                                        $("#yes_no").css("display","none");
                                        $("#yes").css("display","block");
                                        var filename = $("#cmuf_contract_document").val().replace(/.*(\/|\\)/, '');

                                        $("#fname").html(filename);
                                        $("#cmuf_contract_document").css("display","none");
                                        $("#fname").css("display","block")
                                        $(self).hide();
                                        $("#cmuf_contract_document").val("")

                                    }
                                    $(".error").html("");

                            }
                    }).fail(function(data){
                        var response = JSON.parse(data.responseText);

                        $(".error").html("");

                        $.each( response.errors, function( key, value) {

                        var errorString = '<ul>';
                        errorString += '<li>' + value + '</li>';
                        var labelfor = $('label[for="' + $("#"+key).attr('id') + '"]');
                        $(labelfor).html(errorString);
                        });

                });


            }
            else if($("#cmuf_contract_document").val()=="" && selection == "1")
            {
                        $('label[for="cmuf_contract_document"]').html("*Contract cannot be empty");
            }
            else{

                 $("#uploadform").slideUp();
                 //$('label[for="yes_no"]').html("*Please choose Yes and proceed");
                 swal("Warning", "Selection should be yes to proceed", "warning");


            }

        });
        var rfcuploadevent = function(event){
                $('label').html("");
                var hiddeninput = $("#uploadrfc").attr("attr_hidden");

                var formulario = $('#uploadform-data')[0];
                var self ="#uploadrfc";
                var formData = new FormData(formulario);
                formData.append("upload_file","rfc_pricing_template");
                $.ajax({
                        type: "post",
                        url: "{{route('contracts.attachfile')}}",
                        data: formData,
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        success: function (response) {
                                $("#"+hiddeninput).val(response);
                                //$(self).off('click');
                                //$("#uploadrfc").hide();
                                $(self).hide();
                                $("#rfc_pricing_template").hide();
                                $("#rfcuploadlabel").html($("#rfc_pricing_template").val().split('\\').pop()+'&nbsp;&nbsp;&nbsp;&nbsp; <a class="btn submit" style="cursor:pointer;color:#fff,font-weight:bold" id="resetrfc">Remove</a> ');
                                $("#rfcuploadlabel").show();
                                $("#rfc_pricing_template").val("");


                        }
                }).fail(function(data){

                        var response = JSON.parse(data.responseText);


                        $(".error").html("");

                        $.each( response.errors, function( key, value) {
                        var errorString = '<ul>';
                        errorString += '<li>' + value + '</li>';
                        var labelfor = $('label[for="' + $("#"+key).attr('id') + '"]');
                        $(labelfor).html(errorString);
                        });

                }).done(function(event){
                        var savemode = getCookie("savemode");
                        $("#resetrfc").on("click",function(event){
                                $("#rfcuploadlabel").html("").show();
                                $("#rfc_pricing_template").val("").show();
                                $("#rfc_document_attachment").val("");
                                $("#uploadrfc").show();
                        })
                        if(savemode == "1")
                        {
                                //$("#save").trigger("click");
                                //handleformsave(event);
                                $("#ongoingupload").val(parseInt($("#ongoingupload").val())-1);


                        }
                });
        }
        $("#uploadrfc").on('click',function(event){
                var filename = $("#rfc_pricing_template").val();
                if(filename =="")
                {
                        swal("Warning", "Please choose a file", "warning");
                }
                else
                {
                rfcuploadevent(event);
                }
        });

        $("#poupload").on('click',function(event){
                var filename = $("#po_upload").val();
                if(filename =="")
                {
                        swal("Warning", "Please choose a file", "warning");
                }
                else
                {
                   pouploadevent(event);
                }

        });


        var amendfileupload = function(event,self){

                var buttonid = $(self).attr("id") ;
                var self = self;
                var input = $(self).attr("attr_file");
                var fileinputval = $(self).attr("attr_file_input_val");
                var filename = /([^\\]+)$/.exec($("#"+input).val())[1];
                var hiddeninput = $(self).attr("attr_hidden");
                var formulario = $('#uploadform-data')[0];
                var formData = new FormData(formulario);
                formData.append("upload_amend","amendments");
                formData.append("upload_file",input);
                $.ajax({
                        type: "post",
                        url: "{{route('contracts.attachfile')}}",
                        data: formData,
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        success: function (response) {
                                $("#"+hiddeninput).val(response);
                                $(self).hide();
                                //$("#"+buttonid).off('click');
                                $("#"+input).hide();
                                if(input!="amendment_attachment_id")
                                {
                                     var labelid = input.replace("amendment_attachment_id_","amendmentuploadlabel_");
                                }
                                else{
                                        var labelid = "amendmentuploadlabel";
                                }
                                $("#"+input).val("");
                                $("#"+labelid).html($("#"+input).val().split('\\').pop()+filename+' <a attr-labelid="'+labelid+'" attr_filecontrolid="'+input+'" attr_filevaluecontrolid="'+fileinputval+'" attr_buttoncontrolid="'+buttonid+'" class="resetamendment btn submit" style="cursor:pointer;color:#fff" >Remove</a> ');
                                $("#"+labelid).show();
                        }
                }).fail(function(data){

                        var response = JSON.parse(data.responseText);
                        $(".error").html("");
                        $.each( response.errors, function( key, value) {

                        var errorString = '<ul>';
                        errorString += '<li>' + value + '</li>';
                        var labelfor = $('label[for="' + $("#"+key).attr('id') + '"]');
                        $(labelfor).html(errorString);
                        });

                }).done(function(event){

                });


        }

        $("#amendment_attachment_button_id").on('click',function(event){
                var self =this;
                var filename = $(this).attr("attr_file");
                if($("#"+filename).val()=="")
                {
                        swal("Warning", "Please choose a file", "warning");
                }
                else
                {
                  amendfileupload(event,self);
                }

        })

        $(document).on("change","#yes_no",function(event){
            event.preventDefault();
            var selection = $("#yes_no").val();
            if(selection == 1)
            {
                $("#yesbar").show();
            }
            else{
                $("#uploadform").slideUp();
                $("#yesbar").hide();
            }
        });

        $(document).on("click",".resetamendment",function(event){
                                var filecontrol = $(this).attr("attr_filecontrolid");
                                var buttoncontrol = $(this).attr("attr_buttoncontrolid");
                                var filevalue = $(this).attr("attr_filevaluecontrolid");
                                var hiddencontrol = buttoncontrol.replace("amendment_attachment_id","amendment_document_attachment");
                                $("#"+$(this).attr("attr-labelid")).html("");
                                $("#"+filecontrol).val("").show();
                                $("#"+hiddencontrol).val("0");
                                $("#"+filevalue).val("");
                                $("#"+buttoncontrol).show();
                        });
        $("#sales_employee_id").on('select2:select',function(e){

                if($(this).val()=="")
                {
                        $("#sales_contact_job_title").val("");
                        $("#sales_contact_emailaddress").val("");
                        $("#sales_contact_office_number").val("");
                        $("#sales_contact_cell_number").val("");
                        $("#sales_contact_faxno").val("");
                }
                else{
                        $.ajax({
                                type: "post",
                                url: "{{ route('contracts.get-user-details')}}",
                                data: {"userid":$(this).val()},
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                     var primarycontact = $.parseJSON(response);
                                     var position = primarycontact.positionid;

                                     $("#sales_contact_emailaddress").val(primarycontact.email);
                                     $("#sales_contact_office_number").val(primarycontact.officenumber);
                                     $("#sales_contact_cell_number").val(primarycontact.cellnumber);
                                     //$("#sales_contact_faxno").val(primarycontact.faxnumber);
                                     var salesmanagerpositiontext = $("#area_manager_position_text").find("[id-value='" + position + "']").val();


                                     $("#sales_contact_job_title").val(position).select2().trigger("change");


                                }
                        });
                }
        });

        $("#primary_contact").on('select2:select',function(){

                if($(this).val()=="")
                {
                        $("#contact_jobtitle").val("");
                        $("#contact_name").val("");
                        $("#contact_emailaddress").val("");
                        $("#contact_phoneno").val("");
                        $("#contact_cellno").val("");
                        //$("#contact_faxno").val("");
                }
                else
                {
                        $.ajax({
                                type: "post",
                                url: "{{ route('contracts.get-user-details')}}",
                                data: {"userid":$(this).val()},
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                     var primarycontact = $.parseJSON(response);
                                     var position = primarycontact.positionid;
                                     $("#contact_jobtitle").val("Client");
                                     $("#contact_name").val(primarycontact.name);
                                     $("#contact_emailaddress").val(primarycontact.email);
                                     $("#contact_phoneno").val(primarycontact.officenumber);
                                     $("#contact_cellno").val(primarycontact.cellnumber);
                                     //$("#contact_faxno").val(primarycontact.faxnumber);

                                }
                        });
                }
        });

        $("#supervisoremployeenumber").on('select2:select',function(){

                if($(this).val()=="")
                {
                        $("#employeename").val("");
                        $("#employeeemailaddress").val("");
                        $("#employeetelephone").val("");
                        $("#employeecellphone").val("");
                        //$("#employeefaxno").val("");
                }
                else
                {
                        $.ajax({
                                type: "post",
                                url: "{{ route('contracts.get-user-details')}}",
                                data: {"userid":$(this).val()},
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                     var primarycontact = $.parseJSON(response);
                                     var position = primarycontact.positionid;
                                     $("#employeename").val(primarycontact.name);
                                     $("#employeeemailaddress").val(primarycontact.email);
                                     $("#employeetelephone").val(primarycontact.officenumber);
                                     $("#employeecellphone").val(primarycontact.cellnumber);
                                     //$("#employeefaxno").val(primarycontact.faxnumber);

                                }
                        });
                }
        });

        $(document).on('change','#customer_client',function(){
                $.ajax({
                        type: "post",
                        url: "{{route('contracts.clientdetails')}}",
                        data: {"clientid":$(this).val()},
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                                var clientjson = $.parseJSON(response);
                                var areamanagerpositiontext = $("#area_manager_position_text").find("[id-value='" + clientjson.regionalmanagerpositionid + "']").val();

                                $("#contract_number").val(clientjson.contract_number);
                                var areamanagername = "";
                                if(clientjson.regionalmanagerfirstname!=null)
                                {
                                        areamanagername = clientjson.regionalmanagerfirstname;
                                }

                                if(clientjson.regionalmanagerlastname!=null)
                                {
                                        areamanagername+= " "+clientjson.regionalmanagerlastname;
                                }

                                $("#area_manager_text").val(areamanagername);
                                $("#area_manager").val(areamanagername);
                                $("#area_manager_position_id").val(clientjson.regionalmanagerpositionid);
                                $("#area_manager_email_address").val(clientjson.regionalmanageremailid);
                                $("#area_manager_office_number").val(clientjson.regionalmanagerphone);
                                $("#area_manager_cell_number").val(clientjson.regionalmanagercell);
                                $("#contract_startdate").val(clientjson.projectopendate);
                                $("#area_manager_id").val(clientjson.regionalmanagerid);
                                //$("#area_manager_position_text option[id-value='"+clientjson.regionalmanagerpositionid+"']").attr('selected','selected');;
                                //$("#area_manager_position_text").select2().val(clientjson.regionalmanagerpositionid).trigger("change");


                                $("#area_manager_fax_number").val("");
                                //$("#contact_faxno").val("");

                                var areamanagerposition = function(areamanagerpositiontext)
                                {
                                     $("#area_manager_position_text").val(areamanagerpositiontext).select2().trigger("change");
                                     //alert(areamanagerpositiontext);
                                }

                                setTimeout(areamanagerposition, 1000, areamanagerpositiontext);


                        }
                }).done(function(data){

                });
        });

        $(document).on('click','#multidivision',function(){
                var value = $(this).val();
                if(value == true){
                        $("#division_lookup").prop("disabled",false);
                        $("#leaddiv").slideDown("slow");
                }
                else{
                        $("#division_lookup").prop("disabled",true);
                        $("#leaddiv").slideUp("slow");
                }
        });

        $(document).on('click','#masterentity',function(){
                var value = $(this).val();
                if(value == true){
                        $("#master_customer").prop("disabled",false);
                        $("#parentdiv").slideDown("slow");
                }
                else{
                        $("#master_customer").prop("disabled",true);
                        $("#parentdiv").slideUp("slow");
                }
        });

        $("#renewable_contract").on('change',function(){
                var value = $(this).val();

                $("#contract_length_renewal_years").val("0");
                if(value == 1){
                        $("#renewableyears").show();
                        $("#contract_length_renewal_years").prop("readonly",false);
                }
                else{
                        $("#renewableyears").hide();
                        $("#contract_length_renewal_years").prop("readonly",true);
                }
        })

        $("#termination_clause_client").on('change',function(){
                var value = $(this).val();

                $("#terminationnoticeperiodclient").val("0");
                if(value == 1){
                        $("#terminationnoticeperiodclientdiv").show();
                        $("#terminationnoticeperiodclient").prop("readonly",false);
                }
                else{
                        $("#terminationnoticeperiodclientdiv").hide();
                        $("#terminationnoticeperiodclient").prop("readonly",true);
                }
        })

        $("#termination_clause").on('change',function(){
                var value = $(this).val();

                $("#terminationnoticeperiod").val("0");
                if(value == 1){
                        $("#terminationnoticeperioddiv").show();
                        $("#terminationnoticeperiod").prop("readonly",false);
                }
                else{
                        $("#terminationnoticeperioddiv").hide();
                        $("#terminationnoticeperiod").prop("readonly",true);
                }
        })

        var cmufsaveevent = function(event){
                var formulario = $('#uploadform-data')[0];
                var formData = new FormData(formulario);
                var amendmentcount = $("amendmentcount").val();
                $.ajax({
                        type: "post",
                        url: "{{ route('contracts.storecontractform')}}",
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {

                             $("#uploadform-data").hide().after(function(){
                                     var jsonvalues = $.parseJSON(response);
                                     var insertedid = jsonvalues.lastinserted;
                                     $.ajax({
                                             type: "get",
                                             url: "{{route('contracts.previewcontract')}}",
                                             data: jsonvalues,
                                             headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                             },
                                             success: function (response) {
                                                   $("#cmufcontract").slideDown().html(response);

                                             }
                                     }).done(function(event){
                                               setTimeout(() => {
                                                       swal.close();
                                               }, 500);

                                                unlinkPersistantzerofiles();
                                     });

                             });
                        }

                }).fail(function(data){
                        swal.close();
                        var response = JSON.parse(data.responseText);
                        $(".error").html("");
                        var firstid = "";
                        var i = 0;
                        $.each( response.errors, function( key, value) {

                        var errorString = '<ul>';
                        errorString += '<li>' + value + '</li>';
                        var labelfor = $('label[for="' + $("#"+key).attr('id') + '"]');
                        $(labelfor).html(errorString);
                        i++;
                        if(i==1)
                        {
                            firstid=key;
                        }

                        });
                        if(firstid!="")
                        {
                                if(firstid=="rfc_document_attachment")
                                {
                                        scrolltofunction("rfc_pricing_template");
                                }
                                else
                                {
                                        scrolltofunction(firstid);
                                }

                                $("#"+firstid).focus();

                        }



                         //

                }).done(function(event){
                        setTimeout(() => {

                                refreshSideMenu();
                        }, 2000);

                });

        }

        var scrolltofunction = function (elemid) {
                $('html, body').stop(true, false).animate({
                                scrollTop: $("#"+elemid).offset().top
                }, 1000);
                $("#"+elemid).focus();
          }


          var unlinkPersistantzerofiles = function(event){

          }

          function setCookie(cname, cvalue, exdays)
          {
                var d = new Date();
                d.setTime(d.getTime() + (exdays*24*60*60*1000));
                var expires = "expires="+ d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
           }

           function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                        }
                }
                return "";
           }

        $(document).on('click','#save',function(event){
                event.preventDefault();
                swal({
                    title: "Confirm ",
                    text: "Do you want to proceed ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    if(true){
                        handleformsave(event);
                    }
                });






        });



        var savefunction = function(event){
                var ongoingupload = $("#ongoingupload").val();
                if(ongoingupload<0)
                {
                        handleformsave(event);
                }
                else
                {
                        setTimeout(savefunction(event),1500);
                }
        }

        handleformsave = function(event){
                //setCookie("savemode", "0", 1);
                $('label').html("");
                var event = event;
                $("#savemode").val("1");
                var multidivision = $("#multidivision").val();
                var masterentity = $("#masterentity").val();

                var clientblockcount = $("#clientcontactcount").val();

                var filevalidation = filevalid(event);
                if(filevalidation < 1)
                {
                     cmufsaveevent(event);
                }else{
                        swal.close();
                }



        }

        var filevalid = function(event){
                var returnflag = 0;
                var lastamendid = "amendmentcount";
                var amendmentcount = $("#amendmentcount").val();
                if($("#amendment_attachment_id").val()!="" && $("#amendment_document_attachment").val()<1)
                {
                        returnflag = parseInt(returnflag)+1;
                        //$('label [for="amendment_attachment_id"]').html("Hi");
                        $('label[for="amendment_attachment_id"]').html("Please upload the amendment");
                }
                for($i=0;$i<parseInt(amendmentcount)+1;$i++)
                {
                       if($("#amendment_attachment_id_"+$i).val()!="" && $("#amendment_document_attachment_"+$i).val()<1)
                       {
                            returnflag = parseInt(returnflag)+1;
                            $('label[for="amendment_attachment_id_'+$i+'"]').html("Please upload the amendment");
                            lastamendid = "amendment_attachment_id_"+$i;
                       }
                }

                if($("#po_upload").val()!="" && $("#po_document_attachment").val()<1)
                {
                        returnflag = parseInt(returnflag)+1;
                        //$('label [for="amendment_attachment_id"]').html("Hi");
                        $('label[for="po_upload"]').html("Please upload the Purchase order");
                        lastamendid = "po_upload";
                }

                if($("#rfc_pricing_template").val()!="" && $("#rfc_document_attachment").val()<1)
                {
                        returnflag = parseInt(returnflag)+1;
                        //$('label [for="amendment_attachment_id"]').html("Hi");
                        $('label[for="rfc_pricing_template"]').html("RFP Template file is mandatory ");
                        lastamendid = "rfc_pricing_template";
                }




                if(returnflag>0)
                {
                    scrolltofunction(lastamendid);
                }

                return returnflag;
        }

        $(document).on('change','#supervisorassigned',function(event){
                var choice = $(this).val();
                if(choice == 1)
                {

                        $("#supervisorornot").css('display','block');
                        refreshSideMenu();
                }
                else
                {
                        $("#supervisorornot").css('display','none');
                }
        });

        $(document).on('click','#addmoreclient',function(event){
                var url = "{{route('contracts.addmoreclientblock')}}";
                var contactcontrol = $("#primary_contact").html();
                var jobtitlecontrol = $("#contact_jobtitle").html();
                var data = {"clientcontactcount":$("#clientcontactcount").val()};
                var countattrtibute = $(this).attr("countattr");
                $.ajax({
                        type: "post",
                        url: url,
                        data: {"clientcontactcount":$("#clientcontactcount").val()},
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                                $("#remclientblock").show();
                          $(response).insertBefore("#addmoreclientblock");
                          var controlid = "primary_contact_"+$("#clientcontactcount").val();

                          $("#"+controlid).select2();
                          $("#"+controlid).on('select2:select',function(event){


                                if($(this).val()=="")
                                {
                                        $("#contact_jobtitle_"+countattrtibute).val("");
                                        $("#contact_name_"+countattrtibute).val("");
                                        $("#contact_emailaddress_"+countattrtibute).val("");
                                        $("#contact_phoneno_"+countattrtibute).val("");
                                        $("#contact_cellno_"+countattrtibute).val("");
                                        //$("#contact_faxno_"+countattrtibute).val("");
                                }
                                else
                                {
                                        $.ajax({
                                                type: "post",
                                                url: "{{ route('contracts.get-user-details')}}",
                                                data: {"userid":$(this).val()},
                                                headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                },
                                                success: function (response) {

                                                var countattrtibute = $("#"+controlid).attr("countattr") ;
                                                var primarycontact = $.parseJSON(response);
                                                var position = primarycontact.positionid;


                                                $("#contact_jobtitle_"+countattrtibute).val("Client");
                                                $("#contact_name_"+countattrtibute).val(primarycontact.name);
                                                $("#contact_emailaddress_"+countattrtibute).val(primarycontact.email);
                                                $("#contact_phoneno_"+countattrtibute).val(primarycontact.officenumber);
                                                $("#contact_cellno_"+countattrtibute).val(primarycontact.cellnumber);
                                                $("#contact_faxno_"+countattrtibute).val(primarycontact.faxnumber);
                                                var areamanagerpositiontext = $("#contact_jobtitle_"+countattrtibute).find("[value='" + position + "']").val();
                                                // $("#contact_jobtitle_"+countattrtibute).select2().trigger("change");
                                                }
                                        });
                                }


                          })
                          $("#clientcontactcount").val(parseInt($("#clientcontactcount").val())+1);

                          //$(".primary_contact").select2();
                        }
                }).done(function(){
                        var controlid = $("#clientcontactcount").val()-1;

                        $("#contact_emailaddress_"+$("#clientcontactcount").val()-1).val("Email address");
                        setTimeout((controlid) => {
                                var controlid = $("#clientcontactcount").val()-1;
                                $(".phone").mask("(999)999-9999");

                        }, 500);
                        // $("#contact_jobtitle_"+controlid).select2();
                });
        });
        $("#remclientblock").on("click",function(event){
                                        event.preventDefault();

                                        var blockid = parseInt($("#clientcontactcount").val())-1;

                                        $("#block-"+blockid).remove();
                                        $("#clientcontactcount").val(parseInt($("#clientcontactcount").val())-1);
                                        if(blockid==1)
                                        {
                                                $(this).hide();
                                        }

                                });
        $(document).on('click','#addmoreammendments',function(event){
               event.preventDefault();

                var url = "{{route('contracts.addmoreamendmentsblock')}}";
                var data = {"amendmentcount":$("#amendmentcount").val()};
                $.ajax({
                        type: "post",
                        url: url,
                        data: {"amendmentcount":$("#amendmentcount").val()},
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {





                        }
                }).done(function(response){
                        $(response).insertBefore("#addmoreamendmentblock");

                        var blockcount = parseInt($("#amendmentcount").val());

                        $("#amendmentcount").val(parseInt($("#amendmentcount").val())+1);
                        $("#amendment_attachment_id_"+blockcount).on("change",function(){
                                //$("#amendment_attachment_button_id_"+blockcount).trigger("click");
                        });
                        var blockcount = parseInt($("#amendmentcount").val())-1;
                        var controlname = "amendment_attachment_button_id_"+blockcount;
                        var fileattachmentcontrol = "amendment_attachment_id_"+blockcount;
                        var amself = $("#amendment_attachment_button_id_"+blockcount);

                        setTimeout((response) => {
                                $("#"+controlname).on("click",function(event){
                                        var filename = $(this).attr("attr_file");
                                        if($("#"+filename).val()=="")
                                        {
                                                swal("Warning", "Please choose a file", "warning");
                                        }
                                        else
                                        {
                                           amendfileupload(event,amself);
                                        }

                                });

                        }, 1500);
                        /*
                        $("#"+fileattachmentcontrol).on("change",function(event){

                                var blockcount = parseInt($("#amendmentcount").val())-1;
                                var amself = $("#amendment_attachment_button_id_"+blockcount)
                                var attachmentid = $("#amendment_document_attachment_"+blockcount).val();
                                if(attachmentid>0)
                                {
                                        $("#"+controlname).show();
                                        $("#amendment_document_attachment_"+blockcount).val("0");
                                        $("#"+controlname).on("click",function(event){
                                                amendfileupload(event,amself);
                                        });

                                }
                                else
                                {
                                        //$("#"+controlname).trigger("click");
                                }



                          });
                        */



                });
        });

        $(".primary_contact").on('select2:select',function(){
                var countattrtibute = $(this).attr("countattr");
                if($(this).val()=="")
                {
                        $("#contact_jobtitle_"+countattrtibute).val("");
                        $("#contact_name_"+countattrtibute).val("");
                        $("#contact_emailaddress_"+countattrtibute).val("");
                        $("#contact_phoneno_"+countattrtibute).val("");
                        $("#contact_cellno_"+countattrtibute).val("");
                        $("#contact_faxno_"+countattrtibute).val("");
                }
                else
                {
                        $.ajax({
                                type: "post",
                                url: "{{ route('contracts.get-user-details')}}",
                                data: {"userid":$(this).val()},
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {

                                     var primarycontact = $.parseJSON(response);
                                     var position = primarycontact.positionid;


                                     $("#contact_jobtitle_"+countattrtibute).val("Client");
                                     $("#contact_name_"+countattrtibute).val(primarycontact.name);
                                     $("#contact_emailaddress_"+countattrtibute).val(primarycontact.email);
                                     $("#contact_phoneno_"+countattrtibute).val(primarycontact.officenumber);
                                     $("#contact_cellno_"+countattrtibute).val(primarycontact.cellnumber);
                                     $("#contact_faxno_"+countattrtibute).val(primarycontact.faxnumber);

                                }
                        });
                }
        });
        $(".markupval").on("keyup",function(e){
                var average_billrate = $("#average_billrate").val();
                var average_wagerate = $("#average_wagerate").val();
                var calcfield = 0;
                if(average_wagerate!="" && average_wagerate!=""){
                        calcfield = ((parseFloat(average_billrate)/parseFloat(average_wagerate))-1)*100 ;
                        if(calcfield.toFixed(2)<0){
                                $("#average_markup").val("0");
                        }else{
                                $("#average_markup").val(calcfield.toFixed(2));
                        }

                }
        })
        $(function(){
                $('.dollar').keypress(function(event) {
                        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                        event.preventDefault();
                        }
                        var input = $(this);
                        var oldVal = input.val();
                        var regex = new RegExp(input.attr('pattern'), 'g');

                        setTimeout(function(){
                        var newVal = input.val();
                        if(!regex.test(newVal)){
                        input.val(oldVal);
                        }
                        }, 0);
                });
                $('label').html("");

                $('.dollar').on('keydown', 'input[pattern]', function(e){


                });


        })

        $(document).on("keydown",".notdecimal",function(event){
                console.log(event.keyCode)
                if (event.keyCode < 48 || event.keyCode > 57)
        return false;
        });

        $("#rfc_pricing_template").on("change",function(event){
                var attachmentid = $("#rfc_document_attachment").val();
                if(attachmentid > 0)
                {
                        $("#uploadrfc").show();
                        $("#rfc_document_attachment").val("0");
                        /*
                        $("#uploadrfc").on("click",function(event){
                                rfcuploadevent(event);
                        });
                        */

                }
                else{
                        //$("#uploadrfc").trigger("click");
                }

        })
        var pouploadevent = function(event){
                $('label').html("");
                var hiddeninput = $("#poupload").attr("attr_hidden");
                var formulario = $('#uploadform-data')[0];
                var formData = new FormData(formulario);
                var self = "poupload";
                formData.append("upload_file","po_upload");
                $.ajax({
                        type: "post",
                        url: "{{route('contracts.attachfile')}}",
                        data: formData,
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        success: function (response) {
                                $("#"+hiddeninput).val(response);
                                //$(self).off('click');
                                $("#po_upload").hide();
                                $("#poupload").hide();
                                $("#pouploadlabel").html($("#po_upload").val().split('\\').pop()+'&nbsp;&nbsp;&nbsp; <a style="cursor:pointer;color:#fff" class="btn submit" id="resetpo">Remove</a> ');
                                $("#pouploadlabel").show();
                                $("#po_upload").html("");

                        }
                }).fail(function(data){
                        var response = JSON.parse(data.responseText);

                        $(".error").html("");

                        $.each( response.errors, function( key, value) {

                        var errorString = '<ul>';
                        errorString += '<li>' + value + '</li>';
                        var labelfor = $('label[for="' + $("#"+key).attr('id') + '"]');
                        $(labelfor).html(errorString);
                        });

                }).done(function(event){
                        var savemode = getCookie("savemode");

                        $("#resetpo").on("click",function(event){
                                $("#pouploadlabel").html("").hide();
                                $("#po_upload").val("").show();
                                $("#po_document_attachment").val("0");
                                $("#poupload").show();
                        })

                        if(savemode == "1")
                        {

                                //handleformsave(event)
                                $("#ongoingupload").val(parseInt($("#ongoingupload").val())-1);


                        }
                });
        }
        $("#po_upload").on("change",function(event){
                var attachmentid = $("#rfc_document_attachment").val();
                if(attachmentid > 0)
                {
                        $("#poupload").show();
                        $("#po_document_attachment").val("0");
                        /*
                                $("#poupload").on("click",function(event){
                                pouploadevent(event);
                                });
                        */

                }
                else{
                        //$("#poupload").trigger("click");
                }

        });

        $("#amendment_attachment_id").on("change",function(event){
                var self ="#amendment_attachment_button_id";

                var attachmentid = $("#amendment_document_attachment").val();
                if(attachmentid > 0)
                {
                        $("#amendment_attachment_button_id").show();
                        $("#amendment_document_attachment").val("0");
                        $("#amendment_attachment_button_id").on("click",function(event){
                                var filename = $(this).attr("attr_file");
                                if($("#"+filename).val()=="")
                                {
                                        swal("Warning", "Please choose a file", "warning");
                                }
                                else
                                {
                                amendfileupload(event,self);
                                }

                        });
                        //$("#amendment_attachment_button_id").trigger("click");
                }
                else{
                        //$("#amendment_attachment_button_id").trigger("click");
                }

        });

        $("#contract_startdate").on("change",function(event){
                var d = new Date($(this).val());
                if(d=="Invalid Date")
                {
                        $(this).val("");
                        $('label[for="contract_startdate"]').html("Invalid date")
                }
                else
                {
                        $('label[for="contract_startdate"]').html("");
                        var endd = new Date($("#contract_enddate").val());

                        if(endd < d && endd!="Invalid Date")
                        {
                                swal("Warning", "End date cannot be greater than start date", "warning");
                                $("#contract_enddate").val("");
                                $("#contract_enddate").focus();
                        }
                        else
                        {
                                //alert("Proper value");
                        }
                }
        });

        $("#contract_enddate").on("change",function(event){
                var d = new Date($(this).val());
                if(d=="Invalid Date")
                {
                        $(this).val("");
                        $('label[for="contract_enddate"]').html("Invalid date")
                }
                else{
                        $('label[for="contract_enddate"]').html("")
                        var startd = new Date($("#contract_startdate").val());

                        if(startd > d && startd!="Invalid Date")
                        {
                                swal("Warning", "End date cannot be greater than start date", "warning");
                                $("#contract_enddate").val("");
                                $("#contract_enddate").focus();
                        }
                        else
                        {
                                //alert("Proper value");
                        }
                }
        });

        $('textarea').keyup(function() {
                var length = $(this).val().length;
                if(length >3000)
                {

                    swal("Warning", "Cannot be more than 3000 characters", "warning");
                }
        })

        $(document).on("click",".removeprimarycontent",function(e){
                var descid = $(this).attr("attr-descid");
                var attachremove = $(this).attr("attr-attachremoveid");

                try {
                        $("a[attr_buttoncontrolid="+attachremove+"]").trigger("click");
                } catch (error) {
                    console.log("Error in hidden a");
                }
                $("#"+descid).val("");
                if($(this).attr("attr-blockid")!="contractamendments"){
                        $("#"+$(this).attr("attr-blockid")).hide();
                }

        })

        $("#swap").on("click",function(){
                $("#cmufcontract").toggle().css("height","2000");
                refreshSideMenu();
        });

        $(document).ready(function() {
                $('body').loading('stop');
        })
        </script>
@endsection
