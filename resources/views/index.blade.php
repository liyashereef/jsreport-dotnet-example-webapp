@extends('layouts.app') @section('content')
<style>
    .center {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 90%;
        vertical-align: middle;
        height: 100%;
    }

    .landing-text2 {
        font-size: 100px;
    line-height: normal;
    /* font-family: 'MicrosoftJhengHeiUI', sans-serif; */
    !important: ;
    margin: auto;
    width: 100%;
    margin-top: 15%;
    text-align: center;
    height: 100%;
    }

    .landing-text2 span.logo-first {
        color: #f26b38;
    }

    .landing-text2 span.logo-second {
        color: #323f4f;
        margin-left: -2%;
    }

    .landing-text2 .tag-line {
        font-size: 30px;
        /* margin-top: 4px; */
        float: left;
        margin: auto;
        text-align: center;
        color: #323f4f;
        width: 100%;
    }

    .landing-text2 sup {
        font-size: 10pt;
        top: -50pt;
    }
</style>
<div class="row">
    <div class="col-sm-12">

        <!--<div class="landing-text2">
            <span class="logo-first">CGL</span><span class="logo-second"> 360</span><sup>TM</sup>
            <div class="tag-line">Integrated Service Delivery Platform</div>
        </div>-->
        <img class="landing-img" src="{{ asset('images/landing-page-cgl360.jpg') }}">
        <!--<div class="landing-text" style="right:9%">
            CGL 360<sup style="
            font-size: 9pt;
            top: -20pt;
        ">TM</sup>
            <br><span class="landing-span" style="text-transform:none;">Integrated Service Delivery Platform</span>
        </div>-->
    </div>
</div>
@endsection