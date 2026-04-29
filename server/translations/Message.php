<?php

namespace Translation;

use Translation\Message\English;
use Translation\Message\Amharic;
use Common\Lang\BackLang as Lang;

class Message extends Lang {

    /**
     * Available Language
     *
     * @var array<int, class> $langs
     */
    protected static $langs = [
        English::class,
        Amharic::class,
    ];
}