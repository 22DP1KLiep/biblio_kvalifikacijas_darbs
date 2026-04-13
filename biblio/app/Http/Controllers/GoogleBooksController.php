<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Book;
use Illuminate\Support\Str;


class GoogleBooksController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->query('q', 'latviešu grāmatas');
        $page = max((int)$request->query('page', 1), 1);
        $perPage = min((int)$request->query('perPage', 10), 40);
        $filter = $request->query('filter'); // 'free-ebooks' | 'ebooks' | null

        $params = [
            'q' => $q,
            'langRestrict' => 'lv',
            'maxResults' => $perPage,
            'startIndex' => ($page - 1) * $perPage,
            'printType' => 'books',
        ];
        if ($filter) {
            $params['filter'] = $filter;
        }

        // (Neobligāti) ja gribi API atslēgu, .env: GOOGLE_BOOKS_API_KEY=xxx
        if (config('services.google.books_key')) {
            $params['key'] = config('services.google.books_key');
        }

        $resp = Http::get('https://www.googleapis.com/books/v1/volumes', $params);
        $json = $resp->json();

        // Normalizējam atbildi frontendam
        $items = collect($json['items'] ?? [])->map(function ($item) {
            $v = $item['volumeInfo'] ?? [];
            $authors = isset($v['authors']) ? implode(', ', $v['authors']) : null;
            $thumb = $v['imageLinks']['thumbnail'] ?? null;

            // publicēšanas gads (Google mēdz dot '2001-05-01' vai tikai '2001')
            $year = null;
            if (!empty($v['publishedDate'])) {
                if (preg_match('/\d{4}/', $v['publishedDate'], $m)) {
                    $year = (int)$m[0];
                }
            }

            return [
                'volumeId' => $item['id'] ?? null,
                'title' => $v['title'] ?? null,
                'author' => $authors,
                'description' => $v['description'] ?? null,
                'image' => $thumb, // pilns URL
                'published_year' => $year,
                'previewLink' => $v['previewLink'] ?? null,
                'isFreeEbook' => ($item['saleInfo']['isEbook'] ?? false) && (($item['saleInfo']['saleability'] ?? '') === 'FREE'),
                'language' => $v['language'] ?? null,
            ];
        });

        return response()->json([
            'items' => $items,
            'total' => $json['totalItems'] ?? 0,
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'volumeId' => 'required|string',
        ]);
        $volumeId = $request->input('volumeId');

        $params = [];
        if (config('services.google.books_key')) {
            $params['key'] = config('services.google.books_key');
        }

        $resp = Http::get("https://www.googleapis.com/books/v1/volumes/{$volumeId}", $params);
        if ($resp->failed()) {
            return response()->json(['message' => 'Neizdevās nolasīt grāmatu no Google Books'], 422);
        }
        $data = $resp->json();
        $v = $data['volumeInfo'] ?? [];

        $authors = isset($v['authors']) ? implode(', ', $v['authors']) : null;
        $thumb = $v['imageLinks']['thumbnail'] ?? null;

        $year = null;
        if (!empty($v['publishedDate']) && preg_match('/\d{4}/', $v['publishedDate'], $m)) {
            $year = (int)$m[0];
        }

        // (Pēc izvēles) – novērst dublikātus pēc Google ID (ja pievienosi kolonnas DB)
        // Šobrīd vienkārši pārbaudām pēc title+author
        $existing = Book::where('title', $v['title'] ?? '')
            ->when($authors, fn($q) => $q->where('author', $authors))
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Šī grāmata jau ir tavā bibliotēkā.', 'book' => $existing], 200);
        }

        $book = new Book();
        $book->title = $v['title'] ?? 'Nezināms nosaukums';
        $book->author = $authors;
        $book->description = $v['description'] ?? null;
        $book->published_year = $year;
        // Svarīgi: saglabājam pilnu URL, nevis /relative/path
        $book->image = $thumb; 
        $book->save();

        // (Pēc izvēles) – automātiski piešķirt žanrus, ja vēlēsies (piem., pēc kategorijām)
        // $categories = $v['categories'] ?? [];
        // ...atrodi vai izveido žanrus un piesaisti: $book->genres()->sync([...]);

        return response()->json(['message' => 'Grāmata importēta!', 'book' => $book], 201);
    }
}
