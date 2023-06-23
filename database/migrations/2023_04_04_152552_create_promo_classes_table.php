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
        Schema::create('promo_classes', function (Blueprint $table) {
            $table->integer('ID_PROMO_CLASS')->primary();
            $table->integer('ID_CLASS');
            $table->foreign('ID_CLASS')->references('ID_CLASS')->on('class_details')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('AMOUNT_DEPOSIT');
            $table->integer('BONUS_PACKAGE');
            $table->date('DURATION');
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
        Schema::dropIfExists('promo_classes');
    }
};
