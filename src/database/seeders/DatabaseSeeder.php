<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            CidadesTableSeeder::class,
            UnidadesTableSeeder::class,
            PessoasTableSeeder::class,
            EnderecosTableSeeder::class,
            ServidoresEfetivosTableSeeder::class,
            ServidoresTemporariosTableSeeder::class,
            LotacoesTableSeeder::class,
            FotosPessoasTableSeeder::class,
        ]);
    }
}
