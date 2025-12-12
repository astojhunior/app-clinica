<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;


class Kernel extends HttpKernel
{
    /**
     * The application's route middleware aliases.
     *
     * @var array<string, class-string|string>
     */
     protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,


    ];





}
