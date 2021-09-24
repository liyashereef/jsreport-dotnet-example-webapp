<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateOpenshiftApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_openshift_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shiftid');
            $table->unsignedInteger('userid');
            $table->unsignedInteger('customerid');
            $table->unsignedInteger('multifillid')->nullable();
            $table->date('startdate');
            $table->date('enddate');
            $table->time('starttime')->nullable();
            $table->time('endtime')->nullable();
            $table->unsignedInteger('openshifts');
            $table->string('address', 600);
            $table->decimal('siterate', 12, 2);
            $table->decimal('lineardistance', 12, 2);
            $table->decimal('actualdistance', 12, 2);
            $table->string('sitenotes', 1000)->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('candidate_openshift_applications');
    }
}
