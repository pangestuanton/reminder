<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['required', Rule::in(['light', 'dark', 'system'])],
        ]);

        $request->user()->update(['theme_preference' => $validated['theme']]);

        return response()->json(['success' => true, 'theme' => $validated['theme']]);
    }
}
