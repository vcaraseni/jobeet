<?php

declare(strict_types=1);

namespace App\Security;

class TokenGenerator
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    /**
     * @param int $length
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getRandomSecureToken(int $length): string
    {
        $maxNumber = strlen(self::ALPHABET);
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= self::ALPHABET[random_int(0, $maxNumber - 1)];
        }

        return $token;
    }
}