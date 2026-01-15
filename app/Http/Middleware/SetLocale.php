<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    private const array SUPPORTED_LOCALES = ['en', 'hu'];

    private const string LOCALE_COOKIE_NAME = 'locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie(self::LOCALE_COOKIE_NAME);

        if (is_string($locale) && in_array($locale, self::SUPPORTED_LOCALES, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
