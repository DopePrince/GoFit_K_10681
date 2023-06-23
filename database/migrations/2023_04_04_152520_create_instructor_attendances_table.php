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
        Schema::create('instructor_attendances', function (Blueprint $table) {
            $table->increments('ID_INSTRUCTOR_ATTENDANCE');
            $table->string('ID_INSTRUCTOR');
            $table->foreign('ID_INSTRUCTOR')->references('ID_INSTRUCTOR')->on('instructors')->onUpdate('cascade')->onDelete('cascade');
            $table->time('START_TIME');
            $table->time('END_TIME');
            $table->boolean('IS_ATTENDED');
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
        Schema::dropIfExists('instructor_attendances');
    }
};
