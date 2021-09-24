@extends('layouts.landing')
<style>
    .tilebox {
        width: 100%;
        padding-right: 3px;
    }

    .loader3:before {
        content: '.';
        animation: dots 1s steps(5, end) infinite;
    }

    @keyframes dots {

        0%,
        20% {
            color: rgba(0, 0, 0, 0);
            text-shadow:
                .25em 0 0 rgba(0, 0, 0, 0),
                .5em 0 0 rgba(0, 0, 0, 0);
        }

        40% {
            color: white;
            text-shadow:
                .25em 0 0 rgba(0, 0, 0, 0),
                .5em 0 0 rgba(0, 0, 0, 0);
        }

        60% {
            text-shadow:
                .25em 0 0 white,
                .5em 0 0 rgba(0, 0, 0, 0);
        }

        80%,
        100% {
            text-shadow:
                .25em 0 0 white,
                .5em 0 0 white;
        }
    }

    .loader1 {
        margin: auto;
        left: 0;
        right: 0;
        top: 0;
        position: absolute;
        font-size: 25px;
        width: 1em;
        height: 1em;
        border-radius: 50%;
        text-indent: -9999em;
        -webkit-animation: load5 1.1s infinite ease;
        animation: load5 1.1s infinite ease;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
    }

    @-webkit-keyframes load5 {

        0%,
        100% {
            box-shadow: 0em -2.6em 0em 0em #0f0f0f, 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.5), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.7);
        }

        12.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.7), 1.8em -1.8em 0 0em #0f0f0f, 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.5);
        }

        25% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.5), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.7), 2.5em 0em 0 0em #0f0f0f, 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        37.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.5), 2.5em 0em 0 0em rgba(15, 15, 15, 0.7), 1.75em 1.75em 0 0em #0f0f0f, 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        50% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.5), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.7), 0em 2.5em 0 0em #0f0f0f, -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        62.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.5), 0em 2.5em 0 0em rgba(15, 15, 15, 0.7), -1.8em 1.8em 0 0em #0f0f0f, -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        75% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.5), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.7), -2.6em 0em 0 0em #0f0f0f, -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        87.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.5), -2.6em 0em 0 0em rgba(15, 15, 15, 0.7), -1.8em -1.8em 0 0em #0f0f0f;
        }
    }

    @keyframes load5 {

        0%,
        100% {
            box-shadow: 0em -2.6em 0em 0em #0f0f0f, 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.5), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.7);
        }

        12.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.7), 1.8em -1.8em 0 0em #0f0f0f, 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.5);
        }

        25% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.5), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.7), 2.5em 0em 0 0em #0f0f0f, 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        37.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.5), 2.5em 0em 0 0em rgba(15, 15, 15, 0.7), 1.75em 1.75em 0 0em #0f0f0f, 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        50% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.5), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.7), 0em 2.5em 0 0em #0f0f0f, -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.2), -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        62.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.5), 0em 2.5em 0 0em rgba(15, 15, 15, 0.7), -1.8em 1.8em 0 0em #0f0f0f, -2.6em 0em 0 0em rgba(15, 15, 15, 0.2), -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        75% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.5), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.7), -2.6em 0em 0 0em #0f0f0f, -1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2);
        }

        87.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(15, 15, 15, 0.2), 1.8em -1.8em 0 0em rgba(15, 15, 15, 0.2), 2.5em 0em 0 0em rgba(15, 15, 15, 0.2), 1.75em 1.75em 0 0em rgba(15, 15, 15, 0.2), 0em 2.5em 0 0em rgba(15, 15, 15, 0.2), -1.8em 1.8em 0 0em rgba(15, 15, 15, 0.5), -2.6em 0em 0 0em rgba(15, 15, 15, 0.7), -1.8em -1.8em 0 0em #0f0f0f;
        }
    }


    .bar-color-red {
        background: red !important;
    }

    .bar-color-yellow {
        /* background: #fbe6a8; */
        background: #F5AE00 !important;
        color: black !important;
    }

    .bar-color-darkgreen {
        background: darkgreen !important;
        color: white !important;
    }

    .bar-color-green {
        background: green !important;
    }

    .bar-color-magenta {
        background: #E377E3 !important;
    }

    .bar-color-blue {
        background: #6464D1 !important;
    }

    .bar-color-pink {
        background: #EBB9C2 !important;
    }

    .bar-color-brown {
        background: #D99D71 !important;
    }

    .bar-color-aqua {
        background: #9BDFE7 !important;
    }

    .bar-color-grey {
        background: grey !important;
    }

    .bar-color-lavender {
        background: lavender !important;
    }

    .bar-color-black {
        background: black !important;
    }

    .bar-color-empty {
        background: #e9ecef !important;
    }

    .summary-dashboard-tile {
        color: white;
        background-color: red;
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
    }


    #content {
        font-family: 'Montserrat' !important;
    }

    .summary-box {
        background-color: #333f50;
        text-align: center;
        color: white;
        padding-top: 5%;
    }

    .client-survey-loader,
    .employee-survey-loader {
        bottom: 10%;
    }

    .operations-dashboard-loader {
        top: 30%;
    }

    .safety-dashboard-loader {
        top: 87%;
    }

    .embed-responsive-item {
        font-weight: bold;
        font-size: 30px;
    }

    #content {
        height: 100vh;
    }

    .tail-content {
        padding: 9% 0% 9% 0%;
    }

    .datepicker-loading {
        pointer-events: none !important;
    }
