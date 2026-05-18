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
