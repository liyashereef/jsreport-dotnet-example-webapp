@extends('layouts.cgl360_osgc_scheduling_layout')

@section('css')

<style>
   html, body {
  height: 100%;
  margin: 0;
  font-family: 'Montserrat' !important;
    
}
.content-area
{
  padding-top: 30px;
}
.content-area h2 {
    font-size: 22px;
    color: #ef7b10;
    font-weight: 600;
    line-height: 57px;

}
.content-area ul {
    padding-left: 18px;
    font-size: 17px;
    color: #8b7878;
}
.content-area ul li {
    padding-bottom: 10px;
    
}
.elementor-11875 .elementor-element.elementor-element-83eef2f .elementor-button {
    font-family: "Montserrat", Sans-serif;
    font-weight: 500;
    text-shadow: 0px 0px 0px rgb(0 0 0 / 30%);
}
.elementor-button-link {
    font-size: 15px;
    padding: 12px 73px;
    border-radius: 0px;
    background-color: #ed1c24;
    width: 100%;
    color: #fff !important;
    font-weight: 500;
}
.container
{
  max-width: 90% !important;
}
.description {
    padding-bottom: 22px;
}
</style>
@stop
@section('content')

   
<section class="container">
<div class="row">
   
      <div class="container-fluid mb-0 ">
       
              <div class="row">
                
                  <div class="col-md-4" align="left">
                  <img  width="380" height="550" src="{{ asset('images/guard_training.png') }}" alt="" sizes="(max-width: 450px) 100vw, 450px">
                  
                  </div>
                  <div class="col-md-8 content-area">
                  <div class="content-area-inside">
                        <h2>Start Your Security Career With Commissionaires</h2>
                        <div class="description ">
                          <ul>
                            <li>Training package includes over 40 hours of course content to prepare you for the Ontario Security Guard Ministry exam.*</li>
                            <li>When you pass the exam, you will be offered employment on our spares list to build your security experience.</li>
                            <li>You will receive a corporate issued smart-phone to choose your hours and a uniform kit at no cost to you.</li>
                            <li>Starting wages $16.25 per hour with full-time hours available.</li>
                            <li>After a year of experience, you will be eligible for full-time opportunities, many with starting wages at $20.00/hour.</li>
                            <li>In line with our not-for-profit mandate, our course fee is provided at cost recovery.</li>
                            <li>You will be required to take a separate course for First Aid/CPR offered at our in-house training facilities in Oakville, Barrie or London to register for employment with our organization.*</li>
                          </ul>
                        </div>
                        <div class="elementor-button-wrapper">
                        <a href="{{url('osgc/home')}}"  class="elementor-button-link elementor-button elementor-size-lg" role="button">
                        <span class="elementor-button-content-wrapper">
                        <span class="elementor-button-text">Click Here To Access The Ontario Security Guard Course</span>
                        </span>
                        </a>
                        </div>
                  </div>
                  </div>












        


              </div>
        </div>
    </div>
</section>         
   



@stop



