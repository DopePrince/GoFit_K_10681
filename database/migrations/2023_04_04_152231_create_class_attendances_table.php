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
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->string('ID_CLASS_ATTENDANCE')->primary();
            $table->integer('ID_CLASS_BOOKING');
            $table->foreign('ID_CLASS_BOOKING')->references('ID_CLASS_BOOKING')->on('class_bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('DATE_TIME');
            $table->float('SISA_DEPOSIT_REGULAR');
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
        Schema::dropIfExists('class_attendances');
    }
};
