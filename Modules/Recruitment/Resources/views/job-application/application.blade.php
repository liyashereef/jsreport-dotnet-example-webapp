<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0 auto;
            padding: 0;
            font-size: 14px;
        }

        .cgl-caption {
            font-size: 50px;
            font-weight: 200;
            text-transform: uppercase;
            color: #666;
            text-align: center;
        }

        .cgl-caption span {
            color: #f16b23;
        }

        .choice-heading {
            color: #093;
            font-size: 17px;
            font-weight: normal;
            text-transform: capitalize;
        }

        .application-heading {
            color: #0c3c62;
            font-size: 18px;
            text-transform: uppercase;
            padding: 10px 0;
            font-weight: bold;
        }

        .light-heading {
            color: #b1b1b1;
            font-size: 18px;
            text-transform: capitalize;
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
        }

        .brown-heading {
            color: #b35a35;
            font-size: 18px;
            text-transform: capitalize;
            text-align: left !important;
            padding: 0px 0;
            font-weight: bold;
        }

        .dark-heading {
            color: #0c3c62;
            font-size: 16px;
            text-transform: capitalize;
            text-align: left;
            padding: 10px 0;
            font-weight: bold;
        }

        .content-normal-caps {
            font-size: 18px;
            text-transform: uppercase;
            font-weight: normal;
        }

        .content-normal-caps {
            font-size: 18px;
            font-weight: normal;
        }

        .content-normal-bold-text {
            font-size: 14px;
            font-weight: normal;
            color: #0c3c62;
        }

        .content-caps-bold-text {
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
            color: #0c3c62;
        }

        .content-caps-small-text {
            font-size: 13px;
            font-weight: normal;
            color: #0c3c62;
        }

        .content-normal-bold {
            font-size: 22px;
            font-weight: bold;
        }

        .text-box-full {
            border-bottom: 2px solid #ccc !important;
            border: none;
            width: 100%;
            padding: 10px 0;
            margin: 0;
            color: #0c3c62;
            font-size: 17px;
        }

        .text-box-half {
            border-bottom: 2px solid #ccc !important;
            border: none;
            width: 100%;
            padding: 10px 0;
            margin: 0;
            color: #0c3c62;
            font-size: 17px;
        }

        .text-box-small {
            border-bottom: 2px solid #ccc !important;
            border: none;
            width: 60px;
            padding: 10px 0;
            margin: 0;
            color: #0c3c62;
            font-size: 17px;
            text-align: center;
        }

        .table-form {
            margin: 0 auto;
            padding: 0;
            border: none;
            border-collapse: collapse;
        }

        .table-form td {
            margin: 0;
            padding: 10px 10px;
            border: none;
            border-collapse: collapse;
        }

        .table-inner-form {
            width: 100%;
            margin: 0 auto;
            padding: 0;
            border: none;
            border-collapse: collapse;
        }

        .table-inner-form td {
            margin: 0;
            padding: 0px 0px;
            border: none;
            border-collapse: collapse;
        }

        .table-inner-space {
            width: 100%;
            margin: 0 auto;
            padding: 0;
            border: none;
            border-collapse: collapse;
            text-align: center;
        }

        .table-inner-space td {
            margin: 0;
            padding: 6px 0px;
            border: none;
            border-collapse: collapse;
        }

        .pdf-head {
            margin-top: 10px;
        }

        .green {
            color: #008000;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .label-b {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }


        .row-custum {
            width: 100%;
            float: left;
        }

        .pdf-column-left {
            float: left;
            width: 30%;
        }

        .pdf-column-right {
            float: left;
            width: 65%;
        }


        .blue {
            color: #003A63;
            font-size: 14px;
        }

        .orange {
            color: #F48452;
        }

        .pdf-label-style {
            color: #003a63;
            font-size: 14px;
        }

        .pdf-border-normal {
            border-bottom: solid 1px #003a63;
            width: 100%;
            float: left;
            padding: 0 5px 5px 0px;
        }

        .label-b {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .hr-line-label {
            font-size: 20px;
            margin-top: 20px;
            margin-bottom: 18px;
            border-bottom: solid 1px rgba(204, 204, 204, 0.45);
            padding-bottom: 14px;
        }

        .page-break {
            page-break-before: always;
            page-break-inside: avoid;
        }

        .page-break-after {
            page-break-after: always;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
<table
        width="90%"
        class="table-form page-break-after"
        >
    <tr>
        <td height="200px"></td>
    </tr>
    <tr>
        <td width="50%" style="text-align: right">
            <img style="border-radius: 50%; margin: 0px 5vh;"
                 src="{{public_path()."/images/uploads/".$candidateJob->candidate->profile_image }}"
                 height="150px" width="150px" name="image" id="profile-img"
            >
        </td>
        <td width="50%">
            <table>
                <tr>
                    <td class="pdf-label-style pdf-border-normal" style="border-bottom:2px solid black">
                                    Candidate Screening Assessment
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="orange" style="font-size: 25px; font-weight: bold;">
                                    {{$candidateJob->candidate->name}}
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" height="500px">

        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td style="text-align: right">
            <img src="{{public_path()}}/images/CGL-LOGO-600px-152px.png" width="150px">
        </td>
    </tr>
</table>

<table width="90%" border="0" class="table-form">

    <tr>
        <td colspan="6" class="cgl-caption" style="text-align: center">
            <img src="{{public_path()}}/images/logo.png">
            <div class="blue label-b">Application for Employment</div>
        </td>
    </tr>
    <tr>
        <td class="content-normal-bold-text" colspan="4" width="35%"> Full Legal Name</td>
        <td colspan="2" class="content-caps-small-text"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->name}}</td>
    </tr>
    <tr>
        <td class="content-normal-bold-text" colspan="4" width="35%"> Date of Birth
        </td>
        <td colspan="2" class="content-caps-small-text"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->dob))}}</td>
    </tr>

    <tr>
        <td class="content-normal-bold-text" colspan="4" width="35%">Home Phone Number</td>
        <td colspan="2" class="content-caps-small-text"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">
            @if($candidateJob->candidate->phone != "")
                {{$candidateJob->candidate->phone}}
            @else
                --
            @endif
        </td>
    </tr>


    <tr>
        <td class="content-normal-bold-text" colspan="4" width="35%">Cellular Number</td>
        <td colspan="2" class="content-caps-small-text"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">
            @if($candidateJob->candidate->phone_cellular != "")
                {{$candidateJob->candidate->phone_cellular}}
            @else
                --
            @endif
        </td>
    </tr>


    <tr>
        <td class="content-normal-bold-text" colspan="4">Email Address</td>
        <td class="content-caps-small-text" style="border-bottom: solid 1px #003a63;font-size: 14px;"
            colspan="2"> {{$candidateJob->candidate->email}} </td>
    </tr>

    @if(isset($candidateJob->candidate->profile_image))
        <tr>
            <td class="content-normal-bold-text" colspan="4" width="35%" style="padding-top: 40px;">Profile Picture</td>
            <td colspan="2" class="content-caps-small-text" style="font-size: 14px;">
                <img style="border-radius: 50%;"
                     src="{{public_path()."/images/uploads/".$candidateJob->candidate->profile_image }}"
                     height="100px"
                     width="100px" name="image" id="profile-img"/>
            </td>
        </tr>
    @endif

    <tr>
        <td class="content-normal-bold-text" colspan="4" width="35%">Apartment Number/Street Address</td>
        <td colspan="2" class="content-caps-small-text"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->address}}</td>
    </tr>


    <tr>
        <td class="content-normal-bold-text" colspan="4" width="35%">City</td>
        <td colspan="2" class="content-caps-small-text"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->city}}</td>
    </tr>


    <tr>
        <td class="content-normal-bold-text" colspan="4" width="35%">Postal Code</td>
        <td colspan="2" class="content-caps-small-text"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->postal_code}}</td>
    </tr>

