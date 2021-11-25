@extends('adminlte::page')
@section('title', 'Vehicle List')
@section('content_header')
<h1>Vehicle Lists</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Vehicle">Add
    <span class="add-new-label">New</span>
</div>

<table class="table table-bordered" id="vehicle-list-table" style="width:100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Vehicle Make</th>
            <th>Registration</th>
            <th>Vehicle Model</th>
            <th>VIN</th>
            <th>Manufacturing Year</th>
             <th>Purchase Date</th>
             <th>Odometer Reading</th>
             <th>Region</th>
              <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Vehicle Lists</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'vehicle-list-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                  <!-- Active Toggle button - Start -->
                <div class="form-group  col-lg-12 row" id="active" >
                    <label class="switch" style="float:right;">
                      <input name="active" value="0" type="checkbox">
                      <span class="slider round"></span>
                    </label>
                    <label style="float:right;padding-right: 5px;">Active</label>
                </div>
                <!-- Active Toggle button - End -->
                
                <div class="form-group row" id="make">
                    <label for="make" class="col-sm-3 control-label">Vehicle Make<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('make',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div> 
                
                <div class="form-group row" id="number">
                    <label for="number" class="col-sm-3 control-label">Registration<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('number',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>  

                <div class="form-group row" id="model">
                    <label for="number" class="col-sm-3 control-label">Vehicle Model<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('model',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group row" id="vin">
                    <label for="text" class="col-sm-3 control-label">VIN<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('vin',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                 <div class="form-group row" id="year">
                    <label for="year" class="col-sm-3 control-label">Manufacturing Year<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('year',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div> 

                 <div class="form-group row" id="purchasing_date">
                    <label for="purchased_date" class="col-sm-3 control-label">Purchase Date<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('purchasing_date',null,array('class'=>'form-control datepicker')) }}
                        <small class="help-block"></small>
                    </div>
                </div> 
 

                 <div class="form-group row" id="odometer_reading">
                    <label for="number" class="col-sm-3 control-label">Odometer Reading<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('odometer_reading',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div> 

                <div class="form-group row" id="region">
                    <label for="region" class="col-sm-3 control-label">Region<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                          {{ Form::select('region',[null=>'Select']+$lookups['regionLookup'], old('region'),array('class' => 'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="description">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                          <textarea name="description" class="form-control"></textarea>
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
    $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#vehicle-list-table').DataTable({
            processing: true,
            serverSide: true,
              responsive: true,
             
              dom: 'lfrtBip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    },
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Users');
                    }
                }
            ],
            ajax: "{{ route('vehicle-list.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [1, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false,
                },
                {
                    data: 'make',
                    name: 'make'
                },
                {
                    data: 'number',
                    name: 'number'
                },
                {
                    data: 'model',
                    name: 'model',
                     defaultContent: '--'

                },
                  {
                    data: 'vin',
                    name: 'vin',
                    defaultContent: '--'
                },
                {
                    data: 'year',
                    name: 'year',
                     defaultContent: '--'
                },
                 {
                    data: 'purchasing_date',
                    name: 'purchasing_date',
                     defaultContent: '--'
                },
                {
                    data: 'odometer_reading',
                    name: 'odometer_reading',
                     defaultContent: '--'
                },
                  {
                    data: 'region_name',
                    name: 'region_name',
                    defaultContent: '--'
                },
                 {
                    data: 'active',
                    name: 'active',
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                       
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                       
                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }    

        $('#vehicle-list-form').submit(function (e) {
            e.preventDefault();
            if($('#vehicle-list-form input[name="id"]').val()){
                var message = 'Vehicle list has been updated successfully';
            }else{
                $('#myModal input[name="active"]').prop('checked', true);
                var message = 'Vehicle has been created successfully';
            }     
            formSubmit($('#vehicle-list-form'), "{{ route('vehicle-list.store') }}", table, e, message);
        });
        


        $("#vehicle-list-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            $('#myModal').find('#active').show();
            var url = '{{ route("vehicle-list.single",":id") }}';
            var url = url.replace(':id', id);
            $('#vehicle-list-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        console.log(data.active)
                        $('#myModal input[name="active"]').prop('checked', data.active);
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="make"]').val(data.make);
                        $('#myModal input[name="number"]').val(data.number);
                        $('#myModal input[name="model"]').val(data.model);
                        $('#myModal input[name="year"]').val(data.year);
                        $('#myModal input[name="vin"]').val(data.vin);
                         $('#myModal textarea[name="description"]').val(data.description);
                        $('#myModal input[name="purchasing_date"]').val(data.purchasing_date);
                          $('#myModal select[name="region"]').val(data.region)
                        $('#myModal input[name="odometer_reading"]').val(parseInt(data.odometer_reading));
                        $("#myModal").modal();
                    } else {
                        alert(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });

        $('#vehicle-list-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('vehicle-list.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Vehicle  has been deleted successfully';
            deleteRecord(url, table, message);
        });
         
         $('.add-new').click(function(){
         $('#myModal input[name="active"]').prop('checked', true);
     });
    }); 
</script>

@stop
