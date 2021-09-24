<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
<style>
   /* @import url(//127.0.0.1:8000/fonts/ArialNova.ttf);*/
@font-face {
    font-family: 'Arial Nova';
    src: url({{ storage_path('fonts/ArialNova.ttf') }}) format("truetype");
    font-weight: normal; 
    font-style: normal; 
}


        html{margin:70px 70px 0px 70px}

        body{
            font-family: 'Arial Nova';
        }
        tr{
            margin: 2px 5px;
        }
        .header-tr{
            background-color:#f36424;
            height: 8px;
            color: white;
            font-family: 'Arial Nova';
            font-size: 12px;
        }
        .detail-tr{
            height: 8px;
            font-family: 'Arial Nova';
            font-size: 12px;
        }
        td{
            padding: 1px 0px 1px 8px;
        }
        .title-td{
            background-color:#1f2b3f;
            color: white;
            font-family: 'Arial Nova';
            font-size: 12px;
        }
        .title-header{
            color:#ea670f;
            font-size: 25px;
            float: right;
            font-family: 'Arial Nova';
        }
        .box-table{
            width: 100%;
            border: solid 0.01em #1d1d1d;
            border-spacing: 0px;
        }
        .box-table td{
            border: solid 0.01em #1d1d1d;
        }  
        .page-break {
            page-break-after: always;
        }
        .box{ 
            border: solid 0.02em #1d1d1d;
            outline: solid 0.02em #1d1d1d;
        }
        .div-table {
            margin-left: 0.6em;
            margin-right: 0.1em;
            padding-top: 3px;
        }
        .div-table-col-header {
            background-color:#f36424;
            height: 20px;
            color: white;
            font-family: 'Arial Nova';
            border: solid 0.01em #1d1d1d;
            padding-left:10px;
            font-size: 12px;
        }
        .div-table-col-body {
           font-family:Arial Nova;
           font-size: 12px;
           border: solid 0.01em #1d1d1d;
           text-align: justify;
           padding:5px; 
    }
    
</style>
    </head>
<body>

