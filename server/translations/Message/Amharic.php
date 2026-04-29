<?php

namespace Translation\Message;

use Common\Lang\Lang;

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
            'not_found' => 'ምንም አልተገኘም',
            'user_not_found' => 'ተጠቃሚ አልተገኘም',
            'email_required' => 'ኢሜይል ያስፈልጋል',
            'invalid_email' => 'ልክ ያልሆነ ኢሜይል ተሰጥቷል',
            'enter_your_password' => 'እባክዎ የይለፍ ቃልዎን ያስገቡ',
            'invalid_credentials' => 'ልክ ያልሆኑ መረጃዎች',
            'too_many_login_attempts' => 'በጣም ብዙ የመግቢያ ሙከራዎች',
            'successfully_loggedout' => 'በተሳካ ሁኔታ ወጥተዋል',
            'forbidden' => 'ይህን ምንጭ ለመድረስ አይፈቀድልዎትም',
            'unauthorized' => 'ይህን ምንጭ ለመድረስ ስልጣን የለዎትም',
            'unable_to_login_please_contact_administrator' => 'መግባት አልተቻለም፣ እባክዎ አስተዳዳሪውን ያነጋግሩ',
        ];
    }
}
