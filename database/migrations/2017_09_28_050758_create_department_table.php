<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name")->unique();
            $table->string("office_phone", 15);
            $table->string("manager");
            $table->integer("id_intialized")->unsigned();
            $table->foreign("id_intialized")->references("id")->on("users")->onupdate("cascade");
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
        Schema::table("department", function(Blueprint $table){
            Schema::drop("department");
        });
    }
}
