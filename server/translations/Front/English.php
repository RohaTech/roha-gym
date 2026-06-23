<?php

namespace Translation\Front;

use Common\Lang\Lang;
use Helper\Type\Gender\Female;
use Helper\Type\Gender\Male;

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
            'notFilledInLanguage' => ' ( Not Filled in English )',
            'english' => 'English',
            'amharic' => 'አማርኛ',

            // App
            'appName' => 'RohaGym',

            // Navigation
            'features' => 'Features',
            'stats' => 'Stats',
            'pricing' => 'Pricing',
            'login' => 'Log in',
            'getStarted' => 'Get Started',

            // Homepage — Hero
            'heroBadge' => 'The #1 Gym Management Platform',
            'heroTitlePart1' => 'Manage Your Gym',
            'heroTitlePart2' => 'Like Never Before',
            'heroSubtitle' => 'Digitally manage members, streamline check-ins with QR codes, and gain powerful insights into your gym operations — all in one platform.',
            'startFreeTrial' => 'Start Free Trial',
            'seeFeatures' => 'See Features',

            // Homepage — Stats
            'activeGyms' => 'Active Gyms',
            'membersManaged' => 'Members Managed',
            'checkinsRecorded' => 'Check-ins Recorded',
            'uptime' => 'Uptime',

            // Homepage — Features
            'whyChooseUs' => 'Why Choose Us',
            'featuresTitle' => 'Everything You Need to',
            'featuresTitle2' => 'Run Your Gym',
            'featuresSubtitle' => 'From member registration to attendance analytics, we\'ve got every aspect of gym management covered.',
            'memberManagement' => 'Member Management',
            'memberManagementDesc' => 'Add members with photos, assign plans, track status, and auto-calculate expiry dates — all in one place.',
            'qrCheckin' => 'QR Check-in',
            'qrCheckinDesc' => 'Scan a member\'s QR code for instant check-in. Verify identity with displayed photo and record attendance.',
            'analytics' => 'Dashboard & Analytics',
            'analyticsDesc' => 'Track registrations, attendance trends, and member activity with real-time data-driven insights.',
            'checkinValidation' => 'Check-in Validation',
            'checkinValidationDesc' => 'Automatic checks for membership expiry and daily limits before recording attendance.',
            'membershipPlans' => 'Membership Plans',
            'membershipPlansDesc' => 'Create flexible plans — monthly, daily pass, or custom. Assign rules and auto-generate ID cards with QR codes.',
            'multiGym' => 'Multi-Gym Platform',
            'multiGymDesc' => 'Each gym gets its own private workspace. Fully isolated data with scalable multi-tenant architecture.',

            // Homepage — How it Works
            'howItWorks' => 'How It Works',
            'howItWorksTitle' => 'Get Started in',
            'howItWorksTitle2' => '3 Simple Steps',
            'step1Title' => 'Register Your Gym',
            'step1Desc' => 'Create your account in seconds. Set up your gym profile, branding, and preferences.',
            'step2Title' => 'Add Members',
            'step2Desc' => 'Register members, assign plans, and generate QR-coded ID cards instantly.',
            'step3Title' => 'Start Managing',
            'step3Desc' => 'Scan QR codes for check-ins, track attendance, and view real-time analytics.',

            // Homepage — CTA
            'ctaTitle' => 'Ready to Transform',
            'ctaTitle2' => 'Your Gym Operations?',
            'ctaSubtitle' => 'Join hundreds of gyms already using our platform to manage members, automate check-ins, and grow their business.',
            'getStartedFree' => 'Get Started — It\'s Free',
            'allRightsReserved' => 'All rights reserved.',

            // Auth — Common
            'email' => 'Email',
            'emailPlaceholder' => 'you@yourgym.com',
            'password' => 'Password',
            'passwordPlaceholder' => '••••••••',
            'confirmPassword' => 'Confirm Password',
            'forgotPassword' => 'Forgot password?',
            'signIn' => 'Sign In',
            'or' => 'or',

            // Login
            'loginTitle' => 'Sign in to your account',
            'loginSubtitle' => 'Enter your credentials below to access your dashboard',
            'loginPanelTitle' => 'Welcome back to',
            'loginPanelTitle2' => 'your gym dashboard',
            'loginPanelDesc' => 'Manage members, track attendance, and grow your gym — all from one powerful platform.',
            'loginTestimonial' => 'RohaGym transformed how we run our gym. Check-ins take seconds, not minutes.',
            'loginTestimonialAuthor' => 'Abebe K.',
            'loginTestimonialRole' => 'Gym Owner, Addis Ababa',
            'dontHaveAccount' => 'Don\'t have an account?',
            'createAccount' => 'Create one',

            // Register
            'registerTitle' => 'Create your account',
            'registerSubtitle' => 'Register your gym and start managing members today',
            'registerPanelTitle' => 'Start managing your',
            'registerPanelTitle2' => 'gym today',
            'registerPanelDesc' => 'Set up your gym in minutes. Add members, generate QR codes, and track everything from one place.',
            'registerPanelFooter' => 'Trusted by 500+ gyms across the country.',
            'chipFreeSetup' => 'Free Setup',
            'chipQrCards' => 'QR ID Cards',
            'chipAnalytics' => 'Analytics',
            'chipMultiGym' => 'Multi-Gym',
            'gymName' => 'Gym Name',
            'gymNamePlaceholder' => 'e.g. FitZone Gym',
            'phone' => 'Phone',
            'phonePlaceholder' => '+251 9XX XXX XXX',
            'address' => 'Address',
            'addressPlaceholder' => 'City, Sub-city, Area',
            'agreeToTerms' => 'I agree to the',
            'termsOfService' => 'Terms of Service',
            'and' => 'and',
            'privacyPolicy' => 'Privacy Policy',
            'alreadyHaveAccount' => 'Already have an account?',

            // Check In
            'checkInTitle' => 'Member Check-In',
            'checkInSubtitle' => 'Scan a QR code or manually enter a member ID to record attendance.',
            'qrScanner' => 'QR Scanner',
            'manualEntry' => 'Manual Entry',
            'scanQrCode' => 'Scan QR Code',
            'scanQrDesc' => 'Hold the member\'s ID card or mobile screen up to the camera.',
            'cameraError' => 'Camera Error',
            'processing' => 'Processing...',
            'enterCode' => 'Enter Member ID',
            'enterCodeDesc' => 'Type the 5-digit unique code shown on the member\'s profile.',
            'codePlaceholder' => 'e.g. 12345',
            'submitCheckIn' => 'Record Check-In',
            'checkInSuccess' => 'Check-In Successful',
            'daysRemaining' => 'days remaining',
            'checkInFailed' => 'Check-In Failed',
            'reasonNotFound' => 'Member not found. Please verify the ID or QR code.',
            'reasonExpired' => 'Membership has expired. Renewal required.',
            'reasonLimit' => 'Daily check-in limit reached for this membership.',
            'reasonUnknown' => 'An unknown error occurred.',

            // Layouts
            'adminLayoutOverline' => 'System Admin',
            'adminLayoutTitle' => 'Admin Dashboard',
            'userLayoutOverline' => 'Gym Owner',
            'userLayoutTitle' => 'Gym Dashboard',

            // Sidebar
            'sidebarBrand' => 'RohaGym',
            'sidebarWorkspace' => 'Workspace',
            'sidebarMain' => 'Main',
            'sidebarDashboard' => 'Dashboard',
            'sidebarMembers' => 'Members',
            'sidebarAllMembers' => 'All Members',
            'sidebarAddMember' => 'Add Member',
            'sidebarExpiringSoon' => 'Expiring Soon',
            'sidebarMembershipTypes' => 'Membership Types',
            'sidebarCheckIn' => 'Check-in',
            'sidebarAnalytics' => 'Analytics',
            'sidebarMembershipCards' => 'Membership Cards',
            'sidebarSettings' => 'Settings',
            'sidebarAccount' => 'Account',
            'sidebarProfile' => 'Profile',
            'sidebarPassword' => 'Password',
            'sidebarLogout' => 'Log out',
            'sidebarGyms' => 'Gyms',
            'sidebarAllGyms' => 'All Gyms',
            'sidebarAddGym' => 'Add Gym',
            'sidebarSubscriptions' => 'Subscriptions',
            'sidebarSystemSettings' => 'System Settings',
            'sidebarAdminAccount' => 'Admin Account',

            // Admin Dashboard
            'adminDashboardTitle' => 'Overview',
            'adminDashboardSubtitle' => 'Multi-tenant performance and growth signals',
            'adminStatGyms' => 'Total Gyms',
            'adminStatGymsValue' => '0',
            'adminStatRevenue' => 'Revenue',
            'adminStatRevenueValue' => '0',
            'adminStatActivePlans' => 'Active Plans',
            'adminStatActivePlansValue' => '0',
            'adminStatCompliance' => 'Compliance',
            'adminStatComplianceValue' => '0',
            'adminDashboardActivityTitle' => 'Recent System Activity',
            'adminDashboardActivityBody' => 'Track audits, onboarding, and subscription lifecycle updates in one place.',

            // User Dashboard
            'userDashboardTitle' => 'Today at a glance',
            'userDashboardSubtitle' => 'Member activity, check-ins, and trends',
            'userStatMembers' => 'Active Members',
            'userStatMembersValue' => '0',
            'userStatCheckIns' => 'Check-ins',
            'userStatCheckInsValue' => '0',
            'userStatMemberships' => 'Memberships',
            'userStatMembershipsValue' => '0',
            'userStatAnalytics' => 'Insights',
            'userStatAnalyticsValue' => '0',
            'userDashboardFocusTitle' => 'Operational Focus',
            'userDashboardFocusBody' => 'Prioritize renewals, expiring memberships, and daily attendance.',

            // Dashboard
            'dashTotalMembers'   => 'Total Members',
            'dashActiveMembers'  => 'Active Members',
            'dashExpiredMembers' => 'Expired Members',
            'dashCheckinsToday'  => 'Check-ins Today',
            'dashWeeklyCheckins' => 'Weekly Check-ins',
            'dashTodaysCheckins' => "Today's Check-ins",
            'dashExpiringSoon'   => 'Expiring Soon',
            'dashNoCheckins'     => 'No check-ins today',
            'dashNoExpiring'     => 'No memberships expiring soon',
            'dashDaysLeft'       => 'days left',

            // Analytics
            'analyticsTitle'            => 'Analytics',
            'analyticsSubtitle'         => 'Insights into your gym performance',
            'analyticsNewThisMonth'     => 'New This Month',
            'analyticsAvgDailyCheckins' => 'Avg Daily Check-ins',
            'analyticsAttendance'       => 'Attendance',
            'analyticsDailyCheckins'    => 'Daily Check-ins',
            'analyticsCheckinsByDay'    => 'Check-ins by Day of Week',
            'analyticsCheckinsByHour'   => 'Check-ins by Hour',
            'analyticsQrVsManual'       => 'QR vs Manual',
            'analyticsMemberGrowth'     => 'Member Growth',
            'analyticsMonthlyReg'       => 'Monthly Registrations',
            'analyticsCumulative'       => 'Cumulative Members',
            'analyticsRetentionRate'    => 'Retention Rate',
            'analyticsMembersRenewed'   => 'Members Renewed',
            'analyticsMembership'       => 'Membership',
            'analyticsMembershipDist'   => 'Membership Distribution',
            'analyticsExpiringSoon'     => 'Expiring in 7 Days',
            'analyticsExpiredMonth'     => 'Expired This Month',
            'analyticsMemberActivity'   => 'Member Activity',
            'analyticsAvgCheckinsWeek'  => 'Avg Check-ins / Member / Week',
            'analyticsMostActive'       => 'Top 10 Most Active Members',
            'analyticsInactive'         => 'Inactive Members',
            'analyticsNoCheckin14Days'  => 'No check-in in 14 days',
            'analyticsQr'               => 'QR',
            'analyticsManual'           => 'Manual',
            'analyticsCheckins'         => 'check-ins',
            'analyticsLoading'          => 'Loading analytics...',
            'analyticsError'            => 'Failed to load analytics.',
            'analyticsNoData'           => 'No data available',
            'analyticsRange7d'          => '7d',
            'analyticsRange30d'         => '30d',
            'analyticsRange3m'          => '3m',
            'analyticsRange12m'         => '12m',

            // Gym Profile
            'profileTitle'           => 'Gym Profile',
            'profileSubtitle'        => 'Manage your gym information',
            'profileGymInfo'         => 'Gym Information',
            'profileGymInfoDesc'     => 'Update your gym\'s name, contact details, and address',
            'profileLogo'            => 'Gym Logo',
            'profileLogoDesc'        => 'Upload your gym\'s logo',
            'profileGymName'         => 'Gym Name',
            'profileGymNamePlaceholder' => 'e.g. FitZone Gym',
            'profileEmail'           => 'Email',
            'profileEmailPlaceholder' => 'you@yourgym.com',
            'profilePhone'           => 'Phone',
            'profilePhonePlaceholder' => '+251 9XX XXX XXX',
            'profileAddress'         => 'Address',
            'profileAddressPlaceholder' => 'City, Sub-city, Area',
            'profileSaveChanges'     => 'Save Changes',
            'profileSaving'          => 'Saving...',
            'profileCancel'          => 'Cancel',
            'profileLoading'         => 'Loading profile...',
            'profileLoadError'       => 'Failed to load profile.',
            'profileClickToUpload'   => 'Click to upload logo',
            'profileLogoHint'        => 'JPEG, PNG, WebP (max 2MB)',
            'profileOptional'        => 'Optional',

            // Errors
            'forbiddenTitle' => 'Access denied',
            'forbiddenBody' => 'You do not have permission to view this page.',
            'forbiddenCta' => 'Go back home',
            'notFoundTitle' => 'Page not found',
            'notFoundBody' => 'The page you are looking for does not exist.',
            'notFoundCta' => 'Return to home',
        ];
    }
}
