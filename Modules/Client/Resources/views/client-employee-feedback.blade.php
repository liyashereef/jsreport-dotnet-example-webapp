@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
.text-wrap{
    /* height: 40px; */
    width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@section('content')
<div class="table_title">
    <h4>Client Feedback</h4>
</div>
<div id="message"></div>
@can('add_client_feedback')
<div class="col-lg-12 dropdown-adjust">
    <div class="dropdown-alignment row" id="project-filter-div">
        <label class="col-md-2">Select Project</label>
        <div class="col-md-6">
            <select class="form-control option-adjust" id="project-filter">
                <option value="">Please Select</option>
                @foreach($project_list as $id => $each_project)
                <option value="{{$id}}">{{$each_project}}</option>
                @endforeach
            </select>
            <span class="help-block"></span>
        </div>
    </div>
</div>
<div class="col-lg-12 dropdown-adjust">
    <div class="dropdown-alignment row" id="feedback-filter-div">
        <label class="col-md-2">Select Type of Feedback</label>
        <div class="col-md-6">
            <select class="form-control option-adjust" id="feedback-filter">
                <option value="">Please Select</option>
                @foreach($client_feedback_list as $id => $each_feedback)
                <option value="{{$id}}">{{$each_feedback}}</option>
                @endforeach
            </select>
            <span class="help-block"></span>
        </div>
    </div>
</div>
<div class="col-lg-12 dropdown-adjust" id="employee-filter-div" style="display:none" >
    <div class="dropdown-alignment row">
        <label class="col-md-2">Select Employee</label>
        <div class="col-md-6">
            <select class="form-control option-adjust" id="employee-filter">

            </select>
            <span class="help-block"></span>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="dropdown-alignment row">
        <input
        class="btn submit add-new"
        onclick="addnew()"
        type="submit"
        value="Enter New Record"
        style="display:none" id="new-feedback-record">

    </div>
</div>
@endcan

<table class="table table-bordered" id="table-id">
    <thead>
        <tr>
            <th>#</th>
             <th>Project</th>
             <th>Feedback Type</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Rating</th>
            <th>Comments</th>
            <th>Status</th>
            <th>Regional Manager Notes</th>
            <th>Date & Time</th>
            <th>Rated By</th>
            @can("review_client_feedback")
            <th>Action</th>
            @endcan
        </tr>
    </thead>
</table>

@include('client::partials.feedback-modal')

@stop
@section('scripts')
<script>
    $(function () {

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#table-id').DataTable({
                bProcessing: false,
                responsive: true,
                dom: 'lfrtip',
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url":'{{ route('client.employee-rating.get-employee-rating-list') }}',
                    "data": function ( d ) {
                        d.payperiod = $("#payperiod-filter").val();
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                order: [[0, 'desc']],
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                fnRowCallback: function (nRow, aData, iDisplayIndex) {
                    status = aData['status_color_code'];
                    console.log(aData);
                    /* Append the grade to the default row class name */
                    if (status == "1") {
                        $(nRow).addClass('open');
                    } else if(status == "2"){
                        $(nRow).addClass('in_progress');
                    }else if(status == "3"){
                        $(nRow).addClass('closed');
                    }else{
                        $(nRow).addClass('white');
                    }
                },
                columns: [
                {
                    data: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false
                },
                {data: 'project', name: 'project'},
                {data: 'feedback', name: 'feedback'},
                {data: 'full_name', name: 'full_name'},
                {data: 'role', name: 'role'},
                {data: 'rating',  name:'rating'},
                {
                    data: null,
                    name: 'comments',
                    defaultContent: "--",
                    render: function (o) {
                        var notesDiv = '';
                        var notes = o.comments;
                        if(notes){
                            if(notes.length > 100)
                            {
                                notesDiv += '<div class="text-wrap width-200">' +  notes.substr(0, 100) + '...</div>';
                            }else{
                                notesDiv += '<div class="text-wrap width-200">' + notes + '</div>';
                            }
                        }
                       return notesDiv;
                    },
                },
                {data: 'status_lookup_id', name: 'status_lookup_id'},
                {
                    data: null,
                    name: 'reg_manager_notes',
                    defaultContent: "--",
                    render: function (o) {
                        var notesDiv = '';
                        var notes = o.reg_manager_notes;
                        if(notes){
                            if(notes.length > 100)
                            {
                                notesDiv += '<div class="text-wrap width-200">' +  notes.substr(0, 100) + '...</div>';
                            }else{
                                notesDiv += '<div class="text-wrap width-200">' + notes + '</div>';
                            }
                        }
                       return notesDiv;
                    },
                },
                {data: 'date_time', name: 'date_time'},
                {data: 'rated_by', name: 'rated_by'},
                @can("review_client_feedback")
                {
                    data: null,
                    name: 'action',
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions = '<a href="#" class="edit fa fa-edit" data-id=' + o.id + '></a>';
                        return actions;
                    },
                }
                @endcan
                ]
            });
        } catch(e){
            console.log(e.stack);
        }
        $("#project-filter").val($("#project-filter option:eq(1)").val());

        $('#employee-filter').select2();//Added Select2 to employee listing
        $("#employee-filter").on('change',function(){
            var selected_feedback=$('#feedback-filter').val();
            var selected_emp_no = $("#employee-filter").val();
              var emp = $("#employee-filter  option:selected").text();
            if(selected_emp_no !== "" || selected_feedback!=2){
                $("#new-feedback-record").show();
            } else{
                $("#new-feedback-record").hide();
            }
            $("#employee_id").val(selected_emp_no);
               $("#emp").val(emp);
        });

        $('#project-filter').select2();//Added Select2 to project listing
        $("#project-filter-div,#feedback-filter-div").on('change',function(){
           var selected_feedback=$('#feedback-filter').val();
           var selected_prj_no = $("#project-filter").val();
            var feedback=$('#feedback-filter option:selected').text();
            if(selected_prj_no !== "" && selected_feedback==2 && selected_prj_no !== null){
                var url = '{{ route("client.employee-rating.get-employee","") }}/:prj_id';
                var url = url.replace(':prj_id', selected_prj_no);
                fetchEmployee(url);
            } else{
                $("#employee-filter-div").hide();
                $('#employee-filter').val("").trigger("change");
            }
            $("#customer_id").val(selected_prj_no);
            $("#feedback_id").val(selected_feedback);
        $("#feedback").val(feedback);

        });
    });

    function fetchEmployee(url){
        var default_option = "<option value=''>Please Select</option>";
        var option_html = default_option;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            success: function (data) {
                for(each_option in data){
                    console.log(each_option);
                    option_html += "<option value="+each_option+">"+data[each_option]+"</option>";
                }
                console.log(option_html);
                $('#employee-filter').html(option_html);
                $('#employee-filter').val("").trigger("change");
                $("#employee-filter-div").show();
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });
    }

    $('#table-id').on('click', '.edit', function(e){

        var id = $(this).data('id');
        var base_url = "{{route('client.employee-rating.edit', ':id')}}";
        var url = base_url.replace(':id', id);
        console.log(id,url);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:url,
            type: 'GET',
            success: function (data) {
               if(data){
                 addnew(data);
                $('select[name="employee_rating_lookup_id"]').val(data.employee_rating_lookup_id);
                $('textarea[name="customer_feedback"]').text(data.client_feedback);
                $('input[name="id"]').val(data.id);
                $('input[name="employee_id"]').val(data.user_id);
                $('input[name="customer_id"]').val(data.customer_id);
                $('input[name="feedback_id"]').val(data.feedback_id);
                $('textarea[name="reg_manager_notes"]').val(data.reg_manager_notes);
                $('select[name="status_lookup_id"]').val(data.status_lookup_id);
                $('select[name="employee_rating_lookup_id"]').prop('disabled', true);
                $('textarea[name="customer_feedback"]').attr('readonly', true);
                if(data.user!=null){
                var last_name=(data.user.last_name!=null)?data.user.last_name:'';
            }
                var emp_name=(data.user_id!=null)?data.user.first_name+" "+last_name:'';
                var feedback=(data.client_feedbacks!=null)?data.client_feedbacks.feedback:'';
                modal_title(feedback,emp_name)


               }
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            contentType: false,
            processData: false,
        });
    });

    function addnew(data=null) {
        var selected_feedback=$('#feedback-filter').val();
           var selected_prj_no = $("#project-filter").val();
           if(selected_prj_no=='' && data==null)
           {
             swal("Warning", "Please select project", "warning");
             return false;
           }
            if(selected_feedback=='' && data==null)
           {
             swal("Warning", "Please select feedback", "warning");
              return false;
           }
        $("#myModal").modal();
        $('input[name="id"]').val('');
        $('#client-employee-rating-form').trigger('reset');
        $('#client-employee-rating-form textarea').text('');
        $('select[name="employee_rating_lookup_id"]').prop('disabled', false);
        $('textarea[name="customer_feedback"]').attr('readonly', false);
        $('#client-employee-rating-form').find('.form-group').removeClass('has-error').find('.help-block').text('');

        modal_title($('#feedback').val(),$('#emp').val());

    }
    function modal_title(feedback,employee){
        if(employee!='' && employee!='Please Select' && feedback!=''){
        $('#myModal .modal-title').text(feedback+'-'+employee);
        }
        else
        {
           $('#myModal .modal-title').text(feedback);
        }
    }


</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
