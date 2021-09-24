<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterContractTableForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::disableForeignKeyConstraints();
        Schema::table("contract_submission_reasons",function(BluePrint $table){
            // $table->foreign('createdby')->references('id')->on('users');
             $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
         });
         Schema::table("business_segments",function(BluePrint $table){
            // $table->foreign('createdby')->references('id')->on('users');
             $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
         });
         Schema::table("line_of_businesses",function(BluePrint $table){
            // $table->foreign('createdby')->references('id')->on('users');
             $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
         });
         Schema::table("office_addresses",function(BluePrint $table){
            // $table->foreign('createdby')->references('id')->on('users');
             $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
         });
        Schema::table("contract_billing_rate_changes",function(BluePrint $table){
           // $table->foreign('createdby')->references('id')->on('users');
            $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table("contract_billing_cycles",function(BluePrint $table){
            // $table->foreign('createdby')->references('id')->on('users');
             $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
         });
         
         Schema::table("payment_methods",function(BluePrint $table){
            // $table->foreign('createdby')->references('id')->on('users');
             $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
         });
         Schema::table("device_accesses",function(BluePrint $table){
            // $table->foreign('createdby')->references('id')->on('users');
             $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
         });
         Schema::table("contract_cell_phone_providers",function(BluePrint $table){
            // $table->foreign('createdby')->references('id')->on('users');
             $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
         });
         
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table("contract_submission_reasons",function(BluePrint $table){
            $table->dropForeign('createdby');
        });
        Schema::table("business_segments",function(BluePrint $table){
            $table->dropForeign('createdby');
        });
        Schema::table("line_of_businesses",function(BluePrint $table){
            $table->dropForeign('createdby');
        });
        Schema::table("office_addresses",function(BluePrint $table){
            $table->dropForeign('createdby');
        });
        Schema::table("contract_billing_rate_changes",function(BluePrint $table){
            $table->dropForeign('createdby');
        });
        Schema::table("contract_billing_cycles",function(BluePrint $table){
            $table->dropForeign('createdby');
        });
        Schema::table("payment_methods",function(BluePrint $table){
            $table->dropForeign('createdby');
        });
        Schema::table("device_accesses",function(BluePrint $table){
            $table->dropForeign('createdby');
        });
        Schema::table("contract_cell_phone_providers",function(BluePrint $table){
            $table->dropForeign('createdby');
        });
        Schema::enableForeignKeyConstraints();
    }
}
