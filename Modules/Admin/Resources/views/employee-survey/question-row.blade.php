
<input type="hidden" name="length[]" class="cls-length-id" value="{{$template_form['length'] ?? "1"}}"/>
    <td aria-controls="position-table" class="cls-slno">@if(isset($key)) {{ $key+1}} @else 1 @endif
        </td>
        <input type="hidden" class="quest_id" name="question_id[]" value="{{$template_form['id'] ?? ""}}"/>
  

    <td aria-controls="position-table" style="width:500px;" class="cls-question-text">
        <div class="form-group question_text" id="question_text_{{isset($key)?($key):"0"}}">
            <textarea name="question_text[]" id="" cols="50" rows="4" placeholder="Question" required  @if(isset($is_view)) readonly @endif>{{$template_form['question'] ?? ''}}</textarea>
        <span class="help-block"></span>
        </div>
    </td>

    <td>
        <div class="form-group dropdown answer_type" id="answer_type_{{isset($key)?($key):"0"}}">
            <select class="form-control cls-answer-type" @if(isset($is_view)) readonly @endif name="answer_type[]" required>
                <option value="">Choose one</option>>
                <option  value="1" @if(isset($template_form['answer_type']) && $template_form['answer_type'] ==  "1") selected @endif>Yes/No</option>
               <option  value="2" @if(isset($template_form['answer_type']) && $template_form['answer_type'] ==  "2") selected @endif>Rating</option>
            </select>
            <span class="help-block"></span>
        </div>
    </td>

    <td aria-controls="position-table" style="width:500px;" class="cls-sequence-text" align="center">
        <div class="form-group sequence" id="sequence_{{isset($key)?($key):"0"}}">
            <input type="number" min="1" style="width: 68px;align-content: center;" name="sequence[]" class="form-control order" value="{{$template_form['sequence'] ??  ""}}" @if(isset($is_view)) readonly @endif>
        <span class="help-block"></span>
        </div>
    </td>

    <td class="sorting_disabled">
        <div class="input-group">
            <span>
                <a title="Remove question" href="javascript:;" class="remove_button"  @if(!isset($is_view))  onclick="questionsObj.removeQuestion(this)"  @else style="pointer-events:none;" @endif>
                    <i class="fa fa-minus" aria-hidden="true" @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                </a>
            </span>

            <span>
                <a title="Add another question" href="javascript:;" class="add_button margin-left-table-btn" @if(!isset($is_view))  onclick="questionsObj.addQuestion(this)"  @else style="pointer-events:none;" @endif>
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
            </span>
        </div>
    </td>
