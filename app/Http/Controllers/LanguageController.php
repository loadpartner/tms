<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    public function index(): JsonResponse
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
                'native_name' => 'Español',
            ],
            [
                'code' => 'fr',
                'name' => 'French',
                'native_name' => 'Français',
            ],
            [
                'code' => 'ct',
                'name' => 'Custom',
                'native_name' => 'Custom',
            ],
            // Add more languages as needed
        ];

        return response()->json($languages);
    }
} 