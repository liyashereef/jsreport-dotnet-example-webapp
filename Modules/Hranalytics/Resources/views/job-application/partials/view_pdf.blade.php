@extends('layouts.candidate-layout')
@section('content')
<div class="container">
    <div class="row">
        <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
            <li class="nav-item success" >
                <a class="nav-link disabled" data-toggle="tab" href="#profile"><span>1. Profile</span></a>
            </li>
            <li class="nav-item success">
                <a class="nav-link disabled " data-toggle="tab" href="#questions"><span>2. Questions</span></a>
            </li>
            <li class="nav-item success">
                <a class="nav-link disabled" data-toggle="tab" href="#personality_inventory"><span>3. Personality</span></a>
            </li>
            <li class="nav-item success">
                <a class="nav-link disabled" data-toggle="tab" href="#competency_matrix"><span>4. Competency</span></a>
            </li>
            <li class="nav-item success">
                <a class="nav-link disabled" data-toggle="tab" href="#attachment"><span>5. Attachments</span></a>
            </li>
            <li class="nav-item success">
                <a class="nav-link disabled" data-toggle="tab" href="#submit"><span>6. Submit</span></a>
            </li>
            <li class="nav-item active" style="width: 14.2857%;">
                <a class="nav-link" data-toggle="tab" href="#print"><span>7. Print Application</span></a>
            </li>
        </ul>
        <div class="tab-content pdf-content">
            @include('hranalytics::job-application.pdf')
        </div>
    </div>
</div>
@stop
<style>
.success{
   width: 14.2857% !important;
}
</style>
