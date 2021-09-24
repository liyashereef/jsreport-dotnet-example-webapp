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

           

        body{
            /* font-family: 'Arial Nova'; */
            font-family: 'Montserrat' !important;
        }
        .title-header-report{
            color:#575c5a;
            font-size: 24px;
            font-weight: bold;
        }
        .title-header-logoname{
            color:#575c5a;
            font-size: 17px;
        }
        .title-header{
            line-height: 5px;
        }
        .title-date{
            color:#575c5a;
            font-size: 13px;
            line-height: 5px;
        }
        .footer{
            color:#575c5a;
            font-size: 14px;
            font-weight: bold;
            
        }
        .title-label{
            font-weight: bold;
            
        }
        .title-end-label{
            padding-left: 5px;
        }
        
        .title-customer-name
        {
            color:#575c5a;
            font-size: 16px;
            line-height: 30px;
        }
        .box-table{
            width: 100%;
            border: solid 0.01em #1d1d1d;
            border-spacing: 0px;
        }
        .box-table td{
            border: solid 0.01em #1d1d1d;
            color:#575c5a;
            font-size: 13px;
            padding: 0px 0px 0px 8px;
           

        }  
        

        .box-table th{
            background-color: #7f7b7b;
            color: white;
        }
        .issue-details
        {
           
        }
        
        html{margin:20px 20px 0px 20px;
            font-family: 'Montserrat' !important;}
</style>
    </head>
<body>

   
<table style="width: 100%;">
                <tr>
                    <td  width="20%"><img  src="{{public_path()."/images/CGL-LOGO-600px-152px.png"}}" width="200px" >
                    </td>  
                    <td   width="48%" class="title-header" align="center">
                        <p class="title-header-report">Daily Activity Report<p>
                        <p class="title-header-logoname">Commissionaires Great Lakes<p>
                    </td>  
                    <td   width="32%" class="title-date">
                        <p><span class="title-label">Start:</span> {{\Carbon::parse($fromDate)->format('M d, Y h:i:s A')}}</p>
                        <p><span class="title-label title-end-label">  End:</span> {{\Carbon::parse($toDate)->format('M d, Y h:i:s A')}}</p>
                    </td>  
                </tr>
                <tr class="sub-title">
                    <td colspan="3" class="title-customer-name">{{$customerDetails}}</td>
                </tr>
               
                <tr>
                <table class="box-table">
     
                         <tr>
                             <th>
                             Created Date
                             </th> 
                             <th>
                                Issue Details
                             </th>   
                         </tr> 
                         @foreach($result as $row)
                         <tr>
                             <td>
                             {{\Carbon::parse($row->time)->format('M d, Y h:i:s A')}} 
                             </td> 
                             <td class="issue-details">
                                <p><span>{{$row->user->first_name.' '.$row->user->last_name ?? ''}}</span> - <span>{{$row->QrcodeWithTrashed->location}}</span>
                                @if($row->comments)<span><br/>NOTE:{{$row->comments}}</span>@endif
                                </p>
                             </td>   
                         </tr>
                         @endforeach
                         
                </table>
                
                </tr>
                
               
                   
     
</table>
<table style="width: 100%;"><tr class="footer"><td colspan="3" align="center">Report Run: {{\Carbon::now()->format('M d, Y h:i:s A')}}</td></tr></table>       
</body>
</html>
