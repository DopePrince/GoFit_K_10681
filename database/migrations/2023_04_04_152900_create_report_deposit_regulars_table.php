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
        Schema::create('report_deposit_regulars', function (Blueprint $table) {
            $table->string('NO_STRUK_REGULAR')->primary();
            $table->string('ID_MEMBER');
            $table->foreign('ID_MEMBER')->references('ID_MEMBER')->on('members')->onUpdate('cascade')->onDelete('cascade');
            $table->string('ID_PEGAWAI');
            $table->foreign('ID_PEGAWAI')->references('ID_PEGAWAI')->on('pegawais')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('ID_PROMO_REGULAR');
            $table->foreign('ID_PROMO_REGULAR')->references('ID_PROMO_REGULAR')->on('promo_regulars')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('TANGGAL_TRANSAKSI');
            $table->float('TOPUP_AMOUNT');
            $table->float('BONUS');
            $table->float('REMAINING_REGULAR');
            $table->float('TOTAL_REGULAR');
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
        Schema::dropIfExists('report_deposit_regulars');
    }
};
