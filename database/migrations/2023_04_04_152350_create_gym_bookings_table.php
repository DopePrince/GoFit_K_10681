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
        Schema::create('gym_bookings', function (Blueprint $table) {
            $table->string('ID_GYM_BOOKING')->primary();
            $table->string('ID_MEMBER');
            $table->foreign('ID_MEMBER')->references('ID_MEMBER')->on('members')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('ID_GYM');
            $table->foreign('ID_GYM')->references('ID_GYM')->on('gyms')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('DATE_TIME_BOOKING');
            $table->dateTime('DATE_TIME_PRESENSI');
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
        Schema::dropIfExists('gym_bookings');
    }
};
