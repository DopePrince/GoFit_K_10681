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
        Schema::create('class_bookings', function (Blueprint $table) {
            $table->integer('ID_CLASS_BOOKING')->primary();
            $table->string('ID_MEMBER');
            $table->foreign('ID_MEMBER')->references('ID_MEMBER')->on('members')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('ID_CLASS_ON_RUNNING_DAILY');
            $table->foreign('ID_CLASS_ON_RUNNING_DAILY')->references('ID_CLASS_ON_RUNNING_DAILY')->on('class_on_running_dailies')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('DATE_TIME');
            $table->string('PAYMENT_TYPE');
            $table->boolean('STATUS_PRESENSI');
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
        Schema::dropIfExists('class_bookings');
    }
};
