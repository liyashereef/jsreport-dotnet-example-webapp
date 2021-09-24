    <div id="contractamendments{{$amendmentcount}}">
    <div class="form-group row">
        <div class="col-sm-12 " style="border-bottom:solid 1px #000 "></div>
    </div>
    <div class="form-group row">
        <div class="col-sm-4">Description</div>
        <div class="col-sm-4">
        <textarea rows="7" style="resize:none" id="amendment_description_{{$amendmentcount}}" name="amendment_description_{{$amendmentcount}}" class="form-control" maxlength="3000"></textarea>
        </div>
    </div>
    <div class="form-group row">
            <div class="col-sm-4">Attachment(Supports doc,docx,pdf,xls,xlsx,ods,ppt,pptx)</div>
            <div class="col-sm-4">
                <input type="file" name="amendment_attachment_id_{{$amendmentcount}}" id="amendment_attachment_id_{{$amendmentcount}}" class="form-control amendmentfilecontrol">
                <input type="hidden" name="amendment_document_attachment_{{$amendmentcount}}" id="amendment_document_attachment_{{$amendmentcount}}" value="">
                <p id="amendmentuploadlabel_{{$amendmentcount}}" style="display:none"></p>
            </div>
            <div class="col-sm-1">
                <button type="button" 
                id="amendment_attachment_button_id_{{$amendmentcount}}" 
                attr_file="amendment_attachment_id_{{$amendmentcount}}" 
                attr_hidden="amendment_document_attachment_{{$amendmentcount}}" 
                attr_file_input_val="amendment_document_attachment_{{$amendmentcount}}"
                class="button btn submit uploadamendfile">Upload</button>
            </div>
            <div class="col-sm-3">
                <label class="error text-danger" for="amendment_attachment_id_{{$amendmentcount}}"></label>
            </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
                <button type="button"
                attr-descid="amendment_description_{{$amendmentcount}}" 
                attr-attachid="amendment_attachment_id_{{$amendmentcount}}"
                attr-attachremoveid="amendment_attachment_button_id_{{$amendmentcount}}" 
                attr-blockid="contractamendments{{$amendmentcount}}" class="button btn submit removeprimarycontent">-</button>
        </div>
</div>
    </div>