</style>
@section('content')
<div class="row" style="height:5vh; min-height:40px;">
    <div class="col-md-8">
        <div class="row" style="height: 100%;">
            <div class="col-md-12">
                <h4>Summary Dashboard</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row" style="height: 100%;">
            <div class="col-md-5 start_date_input">
                <input type="text" class="form-control datepicker1" name="start_date" id="start_date" placeholder="Start Date" />
            </div>
            <div class="col-md-5 end_date_input">
                <input type="text" class="form-control datepicker2" name="end_date" id="end_date" placeholder="End Date" />
            </div>
            <div class="col-md-2" style="padding: 1% 0% 0% 2.5%;">
                <a style="background-color: #f36a27;color: white;" class="btn detailed_view" href="#" title="Detail View"><i class="fa fa-list"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div style="height: 100%;width: 100%;display: flex;padding: 0% 1% 0% 1%;">
        <div class="tilebox js-title-box" data-url="{{route('scheduling.report-non-compliance')}}">
            <div class="summary-dashboard-tile schedule-infractions-tile" id="schedule-infractions-tile">
                <div class="tail-content">
                    <div class="embed-responsive-item s-d-count-1 schedule-infractions"><span class="loader3"></span></div>
                    Schedule<br />Infractions
                </div>
            </div>
        </div>
        <div class="tilebox js-title-box" data-url="{{route('client.concern')}}">
            <div class="summary-dashboard-tile client-concern-tile" id="client-concern-tile">
                <div class="tail-content">
                    <div class="embed-responsive-item s-d-count-1 client-concern-count"><span class="loader3"></span></div>
                    Client<br />Concern
                </div>
            </div>
        </div>
        <div class="tilebox js-title-box" data-url="{{route('incident.dashboard')}}">
            <div class="summary-dashboard-tile incidents-tile" id="incidents-tile">
                <div class="tail-content">
                    <div class="embed-responsive-item s-d-count-1 incidents-count"><span class="loader3"></span></div>
                    Number of<br />Incidents
                </div>
            </div>
        </div>

        <div class="tilebox js-title-box" data-url="{{route('recruitment-job')}}">
            <div class="summary-dashboard-tile recruiting-tickets-tile" id="recruiting-tickets-tile">
                <div class="tail-content">
                    <div class="embed-responsive-item s-d-count-1 recruiting-tickets"><span class="loader3"></span></div>
                    Recruiting<br />Tickets
                </div>
            </div>
        </div>
        <div class="tilebox js-title-box" data-url="{{route('guard-perfomance')}}">
            <div class="summary-dashboard-tile guard-performance-tile" id="guard-performance-tile">
                <div class="tail-content">
                    <div class="embed-responsive-item s-d-count-1 guard-performance-count"><span class="loader3"></span></div>
                    Guard<br />Performance
                </div>
            </div>
        </div>
        <div class="tilebox js-title-box" data-url="{{route('customers.mapping')}}">
            <div class="summary-dashboard-tile site-metric-tile" id="site-metric-tile">
                <div class="tail-content">
                    <div class="embed-responsive-item s-d-count-1 site-metric-count"><span class="loader3"></span></div>
                    Site<br />Metric
                </div>
            </div>
        </div>

        <div class="tilebox">
            <div class="summary-dashboard-tile guard-tour-compliance-tile" id="guard-tour-compliance-tile">
                <div class="tail-content">
                    <div class="embed-responsive-item s-d-count-1 guard-tour-compliance"><span class="loader3"></span></div>
                    Guard Tour<br />Compliance
                </div>
            </div>
        </div>
        <div class="tilebox js-title-box" data-url="{{route('employee.exitterminationsummary')}}">
            <div class="summary-dashboard-tile site-tour-over-tile" id="site-tour-over-tile">
                <div class="tail-content">
                    <div class="embed-responsive-item s-d-count-1 site-tour-over"><span class="loader3"></span></div>
                    Site<br />Turnover
                </div>
            </div>
        </div>
        <div class="tilebox js-title-box" data-url="{{route('training-compliance-inner')}}">
            <div class="summary-dashboard-tile training-compliance-tile" id="training-compliance-tile">
                <div class="tail-content">
                    <div class="embed-responsive-item s-d-count-1 training-compliance-count"><span class="loader3"></span></div>
                    Training<br />Compliance
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="row" style="height: calc(13vh);padding:0% 1% 0.1% 0.9%;">
    <div class="col-md-4">
        <div class="row" style="height: 100%;">
            <div class="col-md-4">
                <div class="summary-dashboard-tile schedule-infractions-tile" id="schedule-infractions-tile">
                    <div class="embed-responsive-item s-d-count-1 schedule-infractions"><span class="loader3"></span></div>
                    Schedule<br />Infractions
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-dashboard-tile client-concern-tile" id="client-concern-tile">
                        <div class="embed-responsive-item s-d-count-1 client-concern-count"><span class="loader3"></span></div>
                        Client<br />Concern
                    </div>
            </div>
            <div class="col-md-4">
                <div class="summary-dashboard-tile incidents-tile" id="incidents-tile">
                    <div class="embed-responsive-item s-d-count-1 incidents-count"><span class="loader3"></span></div>
                    Number of<br />Incidents
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row" style="height: 100%;">
            <div class="col-md-4">
            <div class="summary-dashboard-tile recruiting-tickets-tile" id="recruiting-tickets-tile">
                    <div class="embed-responsive-item s-d-count-1 recruiting-tickets"><span class="loader3"></span></div>
                    Recruiting<br />Tickets
                </div>
            </div>
            <div class="col-md-4">
            <div class="summary-dashboard-tile guard-performance-tile" id="guard-performance-tile">
                    <div class="embed-responsive-item s-d-count-1 guard-performance-count"><span class="loader3"></span></div>
                    Guard<br />Performance
                </div>
            </div>
            <div class="col-md-4">
            <div class="summary-dashboard-tile site-metric-tile" id="site-metric-tile">
                    <div class="embed-responsive-item s-d-count-1 site-metric-count"><span class="loader3"></span></div>
                    Site<br />Metric
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row"  style="height: 100%;">
            <div class="col-md-4">
            <div class="summary-dashboard-tile guard-tour-compliance-tile" id="guard-tour-compliance-tile">
                    <div class="embed-responsive-item s-d-count-1 guard-tour-compliance"><span class="loader3"></span></div>
                    Guard Tour<br />Compliance
                </div>
            </div>
            <div class="col-md-4">
            <div class="summary-dashboard-tile site-tour-over-tile" id="site-tour-over-tile">
                    <div class="embed-responsive-item s-d-count-1 site-tour-over"><span class="loader3"></span></div>
                    Site<br />Turnover
                </div>
            </div>
            <div class="col-md-4">
            <div class="summary-dashboard-tile training-compliance-tile" id="training-compliance-tile">
                    <div class="embed-responsive-item s-d-count-1 training-compliance-count"><span class="loader3"></span></div>
                    Training<br />Compliance
                </div>
            </div>
        </div>
    </div>