<!-- <tr>
    <td colspan="4" class="content-caps-bold-text" rowspan="4">ADDRESS</td>
    <td class="content-caps-small-text"style="border-bottom: solid 1px #003a63;font-size: 14px;" colspan="2">{{$candidateJob->candidate->address}}</td>
  </tr>
<tr class="content-caps-bold-text">
    <td colspan="2"><center style="font-size: 14px; color: #443a3f;" >Apartment Number/ Street Address</center></td>
  </tr> -->
<!-- <tr>
<td  style="border-bottom: solid 1px #003a63;font-size: 14px;" >{{$candidateJob->candidate->city}}</td>
    <td  style="border-bottom: solid 1px #003a63;font-size: 14px;" >{{$candidateJob->candidate->postal_code}}</td>
  </tr>
  <tr class="content-caps-bold-text">
    <td><center style="font-size: 14px; color: #443a3f;" >City</center></td>
    <td><center style="font-size: 14px; color: #443a3f;" >Postal Code</center></td>
  </tr> -->

    @if(!$candidateJob->candidate->addresses->isEmpty())

        <tr class="content-normal-bold-text">
            <td colspan="6"><br><br>Past address over last five years (Required for RCMP background check)</td>
        </tr>
        @foreach($candidateJob->candidate->addresses as $pastAddress)

            <tr class="content-normal-bold-text">
                <td colspan="4" width="30%"> Address</td>
                <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$pastAddress->address}}
                </td>
            </tr>
            <tr class="content-normal-bold-text">
                <td colspan="4" width="30%"> From</td>
                <td colspan="2"
                    style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($pastAddress->from))}}
                </td>
            </tr>
            <tr class="content-normal-bold-text">
                <td colspan="4" width="30%"> To</td>
                <td colspan="2"
                    style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($pastAddress->to))}}
                </td>
            </tr>

        @endforeach

    @endif


    {{-- <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Position Information</td>
    </tr>
    

 

    <tr class="content-normal-bold-text">
        <td colspan="4">Wage per Hour</td>
        <td colspan="2" style="border-bottom: solid 1px #003a63;font-size: 14px;">
             {{number_format($candidateJob->candidate->wageexpectation->wage,2)}} 
           </td>
    </tr> --}}

    <tr class="light-heading light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Orientation
        </td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4"> Did you attend an orientation session hosted by our SVP/COO prior to applying.</td>
        @if($candidateJob->candidate->referalAvailibility->orientation == "1")
            <td colspan="2" style="border-bottom: solid 1px #003a63;font-size: 14px;"> Yes</td>
        @else
            <td colspan="2" style="border-bottom: solid 1px #003a63;font-size: 14px;"> No</td>
        @endif
    </tr>

    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Referral
        </td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">How did you find out about this job posting
        </td>
        <td colspan="2"
            style="border-bottom: solid 1px #003a63;font-size: 14px;">
            @if(isset($candidateJob->candidate->referalAvailibility->jobPostFinding->job_post_finding))
                {{$candidateJob->candidate->referalAvailibility->jobPostFinding->job_post_finding }}
            @else
                --
            @endif
        </td>
    </tr>
     @if(isset($candidateJob->candidate->referalAvailibility->sponser_email))
    <tr class="content-normal-bold-text">
        <td colspan="4">Please enter the email address of the person who referred you to Commissionaires. Please make
            sure to accurately enter the email address or your sponsor will not get the referral credit.
        </td>
        <td colspan="2"
            style="border-bottom: solid 1px #003a63;font-size: 14px;">
            @if(isset($candidateJob->candidate->referalAvailibility->sponser_email))
                {{$candidateJob->candidate->referalAvailibility->sponser_email }}
            @else
                --
            @endif
        </td>
    </tr>
    @endif
    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Availability</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4" width="30%"> Would you be willing to start as a "floater/spare" until a permanent position comes
            up, or are you only interested in assignments you've applied to.
        </td>
        <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;">
            {{ config('globals.position_availibility')[$candidateJob->candidate->referalAvailibility->position_availibility ]}}</td>
    </tr>
   {{--  @if($candidateJob->candidate->referalAvailibility->position_availibility == "1")
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%"> How many hours a week are you looking for ?</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->referalAvailibility->floater_hours }}</td>
        </tr>
    @endif --}}
    <tr class="content-normal-bold-text">
        <td colspan="4" width="30%">How soon could you start ?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{config('globals.starting_time')[$candidateJob->candidate->referalAvailibility->starting_time] }}</td>
    </tr>

    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Fit Assessment</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Prior to our online ad, had you heard about Commissionaires?</td>
        <td colspan="2"
            style="border-bottom: solid 1px #003a63;font-size: 14px;">
            @if(isset($candidateJob->candidate_brand_awareness->answer))
                {{$candidateJob->candidate_brand_awareness->answer}}
            @else
                --
            @endif
        </td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Prior to our online ad, how familiar are you with Garda, G4S,Securitas or Palladin?</td>
        <td colspan="2"
            style="border-bottom: solid 1px #003a63;font-size: 14px;">
            @if(isset($candidateJob->candidate_security_awareness->answer))
                {{$candidateJob->candidate_security_awareness->answer}}
            @else
                --
            @endif
        </td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">How many hours per week would you prefer to work?</td>
        <td colspan="2"
            style="border-bottom: solid 1px #003a63;font-size: 14px;">
                {{$candidateJob->candidate->awareness->prefered_hours_per_week or '--'}}
        </td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Please share your understanding of Commissionaires <b><u>PRIOR</u></b> to applying</td>
        <td colspan="2"
            style="border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->comissionaires_understanding[0]->candidateUnderstandingLookup->commissionaires_understandings}}</td>
    </tr>

    {{-- <tr class="content-normal-bold-text">
        <td colspan="4">Please elaborate why you are applying for this specific role, and why you think you would
            succeed in the role
        </td>
        <td colspan="2"
            style="border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->fit_assessment_why_apply_for_this_job }}</td>
    </tr> --}}


    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Licensing Information And Security Guarding Experience</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Do you have valid security guarding licence in ontario with First Aid and CPR?</td>
        <td colspan="2"
            style="border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->guardingexperience->guard_licence }}</td>
    </tr>


    @if($candidateJob->candidate->guardingexperience->guard_licence == "Yes")
        <tr class="light-heading orange label-b hr-line-label">
            <td colspan="6">Licence Start Date</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%"> Guarding Licence In Ontario</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->start_date_guard_license))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%">First Aid Certificate</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->start_date_first_aid))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%">CPR Certificate</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->start_date_cpr))}}</td>
        </tr>

        <tr class="light-heading orange label-b hr-line-label">
            <td colspan="6"><br><br>Licence End Date</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%"> Guarding Licence in Ontario</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->expiry_guard_license))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%">First Aid Certificate</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->expiry_first_aid))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%">CPR Certificate</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->expiry_cpr))}}</td>
        </tr>

    
        <tr class="light-heading orange label-b hr-line-label">
            <td colspan="6"><br><br>Security Clearance Information</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%"> Do you have a valid security clearance ?</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->guardingexperience->security_clearance }}</td>
        </tr>
        @if($candidateJob->candidate->guardingexperience->security_clearance == "Yes")
            <tr class="content-normal-bold-text">
                <td colspan="4" width="30%"> What type of security clearance ?</td>
                <td colspan="2"
                    style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->guardingexperience->security_clearance_type }}</td>
            </tr>
            <tr class="content-normal-bold-text">
                <td colspan="4" width="30%"> Enter the expiry date</td>
                <td colspan="2"
                    style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->security_clearance_expiry_date))}}
                    <
                </td>
            </tr>
        @endif
    @endif
    @if(isset($candidateJob->candidate->guardingexperience->test_score_percentage))
            <tr class="light-heading orange label-b hr-line-label">
                <td colspan="6"><br><br>Ontario Security Guard Test Scores<br></td>
            </tr>
            <tr class="content-normal-bold-text">
                <td colspan="4">What was your test score on the Ontario Security Guard exam? (Percent)</td>
                <td colspan="2"
                    style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->guardingexperience->test_score_percentage }}</td>
            </tr>
        @endif

               @if (isset($candidateJob->candidate->force))
                @if ($candidateJob->candidate->force->force == "Yes")
                <tr class="light-heading orange label-b hr-line-label">
                <td colspan="6"><br><br>Use Of Force<br></td>
            </tr>

             <tr class="content-normal-bold-text">
                <td colspan="4">Are you use of force certified?</td>
                <td colspan="2"
                    style=" border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->force->force }}
            </tr>

             <tr class="content-normal-bold-text">
                <td colspan="4">If yes, please provide your certification</td>
                <td colspan="2"
                    style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->force->force_lookup->use_of_force or '--'}}
            </tr>

             <tr class="content-normal-bold-text">
                <td colspan="4">When does your certification expire?</td>
                <td colspan="2"
                    style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{date('F j, Y', strtotime($candidateJob->candidate->force->expiry))}}
            </tr>

                @else

                 <tr class="light-heading orange label-b hr-line-label">
                <td colspan="6"><br><br>Use Of Force<br></td>
            </tr>
            <tr class="content-normal-bold-text">
                <td colspan="4">Are you use of force certified?</td>
                <td colspan="2"
                    style=" border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->force->force }}
            </tr>
                   
                @endif
            @endif

    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Security Guarding Experience</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4" width="30%">Do you have a valid Social Insurance Number in Canada?</td>
        @if(isset($candidateJob->candidate->guardingexperience->social_insurance_number)&& (@$candidateJob->candidate->guardingexperience->social_insurance_number)==1)
            <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;"><label
                        class="padding-5">Yes</label></td>
        @endif
        @if(isset($candidateJob->candidate->guardingexperience->social_insurance_number)&& (@$candidateJob->candidate->guardingexperience->social_insurance_number)==0)
            <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;"><label
                        class="padding-5">No</label></td>

    @endif

    <!-- <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->expiry_cpr))}}</td>  -->
    </tr>

    @if($candidateJob->candidate->guardingexperience->social_insurance_number == 1)
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%">Do you have an expiry date on your SIN ?</td>
            @if(isset($candidateJob->candidate->guardingexperience->sin_expiry_date_status)&& (@$candidateJob->candidate->guardingexperience->sin_expiry_date_status)==1)
                <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;"><label
                            class="padding-5">Yes</label></td>
            @endif
            @if(isset($candidateJob->candidate->guardingexperience->sin_expiry_date_status)&& (@$candidateJob->candidate->guardingexperience->sin_expiry_date_status)==0)
                <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;"><label
                            class="padding-5">No</label></td>

            @endif
        </tr>
    @endif

    @if($candidateJob->candidate->guardingexperience->sin_expiry_date_status ==1)
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%">Expiry date of your SIN</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->sin_expiry_date))}}</td>

        </tr>
    @endif


    <tr class="content-normal-bold-text">
        <td colspan="4">How many total years of security guarding
            experience do you have?
        </td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">
            {{$candidateJob->candidate->guardingexperience->years_security_experience}}
        </td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">What is the most senior position you have held in security?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{@$candidateJob->candidate->guardingexperience->most_senior_position_held>0? @$candidateJob->candidate->guardingexperience->position->position:'Other'}}</td>
    </tr>


    <tr>
        <td colspan="6" class="dark-heading"><br><br>Please list all the positions you've held and years of
            experience<br><br></td>
    </tr>


    @foreach(json_decode($candidateJob->candidate->guardingexperience->positions_experinces) as $key=>$value)
        <tr class="content-normal-bold-text">
            <td colspan="4">{{ ucwords(str_replace("_"," ",$key)) }}</td>
            <td style=" border-bottom: solid 1px #003a63;font-size: 14px;" colspan="1">{{ $value }}</td>
        </tr>
    @endforeach


    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Compensation<br><br></td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">What are your wage exceptions (Per Hour)?</td>
        <td style=" border-bottom: solid 1px #003a63;font-size: 14px;" colspan="2">
            ${{number_format($candidateJob->candidate->wageexpectation->wage_expectations, 2)}} 
        </td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">What was your last hourly wage within the security guarding industry?</td>
        <td style=" border-bottom: solid 1px #003a63;font-size: 14px;" colspan="2">
            ${{number_format($candidateJob->candidate->wageexpectation->wage_last_hourly, 2)}}</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">How many hours per week were you working at this wage?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->wageexpectation->wage_last_hours_per_week or '--'}} hours per week</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Can you validate your current wage with a paystup as evidence if we pay a higher wage?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->wageexpectation->current_paystub}}</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Who was the security provider that paid the wage?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->wageexpectation->wageprovider->security_provider}}</td>
    </tr>

    @if($candidateJob->candidate->wageexpectation->wageprovider->security_provider=="Other")
        <tr class="content-normal-bold-text">
            <td colspan="4">Please enter the name of the security provider that paid your previous wage?</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->wageexpectation->wage_last_provider_other}}</td>
        </tr>
    @endif

    @php
        $security_provider_name=isset($candidateJob->candidate->wageexpectation->wage_last_provider) && $candidateJob->candidate->wageexpectation->wageprovider->security_provider!='Other' ? $candidateJob->candidate->wageexpectation->wageprovider->security_provider : $candidateJob->candidate->wageexpectation->wage_last_provider_other;
    @endphp


    <tr class="content-normal-bold-text">
        <td colspan="4">What were the strengths of {{$security_provider_name}}</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{@$candidateJob->candidate->wageexpectation->security_provider_strengths}}</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">What do you hope to get from Commissionaires that you feel {{$security_provider_name}} was not providing?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{@$candidateJob->candidate->wageexpectation->security_provider_notes}}</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">How would you rate your experience at {{$security_provider_name}}?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{@$candidateJob->candidate->wageexpectation->rating->experience_ratings}}</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">What was your previous role? (at your last wage rate)</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{@$candidateJob->candidate->wageexpectation->last_role_held>0?@$candidateJob->candidate->wageexpectation->lastrole->position:'Other'}}</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Please justify/explain your wage expectation. Why do you think you're worth the wage you are
            asking for?
        </td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->wageexpectation->explanation_wage_expectation}}</td>
    </tr>
    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Availability<br><br></td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">When you are available to start?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->availability->availability_start))}}</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">What is your current availability?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->availability->current_availability }}</td>
    </tr>


    @if($candidateJob->candidate->availability->current_availability == "Part-Time (Less than 40 hours per week)")
        <tr class="content-normal-bold-text">
            <td colspan="4">If only part time - please briefly explain your
                        limitation</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{!! $candidateJob->candidate->availability->availability_explanation !!}</td>
        </tr>
    @endif 
       @if($candidateJob->candidate->availability->days_required)
    <tr class="content-normal-bold-text">
            <td colspan="4">Which days are you available to work?<br><i>(Select those days you
                                are available to work. If you are joining our spares pool, we will expect you to work on
                                the days and shifts you select)</i></td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{!! $candidateJob->candidate->availability->days_required !!}</td>
        </tr>
        @endif

            @if($candidateJob->candidate->availability->shifts)
              <tr class="content-normal-bold-text">
            <td colspan="4">Which shifts are you willing to work?</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{!! $candidateJob->candidate->availability->shifts !!}</td>
        </tr>
            @endif

    <tr class="content-normal-bold-text">
        <td colspan="6"><br>Most of our assignments require employee to work 8-12 hours shifts on a rotating basis. As a
            result
            you are likely to be required to be available to work at any time day or night, 7days per week. Please
            note that Monday to Friday or day shifts are rarely available.
        </td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Do you understand the shift availability as noted above?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->availability->understand_shift_availability }}</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Are you available for shift work including evenings and nights?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->availability->available_shift_work }}</td>
    </tr>

    @if($candidateJob->candidate->availability->available_shift_work == "No")
        <tr class="content-normal-bold-text">
            <td colspan="4">If you answered "no", please explain your restrictions:</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->availability->explanation_restrictions}}</td>
        </tr>
    @endif

    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Security Clearance<br><br></td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Were you born outside of Canada?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityclearance->born_outside_of_canada }}</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Please indicate your working status in Canada?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityclearance->work_status_in_canada }}</td>
    </tr>


    <tr class="content-normal-bold-text">
        <td colspan="4">How many years have you lived in Canada(approximately)?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityclearance->years_lived_in_canada}}</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Are you prepared to submit to a security screening?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityclearance->prepared_for_security_screening }}</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4"><br>Do you have reason to believe you may NOT be granted a clearance?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityclearance->no_clearance }}</td>
    </tr>

    @if($candidateJob->candidate->securityclearance->no_clearance == "Yes")

        <tr class="content-normal-bold-text">
            <td colspan="4"><br>If you answered "Yes", please explain:</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityclearance->no_clearance_explanation}}</td>
        </tr>

    @endif


    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br><br>Proximity To Client Site</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Do you have a valid drivers Licence?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityproximity->driver_license}}</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Do you have access to a vehicle?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityproximity->access_vehicle }}</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">If you do not have a licence or access to a vehicle, do you have access to public transit?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityproximity->access_public_transport }}</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Does your method of transporation limit your availablity?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->securityproximity->transportation_limitted }}</td>
    </tr>


    @if($candidateJob->candidate->securityproximity->transportation_limitted == "Yes")
        <tr class="content-normal-bold-text">
            <td colspan="4"><br>If you answered "Yes", please explain</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->securityproximity->explanation_transport_limit}}</td>
        </tr>
    @endif


    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Employment History<br></td>
    </tr>
    @foreach($candidateJob->candidate->employment_history as $i=>$employment_history)
        <tr class="brown-heading">
            <td colspan="6">Employer {{$i+1}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Start Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($employment_history->start_date))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">End Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($employment_history->end_date))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Employer</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$employment_history->employer}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Role</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$employment_history->role}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Duties</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$employment_history->duties}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Reason for Leaving</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$employment_history->reason}}</td>
        </tr>
    @endforeach


    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>References</td>
    </tr>
    @foreach($candidateJob->candidate->references as $i=>$references)
        <tr class="brown-heading">
            <td colspan="6">Reference {{$i+1}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Name</td>
            <td style=" border-bottom: solid 1px #003a63;font-size: 14px;"
                colspan="2">{{$references->reference_name}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Employer</td>
            <td style=" border-bottom: solid 1px #003a63;font-size: 14px;"
                colspan="2">{{$references->reference_employer}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Position</td>
            <td style=" border-bottom: solid 1px #003a63;font-size: 14px;"
                colspan="2">{{$references->reference_position}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Phone</td>
            <td style=" border-bottom: solid 1px #003a63;font-size: 14px;"
                colspan="2">{{$references->contact_phone}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Email</td>
            <td style=" border-bottom: solid 1px #003a63;font-size: 14px;"
                colspan="2">{{$references->contact_email}}</td>
        </tr>
    @endforeach


    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Education</td>
    </tr>

    @foreach($candidateJob->candidate->educations as $i=>$educations)
        <tr class="brown-heading">
            <td colspan="6">Education {{$i+1}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Start Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($educations->start_date_education))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">End Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($educations->end_date_education))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Grade</td>
            <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$educations->grade}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Program</td>
            <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$educations->program}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">School/Institute</td>
            <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$educations->school}}</td>
        </tr>

    @endforeach



    <tr>
        <td colspan="6">
            <table border="0" class="table-inner-space page-break-after">
                <tr class="light-heading orange label-b hr-line-label">
                    <td colspan="6"><br><br>Languages</td>
                </tr>
                <tr class="content-caps-bold-text">
                    <td width="20%">Skill Level</td>
                    <td width="20%">A- Limited</td>
                    <td width="20%">B- Functional</td>
                    <td width="20%">C- Fluent</td>
                    <td width="20%">D- No knowledge</td>
                </tr>


                @foreach($candidateJob->candidate->languages as $each_language_skill)
                    <tr>
                        <td colspan="5"
                            class="brown-heading">{{ $each_language_skill->language_looukp->language }} </td>
                    </tr>
                    <tr class="content-normal-bold-text">
                        <td width="20%" style=" text-align:left;">Speaking/Oral comprehension</td>
                        @if($each_language_skill->speaking=='A - Limited - I am just learning the language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> A</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> A</td>
                        @endif

                        @if($each_language_skill->speaking=='B - Functional - this is my second language but I can get by.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> B</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> B</td>
                        @endif

                        @if($each_language_skill->speaking=='C - Fluent - this is my native language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> C</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> C</td>
                        @endif

                        @if($each_language_skill->speaking=='D - No Knowledge.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> D</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> D</td>
                        @endif
                    </tr>

                    <tr class="content-normal-bold-text">
                        <td width="20%" style=" text-align:left;">Reading</td>
                        @if($each_language_skill->reading=='A - Limited - I am just learning the language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> A</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> A</td>
                        @endif

                        @if($each_language_skill->reading=='B - Functional - this is my second language but I can get by.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> B</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> B</td>
                        @endif

                        @if($each_language_skill->reading=='C - Fluent - this is my native language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> C</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> C</td>
                        @endif

                        @if($each_language_skill->reading=='D - No Knowledge.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> D</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> D</td>
                        @endif
                    </tr>

                    <tr class="content-normal-bold-text">
                        <td width="20%" style=" text-align:left;">Writing</td>

                        @if($each_language_skill->writing=='A - Limited - I am just learning the language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> A</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> A</td>
                        @endif

                        @if($each_language_skill->writing=='B - Functional - this is my second language but I can get by.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> B</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> B</td>
                        @endif

                        @if($each_language_skill->writing=='C - Fluent - this is my native language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> C</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> C</td>
                        @endif

                        @if($each_language_skill->writing=='D - No Knowledge.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> D</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> D</td>
                        @endif
                    </tr>

                @endforeach
                @foreach($candidateJob->candidate->other_languages as $key => $each_language_skill)
                    @if($key == 4)
            </table>
            <table border="0" class="table-inner-space">
                    @endif
                    <tr>
                        <td colspan="5"
                            class="brown-heading"> {{$each_language_skill->language_lookup->language }} </td>
                    </tr>
                    <tr class="content-normal-bold-text">
                        <td width="20%" style=" text-align:left;">Speaking/Oral comprehension</td>
                        @if($each_language_skill->speaking=='A - Limited - I am just learning the language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> A</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> A</td>
                        @endif

                        @if($each_language_skill->speaking=='B - Functional - this is my second language but I can get by.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> B</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> B</td>
                        @endif

                        @if($each_language_skill->speaking=='C - Fluent - this is my native language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> C</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> C</td>
                        @endif

                        @if($each_language_skill->speaking=='D - No Knowledge.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> D</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> D</td>
                        @endif
                    </tr>

                    <tr class="content-normal-bold-text">
                        <td width="20%" style=" text-align:left;">Reading</td>
                        @if($each_language_skill->reading=='A - Limited - I am just learning the language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> A</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> A</td>
                        @endif

                        @if($each_language_skill->reading=='B - Functional - this is my second language but I can get by.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> B</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> B</td>
                        @endif

                        @if($each_language_skill->reading=='C - Fluent - this is my native language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> C</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> C</td>
                        @endif

                        @if($each_language_skill->reading=='D - No Knowledge.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> D</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> D</td>
                        @endif
                    </tr>

                    <tr class="content-normal-bold-text">
                        <td width="20%" style=" text-align:left;">Writing</td>

                        @if($each_language_skill->writing=='A - Limited - I am just learning the language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> A</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> A</td>
                        @endif

                        @if($each_language_skill->writing=='B - Functional - this is my second language but I can get by.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> B</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> B</td>
                        @endif

                        @if($each_language_skill->writing=='C - Fluent - this is my native language.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> C</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> C</td>
                        @endif

                        @if($each_language_skill->writing=='D - No Knowledge.')
                            <td width="20%"><img src="{{public_path()}}/images/checked_checkbox.png"> D</td>
                        @else
                            <td width="20%"><img src="{{public_path()}}/images/checkbox_unchecked.png"> D</td>
                        @endif
                    </tr>

                @endforeach

            </table>

            <table border="0" class="table-inner-space">
                <tr class="light-heading orange label-b hr-line-label">
                    <td colspan="5"><br><br>Special Skills<br><br></td>
                </tr>
                <tr class="content-caps-bold-text">
                    <td colspan="6" style=" text-align:left;">Please indicate the computer applications you've used in
                        the past<br></td>
                </tr>
                <tr class="content-caps-bold-text">
                    <td width="16.6%">&nbsp;</td>
                    <td width="12%">No</td>
                    <td width="12%">Basic</td>
                    <td width="12%">Good</td>
                    <td width="12%">Advanced</td>
                    <td width="12%">Expert</td>
                </tr>

                @foreach($candidateJob->candidate->skills as $eachSkill) @if($eachSkill->skill_lookup->category=='Special Skills')

                    <tr class="content-normal-bold-text">
                        <td width="16.6%" style=" text-align:left;">{{ $eachSkill->skill_lookup->skills }}</td>
                        @if($eachSkill->skill_level=='No Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                        @if($eachSkill->skill_level=='Basic Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                        @if($eachSkill->skill_level=='Good Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                        @if($eachSkill->skill_level=='Advanced Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                        @if($eachSkill->skill_level=='Expert Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                    </tr>

                @endif @endforeach


            </table>
            <table border="0" class="table-inner-space">
                <tr class="light-heading orange label-b hr-line-label">
                    <td colspan="6"><br><br>Soft Skills<br><br></td>
                </tr>
                <tr class="content-caps-bold-text">
                    <td colspan="6" style=" text-align:left;">Please rate the following "soft" skills</td>
                </tr>
                <tr class="content-caps-bold-text">
                    <td width="16.6%">&nbsp;</td>
                    <td width="12%">No</td>
                    <td width="12%">Basic</td>
                    <td width="12%">Good</td>
                    <td width="12%">Advanced</td>
                    <td width="12%">Expert</td>
                </tr>

                @foreach($candidateJob->candidate->skills as $eachSkill) @if($eachSkill->skill_lookup->category=='Soft Skills')

                    <tr class="content-normal-bold-text">
                        <td width="16.6%" style=" text-align:left;">{{ $eachSkill->skill_lookup->skills }}</td>

                        @if($eachSkill->skill_level=='No Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                        @if($eachSkill->skill_level=='Basic Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                        @if($eachSkill->skill_level=='Good Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                        @if($eachSkill->skill_level=='Advanced Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                        @if($eachSkill->skill_level=='Expert Knowledge')
                            <td width="12%"><img src="{{public_path()}}/images/checked_checkbox.png"></td>
                        @else
                            <td width="12%"><img src="{{public_path()}}/images/checkbox_unchecked.png"></td>
                        @endif
                        @endif @endforeach

                    </tr>
            </table>
        </td>
    </tr>

    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Technical Summary <br></td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4" width="30%">Please indicate if you have a Smartphone?</td>
        @if(isset($candidateJob->candidate->smart_phone_type_id))
            <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;"><label
                        class="padding-5">Yes</label></td>
        @endif
        @if(!isset($candidateJob->candidate->smart_phone_type_id))
            <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;"><label
                        class="padding-5">No</label></td>

    @endif

    <!-- <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->guardingexperience->expiry_cpr))}}</td>  -->
    </tr>

    @if(isset($candidateJob->candidate->smart_phone_type_id))
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%">If you have a smart phone what kind of phone is it?</td>
            <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;"><label
                        class="padding-5"> {{$candidateJob->candidate->technicalSummary->type}}</label></td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4" width="30%">How would you rate your proficiency with using apps on your mobile phone?</td>
            <td colspan="2" style=" border-bottom: solid 1px #003a63;font-size: 14px;">
                <label>  {{$candidateJob->candidate->smart_phone_skill_level}}</label></td>

        </tr>
    @endif

    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Commissionaires Experience<br></td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Are you a current employee of Commissionaires Great Lakes?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->experience->applied_employment or 'No' }}</td>
    </tr>

    @if( isset($candidateJob->candidate->experience) && $candidateJob->candidate->experience->current_employee_commissionaries == "Yes")
        <tr class="content-normal-bold-text">
            <td colspan="4">Employee Number</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->experience->employee_number}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Currently Posted Site</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->experience->currently_posted_site}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Position</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->experience->position}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Hours/Week</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->experience->hours_per_week}}</td>
        </tr>
    @endif


    <tr class="content-normal-bold-text">
        <td colspan="4">Have you ever applied for employment with Commissionaires Great Lakes?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->experience->applied_employment or 'No' }}</td>
    </tr>


    @if(isset($candidateJob->candidate->experience) && $candidateJob->candidate->experience->applied_employment == "Yes")
        <tr class="content-normal-bold-text">
            <td colspan="6"><br><br>If your answer was "Yes"</td>
        </tr>

        <tr class="content-normal-bold-text">
            <td colspan="4">Start Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->experience->start_date_position_applied))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">End Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->experience->end_date_position_applied))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Position</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->experience->position_applied}}</td>
        </tr>
    @endif

    <tr class="content-normal-bold-text">
        <td colspan="4">Have you ever been employed by the corps of Commissionaires?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->experience->employed_by_corps or 'No' }}</td>
    </tr>

    @if(isset($candidateJob->candidate->experience) && $candidateJob->candidate->experience->employed_by_corps == "Yes")
        <tr class="content-normal-bold-text">
            <td colspan="6"><br><br>If your answer was "Yes"</td>

            </td>
        </tr>

        <tr class="content-normal-bold-text">
            <td colspan="4">Position</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->experience->position_employed}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Start Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->experience->start_date_employed))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">End Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->experience->end_date_employed))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Division</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{@$candidateJob->candidate->experience->division->division_name}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Employee Number</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->experience->employee_num}}</td>
        </tr>
    @endif

    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Military Experience<br></td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Are you a reservist/veteran of the Canadian Armed Forces, our allied forces, or RCMP?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->miscellaneous->veteran_of_armedforce or 'No' }}</td>
    </tr>


    @if(isset($candidateJob->candidate->miscellaneous) && $candidateJob->candidate->miscellaneous->veteran_of_armedforce == "Yes")

        <tr class="content-normal-bold-text">
            <td colspan="4">Service Number</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->miscellaneous->service_number}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Candidate Force Branch or RCMP</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->miscellaneous->canadian_force}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Enrollement Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->miscellaneous->enrollment_date))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Release Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->miscellaneous->release_date))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Release Number</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->miscellaneous->item_release_number}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Rank on Release</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->miscellaneous->rank_on_release}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Military Occupation</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->miscellaneous->military_occupation}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Reason For Release</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->miscellaneous->reason_for_release}}</td>
        </tr>

    @endif

    <tr class="content-normal-bold-text">
        <td colspan="4">Are you the spouse of someone who served in the Canadian Armed Forces?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->miscellaneous->spouse_of_armedforce or 'No'}}</td>
    </tr>
    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Indigenous Status<br></td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Are you a native Indian/Indigenous person in Canada and hold an official Certificate of Indian
            Status?
        </td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->miscellaneous->is_indian_native or 'No' }}</td>
    </tr>

    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Dismissals</td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Have you ever been dismissed or asked to resign from employment?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->miscellaneous->dismissed or 'No'}}</td>
    </tr>

    @if(isset($candidateJob->candidate->miscellaneous) && $candidateJob->candidate->miscellaneous->dismissed == "Yes")
        <tr class="content-normal-bold-text">
            <td colspan="4">If you answered "Yes", please explain :</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->miscellaneous->explanation_dismissed}}</td>
        </tr>
    @endif


    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6"><br><br>Other Requirements<br></td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">The majority of our positions require good mobility, and good sensory perception such as
            hearing, sight, and smell. Applicants must be psychologically healthy and should be capable of working alone
            for 24 hour rotating shifts.Do you have any limitations in these areas?
        </td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$candidateJob->candidate->miscellaneous->limitations or 'No' }}</td>
    </tr>

    @if(isset($candidateJob->candidate->miscellaneous) && $candidateJob->candidate->miscellaneous->limitations == "Yes")
        <tr class="content-normal-bold-text">
            <td colspan="4">If you answered "Yes", please explain</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->miscellaneous->limitation_explain}}</td>
        </tr>
    @endif


    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6">Criminal Convictions<br></td>
    </tr>
    <tr class="content-normal-bold-text">
        <td colspan="4">Have you ever been convicted of a criminal offence for which you've not received a pardon?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->miscellaneous->criminal_convicted or 'No' }}</td>
    </tr>

    @if(isset($candidateJob->candidate->miscellaneous) && $candidateJob->candidate->miscellaneous->criminal_convicted == "Yes")
        <tr class="content-normal-bold-text">
            <td colspan="6"><br><br>If your answer was "Yes" please complete the section below</td>
        </tr>

        <tr class="content-normal-bold-text">
            <td colspan="4">Offence</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->miscellaneous->offence}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Date</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{date('d-m-Y', strtotime($candidateJob->candidate->miscellaneous->offence_date))}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="4">Location</td>
            <td colspan="2"
                style=" border-bottom: solid 1px #003a63;font-size: 14px;">{{$candidateJob->candidate->miscellaneous->offence_location}}</td>
        </tr>

    @endif

    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6">Career Interests</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">How would you describe your longer term career interests in security?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->miscellaneous->career_interest or '--'}}</td>
    </tr>

    <tr class="content-normal-bold-text">
        <td colspan="4">Would you consider other roles with Commissionaires beyond what you've applied for?</td>
        <td colspan="2"
            style=" border-bottom: solid 1px #003a63;font-size: 14px;">  {{$candidateJob->candidate->miscellaneous->other_roles  or '--'}}</td>
    </tr>

    <?php $already_shown = null;?>
    @foreach($candidateJob->candidate->screening_questions as $screening_question)
        @if($screening_question->question->category==null || $screening_question->question->category!=$already_shown)
            <tr class="light-heading orange label-b hr-line-label">
                <td colspan="6">{{$screening_question->question->category }}</td>
            </tr>
            <?php $already_shown = $screening_question->question->category;?> @endif
        <tr class="content-normal-bold-text">
            <td colspan="6">{{$screening_question->question->screening_question}}</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td style=" border-bottom: solid 1px #003a63;font-size: 14px;"
                colspan="6">   {{$screening_question->answer}}</td>
        </tr>
