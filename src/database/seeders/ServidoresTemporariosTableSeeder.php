<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\ServidorTemporario;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ServidoresTemporariosTableSeeder extends Seeder
{
    public function run()
    {
        $pessoas = Pessoa::all()->skip(30)->take(30);

        foreach ($pessoas as $pessoa) {
            $admissao = Carbon::now()->subMonths(rand(1, 24));
            
            ServidorTemporario::create([
                'pes_id' => $pessoa->pes_id,
                'st_data_admissao' => $admissao,
                'st_data_demissao' => rand(0, 1) ? $admissao->addMonths(rand(1, 12)) : null
            ]);
        }
    }
}