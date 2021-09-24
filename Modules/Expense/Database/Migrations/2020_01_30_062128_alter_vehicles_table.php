<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('vehicles', function (Blueprint $table) {
            $table->string('model')->nullable()->after('number');
            $table->integer('year')->nullable()->after('model');
            $table->integer('odometer_reading')->nullable()->after('year');
            $table->date('purchasing_date')->nullable()->after('odometer_reading');
            $table->integer('region')->nullable()->after('purchasing_date');
            $table->boolean('is_initiated')->default(0)->after('region');
            $table->date('initiated_date')->nullable()->after('is_initiated');
            $table->boolean('maintenance_due')->default(0)->after('initiated_date');
            $table->boolean('maintenance_critical')->default(0)->after('maintenance_due');
            $table->text('maintenance_notes')->nullable()->after('maintenance_critical');
            $table->date('maintenance_critical_date')->nullable()->after('maintenance_notes');
            $table->date('email_notification_date')->nullable()->after('maintenance_critical_date');
            $table->boolean('active')->default(1)->after('email_notification_date');
            $table->text('notes')->after('active')->nullable();
            $table->integer('created_by')->nullable()->after('active');
            $table->integer('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
           $table->dropColumn('model');
           $table->dropColumn('year');
           $table->dropColumn('odometer_reading');
           $table->dropColumn('purchasing_date');
           $table->dropColumn('region');
            $table->dropColumn('is_initiated');
           $table->dropColumn('initiated_date');
           $table->dropColumn('maintenance_due');
           $table->dropColumn('maintenance_critical');
           $table->dropColumn('maintenance_notes');
           $table->dropColumn('maintenance_critical_date');
           $table->dropColumn('email_notification_date');
           $table->dropColumn('active');
           $table->dropColumn('created_by');
           $table->dropColumn('updated_by');
        });
    }
}
