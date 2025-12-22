<?php

namespace App\Helpers;

class TextNormalizer
{
    public static function normalizeArabic(string $text): string
    {
        $text = trim($text);

        // Remove Arabic diacritics (Tashkeel)
        $text = preg_replace('/[\p{Mn}ًٌٍَُِّْ]/u', '', $text);

        // Normalize Arabic letters
        $text = preg_replace('/[إأآا]/u', 'ا', $text);
        $text = preg_replace('/ى/u', 'ي', $text);
        $text = preg_replace('/ؤ/u', 'و', $text);
        $text = preg_replace('/ئ/u', 'ي', $text);
        $text = preg_replace('/ة/u', 'ه', $text);

        // Remove extra spaces
        $text = preg_replace('/\s+/', ' ', $text);

        return $text;
    }



    /**
     * Generate normalized SQL expression for a given column
     */
    public static function sqlNormalizeColumn(string $column): string
    {
        return "LOWER(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE($column, 'ة', 'ه'),
                                    'ى', 'ي'),
                                'أ', 'ا'),
                            'إ', 'ا'),
                        'آ', 'ا'),
                    'ؤ', 'و')
                )";
    }
}
