 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorLogDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_log_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned()->index('visitor_log_details_customer_id_foreign');
            $table->integer('template_id')->unsigned()->index('visitor_log_details_template_id_foreign');
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->integer('visitor_type_id')->unsigned()->nullable();
            $table->string('name_of_company', 100)->nullable();
            $table->string('whom_to_visit', 100)->nullable();
            $table->string('notes', 300)->nullable();
            $table->string('picture_file_name', 300)->nullable();
            $table->string('signature_file_name', 300)->nullable();
            $table->integer('created_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitor_log_details');
    }
}
