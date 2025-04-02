<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\ServidorEfetivo;
use Illuminate\Database\Seeder;

class ServidoresEfetivosTableSeeder extends Seeder
{
    public function run()
    {
        $pessoas = Pessoa::all()->take(30);

        foreach ($pessoas as $pessoa) {
            ServidorEfetivo::create([
                'pes_id' => $pessoa->pes_id,
                'se_matricula' => 'SE' . str_pad($pessoa->pes_id, 6, '0', STR_PAD_LEFT)
            ]);
        }
    }
}