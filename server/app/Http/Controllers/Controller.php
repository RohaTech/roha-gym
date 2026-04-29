<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Model;
use Translation\FrontLang;

abstract class Controller {

   
    /**
     * Get the current language from the request
     *
     * @param Request $request
     * @return string
     */
    public static function getCurrentLanguage($request): string {
        $language = $request->header('lang');
        if (!in_array($language, FrontLang::getAvailableLangKeys())) {
            $language = FrontLang::getDefaultLanguage();
        }
        return $language;
    }

}
