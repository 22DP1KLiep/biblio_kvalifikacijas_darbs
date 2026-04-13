<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Fantāzija',
            'Piedzīvojumi',
            'Distopija',
            'Klasika',
            'Vēsturiskais romāns',
            'Bērnu literatūra',
            'Jauniešu literatūra',
            'Psiholoģisks romāns',
            'Dzeja',
            'Romantika',
            'Detektīvs',
            'Humors',
            'Filozofija',
            'Biogrāfija',
            'Trilleris',
            'Dramatisks romāns',
        ];

        foreach ($genres as $name) {
            Genre::create(['name' => $name]);
        }
    }
}
