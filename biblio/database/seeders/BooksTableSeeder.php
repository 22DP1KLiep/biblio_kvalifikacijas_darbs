<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Facades\DB;

class BooksTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('book_genre')->truncate();
        DB::table('books')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $books = [
            [
                'title' => 'Harijs Poters un Filozofu akmens',
                'author' => 'Dž. K. Roulinga',
                'description' => 'Pirmā grāmata par burvju skolu Cūkkārpu.',
                'image' => 'img/harijspotersunfilozofuakmens_978-9934-0-8149-1_1.jpg',
                'published_year' => 1997,
                'genres' => ['Fantāzija', 'Piedzīvojumi', 'Jauniešu literatūra']
            ],
            [
                'title' => 'Dzīvnieku ferma',
                'author' => 'Džordžs Orvels',
                'description' => 'Allegorisks stāsts par varu un sabiedrību.',
                'image' => 'img/dzivnieku_ferma.jpg',
                'published_year' => 1945,
                'genres' => ['Distopija', 'Klasika', 'Filozofija']
            ],
            [
                'title' => 'Mazais princis',
                'author' => 'Antuāns de Sent-Ekziperī',
                'description' => 'Poētisks stāsts par draudzību, dzīvi un mīlestību.',
                'image' => 'img/mazais_princis.jpg',
                'published_year' => 1943,
                'genres' => ['Bērnu literatūra', 'Filozofija', 'Fantāzija']
            ],
            [
                'title' => 'Alķīmiķis',
                'author' => 'Paulu Koelju',
                'description' => 'Garīgs ceļojums par sapņu piepildīšanu.',
                'image' => 'img/alkimikis.jpg',
                'published_year' => 1988,
                'genres' => ['Filozofija', 'Psiholoģisks romāns']
            ],
            [
                'title' => 'Puika svītrainajā pidžamā',
                'author' => 'Džons Boins',
                'description' => 'Draudzība starp diviem zēniem holokausta laikā.',
                'image' => 'img/puisen_svitraina_pidzamma.jpg',
                'published_year' => 2006,
                'genres' => ['Vēsturiskais romāns', 'Dramatisks romāns']
            ],
            [
                'title' => 'Noslēpumu sala',
                'author' => 'Žils Verns',
                'description' => 'Piedzīvojumu romāns par izdzīvošanu salā.',
                'image' => 'img/noslepumu_sala.jpg',
                'published_year' => 1874,
                'genres' => ['Piedzīvojumi', 'Klasika']
            ],
            [
                'title' => 'Mātes piens',
                'author' => 'Nora Ikstena',
                'description' => 'Stāsts par sievietes dzīvi padomju laikā.',
                'image' => 'img/mates_piens.jpg',
                'published_year' => 2015,
                'genres' => ['Psiholoģisks romāns', 'Dramatisks romāns']
            ],
            [
                'title' => 'Mazās sievietes',
                'author' => 'Lūiza Meja Alokta',
                'description' => 'Māsas pieaug kara laika Amerikā.',
                'image' => 'img/lasitprieks-mazas-sievietes-m-v.webp',
                'published_year' => 1868,
                'genres' => ['Klasika', 'Romantika']
            ],
            [
                'title' => 'Velniņi',
                'author' => 'Rūdolfs Blaumanis',
                'description' => 'Interesants pastāsts par divu velnēnu gaitām virszemē.',
                'image' => 'img/velnini.jpg',
                'published_year' => 1901,
                'genres' => ['Bērnu literatūra', 'Piedzīvojumi']
            ],
            [
                'title' => 'Baltā grāmata',
                'author' => 'Jānis Jaunsudrabiņš',
                'description' => 'Bērnības atmiņas un lauku dzīves apraksti.',
                'image' => 'img/balta_gramata.jpg',
                'published_year' => 1914,
                'genres' => ['Bērnu literatūra', 'Klasika']
            ],
            [
                'title' => 'Harijs Poters un Noslēpumu kambaris',
                'author' => 'Dž. K. Roulinga',
                'description' => 'Harijs atgriežas Cūkkārpā, lai atklātu skolas noslēpumus.',
                'image' => 'img/noslepumu_kambaris.jpg',
                'published_year' => 1998,
                'genres' => ['Fantāzija', 'Piedzīvojumi', 'Jauniešu literatūra']
            ],
            [
                'title' => 'Harijs Poters un Azkabanas gūsteknis',
                'author' => 'Dž. K. Roulinga',
                'description' => 'Harijs uzzina par Siriju Bleku un viņa pagātni.',
                'image' => 'img/azkabanas_gusteknis.jpg',
                'published_year' => 1999,
                'genres' => ['Fantāzija', 'Piedzīvojumi', 'Jauniešu literatūra']
            ],
            [
                'title' => 'Harijs Poters un Uguns biķeris',
                'author' => 'Dž. K. Roulinga',
                'description' => 'Harijs piedalās Trim burvju sacensībās.',
                'image' => 'img/uguns_bikeris.jpg',
                'published_year' => 2000,
                'genres' => ['Fantāzija', 'Piedzīvojumi', 'Jauniešu literatūra']
            ],
            [
                'title' => 'Harijs Poters un Fēniksa ordenis',
                'author' => 'Dž. K. Roulinga',
                'description' => 'Harijs saskaras ar Voldemorta atgriešanos un izveido slepenu grupu.',
                'image' => 'img/feniksa_ordenis.jpg',
                'published_year' => 2003,
                'genres' => ['Fantāzija', 'Piedzīvojumi', 'Jauniešu literatūra']
            ],
            [
                'title' => 'Harijs Poters un Jauktasiņu princis',
                'author' => 'Dž. K. Roulinga',
                'description' => 'Harijs uzzina par Voldemorta pagātni un dvēseles noslēpumiem.',
                'image' => 'img/jauktasinu_princis.jpg',
                'published_year' => 2005,
                'genres' => ['Fantāzija', 'Piedzīvojumi', 'Jauniešu literatūra']
            ],
            [
                'title' => 'Harijs Poters un Nāves dāvesti',
                'author' => 'Dž. K. Roulinga',
                'description' => 'Noslēdzošā cīņa starp labo un ļauno.',
                'image' => 'img/naves_davesti.webp',
                'published_year' => 2007,
                'genres' => ['Fantāzija', 'Piedzīvojumi', 'Jauniešu literatūra']
            ],
            [
                'title' => 'Bada spēles',
                'author' => 'Sūzena Kolinsa',
                'description' => 'Katnisa Everdīna piedalās nāvējošās sacensībās.',
                'image' => 'img/bada_speles.jpg',
                'published_year' => 2008,
                'genres' => ['Distopija', 'Jauniešu literatūra', 'Trilleris']
            ],
            [
                'title' => 'Spēle ar uguni',
                'author' => 'Sūzena Kolinsa',
                'description' => 'Katnisa tiek ierauta dumpja simbolikā.',
                'image' => 'img/spele_ar_uguni.jpg',
                'published_year' => 2009,
                'genres' => ['Distopija', 'Jauniešu literatūra', 'Trilleris']
            ],
            [
                'title' => 'Zobgaļsīlis',
                'author' => 'Sūzena Kolinsa',
                'description' => 'Revolūcijas kulminācija pret Kapitoliju.',
                'image' => 'img/zobgalsilis.jpg',
                'published_year' => 2010,
                'genres' => ['Distopija', 'Jauniešu literatūra', 'Trilleris']
            ],
            [
                'title' => 'Pieci pirksti',
                'author' => 'Māra Zālīte',
                'description' => 'Romāns par padomju Latvijas bērnību.',
                'image' => 'img/pieci_pirksti.webp',
                'published_year' => 2014,
                'genres' => ['Vēsturiskais romāns', 'Psiholoģisks romāns']
            ],
            [
                'title' => 'Svina garša',
                'author' => 'Māris Bērziņš',
                'description' => 'Romāns par dzīvi padomju okupācijas laikā.',
                'image' => 'img/svina_garsa.webp',
                'published_year' => 2015,
                'genres' => ['Dramatisks romāns', 'Vēsturiskais romāns']
            ],
            [
                'title' => 'Stikli',
                'author' => 'Inga Gaile',
                'description' => 'Psiholoģisks romāns par ģimenes attiecībām un vēsturi.',
                'image' => 'img/stikli.webp',
                'published_year' => 2013,
                'genres' => ['Psiholoģisks romāns', 'Dramatisks romāns']
            ],
            [
                'title' => 'Sarkanie bērni',
                'author' => 'Inga Žolude',
                'description' => 'Filigrāni veidots stāsts par sievietes iekšējo pasauli.',
                'image' => 'img/sarkanie_berni.jpg',
                'published_year' => 2010,
                'genres' => ['Psiholoģisks romāns']
            ],
            [
                'title' => 'Virsnieku sievas',
                'author' => 'Andra Manfelde',
                'description' => 'Romāns par sieviešu likteņiem padomju armijas ēnā.',
                'image' => 'img/virsnieka sieva.jpg',
                'published_year' => 2012,
                'genres' => ['Dramatisks romāns', 'Vēsturiskais romāns']
            ]
        ];


        foreach ($books as $bookData) {
            // Atjaunina vai izveido grāmatu pēc title
            $book = Book::updateOrCreate(
                ['title' => $bookData['title']],
                [
                    'author' => $bookData['author'],
                    'description' => $bookData['description'],
                    'image' => $bookData['image'],
                    'published_year' => $bookData['published_year'],
                ]
            );

            // Atrod žanru ID un sinhronizē ar šo grāmatu
            $genreIds = Genre::whereIn('name', $bookData['genres'])->pluck('id')->toArray();
            $book->genres()->sync($genreIds);
        }
    }
}
