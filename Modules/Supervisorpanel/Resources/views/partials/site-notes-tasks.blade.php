<tr 
    class="site-note-tasks" 
    data-task-id="@if(isset($each_note_task)) {{$each_note_task->id}} @else 0 @endif"     
    >
    <td style="width: 40%;">
        @isset($each_note_task)<span class="site-note-task-subject-span view-form-element scroll-element">{{$each_note_task->task_name}}</span>@endisset
        <input class="site-note-task-subject form-control" @isset($each_note_task) value="{{$each_note_task->task_name or '' }}" disabled  style="display:none"@endisset>
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12" id="note-data-task_list-@if(isset($index)){{$index}}@else{{0}}@endif-task_subject"></span></div>

    </td>
    <td style="width: 30%;">
        <select class="site-note-user form-control" @if(isset($each_note_task)) disabled @endif>
            <option value=""> Select </option>
            @foreach($allocated_users as $user)
            <option value="{{$user->id}}" @if(isset($each_note_task) && $user->id == $each_note_task->assigned_to) selected @endif>{{$user->full_name}} </option>
            @endforeach
        </select>
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12" id="note-data-task_list-@if(isset($index)){{$index}}@else{{0}}@endif-assignee"></span></div>
    </td>
    <td>
        <input type="text" class="site-note-due-date form-control date-input @if(!isset($each_note_task)) datepicker @endif" @isset($each_note_task)value="{{$each_note_task->due_date or '' }}" disabled @endisset>
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12" id="note-data-task_list-@if(isset($index)){{$index}}@else{{0}}@endif-due_date"></span></div>
    </td>
    <td style="width: 15%;">
        <select class="site-note-task-status form-control">
            <option value=""> Select </option>
            @foreach($site_note_status as $id => $status)
            <option value="{{$id}}" @if(isset($each_note_task) && $id == $each_note_task->status_id) selected @endif>{{$status}}</option>
            @endforeach
        </select>
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12" id="note-data-task_list-@if(isset($index)){{$index}}@else{{0}}@endif-task_status"></span></div>
    </td>
</tr>
