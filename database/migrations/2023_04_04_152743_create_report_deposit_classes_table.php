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
        Schema::create('report_deposit_classes', function (Blueprint $table) {
            $table->string('NO_STRUK_CLASS')->primary();
            $table->string('ID_MEMBER');
            $table->foreign('ID_MEMBER')->references('ID_MEMBER')->on('members')->onUpdate('cascade')->onDelete('cascade');
            $table->string('ID_PEGAWAI');
            $table->foreign('ID_PEGAWAI')->references('ID_PEGAWAI')->on('pegawais')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('ID_CLASS');
            $table->foreign('ID_CLASS')->references('ID_CLASS')->on('class_details')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('ID_PROMO_CLASS');
            $table->foreign('ID_PROMO_CLASS')->references('ID_PROMO_CLASS')->on('promo_classes')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('TANGGAL_TRANSAKSI');
            $table->float('TOTAL_PRICE');
            $table->integer('TOTAL_PACKAGE');
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
        Schema::dropIfExists('report_deposit_classes');
    }
};
