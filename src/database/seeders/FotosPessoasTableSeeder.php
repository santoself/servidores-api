<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\FotoPessoa;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FotosPessoasTableSeeder extends Seeder
{
    public function run()
    {
        $pessoas = Pessoa::all();

        foreach ($pessoas as $pessoa) {
            // 50% de chance de ter foto
            if (rand(0, 1)) {
                $hash = md5($pessoa->pes_nome . $pessoa->pes_id);
                $extensoes = ['jpg', 'png', 'jpeg'];
                
                FotoPessoa::create([
                    'pes_id' => $pessoa->pes_id,
                    'fp_data' => Carbon::now()->subDays(rand(1, 365)),
                    'fp_bucket' => env('MINIO_BUCKET', 'servidores'),
                    'fp_hash' => $hash . '.' . $extensoes[array_rand($extensoes)]
                ]);
            }
        }
    }
}