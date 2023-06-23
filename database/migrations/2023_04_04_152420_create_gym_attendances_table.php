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
        Schema::create('gym_attendances', function (Blueprint $table) {
            $table->string('ID_GYM_ATTENDANCE')->primary();
            $table->integer('ID_GYM_BOOKING');
            $table->foreign('ID_GYM_BOOKING')->references('ID_GYM_BOOKING')->on('gym_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('DATE_TIME');
            $table->time('BOOKED_SLOT');
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
        Schema::dropIfExists('gym_attendances');
    }
};
