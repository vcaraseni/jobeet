<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testTokenGeneration(): void
    {
        $tokenGenerator = new TokenGenerator();
        $token = $tokenGenerator->getRandomSecureToken(30);

        $this->assertEquals('30', strlen($token), 'Expected to have the same length.');
        $this->assertEquals(true, ctype_alnum($token), 'Expected to have only alphanumeric characters.');
    }
}