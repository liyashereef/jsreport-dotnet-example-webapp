<div class="margin-top">
    <div class="form-group row print-hide">
        <div class="col-sm-9"></div>
        <div class="col-sm-3">
            <div class="btn submit pdf-hide" onClick="window.print();">
                <a href="javascript:;"> Print Application </a>
            </div>
            @if(str_contains(Request::url(),'apply')) <a href="{{ route('applyjob.logout') }}">
                <div class="btn submit pdf-hide">
                    Exit
                </div>
            </a> @endif
        </div>
    </div>
    <!-- new -->
    <div class="row page-break-after" style="margin-top: 5vh;">
        <div class="row-custum" style="margin-top: 40px;">
            <div class="pdf-column-left" style="width: 40%;margin: 15px 0px; text-align: center">
                <span class="pdf-label-style">
                        <img
                                style="border-radius: 50%; margin: 0px 5vh;"
                                src="{{asset('images/uploads/') }}/{{ $candidateJob->candidate->profile_image }}"
                                height="200px" width="200px" name="image" id="profile-img">
                        </span>
            </div>
            <div class="pdf-column-right " style="width: 60%; text-align:center">
                <div style="margin: 90px 20px;">
                    <span class="pdf-label-style pdf-border-normal"
                          style="font-size: 20px;color: black;font-weight: bold;">
                                    Candidate Screening Assessment
                                </span>
                    <span class="orange"
                          style="font-size: 25px; font-weight: bold;">
                                    {{$candidateJob->candidate->name}}
                                </span>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row-custum hide-screen" style="text-align: center; margin-top: 50vh;">
            <div class="offset-sm-6 pull-right">
                <img src="{{ asset('images/CGL-LOGO-600px-152px.png') }}" width="250px";>
            </div>
        </div>
    </div>
    <!-- new -->
    <div style="text-align: center;margin-bottom: 30px">
        <img src="{{ asset('images/logo.png') }}" style="margin-left: 40px;"/>
        <div class="blue label-b" style="margin-top: -2px">Application for Employment</div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12 col-md-12">
            <div class="row-custum">
                <div class="pdf-column-left">
                    <label class="pdf-label pdfgen-label" style="float: left">Full Legal Name</label>
                </div>
                <div class="pdf-column-right pdfgen-display">
                    <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->name}}</span>
                </div>
            </div>
            <div class="row-custum">
                <div class="pdf-column-left">
                    <label class="pdf-label pdfgen-label" style="float: left">Date of Birth</label>
                </div>
                <div class="pdf-column-right pdfgen-display">
                    <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->dob))}}</span>
                </div>
            </div>
            <div class="row-custum">
                <div class="pdf-column-left">
                    <label class="pdf-label pdfgen-label" style="float: left">Home Phone Number</label>
                </div>
                <div class="pdf-column-right pdfgen-display">
                    <div class="col-sum-pdf-full">
                        <span class="pdf-label-style pdf-border-normal">
                            @if($candidateJob->candidate->phone_home != "")
                                {{$candidateJob->candidate->phone_home}}
                            @else
                                --
                            @endif
                        </span>

                    </div>
                </div>
            </div>

            <div class="row-custum">
                <div class="pdf-column-left">
                    <label class="pdf-label pdfgen-label" style="float: left">Cellular Number</label>
                </div>
                <div class="pdf-column-right pdfgen-display">
                    <div class="col-sum-pdf-full">
                        <span class="pdf-label-style pdf-border-normal">
                             @if($candidateJob->candidate->phone_cellular != "")
                                {{$candidateJob->candidate->phone_cellular}}
                            @else
                                --
                            @endif
                        </span>

                    </div>
                </div>
            </div>

            <div class="row-custum">
                <div class="pdf-column-left">
                    <label class="pdf-label pdfgen-label" style="float: left">Email Address</label>
                </div>
                <div class="pdf-column-right pdfgen-display">
                    <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->email}}</span>
                </div>
            </div>

            @if(isset($candidateJob->candidate->profile_image))
                <div class="row-custum">
                    <div class="pdf-column-left">
                        <label class="pdf-label pdfgen-label" style="float: left;padding-top: 40px;">Profile
                            Picture</label>
                    </div>
                    <div class="pdf-column-right pdfgen-display">
                    <span class="pdf-label-style">
                    <img style="border-radius: 50%;"
                         src="{{asset('images/uploads/') }}/{{ $candidateJob->candidate->profile_image }}"
                         height="100px" width="100px" name="image" id="profile-img"/>
                    </span>
                    </div>
                </div>
            @endif

            <div class="row-custum">
                <div class="pdf-column-left">
                    <label class="pdf-label pdfgen-label" style="float: left">Apartment Number/<br>Street
                        Address</label>
                </div>
                <div class="pdf-column-right pdfgen-display">
                    <div class="col-sum-pdf-full">
                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->address}}</span>

                    </div>
                </div>
            </div>

            <div class="row-custum">
                <div class="pdf-column-left">
                    <label class="pdf-label pdfgen-label" style="float: left">City</label>
                </div>
                <div class="pdf-column-right pdfgen-display">
                    <div class="col-sum-pdf-full">
                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->city}}</span>

                    </div>
                </div>
            </div>

            <div class="row-custum">
                <div class="pdf-column-left">
                    <label class="pdf-label pdfgen-label" style="float: left">Postal Code</label>
                </div>
                <div class="pdf-column-right pdfgen-display">
                    <div class="col-sum-pdf-full">
                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->postal_code}}</span>

                    </div>
                </div>
            </div>

            <div class="clearfix"></div>
            @if(!$candidateJob->candidate->addresses->isEmpty())
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1"> Past address over last five years (Required for RCMP background
                            check)</label>
                    </div>
                </div>


                <div class="clearfix"></div>
                @foreach($candidateJob->candidate->addresses as $pastAddress)
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8 float-left">
                            <label class="pdf-label-1"> Address</label>
                        </div>
                        <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$pastAddress->address}}
                    </span>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8 float-left">
                            <label class="pdf-label-1"> From</label>
                        </div>
                        <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{date('F j, Y', strtotime($pastAddress->from))}}
                    </span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8 float-left">
                            <label class="pdf-label-1"> To</label>
                        </div>
                        <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{date('F j, Y', strtotime($pastAddress->to))}}
                    </span>
                        </div>
                    </div>



                <!-- <div class="form-group row flex-nowarp">
                <div class="col-sm-8 pdfgen-display float-left pdfgen-split-3">
                    <span class="sub-label text-left">Address</span>
                    <span class="pdf-label-style pdf-border-normal">{{$pastAddress->address}}</span>
                </div> -->
                <!-- <div class="col-sm-2 pdfgen-display float-left pdfgen-split-3">
                    <span class="sub-label text-left">From</span>
                    <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($pastAddress->from))}}</span>
                </div> -->
                <!-- <div class="col-sm-2 pdfgen-display float-left pdfgen-split-3">
                    <span class="sub-label text-left">To</span>
                    <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($pastAddress->to))}}</span>
                </div>
            </div>
            <div class="clearfix"></div> -->
                @endforeach @endif

            <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>Position Information</b></label>
            </center>
            <div class="clearfix"></div>
            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1"> Date of Application</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{date('F j, Y', strtotime($candidateJob->submitted_date))}}
                    </span>
                </div>
            </div>

            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1"> What position code have you applied for ?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->job->unique_key}}
                    </span>
                </div>
            </div>
            <div class="form-group row flex-nowarp">
                <label class="col-sm-8 pdf-label-1 float-left">Wage per Hour</label>
                <div class="col-sm-3 float-right hour">
                    <label class="pdf-text">Low: ${{number_format($candidateJob->job->wage_low,2)}}</label>
                    <label class="pdf-text hour-top"></label>

                    <label class="pdf-text">High: ${{number_format($candidateJob->job->wage_high,2)}}</label>
                    <label class="pdf-text hour-top"></label>
                    <span class="pdf-border-normal"></span>
                </div>
            </div>


            <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>Orientation</b></label></center>
            <div class="clearfix"></div>
            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">Did you attend an orientation session hosted by our SVP/COO prior to
                        applying </label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                   @if(isset($candidateJob->candidate->referalAvailibility->orientation))
                            @if($candidateJob->candidate->referalAvailibility->orientation == "1")
                                Yes
                            @else
                                No
                            @endif
                        @else
                            --
                        @endif
                    </span>
                </div>
            </div>

            <div class="page-break-after">
                <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>Referral</b></label></center>
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1"> How did you find out about this job posting </label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                        @if(isset($candidateJob->candidate->referalAvailibility->jobPostFinding->job_post_finding))
                            {{$candidateJob->candidate->referalAvailibility->jobPostFinding->job_post_finding }}
                        @else
                            --
                        @endif
                    </span>
                    </div>
                </div>
                @if(isset($candidateJob->candidate->referalAvailibility->job_post_finding) && $candidateJob->candidate->referalAvailibility->job_post_finding == 3)
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1"> Please enter the email address of the person who referred you to
                            Commissionaires. Please make sure to accurately enter the email address or your sponsor will
                            not
                            get the referral credit. </label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                   @if(isset($candidateJob->candidate->referalAvailibility->sponser_email))
                            {{$candidateJob->candidate->referalAvailibility->sponser_email }}
                        @else
                            --
                        @endif
                    </span>
                    </div>
                </div>
                @endif
                <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>Availability</b></label>
                </center>
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1"> Would you be willing to start as a "floater/spare" until a permanent
                            position comes up, or are you only interested in assignments you've applied to. </label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                 @if(isset($candidateJob->candidate->referalAvailibility->position_availibility))
                            {{ config('globals.position_availibility')[$candidateJob->candidate->referalAvailibility->position_availibility ]}}
                        @else
                            --
                        @endif
                    </span>
                    </div>
                </div>
                @if(isset($candidateJob->candidate->referalAvailibility->position_availibility) && ($candidateJob->candidate->referalAvailibility->position_availibility == "1"))
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8 float-left">
                            <label class="pdf-label-1"> How many hours a week are you looking for ? </label>
                        </div>
                        <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                  {{$candidateJob->candidate->referalAvailibility->floater_hours }}
                    </span>
                        </div>
                    </div>
                @endif
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1"> How soon could you start ? </label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                        @if(isset($candidateJob->candidate->referalAvailibility->starting_time))
                            {{config('globals.starting_time')[$candidateJob->candidate->referalAvailibility->starting_time] }}
                        @else
                            --
                        @endif
                    </span>
                    </div>
                </div>

                <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>Fit Assessment</b></label>
                </center>
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1"> Prior to our online ad, had you heard about
                            Commissionaires? </label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">

                   {{$candidateJob->candidate_brand_awareness->answer }}
                    </span>
                    </div>
                </div>

                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1"> Prior to our online ad, how familiar are you with Garda,
                            G4S,Securitas
                            or Palladin?</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">

                    {{$candidateJob->candidate_security_awareness->answer }}
                    </span>
                    </div>
                </div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1"> Please share your understanding of Commissionaires
                            <b><u>PRIOR</u></b>
                            to applying </label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">

                    {{$candidateJob->candidate->comissionaires_understanding[0]->candidateUnderstandingLookup->commissionaires_understandings}}
                    </span>
                    </div>
                </div>

                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Please elaborate why you are applying for this specific role, and why
                            you
                            think you would succeed in the role</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">

                    {{$candidateJob->fit_assessment_why_apply_for_this_job }}
                    </span>
                    </div>
                </div>
            </div>
            <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>Licensing Information And
                        Security Guarding Experience</b></label></center>
            <div class="clearfix"></div>
            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">Do you have valid security guarding licence in ontario with First Aid and
                        CPR?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->guardingexperience->guard_licence }}
                    </span>
                </div>
            </div>

            <div class="clearfix"></div>
            @if($candidateJob->candidate->guardingexperience->guard_licence == "Yes")
                <aside>
                    <label class="blue"> <b>Licence Start Date </b></label>
                    <div class="form-group row flex-nowarp">
                        <label class="col-sm-8 pdf-label-1 float-left">Guarding licence in Ontario </label>
                        <div class="col-sm-3 float-left">
                            <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->guardingexperience->start_date_guard_license))}}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <label class="col-sm-8 pdf-label-1 float-left">First aid certificate</label>
                        <div class="col-sm-3 float-left">
                            <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->guardingexperience->start_date_first_aid))}}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <label class="col-sm-8 pdf-label-1 float-left">CPR certificate </label>
                        <div class="col-sm-3 float-left">
                            <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->guardingexperience->start_date_cpr))}}</span>
                        </div>
                    </div>
                </aside>
                <div class="clearfix"></div>
                <aside>
                    <label class="blue"> <b>Licence End Date </b></label>
                    <div class="form-group row flex-nowarp">
                        <label class="col-sm-8 pdf-label-1 float-left">Guarding licence in Ontario </label>
                        <div class="col-sm-3 float-left">
                            <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->guardingexperience->expiry_guard_license))}}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <label class="col-sm-8 pdf-label-1 float-left">First aid certificate </label>
                        <div class="col-sm-3 float-left">
                            <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->guardingexperience->expiry_first_aid))}}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <label class="col-sm-8 pdf-label-1 float-left">CPR certificate </label>
                        <div class="col-sm-3 float-left">
                            <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->guardingexperience->expiry_cpr))}}</span>
                        </div>
                    </div>
                </aside>
                @if(isset($candidateJob->candidate->guardingexperience->test_score_percentage))
                    <aside>
                        <label class="blue"> <b>Ontario Security Guard Test Scores </b></label>
                        <div class="form-group row flex-nowarp">
                            <label class="col-sm-8 pdf-label-1 float-left">What was your test score on the Ontario
                                Security Guard exam? (Percent) </label>
                            <div class="col-sm-3 float-left">
                                <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->guardingexperience->test_score_percentage }}</span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </aside>
                @endif
                <aside>
                    <label class="blue"> <b>Security Clearance Information </b></label>
                    <div class="form-group row flex-nowarp">
                        <label class="col-sm-8 pdf-label-1 float-left">Do you have a valid security clearance ? </label>
                        <div class="col-sm-3 float-left">
                            <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->guardingexperience->security_clearance }}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    @if($candidateJob->candidate->guardingexperience->security_clearance == "Yes")
                        <div class="form-group row flex-nowarp">
                            <label class="col-sm-8 pdf-label-1 float-left">What type of security clearance ?</label>
                            <div class="col-sm-3 float-left">
                                <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->guardingexperience->security_clearance_type }}</span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group row flex-nowarp">
                            <label class="col-sm-8 pdf-label-1 float-left">Enter the expiry date</label>
                            <div class="col-sm-3 float-left">
                                <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->guardingexperience->security_clearance_expiry_date))}}</span>
                            </div>
                        </div>
                    @endif
                </aside>
            @endif
            @if (isset($candidateJob->candidate->force))
                @if ($candidateJob->candidate->force->force == "Yes")
                    <aside>
                        <label class="blue"> <b>Use Of Force</b></label>
                        <div class="form-group row flex-nowarp">
                            <label class="col-sm-8 pdf-label-1 float-left">Are you use of force certified?</label>
                            <div class="col-sm-3 float-left">
                                <span class="pdf-label-style pdf-border-normal">
                                    {{$candidateJob->candidate->force->force }}
                                </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group row flex-nowarp">
                            <label class="col-sm-8 pdf-label-1 float-left">If yes, please provide your certification</label>
                            <div class="col-sm-3 float-left">
                                <span class="pdf-label-style pdf-border-normal">
                                    {{$candidateJob->candidate->force->force_lookup->use_of_force}}
                                </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group row flex-nowarp">
                            <label class="col-sm-8 pdf-label-1 float-left">When does your certification expire?</label>
                            <div class="col-sm-3 float-left">
                                <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->force->expiry))}}</span>
                            </div>
                        </div>
                    </aside>
                @else
                    <aside>
                        <label class="blue"> <b>Use Of Force</b></label>
                        <div class="form-group row flex-nowarp">
                            <label class="col-sm-8 pdf-label-1 float-left">Are you use of force certified?</label>
                            <div class="col-sm-3 float-left">
                                <span class="pdf-label-style pdf-border-normal">
                                    {{$candidateJob->candidate->force->force }}
                                </span>
                            </div>
                        </div>
                    </aside>
                @endif
            @endif
            <center><label class="col-sm-11 orange label-b  hr-line-label"><b>Security Guarding Experience</b></label>
            </center>
            <aside>
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <label class="col-sm-8 pdf-label-1 float-left">Do you have a valid Social Insurance Number in
                        Canada? </label>
                    <div class="col-sm-3 float-left">
                        @if(isset($candidateJob->candidate->guardingexperience->social_insurance_number)&& ($candidateJob->candidate->guardingexperience->social_insurance_number == 1))
                            <span class="pdf-label-style pdf-border-normal"><label class="padding-5">Yes</label></span>
                        @endif
                        @if(isset($candidateJob->candidate->guardingexperience->social_insurance_number)&& (@$candidateJob->candidate->guardingexperience->social_insurance_number)==0)
                            <span class="pdf-label-style pdf-border-normal"><label class="padding-5">No</label></span>
                        @endif
                    </div>
                </div>

                <div class="clearfix"></div>
                @if($candidateJob->candidate->guardingexperience->social_insurance_number == 1)
                    <div class="form-group row flex-nowarp">
                        <label class="col-sm-8 pdf-label-1 float-left">Do you have an expiry date on your SIN ? </label>
                        <div class="col-sm-3 float-left">
                            @if(isset($candidateJob->candidate->guardingexperience->sin_expiry_date_status)&& ($candidateJob->candidate->guardingexperience->sin_expiry_date_status == 1))
                                <span class="pdf-label-style pdf-border-normal"><label
                                            class="padding-5">Yes</label></span>
                            @endif
                            @if(isset($candidateJob->candidate->guardingexperience->sin_expiry_date_status)&& (@$candidateJob->candidate->guardingexperience->sin_expiry_date_status)==0)
                                <span class="pdf-label-style pdf-border-normal"><label
                                            class="padding-5">No</label></span>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="clearfix"></div>
                @if($candidateJob->candidate->guardingexperience->sin_expiry_date_status == 1)
                    <div class="form-group row flex-nowarp">
                        <label class="col-sm-8 pdf-label-1 float-left">Expiry date of your SIN </label>
                        <div class="col-sm-3 float-left">
                    <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->guardingexperience->sin_expiry_date))}}
                       </span>
                            @endif

                        </div>
                    </div>

            </aside>

            <div class="clearfix"></div>


            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">How many total years of security guarding experience do you have?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                        @if(isset($candidateJob->candidate->guardingexperience->years_security_experience)))
                            {{$candidateJob->candidate->guardingexperience->years_security_experience}}
                        @else
                            --
                        @endif
                    </span>
                </div>
            </div>

            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">What is the most senior position you have held in security?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{@$candidateJob->candidate->guardingexperience->most_senior_position_held>0? @$candidateJob->candidate->guardingexperience->position->position:'Other'}}
                    </span>
                </div>
            </div>

            </aside>
            <div class="clearfix"></div>
            <label class="blue"> <b>Please list all the positions you've held and years of experience </b></label>
            <aside class="form-block-section">
                <div class="row">
                    @foreach(json_decode($candidateJob->candidate->guardingexperience->positions_experinces) as $key=>$value)
                        <div class="col-md-4 col-xs-12 col-sm-12 pdfgen-column">
                            <div class="row form-group flex-nowarp">
                                <label class="col-sm-3 col-md-9 col-xs-12 start pdf-label-1">{{ ucwords(str_replace("_"," ",$key)) }}</label>
                                <div class="col-sm-2 col-md-3 col-xs-12 ">
                                    <span class="col-sm-2 pdf-text pdf_border"> {{ $value }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>
            <div class="clearfix page-break-after"></div>
            <center><label class="col-sm-11 orange label-b hr-line-label"><b>Compensation</b></label></center>
            <aside>
                <div class="form-group row flex-nowarp">
                    <label class="col-sm-8 pdf-label-1 float-left">What are your wage exceptions (Per Hour)?</label>
                    <div class="col-sm-3 float-left hour">
                        <label class="pdf-text">From : ${{number_format($candidateJob->candidate->wageexpectation->wage_expectations_from, 2)}}</label>
                        <label class="pdf-text hour-top"></label>

                        <label class="pdf-text">To : ${{number_format($candidateJob->candidate->wageexpectation->wage_expectations_to, 2)}}</label>
                        <label class="pdf-text hour-top"></label>
                        <span class="pdf-border-normal"></span>
                    </div>
                </div>
                <div class="form-group row flex-nowarp">
                    <label class="col-sm-8 pdf-label-1 float-left">What was your last hourly wage within the security
                        guarding industry?</label>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">${{number_format($candidateJob->candidate->wageexpectation->wage_last_hourly, 2)}}</span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">How many hours per week were you working at this wage?</label>
                    </div>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->wageexpectation->wage_last_hours_per_week}} hours per week</span>
                        <span></span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">Can you validate your current wage with a paystup as evidence if we
                            pay a higher wage?</label>
                    </div>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->wageexpectation->current_paystub}}</span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Who was the security provider that paid the wage?</label>
                    </div>
                    <div class="col-sm-3 float-left">

                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->wageexpectation->wageprovider->security_provider}}</span>
                    </div>
                </div>
                @if($candidateJob->candidate->wageexpectation->wageprovider->security_provider=="Other")
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8 float-left">
                            <label class="pdf-label-1">Please enter the name of the security provider that paid your
                                previous wage?</label>
                        </div>
                        <div class="col-sm-3 float-left">
                            <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->wageexpectation->wage_last_provider_other}}</span>
                        </div>
                    </div>
                @endif
                @php
                    $security_provider_name=isset($candidateJob->candidate->wageexpectation->wage_last_provider) && $candidateJob->candidate->wageexpectation->wageprovider->security_provider!='Other' ? $candidateJob->candidate->wageexpectation->wageprovider->security_provider : $candidateJob->candidate->wageexpectation->wage_last_provider_other;
                @endphp
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">What were the strengths of {{$security_provider_name}}</label>
                    </div>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{@$candidateJob->candidate->wageexpectation->security_provider_strengths}}</span>
                    </div>

                </div>
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">What do you hope to get from Commissionaires that you
                            feel {{$security_provider_name}} was not providing?</label>
                    </div>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{@$candidateJob->candidate->wageexpectation->security_provider_notes}}</span>
                    </div>

                </div>
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">How would you rate your experience at {{$security_provider_name}}
                            ?</label>
                    </div>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{@$candidateJob->candidate->wageexpectation->rating->experience_ratings}}</span>
                    </div>

                </div>

                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">What was your previous role? (at your last wage rate)</label>
                    </div>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{@$candidateJob->candidate->wageexpectation->last_role_held>0?@$candidateJob->candidate->wageexpectation->lastrole->position:'Other'}}</span>
                    </div>

                </div>
                <div class="clearfix"></div>

                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Please justify/explain your wage expectation. Why do you think you're
                            worth the wage you are asking for?</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">

                    {{$candidateJob->candidate->wageexpectation->explanation_wage_expectation}}
                    </span>
                    </div>
                </div>

            </aside>
            <div class="clearfix"></div>
            <center><label class="col-sm-12 orange label-b hr-line-label"><b>Availability</b></label></center>
            <aside>
                <div class="form-group row flex-nowarp">
                    <label class="col-sm-8 pdf-label-1 float-left">When you are available to start?</label>
                    <div class="col-sm-3 float-left ml-25">
                        <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->availability->availability_start))}}</span>
                    </div>
                </div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">What is your current availability?</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->availability->current_availability }}
                        </span>
                    </div>
                </div>
            </aside>
            <div class="clearfix"></div>
            @if($candidateJob->candidate->availability->current_availability == "Part-Time (Less than 40 hours per week)")
                <div class="form-group row flex-nowarp">
                    <label class="col-sm-11 pdf-label-1">If only part time - please briefly explain your
                        limitation</label>
                </div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-11 float-sm-right">
                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->availability->availability_explanation}}</span>
                    </div>
                </div>
            @endif


            @if(isset($candidateJob->candidate->availability->days_required))
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Which days are you available to work?<br><i>(Select those days you
                                are available to work. If you are joining our spares pool, we will expect you to work on
                                the days and shifts you select)</i></label>
                    </div>
                    <div class="col-sm-3 float-sm-right">
                        <span class="pdf-label-style pdf-border-normal"> {!! implode(", ", json_decode($candidateJob->candidate->availability->days_required)) !!}</span>
                    </div>
                </div>

            @endif
            @if($candidateJob->candidate->availability->shifts)
                <div class="clearfix"></div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Which shifts are you willing to work?</label>
                    </div>
                    <div class="col-sm-3 float-sm-right">
                        <span class="pdf-label-style pdf-border-normal"> {!! implode(", ", json_decode($candidateJob->candidate->availability->shifts)) !!}</span>
                    </div>
                </div>
            @endif
            <div class="clearfix"></div>
            <div class="form-group row flex-nowarp">
                <label class="col-sm-11 pdf-label-1">Most of our assignments require employee to work 8-12 hours shifts
                    on a rotating basis. As a result you are likely to be required to be available to work at any time
                    day or night, 7days per week. Please note that Monday to Friday or day shifts are rarely
                    available.</label>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">Do you understand the shift availability as noted above?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                        {{$candidateJob->candidate->availability->understand_shift_availability }}
                    </span>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row flex-nowarp">
                <div class="col-sm-8  float-left">
                    <label class="pdf-label-1">Are you available for shift work including evenings and nights?</label>
                </div>
                <div class="col-sm-3 float-left ">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->availability->available_shift_work }}
                        </span>
                </div>
            </div>
            <div class="clearfix"></div>
            @if($candidateJob->candidate->availability->available_shift_work == "No")

                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">If you answered "no", please explain your restrictions:</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                        <span class="pdf-label-style pdf-border-normal">
                        {{$candidateJob->candidate->availability->explanation_restrictions}}
                        </span>
                    </div>
                </div>

                @endif
                </aside>
                <div class="clearfix"></div>
                <center><label class="col-sm-11 orange label-b hr-line-label"><b>Security Clearance</b></label></center>
                <aside>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8  float-left">
                            <label class="pdf-label-1">Were you born outside of Canada?</label>
                        </div>
                        <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->securityclearance->born_outside_of_canada }}
                        </span>
                        </div>
                    </div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8  float-left">
                            <label class="pdf-label-1">Please indicate your working status in Canada?</label>
                        </div>
                        <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->securityclearance->work_status_in_canada }}
                        </span>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8  float-left">
                            <label class="pdf-label-1">How many years have you lived in Canada(approximately)?</label>
                        </div>
                        <div class="col-sm-3 float-left ">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->securityclearance->years_lived_in_canada}}
                        </span>
                        </div>
                    </div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8  float-left">
                            <label class="pdf-label-1">Are you prepared to submit to a security screening?</label>
                        </div>
                        <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->securityclearance->prepared_for_security_screening }}
                        </span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8  float-left">
                            <label class="pdf-label-1">Do you have reason to believe you may NOT be granted a
                                clearance?</label>
                        </div>
                        <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->securityclearance->no_clearance }}
                        </span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    @if($candidateJob->candidate->securityclearance->no_clearance == "Yes")
                        <div class="form-group row flex-nowarp">
                            <div class="col-sm-8  float-left">
                                <label class="pdf-label-1">If you answered "Yes", please explain:</label>
                            </div>
                            <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                        {{$candidateJob->candidate->securityclearance->no_clearance_explanation}}
                        </span>
                            </div>
                        </div>

                    @endif
                </aside>
                <div class="clearfix"></div>
                <center><label class="col-sm-11 orange label-b hr-line-label"><b>Proximity To Client Site</b></label>
                </center>
                <aside>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8  float-left">
                            <label class="pdf-label-1">Do you have a valid drivers Licence?</label>
                        </div>
                        <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->securityproximity->driver_license}}
                        </span>
                        </div>
                    </div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8  float-left">
                            <label class="pdf-label-1">Do you have access to a vehicle?</label>
                        </div>
                        <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->securityproximity->access_vehicle }}
                        </span>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8  float-left">
                            <label class="pdf-label-1">If you do not have a licence or access to a vehicle, do you have
                                access to public transit?</label>
                        </div>
                        <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->securityproximity->access_public_transport }}
                        </span>
                        </div>
                    </div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8  float-left">
                            <label class="pdf-label-1">Does your method of transporation limit your availablity?</label>
                        </div>
                        <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->securityproximity->transportation_limitted }}
                        </span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    @if($candidateJob->candidate->securityproximity->transportation_limitted == "Yes")
                        <div class="form-group row flex-nowarp">
                            <div class="col-sm-8  float-left">
                                <label class="pdf-label-1">If you answered "Yes", please explain:</label>
                            </div>
                            <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                        {{$candidateJob->candidate->securityproximity->explanation_transport_limit}}
                        </span>
                            </div>
                        </div>

                    @endif
                    <div class="clearfix"></div>
                </aside>
                <!--added new-->
                <div class="clearfix"></div>
                <center><label class="col-sm-11 orange label-b hr-line-label"><b class="font-20">Employment History</b></label>
                </center>
                <aside>
                    <div class="row">
                        @foreach($candidateJob->candidate->employment_history as $i=>$employment_history)
                            <div class="col-md-6 col-xs-12 col-sm-12 margin-top-20 pdfgen-column">
                                <label class="col-sm-12 col-xs-12 orange padding-clear sub-section-label"><b>Employer {{$i+1}}</b></label>
                                <div class="row form-group pdf-row">
                                    <label class="col-sm-3 col-md-3 col-xs-12 start pdf-label-1 pdfgen-label">Start
                                        Date</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{date('F j, Y', strtotime($employment_history->start_date))}}</span>
                                    </div>
                                </div>
                                <div class="row form-group pdf-row">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">End Date</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{date('F j, Y', strtotime($employment_history->end_date))}}</span>
                                    </div>
                                </div>
                                <div class="row form-group pdf-row">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">Employer</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$employment_history->employer}}</span>
                                    </div>
                                </div>
                                <div class="row form-group pdf-row">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">Role</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$employment_history->role}}</span>
                                    </div>
                                </div>
                                <div class="row form-group pdf-row">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">Duties</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$employment_history->duties}}</span>
                                    </div>
                                </div>
                                <div class="row form-group pdf-row">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">Reason for
                                        Leaving</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$employment_history->reason}}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
                <!--added new END-->
                <div class="clearfix"></div>
                <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>References</b></label>
                </center>
                <aside class="form-block-section">
                    <div class="row">
                        @foreach($candidateJob->candidate->references as $i=>$references)
                            <div class="col-md-6 col-xs-12 col-sm-12 margin-top-20 pdfgen-column">
                                <label class="col-sm-12 col-xs-12 orange padding-clear sub-section-label"><b>Reference {{$i+1}}</b></label>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 start pdf-label-1 pdfgen-label">Name</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$references->reference_name}}</span>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 start pdf-label-1 pdfgen-label">Employer</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$references->reference_employer}}</span>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">Position</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$references->reference_position}}</span>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">Phone</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$references->contact_phone}}</span>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">Email</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$references->contact_email}}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
                <div class="clearfix"></div>
                <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>Education</b></label>
                </center>
                <aside class="form-block-section">
                    <div class="row">
                        @foreach($candidateJob->candidate->educations as $i=>$educations)
                            <div class="col-md-6 col-xs-12 col-sm-12 margin-top-20 pdfgen-column">
                                <label class="col-sm-12 col-xs-12 orange padding-clear sub-section-label"><b>Education {{$i+1}}</b></label>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 start pdf-label-1 pdfgen-label">Start
                                        Date</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{date('F j, Y', strtotime($educations->start_date_education))}}</span>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 start pdf-label-1 pdfgen-label">End
                                        Date</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{date('F j, Y', strtotime($educations->end_date_education))}}</span>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">Grade</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$educations->grade}}</span>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">Program</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$educations->program}}</span>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-sm-3 col-md-3 col-xs-12 pdf-label-1 pdfgen-label">School/Institute</label>
                                    <div class="col-sm-9 col-md-9 col-xs-12 pdfgen-display">
                                        <span class="pdf-label-1 pdf-border">{{$educations->school}}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
                <div class="clearfix"></div>
                @if ($candidateJob->candidate->uniform_measurements->count()>0)


                <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>Uniform Measurement</b></label>
                </center>
                <div class="row flex-nowarp">
                    <label class="col-sm-2 blue float-left "><b>Measure</b></label>
                    <label class="col-sm-2 blue float-left "><b>Size (Inches)</b></label>
                    <label class="col-sm-2 blue float-left "><b></b></label>

                </div>
                @foreach ($candidateJob->candidate->uniform_measurements as $uniform_measure)
                <div class="form-group row flex-nowarp">

                    <div class="col-sm-2  ">
                        <label class="pdf-label-1 " style="font-size: 12px;">{{$uniform_measure
                        ->uniformSchedulingMeasurementPoints
                        ->name}}</label>
                    </div>
                    <div class="col-sm-2   ">
                        <label class="pdf-label-1" style="font-size: 12px;">{{$uniform_measure->measurement_values}} (Inches)</label>
                    </div>
                    <div class="col-sm-2   ">
                        <label class="pdf-label-1" style="font-size: 12px;"></label>
                    </div>


                </div>
                @endforeach
                <div class="clearfix"></div>
                @endif
                <center><label class="col-sm-12 col-xs-12 orange label-b hr-line-label"><b>Languages</b></label>
                </center>
                <div class="row flex-nowarp">
                    <label class="col-sm-2 blue float-left text-center"><b>Skill Level</b></label>
                    <label class="col-sm-2 blue float-left text-center"><b>A- Limited</b></label>
                    <label class="col-sm-2 blue float-left text-center"><b>B- Functional</b></label>
                    <label class="col-sm-2 blue float-left text-center"><b>C- Fluent</b></label>
                    <label class="col-sm-2 blue float-left text-center"><b>D- No knowledge</b></label>
                </div>
                <div class="clearfix"></div>

                <div class="clearfix"></div>
                @foreach($candidateJob->candidate->languages as $each_language_skill)
                    <label class="col-sm-11 orange float-left">{{ $each_language_skill->language_looukp->language }}</label>
                    <div class="clearfix"></div>

                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-2 float-left">
                            <label class="pdf-label-1" style="font-size: 12px;">Speaking/ Oral comprehension</label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left checkbox-alignnormal text-center">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->speaking=='A - Limited - I am just learning the language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                A
                            </label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left checkbox-alignnormal text-center">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->speaking=='B - Functional - this is my second language but I can get by.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                B
                            </label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left checkbox-alignnormal text-center">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->speaking=='C - Fluent - this is my native language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                C
                            </label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left checkbox-alignnormal text-center">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->speaking=='D - No Knowledge.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                D
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-2 float-left">
                            <label class="pdf-label-1">Reading</label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->reading=='A - Limited - I am just learning the language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                A
                            </label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->reading=='B - Functional - this is my second language but I can get by.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                B
                            </label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->reading=='C - Fluent - this is my native language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                C
                            </label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->reading=='D - No Knowledge.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                D
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-2 float-left">
                            <label class="pdf-label-1">Writing</label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->writing=='A - Limited - I am just learning the language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                A
                            </label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->writing=='B - Functional - this is my second language but I can get by.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                B
                            </label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->writing=='C - Fluent - this is my native language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                C
                            </label>
                        </div>
                        <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                            <label class="form-check-label padding-left-clear">
                                <img src="{{ ($each_language_skill->writing=='D - No Knowledge.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                D
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    @endforeach
                    @if ($candidateJob->candidate->other_languages->count()>0)
                        @include('hranalytics::job-application.partials.profile.printpreviewlanguage')
                    @endif
                    </aside>
                    <div class="clearfix"></div>
                    <center><label class="col-sm-11 orange label-b hr-line-label"><b>Special Skills</b></label></center>
                    <div class="clearfix"></div>
                    <label class="col-sm-11 blue">Please indicate the computer applications you've used in the
                        past</label>
                    <div class="clearfix"></div>
                    <aside>
                        <div class="form-group row flex-nowarp">
                            <div class="col-sm-1 float-left">
                                <label class="pdf-label-1"></label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b>No</b>
                                </label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b> Basic</b>
                                </label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b> Good</b>
                                </label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b>Advanced</b>
                                </label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b>Expert</b>
                                </label>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        @foreach($candidateJob->candidate->skills as $eachSkill) @if($eachSkill->skill_lookup->category=='Special Skills')
                            <div class="form-group row flex-nowarp">
                                <div class="col-sm-1 float-left">
                                    <label class="pdf-label-1">{{ $eachSkill->skill_lookup->skills }}</label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center checkbox-alignnormal">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='No Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center checkbox-alignnormal">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='Basic Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center checkbox-alignnormal">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='Good Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center checkbox-alignnormal">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='Advanced Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center checkbox-alignnormal">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='Expert Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        @endif @endforeach
                    </aside>
                    <div class="clearfix"></div>
                    <center><label class="col-sm-11 orange label-b  hr-line-label"><b>Soft Skills</b></label></center>
                    <label class="col-sm-11 blue">Please rate the following soft skills</label>
                    <div class="clearfix"></div>
                    <aside>
                        <div class="form-group row flex-nowarp">
                            <div class="col-sm-1 float-left text-center">
                                <label class="pdf-label-1"></label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b>No</b>
                                </label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b> Basic</b>
                                </label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b> Good</b>
                                </label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b>Advanced</b>
                                </label>
                            </div>
                            <div class="form-check col-sm-2 float-left text-center">
                                <label class="text-center">
                                    <b>Expert</b>
                                </label>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        @foreach($candidateJob->candidate->skills as $eachSkill) @if($eachSkill->skill_lookup->category=='Soft Skills')
                            <div class="form-group row flex-nowarp">
                                <div class="col-sm-1 float-left">
                                    <label class="pdf-label-1" style="font-size: 12px">{{ $eachSkill->skill_lookup->skills }}</label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='No Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='Basic Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='Good Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='Advanced Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                                <div class="form-check col-sm-2 float-left text-center">
                                    <label class="form-check-label padding-left-clear">
                                        <img src="{{ ($eachSkill->skill_level=='Expert Knowledge')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                                    </label>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        @endif @endforeach
                    </aside>

                    <div class="clearfix"></div>
                    <center><label class="col-sm-11 orange label-b  hr-line-label"><b>Technical Summary </b></label>
                    </center>
                    <div class="clearfix"></div>

                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-8 float-left">
                            <label class="pdf-label-1">Please indicate if you have a Smartphone? </label>
                        </div>

                        <div class="col-sm-3 float-left">
                            @if(isset($candidateJob->candidate->smart_phone_type_id))
                                <span class="pdf-label-style pdf-border-normal"><label>Yes</label></span>
                            @endif
                            @if(!isset($candidateJob->candidate->smart_phone_type_id))
                                <span class="pdf-label-style pdf-border-normal"><label>No</label></span>
                            @endif
                        </div>

                    </div>

                    <div class="clearfix"></div>
                    @if(isset($candidateJob->candidate->smart_phone_type_id))
                        <div class="clearfix"></div>
                        <aside>

                            <div class="form-group row flex-nowarp">
                                <div class="col-sm-8 float-left">
                                    <label class="pdf-label-1">If you have a smart phone what kind of phone is
                                        it?</label>
                                </div>
                                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->technicalSummary->type}}
                    </span>
                                </div>
                            </div>
                            <div class="form-group row flex-nowarp">
                                <div class="col-sm-8 float-left">
                                    <label class="pdf-label-1">How would you rate your proficiency with using apps on
                                        your mobile phone? </label>
                                </div>
                                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->smart_phone_skill_level}}
                    </span>
                                </div>
                            </div>
                        </aside>
                    @endif


                    <div class="clearfix"></div>
                    <center><label class="col-sm-11 orange label-b  hr-line-label"><b>Commissionaires
                                Experience</b></label></center>
                    <div class="clearfix"></div>
                    <aside>

                        <div class="form-group row flex-nowarp">
                            <div class="col-sm-8 float-left">
                                <label class="pdf-label-1">Are you a current employee of Commissionaires Great
                                    Lakes?</label>
                            </div>
                            <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->current_employee_commissionaries }}
                    </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        @if($candidateJob->candidate->experience->current_employee_commissionaries == "Yes")
                            <div class="clearfix"></div>
                            <aside>

                                <div class="form-group row flex-nowarp">
                                    <div class="col-sm-8 float-left">
                                        <label class="pdf-label-1">Employee Number</label>
                                    </div>
                                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->employee_number}}
                    </span>
                                    </div>
                                </div>

                                <div class="form-group row flex-nowarp">
                                    <div class="col-sm-8 float-left">
                                        <label class="pdf-label-1">Currently Posted Site</label>
                                    </div>
                                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->currently_posted_site}}
                    </span>
                                    </div>
                                </div>


                                <div class="form-group row flex-nowarp">
                                    <div class="col-sm-8 float-left">
                                        <label class="pdf-label-1">Position</label>
                                    </div>
                                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->position}}
                    </span>
                                    </div>
                                </div>

                                <div class="form-group row flex-nowarp">
                                    <div class="col-sm-8 float-left">
                                        <label class="pdf-label-1">Hours/Week</label>
                                    </div>
                                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->hours_per_week}}
                    </span>
                                    </div>
                                </div>


        </div>

    </div>
    </aside>
    @endif
    <div class="clearfix margin-bottom-10"></div>

    <div class="form-group row flex-nowarp">
        <div class="col-sm-8 float-left">
            <label class="pdf-label-1">Have you ever applied for employment with Commissionaires Great Lakes?</label>
        </div>
        <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->applied_employment }}
                    </span>
        </div>
    </div>
    <div class="clearfix margin-bottom-10"></div>
    @if($candidateJob->candidate->experience->applied_employment == "Yes")
        <div class="row">
            <label class="col-sm-12 float-left pdf-label-1">If your answer was "Yes"</label>
        </div>

        <div class="clearfix"></div>



        <div class="form-group row flex-nowarp">
            <div class="col-sm-8 float-left">
                <label class="pdf-label-1">Start Date</label>
            </div>
            <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{date('F j, Y', strtotime($candidateJob->candidate->experience->start_date_position_applied))}}
                    </span>
            </div>
        </div>

        <div class="form-group row flex-nowarp">
            <div class="col-sm-8 float-left">
                <label class="pdf-label-1">Currently Posted Site</label>
            </div>
            <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{date('F j, Y', strtotime($candidateJob->candidate->experience->end_date_position_applied))}}
                    </span>
            </div>
        </div>

        <div class="form-group row flex-nowarp">
            <div class="col-sm-8 float-left">
                <label class="pdf-label-1">Position</label>
            </div>
            <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->position_applied}}
                    </span>
            </div>
        </div>

    @endif
    <div class="clearfix margin-bottom-10"></div>

    <div class="form-group row flex-nowarp">
        <div class="col-sm-8 float-left">
            <label class="pdf-label-1">Have you ever been employed by the corps of Commissionaires?</label>
        </div>
        <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->employed_by_corps }}
                    </span>
        </div>
    </div>
    <div class="clearfix"></div>
    @if($candidateJob->candidate->experience->employed_by_corps == "Yes")
        <div class="row">
            <label class="col-sm-12 pdf-label-1">If your answer was "Yes"</label>
        </div>
        <div class="clearfix"></div>

        <div class="form-group row flex-nowarp">
            <div class="col-sm-8 float-left">
                <label class="pdf-label-1">Position</label>
            </div>
            <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->position_employed}}
                    </span>
            </div>
        </div>


        <div class="form-group row flex-nowarp">
            <div class="col-sm-8 float-left">
                <label class="pdf-label-1">Start Date</label>
            </div>
            <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{date('F j, Y', strtotime($candidateJob->candidate->experience->start_date_employed))}}
                    </span>
            </div>
        </div>


        <div class="form-group row flex-nowarp">
            <div class="col-sm-8 float-left">
                <label class="pdf-label-1">End Date</label>
            </div>
            <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{date('F j, Y', strtotime($candidateJob->candidate->experience->end_date_employed))}}
                    </span>
            </div>
        </div>

        <div class="form-group row flex-nowarp">
            <div class="col-sm-8 float-left">
                <label class="pdf-label-1">Division</label>
            </div>
            <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{@$candidateJob->candidate->experience->division->division_name}}
                    </span>
            </div>
        </div>

        <div class="form-group row flex-nowarp">
            <div class="col-sm-8 float-left">
                <label class="pdf-label-1">Employee Number</label>
            </div>
            <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->experience->employee_num}}
                    </span>
            </div>
        </div>

        @endif

        </aside>
        <div class="clearfix"></div>
        <center><label class="col-sm-12 col-xs-12 orange label-b  hr-line-label"><b>Military Experience</b></label>
        </center>
        <aside>


            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">Are you a reservist/veteran of the Canadian Armed Forces, our allied forces, or
                        RCMP?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->veteran_of_armedforce }}
                    </span>
                </div>
            </div>
            <div class="clearfix"></div>
            @if($candidateJob->candidate->miscellaneous->veteran_of_armedforce == "Yes")

                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Service Number</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->service_number}}
                    </span>
                    </div>
                </div>
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Candidate Force Branch or RCMP</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->canadian_force}}
                    </span>
                    </div>
                </div>


                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Enrollement Date</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{date('F j, Y', strtotime($candidateJob->candidate->miscellaneous->enrollment_date))}}
                    </span>
                    </div>
                </div>




                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Release Date</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{date('F j, Y', strtotime($candidateJob->candidate->miscellaneous->release_date))}}
                    </span>
                    </div>
                </div>


                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Release Number</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->item_release_number}}
                    </span>
                    </div>
                </div>


                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Rank on Release</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->rank_on_release}}
                    </span>
                    </div>
                </div>

                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Military Occupation</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->military_occupation}}
                    </span>
                    </div>
                </div>


                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8 float-left">
                        <label class="pdf-label-1">Reason for Release</label>
                    </div>
                    <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->reason_for_release}}
                    </span>
                    </div>
                </div>


                <div class="clearfix"></div>
            @endif
            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">Are you the spouse of someone who served in the Canadian Armed
                        Forces?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->spouse_of_armedforce }}
                    </span>
                </div>
            </div>
            <div class="clearfix"></div>
        </aside>
        <div class="clearfix"></div>
        <center><label class="col-sm-12 col-xs-12 orange label-b  hr-line-label"><b>Indigenous Status</b></label>
        </center>
        <aside>


            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">Are you a native Indian/Indigenous person in Canada and hold an official
                        Certificate of Indian Status?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->is_indian_native }}
                    </span>
                </div>
            </div>

        </aside>
        <div class="clearfix"></div>
        <center><label class="col-sm-11 orange label-b  hr-line-label"><b>Dismissals</b></label></center>
        <aside>

            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">Have you ever been dismissed or asked to resign from employment?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->dismissed }}
                    </span>
                </div>
            </div>

            <div class="clearfix"></div>
            @if($candidateJob->candidate->miscellaneous->dismissed == "Yes")

                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">If you answered "Yes", please explain:</label>
                    </div>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                        {{$candidateJob->candidate->miscellaneous->explanation_dismissed}}
                        </span>
                    </div>
                </div>

            @endif
        </aside>
        <div class="clearfix"></div>
        <center><label class="col-sm-11 orange label-b  hr-line-label"><b>Other Requirements</b></label></center>
        <aside>

            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">The majority of our positions require good mobility, and good sensory
                        perception such as hearing, sight, and smell.<br> Applicants must be psychologically healthy and
                        should be capable of working alone for 24 hour rotating shifts.<br>Do you have any limitations
                        in these areas?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->limitations }}

                    </span>
                </div>
            </div>

            <div class="clearfix"></div>
            @if($candidateJob->candidate->miscellaneous->limitations == "Yes")
                <div class="form-group row flex-nowarp">
                    <div class="col-sm-8  float-left">
                        <label class="pdf-label-1">If you answered "Yes", please explain:</label>
                    </div>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                        {{$candidateJob->candidate->miscellaneous->limitation_explain}}
                        </span>
                    </div>
                </div>

            @endif
        </aside>
        <div class="clearfix"></div>
        <center><label class="col-sm-12 orange label-b  hr-line-label"><b>Criminal Convictions</b></label></center>
        <aside>

            <div class="form-group row flex-nowarp">
                <div class="col-sm-8 float-left">
                    <label class="pdf-label-1">Have you ever been convicted of a criminal offence for which you've not
                        received a pardon?</label>
                </div>
                <div class="col-sm-3 float-left ">
                    <span class="pdf-label-style pdf-border-normal">
                    {{$candidateJob->candidate->miscellaneous->criminal_convicted }}

                    </span>
                </div>
            </div>
            <div class="clearfix"></div>
            @if($candidateJob->candidate->miscellaneous->criminal_convicted == "Yes")
                <div class="row">
                    <label class="col-sm-11 pdf-label-1">If your answer was "Yes", please complete the section
                        below</label>
                </div>

                <div class="clearfix"></div>

                <div class="form-group row flex-nowarp">
                    <label class="col-sm-8 pdf-label-1 float-left">Offence</label>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->miscellaneous->offence}}</span>
                    </div>
                </div>

                <div class="form-group row flex-nowarp">
                    <label class="col-sm-8 pdf-label-1 float-left">Date</label>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($candidateJob->candidate->miscellaneous->offence_date))}}</span>
                    </div>
                </div>

                <div class="form-group row flex-nowarp">
                    <label class="col-sm-8 pdf-label-1 float-left">Location</label>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->miscellaneous->offence_location}}</span>
                    </div>
                </div>

                <div class="form-group row flex-nowarp">
                    <label class="col-sm-8 pdf-label-1 float-left">Disposition</label>
                    <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">{{$candidateJob->candidate->miscellaneous->disposition_granted}}</span>
                    </div>
                </div>

            @endif
        </aside>
        <div class="clearfix"></div>
        <center><label class="col-sm-11 orange label-b  hr-line-label"><b>Career Interests</b></label></center>
        <div class="clearfix"></div>
        <aside>
            <div class="form-group row">
                <div class="col-sm-8  float-left">
                    <label class="pdf-label-1">How would you describe your longer term career interests in
                        security?</label>
                </div>
                <div class="col-sm-3 float-left ">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->miscellaneous->career_interest }}
                        </span>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row">
                <div class="col-sm-8  float-left">
                    <label class="pdf-label-1">Would you consider other roles with Commissionaires beyond what you've
                        applied for?</label>
                </div>
                <div class="col-sm-3 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$candidateJob->candidate->miscellaneous->other_roles }}
                        </span>
                </div>
            </div>
        </aside>
        <div class="clearfix"></div>
        <?php $already_shown = null;?>
        @foreach($candidateJob->candidate->screening_questions as $screening_question)
            @if($screening_question->question->category==null || $screening_question->question->category!=$already_shown)
                <center><label
                            class="col-sm-11 orange label-b hr-line-label"><b>{{$screening_question->question->category }}</b></label>
                </center>
                <?php $already_shown = $screening_question->question->category;?>
            @endif
            <aside>
                <div class="form-group row">
                    <label class="col-sm-11 pdf-label-1 float-left">{{$screening_question->question->screening_question}}</label>
                </div>
                <div class="clearfix"></div>
                <div class="form-group row">
                    <div class="col-sm-11 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {{$screening_question->answer}}
                        </span>
                    </div>
                </div>
            </aside>
            <div class="clearfix"></div>
        @endforeach


        <div class="page-break">
            <div class="clearfix"></div>
            <center><label class="col-sm-11 orange label-b  hr-line-label"><b>Personality</b></label></center>
            <div class="form-group row">
                <label class="col-sm-11 float-left orange">
                    <b>
                        {{$candidateJob->candidate->personality_scores[0]->score_type->type}}
                    </b>
                </label>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row">
                <div class="col-sm-11 float-left">
                        <span class="pdf-label-style pdf-border-normal">
                            {!! nl2br($candidateJob->candidate->personality_scores[0]->score_type->description) !!}
                        </span>
                </div>
            </div>
        </div>


        <div class="clearfix"></div>
        <div class="page-break form-group">
            <center>
                <label class="col-sm-12 orange label-b hr-line-label"><b>Competency Assessment</b></label>
            </center>
            @foreach($candidateJob->candidate->competency_matrix as $competency_matrix)
                <div class="form-group">
                    <div class="form-group row flex-nowarp">
                        <div class="col-sm-6" style="width: 50%">
                            <label class="col-sm-6 blue float-left text-center"><b>Competency</b></label>
                            <div class="col-sm-6 float-left text-center">
                                <label class="pdf-label-1">{{$competency_matrix->competency_matrix->competency}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6" style="width: 50%">
                            <label class="col-sm-1 blue float-left "><b>Rating</b></label>
                            <div class="form-check col-sm-6 float-left">
                                <label class="form-check-label padding-left-clear pdf-label-1">
                                    {{$competency_matrix->competency_matrix_rating->rating}}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div style="page-break-inside: avoid">
                        <div class="row flex-nowarp">
                            <label class="col-sm-12 blue float-left"><b>Definition</b></label>
                        </div>
                        <div class="row flex-nowarp">
                            <div class="form-check col-sm-12 float-left">
                                <label class="form-check-label padding-left-clear blue">
                                    {{$competency_matrix->competency_matrix->definition}}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div style="page-break-inside: avoid">
                        <div class="row flex-nowarp">
                            <label class="col-sm-12 blue float-left"><b>Behaviors</b></label>
                        </div>
                        <div class="form-check col-sm-12 float-left pdf-border-normal">
                            <label class="form-check-label padding-left-clear blue">
                                {!! nl2br($competency_matrix->competency_matrix->behavior) !!}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            @endforeach
        </div>

        @if(Auth::user()!=null)
            @if($candidateJob->average_score > 0)
                <aside>
                    <div class="form-group row">
                        <label class="col-sm-3 pdf-label-1 float-left">Average Score</label>
                        <div class="col-sm-2 float-left ml-25">
                            <span class="pdf-label-style pdf-border-normal">{{$candidateJob->average_score}}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </aside>
            @endif @if(!$candidateJob->candidate->interviewnote->isEmpty())
                <center><label class="col-sm-11 orange label-b  hr-line-label"><b>Interview Notes</b></label></center>
                <aside>
                    <div class="form-group row flex-nowrap">
                        <label class="col-sm-3 pdf-label-1 float-left text-center mw">Interviewer</label>
                        <label class="col-sm-3 pdf-label-1 float-left text-center mw">Date</label>
                        <label class="col-sm-5 pdf-label-1 float-left text-center">Notes</label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group row flex-nowrap">
                        @foreach($candidateJob->candidate->interviewnote as $interviewnote)
                            <div class="col-sm-3 float-left mw">
                                <span class="pdf-label-style pdf-border-normal">{{$interviewnote->interviewers->full_name}}</span>
                            </div>
                            <div class="col-sm-3 float-left mw">
                                <span class="pdf-label-style pdf-border-normal">{{date('F j, Y', strtotime($interviewnote->interview_date))}}</span>
                            </div>
                            <div class="col-sm-5 float-left">
                                <span class="pdf-label-style pdf-border-normal">{{$interviewnote->interview_notes}}</span>
                            </div>

                        @endforeach
                    </div>
                </aside>
            @endif
        @endif
            <div class="page-break">
                <div class="clearfix"></div>
                <center><label class="col-sm-11 orange label-b hr-line-label"><b>Declaration</b></label></center>
                <aside>
                    <div class="form-group row">
                        <label class="col-sm-11 pdf-label-1 float-left">I declare that the information on this
                            application
                            is true, and I understand that any false statement will be cause for the immediate rejection
                            of
                            this application or the termination of my employment, if selected.</label>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 float-left">
                            <span class="pdf-label-style"></span>
                        </div>
                        <div class="col-sm-5"></div>
                        <div class="col-sm-3 float-right">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <span class="pdf-label-style">
                                {{$candidateJob->candidate->name}}
                            </span>
                        </div>
                        <div class="col-sm-5"></div>
                        <div class="col-sm-3 float-right">
                            <span class="pdf-label-style pdf-border-normal">
                                {{date('F j, Y', strtotime($candidateJob->submitted_date))}}
                            </span>
                        </div>
                    </div>
                </aside>
            </div>
        <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<div class="form-group row pdf-hide">
</div>

<div class="form-group row print-hide">
    <div class="col-sm-5"></div>
    <div class="col-sm-7">
        <div class="btn submit pdf-hide" onClick="window.print();">
            <a href="javascript:;"> Print Application </a>
        </div>
    </div>
</div>

</div>
@section('scripts')
    <script>
        $(function () {
            window.print();
            window.onafterprint = function (e) {
                if (window.location.href.indexOf('apply') > 0) {
                    swal({
                        title: "",
                        text: "Thank you for your interest in Commissionaires Great Lakes. However only those candidates selected for interviews will be contacted. Please check your email for the copy of your application.",
                        type: "success",
                    });
                }
            };
        });
    </script>
@stop
