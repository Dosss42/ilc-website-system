<?php

namespace App\Support;

/**
 * Capitalizes the first letter of each word in a person's name — same rule as public/js/name-case.js:
 * "maria dela cruz" → "Maria Dela Cruz", "o'connell" → "O'Connell". The rest of each word is kept as typed
 * (so "McDonald" / "deGuzman" stay as they are).
 */
class NameCase
{
    public static function words(string $name): string
    {
        $name = preg_replace('/\s+/u', ' ', trim($name));
        return preg_replace_callback("/(^|[\\s\\-'’])(\\p{Ll})/u", fn($m) => $m[1] . mb_strtoupper($m[2]), $name);
    }
}
