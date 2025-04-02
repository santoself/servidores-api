<?php

namespace Database\Seeders;

use App\Models\Unidade;
use Illuminate\Database\Seeder;

class UnidadesTableSeeder extends Seeder
{
    public function run()
    {
        $unidades = [
            ['Secretaria de Educação', 'SEDUC'],
            ['Secretaria de Saúde', 'SES'],
            ['Secretaria de Finanças', 'SEFIN'],
            ['Secretaria de Obras', 'SEOBRA'],
            ['Secretaria de Transportes', 'SETRAN'],
            ['Secretaria de Meio Ambiente', 'SEMA'],
            ['Secretaria de Cultura', 'SECULT'],
            ['Secretaria de Esportes', 'SESP'],
            ['Secretaria de Agricultura', 'SEAGRI'],
            ['Secretaria de Turismo', 'SETUR'],
            ['Secretaria de Segurança', 'SESEG'],
            ['Secretaria de Administração', 'SEAD'],
            ['Secretaria de Planejamento', 'SEPLAN'],
            ['Secretaria de Habitação', 'SEHAB'],
            ['Secretaria de Assistência Social', 'SEAS'],
            ['Secretaria de Ciência e Tecnologia', 'SECT'],
            ['Secretaria de Energia', 'SEENERG'],
            ['Secretaria de Indústria e Comércio', 'SEIC'],
            ['Secretaria de Comunicação', 'SECOM'],
            ['Secretaria de Direitos Humanos', 'SEDH'],
            ['Secretaria de Justiça', 'SEJUS'],
            ['Secretaria de Defesa Civil', 'SEDEC'],
            ['Secretaria de Projetos Especiais', 'SEPE'],
            ['Secretaria de Relações Internacionais', 'SERI'],
            ['Secretaria de Políticas para Mulheres', 'SEPM'],
            ['Secretaria de Juventude', 'SEJUV'],
            ['Secretaria de Igualdade Racial', 'SEIR'],
            ['Secretaria de Pessoa com Deficiência', 'SEPCD'],
            ['Secretaria de Desenvolvimento Econômico', 'SEDE'],
            ['Secretaria de Gestão Pública', 'SEGP']
        ];

        foreach ($unidades as $unidade) {
            Unidade::create([
                'unid_nome' => $unidade[0],
                'unid_sigla' => $unidade[1]
            ]);
        }
    }
}