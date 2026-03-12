<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            PaisSeeder::class,
            DepartamentoSeeder::class,
            CiudadSeeder::class,
            UserSeeder::class,
            ClienteSeeder::class,
        ]);
    }
}
