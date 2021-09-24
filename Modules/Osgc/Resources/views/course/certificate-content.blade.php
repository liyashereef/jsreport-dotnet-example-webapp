<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
<style>
   /* @import url(//127.0.0.1:8000/fonts/ArialNova.ttf);*/
    @font-face {
    font-family: 'Script MT Bold';
    src: url({{ storage_path('fonts/Scriptbl.ttf') }}) format("truetype");
    font-weight: normal; 
    font-style: normal; 
}
@font-face {
    font-family: 'Arial';
    src: url({{ storage_path('fonts/ArialNova.ttf') }}) format("truetype");
    font-weight: normal; 
    font-style: normal; 
}

       

      
        body{
        font-family: 'Script MT Bold';
        background-image: url({{$bg}});
        background-size: 100%;
        background-repeat: no-repeat;
        }
        
        table#mytable td
        {
            border: none !important;
        }
.name { 
   position: absolute; 
   top: 290px; 
   font-size:45px;
   
}
.course-name { 
   position: absolute; 
   top: 480px; 
   color: #003A63;
   font-size:35px;
   font-family: Arial;
   font-style: italic ;
   font-weight: bold;
   
}
.course-date { 
   position: absolute; 
   top: 600px; 
   color: #003A63;
   font-size:20px;
   font-family: Arial;
   font-style: italic;
   font-weight: bold;
}       
</style>
    </head>
<body>

<table align="center">
    <tr ><td ><span  class="name">{{$result['user_name']}}</span></td></tr>
</table>
<table align="center">
    <tr><td ><span  class="course-name">{{$result['course_name']}}</span></td></tr>
</table>
<table align="center">
    <tr><td ><span class="course-date">{{$result['course_date']}}</span></td></tr>
</table>
    
</body>
</html>
