<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterContractClientJobTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_contact_information', function (Blueprint $table) {
            $table->string('contact_jobtitle')->default('Client')->change();


        });
        \DB::statement('update client_contact_information ci set contact_jobtitle=(select position from position_lookups where id=ci.contact_jobtitle)  where contact_jobtitle>0;');
        \DB::statement('update client_contact_information set contact_jobtitle="Client" where isnull(contact_jobtitle);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
