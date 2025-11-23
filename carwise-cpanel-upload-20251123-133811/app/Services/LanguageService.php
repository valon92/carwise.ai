<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class LanguageService
{
    /**
     * Available languages.
     */
    protected $languages = [
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'flag' => '🇺🇸',
            'code' => 'en'
        ],
        'sq' => [
            'name' => 'Albanian',
            'native_name' => 'Shqip',
            'flag' => '🇦🇱',
            'code' => 'sq'
        ],
        'de' => [
            'name' => 'German',
            'native_name' => 'Deutsch',
            'flag' => '🇩🇪',
            'code' => 'de'
        ],
        'fr' => [
            'name' => 'French',
            'native_name' => 'Français',
            'flag' => '🇫🇷',
            'code' => 'fr'
        ]
    ];

    /**
     * Default language.
     */
    protected $defaultLanguage = 'en';

    /**
     * Get all available languages.
     */
    public function getAvailableLanguages()
    {
        return $this->languages;
    }

    /**
     * Get current language.
     */
    public function getCurrentLanguage()
    {
        $current = App::getLocale();
        return $this->languages[$current] ?? $this->languages[$this->defaultLanguage];
    }

    /**
     * Set language.
     */
    public function setLanguage($languageCode)
    {
        if (!isset($this->languages[$languageCode])) {
            $languageCode = $this->defaultLanguage;
        }

        App::setLocale($languageCode);
        Session::put('language', $languageCode);
        
        // Set cookie for 1 year
        Cookie::queue('language', $languageCode, 525600);
        
        return $this->languages[$languageCode];
    }

    /**
     * Get language from request or session.
     */
    public function detectLanguage($request = null)
    {
        // Check if language is set in request
        if ($request && $request->has('lang')) {
            $lang = $request->get('lang');
            if (isset($this->languages[$lang])) {
                return $this->setLanguage($lang);
            }
        }

        // Check session
        if (Session::has('language')) {
            $lang = Session::get('language');
            if (isset($this->languages[$lang])) {
                App::setLocale($lang);
                return $this->languages[$lang];
            }
        }

        // Check cookie
        if (Cookie::has('language')) {
            $lang = Cookie::get('language');
            if (isset($this->languages[$lang])) {
                App::setLocale($lang);
                Session::put('language', $lang);
                return $this->languages[$lang];
            }
        }

        // Check browser language
        if ($request) {
            $acceptLanguage = $request->header('Accept-Language');
            if ($acceptLanguage) {
                $browserLanguages = $this->parseAcceptLanguage($acceptLanguage);
                foreach ($browserLanguages as $lang) {
                    if (isset($this->languages[$lang])) {
                        return $this->setLanguage($lang);
                    }
                }
            }
        }

        // Default language
        App::setLocale($this->defaultLanguage);
        return $this->languages[$this->defaultLanguage];
    }

    /**
     * Parse Accept-Language header.
     */
    protected function parseAcceptLanguage($acceptLanguage)
    {
        $languages = [];
        $parts = explode(',', $acceptLanguage);
        
        foreach ($parts as $part) {
            $part = trim($part);
            if (strpos($part, ';') !== false) {
                $part = substr($part, 0, strpos($part, ';'));
            }
            
            // Extract language code (e.g., 'en-US' -> 'en')
            if (strpos($part, '-') !== false) {
                $part = substr($part, 0, strpos($part, '-'));
            }
            
            $languages[] = $part;
        }
        
        return $languages;
    }

    /**
     * Get translation for a key.
     */
    public function trans($key, $replace = [], $locale = null)
    {
        if ($locale && isset($this->languages[$locale])) {
            $originalLocale = App::getLocale();
            App::setLocale($locale);
            $translation = __($key, $replace);
            App::setLocale($originalLocale);
            return $translation;
        }
        
        return __($key, $replace);
    }

    /**
     * Get language name by code.
     */
    public function getLanguageName($code)
    {
        return $this->languages[$code]['name'] ?? $code;
    }

    /**
     * Get language native name by code.
     */
    public function getLanguageNativeName($code)
    {
        return $this->languages[$code]['native_name'] ?? $code;
    }

    /**
     * Get language flag by code.
     */
    public function getLanguageFlag($code)
    {
        return $this->languages[$code]['flag'] ?? '🌐';
    }

    /**
     * Check if language is RTL.
     */
    public function isRTL($code = null)
    {
        $code = $code ?? App::getLocale();
        // Add RTL languages here if needed
        $rtlLanguages = ['ar', 'he', 'fa', 'ur'];
        return in_array($code, $rtlLanguages);
    }

    /**
     * Get language direction.
     */
    public function getDirection($code = null)
    {
        return $this->isRTL($code) ? 'rtl' : 'ltr';
    }
}
