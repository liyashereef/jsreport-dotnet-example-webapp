@extends('adminlte::page')

@section('title', 'Uniform Items')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3>Uniform Items</h3>
@stop

@section('content')
<div class="container-fluid container-wrap">
    {{ Form::open(array('route'=> 'recruitment.uniform-items.store','id'=>'uniform-item-add-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        <section>
            <div class="form-group row" id="item_name">
                <input type="hidden" name="id" value="{{@$itemName[0]->id}}"/>
                <label class="col-form-label col-md-2" for="item_name">Item Name <span class="mandatory">*</span></label>
                <div class=" col-md-4">
                    <input type="text" class="form-control" placeholder="Item Name" name="item_name" value="{{@$itemName[0]->item_name}}" >
                    <span class="help-block"></span>
                </div>
            </div>

            <div class="form-group row" id="measuring_points">
                <label for="measuring_points" class="col-form-label col-md-2">Measuring Points<span class="mandatory">*</span></label>
                <div class="col-md-4">
                    <select name="measuring_points[]" id="measuringPointId" class="form-control select2" multiple="multiple">
                        @foreach($measuringPoints as $key => $value)
                            <option value="{{$key}}" @if(in_array($key,$measuringIdArr)) selected @endif>{{$value}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>

            <h4 class="color-template-title">Add Size</h4>
            <div class="table-responsive">
                <table id="myTable" style="text-align: center;" class="table table-bordered" role="grid" aria-describedby="position-table_info" >
                    <thead>
                        <tr>
                            <th class="sorting_disabled">Size</th>
                            <th class="sorting_disabled">Measurement Point</th>
                            <th class="sorting_disabled">Min</th>
                            <th class="sorting_disabled">Max</th>
                            <th class="sorting_disabled">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($edit)
                        @php ($min_key=0)
                        @php ($max_key=0)
                        @php ($sizecount=0)
                        @foreach($itemMapping as $sizekey=>$each_item)
                        <tr role="row" class="row-1">
                            <td class="size_type">
                              <div class="form-group" id="size_{{ $sizecount}}">
                                <select class="form-control size_row" name=size[]>
                                    <option  value="0">Please Select</option>
                                    @foreach ($sizes as $size=>$sizeName)
                                        <option value="{{$sizeName->id}}"  @if( $sizekey==$sizeName->id) selected @endif>{{ $sizeName->size_name}}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                             </div>
                            </td>

                            <td class="measure_type">
                                @foreach($each_item as $eachitem)
                                <div class="form-group measure_type_{{$eachitem['uniform_measurement_point']['name'] }}">
                                    <input type="text" class="form-control"  name="measure[]" value="{{ $eachitem['uniform_measurement_point']['name']}}" disabled>
                                    <span class="help-block"></span>
                                </div>
                                @endforeach
                            </td>
                             
                            <td class="min">
                                @foreach($each_item as $eachitem)
                                <div class="form-group min_type_{{$eachitem['uniform_measurement_point']['name'] }}" id="min_{{ $min_key}}">
                                    <input class="form-control" type="number" class="min" name="min[]" value="{{ $eachitem['min']}}">
                                    <span class="help-block"></span>
                                </div>
                                 @php ($min_key++)
                                @endforeach
                            </td>
                            <td class="max">
                                @foreach($each_item as $eachitem)
                                <div class="form-group max_type_{{$eachitem['uniform_measurement_point']['name'] }}" id="max_{{ $max_key}}">
                                    <input class="form-control" type="number" class="max" name="max[]" value="{{ $eachitem['max']}}">
                                    <span class="help-block"></span>
                                </div>
                                 @php ($max_key++)
                                @endforeach
                            </td>
                           
                            <td class="addOrRemove">
                                @if($sizecount!=0)
                                 <a title="Remove size" href="javascript:;" class="remove_button" onclick="addSizeObject.removeSizeRow(this)"> <i class="fa fa-minus" aria-hidden="true"></i>
                                @endif
                                <a title="Add another size" href="javascript:;" class="add_button margin-left-table-btn" onclick="addSizeObject.addNewSizeRow(this)">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </td>
                        </tr>
                        @php ($sizecount++)
                        @endforeach
                        @else
                        <tr role="row" class="row-1">
                            <td class="size_type">
                              <div class="form-group" id="size_0">
                                <select class="form-control size_row" name=size[]>
                                    <option  value="0">Please Select</option>
                                    @foreach ($sizes as $size=>$sizeName)
                                        <option value="{{$sizeName->id}}">{{ $sizeName->size_name}}</option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                             </div>
                            </td>
                            <td class="measure_type">
                                <div class="form-group">
                                    <input type="text" class="form-control measure_type" name="measure[]" disabled>
                                    <span class="help-block"></span>
                                </div>
                            </td>
                            <td class="min">
                                <div class="form-group" id="min_0">
                                    <input class="form-control" type="number" class="min" name="min[]">
                                    <span class="help-block"></span>
                                </div>
                            </td>
                            <td class="max">
                                <div class="form-group" id="max_0">
                                    <input class="form-control" type="number" class="max" name="max[]">
                                    <span class="help-block"></span>
                                </div>
                            </td>
                            <td class="addOrRemove">
                                <a title="Add another size" href="javascript:;" class="add_button margin-left-table-btn" onclick="addSizeObject.addNewSizeRow(this)">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
                <a href="{{ route('recruitment.uniform-items') }}" class="btn btn-primary blue">Cancel</a>
            </div>
        </section>
    {!! Form::close() !!}
</div>
@stop

@section('js')
    <script>
        $('.select2').select2();

        const addSizeObject = {
            addSizeRowHtml: "",
            rowCount: 1,
            measuringPointCount: $('#measuringPointId').val().length,  
            mesuringPointArray: [],
            measuringPoints: {!! json_encode($measuringPoints) !!},
            startLoading() {
                $('body').loading({
                    stoppable: false,
                    message: 'Please wait...'
                });
            },
            getAddSizeRow() {
                var htmlText = '<tr role="row">'+$('#myTable tbody tr:first').html()+'</tr>';
                return htmlText;
            },
            endLoading() {
                $('body').loading('stop');
            },
            init() {
            //Event listeners
            this.registerEventListeners();
            },
            registerEventListeners() {
                let root = this;
                
                 
                   // $('#measuringPointId').change(function(e) {
                 $('#measuringPointId').on('select2:unselect select2:select', function (e) {
                    if(root.measuringPointCount < $('#measuringPointId').val().length) {
                         let measuringPointTobeAdded = e.params.data.id;
                       
                        if(root.measuringPointCount == 0) {          
                        $('.measure_type').empty();
                        $('.measure_type').append(`<div class="form-group measure_type_${root.measuringPoints[$('#measuringPointId').val()]}"> <input type="text" class="form-control" name="measure[]" value="${root.measuringPoints[$('#measuringPointId').val()]}" disabled><span class="help-block"></span></div>`);
                        $('.min div').addClass(`min_type_${root.measuringPoints[$('#measuringPointId').val()]}`);
                        $('.max div').addClass(`max_type_${root.measuringPoints[$('#measuringPointId').val()]}`);
                       } else {
                        
                        $('.measure_type').append(`<div class="form-group measure_type_${root.measuringPoints[measuringPointTobeAdded]}"> <input type="text" class="form-control" name="measure[]" value="${root.measuringPoints[measuringPointTobeAdded]}" disabled>   <span class="help-block"></span></div>`);
                        $('.min').append(` <div class="form-group min_type_${root.measuringPoints[measuringPointTobeAdded]}"> <input class="form-control" type="number" class="min" name="min[]"><span class="help-block"></span> </div>`);
                        $('.max').append(`<div class="form-group max_type_${root.measuringPoints[measuringPointTobeAdded]}"> <input class="form-control" type="number" class="max" name="max[]"><span class="help-block"></span></div>`);
                       
                       }
                    } else {
                         let measuringPointTobeRemoved = e.params.data.id;
                       
                        if(root.measuringPointCount == 1) {
                            $('#myTable').find('tbody').empty();
                            $('#myTable tbody').append(`<tr> <td class="size_type"> <div class="form-group size_row" id="size_0"> <select class="form-control "  name=size[]> <option value="0">Please Select</option> @foreach ($sizes as $size=>$sizeName) <option value="{{$sizeName->id}}">{{ $sizeName->size_name}}</option> @endforeach </select> <span class="help-block"></span> </div></td> <td class="measure_type"> <div class="form-group"> <input type="text" class="form-control measure_type" name="measure[]" disabled>  <span class="help-block"></span> </div> </td> <td class="min" id="min_0"> <div class="form-group"> <input class="form-control" type="number" class="min" name="min[]">   <span class="help-block"></span></div> </td> <td class="max"> <div class="form-group" id="max_0"> <input class="form-control" type="number" class="max" name="max[]">  <span class="help-block"></span> </div> </td> <td> <a title="Add another size" href="javascript:;" class="add_button margin-left-table-btn" onclick="addSizeObject.addNewSizeRow(this)"> <i class="fa fa-plus" aria-hidden="true"></i> </td> </tr>`);
                        } else {
                            $('.measure_type_' + root.measuringPoints[measuringPointTobeRemoved]).remove();
                            $('.min_type_'+ root.measuringPoints[measuringPointTobeRemoved]).remove();
                            $('.max_type_'+ root.measuringPoints[measuringPointTobeRemoved]).remove();
                        }
                    }
                    positionReIndexing();
                    root.mesuringPointArray = $('#measuringPointId').val();
                    root.measuringPointCount = $('#measuringPointId').val().length;
                    root.addSizeRowHtml = addSizeObject.getAddSizeRow();
                });
            },
            addNewSizeRow() {
                let root = this;
                root.rowCount++;
                position=$('.size_type').length;       
                $('#myTable tbody').append(root.addSizeRowHtml); 
                $('#myTable tbody tr:last').attr('class', 'row-'+ root.rowCount);
                $('#myTable tbody tr:last .addOrRemove').empty().append(` <a title="Remove size" href="javascript:;" class="remove_button" onclick="addSizeObject.removeSizeRow(this)"> <i class="fa fa-minus" aria-hidden="true"></i><a title="Add another size" href="javascript:;" class="add_button margin-left-table-btn" onclick="addSizeObject.addNewSizeRow(this)"> <i class="fa fa-plus" aria-hidden="true"></i>`);
                $('#myTable tr.row-'+root.rowCount).find('input[type="number"]').val('');
                $('#myTable tr.row-'+root.rowCount).find('select').val(0);
                $('#myTable tbody tr:last td.size_type').find('div').attr("id", "size_"+position);
                positionReIndexing();
               
            },
           
            removeSizeRow(currObj) {      
               prev_tr_count=$(currObj).closest('tr').prevAll().length;
               minsize=$(currObj).closest('tr').prevAll().find('.min div').length;
               console.log(minsize)   
               maxsize=$(currObj).closest('tr').prevAll().find('.max div').length;   
                $(currObj).closest('tr').nextAll().each(function( index,value ) {
                 $(value).attr("class", 'row-'+prev_tr_count);
                 $(value).attr("data-row", prev_tr_count);
                 $(value).find('td.size_type div').attr("id", 'size_'+prev_tr_count);
                 prev_tr_count++;
              });
                $(currObj).closest('tr').remove();
                positionReIndexing();
            }
        }

        addSizeObject.startLoading();
        addSizeObject.addSizeRowHtml = addSizeObject.getAddSizeRow();
        addSizeObject.endLoading();

        $(function() {
            addSizeObject.init();
        })

        $('#uniform-item-add-form').submit(function (e) {
            e.preventDefault();
            $('.field_type').prop('disabled', false);
            var $form = $(this);
            var form = $('#uniform-item-add-form');
            url = "{{ route('recruitment.uniform-items.store') }}";
            var formData = new FormData($('#uniform-item-add-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data)  {
                    if (data.success) {
                        if(data.result == false){
                            result = "Uniform Item has been updated successfully";
                        }else{
                            result = "Uniform Item has been created successfully";
                        }
                        swal({
                            title: "Saved",
                            text: result,
                            type: "success",
                            confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            window.location.href = "{{ route('recruitment.uniform-items') }}";
                        });
                    }
                },
                fail: function (response) {
                    console.log(data);
                },
                error: function (xhr, textStatus, thrownError) {
                $('.field_type').prop('disabled', true);
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        });

    function positionReIndexing()
    {
        let minsize=0;
        let maxsize=0;
       $('.min div').each(function( index,minvalue ) {
            $(minvalue).attr("id", 'min_'+minsize);
             minsize++;
       }); 
       $('.max div').each(function( index,maxvalue ) {
            $(maxvalue).attr("id", 'max_'+maxsize);
             maxsize++;
       });  
    }
    </script>
@stop
