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
        Schema::create('report_aktivasis', function (Blueprint $table) {
            $table->string('NO_STRUK_AKTIVASI')->primary();
            $table->string('ID_MEMBER');
            $table->foreign('ID_MEMBER')->references('ID_MEMBER')->on('members')->onUpdate('cascade')->onDelete('cascade');
            $table->string('ID_PEGAWAI');
            $table->foreign('ID_PEGAWAI')->references('ID_PEGAWAI')->on('pegawais')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('TANGGAL_TRANSAKSI');
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
        Schema::dropIfExists('report_aktivasis');
    }
};
