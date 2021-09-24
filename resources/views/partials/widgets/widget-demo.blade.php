<!-- Demo Widget -->
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetDemo',function(payload) {
        let wc; //widget container

        function generateContent() {
            let content = `
            <div class="test">
                <p>Simple widget: click here</p>
                <p>Time is: ${(new Date().toString())}</p>
            </div>
            `;
            //..process
            return content;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
        }

        function afterBind() {
            //After content render (eg:register envent listeners | init eg: select2)
            wc.on('click', function() {
                alert('Widget body click');
            });
        }

        //Bind contents
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();
    });
</script>