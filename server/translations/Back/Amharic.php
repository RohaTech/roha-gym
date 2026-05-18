<?php

namespace Translation\Back;

use Common\Lang\Lang;

class Amharic extends Lang {

    protected static $key = 'am';
    protected static $name = 'amharic';
    protected static $icon = 'am.png';

    /**
     * The language translations
     *
     * @return array<string, string>
     */
    public static function translations(): array {
        return [
            'male' => 'ወንድ',
            'female' => 'ሴት',
            'statusPending' => 'በመጠባበቅ ላይ',
            'statusActive' => 'ንቁ',
            'statusInactive' => 'የታግደ',
        ];
    }
}
