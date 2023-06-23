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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->string('ID_PEGAWAI')->primary();
            $table->string('FULL_NAME');
            $table->string('GENDER');
            $table->date('TANGGAL_LAHIR');
            $table->string('PHONE_NUMBER');
            $table->string('ADDRESS');
            $table->string('EMAIL')->unique();
            $table->string('PASSWORD');
            $table->string('ROLE');
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
        Schema::dropIfExists('pegawais');
    }
};
