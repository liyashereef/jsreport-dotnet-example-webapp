@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')


<div class="row  col-sm-12 col-md-12 col-lg-12" style="padding-bottom: 0px;padding-right: 0px;">
<div class="table_title col-sm-12 col-md-12 col-lg-11">
<h4>Visitor Log Details</h4>
</div>
<div style="padding-right: 0px;" class="table_title col-sm-12 col-md-12 col-lg-1">
<button id="filter-btn"  class="filter-btn"><i class="fas fa-filter" ></i></button>
</div>

</div>

<div id="message"></div>
<div id="type-filter" style="display: none;" class="col-md-10 row">
<div class="col-md-6 dropdown-adjust">
    <div class="dropdown-alignment row" id="customer-type-div">
        <label class="col-md-4">Visitor Type</label>
        <div class="col-md-6">
            <select class="form-control option-adjust" id="visitor-type">
                <option value="0">All Visitor Types</option>
                @foreach($visitor_type as $typeid => $eachtype )
                <option value="{{$typeid}}" @if($typeid == $visitor_type_id) selected @endif >{{$eachtype}}</option>
                @endforeach
            </select>

        </div>
    </div>
</div>
<div class="col-md-6 dropdown-adjust">
    <div class="dropdown-alignment row" id="feedback-filter-div">
        <label class="col-md-4">Select Customer</label>
        <div class="col-md-6">
            <select class="form-control option-adjust select2" id="customer">
                @foreach($project_list as $projectid => $each_project)
                <option value="{{$projectid}}" @if($projectid == $customer_id) selected @endif >{{$each_project}}</option>
                @endforeach
            </select>

        </div>
    </div>
</div>
</div>


 <div>
<div id="date-filter" style="display: none;" class="col-md-10 row">
    <div class="form-group col-md-6">
    <div class="dropdown-alignment row" id="feedback-filter-div">
    <label class="col-md-4">From Date</label>
    <div class="col-md-6">
        <input type="text" name="from_date" id="from_date" class="form-control datepicker" placeholder="From Date" value="{{ \Carbon::now()->format('Y-m-d')}}" readonly > </div>
    </div>
    </div>

    <div class="form-group col-md-6">
    <div class="dropdown-alignment row" id="feedback-filter-div">
    <label class="col-md-4">To Date</label>
    <div class="col-md-6">
   <input type="text" name="to_date" id="to_date" class="form-control datepicker" value="{{ \Carbon::now()->format('Y-m-d')}}" placeholder="To Date" readonly>
    </div>
     <div class="form-group col-md-2">    <input type="button" class="submit-filter" value="Filter" id="loadDataTable"    />  </div>
    </div>
    </div>

</div>


</div>


 <table class="table table-bordered" id="table-id">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Date</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Type</th>
            <th>Company</th>
            <th>Person to Visit</th>
        </tr>
    </thead>
</table>
  <div id="mydiv">

  </div>
@stop
@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>
    $(function () {
        is_export = <?php echo isset($_GET['export-v']) ? $_GET['export-v'] : 0 ?>;
        dom = 'lfrtip';
        lengthMenu = [[10, 25, 50, 100,],[10, 25, 50, 100,]];
            if(is_export == 1) {
                dom = 'lfrBtip';
                lengthMenu = [[10, 25, 50, 100, 500, -1],[10, 25, 50, 100, 500, "All"]];
            }
     if(localStorage.getItem("visitor-details-filter")==true){
        alert("ins1");
      $('#date-filter, #type-filter').show();
     }
      $('.select2').select2();
     var visitor_type = $('#visitor-type').val();
     var customer_id =  $('#customer').val();
     var url = "{{ route('visitor-log.list',[':type',':customer']) }}";
     url = url.replace(':type', visitor_type);
     url = url.replace(':customer', customer_id);

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#table-id').DataTable({
                bProcessing: false,
                responsive: true,
                dom: dom,
                processing: true,
                serverSide: true,
                fixedHeader: true,
                bFilter: true,
                ajax: url,
                order: [[0, 'desc']],
                lengthMenu: lengthMenu,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                ],
                columns: [
                {
                    data: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false
                },
                {data: null,
                    render:function(o)
                    {
                  var actions='';
                 actions+="<a href='#' class='details_load' data-id='" + o.id +"' >"+o.full_name+"</a>";
                 return actions;
                  }
                },
                {data: 'date', name: 'date'},
                {data: 'checkin', name: 'checkin'},
                {data: 'checkout', name: 'checkout'},
                {data: 'visitor_type', name: 'visitor_type'},
                {data: 'name_of_company',  name:'name_of_company'},
                {data: 'whom_to_visit', name: 'whom_to_visit'},

                ]
            });
        } catch(e){
            console.log(e);
        }
    });



        $('#table-id').on('click', '.details_load', function(e){
        var id=$(this).data('id');
        var url= '{{route("visitor-log.view",":id")}}';
        var url = url.replace(":id", id);
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:url,
                type: 'GET',
                success: function (data) {
                    console.log(data)
                    if (data.success) {
                        $("#viewModal").html('');
                        $("#mydiv").html(data.content);
                        $("#viewModal").modal('show');
                        // swal("Saved", "The visitor has been checkout", "success");
                    } else {
                        console.log('error in else',data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
});
 $("#loadDataTable").on('click',function(){

      var visitor_type = $('#visitor-type').val();
      var customer_id =  $('#customer').val();
      var from_date = $('#from_date').val();
      var to_date =  $('#to_date').val();
     var url = "{{ route('visitor-log.list',[':type',':customer',':from',':to']) }}";
     url = url.replace(':type', visitor_type);
     url = url.replace(':customer', customer_id);
     url = url.replace(':from', from_date);
     url = url.replace(':to', to_date);

       table.ajax.url( url ).load();

 });

 $('#filter-btn').on('click', function(e){
   $('#date-filter, #type-filter').toggle();
 //  $('#type-filter').toggle();
   if($("#type-filter:visible").length > 0)
   {
     localStorage.setItem("visitor-details-filter", "true");
   }
   else
   {
     localStorage.setItem("visitor-details-filter", "false");
   }
  // $(this).toggleClass('filter-btn-clicked');
});

</script>
@stop
