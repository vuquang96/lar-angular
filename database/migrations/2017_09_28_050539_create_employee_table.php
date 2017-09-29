<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name", 60);
            $table->string("photo", 80);
            $table->string("job_title", 30);
            $table->string("phone", 15);
            $table->string("email", 60);
            $table->date("birthday");
            $table->integer("sex")->length(1);
            $table->string("address", 80);
            $table->date("date_start_work");
            $table->integer("id_intialized")->unsigned();
            $table->foreign("id_intialized")->references("id")->on("users")->onupdate("cascade");
            $table->integer("id_department")->unsigned();
            $table->foreign("id_department")->references("id")->on("department")->onupdate("cascade");
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
        Schema::table('employee', function(Blueprint $table) {
            Schema::drop('employee');
        });
    }
}
