
<div class="table-responsive" id="tracking-table">
    <table class="table table-bordered dataTable">
        <thead>
        <th>No</th>
        <th>Process Step</th>
        <th>Completion Date</th>
        <th>Notes</th>
        <th>Entered By</th>
        </thead>
        <tbody>
            @foreach($lookups as $lookup)
            <tr>
                <td>
                    {{$lookup->id}}
                </td>
                <td>
                    {{$lookup->process_steps}}
                </td>
                @if(in_array($lookup->id,array_keys($already_processed_track_ids)))
                <td>
                    {{ Form::label('completion_date['.$lookup->id.']', $already_processed_track_ids[$lookup->id]->completion_date) }}
                </td>
                <td>
                    {{ Form::label('notes['.$lookup->id.']',$already_processed_track_ids[$lookup->id]->notes) }}
                </td>
                <td>
                    {!! ($already_processed_track_ids[$lookup->id]->entered_by!=null)?$already_processed_track_ids[$lookup->id]->entered_by->full_name:'<i>User Removed</i>' !!}
                </td>
                @else
                <td>
                    {{ Form::text('completion_date['.$lookup->id.']',old('completion_date['.$lookup->id.']'),array('class' => 'datepicker form-control')) }}
                </td>
                <td>
                    {{ Form::textArea('notes['.$lookup->id.']',old('notes['.$lookup->id.']'),array('placeholder'=>"Notes",'cols'=>'30','rows'=>1,'class' => 'form-control')) }}
                </td>
                <td>

                    {{ Form::select('entered_by_id['.$lookup->id.']', [null=>'Please Select']+$users, null,array('class' => 'form-control')) }}
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
