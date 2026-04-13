<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Folder;
use App\Models\User;

class FolderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // vai find(1) ja konkrēts ID

        if (!$user) {
            echo "❌ Lietotājs nav atrasts. Pārliecinies, ka ir lietotājs datubāzē.\n";
            return;
        }

        Folder::firstOrCreate(['user_id' => $user->id, 'name' => 'Izlasīts']);
        Folder::firstOrCreate(['user_id' => $user->id, 'name' => 'Plānoju lasīt']);

        echo "✅ Mapes veiksmīgi pievienotas!\n";
    }
}
