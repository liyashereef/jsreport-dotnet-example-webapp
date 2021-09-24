@extends('layouts.app')
<style>
    .table_title_this_page{
        padding-top: 23px;
        padding-bottom: 24px;
    }
    .sweet-alert {
        box-sizing : border-box;
        max-height : 80% !important;
        overflow-y : auto !important;
        padding : 0 17px 17px !important;
    }
    .sweet-alert:before {
        content : "";
        display : block;
        height : 17px;
        width : 0;
    }
</style>

@section('content')

{{--    <div class="table_title table_title_this_page" style="color: black;">--}}

    {{--        <span style=""> Course : </span><span >{{$course_details->course_title}} </span> <br>--}}
    {{--        <span> Objectives : </span> <span style="color: black;">{{$course_details->course_objectives}}</span> <br>--}}
    {{--        <span style=""> Description : </span> <span style="color: black;">{{$course_details->course_description}}</span>--}}

{{--    </div>--}}

<!--     <div class=" table_title_this_page" >

        {{--        <p> Course : <span style="color: black;font-weight: bold"> </span>  </p>--}}
        @if($course_details->course_image != '')
            <img src="{{asset('LearningAndTraining/course_images/'.$course_details->course_image)}}" style="width: 20%;" id="course_image_src">
        @endif
        <p> Name : <span style="color: black;font-weight: bold">{{$course_details->course_title}} </span>  </p>
        @if($course_details->course_objectives != '')
            <p> Objectives : <span style="color: black;font-weight: bold">{{$course_details->course_objectives}}</span>  </p>
        @endif
        @if($course_details->course_description != '')
            <p> Description :<span style="color: black;font-weight: bold">{{$course_details->course_description}}</span> </p>
        @endif
        @if($course_details->course_due_date != '')
            <p> Due Date :<span style="color: black;font-weight: bold">{{$course_details->course_due_date}}</span> </p>
        @endif

    </div> -->

    <div class="wide-block jumbotron">
       <div class="container-fluid mb-0">
        <div class="row">
            <div class="col-md-3 col-lg-3 col-xl-2">
                @if($course_details->course_image =="")
                <img src="{{ asset('images/courses_noimage.png') }}" alt="" class="w-100 banner-intro"/>
                @else
                <img src="{{ asset('LearningAndTraining/course_images') }}/{{$course_details->course_image}}" alt="" class="w-100 banner-intro"/>
                @endif
            </div>
            <div class="col-md-7 jum-titleblock col-lg-7 col-xl-8">
                <h1 class="color-high-md mt-4 text-sm-center text-md-left text-center">{{$course_details->course_title}}</h1>
                <h2 class="color-light text-sm-center text-md-left text-center">{{$course_details->course_description}}</h2>
                <!-- <div class="rating_div"></div> -->
                <div class="star-rating">
                    @for ($i = 1; $i <= $course_rating; $i++)
                    <span><img src="{{asset('css/training/leaner-dashboard/images/Rating-star-icon.png')}}" alt=""></span>
                    @endfor

                </div>

            </div>
            <div class="second circle progress-circle col-md-2 col-lg-2 col-xl-2 d-flex align-items-center justify-content-center">

            </div>

        </div>
    </div>

</div>

<input type="hidden" name="course_id" value="{{$course_details->id}}">
<div id="dashboard_div">
    <div class="row">

        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 visit-log-padding">

            <div class="visit-log-card">
                <i class="fa fa-users icon-style"></i>
                Assigned
                <div class="visit-log-count-text"> @if(isset($course_details->CourseUserAllocationCount)){{$course_details->CourseUserAllocationCount->data_count}} @else {{0}} @endif</div>
            </div>

        </div>

        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 visit-log-padding">

            <div class="visit-log-card">
                <i class="fa fa-graduation-cap icon-style"> </i>
                Completed
                <div class="visit-log-count-text">@if(isset($course_details->CourseUserAllocationCompletedCount)){{$course_details->CourseUserAllocationCompletedCount->data_count}} @else {{0}} @endif</div>
            </div>

        </div>
         <div class=" col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right" >
        <form action="{{route('learningandtraining.dashboard.training-user-course-details', $id)}}"  method="GET">
            <input type="submit"  value="Candidates View" class="btn submit"  style="background-color: #f26222">
        </form>
    </div>

    </div>
</div>

<br>
<div class="add-new manual-completion">
    <span class="add-new-label">Mark as Completed</span>
