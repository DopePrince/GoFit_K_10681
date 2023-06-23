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
        Schema::create('promo_regulars', function (Blueprint $table) {
            $table->integer('ID_PROMO_REGULAR')->primary();
            $table->float('TOPUP_AMOUNT');
            $table->float('BONUS_REGULAR');
            $table->float('MIN_DEPOSIT');
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
        Schema::dropIfExists('promo_regulars');
    }
};
