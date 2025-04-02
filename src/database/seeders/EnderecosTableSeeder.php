<?php

namespace Database\Seeders;

use App\Models\Cidade;
use App\Models\Endereco;
use Illuminate\Database\Seeder;

class EnderecosTableSeeder extends Seeder
{
    public function run()
    {
        $tiposLogradouro = ['Rua', 'Avenida', 'Travessa', 'Alameda', 'Praça', 'Rodovia', 'Viela'];
        $logradouros = [
            'das Flores', 'Brasil', 'Paulista', 'Rio Branco', '9 de Julho', 'Vasco da Gama',
            'São João', 'Amazonas', 'Santo Antônio', 'Getúlio Vargas', '15 de Novembro',
            '7 de Setembro', 'Tiradentes', 'República', 'Independência', 'Liberdade',
            'Constitucionalistas', 'Nove de Julho', '23 de Maio', 'Ipiranga', 'Anhangabaú',
            'Prestes Maia', 'São Bento', 'Quinze de Novembro', 'Direita', 'São José',
            'Barão de Itapetininga', 'XV de Novembro', 'Boavista', 'Líbero Badaró'
        ];

        $bairros = [
            'Centro', 'Vila Mariana', 'Moema', 'Pinheiros', 'Jardins', 'Bela Vista',
            'Consolação', 'Paraíso', 'Santa Cecília', 'Perdizes', 'Vila Madalena',
            'Itaim Bibi', 'Jardim Paulista', 'Higienópolis', 'Vila Olímpia',
            'Brooklin', 'Campo Belo', 'Morumbi', 'Santo Amaro', 'Lapa',
            'Freguesia do Ó', 'Casa Verde', 'Santana', 'Tucuruvi', 'Jaçanã',
            'Vila Guilherme', 'Vila Maria', 'Vila Formosa', 'Penha', 'Artur Alvim'
        ];

        $cidades = Cidade::all();

        foreach ($cidades as $cidade) {
            for ($i = 0; $i < 2; $i++) {
                Endereco::create([
                    'end_tipo_logradouro' => $tiposLogradouro[array_rand($tiposLogradouro)],
                    'end_logradouro' => $logradouros[array_rand($logradouros)],
                    'end_numero' => rand(1, 1000),
                    'end_bairro' => $bairros[array_rand($bairros)],
                    'cid_id' => $cidade->cid_id
                ]);
            }
        }
    }
}