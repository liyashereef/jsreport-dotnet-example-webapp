@extends('layouts.cgl360_ids_scheduling_layout')
@section('css')
    <style>
        .internaluserclass{
            display: none;
        }
        .loggedinternaluserclass{
            display: none;
        }
        .userblock .row {
        margin-bottom: 15px;
}
.prereqol{
    display: none;
}
.prereqol li{
    margin-bottom: 5px;
}
div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
    th{
        background: #F36907 !important;
    }
    th, td { 
        white-space: nowrap;
         }
    .rotate {
        writing-mode: vertical-lr;
        -webkit-transform: rotate(-180deg);
        -moz-transform: rotate(-180deg);
        font-size: 15px;
    }
    .slot{
        border:solid 1px #000 !important;
        text-align: center;
        cursor: pointer;
    }

    /* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.booked{
    background : red !important; 
}
.open{
    background: #F36907 !important;
}
/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 60%;
}


/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
</style>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/css/gijgo.min.css" rel="stylesheet" type="text/css" /> 
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> 
<link href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css" /> 
@endsection
@section('content')
<div class="container-fluid" style="padding-left:0px !important">
    <div class="row">
        <div class="col-md-6">
            {{ Form::open(array('id'=>'schedule-form', 'class'=>'form-horizontal', 'method'=> 'POST')) }}
            <div class="container-fluid userblock" >
                <div class="row ">
                    <div class="col-md-12 table_title">
                        <h4 style="margin:0px !important">Facility Signout</h4>
                    </div>
                    <div class="col-md-6 usertype" style="display: none">
                        <select class="form-control" id="usertype">
                            <option value="">Select any</option>
                            <option value="internal">Internal - I am an employee of Commissionaires </option>
                            <option value="external">External - I am an External looking to book an Amenity</option>
                        </select>
                        <input type="hidden" id="loggedinuser" value="{{$userid}}" />
                    </div>
                    <div class="col-md-12">
                        
                    </div>
                </div>
                
                <div class="row internaluserclass">
                    <div class="col-md-6">
                        <input id="username" name="username" type="text" autocomplete="false" class="form-control" value="" placeholder="User name" />
                    </div>
                </div>
                <div class="row internaluserclass">
                    <div class="col-md-6">
                        <input id="userpassword" name="userpassword" type="password" autocomplete="false" class="form-control" value="" placeholder="Password" />
                    </div>
                </div>
                <div class="row internaluserclass">
                    <div class="col-md-6">
                        <input type="button" id="internaluserlogin"  class="button btn-primary form-control" value="Submit"  />
                    </div>
                </div>
                <div class="row loggedinternaluserclass">
                    <div class="col-md-6" id="welcomeuser">
                        
                    </div>
                </div>
                <div class="row loggedinternaluserclass">
                    <div class="col-md-6" id="amenities">
                        
                    </div>
                </div>
                <div class="row loggedinternaluserclass amenitycategories">
                    <div class="col-md-6" id="categories">
                        
                    </div>
                </div>
                
                
            </div>
            {{ Form::close() }}
        </div>
        <div class="col-md-6" style="background: cornsilk;border-radius:10px">
                <div class="container-fluid" style="padding: 0px !important">
                    <div class="row" style="background:#F36907 ">
                        <div class="col-md-12" style="margin-top:5px;">
                            <b>Prerequisites</b>
                        </div>
                    </div>
                    <div class="row" style="margin-top:5px">
                        <div class="col-md-12" >
                            <ol class="prereqol">
                                <li>Q1</li>
                                <li>Q2</li>
                                <li>Q3</li>
                                <li>Q4</li>
                                <li>Q5</li>
                                <li>Q6</li>
                                <li>Q7</li>
                                <li>Q8</li>
                                <li>Q9</li>
                            </ol>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    <div class="row colorblock" style="display: none;margin-top:20px !important;font-weight:bold">
        <div class="col-md-1">Legend</div>
        <div class="col-md-2">
            <div style=";border:solid 1px #000 !important;width:50px;height:100%;background:grey;display: inline-block;vertical-align:top"></div>
            <div style="display: inline-block;vertical-align:top">Closed</div>
        </div>
        <div class="col-md-2">
            <div style=";border:solid 1px #000 !important;width:50px;height:100%;background:black;display: inline-block;vertical-align:top"></div>
            <div style="display: inline-block;vertical-align:top">Maximum occupancy</div>
        </div>
        <div class="col-md-3" style="word-wrap: break-word;"><div style="border:solid 1px #000 !important;width:50px;height:100%;background:skyblue;display: inline-block;vertical-align:top"></div>
        <div style="display: inline-block;vertical-align:top;word-wrap: break-word;">Available opening for date selected</div></div>
        <div class="col-md-3"><div style=";border:solid 1px #000 !important;width:50px;height:100%;background:white;display: inline-block;vertical-align:top"></div>
        <div style="display: inline-block;vertical-align:top">Other available slots(Other dates)</div></div>
    </div>
    <div class="row">
        <div class="col-md-12" id="scheduleblock">

        </div>
    </div>
</div>
    
<div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
      
      <div class="container-fluid">
        <div class="row">
              <div class="col-md-11"><b>Booking</b></div>
              <div class="col-md-1" style="text-align: right"><span class="close">&times;</span></div>
        </div>
        <div class="row">
            <div class="col-md-4" style="vertical-align: bottom">Amenity</div>
            <div class="col-md-6" style="padding-top:10px">
                <input type="hidden" id="blockid" value="0" />
                <input type="text" class="form-control" id="modelamenity" value="" readonly />
            </div>
        </div>
        <div class="row amcategory">
            <div class="col-md-4 " style="vertical-align: bottom">Category</div>
            <div class="col-md-6" style="padding-top:10px">
                <input type="text" class="form-control" id="modelamenitycategory" value="" readonly />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4" style="vertical-align: bottom">Booking date</div>
            <div class="col-md-6" style="padding-top:10px">
                <input type="text" class="form-control" id="modelbookingdate" value="" readonly />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4" style="vertical-align: bottom">Slot start time</div>
            <div class="col-md-6" style="padding-top:10px">
                <input type="text" class="form-control" id="modelstarttime" value="" readonly />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4" style="vertical-align: bottom">Slot end time</div>
            <div class="col-md-6" style="padding-top:10px">
                <input type="text" class="form-control" id="modelendtime" value="" readonly />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4" style="vertical-align: bottom">Recaptcha</div>
            <div class="col-md-6" style="padding-top:10px"></div>
        </div>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-6" style="padding-top:10px">
                <button type="button" id="savecondobooking" class="button btn-primary">Save</button>
            </div>
        </div>
      </div>
    </div>
  
  </div>
    
@stop
@section('scripts')
    
    

    <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
    @include('facility::partials.cbsscripts')
    

@endsection