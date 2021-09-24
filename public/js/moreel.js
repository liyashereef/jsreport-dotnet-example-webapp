class MoreEl {
    divSelector= '#dynamicElDiv';
    moreHtmlDiv = '#more-content';
    addButtonSelector = '';
    removeButtonSelector = '';
    elCount = 0;
    variable = {
        position_num : 0,
        name : 'elem',
        placeholder : 'Enter Value',
    };
    divParam = {
        containerDiv: '#dynamic-rows',
        addButton: '.add_button',
        addMaxCount: undefined,
        removeButton: '.remove_button',
        removeOne: false,
        rowDiv: '.el_fields',
        moreHtmlDiv: '#more-content',
        data:{},
        afterAdd: function(){},
        afterRemove: function(){}
    };
    constructor(elName,divParam,variable) {
        $.extend(this.divParam,divParam);
        $.extend(this.variable,variable);
        this.elCount = 0;
        this.variable.name = elName;
        this.divSelector = this.divParam.containerDiv;
        this.moreHtmlDiv = this.divParam.moreHtmlDiv;
        this.addButton = this.divParam.addButton;
        this.addMaxCount = this.divParam.addMaxCount;
        this.removeButton = this.divParam.removeButton;
        this.removeOne = this.divParam.removeOne;
        this.addButtonSelector = this.divSelector+' '+this.addButton;
        this.removeButtonSelector = this.divSelector+' '+this.removeButton;
        this.rowDivSelector = this.divSelector+'> '+this.divParam.rowDiv;
        this.lastRowDivSelector = this.rowDivSelector+':last';
        this.firstRowDivSelector = this.rowDivSelector+':first';
    }
    eventsInit() {
        let parent = this;
        $(this.addButtonSelector).off('click').on('click', function(e) {
            parent.addRow(e, this);
        });
        $(this.removeButtonSelector).off('click').on('click', function(e) {
            parent.removeRow(e, this);
        });
    }

    initElDiv(edit = false){
        $(this.divSelector).html('');
        this.elCount = 0;
        this.variable.position_num = 0;
        if(!edit){
            return this.addRow();
        }
    }

    /**
     * Function to implement add logic
     * @param event Event Object
     * @param eventObj Element Object - "this" in click function
     */
    addRow(event, eventObj) {
        let classObj = this;
        let divId = classObj.divSelector;
        let param = classObj.variable;
        let rowDivSelector = this.rowDivSelector;
        let lastRowDivSelector = this.lastRowDivSelector;
        let firstRowDivSelector = this.firstRowDivSelector;
        let html = $(classObj.moreHtmlDiv).html();
        html = this.replaceHtml(html,param);
        $(divId).append(html);
        let newRowId = $(html).attr('id');
        let newElSelector = ".el_fields[data-elid="+param.position_num+"]#"+newRowId;
        param.position_num++;
        this.elCount++;
        this.hideAddButton(rowDivSelector);
        this.showAddButton(lastRowDivSelector);
        if(this.elCount === 1){
            this.hideRemoveButton(firstRowDivSelector);
        } else {
            this.showRemoveButton(firstRowDivSelector);
        }
        if(this.removeOne) {
            this.hideRemoveButton(rowDivSelector);
            if(this.elCount > 1) {
                this.showRemoveButton(newElSelector);
            }
        }
        if(this.addMaxCount !== undefined && this.elCount === this.addMaxCount) {
            this.hideAddButton(newElSelector);
        }
        this.eventsInit();
        this.afterAdd($(newElSelector));
        return $(newElSelector);
    }

    /**
     * Function to implement remove logic
     *
     * @param event Event Object
     * @param ele Element Object - "this" in click function
     */
    removeRow(event, ele) {
        let parent = this;
        let positionNum = $(ele).data('elid');
        let rowDivId = '#'+this.variable.name+'_row_'+positionNum;
        $(rowDivId).remove();
        this.elCount--;
        this.showAddButton(this.lastRowDivSelector);
        if(this.elCount === 1){
            this.hideRemoveButton(this.firstRowDivSelector);
        }
        if(this.removeOne && this.elCount > 1) {
            this.showRemoveButton(this.lastRowDivSelector);
        }
        if(this.addMaxCount !== undefined && this.elCount < this.addMaxCount) {
            this.showAddButton(this.lastRowDivSelector);
        }
        this.afterRemove();
    }

    /**
     * Function to replace variables in HTML template
     * @param html
     * @param param
     * @returns {*}
     */
    replaceHtml(html,param) {
        Object.entries(param).forEach(([key, val]) => {
            let regxStr = '--'+key+'--';
            let paramRegx = new RegExp(regxStr,'g');
            html = html.replace(paramRegx,val);
        });
        return html;
    }

    showAddButton(el) {
        let addButton = this.divParam.addButton;
        $(el).find(addButton).show();
    }

    hideAddButton(el) {
        let addButton = this.divParam.addButton;
        $(el).find(addButton).hide();
    }

    showRemoveButton(el) {
        let removeButton = this.divParam.removeButton;
        $(el).find(removeButton).show();
    }

    hideRemoveButton(el) {
        let removeButton = this.divParam.removeButton;
        $(el).find(removeButton).hide();
    }

    afterAdd(el){
        this.divParam.afterAdd(el);
    }

    afterRemove(el){
        this.divParam.afterRemove(el);
    }
}
