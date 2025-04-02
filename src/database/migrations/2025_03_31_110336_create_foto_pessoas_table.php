<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('foto_pessoa', function (Blueprint $table) {
            $table->id('fp_id');
            $table->foreignId('pes_id')->constrained('pessoa', 'pes_id');
            $table->date('fp_data');
            $table->string('fp_bucket', 50);
            $table->string('fp_hash', 50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('foto_pessoa');
    }
};
