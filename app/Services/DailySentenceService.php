<?php

namespace App\Services;

class DailySentenceService
{
    public function getSentence(): string|bool
    {
        $ch = curl_init('http://metaphorpsum.com/sentences/3');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        return is_string($result) && !empty($result) ? $result : false;
    }
}
