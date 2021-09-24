{{-- resources/views/admin/dashboard.blade.php --}}

    @extends('adminlte::page')

        @section('title', 'Customers')

        @section('content_header')
            <h1>Customers</h1>
        @stop

        @section('content')
    <div id="message"></div>
    @if(Session::has('customer-updated'))
        <div id="import-success-alert" class="alert alert-info fade in alert-dismissible" role="alert" style="width:50%;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
            {{ Session::get('customer-updated') }}
        </div>
    @endif
    <div class="row">
    <div class="col-md-4">

            <div class="row">
                <div class="col-md-3"><label class="filter-text">Client Name</label></div>
                <div class="col-md-4 filter">
                    <select class="form-control option-adjust client-filter select2" name="clientname-filter" id="clientname-filter">
                        <option value="">Select Customer</option>
                        @foreach($customerList as $each_customername)
                        <option value="{{ $each_customername->id}}">{{ $each_customername->client_name .' ('.$each_customername->project_number.')' }}
                        </option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>

        </div>
        <div class="col-md-3" id="filter">
            <label style="padding-left: 10px;"><input type="radio" name="customer-contract-type" value="{{ PERMANENT_CUSTOMER }}">&nbsp;Permanent</label>
            <label style="padding-left: 10px;"><input type="radio" name="customer-contract-type" value="{{ STC_CUSTOMER }}">&nbsp;Short Term Contracts</label>
            <label style="padding-left: 10px;"><input type="radio" name="customer-contract-type" value= 'ALL_CUSTOMER' checked>&nbsp;All</label>
        </div>

        <div class="col-md-2" id="filterActive">
            <label style="padding: 0px 10px 0 10px !important; font-weight: normal;"><input type="radio" name="customer-status" value="{{ACTIVE}}" checked>&nbsp;Active</label>
            <label style="padding: 0px 10px 0 10px !important; font-weight: normal;"><input type="radio" name="customer-status" value="{{INACTIVE}}" >&nbsp;Inactive</label>
        </div>
        <div class="col-md-3">
            <a href="{!! route('customer.add') !!}"><div class="add-new" >Add <span class="">New</span></div></a>
        </div>
    </div>

<table class="table table-bordered" id="customer-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Project No</th>
            <th>Client Name</th>
            <th>City</th>
            <th>Client Contact Name</th>
            <th>Client Contact Email</th>
            <th>Client Contact Phone Number</th>
            <th>ML/GF</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
{!! Form::open(array('route' => 'customer.import.process','method'=>'POST','files'=>'true')) !!}
<div class="row">
    <div class="col-xs-9 col-sm-9 col-md-9" style="margin-top:30px;">
        <div class="col-md-12">
            {!! Form::label('import_file','Select File to Import:',['class'=>'col-md-4']) !!}
            {!! Form::file('import_file', array('class' => 'col-md-4', 'accept'=>'.xls,.xlsx')) !!}

            {!! Form::submit('Upload',['class'=>'btn btn-primary col-md-1']) !!}
        </div>
        {!! $errors->first('import_file', '<div class="col-md-12"><label class="col-md-10 align-center">
                <p class="help-block">:message</p>
            </label></div>') !!}
        <div class="col-md-12">
            <a href="{{asset('excel_import_template/CGL360_Customer_Import_Template.xlsx')}}">Customer Import Template Format</a>
        </div>
    </div>
</div>
{!! Form::close() !!}






@include('admin::customer.partials.modal')
@stop

@section('js')
<script>
</script>

@include('admin::customer.partials.scripts')
<style>
    ul li {
        list-style: none;
    }
    .option-adjust {
        display: inline !important;
        width: 300px !important;
    }

    hr {
        border: none;
        height: 10px;
        /* Set the hr color */
        color: #333;
        /* old IE */
        background-color: #333;
        /* Modern Browsers */
    }
    .pac-container {
    z-index: 10000 !important;
}

/* Remove margins and padding from the parent ul */
#tabList {
list-style-type: none;
  margin: 0;
  padding: 0;
}

.fa-toggle-on {
    color: #003A63;
}
.fa-toggle-off {
    color: #003A63;
}

.fa-check {
    color: green;
}

.fa-times {
    color: red;
}



/* Style the caret/arrow */
.caret {
  cursor: pointer;
  border: 0;
  margin: 0;
  display: inline;
  padding: 1rem;
  user-select: none; /* Prevent text selection */
}

.list-group-item {
    border: none;
}

.custom-list-group-item {
    border: none;
    position: relative;
    display: block;
    padding: 18px 15px;
    margin-bottom: -1px;
    background-color: #fff;
}

/* Create the caret/arrow with a unicode, and style it */
.caret::before {
  content: "\25B6";
  color: black;
  display: inline-block;
  margin-right: 6px;
}

/* Rotate the caret/arrow icon when clicked on (using JavaScript) */
.caret-down::before {
  transform: rotate(90deg);
}

/* Hide the nested list */
.nested {
  display: none;
}

/* Show the nested list when the user clicks on the caret/arrow (with JavaScript) */
.active {
  display: block;
}
.tab-alignment{
        margin-left: 15px;
        margin-right: 15px;
    }

    .tab-alignment .control-label {
        padding-left: 0px;
    }

    .gj-icon {
        padding: 32px 5px 0 0;
    }
</style>



@stop
