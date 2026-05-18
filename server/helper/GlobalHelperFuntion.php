<?php

use App\Faker\AmharicTranslator;
use App\Http\Controllers\User\UserController;
use App\Models\Academic\School\School;
use App\Models\Admin\Region;
use App\Models\Admin\Woreda;
use App\Models\Admin\Zone;
use App\Models\Permission\Permission;
use App\Models\Role\UserPermissionOverride;
use App\Models\Role\UserRoleBinding;
use App\Models\User;
use Helper\Cache\RoleCacheHandler;
use Helper\Permission\PermissionActionHelper;
use Helper\Type\Scope\Scope;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Module\Teacher\App\Models\TeacherInformation\EmploymentRecord;
use Module\Teacher\App\Models\TeacherInformation\TeacherDetail;
use Translation\FrontLang;

if (!function_exists('autoTranslate')) {

    function autoTranslate(string $text, string $lang): string {
        $lang = strtolower($lang);
        if ($lang === 'en' || trim($text) === '') {
            return $text;
        }

        $url = "https://translate.googleapis.com/translate_a/single"
            . "?client=gtx&sl=en&tl={$lang}&dt=t&q=" . rawurlencode($text);

        if (app()->runningInConsole()) {
            echo "[autoTranslate] {$text} → {$lang}\n";
        }

        $response = @file_get_contents($url);
        if ($response === false) {
            return $text;
        }

        $json = json_decode($response, true);
        if (!is_array($json) || !isset($json[0])) {
            return $text;
        }

        $translated = '';
        foreach ($json[0] as $segment) {
            if (isset($segment[0])) {
                $translated .= $segment[0];
            }
        }

        return trim($translated) !== '' ? trim($translated) : $text;
    }
}


if (!function_exists('getCurrentLanguage')) {
    /**
     * Get the current language from the request
     *
     * @param Request $request
     * @return string
     */
    function getCurrentLanguage($request): string {
        $language = $request->header('lang')
            ?? $request->header('language')
            ?? $request->get('lang')
            ?? $request->get('language');
        $language = is_string($language) ? strtolower(trim($language)) : '';

        if ($language && !in_array($language, FrontLang::getAvailableLangKeys(), true)) {
            if (str_starts_with($language, 'am')) {
                $language = 'am';
            } elseif (str_starts_with($language, 'en')) {
                $language = 'en';
            }
        }

        if (!$language || !in_array($language, FrontLang::getAvailableLangKeys(), true)) {
            $language = FrontLang::getDefaultLanguage();
        }

        return $language;
    }
}

if (!function_exists('getLocalizedValue')) {
    /**
     * Get localized value from a JSON-casted array with fallback.
     *
     * @param array|null $array
     * @param string|null $language
     * @return string|null
     */
    function getLocalizedValue($array, $language = null): ?string {
        $language = $language ?? getCurrentLanguage(request());

        if (!is_array($array) || empty($array)) {
            return null;
        }
        if (isset($array[$language])) {
            return $array[$language];
        }
        $defaultLang = FrontLang::getDefaultLanguage();
        if (isset($array[$defaultLang])) {
            return $array[$defaultLang];
        }
        return reset($array);
    }
}

