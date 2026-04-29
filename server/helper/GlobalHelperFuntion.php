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

if (!function_exists('getAllModuleTranslations')) {
    /**
     * Merge core and all module translation arrays for a given locale and file.
     *
     * @param string $locale
     * @param string $fileName (e.g. 'validation.php', 'pagination.php')
     * @param array $core (optional) The core translation array
     * @return array
     */
    function getAllModuleTranslations(string $locale, string $fileName, array $core = []): array {
        $translations = $core;
        $baseDir = realpath(__DIR__ . '/../modules');
        if ($baseDir === false) {
            return $translations;
        }
        $pattern = $baseDir . '/*/lang/' . $locale . '/' . $fileName;
        $files = glob($pattern);
        foreach ($files as $file) {
            if (is_file($file)) {
                $moduleTranslations = include $file;
                if (is_array($moduleTranslations)) {
                    $translations = array_replace_recursive($translations, $moduleTranslations);
                }
            }
        }
        return $translations;
    }
}
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

if (!function_exists('toEnglish')) {
    /**
     * echo toEnglish('በርናባስ');  // bernabas
     * echo toEnglish('አወል');  // Awol
     * echo toEnglish('አረጋዊ');  // aregawi
     */
    function toEnglish(string $text): string {
        return (new AmharicTranslator())->toEnglish($text);
    }
}

if (!function_exists('toAmharic')) {
    /**
     * echo toAmharic('natnael'); // ናትናኤል
     * echo toAmharic('amare');  // አማረ
     * echo toAmharic('abebe');  // አበበ
     */
    function toAmharic(string $text): string {
        return (new AmharicTranslator())->toAmharic($text);
    }
}


