        <div class="col-sm-6 col-md-6 employeeListHtmlBox">
<!--            <input type="hidden" name="question_{{$name}}" value="{{$question_text}}">-->   
            @if(auth()->user()->can('edit-survey') || (auth()->user()->can('submit-survey') && (!$completed)))
                <select 
                @if(isset($answer) ) 
                attr-answer="{{$answer}}"
                @else 
                attr-answer=""
                @endif
                name="{{$name}}" class="select2-employee-list form-control emplist" tabindex="-1">
                    {{-- <option value="" selected="selected">Please Select</option>
                    @foreach($employee_list as $each_employee_id => $each_empolyee)
                    <option
                        value="{{trim($each_employee_id)}}"
                        @if(isset($answer) && ((trim($each_employee_id) == $answer) ||(trim($each_empolyee) == $answer))) selected @endif>{{$each_empolyee}}</option>
                    @endforeach --}}
                </select>
            @else
                @foreach($employee_list as $each_employee_id => $each_empolyee)
                    @if(isset($answer) && ((trim($each_employee_id) == $answer) ||(trim($each_empolyee) == $answer))) 
                    <span class="view-form-element">{{$each_empolyee}}</span>
                    @endif
                @endforeach            
            @endif            
        </div>
