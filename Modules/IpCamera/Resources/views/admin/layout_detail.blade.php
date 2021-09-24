<style>
    .li-droppable.active{
        background:#003A63;
        color:#fff;
    }

    /* draggable targets - start */
    [data-draggable="target"]
    {
        float:left;
        list-style-type:none;

        width:42%;
        min-height:7.5em;
        overflow-y:auto;

        margin:0 0.5em 0.5em 0;
        padding:0.5em;

        border:1px solid #003A63;
        border-radius:0.2em;

        color:#555;
    }

    [data-draggable="item"]
    {
        display:block;
        list-style-type:none;

        margin:0 0 2px 0;
        padding:0.2em 0.4em;

        border-radius:0.2em;

        line-height:1.3;
    }

    [data-draggable="item"]:focus
    {
        outline:none;
    }

    [data-draggable="item"][aria-grabbed="true"]
    {
        background:#003A63;
        color:#fff;
    }
    /* draggable - end */
    .module-draggable span.close-li {
        display: none;
    }


    /* close icon li - start */
    .module-droppable span.close-li {
        padding-left: 2%;
        cursor: pointer;
        right: 10%;
        padding-top: 1.1em;
        transform: translate(0%, -50%);
    }

    .module-droppable span.close-li:hover {background: #bbb;}
    /* close icon li - end */
</style>

<div class="col-md-1">
    <label for="modules" style="padding-left: 1.5rem;">Modules</label>
</div>

<div class="col-md-5" style="overflow-y: auto !important;overflow-x: hidden !important;height: 500px !important;">
    <ol class="module-draggable" data-draggable="target" id="module-list-ol" data-class="module-draggable" style="width:100%;heigh:200px;">
        @foreach ($modulesList as $ky => $module)
            @php($id = $module['id'])
            @php($model = $module['model'])
                <li data-draggable="item" class="li-droppable"
                data-widget-name="{{$module['name']}}"
                data-model="{{$module['model']}}"
                data-id="{{$module['id']}}" data-customer-widget="{{$module['api_type']}}"
                onclick="load_field_details(event, '{{$id}}', '{{$model}}')">{{$module['name']}}&nbsp;&nbsp;
                    <span class="close-li" onclick="deleteItemSelected(event);">x</span>
                </li>
        @endforeach
    </ol>
</div>

<div class="col-md-6">
    <div class="container-fluid" id="realImageDiv">
        {!!$html!!}
    </div>
</div>

<div id="hidden_array_section"></div>
<div class="col-md-12" id="fields_div">

</div>

<script>
    /* draggable - start*/
 (function() {
     if (
         !document.querySelectorAll ||
         !('draggable' in document.createElement('span')) ||
         window.opera
     ) {
         return;
     }

     //get the collection of draggable targets and add their draggable attribute
     for (var
             targets = document.querySelectorAll('[data-draggable="target"]'),
             len = targets.length,
             i = 0; i < len; i++) {
         targets[i].setAttribute('aria-dropeffect', 'none');
     }

     //get the collection of draggable items and add their draggable attributes
     for (var
             items = document.querySelectorAll('[data-draggable="item"]'),
             len = items.length,
             i = 0; i < len; i++) {
         items[i].setAttribute('draggable', 'true');
         items[i].setAttribute('aria-grabbed', 'false');
         items[i].setAttribute('tabindex', '0');
     }



     //dictionary for storing the selections data
     //comprising an array of the currently selected items
     //a reference to the selected items' owning container
     //and a refernce to the current drop target container
     var selections = {
         items: [],
         owner: null,
         droptarget: null
     };
     //function for selecting an item
     function addSelection(item) {
         //if the owner reference is still null, set it to this item's parent
         //so that further selection is only allowed within the same container
         if (!selections.owner) {
             selections.owner = item.parentNode;
         }

         //or if that's already happened then compare it with this item's parent
         //and if they're not the same container, return to prevent selection
         else if (selections.owner != item.parentNode) {
             return;
         }

         //set this item's grabbed state
         item.setAttribute('aria-grabbed', 'true');
         //add it to the items array
         selections.items.push(item);
     }

     //function for unselecting an item
     function removeSelection(item) {
         //reset this item's grabbed state
         item.setAttribute('aria-grabbed', 'false');
         //then find and remove this item from the existing items array
         for (var len = selections.items.length, i = 0; i < len; i++) {
             if (selections.items[i] == item) {
                 selections.items.splice(i, 1);
                 break;
             }
         }
     }

     //function for resetting all selections
     function clearSelections() {
         //if we have any selected items
         if (selections.items.length) {
             //reset the owner reference
             selections.owner = null;
             //reset the grabbed state on every selected item
             for (var len = selections.items.length, i = 0; i < len; i++) {
                 selections.items[i].setAttribute('aria-grabbed', 'false');
             }

             //then reset the items array
             selections.items = [];
         }
     }

     //shorctut function for testing whether a selection modifier is pressed
     function hasModifier(e) {
         return (e.ctrlKey || e.metaKey || e.shiftKey);
     }


     //function for applying dropeffect to the target containers
     function addDropeffects() {
         //apply aria-dropeffect and tabindex to all targets apart from the owner
         for (var len = targets.length, i = 0; i < len; i++) {
             if (
                 targets[i] != selections.owner &&
                 targets[i].getAttribute('aria-dropeffect') == 'none'
             ) {
                 targets[i].setAttribute('aria-dropeffect', 'move');
                 targets[i].setAttribute('tabindex', '0');
             }
         }

         //remove aria-grabbed and tabindex from all items inside those containers
         for (var len = items.length, i = 0; i < len; i++) {
             if (
                 items[i].parentNode != selections.owner &&
                 items[i].getAttribute('aria-grabbed')
             ) {
                 items[i].removeAttribute('aria-grabbed');
                 items[i].removeAttribute('tabindex');
             }
         }
     }

     //function for removing dropeffect from the target containers
     function clearDropeffects() {
         //if we have any selected items
         if (selections.items.length) {
             //reset aria-dropeffect and remove tabindex from all targets
             for (var len = targets.length, i = 0; i < len; i++) {
                 if (targets[i].getAttribute('aria-dropeffect') != 'none') {
                     targets[i].setAttribute('aria-dropeffect', 'none');
                     targets[i].removeAttribute('tabindex');
                 }
             }

             //restore aria-grabbed and tabindex to all selectable items
             //without changing the grabbed value of any existing selected items
             for (var len = items.length, i = 0; i < len; i++) {
                 if (!items[i].getAttribute('aria-grabbed')) {
                     items[i].setAttribute('aria-grabbed', 'false');
                     items[i].setAttribute('tabindex', '0');
                 } else if (items[i].getAttribute('aria-grabbed') == 'true') {
                     items[i].setAttribute('tabindex', '0');
                 }
             }
         }
     }

     //shortcut function for identifying an event element's target container
     function getContainer(element) {
         do {
             if (element.nodeType == 1 && element.getAttribute('aria-dropeffect')) {
                 return element;
             }
         }
         while (element = element.parentNode);
         return null;
     }



     //mousedown event to implement single selection
     document.addEventListener('mousedown', function(e) {
         //if the element is a draggable item
         if (e.target.getAttribute('draggable')) {
             //clear dropeffect from the target containers
             clearDropeffects();
             //if the multiple selection modifier is not pressed
             //and the item's grabbed state is currently false
             if (
                 !hasModifier(e)
             ) {
                 //clear all existing selections
                 clearSelections();
                 //then add this new selection
                 addSelection(e.target);
             }
         }

         //else [if the element is anything else]
         //and the selection modifier is not pressed
         else if (!hasModifier(e)) {
             //clear dropeffect from the target containers
             clearDropeffects();
             //clear all existing selections
             clearSelections();
         }

         //else [if the element is anything else and the modifier is pressed]
         else {
             //clear dropeffect from the target containers
             clearDropeffects();
         }

     }, false);
     //mouseup event to implement multiple selection
     document.addEventListener('mouseup', function(e) {
         //if the element is a draggable item
         //and the multipler selection modifier is pressed
         if (e.target.getAttribute('draggable') && hasModifier(e)) {
             //if the item's grabbed state is currently true
             if (e.target.getAttribute('aria-grabbed') == 'true') {
                 //unselect this item
                 removeSelection(e.target);
                 //if that was the only selected item
                 //then reset the owner container reference
                 if (!selections.items.length) {
                     selections.owner = null;
                 }
             }

             //else [if the item's grabbed state is false]
             else {
                 //add this additional selection
                 addSelection(e.target);
             }
         }

     }, false);
     //dragstart event to initiate mouse dragging
     document.addEventListener('dragstart', function(e) {
         //if the element's parent is not the owner, then block this event
         if (selections.owner != e.target.parentNode) {
             e.preventDefault();
             return;
         }

         //[else] if the multiple selection modifier is pressed
         //and the item's grabbed state is currently false
         if (
             hasModifier(e) &&
             e.target.getAttribute('aria-grabbed') == 'false'
         ) {
             //add this additional selection
             addSelection(e.target);
         }

         //we don't need the transfer data, but we have to define something
         //otherwise the drop action won't work at all in firefox
         //most browsers support the proper mime-type syntax, eg. "text/plain"
         //but we have to use this incorrect syntax for the benefit of IE10+
         e.dataTransfer.setData('text', '');
         //apply dropeffect to the target containers
         addDropeffects();
     }, false);
     //keydown event to implement selection and abort
     document.addEventListener('keydown', function(e) {
         //if the element is a grabbable item
         if (e.target.getAttribute('aria-grabbed')) {
             //Space is the selection or unselection keystroke
             if (e.keyCode == 32) {
                 //if the multiple selection modifier is pressed
                 if (hasModifier(e)) {
                     //if the item's grabbed state is currently true
                     if (e.target.getAttribute('aria-grabbed') == 'true') {
                         //if this is the only selected item, clear dropeffect
                         //from the target containers, which we must do first
                         //in case subsequent unselection sets owner to null
                         if (selections.items.length == 1) {
                             clearDropeffects();
                         }

                         //unselect this item
                         removeSelection(e.target);
                         //if we have any selections
                         //apply dropeffect to the target containers,
                         //in case earlier selections were made by mouse
                         if (selections.items.length) {
                             addDropeffects();
                         }

                         //if that was the only selected item
                         //then reset the owner container reference
                         if (!selections.items.length) {
                             selections.owner = null;
                         }
                     }

                     //else [if its grabbed state is currently false]
                     else {
                         //add this additional selection
                         addSelection(e.target);
                         //apply dropeffect to the target containers
                         addDropeffects();
                     }
                 }

                 //else [if the multiple selection modifier is not pressed]
                 //and the item's grabbed state is currently false
                 else if (e.target.getAttribute('aria-grabbed') == 'false') {
                     //clear dropeffect from the target containers
                     clearDropeffects();
                     //clear all existing selections
                     clearSelections();
                     //add this new selection
                     addSelection(e.target);
                     //apply dropeffect to the target containers
                     addDropeffects();
                 }

                 //else [if modifier is not pressed and grabbed is already true]
                 else {
                     //apply dropeffect to the target containers
                     addDropeffects();
                 }

                 //then prevent default to avoid any conflict with native actions
                 e.preventDefault();
             }

             //Modifier + M is the end-of-selection keystroke
             if (e.keyCode == 77 && hasModifier(e)) {
                 //if we have any selected items
                 if (selections.items.length) {
                     //apply dropeffect to the target containers
                     //in case earlier selections were made by mouse
                     addDropeffects();
                     //if the owner container is the last one, focus the first one
                     if (selections.owner == targets[targets.length - 1]) {
                         targets[0].focus();
                     }

                     //else [if it's not the last one], find and focus the next one
                     else {
                         for (var len = targets.length, i = 0; i < len; i++) {
                             if (selections.owner == targets[i]) {
                                 targets[i + 1].focus();
                                 break;
                             }
                         }
                     }
                 }

                 //then prevent default to avoid any conflict with native actions
                 e.preventDefault();
             }
         }

         //Escape is the abort keystroke (for any target element)
         if (e.keyCode == 27) {
             //if we have any selected items
             if (selections.items.length) {
                 //clear dropeffect from the target containers
                 clearDropeffects();
                 //then set focus back on the last item that was selected, which is
                 //necessary because we've removed tabindex from the current focus
                 selections.items[selections.items.length - 1].focus();
                 //clear all existing selections
                 clearSelections();
                 //but don't prevent default so that native actions can still occur
             }
         }

     }, false);
     //related variable is needed to maintain a reference to the
     //dragleave's relatedTarget, since it doesn't have e.relatedTarget
     var related = null;
     //dragenter event to set that variable
     document.addEventListener('dragenter', function(e) {
         related = e.target;
     }, false);
     //dragleave event to maintain target highlighting using that variable
     document.addEventListener('dragleave', function(e) {
         //get a drop target reference from the relatedTarget
         var droptarget = getContainer(related);
         //if the target is the owner then it's not a valid drop target
         if (droptarget == selections.owner) {
             droptarget = null;
         }

         //if the drop target is different from the last stored reference
         //(or we have one of those references but not the other one)
         if (droptarget != selections.droptarget) {
             //if we have a saved reference, clear its existing dragover class
             if (selections.droptarget) {
                 selections.droptarget.className =
                     selections.droptarget.className.replace(/ dragover/g, '');
             }

             //apply the dragover class to the new drop target reference
             if (droptarget) {
                 droptarget.className += ' dragover';
             }

             //then save that reference for next time
             selections.droptarget = droptarget;
         }

     }, false);
     //dragover event to allow the drag by preventing its default
     document.addEventListener('dragover', function(e) {
         //if we have any selected items, allow them to be dragged
         if (selections.items.length) {
             e.preventDefault();
         }

     }, false);
     //dragend event to implement items being validly dropped into targets,
     //or invalidly dropped elsewhere, and to clean-up the interface either way
     document.addEventListener('dragend', function(e) {
         if(selections.owner.getAttribute('data-class') == "module-droppable") {
             return false;
         }
         //if we have a valid drop target reference
         //(which implies that we have some selected items)
         if (selections.droptarget) {
             //append the selected items to the end of the target container
             for (var len = selections.items.length, i = 0; i < len; i++) {
                 selections.droptarget.appendChild(selections.items[i]);
             }

             //prevent default to allow the action
             e.preventDefault();
         }

        if(selections.droptarget != null && selections.droptarget.firstElementChild != null && selections.owner.getAttribute('data-class') == "module-draggable") {
            load_field_details(e, selections.droptarget.firstElementChild.getAttribute('data-id'), selections.droptarget.firstElementChild.getAttribute('data-model'));
             if (Number(e.target.parentNode.children.length) > 1) {
                 if (selections.droptarget && selections.droptarget.lastElementChild) {
                     selections.owner.appendChild(selections.droptarget.lastElementChild);
                 }
                 e.preventDefault();
             }
         }

         //if we have any selected items
         if (selections.items.length) {
             //clear dropeffect from the target containers
             clearDropeffects();
             //if we have a valid drop target reference
             if (selections.droptarget) {
                 //reset the selections array
                 clearSelections();
                 //reset the target's dragover class
                 selections.droptarget.className =
                     selections.droptarget.className.replace(/ dragover/g, '');
                 //reset the target reference
                 selections.droptarget = null;
             }
         }
     }, false);
 })();
 /* draggable - end */

    function load_field_details(e, module_id, model_name) {
        $('.li-droppable').removeClass("active");
        $(e.target).addClass('active');
        $('#fields_div').html('');
        $('#save_tab_configurations').hide();
        $('#save_fields').hide();
        if (e.target.parentNode && e.target.parentNode.getAttribute('data-class') === 'module-droppable') {
            $.ajax({
                type: "GET",
                url: "{{route('ip-camera-dashboard.getCustomTableFieldsByModule')}}",
                data: {'module_id': module_id, 'model_name':model_name},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#fields_div').html(response.html);
                    var layout_detail_id = e.target.parentNode.getAttribute('data-id');
                    $('#layout_detail_id').val(layout_detail_id);

                    if(document.getElementById("hidden_array_section").querySelector("#module_field_array_"+module_id+'_'+model_name) && $('#module_field_array_'+module_id+'_'+model_name).val() !== "") {
                        var array_values = JSON.parse($('#module_field_array_'+module_id+'_'+model_name).val());
                        array_values.forEach(function (array_value) {
                            var ky = array_value.key;
                            var display_name = array_value.display_name;
                            var enable_sort = array_value.enable_sort;
                            var sort_order = array_value.sort_order;
                            var permission_text = array_value.permission_text;

                            $('#is_active_checkbox_' + ky).prop('checked', true);
                            $('#permission_text_' + ky).val(permission_text);
                            $('#display_name_text_' + ky).val(display_name);
                            $('#enable_sort_' + ky).prop('checked', enable_sort);
                            $('#sort_order_' + ky).val(sort_order);

                            if(($('#type_' + ky).text() != "Widget") && (model_name != "ShiftModule")) {
                                $('#display_name_text_' + ky).removeAttr("disabled");
                                $('#enable_sort_' + ky).removeAttr("disabled");
                            }else{
                                $('#display_name_text_' + ky).attr("disabled", "disabled");
                                $('#enable_sort_' + ky).attr("disabled", "disabled");
                            }

                            if($('#enable_sort_' + ky).prop('checked')) {
                                $('#sort_order_' + ky).removeAttr("disabled");
                            }else {
                                $('#sort_order_' + ky).attr("disabled", "disabled");
                            }
                        });
                    }else {
                        var model = model_name;
                        var fields_by_module_arr = [];
                        $('.is_active_checkbox').each(function(ind, element){
                            var current_element = $(this);

                            var key = current_element.attr('data-key');
                            $('#is_active_checkbox_' + key).prop('checked', true);
                            if(($('#type_' + key).text() != "Widget") && (model != "ShiftModule")) {
                                $('#display_name_text_' + key).removeAttr("disabled");
                                $('#enable_sort_' + key).removeAttr("disabled");
                            }else{
                                $('#display_name_text_' + key).attr("disabled", "disabled");
                                $('#enable_sort_' + key).attr("disabled", "disabled");
                            }

                            if($('#enable_sort_' + key).prop('checked')) {
                                $('#sort_order_' + key).removeAttr("disabled");
                            }else {
                                $('#sort_order_' + key).attr("disabled", "disabled");
                                $('#sort_order_' + key).val('');
                            }

                            var module_id = current_element.attr('data-module');
                            var display_name = $('#display_name_text_' + key).val();
                            var field_name = $('#field_system_name_' + key).text();
                            var type = $('#type_' + key).text();
                            var model_name = $('#model_name_' + key).text();
                            var enable_sort = $('#enable_sort_' + key).prop("checked");
                            var sort_order = $('#sort_order_' + key).val();
                            var layout_detail_id = $('#layout_detail_id').val();
                            var permission_text = $('#permission_text_' + key).val();
                            var visible = Number($('#visible_' + key).text());

                            fields_by_module_arr.push({
                                'module_id': module_id,
                                'display_name': display_name,
                                'field_name': field_name,
                                'type': type,
                                'model_name': model_name,
                                'enable_sort': (enable_sort)? 1: 0,
                                'sort_order': sort_order,
                                'key': key,
                                'layout_detail_id': layout_detail_id,
                                'permission_text': permission_text,
                                'visible': visible
                            });
                        });

                        if (!document.getElementById("hidden_array_section").querySelector("#module_field_array_" + module_id+'_'+model_name)) {
                            $('#hidden_array_section').append('<input type="hidden" id="module_field_array_' + module_id +'_'+model_name +'" name="module_field_array[]" class="module_hidden_input"/>');
                        }

                        var hidden_element_id = '#module_field_array_' + module_id +'_'+model_name;
                        $(hidden_element_id).val(JSON.stringify(fields_by_module_arr));
                        var array_values = JSON.parse($(hidden_element_id).val());
                        if (array_values.length == 0) {
                            $(hidden_element_id).remove();
                        }
                    }
                    remove_hidden_entries();
                    var total_drop_box_count = document.querySelectorAll('#realImageDiv .module-droppable').length;
                    var total_drop_box_li_count = document.querySelectorAll('.module-droppable .li-droppable').length;
                    var module_fields_mapping_count = document.querySelectorAll('#hidden_array_section .module_hidden_input').length;
                    if((total_drop_box_count == total_drop_box_li_count) && (total_drop_box_count == module_fields_mapping_count)){
                        $('#save_tab_configurations').show();
                    }else {
                        $('#save_fields').show();
                    }
                }
            });
        }
    }

    function remove_hidden_entries() {
        var droppable_arr = [];
        var default_tab_structure = $('#default_tab_structure').prop('checked');

        if(default_tab_structure) {
            $('.module-droppable .li-droppable').each(function() {
                droppable_arr.push('module_field_array_' + $(this).attr('data-id') + '_' + $(this).attr('data-model'));
            });

            if(droppable_arr.length > 0) {
                var element_html = '';
                var temporaryHiddenDiv = document.createElement("div");
                temporaryHiddenDiv.setAttribute('id','temporary_hidden_array_section');

                droppable_arr.forEach(function (array_value) {
                    var myEle = document.getElementById(array_value);
                    if(myEle) {
                        temporaryHiddenDiv.appendChild(document.getElementById(array_value));
                    }
                });

                var hidden_array_section = document.getElementById('hidden_array_section');
                hidden_array_section.innerHTML = temporaryHiddenDiv.innerHTML;
                $('#temporary_hidden_array_section').remove();
            }
        }
    }

    function deleteItemSelected(e) {
        console.log(e.target.parentNode);
        $('#save_tab_configurations').hide();
        $('.module-draggable').append(e.target.parentNode);
        var module_id = e.target.parentNode.getAttribute('data-id');
        var module_type = e.target.parentNode.getAttribute('data-model');
        $('#module_field_array_'+module_id+'_'+module_type).remove();
        $("#module-list-ol li").sort(sort_li).appendTo('#module-list-ol');
        $('#fields_div').html('');
    }

    $(function(){
        var layout_detail_ids = [];
        $('.module-droppable').each(function () {
            var layout_detail_id = Number($(this).attr('data-id'));
            layout_detail_ids.push(layout_detail_id);
        });

        @if(!empty($tabDetails) && !empty($tabDetails['landing_page_widget_layout_id']))
            $('#hidden_array_section').html('');
            var hidden_module_array = @json($tabDetails['hidden_module_array']);
            for(var module_id in hidden_module_array) {
                var existing_field_values = [];

                for(var item in hidden_module_array[module_id]) {
                    if(layout_detail_ids.includes(hidden_module_array[module_id][item]['layout_detail_id'])) {
                        existing_field_values.push({
                            'module_id': hidden_module_array[module_id][item]['module_id'],
                            'display_name': hidden_module_array[module_id][item]['field_display_name'],
                            'field_name': hidden_module_array[module_id][item]['field_system_name'],
                            'type': hidden_module_array[module_id][item]['type'],
                            'model_name': hidden_module_array[module_id][item]['model_name'],
                            'enable_sort': (hidden_module_array[module_id][item]['default_sort'])? 1: 0,
                            'sort_order': hidden_module_array[module_id][item]['default_sort_order'],
                            'key': hidden_module_array[module_id][item]['key'],
                            'layout_detail_id': hidden_module_array[module_id][item]['layout_detail_id'],
                            'permission_text': hidden_module_array[module_id][item]['permission_text'],
                            'visible': (hidden_module_array[module_id][item]['visible'] == true)? 1 : 0
                        });
                    }
                }

                if(existing_field_values.length > 0) {
                    var module_element_value = JSON.stringify(existing_field_values);
                    $('#hidden_array_section').append('<input type="hidden" id="module_field_array_' + module_id + '" name="module_field_array[]" class="module_hidden_input"/>');
                    $('#module_field_array_' + module_id).val(module_element_value);
                }
            }
        @endif
    });

    function sort_li(a, b){
        return ($(b).data('widget-name').toUpperCase()) < ($(a).data('widget-name').toUpperCase()) ? 1 : -1;
    }
    </script>