@endforeach

 @if ($candidateJob->candidate->personality_scores->count()>0)
             <tr class="light-heading orange label-b hr-line-label">
                <td colspan="6">Personality</td>
            </tr>

              <tr class="content-normal-bold-text">
            <td colspan="6">  {{$candidateJob->candidate->personality_scores[0]->score_type->type}}</td>
        </tr>
          <tr class="content-normal-bold-text">
            <td colspan="6" >   {!! nl2br($candidateJob->candidate->personality_scores[0]->score_type->description) !!}</td>
        </tr>


         
        @endif


    <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6">Competency Assessment</td>
    </tr>

        {{-- <div class="clearfix"></div>
        <div class="page-break form-group">
            <center>
                <label class="col-sm-12 orange label-b hr-line-label"><b>Competency Assessment</b></label>
            </center> --}}
            @foreach($candidateJob->candidate->competency_matrix as $competency_matrix)
             <tr class="content-normal-bold-text">
            <td colspan="4" class="content-caps-bold-text"> Competency</td>
            <td colspan="4"> {{$competency_matrix->competency_matrix->competency}}</td>
            
        </tr>
         <tr class="content-normal-bold-text">
         <td colspan="4" class="content-caps-bold-text"> Rating</td>
             <td colspan="4"> {{$competency_matrix->competency_matrix_rating->rating}}</td>
           </tr>  
            <tr class="content-caps-bold-text">
            <td colspan="2"> Definition</td>
        </tr>    
             <tr class="content-normal-bold-text">
            <td colspan="6"> {{$competency_matrix->competency_matrix->definition}}</td>
        </tr>
         <tr class="content-caps-bold-text">
            <td colspan="2"> Behaviors</td>
        </tr>   
          <tr class="content-normal-bold-text">
            <td colspan="6" style=" border-bottom: solid 1px #003a63;font-size: 14px;"> {{$competency_matrix->competency_matrix->behavior}}</td>
        </tr>
           
            @endforeach
    {{--     </div> --}}

        @if(Auth::user()!=null)
            @if($candidateJob->average_score > 0)
            <tr class="content-caps-bold-text">
            <td colspan="6"> Average Score</td>
        </tr>
        <tr class="content-normal-bold-text">
            <td colspan="6"> {{$candidateJob->average_score}}</td>
        </tr>
             
            @endif
           



 <tr class="light-heading orange label-b hr-line-label">
        <td colspan="6">Declaration</td>
    </tr>
     <tr class="content-normal-bold-text">
            <td colspan="6"> I declare that the information on this
                            application
                            is true, and I understand that any false statement will be cause for the immediate rejection
                            of
                            this application or the termination of my employment, if selected.</td>
        </tr>
          <tr class="content-normal-bold-text">
            <td colspan="6"> {{$candidateJob->candidate->name}}</td>
        </tr>
          <tr class="content-normal-bold-text">
            <td colspan="6">   {{date('F j, Y', strtotime($candidateJob->candidate->competencyTracking->completed_date))}}</td>
        </tr>
        @endif 
</table>
</body>
</html>
