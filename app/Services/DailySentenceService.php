<?php

namespace App\Services;

class DailySentenceService
{
    public function getSentence(): string
    {
        $ch = curl_init('http://metaphorpsum.com/sentences/3');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return curl_exec($ch);
    }
}
