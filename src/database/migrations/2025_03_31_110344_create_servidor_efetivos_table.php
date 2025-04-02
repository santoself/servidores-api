<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servidor_efetivo', function (Blueprint $table) {
            $table->foreignId('pes_id')->constrained('pessoa', 'pes_id');
            $table->string('se_matricula', 20);
            $table->primary('pes_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('servidor_efetivo');
    }
};
