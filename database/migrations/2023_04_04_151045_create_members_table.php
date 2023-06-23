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
        Schema::create('members', function (Blueprint $table) {
            $table->string('ID_MEMBER')->primary();
            $table->string('FULL_NAME');
            $table->string('GENDER');
            $table->date('TANGGAL_LAHIR');
            $table->string('PHONE_NUMBER');
            $table->string('ADDRESS');
            $table->string('EMAIL')->unique();
            $table->string('PASSWORD');
            $table->float('DEPOSIT_REGULAR_AMOUNT')->nullable();
            $table->date('EXPIRE_DATE')->nullable();
            $table->boolean('STATUS_MEMBERSHIP')->nullable();
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
        Schema::dropIfExists('members');
    }
};
