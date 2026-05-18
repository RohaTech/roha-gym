<?php

namespace Translation\Front;

use Common\Lang\Lang;
use Helper\Type\Gender\Female;
use Helper\Type\Gender\Male;

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
            'notFilledInLanguage' => '( በአማርኛ አልተሞላም  )',
            'english' => 'English',
            'amharic' => 'አማርኛ',

            // App
            'appName' => 'ሮሃ ጂም (RohaGym)',

            // Navigation
            'features' => 'አገልግሎቶች',
            'stats' => 'ቁጥራዊ መረጃዎች',
            'pricing' => 'ዋጋ/ክፍያ',
            'login' => 'ግባ',
            'getStarted' => 'ጀምር',

            // Homepage — Hero
            'heroBadge' => 'ቁጥር 1 የጂም ማኔጅመንት ሲስተም',
            'heroTitlePart1' => 'ጂምዎን ያስተዳድሩ',
            'heroTitlePart2' => 'ከመቼውም በበለጠ በዘመናዊ መንገድ',
            'heroSubtitle' => 'አባላትን በዲጂታል መንገድ ያስተዳድሩ፣ በQR ኮድ መግቢያን ያቀላጥፉ፣ እና የጂምዎን የሥራ እንቅስቃሴ በጥልቀት ይቆጣጠሩ — ሁሉንም በአንድ ቦታ።',
            'startFreeTrial' => 'በነፃ ይሞክሩ',
            'seeFeatures' => 'አገልግሎቶችን ይመልከቱ',

            // Homepage — Stats
            'activeGyms' => 'ንቁ ጂሞች',
            'membersManaged' => 'አባላት',
            'checkinsRecorded' => 'የመግቢያ ምዝገባዎች',
            'uptime' => 'የአገልግሎት ዝግጁነት',

            // Homepage — Features
            'whyChooseUs' => 'ለምን እኛን ይመርጣሉ?',
            'featuresTitle' => 'ሁሉንም በአንድ ቦታ',
            'featuresTitle2' => 'ጂምዎን ለማንቀሳቀስ',
            'featuresSubtitle' => 'ከአባላት ምዝገባ ጀምሮ እስከ ተገኝነት ትንተና ድረስ፣ ሁሉንም የጂም አስተዳደር ክፍሎች አሟልተን አቅርበናል።',
            'memberManagement' => 'የአባላት አስተዳደር',
            'memberManagementDesc' => 'አባላትን በፎቶ ይመዝግቡ፣ የክፍያ ዓይነት ይምረጡ፣ የሁኔታ ክትትል ያድርጉ፣ እና የአባልነት ማለቂያ ቀንን በራሱ እንዲያሰላ ያድርጉ።',
            'qrCheckin' => 'በQR ኮድ መግቢያ',
            'qrCheckinDesc' => 'ለፈጣን ምዝገባ የአባሉን QR ኮድ ስካን ያድርጉ። ማንነታቸውን በፎቶ ያረጋግጡ እና ተገኝነታቸውን ይመዝግቡ።',
            'analytics' => 'ዳሽቦርድ እና ትንተና',
            'analyticsDesc' => 'የምዝገባ ሁኔታን፣ የመግቢያ ልምዶችን እና የአባላትን እንቅስቃሴ በቅጽበት መረጃ ይከታተሉ።',
            'checkinValidation' => 'የመግቢያ ማረጋገጫ',
            'checkinValidationDesc' => 'ተገኝነት ከመመዝገቡ በፊት የአባልነት ጊዜ ማለቁን እና ዕለታዊ ገደቦችን በራሱ ያረጋግጣል።',
            'membershipPlans' => 'የአባልነት ዓይነቶች',
            'membershipPlansDesc' => 'ተለዋዋጭ የክፍያ ዓይነቶችን (ወርሃዊ፣ ዕለታዊ ወይም እንደ ምርጫዎ) ይፍጠሩ። ደንቦችን ይወስኑ እና የQR ኮድ ያላቸው የመታወቂያ ካርዶችን በራሱ እንዲያመነጭ ያድርጉ።',
            'multiGym' => 'ለበርካታ ጂሞች የሚሆን',
            'multiGymDesc' => 'እያንዳንዱ ጂም የራሱ የሆነ የግል የሥራ ቦታ ይኖረዋል። መረጃዎች ሙሉ በሙሉ ተለይተው የተጠበቁ ናቸው።',

            // Homepage — How it Works
            'howItWorks' => 'እንዴት ይሠራል?',
            'howItWorksTitle' => 'በነዚህ',
            'howItWorksTitle2' => '3 ቀላል ደረጃዎች ይጀምሩ',
            'step1Title' => 'ጂምዎን ያስመዝግቡ',
            'step1Desc' => 'በሰከንዶች ውስጥ አካውንት ይፍጠሩ። የጂምዎን መገለጫ፣ ዓርማ እና ምርጫዎች ያስተካክሉ።',
            'step2Title' => 'አባላትን ይጨምሩ',
            'step2Desc' => 'አባላትን ይመዝግቡ፣ የክፍያ ዓይነት ይምረጡ፣ እና የQR ኮድ ያለው መታወቂያ ወዲያውኑ ያውጡ።',
            'step3Title' => 'አስተዳደር ይጀምሩ',
            'step3Desc' => 'ለመግቢያ QR ኮዶችን ስካን ያድርጉ፣ ተገኝነትን ይከታተሉ፣ እና የሥራ እንቅስቃሴዎን ይገምግሙ።',

            // Homepage — CTA
            'ctaTitle' => 'የጂምዎን አሠራር',
            'ctaTitle2' => 'ለመቀየር ዝግጁ ነዎት?',
            'ctaSubtitle' => 'አባላትን ለማስተዳደር እና ስራቸውን ለማሳለጥ የእኛን ሲስተም እየተጠቀሙ ያሉ በመቶዎች የሚቆጠሩ ጂሞችን ይቀላቀሉ።',
            'getStartedFree' => 'በነፃ ይጀምሩ',
            'allRightsReserved' => 'መብቱ በሕግ የተጠበቀ ነው።',

            // Auth — Common
            'email' => 'ኢሜይል',
            'emailPlaceholder' => 'you@yourgym.com',
            'password' => 'ይለፍ ቃል',
            'passwordPlaceholder' => '••••••••',
            'confirmPassword' => 'ይለፍ ቃል ያረጋግጡ',
            'forgotPassword' => 'ይለፍ ቃል ረስተዋል?',
            'signIn' => 'ግባ',
            'or' => 'ወይም',

            // Login
            'loginTitle' => 'ወደ አካውንትዎ ይግቡ',
            'loginSubtitle' => 'ዳሽቦርድዎን ለመክፈት መለያዎን ያስገቡ',
            'loginPanelTitle' => 'እንኳን ደህና መጡ ወደ',
            'loginPanelTitle2' => 'የጂም ዳሽቦርድዎ',
            'loginPanelDesc' => 'አባላትን ያስተዳድሩ፣ ተገኝነትን ይከታተሉ፣ እና ጂምዎን ያሳድጉ — ሁሉንም በአንድ ጠንካራ ሲስተም።',
            'loginTestimonial' => '"ሮሃ ጂም የጂማችንን አሠራር ቀይሮታል። መግቢያዎችን ለመመዝገብ የሚወስደው ጊዜ አሁን ሰከንዶች ብቻ ነው።"',
            'loginTestimonialAuthor' => 'አበበ ከ.',
            'loginTestimonialRole' => 'የጂም ባለቤት፣ አዲስ አበባ',
            'dontHaveAccount' => 'አካውንት የለዎትም?',
            'createAccount' => 'አዲስ ይፍጠሩ',

            // Register
            'registerTitle' => 'አካውንት ይፍጠሩ',
            'registerSubtitle' => 'ጂምዎን ያስመዝግቡ እና አባላትን ዛሬውኑ ማስተዳደር ይጀምሩ',
            'registerPanelTitle' => 'የጂምዎን አስተዳደር',
            'registerPanelTitle2' => 'ዛሬውኑ ይጀምሩ',
            'registerPanelDesc' => 'ጂምዎን በጥቂት ደቂቃዎች ውስጥ ያደራጁ። አባላትን ይጨምሩ፣ QR ኮድ ያውጡ፣ እና ሁሉንም ነገር በአንድ ቦታ ይከታተሉ።',
            'registerPanelFooter' => 'በአገሪቱ በሚገኙ ከ500 በላይ ጂሞች የታመነ።',
            'chipFreeSetup' => 'በነፃ ማደራጀት',
            'chipQrCards' => 'የQR መታወቂያዎች',
            'chipAnalytics' => 'ትንተናዎች',
            'chipMultiGym' => 'ለበርካታ ጂሞች',
            'gymName' => 'የጂም ስም',
            'gymNamePlaceholder' => 'ለምሳሌ፦ ፊትዞን ጂም',
            'phone' => 'ስልክ ቁጥር',
            'phonePlaceholder' => '+251 9XX XXX XXX',
            'address' => 'አድራሻ',
            'addressPlaceholder' => 'ከተማ፣ ክፍለ ከተማ፣ አካባቢ',
            'agreeToTerms' => 'በነዚህ እስማማለሁ፦',
            'termsOfService' => 'የአገልግሎት ውል',
            'and' => 'እና',
            'privacyPolicy' => 'የግል መረጃ ጥበቃ ፖሊሲ',
            'alreadyHaveAccount' => 'አካውንት አለዎት?',
        ];
    }
}
