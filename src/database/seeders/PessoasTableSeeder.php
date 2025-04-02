<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PessoasTableSeeder extends Seeder
{
    public function run()
    {
        $nomesMasculinos = [
            'João', 'Pedro', 'Lucas', 'Mateus', 'Gabriel', 'Daniel', 'Marcos', 'Carlos', 
            'Antônio', 'José', 'Francisco', 'Paulo', 'Ricardo', 'Fernando', 'Eduardo',
            'Roberto', 'Rafael', 'Bruno', 'Felipe', 'André', 'Thiago', 'Marcelo', 'Márcio',
            'Gustavo', 'Rodrigo', 'Alexandre', 'Vitor', 'Leonardo', 'Diego', 'Sérgio'
        ];

        $nomesFemininos = [
            'Maria', 'Ana', 'Laura', 'Sofia', 'Isabella', 'Manuela', 'Alice', 'Julia', 
            'Valentina', 'Helena', 'Luiza', 'Lara', 'Beatriz', 'Mariana', 'Gabriela',
            'Carolina', 'Amanda', 'Rafaela', 'Fernanda', 'Patrícia', 'Camila', 'Bianca',
            'Vanessa', 'Letícia', 'Cláudia', 'Tatiana', 'Daniela', 'Adriana', 'Renata', 'Cristina'
        ];

        $sobrenomes = [
            'Silva', 'Santos', 'Oliveira', 'Souza', 'Rodrigues', 'Ferreira', 'Alves', 
            'Pereira', 'Lima', 'Gomes', 'Costa', 'Ribeiro', 'Martins', 'Carvalho', 
            'Almeida', 'Lopes', 'Soares', 'Fernandes', 'Vieira', 'Barbosa', 'Rocha', 
            'Dias', 'Nascimento', 'Andrade', 'Moreira', 'Nunes', 'Marques', 'Mendes', 
            'Freitas', 'Xavier'
        ];

        for ($i = 0; $i < 60; $i++) {
            $sexo = ($i % 2 == 0) ? 'Masculino' : 'Feminino';
            $nomes = ($sexo == 'Masculino') ? $nomesMasculinos : $nomesFemininos;
            
            Pessoa::create([
                'pes_nome' => $nomes[array_rand($nomes)] . ' ' . $sobrenomes[array_rand($sobrenomes)],
                'pes_data_nascimento' => Carbon::now()->subYears(rand(20, 60))->subDays(rand(0, 365)),
                'pes_sexo' => $sexo,
                'pes_mae' => $nomesFemininos[array_rand($nomesFemininos)] . ' ' . $sobrenomes[array_rand($sobrenomes)],
                'pes_pai' => $nomesMasculinos[array_rand($nomesMasculinos)] . ' ' . $sobrenomes[array_rand($sobrenomes)]
            ]);
        }
    }
}