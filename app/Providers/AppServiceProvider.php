<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('general_settings')) {
                $settings = \App\Models\GeneralSetting::first();
                if ($settings) {
                    // Share settings with all views
                    \Illuminate\Support\Facades\View::share('general_settings', $settings);

                    if ($settings->school_name) {
                        \Illuminate\Support\Facades\Config::set('adminlte.title', $settings->school_name);
                        \Illuminate\Support\Facades\Config::set('adminlte.logo', '<b>' . substr($settings->school_name, 0, 16) . '</b>');
                    }
                    if ($settings->logo) {
                        // Use asset() with storage prefix to ensure compatibility
                        \Illuminate\Support\Facades\Config::set('adminlte.logo_img', asset('storage/' . $settings->logo));
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore DB errors during setup/migration
        }

        // Handle Dynamic Language Menu, Active State, and Recursive Translation
        \Illuminate\Support\Facades\Event::listen(\JeroenNoten\LaravelAdminLte\Events\BuildingMenu::class, function ($event) {
            $locale = \Illuminate\Support\Facades\App::getLocale();
            
            // We use reflection to access the protected rawMenu property and modify it
            $reflection = new \ReflectionClass($event->menu);
            $property = $reflection->getProperty('rawMenu');
            $property->setAccessible(true);
            $rawMenu = $property->getValue($event->menu);

            // Recursive function to translate items and handle active state
            $processMenu = function (&$items) use (&$processMenu, $locale) {
                foreach ($items as &$item) {
                    // 1. Force Translate text
                    if (isset($item['text']) && is_string($item['text'])) {
                        $key = $item['text'];
                        
                        // Specialized handling for Language Selector (don't translate the dynamic part)
                        if (isset($item['key']) && $item['key'] === 'language_selector') {
                            $langName = ($locale == 'hi') ? 'हिन्दी (Hindi)' : 'English';
                            $item['text'] = (($locale == 'hi') ? 'भाषा' : 'Language') . ': ' . $langName;
                        } else {
                            // Use the global translation helper (it will check hi.json automatically)
                            $item['text'] = __($key);
                        }
                    }

                    // 2. Highlight currently active language
                    if (isset($item['key'])) {
                        if (($item['key'] === 'lang_en' && $locale === 'en') || 
                            ($item['key'] === 'lang_hi' && $locale === 'hi')) {
                            $item['icon'] = 'fas fa-check-circle text-success';
                            $item['active'] = true;
                        }
                    }

                    // 3. Recurse into submenus
                    if (isset($item['submenu']) && is_array($item['submenu'])) {
                        $processMenu($item['submenu']);
                    }
                }
            };

            $processMenu($rawMenu);
            $property->setValue($event->menu, $rawMenu);
        });
    }
}
