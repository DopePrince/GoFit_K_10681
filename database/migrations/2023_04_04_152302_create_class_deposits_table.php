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
        Schema::create('class_deposits', function (Blueprint $table) {
            $table->increments('ID_CLASS_DEPOSIT');
            $table->string('ID_MEMBER');
            $table->foreign('ID_MEMBER')->references('ID_MEMBER')->on('members')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('ID_CLASS');
            $table->foreign('ID_CLASS')->references('ID_CLASS')->on('class_details')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('CLASS_AMOUNT');
            $table->date('EXPIRE_DATE');
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
        Schema::dropIfExists('class_deposits');
    }
};
