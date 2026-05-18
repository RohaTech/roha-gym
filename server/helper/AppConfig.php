<?php
/** Gender Keys */
define('MALE', 1);
define('FEMALE', 2);
define('GENDERS', [MALE, FEMALE]);

define('DEFAULT_STORAGE', 'public');

/** Active and Inactive status keys */
define('STATE_ACTIVE', 1);
define('STATE_INACTIVE', 0);
define('STATES', [STATE_ACTIVE, STATE_INACTIVE]);

define('ENGLISH_LANG_KEY', 'en');
define('AMHARIC_LANG_KEY', 'am');
define('LANGUAGES_KEYS', [ENGLISH_LANG_KEY, AMHARIC_LANG_KEY]);

define('USER_STATUS_INACTIVE', 0);
define('USER_STATUS_ACTIVE', 1);
define('USER_STATUS_PENDING',2);
define('USER_STATUSES', [USER_STATUS_ACTIVE, USER_STATUS_INACTIVE, USER_STATUS_PENDING]);
