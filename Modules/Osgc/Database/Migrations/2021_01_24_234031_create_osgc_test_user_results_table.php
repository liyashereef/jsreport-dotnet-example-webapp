<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsgcTestUserResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osgc_test_user_results', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('course_section_id')->unsigned()->comment('id from osgc_course_content_sections table');
            $table->integer('user_id')->unsigned()->comment('id from user masters table');
            $table->integer('test_course_master_id')->unsigned()->comment('id from osgc test course masters table');
            $table->decimal('course_pass_percentage', 5, 2)->default(0)->comment('total percentage requied to pass course test');
            $table->integer('total_questions')->default(0)->comment('total exam questions');
            $table->integer('total_attempted_questions')->default(0)->comment('total exam attempted questions');
            $table->integer('total_exam_score')->default(0)->comment('total exam sore');
            $table->decimal('score_percentage', 5, 2)->default(0)->comment('score percentage acquired by user');
            $table->boolean('is_exam_pass')->default(false)->comment('0=Fail,1=Pass');
            $table->tinyInteger('status')->default(0)->comment('0=Draft,1=Submitted,2=Close');
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('osgc_test_user_results');
    }
}
