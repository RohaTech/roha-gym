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
            'account_inactive' => 'መለያዎ ንቁ አይደለም። እባክዎ አስተዳዳሪውን ያነጋግሩ።',

            // Subscriptions
            'subscription_recorded' => 'ክፍያ በተሳካ ሁኔታ ተመዝግቧል',
            'subscription_deleted'  => 'የክፍያ መዝገብ ተሰርዟል',

                        // Layouts
            'adminLayoutOverline' => 'የሲስተም አስተዳዳሪ',
            'adminLayoutTitle' => 'የአስተዳዳሪ ዳሽቦርድ',
            'userLayoutOverline' => 'የጂም ባለቤት',
            'userLayoutTitle' => 'የጂም ዳሽቦርድ',

            // Sidebar
            'sidebarBrand' => 'ሮሃ ጂም (RohaGym)',
            'sidebarWorkspace' => 'የሥራ ቦታ',
            'sidebarMain' => 'ዋና ማውጫ',
            'sidebarDashboard' => 'ዳሽቦርድ',
            'sidebarMembers' => 'አባላት',
            'sidebarAllMembers' => 'ሁሉንም አባላት',
            'sidebarAddMember' => 'አባል ጨምር',
            'sidebarExpiringSoon' => 'በቅርቡ የሚያልቁ',
            'sidebarMembershipTypes' => 'የአባልነት ዓይነቶች',
            'sidebarCheckIn' => 'መግቢያ (Check-in)',
            'sidebarAnalytics' => 'ትንተናዎች',
            'sidebarMembershipCards' => 'የአባልነት ካርዶች',
            'sidebarSettings' => 'ቅንብሮች',
            'sidebarAccount' => 'መለያ',
            'sidebarProfile' => 'የግል መገለጫ',
            'sidebarPassword' => 'ይለፍ ቃል',
            'sidebarLogout' => 'ውጣ',
            'sidebarGyms' => 'ጂሞች',
            'sidebarAllGyms' => 'ሁሉንም ጂሞች',
            'sidebarAddGym' => 'ጂም ጨምር',
            'sidebarSubscriptions' => 'የክፍያ ዕቅዶች',
            'sidebarSystemSettings' => 'የሲስተም ቅንብሮች',
            'sidebarAdminAccount' => 'የአስተዳዳሪ መለያ',

            // Admin Dashboard
            'adminDashboardTitle' => 'አጠቃላይ እይታ',
            'adminDashboardSubtitle' => 'የተቋማት አፈጻጸም እና የእድገት ምልክቶች',
            'adminStatGyms' => 'ጠቅላላ ጂሞች',
            'adminStatGymsValue' => '0',
            'adminStatRevenue' => 'ገቢ',
            'adminStatRevenueValue' => '0',
            'adminStatActivePlans' => 'ንቁ የክፍያ ዕቅዶች',
            'adminStatActivePlansValue' => '0',
            'adminStatCompliance' => 'ደንብ አከባበር',
            'adminStatComplianceValue' => '0',
            'adminDashboardActivityTitle' => 'የቅርብ ጊዜ የሲስተም እንቅስቃሴዎች',
            'adminDashboardActivityBody' => 'ኦዲቶችን፣ አዳዲስ ምዝገባዎችን እና የክፍያ ሁኔታዎችን በአንድ ቦታ ይከታተሉ።',

            // User Dashboard
            'userDashboardTitle' => 'የዛሬው ውሎ በአጭሩ',
            'userDashboardSubtitle' => 'የአባላት እንቅስቃሴ፣ መግቢያዎች እና አዝማሚያዎች',
            'userStatMembers' => 'ንቁ አባላት',
            'userStatMembersValue' => '0',
            'userStatCheckIns' => 'የዛሬ መግቢያዎች',
            'userStatCheckInsValue' => '0',
            'userStatMemberships' => 'አባልነቶች',
            'userStatMembershipsValue' => '0',
            'userStatAnalytics' => 'ግንዛቤዎች',
            'userStatAnalyticsValue' => '0',
            'userDashboardFocusTitle' => 'ዋና የሥራ ትኩረት',
            'userDashboardFocusBody' => 'ለአባልነት እድሳት፣ ለሚያልቁ አባልነቶች እና ለዕለታዊ ተገኝነት ቅድሚያ ይስጡ።',

            // Errors
            'forbiddenTitle' => 'መግባት አይቻልም',
            'forbiddenBody' => 'ይህንን ገጽ ለመመልከት ፈቃድ የለዎትም።',
            'forbiddenCta' => 'ወደ መነሻ ገጽ ተመለስ',
            'notFoundTitle' => 'ገጹ አልተገኘም',
            'notFoundBody' => 'የሚፈልጉት ገጽ በሲስተሙ ውስጥ የለም።',
            'notFoundCta' => 'ወደ መነሻ ገጽ ተመለስ',
        ];
    }
}
