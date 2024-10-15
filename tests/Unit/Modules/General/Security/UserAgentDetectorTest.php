<?php declare(strict_types=1);

namespace Document\Tests\Unit\Modules\General\Security;

use Document\Security\Auth\UserAgentDetector;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserAgentDetectorTest extends KernelTestCase
{
    public UserAgentDetector $userAgentDetector;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->userAgentDetector = $container->get(UserAgentDetector::class);
    }

    public function testDetect(): void
    {
        self::assertTrue($this->userAgentDetector->detect('Symfony', 'prague'));
        self::assertTrue($this->userAgentDetector->detect('document-prague.local', 'prague'));
        self::assertFalse($this->userAgentDetector->detect('Safari', 'prague'));
    }
}