if (!function_exists('updateLangField')) {
    /**
     * Merge or update a language-keyed array field.
     * @param array|null $existing
     * @param string $lang
     * @param mixed $value
     * @return array|null
     */

    function updateLangField(?array $existing, string $lang, $value, bool $canBeNull = false): ?array {
        $existing = $existing ?? [];

        if ($value === null) {
            if ($canBeNull) {
                unset($existing[$lang]);
                return $existing;
            }
            return $existing;
        }

        $existing[$lang] = $value;
        return $existing;
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug($name, $modelClass, $field = 'slug'): string {
        $slug = Str::slug($name);
        if (empty($slug)) {
            $slug = Str::uuid();
        }
        $count = 0;

        while ($modelClass::withTrashed()->withoutGlobalScopes()->where($field, $slug)->exists()) {
            $count++;
            $slugBase = empty(Str::slug($name)) ? 'item-' . Str::random(6) : Str::slug($name);
            $slug = $slugBase . '-' . $count;
        }

        return $slug;
    }
}

if (!function_exists('getFileUrl')) {
    /**
     * GEt a file url from a given storage.
     * @param string $storage
     * @param string $fileName
     * @return mixed
     */
    function getFileUrl(?string $storage, ?string $fileName): string | null {
        if (empty($storage) || !$storage) {
            return DEFAULT_STORAGE;
        }

        if (empty($fileName) || !$fileName) {
            return null;
        }

        try {
            return Storage::disk($storage)->url($fileName) ?? null;
        } catch (Exception $e) {
            return null;
        }
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug($name, $modelClass, $field = 'slug'): string {
        $slug = Str::slug($name);
        if (empty($slug)) {
            $slug = Str::uuid();
        }
        $count = 0;

        while ($modelClass::where($field, $slug)->exists()) {
            $count++;
            $slugBase = empty(Str::slug($name)) ? 'item-' . Str::random(6) : Str::slug($name);
            $slug = $slugBase . '-' . $count;
        }

        return $slug;
    }
}

if (!function_exists('generateRandomNumber')) {
    /**
     * Get a random number of a specific length
     * @param int $length
     * @return string
     */
    function generateRandomNumber(int $length): string {
        return Str::padLeft((string) random_int(0, (10 ** $length) - 1), $length, '0');
    }
}

if (!function_exists('formatToTwoDecimals')) {
    /**
     * echo formatToTwoDecimals(5);        // 5.00
     * echo formatToTwoDecimals(5.1);      // 5.10
     * echo formatToTwoDecimals(5.6789);   // 5.68
     */
    function formatToTwoDecimals(float $number): string {
        return number_format($number, 2, '.', '');
    }
}



if (!function_exists('consoleInfo')) {
    function consoleInfo(string $message): void {
        if (app()->runningInConsole()) {
            echo "\033[1;32m {$message}\033[0m\n";
        }
    }
}

if (!function_exists('consoleSuccess')) {
    function consoleSuccess(string $message): void {
        if (app()->runningInConsole()) {
            echo "\033[1;32m✅ {$message}\033[0m\n";
        }
    }
}

if (!function_exists('consoleError')) {
    function consoleError(string $message): void {
        if (app()->runningInConsole()) {
            echo "\033[1;31m❌ {$message}\033[0m\n";
        }
    }
}

if (!function_exists('consoleWarn')) {
    function consoleWarn(string $message): void {
        if (app()->runningInConsole()) {
            echo "\033[1;33m⚠️  {$message}\033[0m\n";
        }
    }
}


if (!function_exists('mask_value')) {
    /**
     * Universal masking function (email, phone, string).
     */
    function mask_value(
        string $value,
        string $type = 'string',
        array $options = []
    ): string {

        $defaults = [
            'email_visible' => 2,
            'phone_visible' => 3,
            'string_start' => 2,
            'string_end' => 2,
        ];

        $opts = array_merge($defaults, $options);

        // Small helper for repeating mask
        $mask = fn ($len) => str_repeat('*', max(0, $len));

        if ($type === 'email') {
            if (!str_contains($value, '@')) {
                return $value;
            }

            [$name, $domain] = explode('@', $value);

            $visible = min($opts['email_visible'], strlen($name));
            $hidden = $mask(strlen($name) - $visible);

            return substr($name, 0, $visible) . $hidden . '@' . $domain;
        }

        if ($type === 'phone') {
            $digits = preg_replace('/\D/', '', $value);

            if (strlen($digits) <= $opts['phone_visible']) {
                return $value;
            }

            return $mask(strlen($digits) - $opts['phone_visible'])
                . substr($digits, -$opts['phone_visible']);
        }

        $len = strlen($value);
        $start = $opts['string_start'];
        $end = $opts['string_end'];

        if ($len <= ($start + $end)) {
            return $value;
        }

        return substr($value, 0, $start)
            . $mask($len - ($start + $end))
            . substr($value, -$end);
    }
}




if (!function_exists('uploadSingleFile')) {
    function uploadSingleFile(UploadedFile $file, string $directory = 'uploads', string $disk = 'public'): string {
        $originalName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs($directory, $originalName, $disk);
    }
}


if (!function_exists('downloadFile')) {
    function downloadFile(string $path, string $name, string $disk = 'public') {
        return Storage::disk($disk)->download($path, $name);
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile(string $path, string $disk = 'public'): bool {
        return Storage::disk($disk)->delete($path);
    }
}

if (!function_exists('deleteMultipleFiles')) {
    function deleteMultipleFiles(array $paths, string $disk = 'public'): bool {
        return Storage::disk($disk)->delete($paths);
    }
}

if (!function_exists('applyMultipleFilters')) {
    /**
     * Apply multiple filters to a query builder.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function applyMultipleFilters($query, array $filters) {
        foreach ($filters as $field => $values) {
            $prepared = is_array($values) ? $values : explode(',', (string) $values);
            $prepared = array_filter(array_map('trim', $prepared), fn ($v) => $v !== '');
            if ($prepared) {
                $query->whereIn($field, $prepared);
            }
        }
        return $query;
    }
}

if (!function_exists('cleanupStoredFiles')) {
    function cleanupStoredFiles(array|string $paths, string $disk = DEFAULT_STORAGE): void {
        $paths = is_array($paths) ? $paths : array_filter([(string) $paths]);

        if (empty($paths)) {
            return;
        }

        $storage = Storage::disk($disk);

        foreach ($paths as $path) {
            if ($path && $storage->exists($path)) {
                $storage->delete($path);
            }
        }
    }


    if (!function_exists('isDropdownEnabled')) {
        /**
         * Check if the dropdown parameter in the request is enabled.
         *
         * @return bool
         */
        function isDropdownEnabled(): bool {
            return request()->boolean('dropdown', false);
        }
    }
}


if (!function_exists('isDatePlusDaysFuture')) {
    /**
     * Check if (date + days) is greater than or equal to today
     *
     * @param string|DateTimeInterface $dateInput
     * @param int $days
     * @return bool
     */
    function isDatePlusDaysFuture($dateInput, int $days): bool {
        try {
            $date = $dateInput instanceof DateTimeInterface
                ? Carbon::instance($dateInput)
                : Carbon::parse($dateInput);
        } catch (Exception $e) {
            return false;
        }

        $expiryDate = $date->copy()->addDays($days)->startOfDay();
        $today = Carbon::today();

        return $expiryDate->greaterThanOrEqualTo($today);
    }
}

if (!function_exists('isDateTodayOrFuture')) {
    /**
     * Check if a date is today or in the future
     *
     * @param string|DateTimeInterface $dateInput
     * @return bool
     */
    function isDateTodayOrFuture($dateInput): bool {
        try {
            $date = $dateInput instanceof DateTimeInterface
                ? Carbon::instance($dateInput)
                : Carbon::parse($dateInput);
        } catch (Exception $e) {
            return false;
        }

        return $date->startOfDay()->greaterThanOrEqualTo(Carbon::today());
    }
}
