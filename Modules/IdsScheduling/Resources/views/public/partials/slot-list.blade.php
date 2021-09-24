@extends('layouts.cgl360_ids_scheduling_layout')
@section('title', 'IDS - '. config('app.name'))
@section('css')

<style>
    .master-slot-container{
        margin: 1%;
        width: auto;
    }
    .day-slot-container{
        /* border: 1px solid black; */
        /* margin: 7px 0px; */
        background-color: #d5d1d15e; 
    }
    .day-slot-container .all-container{
        /* margin: 1% 1%; */
        
    }
    .slot-day-name{
        background-color: #f36905;
        padding: 10px 10px;
        color: #ffffff;
        /* width: fit-content; */
        /* border: 1px solid; */
    }
    .slot-day-name span{
        font-weight: bold;
    }
    .slot-day-name p{
        margin-top: 0;
        margin-bottom: 0px;
    }
    .slot-container{
        /* margin: 2px;  */
    }
    .slot-container button{
        margin: 12px 5px;
        border: 1px solid;
        border-radius: 10px;
        width: 95px;
        padding: 12px;
        background-color: #13486b !important;
        color: #fff !important;
        cursor: pointer;
    }
    .slot-container button:hover{
        border: 1px solid; */
        border-radius: 10px;

        background-color: #13486be0 !important;
        color: #fff !important;
    }
    .btn-light.disabled, .btn-light:disabled {
        border: 1px solid;
        background-color: #e0e0e0  !important;
        color: #04040461 !important; 
    }
    .btn-light:disabled:hover{
        background-color: #e0e0e0  !important; 
        color: #04040461 !important; 
    }
    .slot-details{
        border: 1px solid;
        border-radius: 10px;
        margin: 12px;
        padding: 12px;
        text-align:center;
        width: 95px;
        background-color: #ffffff;
        cursor: pointer;
    }
    .today {
        background: #E6E6FA !important;
    }

    .booked-slot {
        /* background-color: #000 !important; */
    }
    .blocked-or-booked {
        background-color: #e0e0e0 !important;
        color: #04040461 !important; 
    }
    .open-slot {
        
    }
    
/****START* Accordion Style  */
    .card-link{
        color: #f36905 !important;
        font-weight: bold;
    }

    .card-link[aria-expanded="true"] {
        background: #f7f7f7;
    }

/****END* Accordion Style  */


