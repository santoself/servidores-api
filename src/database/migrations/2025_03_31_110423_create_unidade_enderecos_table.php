<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unidade_endereco', function (Blueprint $table) {
            $table->foreignId('unid_id')->constrained('unidade', 'unid_id');
            $table->foreignId('end_id')->constrained('endereco', 'end_id');
            $table->primary(['unid_id', 'end_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('unidade_endereco');
    }
};