</div> -->
<div class="row" style="padding: 0% 1% 0% 1%;">
    <hr color="red" style="height:100%;width:100%;" />
</div>
<div class="row" style="height: 69vh;padding: 0% 1% 0% 1%;">
    <div class="col-md-6" style="height:100%;">
        <div class="row" style="height: 76%;">
            <div class="col-md-6" style="height: 100%;">
                <div class="text-center" style="padding:2% 0% 0% 0%;">
                    <h4>Client Survey</h4>
                </div>
                <div class="loader1 client-survey-loader"></div>
                <div style="height: 87%;"><canvas id="client-survey-canvas" style="display:none"></canvas></div>
            </div>
            <div class="col-md-6" style="height: 100%;">
                <div class="text-center" style="padding:2% 0% 0% 0%;">
                    <h4>Employee Survey</h4>
                </div>
                <div class="loader1 employee-survey-loader"></div>
                <div style="height: 87%;"><canvas id="employee-survey-canvas" height="225" style="display:none"></canvas></div>
            </div>
        </div>
        <div class="row" style="height: 23.65%;padding:0% 0% 0% 0%;">

            <div class="col-md-6" style="padding: 0.1% 0.2% 0% 0%;">
                <div class="summary-box" style="height: 100%;width: 100%;padding:10% 0%;">
                    <div class="embed-responsive-item total-work-hours">
                        <h3><b><span class="loader3"></span></b></h3>
                    </div>
                    <span><b>Total Hours Worked</b></span>
                </div>
            </div>
            <div class="col-md-6" style="padding: 0.1% 0.3% 0% 0.2%;">
                <div class="summary-box" style="height: 100%;width: 100%;padding:10% 0%;">
                    <div class="embed-responsive-item earned-billing">
                        <h3><b><span class="loader3"></span></b></h3>
                    </div>
                    <span><b>Earned Billings</b></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6" style="height: 100%;width: 100%;">
        <div class="row" style="height: 70%;">
            <div class="loader1 operations-dashboard-loader"></div>
            <div id="operations-dashboard-div" style="height:100%;width:100%;overflow-y: auto;"></div>
        </div>
        <div class="row" style="height: 1%;">&nbsp;</div>
        <div class="row" style="height: 29%;">
            <div class="loader1 safety-dashboard-loader"></div>
            <div id="safety-dashboard-div" style="height:100%;width:100%;overflow-y: auto;"></div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script type="text/javascript">
    //logging
    const logger = {
        log(str) {
            console.log(str);
        },
        error(str) {
            console.error(str);
        },
        warn(str) {
            console.warn(str);
        }
    }

    //Summary dashboard routes
    const routes = {
        clientSurvey: "{{route('summary-dashboard.client-survey')}}",
        employeeSurvey: "{{route('summary-dashboard.employee-survey')}}",
        operationsDashboard: "{{route('summary-dashboard.operations-dashboard-matrix')}}",
        safetyDashboard: "{{route('summary-dashboard.safety-dashboard-matrix')}}",
        kpiRelatedTileBlocks: "{{route('summary-dashboard.kpi-tile-blocks')}}",
        summaryTileBlocks: "{{route('summary-dashboard.tile-blocks')}}",
        workHoursVsEarnedBilling: "{{route('summary-dashboard.work-hours-earned-billing-details')}}",
    }

    function reverseObject(object) {
        var newObject = {};
        var keys = [];

        for (var key in object) {
            keys.push(key);
        }

        for (var i = keys.length - 1; i >= 0; i--) {
            var value = object[keys[i]];
            newObject[keys[i]] = value;
        }

        return newObject;
    }

    //summary dashboard widgets loading
    const summaryDashboard = {
        clientSurvey: null,
        employeeSurvey: null,
        //form input to js variable
        formInputs: {
            customerIdArray: [],
            startDate: null,
            endDate: null
        },

        //get latest form values
        fetchLatestFormInputValues() {
            let root = this;
            root.formInputs.customerIdArray = $('#dashboard-filter-customer').val();
            root.formInputs.startDate = $('#start_date').val();
            root.formInputs.endDate = $('#end_date').val();
            return root.formInputs;
        },

        //load client survey graph
        loadClientSurvey() {
            let root = this;
            $.ajax({
                type: "GET",
                url: routes.clientSurvey,
                data: {
                    'customer_ids': root.formInputs.customerIdArray,
                    'start_date': root.formInputs.startDate,
                    'end_date': root.formInputs.endDate,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    let responseCustomer = response.customer_ids;
                    let responseStartdate = response.start_date;
                    let responseEnddate = response.end_date;
                    let customerObject = {
                        'customer_ids': responseCustomer,
                        'start_date': responseStartdate,
                        'end_date': responseEnddate
                    };
                    let identical = identicalArrays(customerObject, responseStartdate, responseEnddate);
                    if (identical == true) {
                        $('.client-survey-loader').css('display', 'none');
                        $('#client-survey-canvas').css('display', 'block');
                        root.generateClientGraph(response, 'client-survey-canvas');
                    }
                }
            });
        },

        //load operations dashboard metric
        loadSafetyDashboardMatrix() {
            let root = this;
            $.ajax({
                type: "GET",
                url: routes.safetyDashboard,
                data: {
                    'customer_ids': root.formInputs.customerIdArray,
                    'start_date': root.formInputs.startDate,
                    'end_date': root.formInputs.endDate,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    let responseCustomer = response.customer_ids;
                    let responseStartdate = response.start_date;
                    let responseEnddate = response.end_date;
                    let customerObject = {
                        'customer_ids': responseCustomer,
                        'start_date': responseStartdate,
                        'end_date': responseEnddate
                    };
                    let identical = identicalArrays(customerObject, responseStartdate, responseEnddate);
                    if (identical == true) {
                        root.generateOperationsDashboardMatrix(response, 'safety-dashboard-div', 'Safety Dashboard', root.formInputs.customerIdArray);
                        $('.safety-dashboard-loader').css('display', 'none');
                        $('#safety-dashboard-div').css('display', 'block');
                    }
                }
            });
        },

        //load operations dashboard metric
        loadOperationsDashboardMatrix() {
            let root = this;
            $.ajax({
                type: "GET",
                url: routes.operationsDashboard,
                data: {
                    'customer_ids': root.formInputs.customerIdArray,
                    'start_date': root.formInputs.startDate,
                    'end_date': root.formInputs.endDate,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    let responseCustomer = response.customer_ids;
                    let responseStartdate = response.start_date;
                    let responseEnddate = response.end_date;
                    let customerObject = {
                        'customer_ids': responseCustomer,
                        'start_date': responseStartdate,
                        'end_date': responseEnddate
                    };
                    let identical = identicalArrays(customerObject, responseStartdate, responseEnddate);
                    if (identical == true) {
                        root.generateOperationsDashboardMatrix(response, 'operations-dashboard-div', 'Operations Dashboard', root.formInputs.customerIdArray);
                        $('.operations-dashboard-loader').css('display', 'none');
                        $('#operations-dashboard-div').css('display', 'block');
                    }

                }
            });
        },
        //load top kpi related tile blocks
        loadKpiRelatedTileBlocks() {
            let root = this;
            $.ajax({
                type: "GET",
                url: routes.kpiRelatedTileBlocks,
                data: {
                    'customer_ids': root.formInputs.customerIdArray,
                    'start_date': root.formInputs.startDate,
                    'end_date': root.formInputs.endDate,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        let responseCustomer = response.customer_ids;
                        let responseStartdate = response.start_date;
                        let responseEnddate = response.end_date;
                        let customerObject = {
                            'customer_ids': responseCustomer,
                            'start_date': responseStartdate,
                            'end_date': responseEnddate
                        };
                        let identical = identicalArrays(customerObject, responseStartdate, responseEnddate);
                        if (identical == true) {
                            $('.site-metric-tile').addClass('bar-color-' + response.data.site_metric.color);
                            $('.site-metric-count').html((response.data.site_metric.average));
                            $('.training-compliance-count').html(Math.round(response.data.training_compliance.average) + '%');
                            $('.client-concern-count').html(response.data.client_concern);
                            $('.incidents-count').html(response.data.incidents);
                            $('.guard-performance-count').html((response.data.performance_mgmt.average));
                            $('.guard-performance-tile').addClass('bar-color-' + response.data.performance_mgmt.color);
                            $('.training-compliance-tile').addClass('bar-color-' + response.data.training_compliance.color);
                        }
                    }
                }
            });
        },

        //load top summary tile blocks
        loadSummaryTileBlocks() {
            let root = this;
            $.ajax({
                type: "GET",
                url: routes.summaryTileBlocks,
                data: {
                    'customer_ids': root.formInputs.customerIdArray,
                    'start_date': root.formInputs.startDate,
                    'end_date': root.formInputs.endDate,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        let responseCustomer = response.customer_ids;
                        let responseStartdate = response.start_date;
                        let responseEnddate = response.end_date;
                        let customerObject = {
                            'customer_ids': responseCustomer,
                            'start_date': responseStartdate,
                            'end_date': responseEnddate
                        };
                        let identical = identicalArrays(customerObject, responseStartdate, responseEnddate);
                        if (identical == true) {
                            $('.schedule-infractions').html(response.data.schedule_infraction);
                            $('.guard-tour-compliance').html(Math.round(response.data.guard_tour_compliance.average) + '%');
                            $('.guard-tour-compliance-tile').addClass('bar-color-' + response.data.guard_tour_compliance.color);
                            $('.recruiting-tickets').html(response.data.job_tickets);
                            $('.site-tour-over').html(Math.round(response.data.site_turn_over.average) + '%');
                            $('.site-tour-over-tile').addClass('bar-color-' + response.data.site_turn_over.color);
                        }
                    }
                }
            });
        },

        //load Total work hours, earned billings
        loadTotalHoursVsEarnedBilling() {
            let root = this;
            $.ajax({
                type: "GET",
                url: routes.workHoursVsEarnedBilling,
                data: {
                    'customer_ids': root.formInputs.customerIdArray,
                    'start_date': root.formInputs.startDate,
                    'end_date': root.formInputs.endDate,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    let responseCustomer = response.customer_ids;
                    let responseStartdate = response.start_date;
                    let responseEnddate = response.end_date;
                    let customerObject = {
                        'customer_ids': responseCustomer,
                        'start_date': responseStartdate,
                        'end_date': responseEnddate
                    };
                    let identical = identicalArrays(customerObject, responseStartdate, responseEnddate);
                    if (identical == true) {
                        if (response.success) {
                            $('.total-work-hours').html('<h1><b>' + numberWithCommas(response.data.hours) + '</b></h1>');
                            $('.earned-billing').html('<h1><b>$' + numberWithCommas(response.data.earned_billing_amount) + '</b></h1>');
                        }
                    }
                    
                    //enable start,end date pickers
                    $('#dashboard-filter-customer').attr('disabled', false);
                    $(".start_date_input").removeClass('datepicker-loading');
                    $(".end_date_input").removeClass('datepicker-loading');
                }
            });
        },

        //load employee survey graph
        loadEmployeeSurvey() {
            let root = this;
            $.ajax({
                type: "GET",
                url: routes.employeeSurvey,
                data: {
                    'customer_ids': root.formInputs.customerIdArray,
                    'start_date': root.formInputs.startDate,
                    'end_date': root.formInputs.endDate,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var jqdata = jQuery.parseJSON(response);

                    let responseCustomer = jqdata.customer_ids;
                    let responseStartdate = jqdata.start_date;
                    let responseEnddate = jqdata.end_date;
                    let customerObject = {
                        'customer_ids': responseCustomer,
                        'start_date': responseStartdate,
                        'end_date': responseEnddate
                    };
                    let identical = identicalArrays(customerObject, responseStartdate, responseEnddate);

                    if (identical == true) {
                        $('.employee-survey-loader').css('display', 'none');
                        $('#employee-survey-canvas').css('display', 'block');
                        root.generateEmployeeGraph(response, 'employee-survey-canvas');
                    }
                }
            });
        },
        //trigger all widgets
        loadWidgets(startDateTrigger = true, endDateTrigger = true) {
            let root = this;

            root.fetchLatestFormInputValues();
            $(".start_date_input").addClass('datepicker-loading');
            $(".end_date_input").addClass('datepicker-loading');
            $('#dashboard-filter-customer').attr('disabled', true);

            if (endDateTrigger) {
                root.loadOperationsDashboardMatrix();
                root.loadSafetyDashboardMatrix();
            }

            root.loadClientSurvey();
            root.loadEmployeeSurvey();
            root.loadKpiRelatedTileBlocks();
            root.loadSummaryTileBlocks();
            root.loadTotalHoursVsEarnedBilling();
        },
        //generate operations dashboard html
        generateOperationsDashboardMatrix(data, table_div, heading, customerIdArray) {
            var html = '';
            html += '<table class="table table-bordered metrics" style="height:100% !important;width:100% !important;margin-bottom:0px"><thead><tr><th colspan="6" style="color: white; background-color: #343F4E;font-size: 18px;"><b>' + heading + '</b></th></tr><tr>';
            if (heading == "Operations Dashboard") {
                html += '<th style="color: white; background-color: red;text-align:left;width:16.6666666667%;"></th>';
                $.each(data.payperiods, function(index, value) {
                    html += '<th style="color: white; background-color: red;text-align:center;font-size:16px;font-style: normal !important;width:16.6666666667%;">' + value + '</th>';
                });
            }

            html += '</tr></thead><tbody><tr>';

            let objectLen = Object.keys(data.categories).length;
            if (objectLen == 0) {
                html += '<td colspan="6" class="text-capitalize text-center" scope="row" style="width:16.6666666667%;vertical-align:middle;color: white; background-color: black;font-size:16px;">Not found</td></tr>';
            } else {
                let n = 0;
                $.each(data.categories, function(index1, value1) {
                    n++;
                    let label = '';
                    if (value1.length > 10) {
                        label = value1.substring(0, 10) + '..';
                    } else {
                        label = value1;
                    }
                    html += '<td class="text-capitalize" scope="row" style="width:16.6666666667%;vertical-align:middle;color: white; background-color: red;font-size:16px;"  title="' + value1 + '"><b>' + label + '</b></td>';
                    $.each(data.result, function(index2, value2) {
                        $.each(value2, function(index3, value3) {
                            if (index1 == index2) {
                                html += '<td align="center" title="' + value3.average + '" class="bar-color-' + value3.color + '"></td>';
                            }
                        });
                    });
                    html += '</tr>';
                    if (n < objectLen) {
                        html += '<tr>';
                    }
                });
            }
            html += '</tbody></table>';
            $('#' + table_div).html(html);
        },
        //generate client survey graph
        generateClientGraph(chartData, canvas_element) {
            let root = this;
            if (root.clientSurvey) {
                root.clientSurvey.destroy();
            }

            root.clientSurvey = new Chart($('#' + canvas_element), {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: false,
                        data: chartData.data,
                        backgroundColor: 'red',
                        borderWidth: 1,
                        barThickness: 35,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    spanGaps: false,
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        // text: 'Client Survey',
                        // fontSize:25
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            },
                            barPercentage: 0.1
                        }],
                        yAxes: [{
                            ticks: {
                                callback: function(label, index, labels) {
                                    return label;
                                }
                            },
                            scaleLabel: {
                                display: false,
                                labelString: 'Regions'
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        },

        //generate Employee survey graph
        generateEmployeeGraph(chartData, canvas_element) {
            let root = this;
            if (root.employeeSurvey) {
                root.employeeSurvey.destroy();
            }

            var surveyData = jQuery.parseJSON(chartData);
            let survData = [];
            $.each(surveyData.datasets[0], function(indexInArray, valueOfElement) {
                survData.push(valueOfElement)
            });
            root.employeeSurvey = new Chart($('#' + canvas_element), {
                type: 'bar',
                data: {
                    labels: surveyData.labels,
                    datasets: survData,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    spanGaps: false,
                    legend: {
                        display: false,
                        position: 'right' // place legend on the right side of chart
                    },
                    scales: {
                        xAxes: [{
                            stacked: true,
                            barPercentage: 0.1 // this should be set to make the bars stacked
                        }],
                        yAxes: [{
                            stacked: true // this also..
                        }]
                    }
                }
            });
        },
    }

    $(function() {
        var dateObj = new Date();
        $('.datepicker1').datepicker({
            format: "dd-mm-yyyy",
            maxDate: moment().subtract(1, 'days').format("DD-MM-YYYY"),
            value: moment().subtract(3, 'months').format("01-MM-YYYY")
        });

        $('.datepicker2').datepicker({
            format: "dd-mm-yyyy",
            value: moment().subtract(1, 'days').format("DD-MM-YYYY"),
            maxDate: moment().subtract(1, 'days').format("DD-MM-YYYY"),
        });

        summaryDashboard.loadWidgets();
    });
    var startValue = null,
        endValue = null;

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    $('.datepicker1').on('change', function(e) {
        if (startValue != null && startValue !== e.target.value) {
            $('.total-work-hours').html('<h3><b><span class="loader3"></span></b></h3>');
            $('.earned-billing').html('<h3><b><span class="loader3"></span></b></h3>');
            $('.s-d-count-1').html('<span class="loader3"></span>');
            $('.summary-dashboard-tile').css('background-color', 'red');
            $('.client-survey-loader').css('display', 'block');
            $('#client-survey-canvas').css('display', 'none');
            $('.employee-survey-loader').css('display', 'block');
            $('#employee-survey-canvas').css('display', 'none');
            $("#guard-tour-compliance-tile").removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
            $("#guard-performance-tile").removeClass().addClass('summary-dashboard-tile guard-performance-tile');
            $('#site-metric-tile').removeClass().addClass('summary-dashboard-tile site-metric-tile');
            $('#site-tour-over-tile').removeClass().addClass('summary-dashboard-tile site-tour-over-tile');
            $('#guard-tour-compliance-tile').removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
            $('#training-compliance-tile').removeClass().addClass('summary-dashboard-tile training-compliance-tile');
            summaryDashboard.loadWidgets(true, false);
        }
        startValue = e.target.value;
    });

    $('.datepicker2').on('change', function(e) {
        if (endValue != null && endValue !== e.target.value) {
            $('.total-work-hours').html('<h3><b><span class="loader3"></span></b></h3>');
            $('.earned-billing').html('<h3><b><span class="loader3"></span></b></h3>');
            $('.s-d-count-1').html('<span class="loader3"></span>');
            $('.summary-dashboard-tile').css('background-color', 'red');
            $('.client-survey-loader').css('display', 'block');
            $('#client-survey-canvas').css('display', 'none');
            $('.employee-survey-loader').css('display', 'block');
            $('#employee-survey-canvas').css('display', 'none');
            $('.operations-dashboard-loader').css('display', 'block');
            $('#operations-dashboard-div').css('display', 'none');
            $('.safety-dashboard-loader').css('display', 'block');
            $('#safety-dashboard-div').css('display', 'none');
            $("#guard-tour-compliance-tile").removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
            $("#guard-performance-tile").removeClass().addClass('summary-dashboard-tile guard-performance-tile');
            $('#site-metric-tile').removeClass().addClass('summary-dashboard-tile site-metric-tile');
            $('#site-tour-over-tile').removeClass().addClass('summary-dashboard-tile site-tour-over-tile');
            $('#guard-tour-compliance-tile').removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
            $('#training-compliance-tile').removeClass().addClass('summary-dashboard-tile training-compliance-tile');
            summaryDashboard.loadWidgets(false, true);
        }
        endValue = e.target.value;
    });

    $('#dashboard-filter-customer').on('input change', function(e) {
        $('.total-work-hours').html('<h3><b><span class="loader3"></span></b></h3>');
        $('.earned-billing').html('<h3><b><span class="loader3"></span></b></h3>');
        $('.s-d-count-1').html('<span class="loader3"></span>');
        $('.summary-dashboard-tile').css('background-color', 'red');
        $('.client-survey-loader').css('display', 'block');
        $('#client-survey-canvas').css('display', 'none');
        $('.employee-survey-loader').css('display', 'block');
        $('#employee-survey-canvas').css('display', 'none');
        $('.operations-dashboard-loader').css('display', 'block');
        $('#operations-dashboard-div').css('display', 'none');
        $('.safety-dashboard-loader').css('display', 'block');
        $('#safety-dashboard-div').css('display', 'none');
        $("#guard-tour-compliance-tile").removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
        $("#guard-performance-tile").removeClass().addClass('summary-dashboard-tile guard-performance-tile');
        $('#site-metric-tile').removeClass().addClass('summary-dashboard-tile site-metric-tile');
        $('#site-tour-over-tile').removeClass().addClass('summary-dashboard-tile site-tour-over-tile');
        $('#guard-tour-compliance-tile').removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
        $('#training-compliance-tile').removeClass().addClass('summary-dashboard-tile training-compliance-tile');
        summaryDashboard.loadWidgets(true, true);
    });
</script>

<!-- Tile overview page -->
<script>
    const sc = {
        onTilePressed(el) {
            let _urlStr = $(el).data('url');

            //Fetch filter values
            let cIds = $('#dashboard-filter-customer').val();
            let from = $('#start_date').val();
            from = moment(from, "DD-MM-YYYY").format("YYYY-MM-DD");
            let to = $('#end_date').val();
            to = moment(to, "DD-MM-YYYY").format("YYYY-MM-DD");

            //Customer is to csv
            let ids = globalUtils.arrayToCsv(cIds);

            //Set query params
            let _url = new URL(_urlStr);
            _url.searchParams.set('from', from);
            _url.searchParams.set('to', to);
            _url.searchParams.set('cIds', ids);

            _url = _url.toString();

            window.open(_url, '_blank').focus();
        },
        init() {
            let root = this;
            $('.js-title-box').click(function() {
                root.onTilePressed(this);
            });
        }
    }

    $(function() {
        sc.init();
    });
    $('.datepicker2').on('change', function(e) {
        if (endValue != null && endValue !== e.target.value) {
            $('.total-work-hours').html('<h3><b><span class="loader3"></span></b></h3>');
            $('.earned-billing').html('<h3><b><span class="loader3"></span></b></h3>');
            $('.s-d-count-1').html('<span class="loader3"></span>');
            $('.summary-dashboard-tile').css('background-color', 'red');
            $('.client-survey-loader').css('display', 'block');
            $('#client-survey-canvas').css('display', 'none');
            $('.employee-survey-loader').css('display', 'block');
            $('#employee-survey-canvas').css('display', 'none');
            $('.operations-dashboard-loader').css('display', 'block');
            $('#operations-dashboard-div').css('display', 'none');
            $('.safety-dashboard-loader').css('display', 'block');
            $('#safety-dashboard-div').css('display', 'none');
            $("#guard-tour-compliance-tile").removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
            $("#guard-performance-tile").removeClass().addClass('summary-dashboard-tile guard-performance-tile');
            $('#site-metric-tile').removeClass().addClass('summary-dashboard-tile site-metric-tile');
            $('#site-tour-over-tile').removeClass().addClass('summary-dashboard-tile site-tour-over-tile');
            $('#guard-tour-compliance-tile').removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
            $('#training-compliance-tile').removeClass().addClass('summary-dashboard-tile training-compliance-tile');
            summaryDashboard.loadWidgets(false, true);
        }
        endValue = e.target.value;
    });

    $('#dashboard-filter-customer').on('input change', function(e) {
        $('.total-work-hours').html('<h3><b><span class="loader3"></span></b></h3>');
        $('.earned-billing').html('<h3><b><span class="loader3"></span></b></h3>');
        $('.s-d-count-1').html('<span class="loader3"></span>');
        $('.summary-dashboard-tile').css('background-color', 'red');
        $('.client-survey-loader').css('display', 'block');
        $('#client-survey-canvas').css('display', 'none');
        $('.employee-survey-loader').css('display', 'block');
        $('#employee-survey-canvas').css('display', 'none');
        $('.operations-dashboard-loader').css('display', 'block');
        $('#operations-dashboard-div').css('display', 'none');
        $('.safety-dashboard-loader').css('display', 'block');
        $('#safety-dashboard-div').css('display', 'none');
        $("#guard-tour-compliance-tile").removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
        $("#guard-performance-tile").removeClass().addClass('summary-dashboard-tile guard-performance-tile');
        $('#site-metric-tile').removeClass().addClass('summary-dashboard-tile site-metric-tile');
        $('#site-tour-over-tile').removeClass().addClass('summary-dashboard-tile site-tour-over-tile');
        $('#guard-tour-compliance-tile').removeClass().addClass('summary-dashboard-tile guard-tour-compliance-tile');
        $('#training-compliance-tile').removeClass().addClass('summary-dashboard-tile training-compliance-tile');
        summaryDashboard.loadWidgets(true, true);
    });

    $(document).on("click", ".detailed_view", function(e) {
        e.preventDefault();

        let customerFilter = $("#dashboard-filter-customer").val();
        let url = '{{ route("dashboard") }}';
        if (customerFilter.length > 0) {
            url = '{{ route("dashboard", ":customer_id") }}';
            url = url.replace(':customer_id', customerFilter);
        }
        window.open(url);
    })

    function identicalArrays(array1, start_date, end_date) {
        let identical = 0;
        let respCustomer = array1.customer_ids;
        let respStartDate = array1.start_date;
        let respEndDate = array1.end_date;


        let array2 = $("#dashboard-filter-customer").val();

        let startdate = $("#start_date").val();
        let enddate = $("#end_date").val();

        if (respCustomer != null) {
            for (let i = 0; i < respCustomer.length; i++) {
                let indexValue = respCustomer[i];
                if (!array2.includes(indexValue)) {
                    identical++;
                }
            }
        }

        if (identical > 0) {
            return false;
        } else {
            if (respCustomer != null) {

                if (respCustomer.length == array2.length) {
                    if (respStartDate == startdate && respEndDate == enddate) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                if (array2 == null || array2.length < 1) {
                    if (respStartDate == startdate && respEndDate == enddate) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }

            }
        }

    }
</script>
<style>
    .js-title-box {
        cursor: pointer;
    }
</style>
@endsection