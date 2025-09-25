<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    protected $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Get all available languages.
     */
    public function index(): JsonResponse
    {
        $languages = $this->languageService->getAvailableLanguages();
        $current = $this->languageService->getCurrentLanguage();

        return response()->json([
            'success' => true,
            'data' => [
                'languages' => $languages,
                'current' => $current,
                'default' => $this->languageService->getLanguageName('en')
            ]
        ]);
    }

    /**
     * Get current language.
     */
    public function current(): JsonResponse
    {
        $current = $this->languageService->getCurrentLanguage();

        return response()->json([
            'success' => true,
            'data' => $current
        ]);
    }

    /**
     * Set language.
     */
    public function setLanguage(Request $request): JsonResponse
    {
        $request->validate([
            'language' => 'required|string|in:en,sq,de,fr'
        ]);

        $language = $this->languageService->setLanguage($request->language);

        return response()->json([
            'success' => true,
            'message' => 'Language updated successfully',
            'data' => $language
        ]);
    }

    /**
     * Get translations for a specific language.
     */
    public function translations(Request $request): JsonResponse
    {
        $language = $request->get('lang', app()->getLocale());
        
        if (!in_array($language, ['en', 'sq', 'de', 'fr'])) {
            $language = 'en';
        }

        // Load translations
        $translations = [];
        
        // Load app translations
        if (file_exists(resource_path("lang/{$language}/app.php"))) {
            $translations['app'] = include resource_path("lang/{$language}/app.php");
        }

        return response()->json([
            'success' => true,
            'data' => [
                'language' => $language,
                'translations' => $translations
            ]
        ]);
    }

    /**
     * Detect and set language from request.
     */
    public function detect(Request $request): JsonResponse
    {
        $language = $this->languageService->detectLanguage($request);

        return response()->json([
            'success' => true,
            'data' => $language
        ]);
    }

    /**
     * Get language info by code.
     */
    public function info($code): JsonResponse
    {
        $languages = $this->languageService->getAvailableLanguages();
        
        if (!isset($languages[$code])) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $languages[$code]
        ]);
    }
}
