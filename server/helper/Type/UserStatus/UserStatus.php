<?php


namespace Helper\Type\UserStatus;

use Helper\Type\Type;

class UserStatusType extends Type {
    public static $id;
    public static $name;
    public static $color;
    public static $icon;

    public const TYPES = [
        UserStatusActive::class,
        UserStatusInactive::class,
        UserStatusPending::class,
    ];
}


class UserStatusActive extends UserStatusType {
    public static $id = USER_STATUS_ACTIVE;
    public static $name = 'statusActive';
    public static $icon = 'fa fa-check-circle';
    public static $color = '#008000';
}
class UserStatusInactive extends UserStatusType {
    public static $id = USER_STATUS_INACTIVE;
    public static $name = 'statusInactive';
    public static $icon = 'fa fa-circle-xmark';
    public static $color = '#FF0000';
}

class UserStatusPending extends UserStatusType {
    public static $id = USER_STATUS_PENDING;
    public static $name = 'statusPending';
    public static $icon = 'fa fa-clock';
    public static $color = '#0000FF';
}

