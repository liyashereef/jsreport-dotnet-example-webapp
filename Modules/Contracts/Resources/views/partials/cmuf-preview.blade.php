


                            <div class="container-fluid">
                                        <div class="form-group row">
                                                        <div class="col-sm-12 candidate-screen-head"> Prerequisites

                                                        </div>

                                                </div>
                        </div>

                            <div class="form-group row">
                                    <div class="col-sm-4">Contract</div>
                                    <div class="col-sm-4">

                                                    <a style="color:black;text-decoration:none" href="{{config('app.url')}}/contracts/cmuf-filedownload?contract_id={{$contractid}}&file_id={{$contract_attachment_id}}&date={{$createddate}}&filetype=contract" target="_blank" >Contract file &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-download" aria-hidden="true"></i></a>
                                            </div>
                                    <div class="col-sm-4">
                                            </div>
                            </div>

                            <div class="container-fluid"  >
                            <div class="form-group row">
                            <div class="col-sm-12 candidate-screen-head" >Contract Information</div>

                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Contract Name</div>
                            <div class="col-sm-4">{{$contractdata->getContractname->client_name}}</div>
                            <div  class="col-sm-4"><label class="error" for="customer_client"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Contract Number</div>
                            <div class="col-sm-4">{{$contractdata->contract_number}}</div>
                            <div  class="col-sm-4"><label class="error" for="contract_number"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Submission Date</div>
                            <div class="col-sm-4">{{date("d-M-Y",strtotime($contractdata->submission_date))}}</div>
                            <div  class="col-sm-4">   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Regional Manager</div>
                            <div class="col-sm-4">
                                     {{$contractdata->area_manager}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="area_manager_text"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Reason for Submission</div>
                            <div class="col-sm-4">
                                     {{$contractdata->getReasonforsubmission->reason}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="reason_for_submission"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-12 candidate-screen-head" >Business Information</div>

                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What business segment does the contract fall under  </div>
                            <div class="col-sm-4">
                                           {{$contractdata->getBusinesssegment->segmenttitle}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="business_segment"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What line of business does the contract fall under  </div>
                            <div class="col-sm-4">
                                            {{$contractdata->getBusinessline->lineofbusinesstitle}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="line_of_business"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Is this a multi division contract </div>
                            <div class="col-sm-4">
                            @if($contractdata->multidivisioncontract ==1)
                            Yes
                            @else
                            No
                            @endif
                            </div>
                            <div  class="col-sm-4"><label class="error" for="multidivision"></label>   </div>
                            </div>

                            @if($contractdata->multidivisioncontract >0)
                            <div class="form-group row">
                            <div class="col-sm-4">Who is the lead division  </div>
                            <div class="col-sm-4">
                                    @if($contractdata->getLeadDivisionlookup!=null)
                                           {{$contractdata->getLeadDivisionlookup->division_name}}
                                    @endif
                            </div>
                            <div  class="col-sm-4"><label class="error" for="division_lookup"></label>   </div>
                            </div>
                            @endif
                            <div class="form-group row">
                            <div class="col-sm-4">Is there a master entity </div>
                            <div class="col-sm-4">
                                            @if($contractdata->master_entity ==1)
                                            Yes
                                            @else
                                            No
                                            @endif
                            </div>
                            <div  class="col-sm-4"><label class="error" for="masterentity"></label>   </div>
                            </div>
                            @if($contractdata->getParentcustomer!=null)
                            <div class="form-group row">
                            <div class="col-sm-4">Parent Project Number </div>
                            <div class="col-sm-4">

                                  {{$contractdata->getParentcustomer->project_number}} - {{$contractdata->getParentcustomer->client_name}}


                            </div>
                            <div  class="col-sm-4"><label class="error" for="master_customer"></label>   </div>
                            </div>
                            @endif
                            <div class="form-group row">
                            <div class="col-sm-12 candidate-screen-head" >Enter Regional Manager Information
                            </div>

                            </div>

                            <div class="form-group row">
                                    <div class="col-sm-4">Who is the Regional Manager assigned to the account </div>
                                    <div class="col-sm-4">
                                           {{$contractdata->area_manager}}
                                    </div>
                                    <div  class="col-sm-4"><label class="error" for="area_manager"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the person's job title </div>
                            <div class="col-sm-4">
                                     <!--
                                    <input type="text" class="form-control" id="area_manager_position_text" name="area_manager_position_text" value="" />
                                     -->
                                     {{$contractdata->area_manager_position_text}}

                                    <input type="hidden"  id="area_manager_position_id" name="area_manager_position_id" value="" />
                            </div>
                            <div  class="col-sm-4"><label class="error" for="area_manager_position_text"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the person's email address </div>
                            <div class="col-sm-4">
                                            {{$contractdata->area_manager_email_address}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="area_manager_email_address"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the person's office number
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->area_manager_office_number}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="area_manager_office_number"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the person's cell number
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->area_manager_cell_number}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="area_manager_cell_number"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the fax number associated with the individual
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->area_manager_fax_number}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="area_manager_fax_number"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Where is the contact's primary office located

                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->getOfficeAddressareamanager->addresstitle}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="office_address"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-12 candidate-screen-head" >Sales Information</div>

                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Who won the contract
                            </div>
                            <div class="col-sm-4">
                                    {{$salesuser    }}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="sales_employee_id"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the person's job title
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->getPositiontitle->position}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="sales_contact_job_title"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the person's email address
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->sales_contact_emailaddress}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="sales_contact_emailaddress"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the person's office number
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->sales_office_number}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="sales_contact_office_number"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the person's cell number
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->sales_cell_number}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="sales_contact_cell_number"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the fax number associated with the individual
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->sales_contact_faxno}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="sales_contact_faxno"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Which division won the bid

                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->getSalesDivisionlookup->division_name}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="sales_contact_division"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Where is the contact's primary office located

                            </div>
                            <div class="col-sm-4">

                                           {{$contractdata->getOfficeAddresssalesmanager->addresstitle}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="sales_contact_office_address"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-12 candidate-screen-head" >Client Contact Information

                            </div>

                            </div>
                            <div class="container-fluid" id="clientcontactinformation" style="padding:0">
                            <div class="form-group row candidate-screen-head" >
                                    <div class="col-sm-2">Client Contact</div>
                                    <div class="col-sm-2">Job Title</div>
                                    <div  class="col-sm-2">Email Address   </div>
                                    <div  class="col-sm-2">Office Number  </div>
                                    <div  class="col-sm-2">Cell Number   </div>
                                    <div  class="col-sm-2">Fax Number   </div>
                            </div>
                            @foreach ($contractclients as $contractclient)
                                    <div class="form-group row">
                                                    <div class="col-sm-2">{{$contractclient->contact_name}}</div>
                                                    <div class="col-sm-2">{{$contractclient->contact_jobtitle}}</div>
                                                    <div  class="col-sm-2">{{$contractclient->contact_emailaddress}}   </div>
                                                    <div  class="col-sm-2">{{$contractclient->contact_phoneno}}  </div>
                                                    <div  class="col-sm-2">{{$contractclient->contact_cellno}}   </div>
                                                    <div  class="col-sm-2">{{$contractclient->contact_faxno}}   </div>
                                    </div>
                            @endforeach





                            </div>

                            <div class="form-group row">
                            <div class="col-sm-12 candidate-screen-head" >Contract Terms</div>

                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Contract Start Date
                            </div>
                            <div class="col-sm-4">
                                            {{date("M-d-Y",strtotime($contractdata->contract_startdate))}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="contract_startdate"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Contract Length (Years)
                            </div>
                            <div class="col-sm-4">
                                    @if($contractdata->contract_length>0)
                                        {{str_replace(".00","",$contractdata->contract_length)}}
                                    @endif
                            </div>
                            <div  class="col-sm-4"><label class="error" for="contract_length"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Contract Expiry
                            </div>
                            <div class="col-sm-4">
                                            {{date("M-d-Y",strtotime($contractdata->contract_enddate))}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="contract_enddate"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Is there a renewal option
                            </div>
                            <div class="col-sm-4">
                                    @if($contractdata->renewable_contract>0)
                                    Yes
                                    @else
                                    No
                                    @endif
                            </div>
                            <div  class="col-sm-4"></div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">How long is the renewal option (Years)
                            </div>
                            <div class="col-sm-4">
                                            @if($contractdata->contract_length_renewal_years>0)
                                                    {{str_replace(".00","",$contractdata->contract_length_renewal_years)}}
                                                    @else

                                            @endif

                            </div>
                            <div  class="col-sm-4"></div>
                            </div>

                             <div class="form-group row">
                                <div class="col-sm-4">Does the client have a termination clause
                                </div>
                                <div class="col-sm-4">
                                        @if($contractdata->termination_clause_client>0)
                                        Yes
                                        @else
                                        No
                                        @endif
                                </div>
                                <div  class="col-sm-4"></div>
                                </div>
                                <div class="form-group row">
                                <div class="col-sm-4">Termination notice period (Days)
                                </div>
                                <div class="col-sm-4">
                                                @if($contractdata->terminationnoticeperiodclient>0)
                                                        {{$contractdata->terminationnoticeperiodclient}}
                                                        @else

                                                @endif

                                </div>
                                <div  class="col-sm-4"></div>
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-4">Does service provider have a termination clause
                                        </div>
                                        <div class="col-sm-4">
                                                @if($contractdata->termination_clause>0)
                                                Yes
                                                @else
                                                No
                                                @endif
                                        </div>
                                        <div  class="col-sm-4"></div>
                                        </div>
                                        <div class="form-group row">
                                        <div class="col-sm-4">Termination notice period (Days)
                                        </div>
                                        <div class="col-sm-4">
                                                        @if($contractdata->terminationnoticeperiod>0)
                                                                {{$contractdata->terminationnoticeperiod}}
                                                                @else

                                                        @endif

                                        </div>
                                        <div  class="col-sm-4"></div>
                                        </div>

                            <div class="form-group row">
                            <div class="col-sm-4">How often will the pay/bill rate change
                            </div>
                            <div class="col-sm-4">
                                          {{$contractdata->getBillingratechange->ratechangetitle}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="billing_ratechange"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">What is the annual increase allowable
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->contract_annualincrease_allowed}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="contract_annualincrease_allowed"></label>   </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">Contract written template </div>
                                <div class="col-sm-4">
                                {{$contractonourtemplatetitle}}
                                </div>
                                <div  class="col-sm-4"><label class="error" for="multidivision"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-12 candidate-screen-head" >Pricing Definition</div>

                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Load RFP Pricing Template
                            </div>
                            <div class="col-sm-4">

                            <a style="color:black;text-decoration:none" href="{{config('app.url')}}/contracts/cmuf-filedownload?contract_id={{$contractid}}&file_id={{$rfc_pricing_tamplate_attachment_id}}&date={{$createddate}}&filetype=rfc" target="_blank" >RFP Template &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-download" aria-hidden="true"></i></a>


                            </div>
                            <div  class="col-sm-4"><label class="error" for="rfc_pricing_template"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Total Annual Contract Billing
                            </div>
                            <div class="col-sm-4">

                                {{str_replace(".00","",$contractdata->total_annual_contract_billing)}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="total_annual_contract_billing"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Total Annual Contract Wages & Benefits
                            </div>
                            <div class="col-sm-4">
                                {{str_replace(".00","",$contractdata->total_annual_contract_wages_benifits)}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="total_annual_contract_wages_benifits"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Total Annual (Expected) Contribution Margin
                            </div>
                            <div class="col-sm-4">
                                {{str_replace(".00","",$contractdata->total_annual_expected_contribution_margin)}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="total_annual_expected_contribution_margin"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Total Hours per Week

                            </div>
                            <div class="col-sm-4">
                                @if ($contractdata->total_hours_perweek>0)
                                        {{ str_replace(".",":",$tothoursperweek)}}
                                @endif


                            </div>
                            <div  class="col-sm-4"><label class="error" for="total_hours_perweek"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Average Bill Rate

                            </div>
                            <div class="col-sm-4">
                                {{str_replace(".00","",$contractdata->average_billrate)}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="average_billrate"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Average Wage Rate

                            </div>
                            <div class="col-sm-4">
                                {{str_replace(".00","",$contractdata->average_wagerate)}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="average_wagerate"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Average Markup


                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->average_markup}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="average_markup"></label>   </div>
                            </div>
                            <div class="form-group row">
                                    <div class="col-sm-12 candidate-screen-head" >Pricing Details</div>

                            </div>
                            <div class="form-group row">
                                    <div class="col-sm-4">Billing Frequency
                                    </div>
                                    <div class="col-sm-4">
                                            {{$contractdata->getBillingFrequency->title}}
                                    </div>
                                    <div  class="col-sm-4"><label class="error" for="contract-billing-cycle"></label>   </div>
                            </div>
                            <div class="form-group row">
                                    <div class="col-sm-4">Payment Method
                                    </div>
                                    <div class="col-sm-4">
                                            {{$contractdata->getPaymentmethod->paymentmethod}}
                                    </div>
                                    <div  class="col-sm-4"><label class="error" for="contract-payment-method"></label>   </div>
                            </div>
                            @if(count($contractholidayagreement)>0)
                        <div class="form-group row">
                        <div class="col-sm-12 candidate-screen-head" >Stat Holidays</div>

                        </div>


                        <div class="form-group row candidate-screen-head">
                        <div class="col-sm-4 ">Holiday</div>
                        <div class="col-sm-4">Payment Type</div>
                        </div>
                        @endif
                            @foreach ($contractholidayagreement as $contractholidays)
                            @if($contractholidays->getHolidaypayment!=null)
                                    <div class="form-group row">
                                                    <div class="col-sm-4">{{$contractholidays->getHoliday->holiday}}</div>
                                                    <div class="col-sm-4">{{$contractholidays->getHolidaypayment->paymentstatus}}</div>
                                    </div>
                            @endif
                            @endforeach




                            <div class="form-group row">
                            <div class="col-sm-12 candidate-screen-head" > PO Information</div>

                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Purchase Order (PO) Number
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->ponumber}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="ponumber"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Company Name
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->pocompanyname}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="pocompanyname"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Attention Name

                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->poattentionto}} </div>

                            <div  class="col-sm-4"><label class="error" for="poattentionto"></label>   </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">Title

                                </div>

                                <div class="col-sm-4">
                                                {{$contractdata->potitle}}
                                        </div>
                                <div  class="col-sm-4"><label class="error" for="potitle"></label>   </div>
                                </div>
                            <div class="form-group row">
                                            <div class="col-sm-4">Mailing Address

                                            </div>

                                            <div class="col-sm-4">
                                                            {{$contractdata->pomailingaddress}}
                                                    </div>
                                            <div  class="col-sm-4"><label class="error" for="potitle"></label>   </div>
                                            </div>

                            <div class="form-group row">
                            <div class="col-sm-4">City

                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->pocity}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="pocity"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Postal Code

                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->popostalcode}}
                                            </div>
                            <div  class="col-sm-4"><label class="error" for="popostalcode"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Phone Number

                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->pophone}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="pophone"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Email

                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->poemail}}

                            </div>
                            <div  class="col-sm-4"><label class="error" for="poemail"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Cell


                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->pocellno}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="pocellno"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Fax Number


                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->pofax}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="pofax"></label>   </div>
                            </div>
                            <div class="form-group row">
                            <div class="col-sm-4">Billing Notes
                            </div>
                            <div class="col-sm-4">
                                            {{$contractdata->ponotes}}
                            </div>
                            <div  class="col-sm-4"><label class="error" for="ponotes"></label>   </div>
                            </div>
                            @if($po_attachment>0)
                            <div class="form-group row">
                            <div class="col-sm-4">Upload</div>
                            <div class="col-sm-4">

                            <a style="color:black;text-decoration:none" href="{{config('app.url')}}/contracts/cmuf-filedownload?contract_id={{$contractid}}&file_id={{$po_attachment}}&date={{$createddate}}&filetype=po" target="_blank" >Purchase order &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-download" aria-hidden="true"></i></a>

                            </div>
                            <div  class="col-sm-4"><label class="error" for="po_upload"></label>   </div>
                            </div>
                            @endif

                            <div class="form-group row">
                            <div class="col-sm-12 candidate-screen-head">Supervisor Information

                            </div>

                            </div>
                            @if($contractdata->supervisorassigned <1)
                            <div class="form-group row">
                            <div class="col-sm-4 ">
                            Supervisor Assigned
                            </div>
                            <div class="col-sm-4 ">
                            No
                            </div>

                            </div>
                            @endif

                            @if($contractdata->supervisorassigned>0)
                            <div class="container-fluid" id="supervisorornot" >
                                        <div class="form-group row">
                                                        <div class="col-sm-4">Is there a supervisor assigned to site
                                                        </div>
                                                        <div class="col-sm-4">
                                                                Yes
                                                        </div>
                                        </div>
                                <div class="form-group row">
                                                <div class="col-sm-4">Employee Number
                                        </div>
                                                <div class="col-sm-4">
                                                                {{$contractdata->getSupervisoremployees->employee_no }}
                                                </div>
                                                <div  class="col-sm-4"><label class="error" for="supervisoremployeenumber"></label>   </div>
                                        </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Employee Name
                                            </div>
                                            <div class="col-sm-4">
                                                            {{$contractdata->getSupervisorname->first_name }} {{$contractdata->getSupervisorname->last_name }}
                                            </div>
                                            <div  class="col-sm-4"><label class="error" for="employeename"></label>   </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">View Training/Performance/Profile, etc.
                                            </div>
                                                    <div class="col-sm-4">

                                                            @if ($contractdata->viewtrainingperformance==true)
                                                                Yes
                                                            @else
                                                                No
                                                            @endif
                                                            </div>
                                                            <div  class="col-sm-4"><label class="error" for="viewtrainingperformance"></label>   </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Cell Phone
                                     </div>
                                                    <div class="col-sm-4">
                                                                    {{$contractdata->employeecellphone }}
                                                    </div>
                                                    <div  class="col-sm-4"><label class="error" for="employeecellphone"></label>   </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Email Address</div>
                                                    <div class="col-sm-4">
                                                                    {{$contractdata->employeeemailaddress }}
                                                    </div>
                                                    <div  class="col-sm-4"><label class="error" for="employeeemailaddress"></label>   </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Telephone</div>
                                                    <div class="col-sm-4">
                                                                    {{$contractdata->employeetelephone }}
                                                    </div>
                                                            <div  class="col-sm-4"><label class="error" for="employeetelephone"></label>   </div>
                                                    </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Fax Number
                                            </div>
                                                    <div class="col-sm-4">
                                                                    {{$contractdata->employeefaxno }}   </div>
                                                    <div  class="col-sm-4">
                                                              </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Who provides the cell phone
                                            </div>
                                            <div class="col-sm-4">
                                                    @if (isset($contractdata->getCellphoneprovider->providername))
                                                        {{$contractdata->getCellphoneprovider->providername }}
                                                    @endif

                                            </div>
                                            <div  class="col-sm-4"><label class="error" for="contractcellphoneprovider"></label>   </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Tablet Required</div>
                                                    <div class="col-sm-4">
                                                        @if ($contractdata->supervisortabletrequired == true)
                                                            Yes
                                                            @else
                                                            No
                                                        @endif
                                                    </div>
                                                    <div  class="col-sm-4"><label class="error" for="supervisortabletrequired"></label>   </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">CGL 360 in Use</div>
                                                    <div class="col-sm-4">
                                                                    @if ($contractdata->supervisorcgluser == true)
                                                                    Yes
                                                                    @else
                                                                    No
                                                                @endif

                                                    </div>
                                                    <div  class="col-sm-4"><label class="error" for="supervisorcgluser"></label>   </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Public Transport or Car Required</div>
                                                    <div class="col-sm-4">
                                                                    @if ($contractdata->supervisorpublictransportrequired == true)
                                                                    Yes
                                                                    @else
                                                                    No
                                                                @endif


                                                    </div>
                                                    <div  class="col-sm-4"><label class="error" for="supervisorpublictransportrequired"></label>   </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Directions or Nearest Intersection</div>
                                                    <div class="col-sm-4">
                                                            {{$contractdata->direction_nearest_intersection}}

                                                    </div>
                                                    <div  class="col-sm-4">  </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Department at Site</div>
                                                    <div class="col-sm-4">
                                                                    {{$contractdata->department_at_site}}

                                                            </div>
                                                    <div  class="col-sm-4">  </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Delivery Hours</div>
                                                    <div class="col-sm-4">
                                                                    {{$contractdata->delivery_hours}}
                                                            </div>

                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Can Mail be Sent</div>
                                                    <div class="col-sm-4">
                                                                    @if ($contractdata->supervisorcanmailbesent == true)
                                                                    Yes
                                                                    @else
                                                                    No
                                                                @endif

                                                    </div>
                                                    <div  class="col-sm-4"><label class="error" for="supervisorcanmailbesent"></label>   </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-4">Computer/Internet Access
                                            </div>
                                                    <div class="col-sm-4">
                                                                    @if($contractdata->getSupervisordeviceaccess!=null)
                                                                            {{$contractdata->getSupervisordeviceaccess->DeviceType}}
                                                                    @endif

                                                    </div>
                                                    <div  class="col-sm-4"><label class="error" for="contractdeviceaccess"></label>   </div>
                                            </div>


                            </div>



                            @endif


                            <div class="container-fluid" style="padding:0">
                                            <div class="form-group row">
                                                            <div class="col-sm-12 candidate-screen-head">Scope of Work
                                            </div></div>
                                            <div class="form-group row">

                                            <div class="col-sm-12">
                                                            {{$contractdata->scopeofwork}}
                                            </div>


                                    </div>
                            <div class="container-fluid" id="contractamendments" style="padding:0">
                                            <div class="form-group row ">
                                                    <div class="col-sm-4 candidate-screen-head">Amendment Description</div>
                                                    <div class="col-sm-4 candidate-screen-head">Attachment</div>
                                                    <div class="col-sm-4 candidate-screen-head">Created By</div>
                                            </div>
                                            @php
                                                $i=0;
                                            @endphp
                                            @foreach ($contractamendments as $amendments)
                                            @php
                                                $i++;
                                            @endphp
                                                    <div class="form-group row">
                                                                    <div class="col-sm-4 " style="overflow-wrap: break-word;">
                                                                        {{$amendments->amendment_description}}
                                                                    </div>
                                                                    <div class="col-sm-4 ">
                                                                                @if($amendments->amendment_attachment_id>0)
                                                                                <a style="color:black;text-decoration:none" href="{{config('app.url')}}/contracts/cmuf-filedownload?contract_id={{$contractid}}&file_id={{$amendments->amendment_attachment_id}}&date={{$createddate}}&filetype=amendment" target="_blank" >Amendment {{$i}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-download" aria-hidden="true"></i></a>
                                                                                @else
                                                                                No attachment
                                                                                @endif
                                                                    </div>
                                                                    <div class="col-sm-4 ">
                                                                                    {{$amendments->getCreateduser->first_name}} {{$amendments->getCreateduser->last_name}}
                                                                    </div>
                                                    </div>
                                            @endforeach

                                            <div class="form-group row ">
                                                  <div class="col-md-12" style="height:80px"></div>
                                            </div>

                            </div>


                    @section('scripts')
                        <script>
                                refreshSideMenu();
                        </script>
                    @endsection
