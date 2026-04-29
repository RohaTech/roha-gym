<?php

namespace Translation\Front;

use Common\Lang\Lang;
use Helper\Type\Gender\Female;
use Helper\Type\Gender\Male;

class English extends Lang {

    protected static $key = 'en';
    protected static $name = 'english';
    protected static $icon = 'us.png';

    /**
     * The language translations
     *
     * @return array<string, string>
     */
    public static function translations(): array {
        return [
            'notFilledInLanguage' => ' ( Not Filled in English )',
            'english' => 'English',
            'amharic' => 'አማርኛ',
        ];
    }
}
