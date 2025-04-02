<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servidor_temporario', function (Blueprint $table) {
            $table->foreignId('pes_id')->constrained('pessoa', 'pes_id');
            $table->date('st_data_admissao');
            $table->date('st_data_demissao')->nullable();
            $table->primary('pes_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('servidor_temporario');
    }
};
