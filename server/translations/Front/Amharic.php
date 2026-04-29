<?php

namespace Translation\Front;

use Common\Lang\Lang;
use Helper\Type\Gender\Female;
use Helper\Type\Gender\Male;

class Amharic extends Lang {

    protected static $key = 'am';
    protected static $name = 'amharic';
    protected static $icon = 'et.png';

    /**
     * The language translations
     *
     * @return array<string, string>
     */
    public static function translations(): array {
        return [
            'notFilledInLanguage' => '( በአማርኛ አልተሞላም  )',
            'english' => 'English',
            'amharic' => 'አማርኛ',
        ];
    }
}
