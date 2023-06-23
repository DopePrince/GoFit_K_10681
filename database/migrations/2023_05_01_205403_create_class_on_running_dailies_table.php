<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_on_running_dailies', function (Blueprint $table) {
            $table->increments('ID_CLASS_ON_RUNNING_DAILY');
            $table->integer('ID_CLASS_ON_RUNNING');
            $table->foreign('ID_CLASS_ON_RUNNING')->references('ID_CLASS_ON_RUNNING')->on('class_on_runnings')->onUpdate('cascade')->onDelete('cascade');
            $table->string('ID_INSTRUCTOR');
            $table->foreign('ID_INSTRUCTOR')->references('ID_INSTRUCTOR')->on('instructors')->onUpdate('cascade')->onDelete('cascade');
            $table->date('DATE');
            $table->string('STATUS');
            $table->integer('CLASS_CAPACITY');
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
        Schema::dropIfExists('class_on_running_dailies');
    }
};