</style>
@stop
@section('content')
<!--Start- Scheduling table container - -->

        <div id="scheduling-table-container" class=" master-slot-container">
        <!--Start-- Day slot  container-->
            <!-- <div class=" day-slot-container container-fluid today">
                    <div class="all-container">
                        <div class="row slot-day-name"> 
                            <span class="col-sm-12"> Wednesday June 17, 2020 </span> 
                            <p class="col-sm-12"> Note: 30 minutes interval </p>
                        </div>
                        
                        <div class="slot-container">
                            <div class="col-sm-12 row" >
                                <div class="slot-details">9 AM</div>
                                <div class="slot-details blocked-or-booked">9.30 AM</div>
                                <div class="slot-details">10 AM</div>
                                <div class="slot-details blocked-or-booked">10.30 AM</div>
                                <div class="slot-details">11 AM</div>
                                <div class="slot-details">11.30 AM</div>

                                <div class="slot-details">12 PM</div>
                                <div class="slot-details">12.30 PM</div>
                                <div class="slot-details">1 PM</div>
                                <div class="slot-details">1.30 PM</div>
                                <div class="slot-details">2 PM</div>
                                <div class="slot-details">2.30 PM</div>
                                
                                <div class="slot-details">3 PM</div>
                                <div class="slot-details">3.30 PM</div>
                                <div class="slot-details">4 PM</div>
                                <div class="slot-details">4.30 PM</div>
                                <div class="slot-details">5 PM</div>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="day-slot-container container-fluid ">
                <div class="all-container">
                    <div class="row slot-day-name"> 
                        <span class="col-sm-12"> Thursday June 18, 2020  </span> 
                        <p class="col-sm-12"> Note: 30 minutes interval </p>
                    </div>
                    
                    <div class="row slot-container">
                        <div class="slot-details">9 AM</div>
                        <div class="slot-details blocked-or-booked">9.30 AM</div>
                        <div class="slot-details">10 AM</div>
                        <div class="slot-details blocked-or-booked">10.30 AM</div>
                        <div class="slot-details">11 AM</div>
                        <div class="slot-details">11.30 AM</div>

                        <div class="slot-details">12 PM</div>
                        <div class="slot-details">12.30 PM</div>
                        <div class="slot-details">1 PM</div>
                        <div class="slot-details">1.30 PM</div>
                        <div class="slot-details">2 PM</div>
                        <div class="slot-details">2.30 PM</div>
                        
                        <div class="slot-details">3 PM</div>
                        <div class="slot-details">3.30 PM</div>
                        <div class="slot-details">4 PM</div>
                        <div class="slot-details">4.30 PM</div>
                        <div class="slot-details">5 PM</div>
                    </div>
                </div>
            </div>

            <div class="day-slot-container container-fluid">
                <div class="all-container">
                    <div class="row slot-day-name"> 
                        <span class="col-sm-12"> Friday June 19, 2020  </span> 
                        <p class="col-sm-12"> Note: 15 minutes interval </p>
                    </div>
                    <div class="row slot-container">
                        <div class="slot-details ">9 AM</div>
                        <div class="slot-details ">9.15 AM</div>
                        <div class="slot-details ">9.30 AM</div>
                        <div class="slot-details ">9.45 AM</div>

                        <div class="slot-details blocked-or-booked">10 AM</div>
                        <div class="slot-details blocked-or-booked">10.15 AM</div>
                        <div class="slot-details">10.30 AM</div>
                        <div class="slot-details blocked-or-booked">10.45 AM</div>

                        <div class="slot-details">11 AM</div>
                        <div class="slot-details">11.15 AM</div>
                        <div class="slot-details">11.30 AM</div>
                        <div class="slot-details">11.45 AM</div>

                        <div class="slot-details blocked-or-booked">12 PM</div>
                        <div class="slot-details">12.15 PM</div>
                        <div class="slot-details">12.30 PM</div>
                        <div class="slot-details">12.45 PM</div>

                        <div class="slot-details">1 PM</div>
                        <div class="slot-details">1.15 PM</div>
                        <div class="slot-details">1.30 PM</div>
                        <div class="slot-details">1.45 PM</div>

                        <div class="slot-details blocked-or-booked">2 PM</div>
                        <div class="slot-details">2.15 PM</div>
                        <div class="slot-details">2.30 PM</div>
                        <div class="slot-details">2.45 PM</div>
                        
                        <div class="slot-details">3 PM</div>
                        <div class="slot-details">3.15 PM</div>
                        <div class="slot-details">3.30 PM</div>
                        <div class="slot-details">3.45 PM</div>

                        <div class="slot-details">4 PM</div>
                        <div class="slot-details">4.15 PM</div>
                        <div class="slot-details">4.30 PM</div>
                        <div class="slot-details">5 PM</div>
                    </div>
                </div>
            </div> -->
         
            <br><br><br><br><br>
       
            <!-- <div class="day-slot-container container-fluid today">
                <div class="all-container">
                    <div class="row slot-day-name"> 
                        <span class="col-sm-12"> Thursday June 17, 2020  </span> 
                        <p class="col-sm-12"> Note: 30 minutes interval </p>
                    </div>
                    
                    <div class="row slot-container">
                       
                        <button type="button" class="btn btn-light disabled" disabled>9 AM</button>
                        <button type="button" class="btn btn-light">9.30 AM</button>
                        <button type="button" class="btn btn-light">10 AM</button>
                        <button type="button" class="btn btn-light">10.30 AM</button>
                        <button type="button" class="btn btn-light">11 AM</button>
                        <button type="button" class="btn btn-light">11.30 AM</button>
                        <button type="button" class="btn btn-light">12 PM</button>
                        <button type="button" class="btn btn-light">12.30 AM</button>
                        <button type="button" class="btn btn-light">1 AM</button>
                        <button type="button" class="btn btn-light">1.30 AM</button>
                        <button type="button" class="btn btn-light">2 AM</button>
                        <button type="button" class="btn btn-light">2.30 AM</button>
                        <button type="button" class="btn btn-light">3 AM</button>
                        <button type="button" class="btn btn-light">3.30 AM</button>
                        <button type="button" class="btn btn-light">4 AM</button>
                        <button type="button" class="btn btn-light">4.30 AM</button>
                        <button type="button" class="btn btn-light">5 AM</button>

                        <button type="button" class="btn btn-light " >9 AM</button>
                        <button type="button" class="btn btn-light">9.30 AM</button>
                        <button type="button" class="btn btn-light">10 AM</button>
                        <button type="button" class="btn btn-light">10.30 AM</button>
                        <button type="button" class="btn btn-light disabled" disabled>11 AM</button>
                        <button type="button" class="btn btn-light">11.30 AM</button>
                        <button type="button" class="btn btn-light">12 PM</button>
                        <button type="button" class="btn btn-light">12.30 AM</button>
                        <button type="button" class="btn btn-light">1 AM</button>
                        <button type="button" class="btn btn-light">1.30 AM</button>
                        <button type="button" class="btn btn-light">2 AM</button>
                        <button type="button" class="btn btn-light">2.30 AM</button>
                        <button type="button" class="btn btn-light">3 AM</button>
                        <button type="button" class="btn btn-light">3.30 AM</button>
                        <button type="button" class="btn btn-light">4 AM</button>
                        <button type="button" class="btn btn-light">4.30 AM</button>
                        <button type="button" class="btn btn-light">5 AM</button>

                        <button type="button" class="btn btn-light " >9 AM</button>
                        <button type="button" class="btn btn-light">9.30 AM</button>
                        <button type="button" class="btn btn-light">10 AM</button>
                        <button type="button" class="btn btn-light">10.30 AM</button>
                        <button type="button" class="btn btn-light">11 AM</button>
                        <button type="button" class="btn btn-light">11.30 AM</button>
                        <button type="button" class="btn btn-light disabled" disabled>12 PM</button>
                        <button type="button" class="btn btn-light">12.30 AM</button>
                        <button type="button" class="btn btn-light">1 AM</button>
                        <button type="button" class="btn btn-light">1.30 AM</button>
                        <button type="button" class="btn btn-light">2 AM</button>
                        <button type="button" class="btn btn-light">2.30 AM</button>
                        <button type="button" class="btn btn-light">3 AM</button>
                        <button type="button" class="btn btn-light">3.30 AM</button>
                        <button type="button" class="btn btn-light">4 AM</button>
                        <button type="button" class="btn btn-light">4.30 AM</button>
                        <button type="button" class="btn btn-light">5 AM</button>

                        <button type="button" class="btn btn-light">9 AM</button>
                        <button type="button" class="btn btn-light">9.30 AM</button>
                        <button type="button" class="btn btn-light">10 AM</button>
                        <button type="button" class="btn btn-light">10.30 AM</button>
                        <button type="button" class="btn btn-light">11 AM</button>
                        <button type="button" class="btn btn-light">11.30 AM</button>
                        <button type="button" class="btn btn-light">12 PM</button>
                        <button type="button" class="btn btn-light">12.30 AM</button>
                        <button type="button" class="btn btn-light">1 AM</button>
                        <button type="button" class="btn btn-light">1.30 AM</button>
                        <button type="button" class="btn btn-light">2 AM</button>
                        <button type="button" class="btn btn-light">2.30 AM</button>
                        <button type="button" class="btn btn-light disabled" disabled>3 AM</button>
                        <button type="button" class="btn btn-light">3.30 AM</button>
                        <button type="button" class="btn btn-light">4 AM</button>
                        <button type="button" class="btn btn-light">4.30 AM</button>
                        <button type="button" class="btn btn-light">5 AM</button>
                    </div>
                   
                </div>
            </div>

            <div class="day-slot-container container-fluid ">
                <div class="all-container">
                    <div class="row slot-day-name"> 
                        <span class="col-sm-12"> Thursday June 19, 2020  </span> 
                        <p class="col-sm-12"> Note: 30 minutes interval </p>
                    </div>
                    
                    <div class="row slot-container">
                       
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light disabled" disabled>9 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">9.30 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">10 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">10.30 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">11 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">11.30 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">12 PM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">12.30 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">1 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">1.30 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">2 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">2.30 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">3 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">3.30 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">4 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">4.30 AM</button>
                        <button type="button" class="col-sm-6 col-lg-1 btn btn-light">5 AM</button>
                    </div>
                   
                </div>
            </div> -->

            <!-- Accordion Example -->
            <div class="card">
                <div class="card-header">
                    <a class="card-link" data-toggle="collapse" href="#collapseOne">
                    Thursday June 17, 2020 
                    </a>
                </div>
                <div id="collapseOne" class="collapse show" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row slot-container">
                            <button type="button" class="btn btn-light disabled" disabled>9 AM</button>
                            <button type="button" class="btn btn-light">9.30 AM</button>
                            <button type="button" class="btn btn-light">10 AM</button>
                            <button type="button" class="btn btn-light">10.30 AM</button>
                            <button type="button" class="btn btn-light">11 AM</button>
                            <button type="button" class="btn btn-light">11.30 AM</button>
                            <button type="button" class="btn btn-light">12 PM</button>
                            <button type="button" class="btn btn-light">12.30 AM</button>
                            <button type="button" class="btn btn-light">1 AM</button>
                            <button type="button" class="btn btn-light">1.30 AM</button>
                            <button type="button" class="btn btn-light">2 AM</button>
                            <button type="button" class="btn btn-light">2.30 AM</button>
                            <button type="button" class="btn btn-light">3 AM</button>
                            <button type="button" class="btn btn-light">3.30 AM</button>
                            <button type="button" class="btn btn-light">4 AM</button>
                            <button type="button" class="btn btn-light">4.30 AM</button>
                            <button type="button" class="btn btn-light">5 AM</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <a class="collapsed card-link" data-toggle="collapse" href="#collapseTwo">
                    Thursday June 19, 2020
                    </a>
                </div>
                <div id="collapseTwo" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row slot-container">
                            <button type="button" class="btn btn-light" >9 AM</button>
                            <button type="button" class="btn btn-light">9.30 AM</button>
                            <button type="button" class="btn btn-light">10 AM</button>
                            <button type="button" class="btn btn-light disabled" disabled>10.30 AM</button>
                            <button type="button" class="btn btn-light">11 AM</button>
                            <button type="button" class="btn btn-light">11.30 AM</button>
                            <button type="button" class="btn btn-light">12 PM</button>
                            <button type="button" class="btn btn-light">12.30 AM</button>
                            <button type="button" class="btn btn-light">1 AM</button>
                            <button type="button" class="btn btn-light">1.30 AM</button>
                            <button type="button" class="btn btn-light">2 AM</button>
                            <button type="button" class="btn btn-light">2.30 AM</button>
                            <button type="button" class="btn btn-light">3 AM</button>
                            <button type="button" class="btn btn-light">3.30 AM</button>
                            <button type="button" class="btn btn-light">4 AM</button>
                            <button type="button" class="btn btn-light">4.30 AM</button>
                            <button type="button" class="btn btn-light">5 AM</button>
                        </div>
                    </div>
                </div>
            </div>
                 
        <!--End-- Day slot  container-->
        </div>
<!--End- Scheduling table container - -->

@stop