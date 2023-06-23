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
        Schema::create('class_on_runnings', function (Blueprint $table) {
            $table->integer('ID_CLASS_ON_RUNNING')->primary();
            $table->string('ID_INSTRUCTOR');
            $table->foreign('ID_INSTRUCTOR')->references('ID_INSTRUCTOR')->on('instructors')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('ID_CLASS');
            $table->foreign('ID_CLASS')->references('ID_CLASS')->on('class_details')->onUpdate('cascade')->onDelete('cascade');
            $table->date('DATE');
            $table->time('START_CLASS');
            $table->time('END_CLASS');
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
        Schema::dropIfExists('class_on_runnings');
    }
};
