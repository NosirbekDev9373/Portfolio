<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class RootController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $supported = config('portfolio.locales.supported');
        $locale = session('locale', config('portfolio.locales.default'));

        if (! in_array($locale, $supported, true)) {
            $locale = config('portfolio.locales.default');
        }

        return redirect()->route('home', ['locale' => $locale]);
    }
}
