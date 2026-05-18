<?php

namespace Translation\Back;

use Common\Lang\Lang;

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
            'male' => 'Male',
            'female' => 'Female',
            'statusActive' => 'Active',
            'statusInactive' => 'Inactive',
            'statusPending' => 'Pending',
        ];
    }
}
