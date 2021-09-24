<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteNoteTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_note_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_notes_id')->unsigned()->comments('ID from Site note task');
            $table->string('task_name', 1000);
            $table->string('assigned_to', 100);
            $table->date('due_date');
            $table->integer('status_id')->unsigned()->comments('ID from Site status');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('site_note_tasks', function (Blueprint $table) {
            $table->foreign('site_notes_id')->references('id')->on('site_notes');
            $table->foreign('status_id')->references('id')->on('site_note_status_lookups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_note_tasks', function (Blueprint $table) {
            $table->dropForeign('site_note_tasks_site_notes_id_foreign');
            $table->dropForeign('site_note_tasks_status_id_foreign');
        });
        Schema::dropIfExists('site_note_tasks');

    }
}
