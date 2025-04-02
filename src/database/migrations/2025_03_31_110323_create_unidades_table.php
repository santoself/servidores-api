<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unidade', function (Blueprint $table) {
            $table->id('unid_id');
            $table->string('unid_nome', 200);
            $table->string('unid_sigla', 20);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('unidade');
    }
};
