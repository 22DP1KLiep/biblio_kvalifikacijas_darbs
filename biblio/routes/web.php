<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Kontrolieri
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\BookController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\GoogleBooksController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\ReportController; 
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;


/*
|--------------------------------------------------------------------------
| Publiskās lapas
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => Inertia::render('HomeView'))->name('home');

Route::get('/gramatas', fn() => Inertia::render('GramatasView'))
    ->name('gramatas');

Route::get('/book/{id}', fn($id) => Inertia::render('Book', ['id' => $id]))
    ->name('book');

Route::get('/auth', fn() => Inertia::render('Auth/AuthForm'))
    ->name('auth');

Route::get('/login', fn () => redirect('/auth')); // Pāradresācija

Route::get('/folders/{id}', [FolderController::class, 'show']);


/*
|--------------------------------------------------------------------------
| Autentificēšanās maršruti
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login');

});


/*
|--------------------------------------------------------------------------
| Iziet
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');


/*
|--------------------------------------------------------------------------
| Administratora maršruti
|--------------------------------------------------------------------------
| Pieejams tikai:
| - auth (ielogoti)
| - admin middleware
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin', 'check.restricted'])
    ->prefix('admin')
    ->group(function () {

        // Admin dashboard
        Route::get('/', fn() => Inertia::render('AdminPanelView'))
            ->name('admin.dashboard');

        // User management
        Route::get('/users',
            [\App\Http\Controllers\Admin\UserController::class, 'index']
        )->name('admin.users.index');

        Route::patch('/users/{user}/role',
            [\App\Http\Controllers\Admin\UserController::class, 'updateRole']
        );

        Route::delete('/users/{user}',
            [\App\Http\Controllers\Admin\UserController::class, 'destroy']
        );

        Route::patch('/users/{user}/restrict',
            [\App\Http\Controllers\Admin\UserController::class, 'restrict']
        );

        Route::patch('/users/{user}/unrestrict',
            [\App\Http\Controllers\Admin\UserController::class, 'removeRestriction']
        );

        // Reports management
        Route::get('/reports',
            [\App\Http\Controllers\Admin\ReportController::class, 'index']
        )->name('admin.reports');

        Route::patch('/reports/{report}/resolve',
            [\App\Http\Controllers\Admin\ReportController::class, 'resolve']
        );

    });


/*
|--------------------------------------------------------------------------
| Autenficēta lietotāja maršruti
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.restricted'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Lietotāa profils
    |--------------------------------------------------------------------------
    */
Route::get('/api/users/search', [UserController::class, 'search']);
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('users.show');

    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])
        ->name('users.follow');
    

    Route::get('/users', [UserController::class, 'index']);



    /*
    |--------------------------------------------------------------------------
    | Kabinets
    |--------------------------------------------------------------------------
    */

    Route::get('/kabinets', function () {
        return Inertia::render('KabinetsView');
    });

    Route::middleware('auth')->put('/user/update', [UserController::class, 'update']);

    Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
    Route::post('/user/avatar', [UserController::class, 'uploadAvatar'])->middleware('auth');
    /*
    |--------------------------------------------------------------------------
    | Vēŗtējumi
    |--------------------------------------------------------------------------
    */

    Route::post('/books/{book}/ratings', [RatingController::class, 'store']);
    Route::get('/books/{book}/my-rating', [RatingController::class, 'show']);


    /*
    |--------------------------------------------------------------------------
    | Komentāri
    |--------------------------------------------------------------------------
    */

    Route::post('/books/{book}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    Route::post('/comments/{comment}/like',
        [CommentLikeController::class, 'toggle']
    );

    Route::post('/comments/{comment}/report',
        [ReportController::class, 'store']
    );


    /*
    |--------------------------------------------------------------------------
    | Mapes
    |--------------------------------------------------------------------------
    */

    Route::get('/folders', [FolderController::class, 'index']);
    Route::post('/folders', [FolderController::class, 'store']);
    Route::get('/folders/{folder}/books', [FolderController::class, 'books']);
    Route::post('/folders/{folder}/books', [FolderController::class, 'addBook']);
    Route::delete('/folders/{folder}/books/{book}', [FolderController::class, 'removeBook']);
    Route::delete('/folders/{folder}', [FolderController::class, 'destroy']);
    Route::get('/books', [BookController::class, 'userBooks']);

    // preview (home)
    Route::get('/books/preview', [BookController::class, 'index']);

    // visas sistēmas grāmatas
    Route::get('/books/all', [BookController::class, 'get_all']);

    // lietotāja bibliotēka
    Route::get('/books/user', [BookController::class, 'userBooks']);

    Route::patch('/folders/{folder}/visibility', [FolderController::class, 'toggleVisibility']);


    /*
    |--------------------------------------------------------------------------
    | Čata sistēma
    |--------------------------------------------------------------------------
    */

    Route::get('/chats', [ChatController::class, 'index'])
        ->name('chats.index');

    Route::get('/chats/new', [ChatController::class, 'new'])
        ->name('chats.new');

    Route::get('/chats/start/{user}', [ChatController::class, 'start'])
        ->name('chats.start');

    Route::get('/chats/{conversation}', [ChatController::class, 'show'])
        ->name('chats.show');

    Route::post('/chats/{conversation}/messages',
        [ChatController::class, 'storeMessage']
    )->name('chats.messages.store');


    /*
    |--------------------------------------------------------------------------
    | Paziņojumi
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications',
        [NotificationsController::class, 'index']
    );
    Route::post('/notifications/{id}/read', function ($id) {
    $notification = \App\Models\Notification::findOrFail($id);

    if ($notification->user_id === auth()->id()) {
        $notification->update(['is_read' => true]);
    }

    return response()->noContent();

});
Route::middleware('auth')->get('/api/notifications', function () {
    return \App\Models\Notification::with('fromUser')
        ->where('user_id', auth()->id())
        ->latest()
        ->get();
});

// Dzēst vienu
Route::delete('/notifications/{id}', function ($id) {
    $notification = \App\Models\Notification::findOrFail($id);

    if ($notification->user_id === auth()->id()) {
        $notification->delete();
    }

    return response()->noContent();
});

// Dzēst visas
Route::delete('/notifications', function () {
    \App\Models\Notification::where('user_id', auth()->id())->delete();
    return response()->noContent();
});
});


/*
|--------------------------------------------------------------------------
| Publiskie API
|--------------------------------------------------------------------------
*/
Route::get('/get/all/books', [BookController::class, 'get_all']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/books/{book}/ratings', [RatingController::class, 'index']);
Route::get('/books/{book}/comments', [CommentController::class, 'index']);

Route::get('/api/genres', fn() => \App\Models\Genre::all());

Route::get('/google-books/search', [GoogleBooksController::class, 'search']);

Route::middleware('auth')->post('/google-books/import',
    [GoogleBooksController::class, 'import']
);