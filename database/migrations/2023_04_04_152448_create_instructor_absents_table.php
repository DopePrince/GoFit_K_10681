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
        Schema::create('instructor_absents', function (Blueprint $table) {
            $table->increments('ID_INSTRUCTOR_ABSENT');
            $table->string('ID_INSTRUCTOR');
            $table->foreign('ID_INSTRUCTOR')->references('ID_INSTRUCTOR')->on('instructors')->onUpdate('cascade')->onDelete('cascade');
            $table->string('ID_SUBSTITUTE_INSTRUCTOR');
            $table->foreign('ID_SUBSTITUTE_INSTRUCTOR')->references('ID_INSTRUCTOR')->on('instructors')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('ID_CLASS_ON_RUNNING');
            $table->foreign('ID_CLASS_ON_RUNNING')->references('ID_CLASS_ON_RUNNING')->on('class_on_runnings')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('ABSENT_DATE_TIME');
            $table->string('ABSENT_REASON');
            $table->boolean('IS_CONFIRMED');
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
        Schema::dropIfExists('instructor_absents');
    }
};
