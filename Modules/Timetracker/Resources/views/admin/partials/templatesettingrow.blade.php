<tr>
    <td class="sl-no">{{isset($key)?($key+1):"1"}}
        <input type="hidden" class="row-no" name="position[]" value="{{isset($key)?($key):"0"}}"/></td>
    <td>
        <div class="form-group color" id="rule_color_{{isset($key)?($key):"0"}}">
        <select  class="form-control width-adjust-auto display-inline" class="clr-dropdown" name="rule_color[]">
            <option value=""  class="clr-item">Choose One</option>
            @foreach($availableColors as $acKey => $color)
            <option value="{{$color}}"  
            class="clr-item" @if(isset($each_rule['color']) && $each_rule['color'] == $color) selected @endif
            >{{ucfirst($color)}}</option>
            @endforeach
        </select>
        <span class="help-block"></span>
        </div>
    </td>
    <td><div class="form-group min" id="min_value_{{isset($key)?($key):"0"}}">
        <input 
            type="text"
            class="min-item form-control  table-option-adjust" 
            name="min_value[]" 
            pattern="^\d{1,3}(\.\d{2})?$" 
            placeholder="00.00" 
            value="{{$each_rule['min'] ?? ''}}"/>
        <span class="help-block"></span>
        </div>
    </td>
    <td><div class="form-group max" id="max_value_{{isset($key)?($key):"0"}}">
        <input type="text" 
            class="max-item form-control  table-option-adjust" 
            name="max_value[]" 
            pattern="^\d{1,3}(\.\d{2})?$"
            placeholder="00.00" 
            value="{{$each_rule['max'] ?? ''}}"/>
        <span class="help-block"></span>
        </div>
    </td>
</tr>
