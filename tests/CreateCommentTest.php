<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Attributes\Test;

final class LoginTest extends BaseTestCase
{
    #[Test]
    public function happyPath(): void
    {
        $this->login();
    }
}