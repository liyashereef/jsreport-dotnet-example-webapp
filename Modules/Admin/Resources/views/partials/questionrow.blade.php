<input type="hidden" name="element_id[]" class="cls-element-id" value="{{$template_form['id'] or ""}}"/>
<input type="hidden" name="length[]" class="cls-length-id" value="{{$template_form['length'] or "1"}}"/>
    <td aria-controls="position-table" class="cls-slno">{{$template_form['position'] or "1"}}<input type="hidden" name="position[]" value="{{$template_form['position'] or "1"}}"/></td>
    <td aria-controls="position-table" class="cls-question-type">
        <div class="form-group question_type dropdown" id="question_type_{{isset($key)?($key):"0"}}">
            <select class="form-control" name="question_type[]" required @if(isset($template_form['show_if_yes']) || isset($template_form['parent_position']) ) readonly @endif>
                @foreach ($question_categories as $category)
                <option
                    id="{{ $category->id }}"
                    value="{{ $category->id }}"
                    @if(isset($template_form['question_category_id']) && $template_form['question_category_id'] == $category->id) selected
                    @elseif(isset($template_form['show_if_yes']) || isset($template_form['parent_position'])) style="display: none;"
                    @endif>{{ $category->description}}</option>
                @endforeach
            </select>
            <span class="help-block"></span>
        </div>
    </td>

    <td aria-controls="position-table" class="cls-parent-question">
        <div class="form-group parent_question dropdown" id="parent_question_{{isset($key)?($key):"0"}}">
            <select class="form-control cls-parent-question-select" name="parent_question[]" id="" data-prev="{{$template_form['parent_position'] or ""}}" required @if(isset($template_form['show_if_yes']) ) readonly @endif>
                <option>NA</option>
                @if(isset($template_position_arr))
                    @for($i = 0; $template_position_arr[$i]<$template_form['position']; $i++)
                        @isset($template_position_arr[$i])
                        <option id="{{$template_position_arr[$i] or ''}}"
                                value="{{$template_position_arr[$i] or ''}}"
                                @if($template_position_arr[$i] == $template_form['parent_position']) selected @elseif(isset($template_form['show_if_yes']) ) style="display: none;" @endif
                                >{{$template_position_arr[$i] or ''}}</option>
                        @endisset
                    @endfor
                @endif
            </select>
            <span class="help-block"></span>
        </div>
    </td>

    <td aria-controls="position-table" style="width:500px;" class="cls-question-text">
        <div class="form-group question_text" id="question_text_{{isset($key)?($key):"0"}}">
            <textarea name="question_text[]" id="" cols="50" rows="4" placeholder="Question" required @if(isset($template_form['answer_type_id']) && $template_form['answer_type_id'] == 4) readonly @endif>{{$template_form['question_text'] or ''}}</textarea>
        <span class="help-block"></span>
        </div>
    </td>

    <td>
        <div class="form-group dropdown" id="answer_type_{{isset($key)?($key):"0"}}">
            <select class="form-control answer_type cls-answer-type" name="answer_type[]" required>
                @foreach ($answer_type as $each_type)
                    @if( $each_type->id == 1)
                    <option id="{{ $each_type->id }}" value="{{ $each_type->id }}" @if(isset($template_form['answer_type_id']) && $template_form['answer_type_id'] !=  1) style="display: none;" @endif class="parent-question-option" @if(isset($template_form['answer_type_id']) && $template_form['answer_type_id'] ==  $each_type->id) selected @endif>{{ $each_type->answer_type}}</option>
                    @else
                    <option id="{{ $each_type->id }}" value="{{ $each_type->id }}" @if(!isset($template_form['answer_type_id']))  style="display: none;" @endif @if(isset($template_form['answer_type_id']) && $template_form['answer_type_id'] ==  1) style="display: none;" @endif  class="child-question-option" @if(isset($template_form['answer_type_id']) && $template_form['answer_type_id'] ==  $each_type->id) selected @endif>{{ $each_type->answer_type}}</option>
                    @endif
                @endforeach
            </select>
            <span class="help-block"></span>
        </div>
    </td>

    <td class="sorting_disabled">
            <input type="checkbox" class="custom-control-input cls-multiple-answers" name="multiple_answers[{{$template_form['position'] or "1"}}]" @if(isset($template_form['multi_answer']) && $template_form['multi_answer'] ==  1) checked="checked" @endif @if(!isset($template_form['show_if_yes'])) style="display: none;" @endif>
    </td>

    <td class="sorting_disabled">
        <div class=" cls-show-if"
        @if(!isset($template_form['show_if_yes'])) style="display: none;" @endif>
            <label class="custom-control custom-radio" >
                 <div class="form-group">
                    <input name="show_if[{{$template_form['position'] or "1"}}]" type="radio" class="custom-control-input" value="1" @if(isset($template_form['show_if_yes']) && $template_form['show_if_yes'] == 1) checked="checked" @endif >
                    <span class="custom-control-description">Yes</span>
                    <span class="help-block"></span>
                   </div>
            </label>


            <label class="custom-control custom-radio ">
                <div class="form-group">
                    <input name="show_if[{{$template_form['position'] or "1"}}]" type="radio" class="custom-control-input" value="0" @if(isset($template_form['show_if_yes']) && $template_form['show_if_yes'] != 1) checked="checked" @endif>
                    <span class="custom-control-description">No</span>
                     <span class="help-block"></span>
                </div>
            </label>
         </div>
    </td>


     <td class="sorting_disabled">
        <div class="cls-score" @if(isset($template_form['answer_type_id']) && $template_form['answer_type_id'] !=  1) style="display: none;" @endif>
            <label class="custom-control custom-radio cls-yes-value">
             <div class="form-group yes_value" id="yes_value_{{isset($key)?($key):"0"}}">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Yes</span>
                <span><input type="text" name="yes_value[]"  min=0 class="width-small-adjust" value="{{$template_form['score_yes'] or ""}}" @if(!isset($template_form['answer_type_id'])||(isset($template_form['answer_type_id']) && $template_form['answer_type_id'] ==  1)) required @endif></span>
                 <span class="help-block"></span>
             </div>
            </label>


            <label class="custom-control custom-radio cls-no-value">
                 <div class="form-group no_value" id="no_value_{{isset($key)?($key):"0"}}">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">No</span>
                <span><input type="text" name="no_value[]" min=0 class="width-small-adjust" value="{{$template_form['score_no'] or ""}}" @if(!isset($template_form['answer_type_id'])||(isset($template_form['answer_type_id']) && $template_form['answer_type_id'] ==  1)) required @endif></span>
                 <span class="help-block"></span>
                    </div>
            </label>
         </div>
    </td>

    <td class="sorting_disabled">
        <div class="input-group">
            <span>
                <a title="Remove question" href="javascript:;" class="remove_button" onclick="questionsObj.removeQuestion(this)">
                    <i class="fa fa-minus" aria-hidden="true" @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                </a>
            </span>

            <span>
                <a title="Add another question" href="javascript:;" class="add_button margin-left-table-btn" onclick="questionsObj.addQuestion(this)">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
            </span>
        </div>
    </td>
