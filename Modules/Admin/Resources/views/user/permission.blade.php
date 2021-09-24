@extends('adminlte::page')
@section('title', 'User permission')
@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h1>User Permissions</h1>
@stop
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body table-responsive">
                <form autocomplete="false" role="form" method="POST" action="{{ route('userpermission.update') }}">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <table id="users" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name & Role</th>
                                <th colspan="4" align="center">Job Module</th>
                                <th colspan="4" align="center">Candidate Module</th>
                                <th colspan="4" align="center">HR Tracking</th>  
                            </tr>
                        </thead>

                        </tbody>
                        <tr>
                            <th></th>
                            <th>Entry</th>
                            <th>List & View</th>
                            <th>Approval</th>
                            <th>Mandatory Attachments settings</th>

                            <th>Entry</th>                            
                            <th>Screening Summary</th>
                            <th>Candidate Summary</th>                       
                            <th>Mapping</th>

                            <th>Job Tracking</th>
                            <th>Candidate Tracking</th>
                            <th>Canidate Approval</th>
                            <th>Canidate Review & Interview Notes</th>
                        </tr>
                        @foreach($users as $each_record)
                        @php
                        $permissions = (array)(json_decode($each_record->permissions));                        
                        @endphp
                        <tr>
                            <td>{{$each_record->full_name}}<br/>{{strtoupper($each_record->role)}}</td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][job_entry]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['job_entry'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][job_list]',[1=>"All",2=>"Self",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['job_list'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][job_approval]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['job_approval'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][job_settings]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['job_settings'] :0),array('class'=>'form-control')) }}
                            </td>                            
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][candidate_entry]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['candidate_entry'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][candidate_candidate-screening-summary]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['candidate_candidate-screening-summary'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][candidate_summary]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['candidate_summary'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][candidate_mapping]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['candidate_mapping'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][hr_jobtracking]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['hr_jobtracking'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][hr_candidatetracking]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['hr_candidatetracking'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][hr_candidateapproval]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['hr_candidateapproval'] :0),array('class'=>'form-control')) }}
                            </td>
                            <td>
                                {{ Form::select('permission['.$each_record->id.'][hr_reviewinterview]',[1=>"Yes",0=>"No Access"],((isset($permissions) && is_array($permissions)) ? $permissions['hr_reviewinterview'] :0),array('class'=>'form-control')) }}
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="13">
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">@lang('Submit')</button>
                                </div> 
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div id="pagination" class="box-footer">
            </div>
        </div>
    </div>
</div>
@stop