<table style="width: 100%;">
                <tr>
                    <td  width="70%"><img  src="{{$customer_details->incident_logo_path_with_fallback}}" width="260px" height="66px" >
                    </td>  
                    <td  class="title-header"  width="30%">Incident Report
                    </td>    
                </tr> 
                <tr>
                   <td  colspan="2">
                   </td>
                </tr>
                <tr>
                   <td  colspan="2">
                   </td>
                </tr>
                <tr>
                    <td colspan="2">
                       
                        <table class="box-table">
     
                         <tr class="header-tr">
                             <td colspan="2">
                                Issue Summary
                             </td>   
                         </tr>
                         <tr class="detail-tr">
                            <td class="title-td" width="30%">
                              Title 
                            </td>
                            <td width="70%">
                                {{$incident_report->title or '--'}}
                            </td>
                        </tr> 
                        <tr class="detail-tr" >
                            <td class="title-td" width="30%">
                                Subject
                            </td>
                            <td width="70%">
                                {{$subjectData->subject}}
                            </td>
                        </tr> 
                       
                        <tr class="detail-tr" >
                            <td class="title-td" width="30%">
                              Severity 
                            </td>
                            <td width="70%"  @if($priorityData->value == 'High')
                        style="background-color:#f44336;"
                        @elseif($priorityData->value == 'Medium')
                        style="background-color:#f5de28;"
                        @elseif($priorityData->value == 'Low')
                        style="background-color:#33c519;"
                        @endif >

                                {{$priorityData->value}}
                            </td>
                        </tr>
                        <tr class="detail-tr" >
                            <td class="title-td" width="30%">
                              Shift 
                            </td>
                            <td width="70%">
                                {{ ucfirst($incident_report->time_of_day) }}
                            </td>
                        </tr>
                        <tr class="detail-tr" >
                            <td class="title-td" width="30%">
                              Date 
                            </td>
                            <td width="70%">
                                {{$data['month']}} {{$incident_report->date}}, {{$data['year']}}
                            </td>
                        </tr>
                        <tr class="detail-tr" >
                            <td class="title-td" width="30%">
                              Time 
                            </td>
                            <td width="70%">
                                {{$incident_report->time}}
                            </td>
                        </tr>
                        <tr class="detail-tr" >
                            <td class="title-td" width="30%">
                              Site 
                            </td>
                            <td width="70%">
                                {{$customer_details->project_number}}-{{$customer_details->client_name}}
                            </td>
                        </tr>
                        <tr class="header-tr">
                            <td colspan="2">
                               Client Summary
                            </td>   
                        </tr>
                        <tr class="detail-tr" >
                            <td class="title-td" width="30%">
                             Recipient 
                            </td>
                            <td width="70%">
                                {{$customer_details->contact_person_name}}
                            </td>
                        </tr>
                        <tr class="detail-tr" >
                            <td class="title-td" width="30%">
                              Title 
                            </td>
                            <td width="70%">
                            {{$customer_details->contact_person_position}}
                            </td>
                        </tr>
                        <tr class="detail-tr">
                            <td class="title-td" width="30%">
                              Client 
                            </td>
                            <td width="70%">
                            {{$customer_details->client_name}}
                            </td>
                        </tr>
                        <tr class="detail-tr">
                            <td class="title-td" width="30%">
                              Site Address 
                            </td>
                            <td width="70%">
                                {{$customer_details->address}},  {{$customer_details->city}}
                            </td>
                        </tr>

                        <tr class="header-tr">
                            <td colspan="2">
                               Security Provider
                            </td>   
                        </tr>
                        <tr class="detail-tr">
                            <td class="title-td" width="30%">
                              Report Author 
                            </td>
                            <td width="70%">
                                {{$incident_report->fullname}}
                            </td>
                        </tr>
                        <tr class="detail-tr">
                            <td class="title-td" width="30%">
                              Supervisor 
                            </td>
                            <td width="70%">
                                {{$incident_report->supervisor}}
                            </td>
                        </tr>
                        <tr class="detail-tr">
                            <td class="title-td" width="30%">
                              Regional Manager 
                            </td>
                            <td width="70%">
                                {{$incident_report->area_manager}}
                            </td>
                        </tr>
                        <tr class="header-tr">
                            <td colspan="2">
                               Incident Details
                            </td>   
                        </tr>
                        <tr>
                            <td colspan="2" style=" font-family: 'Arial Nova';font-size: 12px;" width="30%">
                                {{$incident_report->details}}
                            </td>
                        </tr>
                         </table>

                    </td>
                </tr>

              </table>
            <div>
                &nbsp;
            </div>
  @if(isset($data['incident_amendment']))
           @foreach ($data['incident_amendment'] as $incident_amendment)
            <div class="div-table">
             <div class="div-table-row box" >
                <div class="div-table-col-header header-tr" align="left">{{ $incident_amendment['user']['full_name'] }} on {{ $incident_amendment['created_at']->format('l M d,Y (H:i)') }} - {{$incident_amendment->incidentSuggestedStatusList->status}}
                </div>

            <div class="div-table-col-body">
                {{$incident_amendment['notes']}}
            </div>   
             </div>
           </div>
                          {{--  <table class="box-table"  style="margin-left: 0.6em;margin-right: 0.1em;">
                          <tr class="header-tr box-new" >
                            <td colspan="2" >
                               {{ $incident_amendment['user']['full_name'] }} on {{ $incident_amendment['created_at']->format('l M d,Y (H:i)') }}
                            </td>   
                        </tr>
                        <tr> 
                            <td colspan="2" style=" font-family: 'Arial Nova';font-size: 12px;" width="30%">
                                {{$incident_amendment['notes']}}
                            </td>
                        </tr>
                       </table> --}}
                          @endforeach
        
         @endif

     {{-- @if(isset($data['incident_amendment']))
              <table class="box-table" style="margin-left: 0.6em;margin-right: 0.1em;">
                        <tr class="header-tr" >
                            <td colspan="2">
                               Amendments
                            </td>   
                        </tr>
                        <tr class="" >
                            <td colspan="2">      
                            </td>   
                        </tr>
       @foreach ($data['incident_amendment'] as $incident_amendment)
                        
                          <tr class="header-tr box-new" >
                            <td colspan="2" >
                               {{ $incident_amendment['user']['full_name'] }} on {{ $incident_amendment['created_at']->format('l M d,Y (H:i)') }}
                            </td>   
                        </tr>
                        <tr> 
                            <td colspan="2" style=" font-family: 'Arial Nova';font-size: 12px;" width="30%">
                                {{$incident_amendment['notes']}}
                            </td>
                        </tr>
                      
                          @endforeach
         </table>
         @endif --}}
 
     @if(count($data['attachment_id_array']))
      @foreach ($data['attachment_id_array'] as $arr)
      <?php $imageURL = "{storage_path('app')}}/{{config('globals.incident_attachment_folder')}}/{{$customer_details->id}}/{{$data['payperiod_id']}}/{{$arr['name']}}"; ?>
      {{-- @if (file_exists($imageURL))  --}}
              <table class="box-table" style="page-break-before: always;">
                        <tr class="header-tr" >
                            <td colspan="2">
                               Image
                            </td>   
                        </tr>
    {{--  @foreach ($data['attachment_id_array'] as $arr) --}}

       <tr>
         <td colspan="2" align="center"><BR>
           <img src="{{storage_path('app')}}/{{config('globals.incident_attachment_folder')}}/{{$customer_details->id}}/{{$data['payperiod_id']}}/{{$arr['name']}}"  width="600px">
           <BR> <br></td>
       </tr>

      
         </table>
    {{--  @endif --}}
     @endforeach
       @endif
</body>
</html>