</div>

<div class="table_title">
    <h4>Employee Lists </h4>
</div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 card-padding">

        <table class="table table-bordered" id="team-table">
            <thead>
                <tr>
                    <th class="dt-body-center text-center select_header"><input name="select_all" value="1" id="example-select-all" type="checkbox"/></th>
                    <!--  <th>#</th> -->

                    {{--                    <th>Emp No</th>--}}
                    <th>Name</th>
                    <th>Email</th>
                    <th>Assigned Date</th>
                    <th>Completed</th>
                    <th>Manually Completed</th>
                    <th>Completed Date</th>
                    <th>Course Type</th>
                    <th>Score (%) </th>
                    <th>No.of Attempts</th>
                </tr>
            </thead>
        </table>


</div>

@stop
@section('scripts')
<link href="{{ asset('css/training/leaner-dashboard/css/dashboard-styles.css') }}" rel="stylesheet">
<link href="{{ asset('css/training/course-list.css') }}" rel="stylesheet">
<script>
$(window).resize(function(){
    $(".sweet-alert").css("margin-top",-$(".sweet-alert").outerHeight()/3);
});
    $(document).ready(function() {

        /***** Course  Listing - Start */

        $.fn.dataTable.ext.errMode = 'throw';

        try {
            var exportName = `Allocation Details of {{$course_details->course_title}}`;
            var view_url = '{{ route("learningandtraining.dashboard.course-details-users",":id") }}';
            view_url = view_url.replace(':id', {{$id}});

            table = $('#team-table').DataTable({
                bProcessing: false,
                responsive: true,
                dom: 'lfrtBip',
                buttons: [
                    {
                            extend: 'pdfHtml5',
                            text: ' ',
                            title : exportName,
                            orientation: 'landscape',
                            className: 'btn btn-primary fa fa-file-pdf-o',
                            exportOptions: {
                                columns:[1, 2, 3, 4, 5, 6, 7, 8, 9]
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: ' ',
                            title : exportName,
                            className: 'btn btn-primary fa fa-file-excel-o',
                            exportOptions: {
                                columns:[1, 2, 3, 4, 5, 6, 7, 8, 9]
                            }
                        },
                        {
                            extend: 'print',
                            text: ' ',
                            title : exportName,
                            orientation: 'landscape',
                            className: 'btn btn-primary fa fa-print',
                            exportOptions: {
                                columns:[1, 2, 3, 4, 5, 6, 7, 8, 9]
                            }
                        },
                    ],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                    ],
                processing: true,

                fixedHeader: true,
                ajax: {
                    "url": view_url,
                    "data": function (d) {
                        d.payperiod = $("#payperiod-filter").val();
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                'columnDefs': [
                {
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center',
                    'visible':true,
                    'render': function (data, type, full, meta){
                        if(full.completed!='Yes'){
                            return '<input type="checkbox"  name="employee_id_for_course_completion" value="' + $('<div/>').text(data).html() + '">';
                        }
                        else
                        {
                            return '';
                        }
                    }
                },

                ],
                 "fnInitComplete": hideAllCmpleted,


                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                    // order: [[0, 'desc']],
                    columns: [
                    {
                        data:'id',
                        name:'id',
                    },
                    // {
                    //     data: 'id',
                    //     render: function (data, type, row, meta) {
                    //         return meta.row + meta.settings._iDisplayStart + 1;
                    //     },
                    //     orderable: false
                    // },
                        // { data: 'emp_no', name: 'emp_no' },
                        { data: 'emp_name', name:'emp_name'},
                        { data: 'emp_email', name: 'emp_email' },
                        { data: 'alloted_date', name: 'alloted_date' },
                        {
                            //data: 'employee.user.first_name',
                            data:'completed',
                            name:'completed',
                        },
                        {
                            //data: 'employee.user.first_name',
                            data:null,
                            name:'manual_completion',
                            render:function(o){

                                var text='';
                                if(o.completed == 'Yes')
                                {
                                    text= o.manual_completion;

                                    orderable: false
                                }
                                return text;
                            }
                        },
                        {
                            //data: 'employee.user.first_name',
                            data:null,render:function(o){
                                if(o.completed_date != null)
                                {
                                    return o.completed_date
                                }else{
                                    return ''
                                }
                                orderable: false
                            },
                            name:'completed_date'
                        },

                        {
                            //data: 'employee.user.first_name',
                            data:null,render:function(o){
                                if(o.course_type_flag == 1)
                                {
                                    return 'Mandatory'
                                }else if(o.course_type_flag == 2){
                                    return 'Recommended'
                                }else{
                                    return ''
                                }
                                orderable: false
                            },
                            name:'course_type'
                        },
                        { data: 'score', name: 'score' },
                        // { data: 'number_attempts', name: 'number_attempts' },
                        {
                            data: null,
                            name: 'number_attempts',
                            // sortable: false,
                            render: function (o) {
                                var actions = '';
                                let attemptString = btoa(JSON.stringify(o.attempts_history));
                                actions += '<a href="#" class="attempts_history"  data-history=' + attemptString + '>' + o.number_attempts + '</a>';
                                return actions;
                            },
                     }

                        ]
                    });
        } catch (e) {
            console.log(e.stack);
        }
            /***** Course  Listing - End */
            $('#team-table').on('click', '.attempts_history', function(){

               let attempts_history = JSON.parse(atob($(this).data('history')));

                if(attempts_history.length >0 ){

                    var swal_html = `<div class="panel">
                                         <div class="panel-body">
                                            <table align="center" class="table">`;
                    swal_html+= `<thead>
                                    <tr>
                                        <th>Score</th>
                                        <th>Percentage</td>

                                        <th style="text-align: center; vertical-align: middle;">Submitted Date and Time</th>
                                    </tr>
                                </thead>`;
                    $.each(attempts_history, function( index, value ) {
                        let status = 'Fail';
                        if(value.is_exam_pass == 1){
                            status = 'Pass';
                        }
                        // <td style="text-align: center; vertical-align: middle;">${status}</td>
                        swal_html+= `<tr>
                                        <td style="text-align: center; vertical-align: middle;">${value.total_exam_score}</td>
                                        <td style="text-align: center; vertical-align: middle;">${Math.round(value.score_percentage)}%</td>

                                        <td style="text-align: center; vertical-align: middle;">${moment(value.submitted_at).format('MMMM Do YYYY, h:mm A')}</td>
                                    </tr>`;
                    });
                    swal_html+= `   </table>
                                  </div>
                                </div>`;
                }else{
                    var swal_html='<p>No previous record found</p>';
                }

                swal({title:"Result History", text: swal_html,html: true,backdrop:false,heightAuto: false,});

            });

            $('#team-table').on('click', '#example-select-all', function(){


                var rows = table.rows({ 'search': 'applied' }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
                        // Handle click on checkbox to set state of "Select all" control
            $('#team-table tbody').on('change', 'input[type="checkbox"]', function(){
                // If checkbox is not checked
                if(!this.checked){
                    var el = $('#example-select-all').get(0);
                    // If "Select all" control is checked and has 'indeterminate' property
                    if(el && el.checked && ('indeterminate' in el)){
                    // Set visual state of "Select all" control
                    // as 'indeterminate'
                    el.indeterminate = true;
                    }
                }
            });

            $('.manual-completion').click(function() {

               if( $("#team-table input:checkbox:checked").length ==0)
               {
                   swal("Alert", "No checkbox checked", "warning");
               }
               else
               {
                    var rows = table.rows({ 'search': 'applied' }).nodes();
                    var employee_array = $.map($('input[name="employee_id_for_course_completion"]:checked',rows), function(c){return c.value; })
                    console.log(employee_array)
                    var course_id= $('input[name="course_id"]').val();
                    $.ajax({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{route('learningandtraining.manual-completion')}}',
                    type: 'POST',
                    data:  {'employee_array': employee_array, 'course_id':course_id},
                    success: function (data) {
                        if (data.success) {
                            swal("Completed", "Course has been manually completed", "success");
                            table.ajax.reload(hideAllCmpleted);

                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                            //alert(xhr.status);
                            //alert(thrownError);
                            console.log(xhr.status);
                            console.log(thrownError);
                            swal("Oops", "Something went wrong", "warning");
                        },
                    });
                }
            });

        });

function hideAllCmpleted() {

     var remaining_to_complete = table.$('tr').find('input[name="employee_id_for_course_completion"]').length;
    if(remaining_to_complete==0)
    {
      $('.select_header').hide();
      table.column(0).visible(false);
      $('.manual-completion').hide();
  }
}
</script>
<style type="text/css">
    .manual-completion
    {
      margin-right: 1em;

    }
</style>
@stop
