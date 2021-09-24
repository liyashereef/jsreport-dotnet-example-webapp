<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeeShiftWorkHourTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shift_work_hour_types', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->boolean('is_editable')->default(1)->after('description');
            $table->boolean('is_deletable')->default(1)->after('is_editable');
            $table->integer('created_by')->nullable()->after('is_deletable');
            $table->integer('updated_by')->nullable()->after('created_by');
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
         Schema::table('employee_shift_work_hour_types', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('is_editable');
            $table->dropColumn('is_deletable');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
         });
    }
}
