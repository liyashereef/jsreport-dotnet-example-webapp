@extends('adminlte::page')
@section('title', 'Holidays')
@section('css')
<style>
    .tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

.add-new-stat {
    float: right;
    width: 175px;
    background-color: #f26222;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 10px;
    text-align: center;
    border-radius: 5px;
    padding: 5px 0px;
    margin-left: 5px;
    cursor: pointer;
}

.add-new,
.dataTables_wrapper .dataTables_filter input {
    width: 175px;
}
/* Style the buttons that are used to open the tab content */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}    
</style>    
@endsection
@section('content_header')
<h1>Holidays</h1>
@stop
@section('content')
<div class="tab">
    <button class="tablinks" id="tab1" onclick="openCity(event, 'Holidays','tab1')">Holidays</button>
    <button class="tablinks" id="tab2" onclick="openCity(event, 'stat','tab2')">Stat Holidays</button>
  </div>
  
  <!-- Tab content -->
  <div id="Holidays" class="tabcontent">
    <div id="message"></div>
    <div class="add-new" data-title="Add New Holiday">Add
        <span class="add-new-label">New</span>
    </div>
<table class="table table-bordered" id="holiday-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Year</th>
            <th>Holiday</th>
            <th>Description</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
  </div>
  
  <div id="stat" class="tabcontent">
    <div class="add-new-stat" id="addnewstat" data-title="Add New Stat Holiday">Add
        <span class="add-new-label">New</span>
    </div>
    <table class="table table-bordered" id="statholiday-table">
        <thead>
            <tr>
                <th style="text-align:left">#</th>
                <th style="text-align:left">Holiday</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        
    </table>
  </div>
  
 
  <div class="modal fade" id="statModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Holiday</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'stat-holiday-form','class'=>'form-horizontal', 'method'=> 'POST')) }} 
            {{ Form::hidden('statid',null) }}
            <div class="modal-body">
                

                <div id="holiday" class="form-group">
                    <label for="holiday" class="col-sm-3 control-label">Holiday</label>
                    <div class="col-sm-9">
                        {{ Form::text('statholiday',null,array('class'=>'form-control','placeholder' => 'Holiday','max'=>'2900-12-31')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_stat_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Holiday</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'holiday-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group" id="year">
                    <label for="year" class="col-sm-3 control-label">Year</label>
                    <div class="col-sm-9">
                        <select class="form-control has-error" name="year"></select>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div id="holiday" class="form-group">
                    <label for="holiday" class="col-sm-3 control-label">Holiday</label>
                    <div class="col-sm-9">
                        {{ Form::text('holiday',null,array('class'=>'form-control datepicker','placeholder' => 'Holiday','max'=>'2900-12-31')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="description">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::text('description',null,array('class'=>'form-control','placeholder' => 'Holiday Description')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
@section('js')
<script>

$("#addnewstat").on('click',function(e){
    e.preventDefault();
    
    $('#statModal input[name="statid"]').val("")
    $('#statModal input[name="statholiday"]').val("")
    $("#statModal").modal();
})

function openCity(evt, cityName,id) {
$("#holiday-table").css("width","100%");
$("#statholiday-table").css("width","100%")
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(cityName).style.display = "block";
  $("#"+id).addClass("active");
}
setTimeout(() => {
    $("#tab1").trigger("click");
}, 1000);

$(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#holiday-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                width:"100%",
                buttons: [{
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        action: function (e, dt, node, conf) {
                            emailContent(table, 'Holidays');
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('holiday.list') }}",
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [[ 5, "desc" ]],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'year',
                        name: 'year'
                    },
                    {
                        data: 'holiday',
                        name: 'holiday'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (o) {
                            if (o.holiday >= "{{date('Y-m-d')}}") {
                                @can('edit_masters')
                                actions = '<a href="#" class="edit fa fa-pencil" data-id=' + o.id +'></a>';
                                @endcan
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete fa fa-trash-o" data-id=' +o.id + '></a>';
                                @endcan
                            } else {
                                actions = '<a href="#" class="fa fa-pencil edit-disable"></a>';
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete fa fa-trash-o" data-id=' +o.id + '></a>';
                                @endcan
                            }
                            return actions;
                        },
                    }
                ]
            });

            
        } catch (e) {
            console.log(e.stack);
        }

        try {
            var stattable = $('#statholiday-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                width:"100%",
                buttons: [{
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        action: function (e, dt, node, conf) {
                            emailContent(table, 'Holidays');
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('holiday.statlist') }}",
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [[ 1, "asc" ]],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'holiday',
                        name: 'holiday'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (o) {
                            if (o.id >0) {
                                @can('edit_masters')
                                actions = '<a href="#" class="edit fa fa-pencil" data-id=' + o.id +'></a>';
                                @endcan
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete fa fa-trash-o" data-id=' +o.id + '></a>';
                                @endcan
                            } else {
                                actions = '<a href="#" class="fa fa-pencil edit-disable"></a>';
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete fa fa-trash-o" data-id=' +o.id + '></a>';
                                @endcan
                            }
                            return actions;
                        },
                    }
                ]
            });
        } catch (error) {
            console.log(error);
        }
        
        
        /* Holiday Store - Start*/
        $('#holiday-form').submit(function (e) {
            e.preventDefault();
            if($('#holiday-form input[name="id"]').val()){
                var message = 'Holiday has been updated successfully';
            }else{
                var message = 'Holiday has been created successfully';
            }
            formSubmit($('#holiday-form'), "{{ route('holiday.store') }}", table, e, message);
        });

        $('#stat-holiday-form').submit(function (e) {
            e.preventDefault();
            if($('#stat-holiday-form input[name="id"]').val()){
                var message = 'Holiday has been updated successfully';
            }else{
                var message = 'Holiday has been created successfully';
            }
            formSubmit($('#stat-holiday-form'), "{{ route('statholiday.store') }}", stattable, e, message);
            $('#statModal').modal('hide');
        });
        /* Holiday Store - End*/

        /* Holiday Edit - Start*/
        $("#holiday-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("holiday.single",":id") }}';
            var url = url.replace(':id', id);
            $('#holiday-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal select[name="year"]').val(data.year)
                        $('#myModal input[name="holiday"]').val(data.holiday)
                        $('#myModal input[name="description"]').val(data.description)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Holiday: " + data.description)
                    } else {
                        console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Holiday Edit - End*/

        $("#statholiday-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("statholiday.single",":id") }}';
            var url = url.replace(':id', id);
            $('#holiday-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#statModal input[name="statid"]').val(data.id)
                        $('#statModal input[name="statholiday"]').val(data.holiday)
                        $("#statModal").modal();
                    } else {
                        console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });

        /* Holiday Delete  - Start */
        $('#holiday-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('holiday.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Holiday has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Holiday Delete  - End */

        /* Holiday Delete  - Start */
        $('#statholiday-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('statholiday.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Holiday has been deleted successfully';
            deleteRecord(url, stattable, message);
        });
        /* Holiday Delete  - End */

    });
    var $select = $('#myModal select[name="year"]');
    $select.append($('<option selected disabled></option>').val('Select').html('Select'));
    for (i = 2017; i <= 2047; i++) {
        $select.append($('<option></option>').val(i).html(i));
    }
</script>
@stop
