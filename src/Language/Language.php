<?php
namespace Bucorel\Waf\Language;

enum Language: string {
    // 13 Major Indian & South Asian Languages
    case BN = 'bn'; // Bengali
    case GU = 'gu'; // Gujarati
    case HI = 'hi'; // Hindi
    case KN = 'kn'; // Kannada
    case ML = 'ml'; // Malayalam
    case MR = 'mr'; // Marathi
    case NE = 'ne'; // Nepali
    case OR = 'or'; // Odia
    case PA = 'pa'; // Punjabi
    case SI = 'si'; // Sinhala
    case TA = 'ta'; // Tamil
    case TE = 'te'; // Telugu
    case UR = 'ur'; // Urdu

    // 19 Major Foreign & Regional Languages
    case AR = 'ar'; // Arabic
    case DE = 'de'; // German
    case EN = 'en'; // English
    case ES = 'es'; // Spanish
    case FA = 'fa'; // Persian (Farsi)
    case FR = 'fr'; // French
    case ID = 'id'; // Indonesian
    case IT = 'it'; // Italian
    case JA = 'ja'; // Japanese
    case KO = 'ko'; // Korean
    case MY = 'my'; // Burmese
    case PT = 'pt'; // Portuguese
    case RU = 'ru'; // Russian
    case TH = 'th'; // Thai
    case TR = 'tr'; // Turkish
    case VI = 'vi'; // Vietnamese
    case ZH = 'zh'; // Chinese (Mandarin)

    public static function asArray(): array {
        return array_combine(
            array_map(fn($case) => $case->value, self::cases()), // Extract values
            [
                "বাংলা", "ગુજરાતી", "हिन्दी", "ಕನ್ನಡ", "മലയാളം", 
                "मराठी", "नेपाली", "ଓଡ଼ିଆ", "ਪੰਜਾਬੀ", "සිංහල", 
                "தமிழ்", "తెలుగు", "اردو","العربية", "Deutsch", "English", "Español", "فارسی", 
                "Français", "Bahasa Indonesia", "Italiano", "日本語", "한국어", 
                "မြန်မာစာ", "Português", "Русский", "ไทย", "Türkçe", 
                "Tiếng Việt", "中文"
            ]
        );
    }
    
    /**
     * returns language name for a given language code
     */
    public static function getName( string $langCode ) : string{
    	$a = Language::asArray();
    	return $a[ $langCode ];
    }
    
    /**
     * returns all supported language codes 
     */
    public static function getNames(): array {
    	return  array_keys(Language::asArray());
    }
    
    /**
     * checks if a language code exists in supported languages
     */
    public static function isSupported( string $langCode ): bool{
    	return in_array( $langCode, self::getNames() );
    }
    
    public static function getLocale(): array{
		return array(
			// 13 Major Indian & South Asian Languages
			'bn' => 'bn_IN', // Bengali
			'gu' => 'gu_IN', // Gujarati
			'hi' => 'hi_IN', // Hindi
			'kn' => 'kn_IN', // Kannada
			'ml' => 'ml_IN', // Malayalam
			'mr' => 'mr_IN', // Marathi
			'ne' => 'ne_NP', // Nepali
			'or' => 'or_IN', // Odia
			'pa' => 'pa_IN', // Punjabi
			'si' => 'si_LK', // Sinhala
			'ta' => 'ta_IN', // Tamil
			'te' => 'te_IN', // Telugu
			'ur' => 'ur_IN', // Urdu

			// 19 Major Foreign & Regional Languages
			'ar' => 'ar_SA', // Arabic (Saudi Arabia)
			'de' => 'de_DE', // German (Germany)
			'en' => 'en_US', // English (United States)
			'es' => 'es_ES', // Spanish (Spain)
			'fa' => 'fa_IR', // Persian (Iran)
			'fr' => 'fr_FR', // French (France)
			'id' => 'id_ID', // Indonesian
			'it' => 'it_IT', // Italian
			'ja' => 'ja_JP', // Japanese
			'ko' => 'ko_KR', // Korean
			'my' => 'my_MM', // Burmese
			'pt' => 'pt_PT', // Portuguese (Portugal) — or use 'pt_BR' for Brazil
			'ru' => 'ru_RU', // Russian
			'th' => 'th_TH', // Thai
			'tr' => 'tr_TR', // Turkish
			'vi' => 'vi_VN', // Vietnamese
			'zh' => 'zh_CN', // Chinese (Simplified, China) — or use 'zh_TW' for Traditional
		);
    }
    
    public static function getBrowserLanguage():?string{
    	if( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ){
    		return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    	}else{
    		return null;
    	}
    }
}

?>
