<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Los datos iniciales se cargan mediante migraciones de seed individuales.
        // No ejecutar db:seed en producción sin especificar --class=NombreSeeder.
    }
}
