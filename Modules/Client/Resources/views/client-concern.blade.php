@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('css')
<style>
    .add-new{
        float: left;
        margin-top: 0px;
        padding: 8px;
    }
    .text-wrap{
    /* height: 40px; */
    width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    }
</style>

@endsection

@section('content')
<div class="table_title">
    <h4>Client Concern</h4>
</div>
<div id="message"></div>
@can('add_client_concern')
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
        <div class="col-md-2" >
                <input
                class="btn submit add-new"
                onclick="addnew()"
                type="submit"
                value="Enter New Record"
               {{--  style="display:none"  --}}
                id="new-feedback-record">

        </div>
    </div>
</div>


@endcan

<table class="table table-bordered" id="table-id">
    <thead>
        <tr>
            <th>#</th>
             <th>Project</th>
             <th>Client Name</th>
            <th>Severity</th>
            <th>Concern</th>
            <th>Status</th>
            <th>Regional Manager Notes</th>
            <th>Date & Time</th>
            @can("review_client_concern")
            <th>Action</th>
            @endcan
        </tr>
    </thead>
</table>

@include('client::partials.concern-modal')

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
                    "url":"{{ route('client.concern-list') }}",
                    "data": function ( d ) {
                      d.payperiod = $("#payperiod-filter").val();
                      //Url arguments
                      let args = globalUtils.uraQueryParamToJson(window.location.href);
                      d =  $.extend(d, args);
                      d.cIds = globalUtils.decodeFromCsv(d.cIds);
                      return d;
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
                {data: 'full_name', name: 'full_name'},
                {data: 'severity', name: 'severity'},
                {
                    data: null,
                    name: 'concern',
                    defaultContent: "--",
                    render: function (o) {
                        var notesDiv = '';
                        var concern_notes = o.concern;
                        if(concern_notes){
                            if(concern_notes.length > 100)
                            {
                                notesDiv += '<div class="text-wrap width-200">' +  concern_notes.substr(0, 100) + '...</div>';
                            }else{
                                notesDiv += '<div class="text-wrap width-200">' + concern_notes + '</div>';
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
                @can("review_client_concern")
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
                ],
            });

            table.on('draw', function () {
                refreshSideMenu();
            } );
        } catch(e){
            console.log(e.stack);
        }
        $("#project-filter").val($("#project-filter option:eq(1)").val());
          var selected_prj_no = $("#project-filter").val();
            $("#customer_id").val(selected_prj_no);

        $('#project-filter').select2();//Added Select2 to project listing

    });
           $("#project-filter-div").on('change',function(){
            var selected_prj_no = $("#project-filter").val();
            $("#customer_id").val(selected_prj_no);
              });


    $('#table-id').on('click', '.edit', function(e){
        var id = $(this).data('id');
        var base_url = "{{route('client-concern.edit', ':id')}}";
        var url = base_url.replace(':id', id);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:url,
            type: 'GET',
            success: function (data) {
               if(data){
                addnew(data);
                $('select[name="severity"]').val(data.severity_id);
                $('textarea[name="concern"]').text(data.concern);
                $('input[name="id"]').val(data.id);
                $('input[name="employee_id"]').val(data.user_id);
                $('input[name="customer_id"]').val(data.customer_id);
                $('textarea[name="reg_manager_notes"]').val(data.reg_manager_notes);
                $('select[name="status_lookup_id"]').val(data.status_lookup_id);
                $('select[name="severity"]').prop('disabled', true);
                $('textarea[name="concern"]').attr('readonly', true);
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
           var selected_prj_no = $("#project-filter").val();
           if(selected_prj_no=='' && data==null)
           {
             swal("Warning", "Please select project", "warning");
             return false;
           }
        $("#myModal").modal();
        $('input[name="id"]').val('');
        $('#client-employee-rating-form').trigger('reset');
        $('#client-employee-rating-form textarea').text('');
        $('select[name="severity"]').prop('disabled', false);
        $('textarea[name="concern"]').attr('readonly', false);
        $('#client-employee-rating-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
    }
</script>
<style type="text/css">

</style>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
