<?php 

// namespace App\Http;

// use Illuminate\Foundation\Http\Kernel as HttpKernel;

// class Kernel extends HttpKernel
// {
//     protected $middleware = [
//         // Globālie middleware
//         \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
//     ];

//     protected $middlewareGroups = [
//         'web' => [
//             \App\Http\Middleware\HandleInertiaRequests::class,
//             \Illuminate\Cookie\Middleware\EncryptCookies::class,
//             \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
//             \Illuminate\Session\Middleware\StartSession::class,
//             \Illuminate\View\Middleware\ShareErrorsFromSession::class,
//             \Illuminate\Routing\Middleware\SubstituteBindings::class,
//         ],

//         'api' => [
//             'throttle:api',
//             \Illuminate\Routing\Middleware\SubstituteBindings::class,
//         ],
//     ];

//     protected $routeMiddleware = [
//         'auth' => \App\Http\Middleware\Authenticate::class,
//         'admin' => \App\Http\Middleware\AdminMiddleware::class, // 👈 pievieno šeit
//     ];
// }
