<?php

namespace Translation\Message;

use Common\Lang\Lang;
use Helper\Type\Address\Address;
use Helper\Type\Gender\Gender;
use Helper\Type\Location\Location;
use Helper\Type\Region\RType;
use Helper\Type\Scope\Scope;
use Helper\Type\State\State;
use Helper\Type\Woreda\WType;
use Helper\Type\Zone\ZType;

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
            'not_found' => 'Resource not found',
            'user_not_found' => 'User not found',
            'email_required' => 'Email is required',
            'invalid_email' => 'Invalid email provided',
            'enter_your_password' => 'Please enter you password',
            'invalid_credentials' => 'Invalid credentials',
            'too_many_login_attempts' => 'Too many login attempts',
            'successfully_loggedout' => 'Successfully loggedout',
            'forbidden' => 'You are not allowed to access this resource',
            'unauthorized' => 'You are not authorized to access this resource',
            'unable_to_login_please_contact_administrator' => 'Unable to login please contact administrator',
            'account_inactive' => 'Your account is inactive. Please contact the administrator.',
            'success' => 'success',
            
            // Membership Types
            'membership_type_created' => 'Membership type created successfully',
            'membership_type_updated' => 'Membership type updated successfully',
            'membership_type_deleted' => 'Membership type deleted successfully',
            'membership_type_has_active_members' => 'Cannot delete membership type with active members',
            
            // Members
            'member_created' => 'Member added successfully',
            'member_updated' => 'Member updated successfully',
            'member_deleted' => 'Member deleted successfully',

            // Gym Profile
            'gym_profile_updated' => 'Gym profile updated successfully',

            // Subscriptions
            'subscription_recorded' => 'Payment recorded successfully',
            'subscription_deleted'  => 'Payment record deleted successfully',
        ];
    }
}
