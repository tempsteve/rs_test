<?php

namespace App\Services;

class DailySentenceService
{
    public function getSentence(): bool|string
    {
        $ch = curl_init('http://metaphorpsum.com/sentences/3');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        return is_string($result) && !empty($result) ? $result : false;
    }

    public function getSentenceWithSource($source): bool|string
    {
        if ('metaphorpsum' == $source) {
            $ch = curl_init('http://metaphorpsum.com/sentences/3');
        } elseif ('itsthisforthat' == $source) {
            $ch = curl_init('https://itsthisforthat.com/api.php?text');
        } else {
            return false;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        return is_string($result) && !empty($result) ? $result : false;
    }
}
