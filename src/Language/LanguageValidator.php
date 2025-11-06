<?php
namespace Bucorel\Waf\Language;

class LanguageValidator {

    /**
     * Unicode script/block ranges or patterns mapped by language code.
     */
    private static array $unicodeBlocks = [
        // South Asian / Indian Languages
        'bn' => '\x{0980}-\x{09FF}', // Bengali
        'gu' => '\x{0A80}-\x{0AFF}', // Gujarati
        'hi' => '\x{0900}-\x{097F}', // Hindi (Devanagari)
        'kn' => '\x{0C80}-\x{0CFF}', // Kannada
        'ml' => '\x{0D00}-\x{0D7F}', // Malayalam
        'mr' => '\x{0900}-\x{097F}', // Marathi (Devanagari)
        'ne' => '\x{0900}-\x{097F}', // Nepali (Devanagari)
        'or' => '\x{0B00}-\x{0B7F}', // Odia
        'pa' => '\x{0A00}-\x{0A7F}', // Punjabi (Gurmukhi)
        'si' => '\x{0D80}-\x{0DFF}', // Sinhala
        'ta' => '\x{0B80}-\x{0BFF}', // Tamil
        'te' => '\x{0C00}-\x{0C7F}', // Telugu
        'ur' => '\x{0600}-\x{06FF}', // Urdu (Arabic script)

        // Foreign/International Languages
        'ar' => '\x{0600}-\x{06FF}', // Arabic
        'fa' => '\x{0600}-\x{06FF}', // Persian
        'en' => 'A-Za-z',            // English (Latin)
        'de' => 'A-Za-zÄÖÜäöüß',     // German
        'es' => 'A-Za-zÁÉÍÑÓÚÜáéíñóúü', // Spanish
        'fr' => 'A-Za-zÀÂÆÇÉÈÊËÏÎÔŒÙÛÜŸàâæçéèêëïîôœùûüÿ', // French
        'id' => 'A-Za-z',            // Indonesian (Latin)
        'it' => 'A-Za-zÀÈÉÌÍÎÒÓÙÚàèéìíîòóùú', // Italian
        'pt' => 'A-Za-zÁÂÃÀÇÉÊÍÓÔÕÚÜáâãàçéêíóôõúü', // Portuguese
        'tr' => 'A-Za-zÇĞİÖŞÜçğıöşü', // Turkish
        'vi' => 'A-Za-zĂÂĐÊÔƠƯăâđêôơưÁÀẢÃẠ...', // Vietnamese (partial set)

        'ru' => '\x{0400}-\x{04FF}', // Russian (Cyrillic)
        'my' => '\x{1000}-\x{109F}', // Burmese
        'th' => '\x{0E00}-\x{0E7F}', // Thai
        'ja' => '\x{3040}-\x{30FF}\x{4E00}-\x{9FBF}', // Japanese (Kana & Kanji)
        'ko' => '\x{AC00}-\x{D7AF}', // Korean (Hangul)
        'zh' => '\x{4E00}-\x{9FFF}', // Chinese (Simplified/Traditional)
    ];

    /**
     * Validates if the given text is written in the correct script for a given language.
     */
    //public static function isTextInLanguage(string $text, Language $language): bool {
    public static function isTextInLanguage(string $text, string $langKey): bool {
        //$langKey = $language->value;

        if (!isset(self::$unicodeBlocks[$langKey])) {
            throw new \InvalidArgumentException("Language '$langKey' is not supported for script validation.");
        }

        $unicodeRange = self::$unicodeBlocks[$langKey];

        // Allow spaces (\p{Z}), punctuation (\p{P}), and numbers (\p{N}) in text
        $pattern = "/^[\p{Z}\p{P}\p{N}{$unicodeRange}]+$/u";

        return (bool) preg_match($pattern, $text);
    }
    
	public static function guessLanguage(string $word): ?string {
		$word = trim($word);
		if ($word === '') return null;

		foreach (self::$unicodeBlocks as $lang => $range) {
		    // Strict: the entire word must be composed only of characters from the target language block
		    $pattern = "/^[{$range}]+$/u";
		    if (preg_match($pattern, $word)) {
		        return $lang;
		    }
		}

		// Fallback: try partial match, return language with most matching characters
		$scores = [];
		foreach (self::$unicodeBlocks as $lang => $range) {
		    $pattern = "/[{$range}]/u";
		    if (preg_match_all($pattern, $word, $matches)) {
		        $scores[$lang] = count($matches[0]);
		    }
		}

		arsort($scores);
		return !empty($scores) ? array_key_first($scores) : null;
	}

}

