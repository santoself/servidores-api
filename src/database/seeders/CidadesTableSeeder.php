<?php

namespace Database\Seeders;

use App\Models\Cidade;
use Illuminate\Database\Seeder;

class CidadesTableSeeder extends Seeder
{
    public function run()
    {
        $estados = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 
                   'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 
                   'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];

        $cidades = [
            'São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Porto Alegre', 'Curitiba',
            'Salvador', 'Fortaleza', 'Recife', 'Brasília', 'Goiânia', 'Manaus', 'Belém',
            'Florianópolis', 'Vitória', 'Cuiabá', 'Campo Grande', 'São Luís', 'Teresina',
            'Natal', 'João Pessoa', 'Aracaju', 'Maceió', 'Porto Velho', 'Rio Branco',
            'Macapá', 'Boa Vista', 'Palmas', 'São José dos Campos', 'Ribeirão Preto', 'Uberlândia'
        ];

        foreach ($cidades as $cidade) {
            Cidade::create([
                'cid_nome' => $cidade,
                'cid_uf' => $estados[array_rand($estados)]
            ]);
        }
    }
}