if (!function_exists('validateScopeId')) {
    /**
     * Return validation rules for scope_id based on the scope.
     *
     * @param string|null $scope
     * @return array
     */
    function validateScopeId(?string $scope, array|string|null $permissionKey = null): array {
        $permissionKey = is_array($permissionKey) ? $permissionKey : [$permissionKey];

        $scopeClass = Scope::find($scope);
        $scopeModel = $scopeClass::getModel();
        return [
            'required',
            'integer',
            function ($attribute, $value, $fail) use ($scopeModel, $permissionKey) {
                if (is_null($value) || $value === '') {
                    return;
                }

                $exists = $scopeModel::applyRoleBasedQuery(permissionKey: $permissionKey)
                    ->where('id', $value)
                    ->exists();

                if (!$exists) {
                    $fail(__('validation.invalid_scope', ['attribute' => $attribute]));
                }
            },
        ];
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

/**
 * Get scope id and localized name based on scope type.
 *
 * @param int|null $scope
 * @param int|null $scopeId
 * @return array|null
 */
function getScopeIdAndName(?int $scope, ?int $scopeId): ?array {
    if (!$scope || !$scopeId) {
        return null;
    }

    $modelMap = [
        SCOPE_REGION => Region::class,
        SCOPE_ZONE => Zone::class,
        SCOPE_WOREDA => Woreda::class,
        SCOPE_SCHOOL => School::class,
    ];

    if (!isset($modelMap[$scope])) {
        return null;
    }

    try {
        $modelClass = $modelMap[$scope];
        $model = $modelClass::withoutGlobalScopes()->find($scopeId);

        if (!$model) {
            return null;
        }


        return [
            'id' => $model->id,
            'name' => $model->name__localized,
        ];
    } catch (\Exception $e) {
        return null;
    }
}

if (!function_exists('resolveCalendarPreference')) {

    function resolveCalendarPreference($calendarSystemInput): int {
        $calendarSystemInput = $calendarSystemInput ?? request()->header('calendar_preference');

        return match ($calendarSystemInput) {
            CALENDAR_SYSTEM_GREGORIAN, 'gregorian' => CALENDAR_SYSTEM_GREGORIAN,
            CALENDAR_SYSTEM_ETHIOPIAN, 'ethiopian' => CALENDAR_SYSTEM_ETHIOPIAN,
            default => CALENDAR_SYSTEM_GREGORIAN,
        };
    }
}

if (!function_exists('getCurrentCalendarPreference')) {
    /**
     * Get the currently preferred calendar system
     */
    function getCurrentCalendarPreference(): int {
        $calendarSystemInput = request()->header('calendar_preference');

        return resolveCalendarPreference($calendarSystemInput);
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


/**
 * Get all collection under the given scope.
 *
 * @param int|null $scope
 * @return \Illuminate\Support\Collection|null
 */
function getScopeAllCollection(?int $scope) {
    $modelMap = [
        SCOPE_REGION => Region::class,
        SCOPE_ZONE => Zone::class,
        SCOPE_WOREDA => Woreda::class,
        SCOPE_SCHOOL => School::class,
    ];

    if (!$scope || !isset($modelMap[$scope])) {
        return null;
    }

    return $modelMap[$scope]::all();
}

if (!function_exists('uploadSingleFile')) {
    function uploadSingleFile(UploadedFile $file, string $directory = 'uploads', string $disk = 'public'): string {
        $originalName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs($directory, $originalName, $disk);
    }
}

if (!function_exists('uploadMultipleFiles')) {
    function uploadMultipleFiles(array $files, string $directory = 'uploads', string $disk = 'public'): array {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $originalName = time() . '_' . $file->getClientOriginalName();
                $paths[] = $file->storeAs($directory, $originalName, $disk);
            }
        }

        return $paths;
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

/**
 * Returns a list of descendants scope entities of the current scope
 *
 * @param int $scope
 * @param int $scopeId
 * @return array<int, array{ id: int, name: string }>
 */
function getDescendantScopes(int $scope, int $scopeId): array {
    try {
        $descendants = [];

        switch ($scope) {
            case SCOPE_SCHOOL:
                // Get Region
                $school = School::withoutGlobalScopes()->find($scopeId);
                if ($school) {
                    $woreda = Woreda::withoutGlobalScopes()->find($school->woreda_id);
                    if ($woreda) {
                        $zone = Zone::withoutGlobalScopes()->find($woreda->zone_id);
                        if ($zone) {
                            $region = Region::withoutGlobalScopes()->find($zone->region_id);
                            if ($region) {
                                $descendants[] = [
                                    'id' => $region->id,
                                    'name' => $region->name_localized,
                                    'scope' => Scope::getIdAndNameUsingId(SCOPE_REGION),
                                ];
                            }
                        }

                        $descendants[] = [
                            'id' => $zone->id,
                            'name' => $zone->name_localized,
                            'scope' => Scope::getIdAndNameUsingId(SCOPE_ZONE),
                        ];
                    }

                    $descendants[] = [
                        'id' => $woreda->id,
                        'name' => $woreda->name_localized,
                        'scope' => Scope::getIdAndNameUsingId(SCOPE_WOREDA),
                    ];

                    // Add self entity
                    $descendants[] = [
                        'id' => $school->id,
                        'name' => $school->name_localized,
                        'scope' => Scope::getIdAndNameUsingId(SCOPE_SCHOOL),
                    ];
                }
                break;

            case SCOPE_WOREDA:
                // Get Region, Zone
                $woreda = Woreda::withoutGlobalScopes()->find($scopeId);
                if ($woreda) {
                    $zone = Zone::withoutGlobalScopes()->find($woreda->zone_id);
                    if ($zone) {
                        $region = Region::withoutGlobalScopes()->find($zone->region_id);
                        if ($region) {
                            $descendants[] = [
                                'id' => $region->id,
                                'name' => $region->name_localized,
                                'scope' => Scope::getIdAndNameUsingId(SCOPE_REGION),
                            ];
                        }

                        $descendants[] = [
                            'id' => $zone->id,
                            'name' => $zone->name_localized,
                            'scope' => Scope::getIdAndNameUsingId(SCOPE_ZONE),
                        ];
                    }

                    // Add self entity
                    $descendants[] = [
                        'id' => $woreda->id,
                        'name' => $woreda->name_localized,
                        'scope' => Scope::getIdAndNameUsingId(SCOPE_WOREDA),
                    ];
                }
                break;

            case SCOPE_ZONE:
                // Get Region
                $zone = Zone::withoutGlobalScopes()->find($scopeId);
                if ($zone) {
                    $region = Region::withoutGlobalScopes()->find($zone->region_id);
                    if ($region) {
                        $descendants[] = [
                            'id' => $region->id,
                            'name' => $region->name_localized,
                            'scope' => Scope::getIdAndNameUsingId(SCOPE_REGION),
                        ];
                    }

                    // Add self entity
                    $descendants[] = [
                        'id' => $zone->id,
                        'name' => $zone->name_localized,
                        'scope' => Scope::getIdAndNameUsingId(SCOPE_ZONE),
                    ];
                }
                break;

            case SCOPE_REGION:
                // Get Region self
                $region = Region::withoutGlobalScopes()->find($scopeId);
                if ($region) {
                    $descendants[] = [
                        'id' => $region->id,
                        'name' => $region->name_localized,
                        'scope' => Scope::getIdAndNameUsingId(SCOPE_REGION),
                    ];
                }
                break;
        }

        return $descendants;
    } catch (\Exception $e) {
        return [];
    }
}



/**
 * Assign committee permissions to a user.
 *
 * @param array $userIds
 * @param array $memberPermissions
 * @param array $chairPermissions
 * @param int $scopeType
 * @param int $scopeId
 * @param date $startsAt
 * @param date|null $endsAt
 * @return void
 */
function assignPermissionsToCommittee(array $userIds, int $scopeType, int $scopeId, array $memberPermissions = [], array $chairPermissions = [], $startsAt = null, $endsAt = null, $includeDescendants = false): void {
    $permissions = array_unique(array_merge($memberPermissions, $chairPermissions));
    $userPermissionOverrides = [];
    foreach ($permissions as $permissionCode) {
        $permission = Permission::where('key', $permissionCode)->first();
        if ($permission) {
            foreach ($userIds as $userId) {
                $userPermissionOverrides[$userId] ??= [];
                $permissionOverride = UserPermissionOverride::where('user_id', $userId)
                    ->where('permission_id', $permission->id)
                    ->where('scope_type', $scopeType)
                    ->where('scope_id', $scopeId)
                    ->exists();

                if (!$permissionOverride) {
                    $permissionOverride = UserPermissionOverride::create([
                        'user_id' => $userId,
                        'permission_id' => $permission->id,
                        'assigned_by' => Auth::id(),
                        'scope_type' => $scopeType,
                        'scope_id' => $scopeId,
                        'include_descendants' => false,
                        'allow' => true,
                        'starts_at' => $startsAt ?? now(),
                        'ends_at' => $endsAt ?? null,
                        'include_descendants' => $includeDescendants,
                    ]);

                    array_push($userPermissionOverrides[$userId], $permissionOverride);
                }
            }
        }
    }

    foreach ($userPermissionOverrides as $userId => $overrides) {
        RoleCacheHandler::updateUserCacheFromOverride($overrides);
    }
}



if (!function_exists('revokeCommitteePermissions')) {
    /**
     * Revoke committee permissions from a user.
     *
     * @param array $userIds
     * @param array $permissionCodes
     * @param int $scopeType
     * @param int $scopeId
     * @return void
     */
    function revokeCommitteePermissions(array $userIds, array $permissions, int $scopeType, int $scopeId): void {
        $userPermissionOverrides = [];
        foreach ($permissions as $permissionCode) {
            $permission = Permission::where('key', $permissionCode)->first();

            if ($permission) {
                foreach ($userIds as $userId) {
                    $userPermissionOverrides[$userId] ??= [];
                    $overrides = UserPermissionOverride::where('user_id', $userId)
                        ->where('permission_id', $permission->id)
                        ->where('scope_type', $scopeType)
                        ->where('scope_id', $scopeId)
                        ->get();

                    if ($overrides->isEmpty()) {
                        continue;
                    }
                    foreach ($overrides as $override) {
                        array_push($userPermissionOverrides[$userId], $override);
                        $override->delete();
                    }
                }
            }
        }
        foreach ($userPermissionOverrides as $userId => $overrides) {
            RoleCacheHandler::updateUserCacheFromOverride($overrides);
            RoleCacheHandler::updateUserCache(User::find($userId));
        }
    }
}

if (!function_exists('isUserInCommitteeAndChairman')) {
    /**
     * Check if a user is a member of a specific committee.
     * Optionally check if the user is the chairman.
     *
     * @param int|null $userId User ID to check (defaults to authenticated user)
     * @param string $committeeMemberModel The committee member model class (e.g., LeaveCommitteeMember::class)
     * @param int $committeeId The committee ID to check
     * @param bool $checkChairMan Optional — when true, also require is_chair_man = true (default: false)
     * @return bool
     */
    function isUserInCommitteeAndChairman(
        ?int $userId,
        string $committeeMemberModel,
        int|null $committeeId = null,
        bool $checkChairMan = false
    ): bool {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return false;
        }

        if (!$committeeId) {
            return false;
        }

        try {
            $modelInstance = new $committeeMemberModel();
            $tableName = $modelInstance->getTable();

            if ($tableName === 'recruitment_exam_committees') {
                $foreignKey = 'recruitment_committee_id';
            } else {
                $foreignKey = str_replace('_members', '', $tableName) . '_id';
            }

            $query = $committeeMemberModel::where($foreignKey, $committeeId)
                ->where('member_id', $userId);

            if ($checkChairMan) {
                $query->where('is_chair_man', true);
            }

            return $query->exists();
        } catch (\Exception $e) {
            consoleError("Error checking committee membership: " . $e->getMessage());
            return false;
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


if (!function_exists('getPermissionsFromRequest')) {
    /**
     * Extracts, decrypts, and converts permissions from the request.
     * Accepts either a single encrypted permission or an array of them.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $key The request key to look for (default: 'permissions')
     * @return array Decrypted permission values
     */
    function getPermissionsFromRequest(string $key = DEFAULT_PERMISSION_KEY): array {
        $permissions = request($key);
        if (is_null($permissions)) {
            return [];
        }
        if (is_array($permissions)) {
            return array_map(function ($encrypted) {
                return PermissionActionHelper::decryptAndConvertToPermission($encrypted);
            }, $permissions);
        }
        return [PermissionActionHelper::decryptAndConvertToPermission($permissions)];
    }
}

if (!function_exists('getScopeFromRequest')) {
    /**
     * Extracts and normalizes scope values from the request.
     * Standard format: associative array of scope_type => scope_ids.
     *
     * @param string $key The request key to look for (default: DEFAULT_SCOPE_KEY)
     * @return array Normalized scope values
     */
    function getScopeFromRequest(string $key = DEFAULT_SCOPE_KEY): array {
        $rawScopes = request($key);

        $normalizeScopeType = static function ($scopeType): ?int {
            if (!is_int($scopeType) && !(is_string($scopeType) && is_numeric($scopeType))) {
                return null;
            }

            $scopeType = (int) $scopeType;
            return Scope::find($scopeType) ? $scopeType : null;
        };

        $normalizeScopeIds = static function ($scopeIds): array {
            if (is_null($scopeIds)) {
                return [];
            }

            if (!is_array($scopeIds)) {
                $scopeIds = [$scopeIds];
            }

            $scopeIds = array_values(array_filter(
                $scopeIds,
                static fn ($scopeId) => is_int($scopeId) || (is_string($scopeId) && is_numeric($scopeId))
            ));

            return array_values(array_unique(array_map('intval', $scopeIds)));
        };

        if (!is_array($rawScopes) || array_is_list($rawScopes)) {
            return [[], []];
        }

        $normalizedScopes = [];
        foreach ($rawScopes as $scopeType => $scopeIds) {
            $normalizedScopeType = $normalizeScopeType($scopeType);
            if (is_null($normalizedScopeType)) {
                continue;
            }

            $normalizedScopeIds = $normalizeScopeIds($scopeIds);
            if (count($normalizedScopeIds) === 0) {
                continue;
            }

            $normalizedScopes[$normalizedScopeType] = $normalizedScopeIds;
        }

        return [array_keys($normalizedScopes), $normalizedScopes];
    }
}

if (!function_exists('getScopeFilterFromRequest')) {
    /**
     * Converts request scope payload into filter payload.
     *
     * Example input:
     * - scope_type[4][0] = 21
     * - scope_type[4][1] = 34
     *
     * Example output:
     * - ['zone_id' => [21, 34]]
     *
     * @param string $key The request key to look for (default: DEFAULT_SCOPE_KEY)
     * @return array<string, array<int>>
     */
    function getScopeFilterFromRequest(string $key = DEFAULT_SCOPE_KEY): array {
        [, $scopes] = getScopeFromRequest($key);

        if (empty($scopes)) {
            return [];
        }

        $filters = [];

        foreach ($scopes as $scopeType => $scopeIds) {
            $scopeClass = Scope::find((int) $scopeType);
            if (!$scopeClass || !$scopeClass::hasModel()) {
                continue;
            }

            $relationKey = $scopeClass::modelRelationKey();
            if (!$relationKey) {
                continue;
            }

            $existingIds = $filters[$relationKey] ?? [];
            $filters[$relationKey] = array_values(array_unique([
                ...$existingIds,
                ...array_map('intval', $scopeIds),
            ]));
        }

        return $filters;
    }
}

if (!function_exists('applyRequestedScopeFilter')) {
    /**
     * Apply hierarchical scope filters (region/zone/woreda/school) to a School query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $schoolQuery
     * @param array<int, array<int>>|null $requestedScopePayload
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function applyRequestedScopeFilter($schoolQuery, ?array $requestedScopePayload = null) {
        $requestedScopePayload ??= getScopeFromRequest();

        if (empty($requestedScopePayload)) {
            return $schoolQuery;
        }

        $requestedScopeTypes = array_values(array_map('intval', array_keys($requestedScopePayload)));

        $schoolQuery->where(function ($scopeQuery) use ($requestedScopeTypes, $requestedScopePayload) {
            foreach ($requestedScopeTypes as $scopeType) {
                $scopeIds = $requestedScopePayload[$scopeType] ?? [];
                if (empty($scopeIds)) {
                    continue;
                }

                if ($scopeType === SCOPE_SCHOOL) {
                    $scopeQuery->orWhereIn('id', $scopeIds);
                    continue;
                }

                if ($scopeType === SCOPE_WOREDA) {
                    $scopeQuery->orWhereIn('woreda_id', $scopeIds);
                    continue;
                }

                if ($scopeType === SCOPE_ZONE) {
                    $scopeQuery->orWhereHas('woreda', function ($woredaQuery) use ($scopeIds) {
                        $woredaQuery->whereIn('zone_id', $scopeIds);
                    });
                    continue;
                }

                if ($scopeType === SCOPE_REGION) {
                    $scopeQuery->orWhereHas('woreda.zone', function ($zoneQuery) use ($scopeIds) {
                        $zoneQuery->whereIn('region_id', $scopeIds);
                    });
                }
            }
        });

        return $schoolQuery;
    }
}

if (!function_exists('canAct')) {
    /**
     * Checks if a user can
     * take an action based on the permission
     * key and scopes provided
     *
     * @param array|string $permissionKey
     * @param mixed $scopeType
     * @param mixed $scopeId
     * @param \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param bool $strictPermissions
     * @param bool $strictScopes
     * @param bool $skipDescendants
     *
     * @return bool
     */
    function canAct(
        $permissionKey,
        $scopeType = null,
        $scopeId = null,
        $user = null,
        $strictPermissions = false,
        $strictScopes = false,
        $skipDescendants = false,
    ): bool {
        return (new UserController())
            ->userCan(
                permissionKey: $permissionKey,
                scopeType: $scopeType,
                scopeId: $scopeId,
                user: $user,
                strictPermissions: $strictPermissions,
                strictScopes: $strictScopes,
                skipDescendants: $skipDescendants,
            );
    }
}

if (!function_exists('revokeUserPermission')) {

    function revokeUserPermission(int $userId, int $state = STATE_INACTIVE, $permissionOverrides = []): bool {
        try {
            return DB::transaction(function () use ($userId, $state, $permissionOverrides) {

                $user = User::whereKey($userId)->first();
                if (!$user) {
                    return false;
                }

                // Soft delete related permissions & roles
                UserPermissionOverride::where('user_id', $userId)->delete();
                UserRoleBinding::where('user_id', $userId)->delete();

                // Update user state
                $user->update([
                    'state' => $state,
                ]);

                if (!empty($permissionOverrides)) {
                    assignPermissionOverridesToUser($userId, $permissionOverrides);
                }

                return true;
            });
        } catch (\Throwable $e) {
            return false;
        }
    }
}


if (!function_exists('assignUserPermissionsByScope')) {
    /**
     * Assign permissions to users scoped by scope type and scope id.
     *
     * @param array $userIds
     * @param array $permissions
     * @param int $scopeType
     * @param int $scopeId
     * @param date|null $startsAt
     * @param date|null $endsAt
     * @param bool $includeDescendants
     * @return void
     */
    function assignUserPermissionsByScope(
        array $userIds,
        array $permissions,
        int $scopeType,
        int $scopeId,
        $startsAt = null,
        $endsAt = null,
        bool $includeDescendants = false
    ): void {
        assignPermissionsToCommittee(
            userIds: $userIds,
            memberPermissions: $permissions,
            scopeType: $scopeType,
            scopeId: $scopeId,
            startsAt: $startsAt,
            endsAt: $endsAt,
            includeDescendants: $includeDescendants
        );
    }
}

if (!function_exists('revokeUserPermissionsByScope')) {
    /**
     * Revoke permissions from users scoped by scope type and scope id.
     *
     * @param array $userIds
     * @param array $permissions
     * @param int $scopeType
     * @param int $scopeId
     * @return void
     */
    function revokeUserPermissionsByScope(array $userIds, array $permissions, int $scopeType, int $scopeId): void {
        revokeCommitteePermissions(
            userIds: $userIds,
            permissions: $permissions,
            scopeType: $scopeType,
            scopeId: $scopeId
        );
    }
}

function assignPermissionOverridesToUser(int $userId, array $permissionOverrides = []): bool {
    if (empty($permissionOverrides)) {
        return false;
    }

    try {
        return DB::transaction(function () use ($userId, $permissionOverrides) {

            $userRole = UserRoleBinding::withTrashed()
                ->where('user_id', $userId)
                ->latest()
                ->first();

            if (!$userRole) {
                return false;
            }

            $now = now();
            $authId = auth()->id();
            $permissionIds = Permission::query()
                ->whereIn('key', $permissionOverrides)
                ->pluck('id')
                ->toArray();

            $rows = collect($permissionIds)->map(function ($permissionId) use (
                $userId,
                $userRole,
                $authId,
                $now
            ) {
                return [
                    'user_id' => $userId,
                    'assigned_by' => $authId,
                    'permission_id' => $permissionId,
                    'scope_type' => $userRole->scope_type,
                    'scope_id' => $userRole->scope_id,
                    'include_descendants' => false,
                    'starts_at' => $now,
                    'allow' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->toArray();

            UserPermissionOverride::insert($rows);

            return true;
        });
    } catch (\Throwable $th) {

        Log::error($th->getMessage());
        return false;
    }
}

if (!function_exists('restoreLatestUserPermission')) {
    function restoreLatestUserPermission(int $userId, $needRemoveActivePermissions = false): bool {
        try {
            return DB::transaction(function () use ($userId, $needRemoveActivePermissions) {

                $user = User::whereKey($userId)->first();
                if (!$user) {
                    return false;
                }

                /** -------------------------
                 * Get active permissions ids
                 * ------------------------- */
                $activePermissionsOverrideId = UserPermissionOverride::where('user_id', $userId)->get()->pluck('id')->toArray();
                $activePermissionsId = UserPermissionOverride::where('user_id', $userId)->get()->pluck('permission_id')->toArray();

                /** -------------------------
                 * Restore latest permission overrides
                 * ------------------------- */
                $latestPermissionDeletedAt = UserPermissionOverride::onlyTrashed()
                    ->where('user_id', $userId)
                    ->max('deleted_at');

                if ($latestPermissionDeletedAt) {
                    UserPermissionOverride::onlyTrashed()
                        ->where('user_id', $userId)
                        ->when(!$needRemoveActivePermissions, function ($query) use ($activePermissionsId) {
                            return $query->whereNotIn('permission_id', $activePermissionsId);
                        })
                        ->where('deleted_at', $latestPermissionDeletedAt)
                        ->restore();
                }

                /** -------------------------
                 * Restore latest role bindings
                 * ------------------------- */
                $latestRoleDeletedAt = UserRoleBinding::onlyTrashed()
                    ->where('user_id', $userId)
                    ->max('deleted_at');

                if ($latestRoleDeletedAt) {
                    UserRoleBinding::onlyTrashed()
                        ->where('user_id', $userId)
                        ->where('deleted_at', $latestRoleDeletedAt)
                        ->restore();
                }

                /** -------------------------
                 * Reactivate user
                 * ------------------------- */
                $user->update([
                    'state' => STATE_ACTIVE,
                ]);

                /** -------------------------
                 * Remove active permissions
                 * ------------------------- */
                if ($needRemoveActivePermissions && $activePermissionsOverrideId->count() > 0) {
                    UserPermissionOverride::whereIn('id', $activePermissionsOverrideId)->delete();
                }

                return true;
            });
        } catch (\Throwable $e) {
            Log::error($e);
            return false;
        }
    }
}

if (!function_exists('getTeacherServiceYears')) {
    /**
     * Calculate teacher's total service years from earliest employment
     *
     * @param int $userId
     * @return int
     */
    function getTeacherServiceYears(int $userId): int {
        $teacherDetail = TeacherDetail::where('user_id', $userId)->first();

        if (!$teacherDetail) {
            return 0;
        }

        $earliestEmployment = EmploymentRecord::where('teacher_detail_id', $teacherDetail->id)
            ->whereNotNull('start_date')
            ->whereNull('end_date')
            ->orderBy('start_date', 'asc')
            ->first();

        if (!$earliestEmployment || !$earliestEmployment->start_date) {
            return 0;
        }

        return $earliestEmployment->start_date->diffInYears(now());
    }
}


function scopeIdExistsRule(?string $scope, $permissions = null): \Closure {

    /**
         * validate scope_id based on the scope and permissions.
         *
         * @param string|null $scope
         * @param string|array|null $permissions
         * @return string
         */
    return function ($attribute, $value, $fail) use ($scope, $permissions) {

        $query = match ((int) $scope) {
            SCOPE_REGION => Region::applyRoleBasedQuery($permissions),
            SCOPE_ZONE => Zone::applyRoleBasedQuery($permissions),
            SCOPE_WOREDA => Woreda::applyRoleBasedQuery($permissions),
            SCOPE_SCHOOL => School::applyRoleBasedQuery($permissions),
            default => null
        };

        if (!$query) {
            $fail(__('validation.invalid_scope'));
            return;
        }

        if (!$query->where('id', $value)->exists()) {
            $fail(__('validation.invalid_scope'));
        }
    };
}