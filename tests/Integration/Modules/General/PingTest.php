<?php declare(strict_types=1);

namespace Document\Tests\Integration\Modules\General;

use Document\Controller\RootController;
use Document\Tests\Integration\BaseIntegrationTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(RootController::class)]
#[CoversMethod(RootController::class, 'root')]
class PingTest extends BaseIntegrationTest
{
    public function testPingWrongUserAgent(): void
    {
        $response = $this->callEndpoint('get', '/', '', ['HTTP_USER_AGENT' => 'xxx']);
        $responseBody = self::getResponseBody($response);

        self::assertEquals(200, $response->getStatusCode());
        self::assertSame(['ok - prague'], $responseBody);
    }
}
