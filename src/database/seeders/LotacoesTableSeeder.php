<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\Unidade;
use App\Models\Lotacao;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LotacoesTableSeeder extends Seeder
{
    public function run()
    {
        $pessoas = Pessoa::all();
        $unidades = Unidade::all();

        foreach ($pessoas as $pessoa) {
            $lotacaoAtiva = rand(0, 1);
            $unidade = $unidades->random();

            $lotacao = Lotacao::create([
                'pes_id' => $pessoa->pes_id,
                'unid_id' => $unidade->unid_id,
                'lot_data_lotacao' => Carbon::now()->subMonths(rand(1, 24)),
                'lot_data_remocao' => $lotacaoAtiva ? null : Carbon::now()->subMonths(rand(1, 12)),
                'lot_portaria' => 'Portaria ' . rand(100, 999) . '/' . rand(2018, 2023)
            ]);

            // Algumas pessoas têm mais de uma lotação
            if (rand(0, 3) === 0) {
                Lotacao::create([
                    'pes_id' => $pessoa->pes_id,
                    'unid_id' => $unidades->where('unid_id', '!=', $unidade->unid_id)->random()->unid_id,
                    'lot_data_lotacao' => Carbon::now()->subMonths(rand(25, 48)),
                    'lot_data_remocao' => Carbon::now()->subMonths(rand(13, 24)),
                    'lot_portaria' => 'Portaria ' . rand(100, 999) . '/' . rand(2015, 2017)
                ]);
            }
        }
    }
}