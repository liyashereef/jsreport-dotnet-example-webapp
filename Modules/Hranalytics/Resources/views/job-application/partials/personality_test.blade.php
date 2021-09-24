
<label id='questions_length' class='doc-title-top'></label>
<div class="form-group" id='personality_inventory_question_answer'>
</div>

<script>



    $(function(){
       first_index = 0;
        time_interval = 15000; //Time in miliseconds
        window.alert_var = null;
        personality_inventory_array = [];
        question_answer = {!! json_encode($lookups['personality_questions']->toArray()) !!};
        question_answer_count = question_answer.length;
        $('#navigation-btn').hide();
        displayIntroduction();


        function timeoutInterval() {
            /*var d = new Date();
            console.log(d.getHours()+' '+d.getMinutes()+' '+d.getSeconds()); 
            console.log(d.getSeconds()+(time_interval/1000));*/
            window.alert_var = setTimeout(function(){
                if(!($('.sweet-alert').is(':visible')) && ($(".nav-link.active").attr("href") == "#personality_inventory")){
                    timeOutAlert();
                }
            }, time_interval);
        }

        function timeOutAlert() {
            /*var d = new Date();
            console.log("Showing alert on "+d.getHours()+' '+d.getMinutes()+' '+d.getSeconds());*/
            swal({
                title: "Alert",
                text: "Don't over analyze the question, go with the answer that feels the best",
                type:"warning",
                allowEscapeKey:"false",
                confirmButtonText:"OK",
                confirmButtonClass:"swal-ok"
            },timeoutInterval);
        }


        $('.nav-link').on('click',function(){            
            setTimeout(function(){
                if(
                    $('#start_test').length < 1 &&
                    !($('#navigation-btn').is(':visible')) &&
                    $("#next_question").length == 1
                )
                {       
                    clearTimeout(window.alert_var);             
                    timeoutInterval();
                }
            },1000);
        });


        /* Start button in introduction click function - Start */
        $('#personality_inventory_question_answer').on('click', '#start_test' , function(){
            displayQuestions(question_answer, first_index, question_answer_count);
            $('.help-block').text('');
            timeoutInterval();
        });
        /* Start button in introduction click function - End */


        /* Submit button click function - Start */
        $('#personality_inventory_question_answer').on('click', '#next_question' , function(){            
            index = $('#index').val();
            next_index = index*1+1;
            option = $('input[name="personality_inventory_option"]:checked').val();
            personality_question_id = $('.personality_questions').data('question-id');
            $('#navigation-btn').hide();
            if(option != undefined){
                clearTimeout(window.alert_var);
                $('.help-block').text('');
                personality_inventory_result={};
                if(next_index >= question_answer_count){
                    $('input[name="personality_inventory_option"]').prop('disabled', true);
                    $('#navigation-btn').show();
                    $('#next_question').hide();
                    $('.help-block').text('');
                }else{
                    displayQuestions(question_answer, next_index, question_answer_count);
                    timeoutInterval();
                }
                personality_inventory_result.question_id = personality_question_id;
                personality_inventory_result.question_option_id = option;
                personality_inventory_array.push(personality_inventory_result);
                console.log(personality_inventory_result, personality_inventory_array);
            }else{
                $('.help-block').text('Please choose an option');
            }
        });
        /* Submit button click function - End */

    });

    /* Function to display the introduction text - Start */
    function displayIntroduction(){
        intro = "<p>The following set of questions are designed to measure your aptitude for a position in the security industry.  Completing this part of the onboarding process should take no more than 15 minutes. There are no right or wrong answers to any of these questions, so please answer each of these as best you can. Answer the questions quickly, do not over-analyze them. Some seem worded poorly. Go with what feels best. The most important advice is to answer the questions as \"the way you are\" not \"the way you'd like to be seen by others\". We will use this information to match you to various sites based on aptitude. Should you get to the interview stage, we'll share some fun insights on you based on your responses.</p><hr>"
        intro += "<div class='text-center margin-bottom-5'>";
        intro += "<input type='button' class='confirm btn btn-lg btn-primary' id='start_test' value='Start'>";
        intro += "</div>";
        $('#personality_inventory_question_answer').append(intro);
    }
    /* Function to display the introduction text - End */

    /* Function to display each personality inventory question - Start */
    function displayQuestions(question_answer, index, question_answer_count){
        question_index = index*1+1;
        console.log('Question '+question_index+' of '+question_answer_count);
        $('#questions_length').text('Question '+question_index+' of '+question_answer_count);
        personality_inventory = "<div class='form-group row personality_questions' data-question-id='"+question_answer[index].id+"' id='question_"+question_answer[index].id+"'>";
        personality_inventory += "<input type='hidden' class='form-control' id='index' value="+index+">";
        personality_inventory += "<label class='col-sm-12 col-form-label' id='question_id_"+question_answer[index].id+"'>"+question_index+") "+question_answer[index].question+"</label>";
        personality_inventory += "<div class='col-sm-12 padding-left-8'>";
        $.each(question_answer[index].options, function(key,value){
            personality_inventory += "<label class='radio-button-label' for='answer_id_"+value.id+"'>"
            personality_inventory += "<input class='doc-title-top' type='radio' name='personality_inventory_option' id='answer_id_"+value.id+"' value='"+value.id+"'/>"
            personality_inventory += value.option.toUpperCase()+') '+value.value+"</label></br>";
        });
        personality_inventory += "</div>";
        personality_inventory += "</div>";
        personality_inventory += "<div class='form-group row form-control-feedback col-sm-12'>";
        personality_inventory += "<span class='col-sm-12 padding-left-8 help-block text-danger align-middle font-12'></span>";
        personality_inventory += "</div>";
        personality_inventory += "<div class='text-center margin-bottom-5'>";
        personality_inventory += "<input type='button' class='confirm btn btn-lg btn-primary' id='next_question' value='Submit'>";
        personality_inventory += "</div>";
        $('#personality_inventory_question_answer').empty();
        $('#personality_inventory_question_answer').append(personality_inventory);
    }
    /* Function to display each personality inventory question - End */

</script>
